# MOONS — 럭셔리 셀렉트샵 (Laravel)

트렌비(trenbe) 스타일의 최신 UI/UX를 적용한 명품 커머스 웹앱입니다.
**미스터문(mistermoon.co.kr) 전체 카탈로그(7개 카테고리 · 14,386개 제품)** 를 스크래핑해 실제 상품·이미지·상세 갤러리로 구현했으며,
**고객 / 관리자 / 파트너** 3개 로그인 영역을 Laravel 멀티 가드로 분리했습니다.

## 스택
- Laravel 12 · PHP 8.2 (XAMPP Apache) · MySQL
- 빌드 스텝 없는 커스텀 CSS 디자인 시스템 (`assets/css/app.css`, `panel.css`)

## 주요 기능
- **스토어프론트**: 홈(히어로·카테고리·신상/베스트/세일), 카테고리 상품 리스트(브랜드 필터·정렬·페이지네이션), 상품 상세(갤러리 썸네일), 검색
- **고객 영역** (`/login`, `web` 가드): 로그인·회원가입·마이페이지
- **관리자 영역** (`/admin/login`, `admin` 가드): 대시보드, 상품 CRUD, 파트너 승인/정지
- **파트너 영역** (`/partner/login`, `partner` 가드): 입점 신청→승인, 본인 상품 CRUD

## 로컬 설치 (XAMPP, `localhost/moons`)
```bash
# 1) 의존성 — Apache가 PHP 8.2를 쓰므로 플랫폼을 고정한다 (중요)
composer config platform.php 8.2.12
composer install

# 2) 환경설정
cp .env.example .env          # APP_URL=http://localhost/moons, DB=moons(mysql, root)
php artisan key:generate

# 3) DB
mysql -u root -e "CREATE DATABASE moons CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
php artisan migrate

# 4) 계정 + 데모 카테고리/상품
php artisan db:seed

# 5) 미스터문 전체 카탈로그 적용 (14,386개)
php artisan db:seed --class=MisterMoonSeeder
```
> **라우팅**: vhost 없이 `localhost/moons`로 열리도록 프로젝트 루트에 프론트 컨트롤러(`index.php` + `.htaccess`)를 둡니다. Apache 문서 루트가 `htdocs`이면 그대로 동작합니다.

## 데모 계정 (비밀번호 모두 `password`)
| 영역 | 이메일 |
|---|---|
| 고객 | customer@moons.com |
| 관리자 | admin@moons.com |
| 파트너 | partner@moons.com |

## 아티즌 명령
- `php artisan db:seed --class=MisterMoonSeeder` — 미스터문 카탈로그 재적재 (`database/data/*.json` 기반)
- `php artisan mm:galleries` — 상세페이지에서 갤러리 이미지 보충 (이어하기·동시요청)
- `php artisan mm:brands` — 한글 상품명의 브랜드를 한→영 매핑으로 교정

## 이미지 정책
대표 이미지 1,680개는 `assets/products/`에 로컬 저장했으나 **용량(~266MB) 때문에 저장소에서 제외**했습니다.
클론 후 시더를 돌리면 로컬 파일이 없는 상품은 미스터문 원본 URL(`https://mistermoon.co.kr/web/product/...`)을 참조하므로 사이트는 그대로 동작합니다.

---
데모/학습 목적의 프로젝트입니다. 상품 데이터·이미지의 저작권은 각 브랜드 및 미스터문에 있습니다.
