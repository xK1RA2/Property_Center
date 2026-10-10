import { useMutation, useQueryClient } from '@tanstack/react-query';
import { Link } from 'expo-router';
import { Alert, Pressable, Text, View } from 'react-native';

import { PagedList } from '@/components/PagedList';
import { Badge, Button, colors, money, Screen } from '@/components/ui';
import { api, ApiError } from '@/lib/api';
import { useAuth } from '@/lib/auth';
import type { Booking } from '@/lib/types';

const STATUS_COLOR: Record<Booking['status'], string> = {
  Pending: colors.warning,
  Approved: colors.primary,
  Rejected: colors.danger,
};

function BookingItem({ booking, isDealer }: { booking: Booking; isDealer: boolean }) {
  const qc = useQueryClient();
  const decide = useMutation({
    mutationFn: (status: 'Approved' | 'Rejected') => api.decideBooking(booking.id, status),
    onSuccess: () => qc.invalidateQueries({ queryKey: ['bookings'] }),
    onError: (e) => Alert.alert('Could not update', e instanceof ApiError ? e.detail : 'Try again'),
  });

  return (
    <View style={{ backgroundColor: colors.card, borderRadius: 14, padding: 14, marginBottom: 12, borderWidth: 1, borderColor: colors.border }}>
      <View style={{ flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' }}>
        <Text style={{ fontWeight: '700', color: colors.text }}>{new Date(booking.date).toLocaleString()}</Text>
        <Badge label={booking.status} color={STATUS_COLOR[booking.status]} />
      </View>

      <Link href={{ pathname: '/property/[id]', params: { id: String(booking.property_id) } }} asChild>
        <Pressable>
          <Text style={{ color: colors.primary, marginTop: 6 }} numberOfLines={1}>
            {booking.property
              ? `${booking.property.address.replace(/\s*\n\s*/g, ', ')} · ${money(booking.property.price)}`
              : `Property #${booking.property_id}`}
          </Text>
        </Pressable>
      </Link>

      {isDealer && booking.user ? (
        <Text style={{ color: colors.muted, marginTop: 4 }}>
          From {booking.user.name}
          {booking.user.phone ? ` · ${booking.user.phone}` : ''}
        </Text>
      ) : null}
      {booking.description && booking.description !== 'Empty' ? (
        <Text style={{ color: colors.text, marginTop: 6 }}>{booking.description}</Text>
      ) : null}

      {isDealer && booking.status === 'Pending' ? (
        <View style={{ flexDirection: 'row', gap: 10, marginTop: 12 }}>
          <Button title="Approve" onPress={() => decide.mutate('Approved')} loading={decide.isPending} style={{ flex: 1 }} />
          <Button title="Reject" variant="danger" onPress={() => decide.mutate('Rejected')} disabled={decide.isPending} style={{ flex: 1 }} />
        </View>
      ) : null}
    </View>
  );
}

export default function Bookings() {
  const { isDealer } = useAuth();

  return (
    <Screen>
      <Text style={{ fontSize: 24, fontWeight: '800', color: colors.text, paddingHorizontal: 16, paddingTop: 8 }}>
        {isDealer ? 'Booking requests' : 'My bookings'}
      </Text>
      <PagedList
        queryKey={['bookings']}
        fetchPage={api.bookings}
        renderItem={(b) => <BookingItem booking={b} isDealer={isDealer} />}
        emptyTitle="No bookings yet"
        emptyHint={isDealer ? 'Requests for your properties show up here.' : 'Book a visit from any property page.'}
      />
    </Screen>
  );
}
