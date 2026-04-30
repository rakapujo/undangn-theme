# CLAUDE.md — Undangan Theme

Panduan wajib untuk Claude Code saat bekerja di project ini. Baca seluruh file sebelum menulis kode apa pun. Kalau permintaan user bertentangan dengan aturan di file ini, **PERINGATKAN USER LEBIH DULU** sebelum eksekusi — jelaskan rule yang dilanggar dan tawarkan alternatif. Jangan diam-diam ikuti permintaan yang melanggar standar di sini.

---

## Behavior Protocol untuk Claude

1. Kalau user minta sesuatu yang melanggar Bagian 4–8 → **STOP, peringatkan, tawarkan alternatif**.
2. Kalau ambigu → **TANYA**, jangan asumsi.
3. Kalau menyentuh file di luar `wp-content/themes/undangan/` (mis. `.htaccess`, `wp-config.php`, plugin lain) → **konfirmasi dulu**.
4. Kalau akan hapus file/tabel DB → **konfirmasi dulu**.
5. Kalau task butuh tool eksternal (npm install, composer require, wp-cli) → **konfirmasi dulu**.
6. Setelah implementasi → **lint + test sebelum lapor selesai**.

---

## 1. Project Overview

| Field | Value |
|---|---|
| **Nama** | Undangan |
| **Tipe** | SaaS undangan digital |
| **Goal** | User edit undangan secara mandiri lewat dashboard, hasil di subdomain unik |
| **Theme type** | Block theme (Full Site Editing / FSE) |
| **Bahasa** | Indonesia (single language, no multi-lang) |
| **Responsive** | Mobile-first, fully responsive |

### Arsitektur SaaS

- **Single WP installation** dengan custom routing
- Wildcard DNS `*.mengundang.mu` → 1 server
- URL pattern: `{subdomain}.mengundang.mu/{slug}`
- Multiple subdomain bisa share 1 undangan (konten identik) — contoh:
  - `rakapujo.mengundang.mu/tommy-rasta`
  - `selvikumala.mengundang.mu/tommy-rasta`
  - `rakapujoselvi.mengundang.mu/tommy-rasta`
  - Ketiganya render konten yang sama persis
- **Subdomain** = identitas akun pemilik
- **Slug** = ID undangan (CPT `undangan`)
- **Editing** lewat custom dashboard di `mengundang.mu/dashboard` (frontend terpisah, panggil REST API WP)

### Fitur Utama

- Single page undangan
- RSVP form (custom, bukan plugin)
- Galeri foto (Swiper.js)
- Countdown timer
- Music player (HTML5 audio native)
- Dynamic OG image per slug (auto-generate dari foto + nama + tanggal)

---

## 2. Tech Stack

| Komponen | Tool | Versi |
|---|---|---|
| **WordPress** | Core | latest (6.x) |
| **PHP** | — | 8.3 |
| **Build tool** | `@wordpress/scripts` | latest |
| **CSS** | `theme.json` + SCSS | (no Tailwind) |
| **PHP autoload** | Composer + PSR-4 | namespace `Mengundang\Theme\` |
| **Linting PHP** | PHPCS + WordPress-Coding-Standards | — |
| **Linting JS/CSS** | ESLint + Prettier (via `@wordpress/scripts`) | — |
| **Testing PHP** | PHPUnit | — |
| **Galeri** | Swiper.js (npm) | — |
| **Countdown** | Vanilla JS (no library) | — |
| **Musik** | `<audio>` HTML5 native | — |
| **HEIC handling** | `heic2any` (browser-side) | — |
| **REST namespace** | `mengundang/v1` | — |
| **PHP internal prefix** | `mgu_` | — |
| **Plugin dependency** | ZERO — semua self-contained | — |

### Image Library
- **Auto-detect**: pakai Imagick kalau tersedia, fallback ke GD
- Production: install `php8.3-imagick` di Hestia untuk kualitas terbaik

### Cache Stack
- **Object cache**: auto-detect Redis > WP Transient
- **Page cache**: Cloudflare (Free plan, edge cache)

---

## 3. Folder Structure

```
undangan/
├── style.css                    # Header theme WAJIB
├── theme.json                   # Design tokens (sumber tunggal)
├── functions.php                # Bootstrap: Composer autoload + init Theme
├── index.php                    # Fallback PHP
├── screenshot.png
├── README.md
├── CLAUDE.md                    # File ini
├── CHANGELOG.md
│
├── composer.json                # PSR-4 namespace Mengundang\Theme\ → inc/
├── package.json                 # @wordpress/scripts + Swiper + heic2any
├── phpcs.xml                    # WPCS config
├── phpunit.xml                  # PHPUnit config
├── .eslintrc.js
├── .prettierrc
├── .gitignore
├── .env.example
├── .husky/                      # pre-commit & commit-msg hooks
│
├── inc/                         # PHP source (PSR-4 autoloaded)
│   ├── Theme.php                # Main bootstrap class
│   ├── Routing/
│   │   ├── SubdomainRouter.php  # Parse $_SERVER['HTTP_HOST'] → akun
│   │   └── RewriteRules.php     # Rewrite subdomain+slug → CPT query
│   ├── PostTypes/
│   │   └── UndanganCPT.php      # Register CPT 'undangan'
│   ├── Rest/
│   │   ├── RestBootstrap.php
│   │   ├── UndanganController.php
│   │   └── RsvpController.php
│   ├── Repository/
│   │   ├── UndanganRepository.php
│   │   └── RsvpRepository.php   # Query wp_mgu_rsvp custom table
│   ├── Security/
│   │   ├── Nonce.php
│   │   ├── Sanitizer.php
│   │   ├── RateLimiter.php
│   │   └── Honeypot.php
│   ├── Media/
│   │   ├── ImageOptimizer.php   # Main controller
│   │   ├── WebPConverter.php    # GD/Imagick → WebP
│   │   └── ImageResizer.php     # Generate ukuran preset
│   ├── OgImage/
│   │   ├── OgImageGenerator.php # Render dinamis foto+nama+tanggal
│   │   ├── OgImageCache.php
│   │   └── OgImageMeta.php      # Inject <meta og:image>
│   ├── Cache/
│   │   ├── CacheManager.php     # Wrapper wp_cache_* + transient
│   │   ├── CacheKeys.php
│   │   └── CloudflarePurger.php # Purge edge cache via API
│   └── Setup/
│       ├── ThemeSupports.php
│       └── Enqueue.php          # Conditional asset enqueue
│
├── templates/                   # FSE block templates
│   ├── index.html
│   ├── single-undangan.html     # Template utama undangan
│   └── 404.html
│
├── parts/                       # Template parts FSE
│   ├── header.html
│   └── footer.html
│
├── patterns/                    # Block patterns (PHP register)
│   ├── hero.php
│   ├── countdown.php
│   ├── gallery.php
│   ├── rsvp-form.php
│   └── music-player.php
│
├── blocks/                      # Custom blocks (kalau pattern tidak cukup)
│   └── countdown/
│       ├── block.json
│       ├── edit.js
│       ├── save.js
│       ├── view.js              # Frontend behavior
│       └── style.scss
│
├── src/                         # Asset source (di-build oleh wp-scripts)
│   ├── js/
│   │   ├── frontend.js          # Entry: core
│   │   ├── modules/
│   │   │   ├── countdown.js
│   │   │   ├── gallery.js       # Init Swiper
│   │   │   ├── music.js
│   │   │   └── rsvp.js          # Submit form ke REST
│   │   └── editor.js            # Customisasi block editor
│   └── scss/
│       ├── frontend.scss
│       └── partials/
│           ├── _tokens.scss     # Mirror theme.json
│           ├── _hero.scss
│           ├── _countdown.scss
│           ├── _gallery.scss
│           └── _rsvp.scss
│
├── build/                       # Compiled output (gitignored)
│
├── assets/                      # Static (tidak di-build)
│   ├── images/
│   ├── fonts/
│   ├── audio/                   # Default music files
│   └── og-templates/            # Background OG image per tema
│       ├── classic.png
│       ├── floral.png
│       └── modern.png
│
├── languages/                   # .pot file
│
├── tests/                       # PHPUnit
│   ├── bootstrap.php
│   └── Unit/
│       ├── RouterTest.php
│       ├── RsvpTest.php
│       └── ImageOptimizerTest.php
│
└── vendor/                      # Composer (gitignored)
```

### Aturan Folder

- `inc/` = PHP runtime (autoloaded). `src/` = asset source (compile dulu). **Jangan campur.**
- Mulai dari **patterns** dulu. Naik ke **custom block** hanya kalau butuh attribute/interaktivitas di editor.
- `build/`, `vendor/`, `node_modules/`, `.env` → **wajib gitignore**.

---

## 4. Coding Standards

### 4.1 PHP Naming

| Tipe | Konvensi | Contoh |
|---|---|---|
| Namespace | PSR-4 PascalCase | `Mengundang\Theme\Routing\SubdomainRouter` |
| Class | PascalCase | `class UndanganController` |
| Method | **camelCase** | `public function getBySlug()` |
| Property | camelCase | `private string $tableName` |
| Class constant | UPPER_SNAKE | `const REST_NAMESPACE = 'mengundang/v1'` |
| Procedural function | snake_case + prefix | `mgu_get_undangan()` |
| Hook/filter | snake_case + prefix | `do_action( 'mgu_after_render' )` |
| WP option | snake_case + prefix | `mgu_settings` |
| DB table | lowercase + prefix | `wp_mgu_rsvp` |
| File class | PascalCase.php | `UndanganController.php` |
| File template | kebab-case.php/.html | `single-undangan.html` |

**Prefix wajib**: `mgu_` untuk semua hook, filter, option, transient, table custom. **REST namespace** tetap `mengundang/v1` (URL-facing, lebih readable).

### 4.2 PHP Modern (Wajib)

- `declare(strict_types=1);` di setiap file PHP class
- Typed property, parameter, return type **wajib**
- Pakai PHP 8 features: `readonly`, `enum`, `match`, named args, constructor property promotion
- `final class` **by default** kecuali memang dirancang untuk extend
- `private` by default, naikkan visibility hanya saat perlu

```php
declare(strict_types=1);

namespace Mengundang\Theme\Rest;

final class RsvpController {
    public function __construct(
        private readonly RsvpRepository $repo,
        private readonly Sanitizer $sanitizer,
    ) {}

    public function create( \WP_REST_Request $request ): \WP_REST_Response {
        // ...
    }
}
```

### 4.3 JavaScript

| Tipe | Konvensi | Contoh |
|---|---|---|
| File | kebab-case | `gallery-init.js` |
| Variable/function | camelCase | `initCountdown()` |
| Class | PascalCase | `class RsvpForm` |
| Constant | UPPER_SNAKE | `const REST_BASE = '/wp-json/mengundang/v1'` |

- Module ES6 (`import`/`export`), **no jQuery**
- No global pollution (IIFE atau modul)
- ESLint config: `@wordpress/eslint-plugin/recommended`

### 4.4 SCSS

- BEM-lite: `.undangan-hero__title--large`
- **No `!important`** kecuali override WP core (komentari alasan)
- CSS custom properties dari `theme.json` — **jangan hardcode hex/spacing**
- **Mobile-first**: `min-width` queries, bukan `max-width`

```scss
.undangan-hero {
    padding: var(--wp--preset--spacing--40);
    color: var(--wp--preset--color--primary);

    &__title {
        font-size: var(--wp--preset--font-size--large);
    }

    @media (min-width: 768px) {
        padding: var(--wp--preset--spacing--60);
    }
}
```

### 4.5 WordPress-Specific (Wajib)

| Aturan | Contoh |
|---|---|
| Escape semua output | `<?php echo esc_html( $title ); ?>` |
| Sanitize semua input | `sanitize_text_field( $_POST['name'] )` |
| Verify nonce | `wp_verify_nonce()`, `check_ajax_referer()` |
| Capability check | `current_user_can( 'edit_post', $post_id )` |
| Prepared SQL | `$wpdb->prepare( 'WHERE id = %d', $id )` |
| Text domain | `__( 'Save', 'undangan' )` |
| Enqueue, not inline | `wp_enqueue_script()` di hook `wp_enqueue_scripts` |

### 4.6 Dokumentasi Kode

- PHPDoc untuk class & public method
- Inline comment hanya untuk **WHY**, bukan WHAT
- Tidak ada emoji di kode

---

## 5. Security Rules

### 5.1 Sanitization (per tipe input)

| Jenis | Fungsi WP |
|---|---|
| Text biasa | `sanitize_text_field()` |
| Email | `sanitize_email()` + `is_email()` |
| URL | `esc_url_raw()` |
| Textarea | `sanitize_textarea_field()` |
| Slug | `sanitize_title()` |
| Integer | `(int)` cast + `absint()` |
| HTML terbatas | `wp_kses_post()` |
| File | `wp_check_filetype_and_ext()` (cek MIME asli) |

**Aturan**: Tidak ada satupun input dari `$_GET`, `$_POST`, `$_REQUEST`, REST request, atau header yang langsung dipakai tanpa sanitize.

### 5.2 Output Escaping

| Konteks | Fungsi WP |
|---|---|
| HTML body | `esc_html()` |
| Atribut HTML | `esc_attr()` |
| URL `href`/`src` | `esc_url()` |
| Inline JS variable | `wp_json_encode()` (bukan `json_encode`) |
| `<textarea>` | `esc_textarea()` |
| Translated string | `esc_html__()`, `esc_attr__()` |

Escape **late** (saat output), bukan saat simpan.

### 5.3 Nonce / CSRF

- Setiap form custom: `wp_nonce_field( 'mgu_rsvp_submit', '_mgu_nonce' )`
- Setiap REST endpoint write: verify nonce di `permission_callback`
- Action nonce **spesifik per aksi**, jangan reuse `_wpnonce` generic

### 5.4 REST Permission

```php
register_rest_route( 'mengundang/v1', '/undangan/(?P<id>\d+)', [
    'methods'  => 'PATCH',
    'callback' => [ $controller, 'update' ],
    'permission_callback' => function ( $request ) {
        $id = (int) $request['id'];
        return current_user_can( 'edit_post', $id )
            && wp_verify_nonce( $request->get_header( 'X-WP-Nonce' ), 'wp_rest' );
    },
] );
```

- **TIDAK BOLEH** `__return_true` kecuali memang publik (read undangan, submit RSVP)
- Endpoint publik tetap **wajib** rate limit + nonce

### 5.5 SQL

- **Wajib** `$wpdb->prepare()` untuk semua query bervariabel
- **Tidak boleh** string concatenation di SQL
- Tabel custom RSVP query selalu lewat `RsvpRepository`

```php
// ❌ JANGAN
$wpdb->get_results( "SELECT * FROM {$table} WHERE undangan_id = {$id}" );

// ✅ HARUS
$wpdb->get_results( $wpdb->prepare(
    "SELECT * FROM {$table} WHERE undangan_id = %d", $id
) );
```

### 5.6 File Upload

```
1. Cek $_FILES error code
2. wp_check_filetype_and_ext() → MIME asli, bukan extension
3. Whitelist MIME: image/jpeg, image/png, image/webp
4. Max size: 2 MB raw input
5. wp_handle_upload() dengan 'test_form' => false
6. sanitize_file_name()
7. NEVER trust $_FILES['type'] dari client
```

### 5.7 Auto-Optimasi Image (saat upload)

- **Convert ke WebP** kualitas 85
- **Resize** ke 5 ukuran preset:
  - `mgu-thumb` 400×400 (crop)
  - `mgu-medium` 800×600
  - `mgu-large` 1600×1200
  - `mgu-og` 1200×630 (crop, untuk OG share)
  - `full` (max 2000px width)
- **Hapus file asli** JPEG/PNG setelah convert (hemat storage SaaS)
- **HEIC handling**: convert di browser via `heic2any` SEBELUM upload. Server tetap reject MIME `image/heic`.

### 5.8 Rate Limiting (RSVP)

- **Per IP**: max 10/menit (transient based)
- **Per slug**: max 100/jam (anti-DDoS form)
- **Honeypot**: `<input name="website" hidden>` → kalau ada isinya = bot
- **Time-trap**: cek timestamp render vs submit, < 3 detik = bot

### 5.9 CORS

```php
add_filter( 'rest_pre_serve_request', function ( $served, $result, $request ) {
    $origin = get_http_origin();
    if ( $origin && preg_match( '/^https:\/\/[a-z0-9-]+\.mengundang\.mu$/', $origin ) ) {
        header( "Access-Control-Allow-Origin: {$origin}" );
        header( 'Access-Control-Allow-Credentials: true' );
    }
    return $served;
}, 10, 3 );
```

### 5.10 Security Headers

```
X-Frame-Options: SAMEORIGIN
X-Content-Type-Options: nosniff
Referrer-Policy: strict-origin-when-cross-origin
Permissions-Policy: geolocation=(), microphone=(), camera=()
```
**CSP**: skip MVP, lanjut fase 2.

### 5.11 WordPress Hardening

```php
// wp-config.php
define( 'DISALLOW_FILE_EDIT', true );
define( 'DISALLOW_FILE_MODS', true ); // kalau dashboard sudah handle update

// theme bootstrap
add_filter( 'xmlrpc_enabled', '__return_false' );
remove_action( 'wp_head', 'wp_generator' );
// disable user enumeration via ?author=N
```

### 5.12 Secrets

- **TIDAK BOLEH** commit `wp-config.php`, `.env`, API keys ke git
- `.env` di-load via `vlucas/phpdotenv` (Composer) di `wp-config.php`
- `.gitignore`: `wp-config.php`, `.env`, `.env.local`

---

## 6. Performance Rules

### 6.1 Target Core Web Vitals

| Metrik | Target |
|---|---|
| LCP | < 2.5s |
| CLS | < 0.1 |
| INP | < 200ms |
| Lighthouse Performance | > 90 |

### 6.2 JS Bundle Budget

- **< 50 KB gzipped** per page
- Conditional load: script hanya di-enqueue kalau page-nya butuh

```php
if ( has_block( 'mgu/gallery' ) ) {
    wp_enqueue_script( 'mgu-swiper' );
}
```

### 6.3 Asset Loading

- **No render-blocking JS**: semua `<script>` `defer` atau `async`
- **Critical CSS inline** di `<head>` (~5KB), sisanya defer
- **Font preload** hanya untuk above-the-fold
- **Asset versioning**: `filemtime()` untuk cache busting

### 6.4 Bundle Strategy

| Bundle | Isi | Load Condition |
|---|---|---|
| `frontend-core` | Header, footer, scroll | Setiap page |
| `frontend-gallery` | Swiper init | Page dengan galeri |
| `frontend-countdown` | Countdown logic | Page dengan countdown |
| `frontend-music` | Audio player | Page dengan musik |
| `frontend-rsvp` | Form submit | Page dengan RSVP |

### 6.5 Image Frontend

- `loading="lazy"` di semua `<img>` kecuali hero (LCP)
- Hero: `fetchpriority="high"`, `loading="eager"`
- `<img>` **wajib** `width` & `height` (anti-CLS)
- Responsive `srcset` + `sizes` (otomatis dari ukuran preset Bagian 5.7)

### 6.6 Database

**Custom Table RSVP** (index wajib):
```sql
CREATE TABLE wp_mgu_rsvp (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    undangan_id BIGINT UNSIGNED NOT NULL,
    name        VARCHAR(100) NOT NULL,
    attending   TINYINT(1) NOT NULL,
    guests      TINYINT UNSIGNED NOT NULL DEFAULT 1,
    message     TEXT,
    ip_hash     CHAR(64),
    created_at  DATETIME NOT NULL,
    INDEX idx_undangan_created (undangan_id, created_at),
    INDEX idx_ip_hash (ip_hash)
);
```

**Aturan query**:
- Repository pattern: semua DB access lewat `inc/Repository/*`
- `SELECT` kolom spesifik, bukan `SELECT *`
- Pagination wajib (default 20/page)
- WP_Query optimization:
  ```php
  new WP_Query([
      'no_found_rows'          => true,
      'update_post_meta_cache' => false,
      'update_post_term_cache' => false,
      'fields'                 => 'ids',
  ]);
  ```

### 6.7 Caching

| Layer | Tool | TTL |
|---|---|---|
| Browser | `Cache-Control` | 1 tahun (asset), 5 menit (HTML) |
| Object Cache | Redis (auto-detect) > Transient | 1 jam |
| Page Cache | Cloudflare edge | 1 jam |

**Cache Key — abaikan host** (karena konten sama di 3 subdomain):
```php
$cache_key = 'mgu_undangan_' . $slug;
```

### 6.8 Cloudflare Setup

**DNS** (semua Proxied/Orange):
```
A    mengundang.mu    <IP server>
A    *                <IP server>
A    www              <IP server>
```

**SSL/TLS**: Mode "Full (strict)" + Cloudflare Origin Certificate (15 tahun) install di Hestia.

**Cache Rules**:
1. Cache HTML undangan publik (1 jam edge, 5 menit browser, kecuali `wp-admin`, `wp-login`, REST write, cookie `wordpress_logged_in`)
2. Bypass cache untuk admin & API write
3. Long cache (1 bulan edge, 1 tahun browser) untuk `/wp-content/*.{jpg,png,webp,svg,woff2,css,js,mp3}`

**Speed**:
- Brotli ON, Auto Minify CSS/HTML ON, JS Minify OFF (sudah di-minify wp-scripts)
- Rocket Loader OFF (bisa rusak custom JS)
- Early Hints ON, HTTP/2 to Origin ON

**Security**: Bot Fight Mode ON, WAF Managed Rules ON, Security Level Medium.

**Cache Purge Otomatis**:
- Setup: API Token (permission `Zone.Cache Purge`) + Zone ID → simpan di `.env`
- Trigger: hook `save_post_undangan` → `CloudflarePurger::purgeUrlsBySlug( $slug )`
- Purge URLs: `*.mengundang.mu/{slug}` (pattern)

```env
CLOUDFLARE_API_TOKEN=xxxxx
CLOUDFLARE_ZONE_ID=xxxxx
```

### 6.9 REST API Performance

- ETag (hash data) → 304 Not Modified
- Last-Modified header
- Pagination wajib: `?page=1&per_page=20`
- Field selection: `?_fields=id,name,date`
- Response gzip (server-level di Hestia/Nginx)

### 6.10 Frontend Specific

**Countdown**:
- Update UI via `requestAnimationFrame`
- Timer `setTimeout` per detik (bukan per frame)
- Stop timer saat `visibilitychange` hidden

**Music Player**:
- **No autoplay**
- Format MP3, bitrate 128kbps
- `<audio preload="none">` — jangan download sebelum klik

**Gallery (Swiper)**:
- Lazy loading bawaan Swiper
- `loadOnTransitionStart: true`

### 6.11 Server Config (Hestia/Nginx)

- Brotli/Gzip untuk text
- HTTP/2 atau HTTP/3
- `Cache-Control` per asset:
  ```
  /wp-content/uploads/   → 1 tahun, immutable
  /wp-content/themes/    → 1 tahun, immutable (hash di filename)
  /wp-json/*             → no-cache (kecuali GET undangan: 5 menit)
  ```

---

## 7. Workflow & Git

### 7.1 Branching (Trunk-Based)

```
main          ←── deployable, production
  └── feature/<scope>
  └── fix/<scope>
```

- `main` selalu deployable
- Feature branch pendek (max 2-3 hari)
- Merge via PR ke `main`, **squash merge**
- Delete branch setelah merge

### 7.2 Commit Format (Conventional Commits)

```
<type>(<scope>): <subject>

[optional body]

[optional footer]
```

**Types**: `feat`, `fix`, `refactor`, `perf`, `style`, `test`, `docs`, `chore`, `build`

**Scopes umum**: `router`, `rest`, `rsvp`, `gallery`, `countdown`, `music`, `og`, `theme`, `cache`, `security`, `cf`

**Aturan subject**:
- Imperative ("add" bukan "added")
- Max 72 karakter
- No period
- Lowercase

**Contoh**:
```
feat(rsvp): add honeypot field validation
fix(router): resolve 404 on root path subdomain
perf(image): add lazy load for gallery thumbnails
chore(deps): bump @wordpress/scripts to 27.0.0
```

### 7.3 Pre-commit Hooks (Husky)

```
.husky/pre-commit   → npm run lint:php && lint:js && lint:css
.husky/commit-msg   → commitlint (validate Conventional Commits)
```

Tools: `husky` + `lint-staged` + `@commitlint/cli`.

### 7.4 Local Workflow (Laragon)

```bash
# Setup awal
cd c:/laragon/www/undangan
composer install
npm install
cp .env.example .env

# Daily
npm run start         # wp-scripts watch mode

# Sebelum commit
npm run lint
npm run test
npm run build         # verify production build

# Commit
git add .
git commit -m "feat(rsvp): add submit handler"
git push
```

### 7.5 Versioning (SemVer)

`MAJOR.MINOR.PATCH`
- MAJOR: breaking change
- MINOR: fitur baru, backward compatible
- PATCH: bug fix

Update di:
- `style.css` header `Version:`
- `package.json` `"version"`
- Tag git `v1.2.3`
- `CHANGELOG.md` entry

### 7.6 Repo

- GitHub (private)
- Branch protection di `main`: require PR, require lint passing

### 7.7 Deployment (Manual untuk Sekarang)

- Manual deployment dulu (set proper CI/CD nanti)
- Saat deploy manual: `composer install --no-dev`, `npm ci && npm run build`, lalu rsync/SFTP ke server
- Setelah deploy → manual purge Cloudflare cache via dashboard atau CLI

### 7.8 Environment Files

```
.env.example       (commit, no values)
.env               (LOCAL, gitignored)
.env.production    (di server, NOT in git)
```

```env
WP_ENV=development|staging|production
CLOUDFLARE_API_TOKEN=
CLOUDFLARE_ZONE_ID=
DB_HOST=
DB_NAME=
DB_USER=
DB_PASSWORD=
WP_DEBUG=true
```

### 7.9 Testing Sebelum PR

- [ ] PHPCS lint passed
- [ ] ESLint passed
- [ ] PHPUnit unit test passed
- [ ] Manual test: Chrome + Firefox/Safari
- [ ] Manual test mobile (DevTools responsive)
- [ ] Lighthouse Performance > 90

### 7.10 Manual QA per Fitur

- **RSVP**: submit → cek DB ada entry
- **Galeri**: swipe mobile, lazy load
- **Countdown**: cek pasca expired
- **Musik**: play/pause/volume di iOS Safari
- **OG image**: share ke WhatsApp, cek preview
- **Routing**: 3 subdomain berbeda, slug sama → konten identik

---

## 8. DILARANG (Hard Rules)

> **Kalau user minta sesuatu yang ada di list ini, PERINGATKAN dulu, jelaskan rule yang dilanggar, dan tawarkan alternatif. Jangan diam-diam ikuti.**

### 8.1 Security

- DILARANG echo variabel tanpa escape (`echo $var` → harus `echo esc_html($var)`)
- DILARANG pakai `$_GET`/`$_POST`/`$_REQUEST` langsung tanpa sanitize
- DILARANG concatenate variabel ke SQL — selalu `$wpdb->prepare()`
- DILARANG trust `$_FILES['type']` — pakai `wp_check_filetype_and_ext()`
- DILARANG `permission_callback => '__return_true'` kecuali endpoint memang publik (dan tetap rate-limited)
- DILARANG simpan password / API key / token di kode — selalu `.env`
- DILARANG `eval()`, `extract($_POST)`, `unserialize($user_input)`
- DILARANG biarkan REST endpoint tanpa rate limit (DDoS magnet)
- DILARANG cache response yang berisi nonce/CSRF token di edge
- DILARANG expose `wp-json/wp/v2/users` ke publik (user enumeration)

### 8.2 Architecture

- DILARANG require plugin pihak ketiga untuk fungsi inti — semua self-contained
- DILARANG modifikasi WordPress core file
- DILARANG taro logika business di template HTML — pisah ke class di `inc/`
- DILARANG akses `$wpdb` langsung dari controller/template — selalu lewat Repository
- DILARANG load semua asset di setiap page — pakai conditional enqueue
- DILARANG bikin global function — pakai class + namespace
- DILARANG copy-paste library JS/PHP manual — pakai npm/Composer
- DILARANG hardcode subdomain logic — pakai `SubdomainRouter`
- DILARANG pakai cookie untuk session state — pakai REST + nonce

### 8.3 Code Style

- DILARANG pakai jQuery — vanilla JS / module ES6
- DILARANG inline `<style>` di template — selalu di SCSS file
- DILARANG inline `<script>` panjang (kecuali < 1KB & critical inline)
- DILARANG `!important` (kecuali override WP core, komentari alasan)
- DILARANG hardcode warna/spacing di SCSS — pakai CSS var dari `theme.json`
- DILARANG emoji di kode (komentar, variable, output debug)
- DILARANG `var` di JS — pakai `const` / `let`
- DILARANG lupa `declare(strict_types=1);` di file PHP class
- DILARANG lupa type declaration di parameter & return PHP

### 8.4 WordPress-Specific

- DILARANG `query_posts()` — pakai `WP_Query` atau `pre_get_posts`
- DILARANG `the_excerpt()` di REST — render server-side, kirim plain
- DILARANG lupa `wp_reset_postdata()` setelah custom loop
- DILARANG `add_action('init', heavyFunction)` — pakai hook lebih late (`wp_loaded`, `template_redirect`)
- DILARANG auto-load semua post meta — pakai `update_post_meta_cache => false`
- DILARANG `get_option()` di hot loop — cache hasilnya
- DILARANG lupa text domain `'undangan'` di string translatable
- DILARANG lupa nonce di form custom & REST write

### 8.5 Performance

- DILARANG operasi heavy (loop ribuan post, fetch external) di hook `wp_head`/`wp_footer`
- DILARANG N+1 query
- DILARANG synchronous HTTP request di hook frontend — pakai async/queue
- DILARANG load script di `<head>` tanpa `defer`/`async`
- DILARANG render gambar tanpa `width`/`height` (CLS)
- DILARANG `setInterval` < 250ms (boros battery)
- DILARANG `@import` di CSS — pakai `<link rel="preload">`
- DILARANG bundle Swiper untuk page tanpa galeri

### 8.6 UX

- DILARANG autoplay musik dengan suara
- DILARANG popup modal saat page load
- DILARANG disable scroll / nge-trap user
- DILARANG form RSVP yang reset isian setelah error — preserve user input
- DILARANG `alert()` / `confirm()` JS native — pakai modal custom
- DILARANG loader/spinner > 3 detik tanpa progress indicator

### 8.7 Git & Process

- DILARANG commit `.env`, `wp-config.php`, `vendor/`, `node_modules/`, `build/`
- DILARANG commit ke `main` langsung (kecuali hotfix critical)
- DILARANG force push ke `main`
- DILARANG commit kalau lint/test gagal
- DILARANG commit pesan generic ("update", "fix", "wip") — pakai Conventional Commits
- DILARANG delete branch sebelum merge / archive
- DILARANG push API key ke repo (kalau accidentally → rotate key + filter-branch)
- DILARANG auto-update plugin/theme/core di production tanpa staging test
- DILARANG deploy hari Jumat sore tanpa standby

### 8.8 Saat Pakai Claude

- DILARANG generate file dummy/placeholder tanpa selesaikan implementasi
- DILARANG asumsikan ada plugin/theme tertentu — selalu cek dulu
- DILARANG ubah file di luar `wp-content/themes/undangan/` tanpa eksplisit diminta
- DILARANG hapus file/folder tanpa konfirmasi user
- DILARANG bikin DB table tanpa konfirmasi
- DILARANG jalankan `wp-cli` command yang ubah data tanpa konfirmasi
- DILARANG jawab dengan asumsi — tanya kalau ambigu

---

## Referensi Cepat

| Topik | Lokasi |
|---|---|
| Bootstrap theme | `inc/Theme.php` |
| Routing logic | `inc/Routing/SubdomainRouter.php` |
| CPT registration | `inc/PostTypes/UndanganCPT.php` |
| REST endpoints | `inc/Rest/*` |
| RSVP DB queries | `inc/Repository/RsvpRepository.php` |
| Image optimization | `inc/Media/ImageOptimizer.php` |
| OG image generator | `inc/OgImage/OgImageGenerator.php` |
| Cache wrapper | `inc/Cache/CacheManager.php` |
| Cloudflare purge | `inc/Cache/CloudflarePurger.php` |
| Block templates | `templates/*.html` |
| Block patterns | `patterns/*.php` |
| Frontend JS | `src/js/frontend.js` + `src/js/modules/*` |
| Frontend SCSS | `src/scss/frontend.scss` + `src/scss/partials/*` |
| Design tokens | `theme.json` |
| Tests | `tests/Unit/*` |

---

**End of CLAUDE.md** — versi 1.0.0
