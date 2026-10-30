ADMIN PANEL BASE (Laravel + Filament) — reusable website engine
================================================================
What it is: one admin panel that builds any service-business website:
Pages, Landing Pages (own header/footer, no diverting links), Blog + Categories,
Services (cards), Page Builder (26 sections), forms -> Email and/or WhatsApp,
SEO (schema, FAQ schema, redirects), Site Settings.

Install on Hostinger: follow INSTALL-HOSTINGER.txt (upload, open /install).
Local preview: PHP 8.3+, then:  php artisan serve   (needs a database; SQLite works)

For a new industry: keep this engine, then change content in the admin
(Site Settings -> business details, form fields, menu; Pages; Services) or ask Claude
to build the pages/blocks/fields for that industry on top of this base.

Not included on purpose: local database, logs, installer lock, test data, uploads.