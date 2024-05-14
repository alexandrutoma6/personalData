<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FlashCard;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        
        \App\Models\CodeChallenge::create([
            'title' => 'Check if 786598 is a prime number, print a boolean',
            'answer' => 'false',
            'status' => 'active'
        ]);

        \App\Models\CodeChallenge::create([
            'title' => 'Print the first 10 prime numbers, separated by a comma',    
            'answer' => '2, 3, 5, 7, 11, 13, 17, 19, 23, 29',
            'status' => 'active'
        ]);
        \App\Models\CodeChallenge::create([
            'title' => 'Print the smallest multiple of 31 and 55',    
            'answer' => '1695',
            'status' => 'active'
        ]);

        \App\Models\CodeChallenge::create([
            'title' => 'Check if 699 is divisibile by 3, print a boolean',    
            'answer' => 'true',
            'status' => 'active'
        ]);
    }
}
