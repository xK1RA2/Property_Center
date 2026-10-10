import { Platform } from 'react-native';

import type {
  Booking,
  Comment,
  Lookup,
  Paginated,
  Property,
  PropertyFilters,
  User,
} from './types';

/**
 * Where the Laravel server lives.
 *  - Android emulator: http://10.0.2.2:8000
 *  - iOS simulator / web: http://127.0.0.1:8000
 *  - Physical phone: http://<your-pc-LAN-ip>:8000 (run `php artisan serve --host=0.0.0.0`)
 * Override with EXPO_PUBLIC_API_URL in mobile/.env
 */
const DEFAULT_HOST = Platform.OS === 'android' ? 'http://10.0.2.2:8000' : 'http://127.0.0.1:8000';
export const API_HOST = (process.env.EXPO_PUBLIC_API_URL ?? DEFAULT_HOST).replace(/\/$/, '');
const BASE = `${API_HOST}/api/v1`;

export class ApiError extends Error {
  constructor(
    message: string,
    public status: number,
    public errors?: Record<string, string[]>,
  ) {
    super(message);
  }

  /** First validation message, or the generic one. */
  get detail(): string {
    const first = this.errors ? Object.values(this.errors)[0]?.[0] : undefined;
    return first ?? this.message;
  }
}

let authToken: string | null = null;
let onUnauthorized: (() => void) | null = null;

export const setAuthToken = (token: string | null) => {
  authToken = token;
};
export const setUnauthorizedHandler = (fn: (() => void) | null) => {
  onUnauthorized = fn;
};

type Query = Record<string, string | number | boolean | undefined | null>;

async function request<T>(
  method: string,
  path: string,
  opts: { body?: unknown; form?: FormData; query?: Query } = {},
): Promise<T> {
  let url = BASE + path;
  if (opts.query) {
    const qs = Object.entries(opts.query)
      .filter(([, v]) => v !== undefined && v !== null && v !== '')
      .map(([k, v]) => `${encodeURIComponent(k)}=${encodeURIComponent(String(v))}`)
      .join('&');
    if (qs) url += `?${qs}`;
  }

  const headers: Record<string, string> = { Accept: 'application/json' };
  if (authToken) headers.Authorization = `Bearer ${authToken}`;

  let body: BodyInit | undefined;
  if (opts.form) {
    body = opts.form; // fetch sets the multipart boundary itself
  } else if (opts.body !== undefined) {
    headers['Content-Type'] = 'application/json';
    body = JSON.stringify(opts.body);
  }

  let res: Response;
  try {
    res = await fetch(url, { method, headers, body });
  } catch {
    throw new ApiError(`Can't reach the server at ${API_HOST}. Is it running?`, 0);
  }

  if (res.status === 204) return undefined as T;

  const json = await res.json().catch(() => null);

  if (!res.ok) {
    if (res.status === 401 && authToken) onUnauthorized?.();
    throw new ApiError(json?.message ?? `Request failed (${res.status})`, res.status, json?.errors);
  }
  return json as T;
}

const get = <T>(path: string, query?: Query) => request<T>('GET', path, { query });
const send = <T>(method: string, path: string, body?: unknown) => request<T>(method, path, { body });

// ---------- multipart helpers ----------
export interface LocalImage {
  uri: string;
  name: string;
  type: string;
}

export interface PropertyInput {
  purchase_type: 'Sell' | 'Rent';
  property_type_id: number;
  city_id: number;
  price: number;
  year: number;
  address: string;
  description?: string;
  status?: 'Available' | 'Not Available';
  features?: Partial<Record<'Bedrooms' | 'Bathrooms' | 'Kitchen' | 'Rooms' | 'Area', number>> &
    Partial<Record<'Heating' | 'Air_Conditioner' | 'Parking', boolean>>;
  location?: { name: string; lat: number; lng: number };
}

function propertyForm(input: Partial<PropertyInput>, images: LocalImage[], method?: 'PATCH') {
  const form = new FormData();
  // PHP only parses multipart on POST, so updates are spoofed via _method
  if (method) form.append('_method', method);

  const add = (key: string, value: unknown) => {
    if (value === undefined || value === null) return;
    form.append(key, typeof value === 'boolean' ? (value ? '1' : '0') : String(value));
  };

  (['purchase_type', 'property_type_id', 'city_id', 'price', 'year', 'address', 'description', 'status'] as const).forEach(
    (k) => add(k, input[k]),
  );
  Object.entries(input.features ?? {}).forEach(([k, v]) => add(`features[${k}]`, v));
  if (input.location) {
    add('location[name]', input.location.name);
    add('location[lat]', input.location.lat);
    add('location[lng]', input.location.lng);
  }
  images.forEach((img) => form.append('images[]', img as unknown as Blob));
  return form;
}

// ---------- endpoints ----------
export const api = {
  // auth
  login: (email: string, password: string) =>
    send<{ user: User; token: string }>('POST', '/auth/login', { email, password }),
  register: (data: {
    name: string;
    email: string;
    phone?: string;
    password: string;
    password_confirmation: string;
  }) => send<{ user: User; token: string }>('POST', '/auth/register', data),
  logout: () => send<{ message: string }>('POST', '/auth/logout'),
  me: () => get<{ data: User }>('/me').then((r) => r.data),
  updateProfile: (data: { name?: string; username?: string | null; phone?: string | null }) =>
    send<{ data: User }>('PUT', '/me', data).then((r) => r.data),
  updatePhoto: (photo: LocalImage) => {
    const form = new FormData();
    form.append('photo', photo as unknown as Blob);
    return request<{ data: User }>('POST', '/me/photo', { form }).then((r) => r.data);
  },
  changePassword: (data: { current_password: string; password: string; password_confirmation: string }) =>
    send<{ message: string }>('PUT', '/me/password', data),

  // lookups
  cities: () => get<{ data: (Lookup & { state_id: number })[] }>('/cities').then((r) => r.data),
  propertyTypes: () => get<{ data: Lookup[] }>('/property-types').then((r) => r.data),

  // properties
  properties: (page: number, filters: PropertyFilters = {}) =>
    get<Paginated<Property>>('/properties', { page, per_page: 10, ...filters }),
  myProperties: (page: number) => get<Paginated<Property>>('/my/properties', { page, per_page: 10 }),
  property: (id: number) => get<{ data: Property }>(`/properties/${id}`).then((r) => r.data),
  createProperty: (input: PropertyInput, images: LocalImage[]) =>
    request<{ data: Property }>('POST', '/properties', { form: propertyForm(input, images) }).then((r) => r.data),
  updateProperty: (id: number, input: Partial<PropertyInput>, images: LocalImage[]) =>
    request<{ data: Property }>('POST', `/properties/${id}`, { form: propertyForm(input, images, 'PATCH') }).then(
      (r) => r.data,
    ),
  deleteProperty: (id: number) => send<void>('DELETE', `/properties/${id}`),

  // interactions
  comments: (propertyId: number) =>
    get<Paginated<Comment>>(`/properties/${propertyId}/comments`).then((r) => r.data),
  addComment: (propertyId: number, description: string, anonymous: boolean) =>
    send<{ data: Comment }>('POST', `/properties/${propertyId}/comments`, { description, anonymous }),
  deleteComment: (id: number) => send<void>('DELETE', `/comments/${id}`),
  rate: (propertyId: number, rating: number) =>
    send<{ rating: number; rating_avg: number; rating_count: number }>('PUT', `/properties/${propertyId}/rating`, {
      rating,
    }),
  favorites: (page: number) => get<Paginated<Property>>('/favorites', { page, per_page: 10 }),
  addFavorite: (id: number) => send<{ favorite: boolean }>('PUT', `/properties/${id}/favorite`),
  removeFavorite: (id: number) => send<void>('DELETE', `/properties/${id}/favorite`),

  // bookings
  bookings: (page: number) => get<Paginated<Booking>>('/bookings', { page, per_page: 15 }),
  book: (propertyId: number, date: string, description?: string) =>
    send<{ data: Booking }>('POST', `/properties/${propertyId}/bookings`, { date, description }),
  decideBooking: (id: number, status: 'Approved' | 'Rejected') =>
    send<{ data: Booking }>('PATCH', `/bookings/${id}`, { status }),
  requestDealer: () => send<{ id: number }>('POST', '/trader-requests'),

  // push notifications
  registerDevice: (token: string, platform: 'ios' | 'android' | 'web') =>
    send<{ id: number }>('POST', '/devices', { token, platform }),
  unregisterDevice: (token: string) => send<void>('DELETE', '/devices', { token }),
};
