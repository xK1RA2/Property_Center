export type Role = 1 | 2 | 3; // 1 user, 2 dealer, 3 admin

export interface User {
  id: number;
  name: string;
  username: string | null;
  email?: string;
  phone: string | null;
  profile_image: string | null;
  role_id: Role;
  role?: string;
}

export interface PropertyFeatures {
  Bedrooms: number;
  Bathrooms: number;
  Kitchen: number;
  Rooms: number;
  Area: number;
  Heating: number | boolean;
  Air_Conditioner: number | boolean;
  Parking: number | boolean;
}

export interface Property {
  id: number;
  purchase_type: 'Sell' | 'Rent';
  price: number;
  year: number;
  address: string;
  description: string | null;
  status: 'Available' | 'Not Available';
  published_at: string | null;
  city?: { id: number; name: string };
  type?: { id: number; name: string };
  dealer?: User;
  features?: PropertyFeatures;
  images?: { id: number; url: string; position: number }[];
  primary_image?: string | null;
  location?: { name: string; lat: number; lng: number } | null;
  rating_avg?: number | null;
  rating_count?: number;
  is_favorite?: boolean;
  my_rating?: number | null;
}

export interface Booking {
  id: number;
  property_id: number;
  user_id: number;
  dealer_id: number;
  date: string;
  description: string;
  status: 'Pending' | 'Approved' | 'Rejected';
  property?: { id: number; address: string; price: number } | null;
  user?: User;
  created_at: string;
}

export interface Comment {
  id: number;
  property_id: number;
  description: string;
  anonymous: boolean;
  author: User | null;
  is_mine: boolean;
  created_at: string;
}

export interface Paginated<T> {
  data: T[];
  meta: { current_page: number; last_page: number; total: number };
}

export interface Lookup {
  id: number;
  name: string;
}

export interface PropertyFilters {
  search?: string;
  purchase_type?: 'Sell' | 'Rent';
  city_id?: number;
  property_type_id?: number;
  min_price?: number;
  max_price?: number;
  sort?: 'newest' | 'price_asc' | 'price_desc';
}
