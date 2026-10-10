import { Text, View } from 'react-native';

import { Field, colors } from './ui';

export interface Coordinate {
  lat: number;
  lng: number;
}

/** Web has no native map view, so the pin is entered as numbers. */
export function LocationPicker({
  value,
  onChange,
}: {
  value: Coordinate | null;
  onChange: (coordinate: Coordinate | null) => void;
}) {
  const set = (key: keyof Coordinate) => (text: string) => {
    const n = Number(text);
    const next = { lat: value?.lat ?? 0, lng: value?.lng ?? 0, [key]: Number.isFinite(n) ? n : 0 };
    onChange(text === '' && !value ? null : next);
  };

  return (
    <View>
      <Text style={{ fontSize: 13, fontWeight: '600', color: colors.muted, marginBottom: 6 }}>Location</Text>
      <View style={{ flexDirection: 'row', gap: 10 }}>
        <View style={{ flex: 1 }}>
          <Field label="Latitude" value={value ? String(value.lat) : ''} onChangeText={set('lat')} keyboardType="numeric" />
        </View>
        <View style={{ flex: 1 }}>
          <Field label="Longitude" value={value ? String(value.lng) : ''} onChangeText={set('lng')} keyboardType="numeric" />
        </View>
      </View>
    </View>
  );
}
