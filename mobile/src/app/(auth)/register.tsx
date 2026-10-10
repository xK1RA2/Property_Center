import { Link } from 'expo-router';
import { useState } from 'react';
import { KeyboardAvoidingView, Platform, ScrollView, Text, View } from 'react-native';

import { Button, colors, Field, Screen } from '@/components/ui';
import { api, ApiError } from '@/lib/api';
import { useAuth } from '@/lib/auth';

export default function Register() {
  const { signIn } = useAuth();
  const [form, setForm] = useState({ name: '', email: '', phone: '', password: '', password_confirmation: '' });
  const [busy, setBusy] = useState(false);
  const [errors, setErrors] = useState<Record<string, string[]>>({});
  const [error, setError] = useState('');

  const set = (key: keyof typeof form) => (value: string) => setForm((f) => ({ ...f, [key]: value }));

  const submit = async () => {
    setBusy(true);
    setError('');
    setErrors({});
    try {
      const { token, user } = await api.register({
        ...form,
        email: form.email.trim(),
        phone: form.phone.trim() || undefined,
      });
      await signIn(token, user);
    } catch (e) {
      if (e instanceof ApiError) {
        setErrors(e.errors ?? {});
        if (!e.errors) setError(e.message);
      } else {
        setError('Something went wrong');
      }
    } finally {
      setBusy(false);
    }
  };

  return (
    <Screen>
      <KeyboardAvoidingView behavior={Platform.OS === 'ios' ? 'padding' : undefined} style={{ flex: 1 }}>
        <ScrollView contentContainerStyle={{ padding: 24 }} keyboardShouldPersistTaps="handled">
          <Text style={{ fontSize: 26, fontWeight: '800', color: colors.text, marginBottom: 20 }}>Create account</Text>

          <Field label="Full name" value={form.name} onChangeText={set('name')} error={errors.name?.[0]} />
          <Field label="Email" value={form.email} onChangeText={set('email')} autoCapitalize="none" keyboardType="email-address" error={errors.email?.[0]} />
          <Field label="Phone (optional)" value={form.phone} onChangeText={set('phone')} keyboardType="phone-pad" error={errors.phone?.[0]} />
          <Field label="Password" value={form.password} onChangeText={set('password')} secureTextEntry error={errors.password?.[0]} />
          <Field label="Confirm password" value={form.password_confirmation} onChangeText={set('password_confirmation')} secureTextEntry />
          {error ? <Text style={{ color: colors.danger, marginBottom: 12 }}>{error}</Text> : null}

          <Button title="Sign up" onPress={submit} loading={busy} disabled={!form.name || !form.email || !form.password} />

          <View style={{ flexDirection: 'row', justifyContent: 'center', marginTop: 20 }}>
            <Text style={{ color: colors.muted }}>Already have an account? </Text>
            <Link href="/login" style={{ color: colors.primary, fontWeight: '700' }}>
              Sign in
            </Link>
          </View>
        </ScrollView>
      </KeyboardAvoidingView>
    </Screen>
  );
}
