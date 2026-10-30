# SM Cab Service Website (Laravel + Filament CMS)

Website of **SM Cab Service – Taxi Service in Dehradun** (Kanhaiya Vihar, Kargi, Dehradun, Uttarakhand) with a full admin panel, built for Google Ads landing traffic.
Every text, cab card (name, models, seats, fare), service tile, route, phone number, form field and colour is editable from the admin.

- **Laravel 13 + Filament 5**, PHP 8.3+, MySQL
- Admin panel at `/admin`: pages built from sections (Page Builder), blog posts, cab cards, media library, enquiries (leads) with CSV export,
  site settings (phone, WhatsApp, email, SMTP, colours, buttons, menus, footer, popup, floating buttons, form fields, tracking codes), live SEO score
- Web installer at `/install` (no SSH needed on shared hosting)

## Install on Hostinger (no SSH)

See [INSTALL-HOSTINGER.txt](INSTALL-HOSTINGER.txt). Deploy this repository with hPanel → Git (or upload a ZIP that includes `vendor/`) into `public_html`, then open `/install`.

## Pages after install

| URL | Made of |
|---|---|
| `/` | Home — Banner + Booking Form (One Way / Round Trip / Local / Airport tabs), Trust Badges, Service Tiles, One Way vs Round Trip, Popular Routes, Cab Cards, Char Dham & Hill Tours, FAQ, Why Choose Us, Google Reviews, Trust Factor, CTA |
| `/about-us.php`, `/cab-services.php`, `/join-us.php`, `/contact-us.php` | Company pages (text from the Google Business Profile) |
| `/dehradun-airport-taxi.php`, `/dehradun-to-mussoorie-taxi.php`, `/dehradun-to-rishikesh-haridwar-taxi.php`, `/char-dham-yatra-taxi.php`, `/outstation-taxi-dehradun.php`, `/local-taxi-dehradun.php` | One page per service (footer "Services" links) |
| `/blogs.php` + 2 sample posts | Blog list + sample posts that show the blog format (article, FAQ, cab cards). Edit, copy or delete them in Admin → Blog Posts |
| `/privacy-policy.php`, `/terms-and-conditions.php`, `/cancellation-policy.php`, `/photo-credits.php` | Long text design (please review the text) |
| `/thank-you` | Shown after every form |

Not set yet (fill in Admin → Site Settings): email address and "Send enquiries to" email, SMTP, logo, social links, Google Search Console / Bing codes and tracking codes. Fares are "Get Quote" until real fares are added in Admin → Cabs & Fares.

## Where things are

| Path | What |
|---|---|
| `resources/site/` | Website design: `layout.php` (head, header, footer, popup), `parts.php` (all Page Builder sections), `engine.php` (helpers, forms), `icons.php` (SVG icons) |
| `public/assets/site/` | Website CSS / JS |
| `app/Services/SiteRenderer.php` | Renders a page from the database |
| `app/Filament/Support/SiteFields.php` | Admin fields of every section |
| `app/Http/Controllers/EnquiryController.php` | Form handler (validation, lead, email, thank-you / WhatsApp) |
| `config/site_defaults.php` | Default settings copied in at install (business details, phone, WhatsApp, booking form fields, colours …) |
| `public/images/cabs/` | Default car photos (Wikimedia Commons, see `/photo-credits.php`) — replace from Admin → Cabs & Fares |
| `database/seeders/data/` | Website content copied in at install (pages, service pages, blog posts, cab cards, legal text) |

## Run from this folder

```bash
composer install
php artisan serve
```

Then open `http://localhost:8000/install` (a `.env` with a new key is created automatically on the first visit).
