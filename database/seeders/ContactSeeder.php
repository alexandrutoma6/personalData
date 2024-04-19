<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Contact;
use Faker\Factory as Faker;

class ContactSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Define categories for contacts
        $categories = ['work', 'school', 'family', 'friends'];

        // Generate 50 contacts with random data
        for ($i = 0; $i < 50; $i++) {
            $contact = new Contact([
                'owner_user_id' => 2,
                'name' => $faker->name,
                'phone_number' => $this->generateRandomPhoneNumber(),
                'gender' => $this->generateRandomGender(),
                'city' => $faker->city,
                'email' => $this->generateUniqueRandomEmail(),
                'category' => $categories[array_rand($categories)],
            ]);

            $contact->save();
        }
    }

    private function generateRandomPhoneNumber()
    {
        // Generate a random 10-digit phone number
        return '555' . mt_rand(1000000, 9999999);
    }

    private function generateRandomGender()
    {
        // Generate a random gender (male or female)
        $genders = ['male', 'female'];
        return $genders[array_rand($genders)];
    }

    private function generateUniqueRandomEmail()
    {
        // Generate a unique email address using Laravel factory
        return \Illuminate\Support\Str::random(10) . '@example.com';
    }
}
