import { router } from 'expo-router';
import { Text, View } from 'react-native';

import { PagedList } from '@/components/PagedList';
import { PropertyCard } from '@/components/PropertyCard';
import { Button, colors, Screen } from '@/components/ui';
import { api } from '@/lib/api';

export default function Listings() {
  return (
    <Screen>
      <View style={{ flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', paddingHorizontal: 16, paddingTop: 8 }}>
        <Text style={{ fontSize: 24, fontWeight: '800', color: colors.text }}>My listings</Text>
        <Button title="+ Add" onPress={() => router.push('/property/form')} style={{ paddingVertical: 8 }} />
      </View>
      <PagedList
        queryKey={['my-properties']}
        fetchPage={api.myProperties}
        renderItem={(p) => <PropertyCard property={p} />}
        emptyTitle="No listings yet"
        emptyHint="Add your first property to start receiving bookings."
      />
    </Screen>
  );
}
