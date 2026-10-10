# Property Center mobile app

React Native + Expo (SDK 57, TypeScript, Expo Router) client for the Laravel API in the parent folder.
One app, two roles: **users** browse / save / book / comment / rate, **dealers** also manage their listings and approve or reject bookings.

## Run it

1. Start the backend (from the repo root):
   ```bash
   php artisan serve --host=0.0.0.0     # --host lets a physical phone reach it
   php artisan storage:link             # once, so uploaded photos are served
   ```
2. Point the app at it. Copy `.env.example` to `.env` and set `EXPO_PUBLIC_API_URL`:
   | Where you run the app | Value |
   |---|---|
   | Android emulator | `http://10.0.2.2:8000` (default) |
   | iOS simulator | `http://127.0.0.1:8000` (default) |
   | Physical phone (same Wi-Fi) | `http://<your-PC-LAN-IP>:8000` |
3. Start Expo:
   ```bash
   npm install
   npx expo start        # then scan the QR code with Expo Go, or press a / i
   ```

Seeded accounts are created by `php artisan db:seed` with random passwords; register a new account in the app,
then promote it to dealer with the in-app "Request to become a dealer" + an admin approval
(`PATCH /api/v1/admin/trader-requests/{id}`), or set `role_id = 2` in the database.

## Layout

```
src/
  app/                 Expo Router screens (file = route)
    _layout.tsx          providers (React Query, Auth) + root Stack
    (auth)/              login, register
    (tabs)/              Explore, Saved, Bookings, My listings (dealers only), Profile
    property/[id].tsx    detail: gallery, rating, favorite, booking, comments
    property/form.tsx    create / edit listing with photos (dealers)
  components/          ui kit, PropertyCard, PagedList (infinite scroll)
  lib/
    api.ts               typed client for /api/v1 (token, errors, multipart)
    auth.tsx             session in SecureStore, 401 -> sign out
    types.ts             API types
```

## Checks

```bash
npx tsc --noEmit        # types
npx expo-doctor         # dependency / config health
npx expo export --platform android   # proves the whole app bundles
```

## Maps and push notifications

- **Map picker / location view**: `react-native-maps`. Works in Expo Go. Store builds need a Google Maps key
  (`GOOGLE_MAPS_API_KEY`, read by `app.config.js`).
- **Push notifications**: booking requests go to the dealer, decisions go back to the user.
  Requires a physical device and an EAS project id (`eas init`, or `EAS_PROJECT_ID`).
  **Expo Go on Android cannot receive remote push (SDK 53+)**: use a development build
  (`eas build --profile development --platform android`). Without a project id the app simply skips registration.

## Known gaps

- Dev uses plain HTTP; a release build needs HTTPS for the API (see ../DEPLOY.md).
- Checkout / payments are intentionally not exposed by the API yet.
- Push and maps are type-checked and bundled but have not been exercised on a physical device.
