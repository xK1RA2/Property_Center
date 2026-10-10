import { Linking, Pressable, Text } from 'react-native';

import { colors } from './ui';

export function PropertyMap({ lat, lng }: { lat: number; lng: number; title?: string }) {
  return (
    <Pressable
      onPress={() => Linking.openURL(`https://www.google.com/maps?q=${lat},${lng}`)}
      style={{ marginTop: 20 }}>
      <Text style={{ color: colors.primary, fontWeight: '700' }}>Open location in Maps</Text>
    </Pressable>
  );
}
