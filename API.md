# Property Center REST API (v1)

Base URL: `/api/v1` · JSON only · send `Accept: application/json`
Auth: Sanctum bearer token, `Authorization: Bearer <token>` (from `register` / `login`).

Lists are paginated: `{ "data": [...], "links": {...}, "meta": {...} }` (`?page=`, `?per_page=`).
Errors: `401` unauthenticated, `403` forbidden, `404` not found, `422` validation (`{message, errors}`), `429` throttled.

## Auth
| Method | Path | Notes |
|---|---|---|
| POST | `/auth/register` | `name` (unique), `email`, `password`, `password_confirmation`, optional `username`, `phone` → `201 {user, token}` |
| POST | `/auth/login` | `email`, `password` → `{user, token}` |
| POST | `/auth/logout` | revokes the current token |
| GET | `/me` | current user |
| PUT | `/me/password` | `current_password`, `password`, `password_confirmation` |

## Public
| Method | Path | Notes |
|---|---|---|
| GET | `/properties` | filters: `city_id`, `property_type_id`, `purchase_type` (Sell/Rent), `min_price`, `max_price`, `search`, `sort` (newest/price_asc/price_desc), `per_page` |
| GET | `/properties/{id}` | full detail: city, type, dealer, features, images, location, rating |
| GET | `/properties/{id}/comments` | anonymous comments never expose the author |
| GET | `/states`, `/cities`, `/property-types`, `/dealers` | lookups |

## Any logged-in user
| Method | Path | Notes |
|---|---|---|
| POST | `/properties/{id}/comments` | `description`, optional `anonymous` |
| DELETE | `/comments/{id}` | own comment (or admin) |
| PUT | `/properties/{id}/rating` | `rating` 1–5, one per user (re-send to change) |
| GET | `/favorites` | |
| PUT / DELETE | `/properties/{id}/favorite` | add / remove |
| GET | `/bookings` | users: their own · dealers: on their properties · admins: all |
| POST | `/properties/{id}/bookings` | `date` (future), optional `description`; re-posting updates your booking |
| POST | `/trader-requests` | users ask to become a dealer |

## Dealer
| Method | Path | Notes |
|---|---|---|
| GET | `/my/properties` | own listings, any status |
| POST | `/properties` | `purchase_type`, `property_type_id`, `city_id`, `price`, `year`, `address`, optional `description`, `status`, `published_at`, `features{Bedrooms,Bathrooms,Kitchen,Rooms,Area,Heating,Air_Conditioner,Parking}`, `images[]` (multipart) |
| PUT / PATCH | `/properties/{id}` | own property (or admin); every field optional |
| DELETE | `/properties/{id}` | own property (or admin) |
| PATCH | `/bookings/{id}` | `status`: Approved / Rejected, only for bookings on own properties |

## Admin
| Method | Path | Notes |
|---|---|---|
| GET | `/admin/users` | |
| DELETE | `/admin/users/{id}` | admins and yourself can't be deleted |
| GET | `/admin/trader-requests` | |
| PATCH | `/admin/trader-requests/{id}` | `action`: approve / reject |
