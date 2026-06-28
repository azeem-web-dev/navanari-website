# 🚀 Deploying Navanari to Hostinger (with SSH)

This is a **Laravel** app, so a `git pull` alone is not enough — you must install
dependencies, create a `.env`, and run migrations **once**. After that, every
push just needs a quick re-deploy. You have SSH, so this is straightforward.

Your SSH login looks like:
```
ssh -p 65002 u525408471@145.79.25.36
```

---

## ✅ One-time setup (do this once)

### 1. Create the database (hPanel)
hPanel → **Databases → MySQL Databases** → create a database + user, and note:
- Database name (e.g. `u525408471_navanari`)
- Username (e.g. `u525408471_navanari`)
- Password

### 2. Point Git auto-deploy at `public_html`
hPanel → **Advanced → Git** → connect this repo, branch **`main`**, deploy
directory **`public_html`**. Deploy once so the files land on the server.

> The repo includes a root `.htaccess` that forwards all traffic into Laravel's
> `/public` folder, so it works even if you can't change the document root.
> **Better option if available:** hPanel → set the website's document root to
> `public_html/public` (cleaner & slightly more secure). Either way works.

### 3. SSH in and configure
```bash
ssh -p 65002 u525408471@145.79.25.36
cd ~/domains/<your-domain>/public_html     # the deploy folder from step 2

# Create the environment file
cp .env.example .env
nano .env
```
In `.env` set at least:
```env
APP_NAME=Navanari
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=u525408471_navanari
DB_USERNAME=u525408471_navanari
DB_PASSWORD=your-db-password

FILESYSTEM_DISK=public
WHATSAPP_NUMBER=91XXXXXXXXXX
```
Save (Ctrl+O, Enter, Ctrl+X), then:
```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate --seed --force     # --seed loads demo products (skip if not wanted)
php artisan storage:link
php artisan config:cache
```

### 4. Log in and make it yours
- Visit **https://your-domain.com/login**
- Email `admin@navanari.test` · Password `password` → **change it immediately**:
  ```bash
  php artisan navanari:make-admin you@email.com --name="Your Name"
  ```
- Open **Admin → Settings** to set store name, logo, WhatsApp number, colours, etc.

---

## 🔁 Every later deploy
After Hostinger auto-pulls a new push, SSH in and run:
```bash
cd ~/domains/<your-domain>/public_html
bash deploy.sh
```
(`deploy.sh` runs composer install, migrations, storage link and cache rebuilds.)

---

## 🧯 Troubleshooting
| Symptom | Fix |
|---|---|
| **500 error** | Usually missing `vendor/` or `.env`. Run `composer install` and confirm `.env` exists with a valid `APP_KEY` (`php artisan key:generate`). |
| **Blank page / "no app key"** | `php artisan key:generate && php artisan config:cache` |
| **Images don't show after upload** | `php artisan storage:link` (creates `public/storage`). |
| **CSS/JS missing** | The compiled assets in `public/build` are committed — make sure that folder deployed. If you change styles locally, run `npm run build` and push. |
| **Old config after changes** | `php artisan config:clear && php artisan config:cache` |
| **404 on every page except home** | Document-root issue — confirm the root `.htaccess` deployed, or set docroot to `public_html/public`. |

---

## 🔐 Security reminders
- Set `APP_DEBUG=false` in production (already in the template above).
- Change the default admin password.
- Never commit `.env` (it's gitignored).
- Rotate any access token you've shared in chat.
