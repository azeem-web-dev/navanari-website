# Navanari — Women's Fashion Boutique 🌸

An elegant, fully-customizable **catalog + WhatsApp-enquiry** website with a complete **admin panel**, built with **Laravel** and **Tailwind CSS**. Designed for women's fashion — sarees, dresses, jewellery, footwear, bags, beauty & more — and ready to deploy on **shared hosting (cPanel)**.

> There is **no online checkout / payment**. Customers browse the catalog and tap **“Enquire on WhatsApp”**, which opens WhatsApp pre-filled with the product details and logs the enquiry in the admin panel.

---

## ✨ Features

### Storefront (public, no login required)
- Animated, modern boutique UI (scroll-reveal, hero slider, hover effects, parallax accents)
- Home page with hero slider, featured collections, bestsellers, new arrivals, stats & testimonials
- Shop page with **search**, **category / price / on-sale filters** and **sorting**
- Product pages with image gallery, **size/colour variants**, sale pricing, related products
- **WhatsApp enquiry** button — pre-fills product name, code, price, chosen variant & link
- **Wishlist** saved in the browser (localStorage) — no account needed
- **Reviews & ratings** — visitors submit, admin approves
- Contact form, About page, floating WhatsApp button

### Admin panel (`/admin`)
- Dashboard with stats and a 7-day enquiries chart
- **Products** — full CRUD, multiple images (upload or URL), variants, sale price, per-product price visibility, featured/active toggles
- **Categories** — CRUD with images and featured flag
- **Promotions** — hero sliders, announcement bar & promo strips
- **Enquiries** — every WhatsApp/contact enquiry logged, with status tracking
- **Reviews** — approve / unpublish / delete
- **Settings** — branding, colours, logo, hero, contact & social links, currency, **global price visibility toggle**, page content — *fully customizable without touching code*

---

## 🛠 Tech Stack
- **Laravel** (PHP 8.3+) · **Blade** templates
- **Tailwind CSS 3** + **Alpine.js** (compiled with Vite to static assets — shared-hosting friendly)
- **MySQL** for production · **SQLite** for local development

---

## 🚀 Local Development

```bash
# 1. Install dependencies
composer install
npm install

# 2. Environment
cp .env.example .env
php artisan key:generate

# 3. Database (SQLite by default — zero config)
touch database/database.sqlite
php artisan migrate --seed

# 4. Build assets
npm run build      # or: npm run dev

# 5. Serve
php artisan serve
```

Visit **http://127.0.0.1:8000**.

### Default admin login
```
URL:      /login
Email:    admin@navanari.test
Password: password
```
> ⚠️ Change this immediately. Create a new admin with:
> `php artisan navanari:make-admin you@email.com --name="Your Name"`

---

## 🌐 Deploy to Shared Hosting (cPanel)

1. **Create a MySQL database** and user in cPanel, then update `.env`:
   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://yourdomain.com

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_DATABASE=your_db
   DB_USERNAME=your_user
   DB_PASSWORD=your_pass

   FILESYSTEM_DISK=public
   WHATSAPP_NUMBER=919999999999
   ```
2. **Upload the project** (compiled assets in `public/build` are already committed, so Node is **not** required on the server).
3. Point your domain's document root to the **`public/`** folder. *(If you can't change the docroot, move the contents of `public/` to `public_html/` and update the require paths in `index.php` to the project location.)*
4. Run (via SSH or a one-off script):
   ```bash
   composer install --no-dev --optimize-autoloader
   php artisan key:generate
   php artisan migrate --seed --force
   php artisan storage:link
   php artisan config:cache
   ```
5. Log in at `/login`, then open **Admin → Settings** and set your store name, logo, WhatsApp number, colours and content.

> If `php artisan storage:link` is unavailable on your host, manually create a symlink (or folder) from `public/storage` → `storage/app/public` so uploaded images are served.

---

## ⚙️ Customization
Everything below is editable from **Admin → Settings** — no code changes needed:
- Store name, tagline, description, logo
- Primary & accent colours (applied live across the site)
- Hero heading/subheading/image, announcement bar
- WhatsApp number, phone, email, address, social links
- Currency symbol and the **global "show prices" toggle**
- About text, footer note, shipping note

---

## 🧪 Tests
```bash
php artisan test
```
Covers storefront pages, the WhatsApp enquiry flow, review submission, admin access control, every admin page, product creation and settings.

---

Crafted with ♥ for the modern woman.
