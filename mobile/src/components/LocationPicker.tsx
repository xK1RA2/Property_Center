import * as Location from 'expo-location';
import { useEffect, useRef, useState } from 'react';
import { Alert, Text, View } from 'react-native';
import MapView, { MapPressEvent, Marker, Region } from 'react-native-maps';

import { Button, colors } from './ui';

export interface Coordinate {
  lat: number;
  lng: number;
}

const AMMAN: Region = { latitude: 31.9539, longitude: 35.9106, latitudeDelta: 0.15, longitudeDelta: 0.15 };

/** Tap the map to drop the listing's pin. */
export function LocationPicker({
  value,
  onChange,
}: {
  value: Coordinate | null;
  onChange: (coordinate: Coordinate | null) => void;
}) {
  const map = useRef<MapView>(null);
  const [locating, setLocating] = useState(false);

  // when editing, jump to the saved pin
  useEffect(() => {
    if (value) {
      map.current?.animateToRegion(
        { latitude: value.lat, longitude: value.lng, latitudeDelta: 0.02, longitudeDelta: 0.02 },
        300,
      );
    }
    // only on first mount / when an existing value arrives
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [value === null]);

  const onPress = (e: MapPressEvent) => {
    const { latitude, longitude } = e.nativeEvent.coordinate;
    onChange({ lat: round(latitude), lng: round(longitude) });
  };

  const useMyLocation = async () => {
    setLocating(true);
    try {
      const { granted } = await Location.requestForegroundPermissionsAsync();
      if (!granted) {
        Alert.alert('Location permission needed', 'Allow location access, or tap the map to place the pin.');
        return;
      }
      const pos = await Location.getCurrentPositionAsync({});
      const next = { lat: round(pos.coords.latitude), lng: round(pos.coords.longitude) };
      onChange(next);
      map.current?.animateToRegion(
        { latitude: next.lat, longitude: next.lng, latitudeDelta: 0.01, longitudeDelta: 0.01 },
        300,
      );
    } catch {
      Alert.alert('Could not get your location', 'Tap the map to place the pin instead.');
    } finally {
      setLocating(false);
    }
  };

  return (
    <View style={{ marginBottom: 14 }}>
      <Text style={{ fontSize: 13, fontWeight: '600', color: colors.muted, marginBottom: 6 }}>
        Location (tap the map)
      </Text>
      <View style={{ height: 240, borderRadius: 12, overflow: 'hidden', borderWidth: 1, borderColor: colors.border }}>
        <MapView
          ref={map}
          style={{ flex: 1 }}
          initialRegion={
            value
              ? { latitude: value.lat, longitude: value.lng, latitudeDelta: 0.02, longitudeDelta: 0.02 }
              : AMMAN
          }
          onPress={onPress}>
          {value ? <Marker coordinate={{ latitude: value.lat, longitude: value.lng }} /> : null}
        </MapView>
      </View>
      <View style={{ flexDirection: 'row', gap: 10, marginTop: 8, alignItems: 'center' }}>
        <Button title="Use my location" variant="outline" onPress={useMyLocation} loading={locating} style={{ paddingVertical: 8 }} />
        {value ? (
          <Button title="Clear" variant="outline" onPress={() => onChange(null)} style={{ paddingVertical: 8 }} />
        ) : null}
        <Text style={{ color: colors.muted, flex: 1, textAlign: 'right', fontSize: 12 }}>
          {value ? `${value.lat.toFixed(5)}, ${value.lng.toFixed(5)}` : 'No pin yet'}
        </Text>
      </View>
    </View>
  );
}

const round = (n: number) => Math.round(n * 1e6) / 1e6;
