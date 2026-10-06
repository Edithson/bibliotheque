<?php

namespace App\Jobs;

use App\Models\Book;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ProcessBookFileUploadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Book $book,
        public string $tempPath
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $disk = Storage::disk('local');

        if (! $disk->exists($this->tempPath)) {
            return;
        }

        $extension = pathinfo($this->tempPath, PATHINFO_EXTENSION);
        $finalFilename = "books/{$this->book->slug}-".time().".{$extension}";

        // Move to final private storage
        $disk->move($this->tempPath, $finalFilename);

        // Delete old file if updating
        if ($this->book->file_path && $this->book->file_path !== $finalFilename && $disk->exists($this->book->file_path)) {
            $disk->delete($this->book->file_path);
        }

        $fullPath = $disk->path($finalFilename);
        $pageCount = $this->detectPageCount($fullPath, $extension);

        $this->book->update([
            'file_path' => $finalFilename,
            'nbr_pages' => $pageCount,
        ]);
    }

    /**
     * Detect number of pages from file.
     */
    protected function detectPageCount(string $fullPath, string $extension): int
    {
        if (! file_exists($fullPath)) {
            return 1;
        }

        if (strtolower($extension) === 'pdf') {
            $content = file_get_contents($fullPath);
            if (preg_match_all('/\/Count\s+(\d+)/i', $content, $matches)) {
                $count = (int) max($matches[1]);
                if ($count > 0) {
                    return $count;
                }
            }

            $pageMatches = preg_match_all('/\/Type\s*\/Page\b/i', $content);
            if ($pageMatches > 0) {
                return $pageMatches;
            }
        }

        $fileSize = filesize($fullPath);

        return (int) max(1, ceil($fileSize / 3000));
    }
}
