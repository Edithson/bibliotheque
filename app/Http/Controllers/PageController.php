<?php

namespace App\Http\Controllers;

use App\Mail\ContactSubmittedMail;
use App\Models\Book;
use App\Models\Contact;
use App\Models\Download;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * Display the 'About' page.
     */
    public function about(): View
    {
        return view('home.pages.about');
    }

    /**
     * Display the contact form.
     */
    public function contact(Request $request): View
    {
        $selectedSubject = $request->query('subject', 'general');
        $prefilledMessage = '';

        if ($selectedSubject === 'author_request') {
            $prefilledMessage = "Bonjour,\n\nJe souhaite proposer mes ouvrages numériques sur La Bibliothèque des Mots.\nVoici une brève présentation de mes écrits : ";
        }

        return view('home.pages.contact', compact('selectedSubject', 'prefilledMessage'));
    }

    /**
     * Handle contact form submission.
     */
    public function sendContact(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string'],
            'message' => ['required', 'string', 'min:10'],
        ]);

        $contact = Contact::create($validated);

        $adminEmail = config('mail.admin_address', 'moafogaus@gmail.com');
        Mail::to($adminEmail)->queue(new ContactSubmittedMail($contact));

        return redirect()->route('contact')->with('success', 'Votre message a été transmis au bibliothécaire avec succès et enregistré dans nos registres.');
    }

    /**
     * Display logged-in user's library (purchased and downloaded e-books).
     */
    public function myBooks(): View|RedirectResponse
    {
        $user = auth()->user();

        if (! $user) {
            return redirect()->route('login')->with('info', 'Veuillez vous connecter pour consulter vos livres.');
        }

        $downloadedBookIds = Download::where('user_id', $user->id)->pluck('book_id')->unique();
        $books = Book::with('category')->whereIn('id', $downloadedBookIds)->get();

        return view('home.pages.my_books', compact('books'));
    }
}
