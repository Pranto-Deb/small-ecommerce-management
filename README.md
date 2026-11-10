# Small E-commerce Management — Laravel 12 + Vue 3 + MySQL

A focused technical assessment implementation. The architecture separates **global catalog products** from **shop-specific listings** so shop owners can use a shared product while setting their own price, details, and thumbnail.

## Requirements

- PHP 8.2+ with extensions required by Laravel
- Composer 2
- Node.js 20+ and npm
- MySQL 8+ (or MariaDB)

## Install

```bash
# Extract the zip, then enter the project directory
composer install
npm install
cp .env.example .env
```

Create a blank MySQL database called `small_ecommerce`. Edit `.env` with its `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`. Set `APP_URL=http://localhost:8000` and choose a **secure** `ADMIN_PASSWORD` (at least 8 characters) plus `ADMIN_EMAIL`.

```bash
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan storage:link
npm run dev
```

In another terminal:

```bash
php artisan serve
```

Visit `http://localhost:8000`. Sign in at `/login` using `ADMIN_EMAIL` and `ADMIN_PASSWORD`. To test without a Vite dev server, run `npm run build` and then `php artisan serve`.

### Recommended first steps

1. Log in as administrator.
2. Create two shop-owner accounts in **Shop Owners**.
3. Create a shop for each owner in **Shops**.
4. Add a sample global catalog product in **Product Catalog**.
5. Log in as each owner, select that product from **Shared Catalog**, and specify different prices and details.
6. Visit each public shop through the directory (`/`), verifying different shop prices. Create a new product as an owner and verify it appears in the other owner's shared catalog.
7. Verify that unlisted catalog products never appear on a public shop page.

## Routes

| Role | Page | URL |
| --- | --- | --- |
| Public | Shop directory | `/` |
| Public | Assigned products in a shop | `/shops/{slug}` |
| All users | Login | `/login` |
| Admin | Overview | `/admin` |
| Admin | Shop CRUD | `/admin/shops` |
| Admin | Product CRUD | `/admin/products` |
| Admin | Owner account CRUD | `/admin/owners` |
| Owner | Overview | `/owner` |
| Owner | Browse shared catalog/create shared product | `/owner/catalog` |
| Owner | Shop listings: price/details/thumbnail/remove | `/owner/listings` |

## Data model

- `users`: role `admin` or `owner`.
- `shops`: shop name, slug, description, and optional assigned owner. This assessment supports **one shop per owner**.
- `products`: global product with creator reference, name, SKU, description and default thumbnail. Admin and owners can create catalog products; admin manages all catalog products.
- `shop_products`: many-to-many shop/catalog association storing independent price, details and thumbnail. A `(shop_id, product_id)` unique constraint prevents duplicate assignment.

## Permissions

- Only administrators can access admin routes.
- Only shop owners can access shop management routes. Each owner may only update or remove listings belonging to their own assigned shop (enforced server-side).
- Owners can create catalog products, but cannot edit/remove shared catalog products created by other users. The admin is responsible for global edits and deletes.
- Public pages never query the full product catalog: they display only rows linked via `shop_products`.
- Deleting a global product cascades to all its shop listings. Removing a listing never deletes the shared product.
- Uploaded thumbnails are validated as images up to 2MB. Uploads use Laravel's public storage disk.

## Packaging note

The ZIP contains **application source code** (not Composer vendor libraries or npm node_modules). Run `composer install` and `npm install` as shown above. The source has been statically checked, but a full Laravel runtime or MySQL integration test was not available in the build environment.
