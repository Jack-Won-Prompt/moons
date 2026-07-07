<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class FixBrands extends Command
{
    protected $signature = 'mm:brands';
    protected $description = 'Re-derive brand for products that fell back to LUXURY (Korean-first names)';

    /** Korean brand keyword => canonical English brand. Order: longer/specific first. */
    private array $map = [
        '보테가 베네타' => 'Bottega Veneta', '보테가베네타' => 'Bottega Veneta', '보테가' => 'Bottega Veneta',
        '메종 마르지엘라' => 'Maison Margiela', '메종마르지엘라' => 'Maison Margiela',
        '살바토레 페라가모' => 'Ferragamo', '페라가모' => 'Ferragamo',
        '크리스찬 디올' => 'Dior', '디올' => 'Dior',
        '입생로랑' => 'Saint Laurent', '생로랑' => 'Saint Laurent', '이브 생로랑' => 'Saint Laurent',
        '알렉산더 맥퀸' => 'Alexander McQueen', '알렉산더맥퀸' => 'Alexander McQueen',
        '루이 비통' => 'Louis Vuitton', '루이비통' => 'Louis Vuitton',
        '크롬하츠' => 'Chrome Hearts', '반클리프' => 'Van Cleef & Arpels', '반클리프아펠' => 'Van Cleef & Arpels',
        '몽클레어' => 'Moncler', '몽클레르' => 'Moncler',
        '셀린느' => 'Celine', '셀린' => 'Celine',
        '발렌시아가' => 'Balenciaga', '발렌티노' => 'Valentino',
        '불가리' => 'Bvlgari', '버버리' => 'Burberry', '버바리' => 'Burberry',
        '프라다' => 'Prada', '미우미우' => 'Miu Miu', '샤넬' => 'Chanel',
        '구찌' => 'Gucci', '에르메스' => 'Hermes', '펜디' => 'Fendi',
        '지방시' => 'Givenchy', '로에베' => 'Loewe', '고야드' => 'Goyard',
        '발망' => 'Balmain', '겐조' => 'Kenzo', '마르니' => 'Marni',
        '델보' => 'Delvaux', '코치' => 'Coach', '토즈' => 'Tods', '토리버치' => 'Tory Burch',
        '몽블랑' => 'Montblanc', '티파니' => 'Tiffany', '까르띠에' => 'Cartier', '카르티에' => 'Cartier',
        '오프화이트' => 'Off-White', '아크네' => 'Acne Studios', '톰포드' => 'Tom Ford',
        '스톤아일랜드' => 'Stone Island', '지미추' => 'Jimmy Choo', '마이클코어스' => 'Michael Kors',
        '롱샴' => 'Longchamp', '랄프로렌' => 'Ralph Lauren', '폴로' => 'Ralph Lauren',
        '에트로' => 'Etro', '끌로에' => 'Chloe', '클로에' => 'Chloe', '지안비토로시' => 'Gianvito Rossi',
        '톰브라운' => 'Thom Browne', '아페쎄' => 'A.P.C.', '메종키츠네' => 'Maison Kitsune',
        '보스' => 'Hugo Boss', '휴고보스' => 'Hugo Boss', '제냐' => 'Zegna', '브루넬로쿠치넬리' => 'Brunello Cucinelli',
        '입셍' => 'Saint Laurent', '멀버리' => 'Mulberry', '벨루티' => 'Berluti', '발리' => 'Bally',
        '아미' => 'AMI', '꼼데가르송' => 'Comme des Garcons', '스텔라매카트니' => 'Stella McCartney',
        '지방쉬' => 'Givenchy', '위블로' => 'Hublot', '롤렉스' => 'Rolex', '오메가' => 'Omega',
        '태그호이어' => 'Tag Heuer', '브라이틀링' => 'Breitling', '파네라이' => 'Panerai',
        '예거르쿨트르' => 'Jaeger-LeCoultre', '아이더블유씨' => 'IWC', '몽블랑' => 'Montblanc',
        '샴발라' => 'Shamballa', '불가리' => 'Bvlgari',
    ];

    public function handle(): int
    {
        $updated = 0; $still = 0;
        Product::where('brand', 'LUXURY')->select('id', 'name')->orderBy('id')
            ->chunkById(500, function ($chunk) use (&$updated, &$still) {
                foreach ($chunk as $p) {
                    $brand = $this->derive($p->name);
                    if ($brand) { Product::whereKey($p->id)->update(['brand' => $brand]); $updated++; }
                    else { $still++; }
                }
            });

        $this->info("Updated {$updated} brands; {$still} still generic.");
        return self::SUCCESS;
    }

    private function derive(string $name): ?string
    {
        // 1) English brand before "(" even with leading digits (e.g. "3.1 Phillip Lim(...)")
        if (preg_match('/^([A-Za-z0-9][A-Za-z0-9&.\'\- ]*?)\s*\(/u', $name, $m)) {
            $c = trim($m[1]);
            if (strlen($c) >= 2 && preg_match('/[A-Za-z]/', $c)) return $c;
        }
        // 2) Korean keyword map (first match wins; map is ordered specific-first)
        foreach ($this->map as $ko => $en) {
            if (mb_strpos($name, $ko) !== false) return $en;
        }
        return null;
    }
}
