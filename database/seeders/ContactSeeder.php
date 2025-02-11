<?php

namespace Database\Seeders;

use App\Models\Contact;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;


class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $data = [
            ['name' => $faker->name, 'email' => $faker->unique()->safeEmail, 'phone' => $faker->phoneNumber, 'message' => $faker->sentence],
            ['name' => $faker->name, 'email' => $faker->unique()->safeEmail, 'phone' => $faker->phoneNumber, 'message' => $faker->sentence],
        ];

        foreach ($data as $item) {
            Contact::create($item);
        }
    }
}
