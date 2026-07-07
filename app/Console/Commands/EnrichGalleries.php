<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class EnrichGalleries extends Command
{
    protected $signature = 'mm:galleries {--batch=15} {--limit=0}';
    protected $description = 'Fetch MisterMoon detail pages to fill product galleries (상세 이미지)';

    private const SRC = 'https://mistermoon.co.kr';
    private const UA  = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120 Safari/537.36';

    public function handle(): int
    {
        $batch = (int) $this->option('batch');
        $limit = (int) $this->option('limit');

        $query = Product::whereNull('gallery')->whereNotNull('source_no');
        $totalToDo = $limit > 0 ? min($limit, $query->count()) : $query->count();
        $this->info("Enriching galleries for {$totalToDo} products (batch {$batch})...");

        $done = 0; $hits = 0;
        $query->select('id', 'source_no')->orderBy('id')
            ->chunkById($batch, function ($chunk) use (&$done, &$hits, $limit, $batch) {
                $mh = curl_multi_init();
                $handles = [];
                foreach ($chunk as $p) {
                    $ch = curl_init(self::SRC . "/product/detail.html?product_no={$p->source_no}");
                    curl_setopt_array($ch, [
                        CURLOPT_RETURNTRANSFER => true, CURLOPT_USERAGENT => self::UA,
                        CURLOPT_TIMEOUT => 30, CURLOPT_FOLLOWLOCATION => true, CURLOPT_SSL_VERIFYPEER => false,
                    ]);
                    curl_multi_add_handle($mh, $ch);
                    $handles[$p->id] = $ch;
                }
                do { $st = curl_multi_exec($mh, $run); curl_multi_select($mh, 1.0); } while ($run && $st == CURLM_OK);

                foreach ($handles as $id => $ch) {
                    $html = curl_multi_getcontent($ch);
                    curl_multi_remove_handle($mh, $ch);
                    $gallery = [];
                    if ($html && preg_match_all('#<img[^>]*class="ThumbImage"[^>]*>#i', $html, $imgs)) {
                        foreach ($imgs[0] as $tag) {
                            if (preg_match('#(/web/product/(?:extra/)?(?:small|medium)/\d+/[a-f0-9]+\.[a-z]+)#i', $tag, $m)) {
                                $gallery[] = self::SRC . $m[1];
                            }
                        }
                    }
                    $gallery = array_values(array_unique($gallery));
                    // store [] when none found so we don't re-fetch forever
                    Product::whereKey($id)->update(['gallery' => $gallery]);
                    if ($gallery) $hits++;
                    $done++;
                }
                curl_multi_close($mh);

                if ($done % 300 < $batch) {
                    $this->line("  ... {$done} done ({$hits} with gallery)");
                }
                usleep(120000);

                if ($limit > 0 && $done >= $limit) {
                    return false; // stop chunking
                }
            });

        $this->info("DONE. processed={$done}, filled gallery={$hits}");
        return self::SUCCESS;
    }
}
