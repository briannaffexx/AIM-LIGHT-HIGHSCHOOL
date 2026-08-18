<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MealSchedulesSeeder extends Seeder
{
    public function run(): void
    {
        $activeTerm = DB::table('terms')->where('is_active', true)->first();
        if (! $activeTerm) {
            return;
        }

        $meals = [
            ['day' => 1, 'type' => 'Breakfast', 'items' => ['Tea with Bread', 'Porridge', 'Mandazi']],
            ['day' => 1, 'type' => 'Lunch', 'items' => ['Rice and Beans', 'Ugali and Sukuma', 'Chapati and Stew']],
            ['day' => 1, 'type' => 'Dinner', 'items' => ['Ugali and Vegetables', 'Rice and Peas', 'Spaghetti']],
            ['day' => 2, 'type' => 'Breakfast', 'items' => ['Milk with Cereal', 'Chapati with Tea', 'Boiled Eggs']],
            ['day' => 2, 'type' => 'Lunch', 'items' => ['Pilau and Beef', 'Ugali and Fish', 'Rice and Chicken']],
            ['day' => 2, 'type' => 'Dinner', 'items' => ['Ugali and Beef', 'Rice and Beans', 'Potatoes and Vegetables']],
            ['day' => 3, 'type' => 'Breakfast', 'items' => ['Tea with Pancakes', 'Porridge with Nuts', 'Fresh Fruit']],
            ['day' => 3, 'type' => 'Lunch', 'items' => ['Rice and Kuku', 'Ugali and Mbuzi', 'Chapati and Beans']],
            ['day' => 3, 'type' => 'Dinner', 'items' => ['Githeri', 'Rice and Vegetables', 'Ugali and Fish']],
            ['day' => 4, 'type' => 'Breakfast', 'items' => ['Tea with Bread', 'Milk with Oats', 'Fresh Fruit']],
            ['day' => 4, 'type' => 'Lunch', 'items' => ['Rice and Beef', 'Ugali and Sukuma', 'Chapati and Stew']],
            ['day' => 4, 'type' => 'Dinner', 'items' => ['Ugali and Beans', 'Rice and Peas', 'Spaghetti']],
            ['day' => 5, 'type' => 'Breakfast', 'items' => ['Tea with Chapati', 'Porridge', 'Boiled Eggs']],
            ['day' => 5, 'type' => 'Lunch', 'items' => ['Pilau and Chicken', 'Ugali and Fish', 'Rice and Vegetables']],
            ['day' => 5, 'type' => 'Dinner', 'items' => ['Ugali and Beef', 'Rice and Beans', 'Potatoes and Sukuma']],
            ['day' => 6, 'type' => 'Breakfast', 'items' => ['Milk with Cereal', 'Tea with Bread', 'Fresh Fruit']],
            ['day' => 6, 'type' => 'Lunch', 'items' => ['Rice and Beans', 'Ugali and Meat', 'Chapati and Vegetables']],
            ['day' => 6, 'type' => 'Dinner', 'items' => ['Ugali and Fish', 'Rice and Peas', 'Spaghetti']],
            ['day' => 7, 'type' => 'Breakfast', 'items' => ['Tea with Mandazi', 'Porridge', 'Boiled Eggs']],
            ['day' => 7, 'type' => 'Lunch', 'items' => ['Rice and Beef', 'Ugali and Sukuma', 'Chapati and Stew']],
            ['day' => 7, 'type' => 'Dinner', 'items' => ['Ugali and Beans', 'Rice and Vegetables', 'Potatoes and Meat']],
        ];

        $schedule = [];
        $times    = ['breakfast' => '06:30:00', 'lunch' => '12:30:00', 'dinner' => '18:30:00'];

        foreach ($meals as $meal) {
            $schedule[] = [
                'term_id'     => $activeTerm->id,
                'day_of_week' => $meal['day'],
                'meal_type'   => $meal['type'],
                'menu_item'   => $meal['items'][array_rand($meal['items'])],
                'time'        => $times[strtolower($meal['type'])],
                'created_at'  => now(),
                'updated_at'  => now(),
            ];
        }

        DB::table('meal_schedules')->insert($schedule);
    }
}