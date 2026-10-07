<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\Download;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadController extends Controller
{
    /**
     * Display a listing of downloads with analytics & filter options.
     */
    public function index(Request $request): View
    {
        $categories = Category::all();

        // Analytics KPIs
        $totalDownloads = Download::count();
        $downloadsThisMonth = Download::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $freeDownloads = Download::whereHas('book', fn ($q) => $q->where('price', 0))->count();
        $paidDownloads = Download::whereHas('book', fn ($q) => $q->where('price', '>', 0))->count();
        $topBook = Book::withCount('downloads')->orderByDesc('downloads_count')->first();

        // Filtered downloads query
        $query = $this->buildFilteredQuery($request);
        $downloads = $query->latest()->paginate(15)->withQueryString();

        return view('admin.downloads.index', compact(
            'downloads',
            'categories',
            'totalDownloads',
            'downloadsThisMonth',
            'freeDownloads',
            'paidDownloads',
            'topBook'
        ));
    }

    /**
     * Export filtered downloads to a downloadable CSV file.
     */
    public function export(Request $request): StreamedResponse
    {
        $downloads = $this->buildFilteredQuery($request)->latest()->get();
        $fileName = 'export-telechargements-'.now()->format('Y-m-d-His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($downloads) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for Microsoft Excel / LibreOffice compatibility
            fprintf($handle, "\xEF\xBB\xBF");

            // CSV Header
            fputcsv($handle, [
                'ID Log',
                'Date & Heure',
                'Titre de l\'Ouvrage',
                'Auteur de l\'Ouvrage',
                'Catégorie',
                'Tarif (FCFA)',
                'Nom Lecteur',
                'E-mail Lecteur',
                'Adresse IP',
            ], ';');

            foreach ($downloads as $download) {
                fputcsv($handle, [
                    $download->id,
                    $download->created_at?->format('d/m/Y H:i:s') ?? '',
                    $download->book?->title ?? 'Ouvrage supprimé',
                    $download->book?->author ?? 'Inconnu',
                    $download->book?->category?->name ?? 'Général',
                    $download->book ? ($download->book->price == 0 ? 'Gratuit' : $download->book->price) : 'N/A',
                    $download->user?->name ?? 'Visiteur Invité',
                    $download->user?->email ?? 'N/A',
                    $download->ip_address ?? 'N/A',
                ], ';');
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Build filtered query based on request filters.
     */
    protected function buildFilteredQuery(Request $request)
    {
        $query = Download::with(['book.category', 'user']);

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('book', function ($bq) use ($search) {
                    $bq->where('title', 'like', "%{$search}%")
                        ->orWhere('author', 'like', "%{$search}%");
                })->orWhereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        if ($categoryId = $request->query('category_id')) {
            $query->whereHas('book', fn ($q) => $q->where('category_id', $categoryId));
        }

        if ($priceType = $request->query('price_type')) {
            if ($priceType === 'free') {
                $query->whereHas('book', fn ($q) => $q->where('price', 0));
            } elseif ($priceType === 'paid') {
                $query->whereHas('book', fn ($q) => $q->where('price', '>', 0));
            }
        }

        if ($dateFrom = $request->query('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo = $request->query('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        return $query;
    }
}
