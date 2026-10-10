import Constants from 'expo-constants';
import * as Device from 'expo-device';
import * as Notifications from 'expo-notifications';
import { Platform } from 'react-native';

import { api } from './api';

/**
 * Push notifications (booking requests and decisions).
 *
 * Needs a real device and an EAS project id (`eas init` writes it to app.json under extra.eas.projectId).
 * Remote push is NOT available in Expo Go on Android since SDK 53: use a development build there.
 * Everything here fails soft; the app works fine without push.
 */

// Show notifications as a banner even while the app is open.
Notifications.setNotificationHandler({
  handleNotification: async () => ({
    shouldShowBanner: true,
    shouldShowList: true,
    shouldPlaySound: true,
    shouldSetBadge: false,
  }),
});

let registeredToken: string | null = null;

export async function registerForPush(): Promise<string | null> {
  try {
    if (Platform.OS === 'web' || !Device.isDevice) return null;

    if (Platform.OS === 'android') {
      await Notifications.setNotificationChannelAsync('default', {
        name: 'Bookings',
        importance: Notifications.AndroidImportance.HIGH,
      });
    }

    const current = await Notifications.getPermissionsAsync();
    let granted = current.granted;
    if (!granted) granted = (await Notifications.requestPermissionsAsync()).granted;
    if (!granted) return null;

    const projectId = Constants.expoConfig?.extra?.eas?.projectId ?? Constants.easConfig?.projectId;
    if (!projectId) {
      console.log('[push] no EAS projectId configured; skipping push registration');
      return null;
    }

    const { data: token } = await Notifications.getExpoPushTokenAsync({ projectId });
    await api.registerDevice(token, Platform.OS === 'ios' ? 'ios' : 'android');
    registeredToken = token;
    return token;
  } catch (e) {
    console.log('[push] registration skipped:', e instanceof Error ? e.message : e);
    return null;
  }
}

/** Call before signing out so this phone stops receiving the account's notifications. */
export async function unregisterFromPush(): Promise<void> {
  if (!registeredToken) return;
  const token = registeredToken;
  registeredToken = null;
  try {
    await api.unregisterDevice(token);
  } catch {
    // the server prunes dead tokens itself
  }
}

/** The screen name sent by the backend in the notification payload. */
export function screenFor(data: unknown): '/bookings' | null {
  const screen = (data as { screen?: string } | null | undefined)?.screen;
  return screen === 'bookings' ? '/bookings' : null;
}
