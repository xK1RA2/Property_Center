import { useMutation } from '@tanstack/react-query';
import * as ImagePicker from 'expo-image-picker';
import { useState } from 'react';
import { Alert, Image, Pressable, ScrollView, Text, View } from 'react-native';

import { Badge, Button, colors, Field, Screen } from '@/components/ui';
import { api, ApiError } from '@/lib/api';
import { useAuth } from '@/lib/auth';

export default function Profile() {
  const { user, setUser, signOut, isDealer } = useAuth();
  const [name, setName] = useState(user?.name ?? '');
  const [phone, setPhone] = useState(user?.phone ?? '');
  const [errors, setErrors] = useState<Record<string, string[]>>({});

  const save = useMutation({
    mutationFn: () => api.updateProfile({ name: name.trim(), phone: phone.trim() || null }),
    onSuccess: (u) => {
      setUser(u);
      setErrors({});
      Alert.alert('Saved', 'Your profile was updated.');
    },
    onError: (e) => e instanceof ApiError && setErrors(e.errors ?? { name: [e.message] }),
  });

  const pickPhoto = async () => {
    const res = await ImagePicker.launchImageLibraryAsync({ mediaTypes: ['images'], quality: 0.7, allowsEditing: true, aspect: [1, 1] });
    if (res.canceled) return;
    const asset = res.assets[0];
    try {
      setUser(
        await api.updatePhoto({
          uri: asset.uri,
          name: asset.fileName ?? 'photo.jpg',
          type: asset.mimeType ?? 'image/jpeg',
        }),
      );
    } catch (e) {
      Alert.alert('Upload failed', e instanceof ApiError ? e.detail : 'Try again');
    }
  };

  const becomeDealer = useMutation({
    mutationFn: api.requestDealer,
    onSuccess: () => Alert.alert('Request sent', 'An admin will review your dealer request.'),
    onError: (e) => Alert.alert('Could not send', e instanceof ApiError ? e.detail : 'Try again'),
  });

  if (!user) return null;

  return (
    <Screen>
      <ScrollView contentContainerStyle={{ padding: 20 }}>
        <View style={{ alignItems: 'center', marginBottom: 20 }}>
          <Pressable onPress={pickPhoto}>
            {user.profile_image ? (
              <Image source={{ uri: user.profile_image }} style={{ width: 96, height: 96, borderRadius: 48 }} />
            ) : (
              <View style={{ width: 96, height: 96, borderRadius: 48, backgroundColor: colors.primary, alignItems: 'center', justifyContent: 'center' }}>
                <Text style={{ color: '#fff', fontSize: 36, fontWeight: '800' }}>{user.name.charAt(0).toUpperCase()}</Text>
              </View>
            )}
            <Text style={{ color: colors.primary, marginTop: 8, textAlign: 'center' }}>Change photo</Text>
          </Pressable>
          <Text style={{ fontSize: 20, fontWeight: '800', marginTop: 8, color: colors.text }}>{user.name}</Text>
          <Text style={{ color: colors.muted }}>{user.email}</Text>
          <View style={{ marginTop: 6 }}>
            <Badge label={isDealer ? 'Dealer' : user.role_id === 3 ? 'Admin' : 'Member'} />
          </View>
        </View>

        <Field label="Name" value={name} onChangeText={setName} error={errors.name?.[0]} />
        <Field label="Phone" value={phone} onChangeText={setPhone} keyboardType="phone-pad" error={errors.phone?.[0]} />
        <Button title="Save changes" onPress={() => save.mutate()} loading={save.isPending} />

        {user.role_id === 1 ? (
          <Button title="Request to become a dealer" variant="outline" onPress={() => becomeDealer.mutate()} loading={becomeDealer.isPending} style={{ marginTop: 14 }} />
        ) : null}

        <Button title="Sign out" variant="danger" onPress={signOut} style={{ marginTop: 28 }} />
      </ScrollView>
    </Screen>
  );
}
