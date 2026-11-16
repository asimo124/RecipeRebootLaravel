# Recipe Book API (Laravel)

JSON API for the Recipe Book app. Vue frontend lives in the separate `recipes_vue` repo.

## Stack

- Laravel 10
- MySQL 8.0 (auth `users`, Sanctum tokens, Bills databases)
- PostgreSQL 16 (recipe book `ri_*` tables)
- Docker Compose (`app` PHP-FPM, `webserver` nginx, `db` MySQL, `pgsql`)

## Quick start

```bash
cp .env.example .env   # if needed
docker compose up -d --build
docker compose exec app php artisan key:generate   # first run only
docker compose exec app php artisan migrate --force
docker compose exec app php artisan recipes:copy-mysql-to-pgsql
# Fresh install without existing MySQL recipe data:
# docker compose exec app php artisan db:seed --force
```

API base URL: **http://localhost:8080/api**

MySQL is exposed on host port **3307** (`recipes` / `secret`).
PostgreSQL is exposed on host port **5433** (`recipes` / `secret`).

Recipe models use the `pgsql` connection. Auth stays on the default MySQL connection.

### BillsSite (standalone) in the same stack

The `bills` service runs the pure-PHP BillsSite repo (mounted from `BILLS_SITE_HOST_PATH`) on the same PHP image, MySQL and PostgreSQL as the API.

- BillsSite URL: **http://localhost:8081** (`BILLS_SITE_PORT` to change)
- Databases: `asimo124_bills`, `asimo124_bills_test` on the `db` container. `api_db` is not provisioned; `BILLS_DB_DATABASE2` points at `recipes`, the same fallback Laravel uses.
- `inc/` and dotfiles are blocked by nginx.

Load or refresh the Bills data from server dumps. Each database is **dropped and recreated**. MariaDB-only collations and DEFINERs are patched for MySQL 8:

```bash
cp ~/Downloads/asimo124_bills.sql docker/mysql/dumps/        # .sql or .sql.gz
cp ~/Downloads/asimo124_bills_test.sql docker/mysql/dumps/
scripts/import-bills-dbs.sh            # or: scripts/import-bills-dbs.sh asimo124_bills
```

`docker/mysql/dumps/` is gitignored. On a brand-new MySQL volume the two databases are created empty automatically; then run the import.

### api.hawleywebdesign.com (Yii) in the same stack

The `api` service runs the Yii2 app (mounted from `API_SITE_HOST_PATH`) against the `api_db` database on the `db` container.

- URL: **http://localhost:8082** (`API_SITE_PORT` to change). The Items CRUD is at `/item`.
- Its `config/db.php` reads `API_DB_*` from the container; on the server it falls back to the existing values.
- Load or refresh the data the same way as the Bills databases: put `api_db.sql` (or `.sql.gz`) in `docker/mysql/dumps/` and run `scripts/import-bills-dbs.sh api_db`.

### Last Time I

MyBudgetLP's **Last Time I** page uses native Laravel endpoints (`LastTimeController`) on the `api_db` connection, under `/api/last_time/*.php`, with Sanctum auth. They read the same `ltc_item` / `ltc_item_used_history` tables as the Yii `last-time` API. On the server, set `API_DB_DATABASE=api_db` in `.env`; host, user and password default to `DB_*`.

## Main endpoints

| Resource | Path |
|---|---|
| Recipes | `GET/POST /api/recipes`, `GET/PUT/DELETE /api/recipes/{id}` |
| Recipe ingredients | `POST /api/recipes/{id}/ingredients`, `DELETE /api/recipes/{id}/ingredients/{ingredientId}` |
| Ingredients | `GET /api/ingredients?search=`, `POST/PUT/DELETE /api/ingredients/{id}` |
| Related ingredients | `GET/POST/DELETE /api/ingredients/{id}/related` |
| Inventory | `GET/POST /api/inventory`, `DELETE /api/inventory/{id}` |
| Proteins | `/api/proteins` |
| Meal styles | `/api/recipe-styles` |

CORS allows origins listed in `CORS_ALLOWED_ORIGINS` (default: local Vite).

## Production deploy

See [`../plan/DEPLOY.md`](../plan/DEPLOY.md) for Debian 12 + Apache + existing MariaDB steps.

### PostgreSQL on Debian

The app already runs against MariaDB/MySQL on the server. Recipe `ri_*` tables move to PostgreSQL; `users` stay on MySQL.

```bash
sudo apt update
sudo apt install postgresql php-pgsql
sudo systemctl enable --now postgresql

sudo -u postgres createuser recipes
sudo -u postgres createdb -O recipes recipes
sudo -u postgres psql -c "ALTER USER recipes PASSWORD 'change-me';"
```

If PHP-FPM does not pick up `pdo_pgsql`, install the versioned package (e.g. `php8.2-pgsql`) and restart FPM.

Set `PGSQL_*` in production `.env` (see `.env.production.example`), then:

```bash
php artisan migrate --force
php artisan recipes:copy-mysql-to-pgsql --force
sudo systemctl restart php8.2-fpm   # or the PHP-FPM unit on that host
```

MySQL `ri_*` tables are left in place as a backup after the copy.

## Bills / MyBudget legacy API

MyBudgetLP calls the same legacy BillsSite paths (e.g. `/api/auth/login.php`, `/api/expenses/list.php`). These are registered in `routes/bills_legacy.php` from `routes/bills_legacy_map.php` and handled by RESTful controllers under `App\Http\Controllers\Api\Bills\*`.

- **Auth** (`AuthController`) is implemented in Laravel (`BillsAuthService`) against `hth_users` / `hth_user_sessions` on the `asimo124_bills` connection.
- **All other endpoints** currently delegate to the BillsSite PHP scripts via `LegacyBillsScriptRunner` (configure `BILLS_SITE_PATH` in `.env`).
- Port endpoints to native Laravel services over time, then drop the legacy bridge for each action.

Recipe Book JSON API routes (`/api/login`, `/api/recipes`, …) are unchanged.


All API routes except `POST /api/login` require a Sanctum bearer token.

```bash
# Seed / reset the admin user
docker compose exec app php artisan db:seed --class=AdminUserSeeder --force
```

Default credentials (override in `.env`):

- Email: `alex@recipes.local`
- Password: `recipes`

```
POST /api/login   { "email", "password" } → { token, user }
POST /api/logout  (Bearer token)
GET  /api/me      (Bearer token)
```

Recipe/inventory data is **not** scoped to users — auth only gates access.

## Notes

- Soft-delete uses `ri_recipe.is_deleted` (not `deleted_at`).
- `recipe_link` is `varchar(255)`.
- Seed data comes from `database/seeders/data/api_db_dump.sql` (loaded into PostgreSQL).
- `php artisan recipes:copy-mysql-to-pgsql` copies existing MySQL `ri_*` rows into PostgreSQL and resets identity sequences.
# RecipeRebootLaravel
