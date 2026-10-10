import { useEffect, useState } from 'react';
import { ScrollView, Text, TextInput, View } from 'react-native';

import { PagedList } from '@/components/PagedList';
import { PropertyCard } from '@/components/PropertyCard';
import { Chip, colors, Screen } from '@/components/ui';
import { api } from '@/lib/api';
import type { PropertyFilters } from '@/lib/types';

const SORTS: { label: string; value: NonNullable<PropertyFilters['sort']> }[] = [
  { label: 'Newest', value: 'newest' },
  { label: 'Price ↑', value: 'price_asc' },
  { label: 'Price ↓', value: 'price_desc' },
];

export default function Explore() {
  const [search, setSearch] = useState('');
  const [debounced, setDebounced] = useState('');
  const [purchase, setPurchase] = useState<PropertyFilters['purchase_type']>();
  const [sort, setSort] = useState<PropertyFilters['sort']>('newest');

  // wait for the user to stop typing before hitting the API
  useEffect(() => {
    const t = setTimeout(() => setDebounced(search.trim()), 350);
    return () => clearTimeout(t);
  }, [search]);

  const filters: PropertyFilters = { search: debounced || undefined, purchase_type: purchase, sort };

  const header = (
    <View style={{ marginBottom: 12 }}>
      <TextInput
        value={search}
        onChangeText={setSearch}
        placeholder="Search address or description"
        placeholderTextColor={colors.muted}
        style={{
          backgroundColor: colors.card,
          borderWidth: 1,
          borderColor: colors.border,
          borderRadius: 12,
          paddingHorizontal: 14,
          paddingVertical: 11,
          fontSize: 15,
          marginBottom: 10,
        }}
      />
      <ScrollView horizontal showsHorizontalScrollIndicator={false}>
        <Chip label="All" active={!purchase} onPress={() => setPurchase(undefined)} />
        <Chip label="Buy" active={purchase === 'Sell'} onPress={() => setPurchase('Sell')} />
        <Chip label="Rent" active={purchase === 'Rent'} onPress={() => setPurchase('Rent')} />
        <View style={{ width: 12 }} />
        {SORTS.map((s) => (
          <Chip key={s.value} label={s.label} active={sort === s.value} onPress={() => setSort(s.value)} />
        ))}
      </ScrollView>
    </View>
  );

  return (
    <Screen>
      <Text style={{ fontSize: 24, fontWeight: '800', color: colors.text, paddingHorizontal: 16, paddingTop: 8 }}>
        Find your place
      </Text>
      <PagedList
        queryKey={['properties', filters]}
        fetchPage={(page) => api.properties(page, filters)}
        renderItem={(p) => <PropertyCard property={p} />}
        header={header}
        emptyTitle="No properties found"
        emptyHint="Try a different search or filter."
      />
    </Screen>
  );
}
