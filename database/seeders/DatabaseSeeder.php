<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Partner;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /* ---------------------------------------------------------------
         | Accounts — one per login area (customer / admin / partner)
         * -------------------------------------------------------------- */
        User::updateOrCreate(
            ['email' => 'customer@moons.com'],
            ['name' => '문스 고객', 'password' => 'password']
        );

        Admin::updateOrCreate(
            ['email' => 'admin@moons.com'],
            ['name' => '최고 관리자', 'password' => 'password', 'role' => 'super']
        );

        $partnerA = Partner::updateOrCreate(
            ['email' => 'partner@moons.com'],
            [
                'company_name' => '루이스 럭셔리',
                'name'         => '김파트너',
                'password'     => 'password',
                'phone'        => '02-1234-5678',
                'brand'        => 'GUCCI',
                'status'       => 'approved',
            ]
        );

        $partnerB = Partner::updateOrCreate(
            ['email' => 'partner2@moons.com'],
            [
                'company_name' => '메종 셀렉트',
                'name'         => '이셀렉',
                'password'     => 'password',
                'phone'        => '02-9876-5432',
                'brand'        => 'PRADA',
                'status'       => 'approved',
            ]
        );

        $partners = [$partnerA->id, $partnerB->id];

        /* ---------------------------------------------------------------
         | Categories — trenbe-style top nav + sub categories
         * -------------------------------------------------------------- */
        $tree = [
            ['여성', 'women', '👗', ['의류', '아우터', '원피스']],
            ['남성', 'men', '🧥', ['의류', '셔츠', '팬츠']],
            ['가방', 'bags', '👜', ['숄더백', '토트백', '크로스백', '백팩']],
            ['신발', 'shoes', '👠', ['스니커즈', '로퍼', '힐', '부츠']],
            ['지갑/액세서리', 'accessories', '👛', ['지갑', '벨트', '선글라스', '스카프']],
            ['시계/주얼리', 'jewelry', '⌚', ['시계', '목걸이', '반지', '귀걸이']],
            ['뷰티', 'beauty', '💄', ['향수', '스킨케어', '메이크업']],
            ['키즈', 'kids', '🧸', ['키즈의류', '키즈슈즈']],
        ];

        $leafCategories = [];
        $sort = 0;

        foreach ($tree as [$name, $slug, $icon, $children]) {
            $root = Category::updateOrCreate(
                ['slug' => $slug],
                ['name' => $name, 'icon' => $icon, 'sort' => $sort++, 'is_active' => true]
            );

            $csort = 0;
            foreach ($children as $child) {
                $leaf = Category::updateOrCreate(
                    ['slug' => $slug . '-' . $csort],
                    [
                        'parent_id' => $root->id,
                        'name'      => $child,
                        'icon'      => $icon,
                        'sort'      => $csort++,
                        'is_active' => true,
                    ]
                );
                $leafCategories[] = $leaf->id;
            }
        }

        /* ---------------------------------------------------------------
         | Products — generated across brands & categories
         * -------------------------------------------------------------- */
        $brands = [
            'GUCCI', 'PRADA', 'LOUIS VUITTON', 'CHANEL', 'HERMÈS', 'DIOR',
            'BALENCIAGA', 'BOTTEGA VENETA', 'SAINT LAURENT', 'BURBERRY',
            'CELINE', 'FENDI', 'MONCLER', 'OFF-WHITE', 'LOEWE', 'VALENTINO',
        ];

        $nouns = [
            '레더 토트백', 'GG 마몬트 숄더백', '클래식 카드홀더', '실크 스카프',
            '울 블렌드 코트', '캐시미어 니트', '레더 스니커즈', '첼시 부츠',
            '오버사이즈 셔츠', '테일러드 자켓', '데님 팬츠', '미니 크로스백',
            '레더 벨트', '스퀘어 선글라스', '오토매틱 워치', '진주 목걸이',
            '시그니처 지갑', '패딩 점퍼', '플리츠 스커트', '로고 볼캡',
        ];

        $gradients = [
            '#1a1a2e,#16213e', '#2c1810,#5c2e0e', '#3d2c2e,#663d3d',
            '#0f3443,#34e89e', '#232526,#414345', '#41295a,#2f0743',
            '#42275a,#734b6d', '#1c1c1c,#3a3a3a', '#4b1248,#f0c27b',
            '#334d50,#cbcaa5', '#2b5876,#4e4376', '#603813,#b29f94',
            '#8e0e00,#1f1c18', '#485563,#29323c', '#000428,#004e92',
            '#3e5151,#decba4',
        ];

        $counter = 0;
        foreach ($leafCategories as $catId) {
            $per = 2 + ($catId % 2); // 2-3 products per leaf category
            for ($i = 0; $i < $per; $i++) {
                $brand = $brands[$counter % count($brands)];
                $noun  = $nouns[$counter % count($nouns)];
                $price = (5 + ($counter * 37) % 60) * 100000 + 90000; // 590,000 ~ 6,090,000
                $discounted = $counter % 3 !== 0;
                $sale  = $discounted
                    ? (int) round($price * (1 - (10 + ($counter * 7) % 45) / 100) / 1000) * 1000
                    : null;

                Product::updateOrCreate(
                    ['slug' => 'p-' . ($counter + 1)],
                    [
                        'category_id' => $catId,
                        'partner_id'  => $partners[$counter % count($partners)],
                        'brand'       => $brand,
                        'name'        => $noun,
                        'description' => $brand . ' ' . $noun . ' — 정품 보장, 유럽 현지 직소싱 상품입니다. 트렌드를 선도하는 이번 시즌 컬렉션을 만나보세요.',
                        'price'       => $price,
                        'sale_price'  => $sale,
                        'color'       => $gradients[$counter % count($gradients)],
                        'stock'       => 5 + ($counter % 20),
                        'is_new'      => $counter % 4 === 0,
                        'is_best'     => $counter % 5 === 0,
                        'is_active'   => true,
                        'view_count'  => ($counter * 137) % 5000,
                    ]
                );
                $counter++;
            }
        }
    }
}
