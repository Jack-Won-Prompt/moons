<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MisterMoonSeeder extends Seeder
{
    private const SRC = 'https://mistermoon.co.kr';

    public function run(): void
    {
        // Full catalog list; fall back to the 5-page subset if the full file is absent.
        $full = database_path('data/mistermoon_full.json');
        $subset = database_path('data/mistermoon.json');
        $listFile = is_file($full) ? $full : $subset;

        if (! is_file($listFile)) {
            $this->command->error('catalog json not found');
            return;
        }

        $data  = json_decode(file_get_contents($listFile), true);
        $cats  = $data['cats'];
        $items = $data['products'];

        // Gallery enrichment: source_no => [gallery urls]  (from the detail-scraped subset)
        $galMap = [];
        if (is_file($subset)) {
            foreach (json_decode(file_get_contents($subset), true)['products'] as $p) {
                if (! empty($p['gallery'])) {
                    $galMap[$p['no']] = array_map(fn ($g) => self::SRC . $g, $p['gallery']);
                }
            }
        }

        // ---- Reset catalog (keep accounts intact) ----
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Product::truncate();
        Category::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // ---- Categories ----
        $catMap = [];
        $sort = 0;
        foreach ($cats as $cateNo => [$name, $slug, $icon]) {
            $catMap[$cateNo] = Category::create([
                'name' => $name, 'slug' => $slug, 'icon' => $icon,
                'sort' => $sort++, 'is_active' => true,
            ])->id;
        }

        // newest ~1000 (highest source no) get NEW badge
        $allNos = array_column($items, 'no');
        rsort($allNos);
        $newThreshold = $allNos[min(1000, count($allNos) - 1)] ?? 0;

        // ---- Products ----
        $rows = [];
        $now = now();
        $imported = 0; $localImgs = 0;
        foreach ($items as $p) {
            $catId = $catMap[$p['cate']] ?? null;
            if (! $catId) continue;

            $price = (int) ($p['price'] ?: 0);
            $sale  = ! empty($p['sale']) ? (int) $p['sale'] : null;
            if ($price <= 0) $price = $sale ?: 100000;
            $discount = ($sale && $price) ? round(($price - $sale) / $price * 100) : 0;

            // Prefer a locally-downloaded image; else reference remotely.
            $image = null;
            $local = 'assets/products/' . $p['no'] . '.jpg';
            if (is_file(base_path($local))) { $image = $local; $localImgs++; }
            elseif (! empty($p['img'])) { $image = self::SRC . '/web/product/medium/' . $p['img']; }

            $gallery = $galMap[$p['no']] ?? null;

            $rows[] = [
                'category_id' => $catId,
                'partner_id'  => null,
                'brand'       => mb_substr($p['brand'] ?: 'LUXURY', 0, 80),
                'name'        => mb_substr($p['name'], 0, 250),
                'slug'        => 'mm-' . $p['no'],
                'description' => $p['brand'] . ' ' . $p['name']
                    . ' — 미스터문 정품 명품. 병행수입 정품 보장 및 명품 감정 완료 상품입니다. 안전한 거래와 빠른 배송으로 만나보세요.',
                'price'       => $price,
                'sale_price'  => $sale,
                'image'       => $image,
                'gallery'     => $gallery ? json_encode($gallery, JSON_UNESCAPED_SLASHES) : null,
                'source_no'   => $p['no'],
                'color'       => '#1c1c1c,#3a3a3a',
                'stock'       => 3 + ($p['no'] % 20),
                'is_new'      => $p['no'] >= $newThreshold,
                'is_best'     => $discount >= 60,
                'is_active'   => true,
                'view_count'  => ($p['no'] * 7) % 9000,
                'created_at'  => $now,
                'updated_at'  => $now,
            ];
            $imported++;
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            Product::insert($chunk);
        }

        $this->command->info("Imported {$imported} products (" . count($catMap) . " categories, {$localImgs} local imgs, " . count($galMap) . " with gallery).");
    }
}
