import { Link } from 'expo-router';
import { useState } from 'react';
import { KeyboardAvoidingView, Platform, ScrollView, Text, View } from 'react-native';

import { Button, colors, Field, Screen } from '@/components/ui';
import { api, ApiError } from '@/lib/api';
import { useAuth } from '@/lib/auth';

export default function Login() {
  const { signIn } = useAuth();
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState('');

  const submit = async () => {
    setBusy(true);
    setError('');
    try {
      const { token, user } = await api.login(email.trim(), password);
      await signIn(token, user); // the (auth) layout redirects into the app
    } catch (e) {
      setError(e instanceof ApiError ? e.detail : 'Something went wrong');
    } finally {
      setBusy(false);
    }
  };

  return (
    <Screen>
      <KeyboardAvoidingView behavior={Platform.OS === 'ios' ? 'padding' : undefined} style={{ flex: 1 }}>
        <ScrollView contentContainerStyle={{ padding: 24, flexGrow: 1, justifyContent: 'center' }} keyboardShouldPersistTaps="handled">
          <Text style={{ fontSize: 30, fontWeight: '800', color: colors.primary }}>Property Center</Text>
          <Text style={{ color: colors.muted, marginBottom: 28, marginTop: 4 }}>Sign in to find or list properties</Text>

          <Field label="Email" value={email} onChangeText={setEmail} autoCapitalize="none" keyboardType="email-address" autoComplete="email" />
          <Field label="Password" value={password} onChangeText={setPassword} secureTextEntry autoComplete="password" />
          {error ? <Text style={{ color: colors.danger, marginBottom: 12 }}>{error}</Text> : null}

          <Button title="Sign in" onPress={submit} loading={busy} disabled={!email || !password} />

          <View style={{ flexDirection: 'row', justifyContent: 'center', marginTop: 20 }}>
            <Text style={{ color: colors.muted }}>New here? </Text>
            <Link href="/register" style={{ color: colors.primary, fontWeight: '700' }}>
              Create an account
            </Link>
          </View>
        </ScrollView>
      </KeyboardAvoidingView>
    </Screen>
  );
}
