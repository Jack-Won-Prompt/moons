<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class ExportCatalog extends Command
{
    protected $signature = 'mm:export';
    protected $description = 'Export per-product gallery URLs to database/data/galleries.json (for local download + reseed)';

    public function handle(): int
    {
        $out = [];
        $totalImgs = 0;
        Product::whereNotNull('source_no')->select('source_no', 'gallery')->orderBy('id')
            ->chunk(1000, function ($chunk) use (&$out, &$totalImgs) {
                foreach ($chunk as $p) {
                    $g = $p->gallery ?? [];
                    if ($g) {
                        $out[$p->source_no] = $g;
                        $totalImgs += count($g);
                    }
                }
            });

        $path = database_path('data/galleries.json');
        file_put_contents($path, json_encode($out, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

        $this->info('Exported galleries for ' . count($out) . ' products, ' . $totalImgs . ' gallery images total.');
        $this->info('File: ' . $path . ' (' . round(filesize($path) / 1048576, 1) . ' MB)');
        return self::SUCCESS;
    }
}
