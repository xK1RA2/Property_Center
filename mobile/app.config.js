/**
 * Dynamic Expo config. Static values live in app.json; this adds the pieces that
 * depend on secrets / environment so nothing sensitive is committed.
 *
 *   GOOGLE_MAPS_API_KEY   Google Maps SDK key (Android + iOS builds; not needed in Expo Go)
 *   EAS_PROJECT_ID        from `eas init`; required for push tokens
 */
module.exports = ({ config }) => ({
  ...config,
  plugins: [
    ...(config.plugins ?? []),
    'expo-notifications',
    [
      'expo-location',
      { locationWhenInUsePermission: 'Property Center uses your location to place the pin on your listing.' },
    ],
    [
      'react-native-maps',
      {
        androidGoogleMapsApiKey: process.env.GOOGLE_MAPS_API_KEY,
        iosGoogleMapsApiKey: process.env.GOOGLE_MAPS_API_KEY,
      },
    ],
  ],
  extra: {
    ...config.extra,
    eas: { projectId: process.env.EAS_PROJECT_ID ?? config.extra?.eas?.projectId },
  },
});
