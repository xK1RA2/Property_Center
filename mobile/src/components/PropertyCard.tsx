import { Link } from 'expo-router';
import { Image, Pressable, StyleSheet, Text, View } from 'react-native';

import type { Property } from '@/lib/types';

import { Badge, colors, money } from './ui';

export function PropertyCard({ property }: { property: Property }) {
  return (
    <Link href={{ pathname: '/property/[id]', params: { id: String(property.id) } }} asChild>
      <Pressable style={styles.card}>
        {property.primary_image ? (
          <Image source={{ uri: property.primary_image }} style={styles.image} />
        ) : (
          <View style={[styles.image, styles.placeholder]}>
            <Text style={{ color: colors.muted }}>No photo</Text>
          </View>
        )}
        <View style={{ padding: 12 }}>
          <View style={styles.row}>
            <Text style={styles.price}>
              {money(property.price)}
              {property.purchase_type === 'Rent' ? ' / mo' : ''}
            </Text>
            <Badge label={property.purchase_type === 'Rent' ? 'For rent' : 'For sale'} />
          </View>
          <Text numberOfLines={1} style={styles.address}>
            {property.address.replace(/\s*\n\s*/g, ', ')}
          </Text>
          <Text style={styles.meta}>
            {[property.type?.name, property.city?.name].filter(Boolean).join(' · ')}
            {property.rating_avg ? `  ·  ★ ${property.rating_avg}` : ''}
          </Text>
          {property.status !== 'Available' ? <Badge label={property.status} color={colors.warning} /> : null}
        </View>
      </Pressable>
    </Link>
  );
}

const styles = StyleSheet.create({
  card: {
    backgroundColor: colors.card,
    borderRadius: 14,
    marginBottom: 14,
    overflow: 'hidden',
    borderWidth: 1,
    borderColor: colors.border,
  },
  image: { width: '100%', height: 180, backgroundColor: colors.border },
  placeholder: { alignItems: 'center', justifyContent: 'center' },
  row: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' },
  price: { fontSize: 20, fontWeight: '800', color: colors.text },
  address: { marginTop: 4, color: colors.text, fontSize: 14 },
  meta: { marginTop: 2, color: colors.muted, fontSize: 13, marginBottom: 4 },
});
