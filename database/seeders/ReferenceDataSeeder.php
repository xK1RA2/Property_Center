<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\PropertyType;
use App\Models\State;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Data the application cannot run without. Safe to run repeatedly, and the
 * only seeder you should run in production:
 *
 *   php artisan db:seed --class=ReferenceDataSeeder --force
 *
 * Role ids matter: 1 = user, 2 = dealer, 3 = admin (see App\Models\User::ROLE_*).
 * Comment type ids matter too: 1 = normal, 2 = anonymous.
 */
class ReferenceDataSeeder extends Seeder
{
    public function run(): void
    {
        // The code compares against these ids (User::ROLE_*, comment_type_id 1/2), so they are
        // written explicitly: auto-increment can skip numbers (MySQL does after any rollback).
        DB::table('roles')->upsert([
            ['id' => 1, 'name' => 'user'],
            ['id' => 2, 'name' => 'dealer'],
            ['id' => 3, 'name' => 'admin'],
        ], ['id'], ['name']);

        DB::table('comment_types')->upsert([
            ['id' => 1, 'name' => 'Normal'],
            ['id' => 2, 'name' => 'Ananyomus'],
        ], ['id'], ['name']);

        foreach (['Office', 'Apartment', 'Villa', 'House'] as $name) {
            PropertyType::firstOrCreate(['name' => $name]);
        }

        foreach ($this->cities() as $stateName => $cityNames) {
            $state = State::firstOrCreate(['name' => $stateName]);

            foreach ($cityNames as $cityName) {
                City::firstOrCreate(['name' => $cityName, 'state_id' => $state->id]);
            }
        }
    }

    /** @return array<string, string[]> */
    private function cities(): array
    {
        return [
            'Amman' => ['Abdoun', 'Jabal Al-Weibdeh', 'Jabal Amman', 'Shmeisani', 'Sweifieh', 'Raghadan', 'Tla’a Al-Ali', 'Al-Madina Al-Monawara', 'Bayader Wadi Seer', 'Deir Ghbar'],
            'Irbid' => ['Al-Rasheed', 'Al-Saray', 'Al-Khwarizmi', 'Al-Hashemi', 'Karmeh', 'Al-Mazar', 'Al-Quds', 'Al-Tilal', 'Al-Bashaer', 'Al-Rawda'],
            'Zarqa' => ['Al-Hashimi', 'Al-Musharraf', 'Al-Jandoubi', 'Al-Tawfiq', 'Al-Mahatta', 'Hawzat Al-Zarqa', 'Al-Muqabla', 'Al-Faisal', 'Al-Kindah', 'Al-Madina'],
            'Aqaba' => ['Al-Muhtadi', 'Al-Bayader', 'Al-Kahtaniya', 'Al-Mansheya', 'Al-Shat', 'Al-Ahly', 'Al-Reda', 'Al-Jandala', 'Al-Hamra', 'Al-Mansour'],
            'Mafraq' => ['Al-Dakhiliya', 'Al-Salam', 'Al-Sokhna', 'Al-Manshieh', 'Al-Taybah', 'Al-Khobar', 'Al-Sabha', 'Al-Fayha', 'Al-Huda', 'Al-Jouf'],
            'Balqa' => ['Salt', 'Dibeen', 'Al-Fuhais', 'Al-Karak', 'Al-Shunneh', 'Wadi Al-Seer', 'Ain Al-Basha', 'Al-Mujib', 'Al-Khibet', 'Al-Balqa'],
            'Madaba' => ['Madaba', 'Al-Karak', 'Al-Mahaier', 'Husseiniya', 'Al-Montazah', 'Al-Fahd', 'Al-Samara', 'Al-Zai', 'Al-Khalidiyah', 'Al-Bayda'],
            'Karak' => ['Karak', 'Tafila', 'Al-Salt', 'Al-Qatar', 'Al-Muthanna', 'Ma’an', 'Al-Buwayda', 'Al-Sayf', 'Al-Jabal', 'Al-Safa'],
            'Jerash' => ['Jerash', 'Al-Azraq', 'Al-Hayyaj', 'Al-Munif', 'Al-Qasr', 'Al-Qitar', 'Al-Ramtha', 'Al-Jadida', 'Al-Mahdah', 'Al-Der'],
            'Ma’an' => ['Ma’an', 'Al-Jawf', 'Al-Kheir', 'Al-Ghabariyah', 'Al-Naqah', 'Al-Madbah', 'Al-Haish', 'Al-Mujahideen', 'Al-Wadi', 'Al-Sahab'],
        ];
    }
}
