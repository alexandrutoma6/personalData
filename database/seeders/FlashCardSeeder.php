<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FlashCard;
use Faker\Factory as Faker;

class FlashCradSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        $faker = Faker::create();

        for ($i = 1; $i <= 100; $i++) {
            FlashCard::create([
                'title' => $faker->sentence,
                'question' => $faker->sentence,
                'answer' => $faker->sentence,
                'category' => $faker->word,
                'status' => 'unlearned'
            ]);
        }
    }
}

        // \App\Models\FlashCard::create([
        //     'title' => 'Sample Flashcard 1',
        //     'question' => 'What is the capital of Romania?',
        //     'answer' => 'Bucharest',
        //     'category' => 'Geography',
        //     'status' => 'unlearned'
        // ]);

        // \App\Models\FlashCard::create([
        //     'title' => 'Sample Flashcard 2',
        //     'question' => 'What is the chemical symbol for water?',
        //     'answer' => 'H2O',
        //     'category' => 'Science',
        //     'status' => 'unlearned'
        // ]);
