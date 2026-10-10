import { Linking, Platform, Pressable, Text, View } from 'react-native';
import MapView, { Marker } from 'react-native-maps';

import { colors } from './ui';

export function PropertyMap({ lat, lng, title }: { lat: number; lng: number; title?: string }) {
  const openInMaps = () => {
    const label = encodeURIComponent(title ?? 'Property');
    const url =
      Platform.OS === 'ios'
        ? `http://maps.apple.com/?ll=${lat},${lng}&q=${label}`
        : `geo:${lat},${lng}?q=${lat},${lng}(${label})`;
    Linking.openURL(url).catch(() => {});
  };

  return (
    <View style={{ marginTop: 20 }}>
      <Text style={{ fontSize: 17, fontWeight: '800', color: colors.text, marginBottom: 10 }}>Location</Text>
      <View style={{ height: 200, borderRadius: 12, overflow: 'hidden', borderWidth: 1, borderColor: colors.border }}>
        <MapView
          style={{ flex: 1 }}
          scrollEnabled={false}
          zoomEnabled={false}
          pitchEnabled={false}
          rotateEnabled={false}
          initialRegion={{ latitude: lat, longitude: lng, latitudeDelta: 0.01, longitudeDelta: 0.01 }}>
          <Marker coordinate={{ latitude: lat, longitude: lng }} title={title} />
        </MapView>
      </View>
      <Pressable onPress={openInMaps} style={{ marginTop: 8 }}>
        <Text style={{ color: colors.primary, fontWeight: '700' }}>Open in Maps</Text>
      </Pressable>
    </View>
  );
}
