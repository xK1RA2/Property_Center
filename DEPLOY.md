# Deploying Property Center

One server, Docker, automatic HTTPS. Components (see `docker-compose.yml`):

| Service | What it is |
|---|---|
| `caddy` | TLS termination + Let's Encrypt certificates, reverse proxy |
| `app` | Laravel on Apache (website **and** `/api/v1`), runs migrations on start |
| `worker` | `queue:work` |
| `scheduler` | `schedule:work` |
| `db` | MySQL 8.4 |

> **Status (what has and has not been run):**
>
> - **MySQL 8.4.11: verified.** All 29 migrations, the seeders, the whole test suite (144 tests) and the website
>   and API over HTTP were run against a real MySQL 8.4 server in strict mode (`ONLY_FULL_GROUP_BY`). That run
>   found and fixed: migrations that referenced tables created later, a factory with a duplicate column,
>   and tests/seeders that assumed auto-increment ids. CI now repeats it on every push (`backend-mysql` job).
> - **Docker: not run.** The development PC has no Docker/WSL. The container start-up commands
>   (`config:cache`, `route:cache`, `view:cache`, `event:cache`) were run and succeed, and the files were
>   reviewed, but `docker build` has not been executed locally. The CI `docker` job builds the image on push.
>   Do a first deployment to a throw-away server before real users.
> - Tested on MySQL running on **Windows**, where table names are case-insensitive. Linux MySQL is
>   case-sensitive; all table/foreign-key names are consistent lowercase, and the CI job runs on Linux.
## 0. Try it on MySQL locally first (DDEV)

[DDEV](https://ddev.com) runs the project in Docker with MySQL 8, the same database production uses. It is a
**local development tool, not a hosting platform**: use it to prove the app works on MySQL, then deploy with
section 2 below.

```powershell
# one-time: install Docker Desktop (WSL2 backend) and DDEV  ->  https://docs.ddev.com/en/stable/users/install/
cd Property_Center
ddev start                                  # https://property-center.ddev.site
ddev composer install
cp .env.example .env
ddev artisan key:generate
```

Point `.env` at DDEV's database (these are DDEV's fixed defaults):

```
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=db
DB_USERNAME=db
DB_PASSWORD=db
```

```powershell
ddev artisan migrate:fresh --seed           # runs every migration on MySQL
ddev artisan storage:link
ddev launch                                 # open the site
ddev test-mysql                             # the whole PHPUnit suite against MySQL (separate test_db schema)
```

`ddev start` also gives you a mail catcher for password-reset emails at `ddev launch -m`.
To test the mobile app against it, set `EXPO_PUBLIC_API_URL=https://property-center.ddev.site` (the certificate is
trusted on your PC only, so a physical phone needs `ddev share` or the LAN IP instead).

If `ddev test-mysql` or `migrate:fresh` fails, that is exactly the information you wanted: send me the output.
## 1. Prerequisites

- A Linux server (1 vCPU / 1 GB RAM is enough to start) with Docker + Compose plugin.
- A domain whose **A record** points at the server's IP.
- Ports **80** and **443** open (Caddy needs 80 for the certificate challenge).
- An SMTP account for password-reset emails (any provider).

## 2. First deploy

```bash
git clone https://github.com/xK1RA2/Property_Center.git && cd Property_Center
git checkout fullProject               # or the branch you deploy from

cp .env.production.example .env
nano .env                              # set APP_URL, APP_DOMAIN, passwords, mail
docker compose run --rm app php artisan key:generate --show   # paste into APP_KEY

docker compose up -d --build
docker compose logs -f app             # wait for "apache2 -D FOREGROUND"

# one-time data
docker compose exec app php artisan db:seed --class=ReferenceDataSeeder --force
docker compose exec app php artisan app:create-admin you@example.com --name="Your Name"
```

Open `https://your-domain/index`, sign in at `/login` with the admin, then `/admin`.
The REST API is at `https://your-domain/api/v1` (see [API.md](API.md)).

> Never run `db:seed` without `--class=ReferenceDataSeeder` in production: the default seeder creates fake users.

## 3. Updating

```bash
git pull
docker compose up -d --build           # migrations run automatically on the new container
```

Roll back with `git checkout <previous-commit>` and the same command. Migrations are not
auto-reverted: if a release added a migration, restore the database backup taken before it.

## 4. Backups

Uploaded photos live in the `storage` volume, the database in `dbdata`. Back up **both**:

```bash
# database (run from cron, keep copies off the server)
docker compose exec -T db sh -c 'mysqldump -uroot -p"$MYSQL_ROOT_PASSWORD" --single-transaction "$MYSQL_DATABASE"' \
  | gzip > backup-$(date +%F).sql.gz

# photos
docker run --rm -v property-center_storage:/data -v "$PWD":/out alpine \
  tar czf /out/storage-$(date +%F).tgz -C /data app/public
```

Test a restore at least once. A backup you have never restored is a hope, not a backup.

## 5. The mobile app against production

1. Point it at the API: in `mobile/.env` set `EXPO_PUBLIC_API_URL=https://your-domain`.
   Release builds must use **HTTPS** (iOS blocks plain HTTP by default).
2. `cd mobile && npm i -g eas-cli && eas login && eas init` (writes the EAS project id).
3. Push notifications: remote push needs a **development/production build**, not Expo Go on Android.
   Set `EAS_PROJECT_ID` (printed by `eas init`) and build with `eas build`.
   iOS needs an Apple developer account; Android needs an FCM v1 key uploaded via `eas credentials`.
4. Maps in store builds: create a Google Maps SDK key, restrict it to your package name / bundle id and set
   `GOOGLE_MAPS_API_KEY` in the EAS build environment (see `mobile/app.config.js`).
5. Build: `eas build --platform android --profile production` (and `ios`).

## 6. Security checklist (before real users)

- [ ] `APP_DEBUG=false`, `APP_ENV=production`, a freshly generated `APP_KEY`
- [ ] Strong, unique `DB_PASSWORD` / `DB_ROOT_PASSWORD`; the DB port is **not** published (it isn't in `docker-compose.yml`)
- [ ] Firewall allows only 22, 80, 443
- [ ] SSH key login only; automatic security updates on the host
- [ ] Mail works: request a password reset and confirm the email arrives
- [ ] Backups scheduled and one restore tested
- [ ] A real payment provider integrated in `App\Services\CheckoutService` before taking money
      (today checkout only records a purchase; no card data is stored)
- [ ] Rate limits reviewed (`throttle:` on login/register) and a Content-Security-Policy added
      (`App\Http\Middleware\SecurityHeaders` documents why it is not there yet)

## 7. Without Docker

Any PHP 8.2+ host works: `composer install --no-dev -o`, copy `.env.production.example` to `.env`,
`php artisan key:generate`, `migrate --force`, `db:seed --class=ReferenceDataSeeder --force`,
`storage:link`, `config:cache route:cache view:cache`, point the web root at `public/`,
run `queue:work` under a supervisor and `schedule:run` from cron every minute.

## 8. Email and the newsletter

Outgoing email is used for password-reset links and the newsletter's confirmation message.

1. Put your SMTP details in `.env` (`MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`,
   `MAIL_SCHEME=smtp` for port 587 or `smtps` for 465, `MAIL_FROM_ADDRESS`). Examples are in `.env.example`.
2. **Prove it works** (it prints the exact SMTP error if not):
   ```bash
   docker compose exec app php artisan app:mail-test you@example.com
   ```
3. Newsletter: visitors subscribe in the site footer and must click the emailed link (double opt-in).
   Everyone has a personal one-click unsubscribe link, and the list is `newsletter_subscribers`.
   Send an issue to everyone who confirmed (needs the `worker` container, which compose starts):
   ```bash
   docker compose exec app php artisan newsletter:send "Subject" --body="Your message"
   docker compose exec app php artisan newsletter:send "Subject" --file=issue.txt --dry-run   # count only
   ```
4. Deliverability: use a domain you own for `MAIL_FROM_ADDRESS` and set up SPF/DKIM with your mail provider,
   otherwise messages land in spam. Gmail can send ~500 messages/day; use Brevo/Mailgun/SES for more.
