<?php

namespace App\Console\Commands;

use App\Models\DocumentFile;
use App\Traits\WasabiTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class UpToWasabi extends Command
{

    use WasabiTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:up-to-wasabi';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $documentFiles = DocumentFile::where('file_path', 'like', 'documents/%')->get();

        foreach ($documentFiles as $documentFile) {
            $localPath = $documentFile->file_path;
            $fileContents = Storage::disk('public')->get($localPath);

            $wasabiPath = $this->uploadToWasabi($fileContents);

            $documentFile->file_path = $wasabiPath;
            $documentFile->save();

            // Storage::disk('public')->delete($localPath);
        }

        $this->info('Files have been migrated to Wasabi.');
    }
}
