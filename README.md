# Poula Adel — Portfolio CMS

A personal developer portfolio built with **Laravel 13** and a **Filament 5** admin dashboard. Everything on the public website — profile, photo, CV, skills, projects, experience, services, education, social links, colors and SEO — is managed from `/admin` without touching code.

- **Public site:** Blade, Tailwind CSS 4, Alpine.js, CSS-only 3D hero (tilted orbit of skill logos) and pointer tilt on cards, light/dark theme toggle
- **Admin:** Filament 5 at `/admin` (login only — no public registration)
- **Database:** MySQL (SQLite in-memory for tests)

---

## Requirements

| Tool | Version |
|---|---|
| PHP | 8.3+ with `gd`, `intl`, `zip`, `fileinfo`, `pdo_mysql`, `mbstring`, `exif` |
| Composer | 2.x |
| Node.js / npm | Node 20+ |
| MySQL | 8.0+ (MariaDB 10.6+ also works) |

## Installation (local)

```bash
git clone <your-repo-url> portfolio && cd portfolio
composer install
npm install
cp .env.example .env
php artisan key:generate
```

### Environment configuration

Edit `.env`:

```dotenv
APP_NAME="Poula Adel Portfolio"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=portfolio
DB_USERNAME=root
DB_PASSWORD=

FILESYSTEM_DISK=public

# Mail is used to notify you about new contact messages (optional)
MAIL_MAILER=smtp
MAIL_HOST=...
MAIL_PORT=587
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_FROM_ADDRESS="no-reply@your-domain.com"

# Optional, local only: lets `db:seed` create an admin for you
ADMIN_NAME="Poula Adel"
ADMIN_EMAIL=
ADMIN_PASSWORD=
```

Create the database (`CREATE DATABASE portfolio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;`), then:

### Database migrations & seeders

```bash
php artisan migrate          # create tables
php artisan db:seed          # editable demo content (clearly labelled "Demo")
# or both at once, wiping existing data:
php artisan migrate:fresh --seed
```

The seeders add your profile basics, skill categories with colored brand icons, services, and **demo** projects/experience/education entries for you to replace in the dashboard. One demo project is intentionally left as a draft to show that drafts stay hidden.

### Create an admin account

```bash
php artisan portfolio:make-admin
# non-interactive:
php artisan portfolio:make-admin --name="Poula Adel" --email=you@example.com --password="a-strong-password-123"
```

Passwords must be at least 12 characters with letters and numbers. Running the command again with the same email updates that user. Production credentials are never stored in code or seeders.

### Storage

Uploaded images, the CV and branding files live on the `public` disk:

```bash
php artisan storage:link
```

Uploaded images are converted to optimized WebP files (plus a small responsive version) automatically. SVG uploads are rejected for security; brand icons come from the built-in Simple Icons set instead.

### Build assets & run

```bash
npm run build            # production build (minified)
php artisan serve        # http://localhost:8000
# or, for development with hot reload + queue + logs:
composer run dev
```

Admin dashboard: <http://localhost:8000/admin>

### Queue (contact-form emails)

Notification emails are queued. Run a worker:

```bash
php artisan queue:work
```

If you'd rather not run a worker, set `QUEUE_CONNECTION=sync` in `.env`.

---

## Using the dashboard

| Menu | What it controls |
|---|---|
| **Projects** | Case studies: thumbnail, gallery, category, technologies, live/GitHub links, rich-text overview/challenge/solution, featured & published toggles, drag-and-drop order, SEO. Drafts are never public; admins can preview them via **Preview draft**. Deleted projects go to the trash (restore or delete forever). |
| **Project categories / Technologies** | Filters on the projects page and the tech chips on cards. |
| **Experiences** | Timeline entries (company, role, dates, "currently working here", responsibilities, technologies). |
| **Skills / Skill categories** | Skill cards with an icon (search Simple Icons slugs such as `laravel`, or upload a PNG/WebP), a brand color, optional proficiency, visibility and order. |
| **Services / Education & certificates** | Services and credential cards. |
| **Profile** | Name, job title, intro, biography, **profile photo**, **CV (PDF)**, email, phone (hidden unless "show publicly" is on), location, years of experience, availability. |
| **Website settings** | Site name, logo, favicon, brand colors, default light/dark theme, SEO title/description/share image, footer text, contact form on/off, notification email. |
| **Social links** | Icons in the hero rail, contact section and footer. |
| **Messages** | Contact-form inbox: opening a message marks it read; mark unread, change status, reply by email, delete. |

Changes appear on the website immediately — public pages are cached and the cache is cleared automatically whenever content changes.

## Public pages

| URL | Purpose |
|---|---|
| `/` | One-page portfolio: Home, About, Skills, Projects, Experience, Services, Education, Contact |
| `/projects` | All published projects with category and technology filters |
| `/projects/{slug}` | Project details |
| `/cv` | Downloads the uploaded CV |
| `/sitemap.xml`, `/robots.txt` | SEO (admin and Livewire paths are disallowed) |

## Security notes

- `/admin` requires login and the `is_admin` flag; all content models are protected by policies.
- Contact form: CSRF, validation, HTML stripping, honeypot field, minimum fill-time check, and rate limiting (3/minute, 10/day per IP).
- Uploads: JPG/PNG/WebP only for images (5 MB), PDF only for the CV (5 MB).
- Rich text is sanitized before rendering; security headers are added to every response; the admin is served with `X-Robots-Tag: noindex`.

## Tests

```bash
php artisan test
```

Covers admin authentication and authorization, project CRUD (including uploads, trash and restore), publishing/visibility, filtering, project detail pages, the contact form (spam protection and rate limiting), upload validation, profile/settings updates and cache invalidation. Tests run on in-memory SQLite and don't need the Vite build.

## Deployment

Any PHP 8.3 host works (Laravel Cloud, Forge, a VPS with Nginx + PHP-FPM, or shared hosting with SSH).

```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan storage:link
php artisan portfolio:make-admin      # first deploy only
php artisan optimize                  # config, route, view and event caches
php artisan filament:optimize
php artisan icons:cache
```

Production `.env` essentials: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://your-domain.com`, real database and mail credentials, and `CACHE_STORE`/`SESSION_DRIVER`/`QUEUE_CONNECTION` (`database` works; `redis` is faster). Point the web server's document root to `public/`, make `storage/` and `bootstrap/cache/` writable by the web user, and keep a queue worker running (Supervisor or your host's daemon feature). After each deploy, run `php artisan optimize` and `php artisan queue:restart`.

**Performance (needed for top Lighthouse scores):** enable PHP OPcache, serve text assets compressed (gzip or Brotli), and send long-lived cache headers for `/build/` (file names are content-hashed) and `/storage/`. Laravel Cloud and Forge do this by default. On a plain Nginx server:

```nginx
gzip on;
gzip_types text/css application/javascript image/svg+xml application/json;

location ~* ^/(build|storage)/ {
    expires 1y;
    add_header Cache-Control "public, immutable";
    try_files $uri =404;
}
```
