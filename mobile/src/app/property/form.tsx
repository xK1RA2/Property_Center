import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import * as ImagePicker from 'expo-image-picker';
import { router, Stack, useLocalSearchParams } from 'expo-router';
import { useEffect, useMemo, useState } from 'react';
import { Alert, Image, KeyboardAvoidingView, Platform, Pressable, ScrollView, Text, View } from 'react-native';

import { Coordinate, LocationPicker } from '@/components/LocationPicker';
import { Button, Chip, colors, Field, Loading } from '@/components/ui';
import { api, ApiError, LocalImage, PropertyInput } from '@/lib/api';

export default function PropertyForm() {
  const { id } = useLocalSearchParams<{ id?: string }>();
  const editing = id ? Number(id) : null;
  const qc = useQueryClient();

  const types = useQuery({ queryKey: ['property-types'], queryFn: api.propertyTypes, staleTime: Infinity });
  const cities = useQuery({ queryKey: ['cities'], queryFn: api.cities, staleTime: Infinity });
  const existing = useQuery({
    queryKey: ['property', editing],
    queryFn: () => api.property(editing!),
    enabled: editing !== null,
  });

  const [purchase, setPurchase] = useState<'Sell' | 'Rent'>('Sell');
  const [typeId, setTypeId] = useState<number>();
  const [cityId, setCityId] = useState<number>();
  const [cityQuery, setCityQuery] = useState('');
  const [price, setPrice] = useState('');
  const [year, setYear] = useState(String(new Date().getFullYear()));
  const [address, setAddress] = useState('');
  const [description, setDescription] = useState('');
  const [bedrooms, setBedrooms] = useState('1');
  const [bathrooms, setBathrooms] = useState('1');
  const [area, setArea] = useState('50');
  const [parking, setParking] = useState(false);
  const [ac, setAc] = useState(false);
  const [heating, setHeating] = useState(false);
  const [pin, setPin] = useState<Coordinate | null>(null);
  const [images, setImages] = useState<LocalImage[]>([]);
  const [errors, setErrors] = useState<Record<string, string[]>>({});

  // fill the form when editing
  useEffect(() => {
    const p = existing.data;
    if (!p) return;
    setPurchase(p.purchase_type);
    setTypeId(p.type?.id);
    setCityId(p.city?.id);
    setPrice(String(p.price));
    setYear(String(p.year));
    setAddress(p.address);
    setDescription(p.description ?? '');
    if (p.features) {
      setBedrooms(String(p.features.Bedrooms));
      setBathrooms(String(p.features.Bathrooms));
      setArea(String(p.features.Area));
      setParking(!!p.features.Parking);
      setAc(!!p.features.Air_Conditioner);
      setHeating(!!p.features.Heating);
    }
    if (p.location) setPin({ lat: p.location.lat, lng: p.location.lng });
  }, [existing.data]);

  const cityMatches = useMemo(() => {
    const q = cityQuery.trim().toLowerCase();
    const all = cities.data ?? [];
    return (q ? all.filter((c) => c.name.toLowerCase().includes(q)) : all).slice(0, 8);
  }, [cities.data, cityQuery]);
  const selectedCity = cities.data?.find((c) => c.id === cityId);

  const pickImages = async () => {
    const res = await ImagePicker.launchImageLibraryAsync({ mediaTypes: ['images'], allowsMultipleSelection: true, quality: 0.7, selectionLimit: 10 });
    if (res.canceled) return;
    setImages((cur) => [
      ...cur,
      ...res.assets.map((a, i) => ({ uri: a.uri, name: a.fileName ?? `photo-${Date.now()}-${i}.jpg`, type: a.mimeType ?? 'image/jpeg' })),
    ]);
  };

  const save = useMutation({
    mutationFn: () => {
      const input: PropertyInput = {
        purchase_type: purchase,
        property_type_id: typeId!,
        city_id: cityId!,
        price: Number(price),
        year: Number(year),
        address: address.trim(),
        description: description.trim(),
        features: {
          Bedrooms: Number(bedrooms) || 0,
          Bathrooms: Number(bathrooms) || 0,
          Area: Number(area) || 0,
          Parking: parking,
          Air_Conditioner: ac,
          Heating: heating,
        },
        ...(pin ? { location: { name: address.trim() || 'Location', lat: pin.lat, lng: pin.lng } } : {}),
      };
      return editing ? api.updateProperty(editing, input, images) : api.createProperty(input, images);
    },
    onSuccess: (p) => {
      qc.invalidateQueries({ queryKey: ['my-properties'] });
      qc.invalidateQueries({ queryKey: ['properties'] });
      qc.invalidateQueries({ queryKey: ['property', p.id] });
      router.back();
    },
    onError: (e) => {
      if (e instanceof ApiError && e.errors) setErrors(e.errors);
      else Alert.alert('Could not save', e instanceof ApiError ? e.detail : 'Something went wrong');
    },
  });

  if (editing && existing.isLoading) return <Loading />;

  const ready = typeId && cityId && price && year && address.trim();

  return (
    <KeyboardAvoidingView behavior={Platform.OS === 'ios' ? 'padding' : undefined} style={{ flex: 1 }}>
      <Stack.Screen options={{ title: editing ? 'Edit listing' : 'New listing' }} />
      <ScrollView contentContainerStyle={{ padding: 16, paddingBottom: 48 }} keyboardShouldPersistTaps="handled">
        <Text style={labelStyle}>For</Text>
        <View style={{ flexDirection: 'row', marginBottom: 14 }}>
          <Chip label="Sale" active={purchase === 'Sell'} onPress={() => setPurchase('Sell')} />
          <Chip label="Rent" active={purchase === 'Rent'} onPress={() => setPurchase('Rent')} />
        </View>

        <Text style={labelStyle}>Property type</Text>
        <View style={{ flexDirection: 'row', flexWrap: 'wrap', rowGap: 8, marginBottom: 14 }}>
          {(types.data ?? []).map((t) => (
            <Chip key={t.id} label={t.name} active={typeId === t.id} onPress={() => setTypeId(t.id)} />
          ))}
        </View>
        {errors.property_type_id ? <Text style={errStyle}>{errors.property_type_id[0]}</Text> : null}

        <Field label={selectedCity ? `City: ${selectedCity.name}` : 'City (type to search)'} value={cityQuery} onChangeText={setCityQuery} placeholder="Search city…" error={errors.city_id?.[0]} />
        <View style={{ flexDirection: 'row', flexWrap: 'wrap', rowGap: 8, marginBottom: 14, marginTop: -6 }}>
          {cityMatches.map((c) => (
            <Chip key={c.id} label={c.name} active={cityId === c.id} onPress={() => setCityId(c.id)} />
          ))}
        </View>

        <Field label="Price" value={price} onChangeText={setPrice} keyboardType="numeric" error={errors.price?.[0]} />
        <Field label="Year built" value={year} onChangeText={setYear} keyboardType="numeric" error={errors.year?.[0]} />
        <Field label="Address" value={address} onChangeText={setAddress} error={errors.address?.[0]} />
        <Field label="Description" value={description} onChangeText={setDescription} multiline />

        <View style={{ flexDirection: 'row', gap: 10 }}>
          <View style={{ flex: 1 }}><Field label="Bedrooms" value={bedrooms} onChangeText={setBedrooms} keyboardType="numeric" /></View>
          <View style={{ flex: 1 }}><Field label="Bathrooms" value={bathrooms} onChangeText={setBathrooms} keyboardType="numeric" /></View>
          <View style={{ flex: 1 }}><Field label="Area m²" value={area} onChangeText={setArea} keyboardType="numeric" /></View>
        </View>
        <View style={{ flexDirection: 'row', marginBottom: 14 }}>
          <Chip label="Parking" active={parking} onPress={() => setParking(!parking)} />
          <Chip label="A/C" active={ac} onPress={() => setAc(!ac)} />
          <Chip label="Heating" active={heating} onPress={() => setHeating(!heating)} />
        </View>

        <LocationPicker value={pin} onChange={setPin} />

        <Text style={labelStyle}>Photos {images.length ? `(${images.length} new)` : ''}</Text>
        <ScrollView horizontal showsHorizontalScrollIndicator={false} style={{ marginBottom: 10 }}>
          {images.map((img, i) => (
            <Pressable key={img.uri} onPress={() => setImages((cur) => cur.filter((_, j) => j !== i))}>
              <Image source={{ uri: img.uri }} style={{ width: 84, height: 84, borderRadius: 10, marginRight: 8 }} />
            </Pressable>
          ))}
        </ScrollView>
        {images.length ? <Text style={{ color: colors.muted, fontSize: 12, marginBottom: 10 }}>Tap a photo to remove it.</Text> : null}
        <Button title="Add photos" variant="outline" onPress={pickImages} style={{ marginBottom: 20 }} />
        {errors.images ? <Text style={errStyle}>{errors.images[0]}</Text> : null}

        <Button title={editing ? 'Save changes' : 'Publish listing'} onPress={() => save.mutate()} loading={save.isPending} disabled={!ready} />
      </ScrollView>
    </KeyboardAvoidingView>
  );
}

const labelStyle = { fontSize: 13, fontWeight: '600' as const, color: colors.muted, marginBottom: 6 };
const errStyle = { color: colors.danger, fontSize: 12, marginBottom: 8 };
