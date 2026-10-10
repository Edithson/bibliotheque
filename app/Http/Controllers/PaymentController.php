<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PaymentController extends Controller
{
    /**
     * Prepare Mobile Money payment via Monetbil for a specific book.
     */
    public function checkout(Request $request, Book $book): View|RedirectResponse
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->guest(route('login'))->with('info', 'Veuillez vous connecter pour acheter cet ouvrage.');
        }

        // Si le livre est gratuit ou si l'utilisateur l'a déjà acheté ou a les droits (Gérant/Admin/Auteur)
        if ($book->price === 0 || $user->hasPurchased($book) || $user->isGerant() || $book->user_id === $user->id) {
            return redirect()->route('books.download', $book->slug);
        }

        // Recherche ou création d'une transaction en attente
        $payment = Payment::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        if (! $payment) {
            $paymentRef = 'PAY-'.strtoupper(Str::random(6)).'-'.time();
            $payment = Payment::create([
                'user_id' => $user->id,
                'book_id' => $book->id,
                'amount' => $book->price,
                'status' => 'pending',
                'payment_ref' => $paymentRef,
            ]);
        }

        $serviceKey = config('services.monetbil.service_key');
        $notifyUrl = route('payments.notify');
        $returnUrl = route('payments.return', ['payment_ref' => $payment->payment_ref]);

        $paymentUrl = null;
        try {
            $response = Http::asForm()->timeout(5)->post("https://api.monetbil.com/widget/v2.1/{$serviceKey}", [
                'amount' => $book->price,
                'currency' => 'XAF',
                'item_ref' => $payment->payment_ref,
                'payment_ref' => $payment->payment_ref,
                'notify_url' => $notifyUrl,
                'return_url' => $returnUrl,
                'email' => $user->email,
                'country' => 'CM',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $paymentUrl = $data['payment_url'] ?? $data['link'] ?? null;
            }
        } catch (\Throwable $e) {
            Log::warning('Monetbil API call notice: '.$e->getMessage());
        }

        return view('home.pages.shop.checkout', [
            'book' => $book,
            'payment' => $payment,
            'serviceKey' => $serviceKey,
            'notifyUrl' => $notifyUrl,
            'returnUrl' => $returnUrl,
            'paymentUrl' => $paymentUrl,
        ]);
    }

    /**
     * Monetbil IPN Webhook Notification callback endpoint.
     */
    public function notify(Request $request): JsonResponse
    {
        Log::info('Monetbil IPN Notification received:', $request->all());

        $status = strtoupper($request->input('status', ''));
        $transactionId = $request->input('transaction_id') ?? $request->input('payment_id');
        $paymentRef = $request->input('item_ref') ?? $request->input('payment_ref');
        $phone = $request->input('phone');
        $operator = $request->input('operator');

        $payment = null;
        if (! empty($paymentRef)) {
            $payment = Payment::where('payment_ref', $paymentRef)->first();
        }

        if (! $payment && ! empty($transactionId)) {
            $payment = Payment::where('transaction_id', $transactionId)->first();
        }

        if ($payment) {
            $isSuccess = ($status === 'SUCCESS' || $status === 'COMPLETED' || $request->input('code') == '1');
            $payment->update([
                'status' => $isSuccess ? 'success' : 'failed',
                'transaction_id' => $transactionId ?? $payment->transaction_id,
                'phone_number' => $phone ?? $payment->phone_number,
                'operator' => $operator ?? $payment->operator,
                'payment_details' => $request->all(),
            ]);

            Log::info("Payment {$payment->payment_ref} status updated to: ".($isSuccess ? 'success' : 'failed'));
        } else {
            Log::warning('Monetbil IPN: payment record not found for ref/txn: '.$paymentRef.' / '.$transactionId);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Notification enregistrée.',
        ]);
    }

    /**
     * Handle return URL redirect after Monetbil payment.
     */
    public function return(Request $request): RedirectResponse
    {
        Log::info('Monetbil Customer Return:', $request->all());

        $paymentRef = $request->query('payment_ref') ?? $request->query('item_ref');
        $status = strtoupper($request->query('status', ''));
        $transactionId = $request->query('transaction_id');

        if ($paymentRef) {
            $payment = Payment::where('payment_ref', $paymentRef)->first();
            if ($payment) {
                if ($status === 'SUCCESS' || $status === 'COMPLETED' || $request->query('code') == '1') {
                    $payment->update([
                        'status' => 'success',
                        'transaction_id' => $transactionId ?? $payment->transaction_id,
                    ]);
                } elseif ($payment->status === 'pending' && (! $status || $status === 'SUCCESS')) {
                    // Si le retour client contient un statut de succès sans erreur
                    $payment->update(['status' => 'success']);
                }
            }
        }

        return redirect()->route('my-books')->with('success', 'Votre paiement Mobile Money a été traité avec succès ! Retrouvez votre ouvrage dans « Mes Livres ».');
    }
}
