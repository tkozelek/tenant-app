# App Ideas & Improvements

## Bugs to fix

- `TenantProduct::getLowestCurrentPriceAttribute()` and `getCheapestVariant()` both have wrong logic — they filter variants by `global_product_id = $this->id` but `$this` is a TenantProduct, not a GlobalProduct. Should be filtering by `tenant_product_id = $this->id` directly on `TenantProductVariant`.

---

## Database / Models

**Tenant — missing contact/profile fields**
Add `address`, `city`, `phone`, `email`, `website` to tenants. Essential for a shop directory page. Also `is_verified` / `verified_at` — so you can badge verified shops vs unverified ones.

**SEO meta fields**
GlobalProduct and Category are missing `meta_title` and `meta_description`. Without these the frontend won't have proper og/meta tags per page.

**Category images**
Categories table has no image. A Heureka-style category listing page looks much better with icons/images.

**Featured products**
No `is_featured` flag on GlobalProduct. Needed for a homepage or top picks section.

**Reviews / ratings**
No review system at all. Consider a `reviews` table: `user_id`, `tenant_id` (nullable), `global_product_id` (nullable), `rating` (1–5), `body`, `is_approved`. Tenants could have shop reviews, products could have product reviews.

**Price alerts**
`price_alerts` table: `user_id`, `global_product_id`, `target_price`, `notified_at`. User sets a max price and gets notified when any tenant drops below it. Classic Heureka feature.

**Click / view tracking**
`product_views` table: `global_product_id`, `tenant_product_id` (nullable), `ip`, `created_at`. Lets you see what's popular and show "X people viewed this today".

**Soft deletes**
Most models permanently delete. Adding `SoftDeletes` to TenantProduct, TenantProductVariant, GlobalProduct, Bundle would prevent accidental data loss.

---

## Frontend (public-facing)

The whole Heureka concept requires a public frontend — currently nothing exists. Rough page structure:

- `/` — homepage, featured products, popular categories
- `/kategoria/{slug}` — category listing with global products, filterable by attributes
- `/produkt/{slug}` — global product detail: shows all tenants offering it, sorted by price. Price history chart. Reviews.
- `/obchod/{slug}` — tenant shop page: name, logo, contact info, all their products, reviews
- `/hladat?q=...` — search results

This is the biggest missing piece. Can be built with Laravel routes + Blade + Alpine/Livewire, or as an API backend + separate frontend. API would be more flexible long-term.

---

## Admin reports

**Low stock report**
Cross-tenant view: variants where `stock_quantity <= threshold`, grouped by tenant and product. Admin can see who's running out across the platform.

**Tenant performance report**
Table: tenant name, product count, active products, variants, price changes last 30 days, last activity. Helps identify inactive or underperforming tenants.

**Coupon usage report**
`used_count` exists on Coupon but nothing drives it (no order system). Even without orders, could show coupons approaching expiry, usage % vs limit, tenants with 0-use coupons.

**Price movement report**
Dedicated page showing which products had the most price changes recently, biggest drops/increases. Data is all there in `price_history`, just needs a report page.

---

## Tenant panel

**Price comparison widget**
When a tenant views a product linked to a global product, show a small table/chart of competitor prices for the same global product. They currently have no visibility into market pricing within the app.

**Low stock notifications**
The dashboard already shows low stock count, but no push notification. A queued job that runs daily and sends a Filament database notification when variants drop below e.g. 5 units would be useful.

**Product import**
Tenants can export products but can't import. An importer for TenantProductVariants (bulk price/stock update via CSV) would save a lot of manual work.

---

## App logic / infrastructure

**Scheduled jobs**
Nothing in the schedules yet. At minimum: expire coupons (`is_active = false` when `expires_at` passes), send low stock alerts, send price alert notifications.

**API layer**
For the public frontend and potential future mobile app. Laravel API resources are already the right approach. A versioned `/api/v1/` with routes for products, categories, tenants, search.

**Full-text search**
Laravel Scout with Meilisearch or Algolia on GlobalProduct and TenantProduct. The current `->searchable()` on Filament tables only does SQL LIKE queries which won't scale for a public search box.

**Sitemap**
Once the frontend exists, a generated sitemap for all global products, categories, and public tenant pages is needed for SEO. `spatie/laravel-sitemap` would fit in here cleanly.

**GlobalProduct import**
Admin can only import users and categories. A GlobalProduct importer (bulk CSV) would speed up catalog building significantly.
