import { Ionicons } from '@expo/vector-icons';
import { Redirect } from 'expo-router';
import type { ColorValue } from 'react-native';
import { Tabs } from 'expo-router/js-tabs';

import { colors, Loading } from '@/components/ui';
import { useAuth } from '@/lib/auth';

type IconName = React.ComponentProps<typeof Ionicons>['name'];

const icon = (name: IconName) =>
  function TabIcon({ color, size }: { color: ColorValue; size: number }) {
    return <Ionicons name={name} size={size} color={color} />;
  };

export default function TabsLayout() {
  const { user, loading, isDealer } = useAuth();

  if (loading) return <Loading />;
  if (!user) return <Redirect href="/login" />;

  return (
    <Tabs
      screenOptions={{
        headerShown: false,
        tabBarActiveTintColor: colors.primary,
        tabBarInactiveTintColor: colors.muted,
      }}>
      <Tabs.Screen name="index" options={{ title: 'Explore', tabBarIcon: icon('search') }} />
      <Tabs.Screen name="favorites" options={{ title: 'Saved', tabBarIcon: icon('heart') }} />
      <Tabs.Screen name="bookings" options={{ title: 'Bookings', tabBarIcon: icon('calendar') }} />
      {/* only dealers get the listings tab */}
      <Tabs.Screen
        name="listings"
        options={{ title: 'My listings', tabBarIcon: icon('home'), href: isDealer ? undefined : null }}
      />
      <Tabs.Screen name="profile" options={{ title: 'Profile', tabBarIcon: icon('person') }} />
    </Tabs>
  );
}
