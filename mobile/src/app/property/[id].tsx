import { Ionicons } from '@expo/vector-icons';
import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import { router, Stack, useLocalSearchParams } from 'expo-router';
import { useState } from 'react';
import {
  Alert,
  FlatList,
  Image,
  Pressable,
  ScrollView,
  Switch,
  Text,
  useWindowDimensions,
  View,
} from 'react-native';

import { DateTimeField } from '@/components/DateTimeField';
import { PropertyMap } from '@/components/PropertyMap';
import { Badge, Button, colors, ErrorState, Field, Loading, money } from '@/components/ui';
import { api, ApiError } from '@/lib/api';
import { useAuth } from '@/lib/auth';

const tomorrowAt10 = () => {
  const d = new Date(Date.now() + 86_400_000);
  d.setHours(10, 0, 0, 0);
  return d;
};

export default function PropertyDetail() {
  const { id: rawId } = useLocalSearchParams<{ id: string }>();
  const id = Number(rawId);
  const { user } = useAuth();
  const qc = useQueryClient();
  const { width } = useWindowDimensions();

  const property = useQuery({ queryKey: ['property', id], queryFn: () => api.property(id) });
  const comments = useQuery({ queryKey: ['comments', id], queryFn: () => api.comments(id) });

  const [when, setWhen] = useState<Date>(tomorrowAt10());
  const [note, setNote] = useState('');
  const [comment, setComment] = useState('');
  const [anonymous, setAnonymous] = useState(false);

  const refreshProperty = () => {
    qc.invalidateQueries({ queryKey: ['property', id] });
    qc.invalidateQueries({ queryKey: ['properties'] });
  };
  const fail = (title: string) => (e: unknown) =>
    Alert.alert(title, e instanceof ApiError ? e.detail : 'Something went wrong');

  const toggleFavorite = useMutation({
    mutationFn: async (on: boolean) => {
      if (on) await api.addFavorite(id);
      else await api.removeFavorite(id);
    },
    onSuccess: () => {
      refreshProperty();
      qc.invalidateQueries({ queryKey: ['favorites'] });
    },
    onError: fail('Could not update favorites'),
  });

  const rate = useMutation({
    mutationFn: (n: number) => api.rate(id, n),
    onSuccess: refreshProperty,
    onError: fail('Could not rate'),
  });

  const book = useMutation({
    mutationFn: () => {
      return api.book(id, when.toISOString(), note.trim() || undefined);
    },
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['bookings'] });
      Alert.alert('Request sent', 'The dealer will confirm your visit.');
    },
    onError: fail('Could not book'),
  });

  const addComment = useMutation({
    mutationFn: () => api.addComment(id, comment.trim(), anonymous),
    onSuccess: () => {
      setComment('');
      qc.invalidateQueries({ queryKey: ['comments', id] });
    },
    onError: fail('Could not comment'),
  });

  const removeComment = useMutation({
    mutationFn: api.deleteComment,
    onSuccess: () => qc.invalidateQueries({ queryKey: ['comments', id] }),
    onError: fail('Could not delete'),
  });

  const remove = useMutation({
    mutationFn: () => api.deleteProperty(id),
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['my-properties'] });
      qc.invalidateQueries({ queryKey: ['properties'] });
      router.back();
    },
    onError: fail('Could not delete'),
  });

  if (property.isLoading) return <Loading />;
  if (property.isError || !property.data)
    return <ErrorState message={(property.error as Error)?.message ?? 'Not found'} onRetry={() => property.refetch()} />;

  const p = property.data;
  const isOwner = user && (p.dealer?.id === user.id || user.role_id === 3);
  const f = p.features;
  const images = p.images ?? [];

  const confirmDelete = () =>
    Alert.alert('Delete listing?', 'This cannot be undone.', [
      { text: 'Cancel', style: 'cancel' },
      { text: 'Delete', style: 'destructive', onPress: () => remove.mutate() },
    ]);

  return (
    <>
      <Stack.Screen
        options={{
          title: p.type?.name ?? 'Property',
          headerRight: () => (
            <Pressable onPress={() => toggleFavorite.mutate(!p.is_favorite)} hitSlop={10}>
              <Ionicons name={p.is_favorite ? 'heart' : 'heart-outline'} size={26} color={colors.danger} />
            </Pressable>
          ),
        }}
      />
      <ScrollView contentContainerStyle={{ paddingBottom: 40 }} keyboardShouldPersistTaps="handled">
        {images.length ? (
          <FlatList
            data={images}
            horizontal
            pagingEnabled
            showsHorizontalScrollIndicator={false}
            keyExtractor={(i) => String(i.id)}
            renderItem={({ item }) => <Image source={{ uri: item.url }} style={{ width, height: 240 }} />}
          />
        ) : (
          <View style={{ height: 160, backgroundColor: colors.border, alignItems: 'center', justifyContent: 'center' }}>
            <Text style={{ color: colors.muted }}>No photos</Text>
          </View>
        )}

        <View style={{ padding: 16 }}>
          <View style={{ flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' }}>
            <Text style={{ fontSize: 26, fontWeight: '800', color: colors.text }}>
              {money(p.price)}
              {p.purchase_type === 'Rent' ? ' / mo' : ''}
            </Text>
            <Badge label={p.purchase_type === 'Rent' ? 'For rent' : 'For sale'} />
          </View>
          <Text style={{ marginTop: 6, color: colors.text, fontSize: 15 }}>{p.address.replace(/\s*\n\s*/g, ', ')}</Text>
          <Text style={{ color: colors.muted, marginTop: 2 }}>
            {[p.city?.name, p.year ? `Built ${p.year}` : null].filter(Boolean).join(' · ')}
          </Text>
          {p.status !== 'Available' ? <View style={{ marginTop: 8 }}><Badge label={p.status} color={colors.warning} /></View> : null}

          {f ? (
            <View style={{ flexDirection: 'row', flexWrap: 'wrap', gap: 8, marginTop: 14 }}>
              {[
                `${f.Bedrooms} bed`,
                `${f.Bathrooms} bath`,
                `${f.Area} m²`,
                f.Parking ? 'Parking' : null,
                f.Air_Conditioner ? 'A/C' : null,
                f.Heating ? 'Heating' : null,
              ]
                .filter(Boolean)
                .map((t) => (
                  <Badge key={t} label={t as string} color={colors.text} />
                ))}
            </View>
          ) : null}

          {p.description ? <Text style={{ marginTop: 16, color: colors.text, lineHeight: 21 }}>{p.description}</Text> : null}

          {p.dealer ? (
            <Text style={{ marginTop: 16, color: colors.muted }}>
              Listed by <Text style={{ color: colors.text, fontWeight: '700' }}>{p.dealer.name}</Text>
              {p.dealer.phone ? ` · ${p.dealer.phone}` : ''}
            </Text>
          ) : null}

          {p.location ? <PropertyMap lat={p.location.lat} lng={p.location.lng} title={p.address} /> : null}

          {isOwner ? (
            <View style={{ flexDirection: 'row', gap: 10, marginTop: 16 }}>
              <Button title="Edit" variant="outline" style={{ flex: 1 }} onPress={() => router.push({ pathname: '/property/form', params: { id: String(id) } })} />
              <Button title="Delete" variant="danger" style={{ flex: 1 }} onPress={confirmDelete} loading={remove.isPending} />
            </View>
          ) : null}

          <Text style={{ fontSize: 17, fontWeight: '800', marginTop: 24, color: colors.text }}>Rating</Text>
          <View style={{ flexDirection: 'row', alignItems: 'center', marginTop: 6 }}>
            {[1, 2, 3, 4, 5].map((n) => (
              <Pressable key={n} onPress={() => rate.mutate(n)} hitSlop={6}>
                <Ionicons name={n <= (p.my_rating ?? 0) ? 'star' : 'star-outline'} size={28} color={colors.warning} />
              </Pressable>
            ))}
            <Text style={{ marginLeft: 10, color: colors.muted }}>
              {p.rating_avg ? `${p.rating_avg} avg` : 'No ratings yet'}
              {p.rating_count ? ` (${p.rating_count})` : ''}
            </Text>
          </View>

          {!isOwner && p.status === 'Available' ? (
            <View style={{ marginTop: 24 }}>
              <Text style={{ fontSize: 17, fontWeight: '800', color: colors.text, marginBottom: 10 }}>Book a visit</Text>
              <DateTimeField label="Preferred date & time" value={when} onChange={setWhen} />
              <Field label="Note (optional)" value={note} onChangeText={setNote} />
              <Button title="Request visit" onPress={() => book.mutate()} loading={book.isPending} />
            </View>
          ) : null}

          <Text style={{ fontSize: 17, fontWeight: '800', marginTop: 28, marginBottom: 10, color: colors.text }}>Comments</Text>
          <Field label="Add a comment" value={comment} onChangeText={setComment} multiline />
          <View style={{ flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', marginBottom: 12 }}>
            <View style={{ flexDirection: 'row', alignItems: 'center' }}>
              <Switch value={anonymous} onValueChange={setAnonymous} trackColor={{ true: colors.primary }} />
              <Text style={{ marginLeft: 8, color: colors.muted }}>Anonymous</Text>
            </View>
            <Button title="Post" onPress={() => addComment.mutate()} loading={addComment.isPending} disabled={!comment.trim()} style={{ paddingVertical: 9 }} />
          </View>

          {(comments.data ?? []).map((c) => (
            <View key={c.id} style={{ backgroundColor: colors.card, borderRadius: 10, padding: 12, marginBottom: 8, borderWidth: 1, borderColor: colors.border }}>
              <View style={{ flexDirection: 'row', justifyContent: 'space-between' }}>
                <Text style={{ fontWeight: '700', color: colors.text }}>{c.anonymous ? 'Anonymous' : c.author?.name ?? 'User'}</Text>
                {c.is_mine || user?.role_id === 3 ? (
                  <Pressable onPress={() => removeComment.mutate(c.id)} hitSlop={8}>
                    <Text style={{ color: colors.danger }}>Delete</Text>
                  </Pressable>
                ) : null}
              </View>
              <Text style={{ marginTop: 4, color: colors.text }}>{c.description}</Text>
            </View>
          ))}
          {comments.data?.length === 0 ? <Text style={{ color: colors.muted }}>Be the first to comment.</Text> : null}
        </View>
      </ScrollView>
    </>
  );
}
