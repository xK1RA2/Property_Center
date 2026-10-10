import * as SecureStore from 'expo-secure-store';
import { createContext, ReactNode, useCallback, useContext, useEffect, useMemo, useState } from 'react';
import { Platform } from 'react-native';

import { api, setAuthToken, setUnauthorizedHandler } from './api';
import { registerForPush, unregisterFromPush } from './push';
import type { User } from './types';

const TOKEN_KEY = 'pc_token';

const storage = {
  get: async (): Promise<string | null> => {
    try {
      if (Platform.OS === 'web') return globalThis.localStorage?.getItem(TOKEN_KEY) ?? null;
      return await SecureStore.getItemAsync(TOKEN_KEY);
    } catch {
      return null;
    }
  },
  set: async (value: string) => {
    try {
      if (Platform.OS === 'web') globalThis.localStorage?.setItem(TOKEN_KEY, value);
      else await SecureStore.setItemAsync(TOKEN_KEY, value);
    } catch {}
  },
  clear: async () => {
    try {
      if (Platform.OS === 'web') globalThis.localStorage?.removeItem(TOKEN_KEY);
      else await SecureStore.deleteItemAsync(TOKEN_KEY);
    } catch {}
  },
};

interface AuthState {
  user: User | null;
  loading: boolean;
  isDealer: boolean;
  signIn: (token: string, user: User) => Promise<void>;
  signOut: () => Promise<void>;
  setUser: (user: User) => void;
  refresh: () => Promise<void>;
}

const AuthContext = createContext<AuthState | null>(null);

export function AuthProvider({ children }: { children: ReactNode }) {
  const [user, setUser] = useState<User | null>(null);
  const [loading, setLoading] = useState(true);

  const clearSession = useCallback(async () => {
    setAuthToken(null);
    setUser(null);
    await storage.clear();
  }, []);

  useEffect(() => {
    (async () => {
      const token = await storage.get();
      if (token) {
        setAuthToken(token);
        try {
          setUser(await api.me());
        } catch {
          await clearSession();
        }
      }
      setLoading(false);
    })();
  }, [clearSession]);

  const userId = user?.id;
  useEffect(() => {
    if (userId) void registerForPush();
  }, [userId]);

  useEffect(() => {
    setUnauthorizedHandler(() => void clearSession());
    return () => setUnauthorizedHandler(null);
  }, [clearSession]);

  const value = useMemo<AuthState>(
    () => ({
      user,
      loading,
      isDealer: user?.role_id === 2,
      setUser,
      signIn: async (token, nextUser) => {
        setAuthToken(token);
        await storage.set(token);
        setUser(nextUser);
      },
      signOut: async () => {
        await unregisterFromPush();
        try {
          await api.logout();
        } catch {}
        await clearSession();
      },
      refresh: async () => setUser(await api.me()),
    }),
    [user, loading, clearSession],
  );

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
}

export function useAuth(): AuthState {
  const ctx = useContext(AuthContext);
  if (!ctx) throw new Error('useAuth must be used inside <AuthProvider>');
  return ctx;
}
