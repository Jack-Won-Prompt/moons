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

## 이미지 정책 (로컬 전용, 원격 참조 없음)
모든 이미지는 **로컬 파일만 참조**하며 미스터문 원본 URL을 런타임에 참조하지 않습니다.
- 대표 이미지: `assets/products/{source_no}.jpg` (14,386개)
- 상세 갤러리: `assets/products/g/...` (약 100,000개)

용량이 크므로(~7GB) 이미지 자체는 **git에서 제외**(`/assets/products` 무시)하고 **FTP로 서버에 업로드**합니다.
경로 재현·재시딩에 필요한 메타데이터(`database/data/galleries.json` 등)는 저장소에 포함되어, 이미지를 올린 뒤 `MisterMoonSeeder`를 돌리면 DB가 로컬 경로로 채워집니다.
로컬 이미지를 다시 내려받는 스크립트는 세션 스크래치패드의 `dl_main.php` / `dl_gallery.php`를 참고하세요.

---
데모/학습 목적의 프로젝트입니다. 상품 데이터·이미지의 저작권은 각 브랜드 및 미스터문에 있습니다.
