import { Text } from 'react-native';

import { PagedList } from '@/components/PagedList';
import { PropertyCard } from '@/components/PropertyCard';
import { colors, Screen } from '@/components/ui';
import { api } from '@/lib/api';

export default function Favorites() {
  return (
    <Screen>
      <Text style={{ fontSize: 24, fontWeight: '800', color: colors.text, paddingHorizontal: 16, paddingTop: 8 }}>
        Saved properties
      </Text>
      <PagedList
        queryKey={['favorites']}
        fetchPage={api.favorites}
        renderItem={(p) => <PropertyCard property={p} />}
        emptyTitle="Nothing saved yet"
        emptyHint="Tap the heart on a property to save it here."
      />
    </Screen>
  );
}
