<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\City;
use App\Models\State;
use App\Models\Role;
use App\Models\Rate;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\PropertyImages;
use App\Models\PropertyFeatures;
use App\Models\Order;
use App\Models\Comment;
use App\Models\CommentType;

use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class DatabaseSeeder extends Seeder
{
   
    public function run(): void
    {
        
   
    

        $states = [
            'Amman' => [
                'Abdoun', 'Jabal Al-Weibdeh', 'Jabal Amman', 'Shmeisani', 'Sweifieh', 
                'Raghadan', 'Tla’a Al-Ali', 'Al-Madina Al-Monawara', 'Bayader Wadi Seer', 'Deir Ghbar'
            ],
            'Irbid' => [
                'Al-Rasheed', 'Al-Saray', 'Al-Khwarizmi', 'Al-Hashemi', 'Karmeh', 
                'Al-Mazar', 'Al-Quds', 'Al-Tilal', 'Al-Bashaer', 'Al-Rawda'
            ],
            'Zarqa' => [
                'Al-Hashimi', 'Al-Musharraf', 'Al-Jandoubi', 'Al-Tawfiq', 'Al-Mahatta', 
                'Hawzat Al-Zarqa', 'Al-Muqabla', 'Al-Faisal', 'Al-Kindah', 'Al-Madina'
            ],
            'Aqaba' => [
                'Al-Muhtadi', 'Al-Bayader', 'Al-Kahtaniya', 'Al-Mansheya', 'Al-Shat', 
                'Al-Ahly', 'Al-Reda', 'Al-Jandala', 'Al-Hamra', 'Al-Mansour'
            ],
            'Mafraq' => [
                'Al-Dakhiliya', 'Al-Salam', 'Al-Sokhna', 'Al-Manshieh', 'Al-Taybah', 
                'Al-Khobar', 'Al-Sabha', 'Al-Fayha', 'Al-Huda', 'Al-Jouf'
            ],
            'Balqa' => [
                'Salt', 'Dibeen', 'Al-Fuhais', 'Al-Karak', 'Al-Shunneh', 
                'Wadi Al-Seer', 'Ain Al-Basha', 'Al-Mujib', 'Al-Khibet', 'Al-Balqa'
            ],
            'Madaba' => [
                'Madaba', 'Al-Karak', 'Al-Mahaier', 'Husseiniya', 'Al-Montazah', 
                'Al-Fahd', 'Al-Samara', 'Al-Zai', 'Al-Khalidiyah', 'Al-Bayda'
            ],
            'Karak' => [
                'Karak', 'Tafila', 'Al-Salt', 'Al-Qatar', 'Al-Muthanna', 
                'Ma’an', 'Al-Buwayda', 'Al-Sayf', 'Al-Jabal', 'Al-Safa'
            ],
            'Jerash' => [
                'Jerash', 'Al-Azraq', 'Al-Hayyaj', 'Al-Munif', 'Al-Qasr', 
                'Al-Qitar', 'Al-Ramtha', 'Al-Jadida', 'Al-Mahdah', 'Al-Der'
            ],
            'Ma’an' => [
                'Ma’an', 'Al-Jawf', 'Al-Kheir', 'Al-Ghabariyah', 'Al-Naqah', 
                'Al-Madbah', 'Al-Haish', 'Al-Mujahideen', 'Al-Wadi', 'Al-Sahab'
            ]
        ];

        foreach ($states as $state => $cities) {
            $stateModel = State::create(['name' => $state]);
        
            foreach ($cities as $city) {
                $stateModel->cities()->create(['name' => $city]);
            }
        }


        $userRole = Role::firstOrCreate(['name' => 'user']);
        $dealerRole = Role::firstOrCreate(['name' => 'dealer']);

        $commenttype= CommentType::firstOrCreate(['name'=>'Normal']);
        $commenttype= CommentType::firstOrCreate(['name'=>'Ananyomus']);


        $propertyType=[
            'Ofiice' , 'Apartment' , 'Villa' , 'House'
        ];

        foreach($propertyType as $property){
            $propertymake=PropertyType::firstOrCreate(['name' => $property]);
        }

        

    //Create A 3 Users 
    User::factory()
    ->count(3)
    ->state(['role_id' =>$userRole->id ])
    ->create();
    
        
    User::factory()
    ->count(4)
    ->state(['role_id' => $dealerRole->id])
    ->has(Property::factory()
        ->count(10) 
        ->has(
            PropertyImages::factory()
           ->count(5)
           ->sequence(fn(Sequence $sequence)=>
           ['position'=>$sequence->index %5 +1]),
             'PropertyImages')
           ->hasFeatures()
           ->hasLocation()

        )
        ->create();

      
    

}

}
