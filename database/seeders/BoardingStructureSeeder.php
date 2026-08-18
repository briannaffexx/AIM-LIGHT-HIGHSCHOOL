<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BoardingStructureSeeder extends Seeder
{
    public function run(): void
    {
        // Houses
        $houses = [
            ['name' => 'Kenyatta House', 'description' => 'Senior boys house', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kenyatta House - Girls', 'description' => 'Senior girls house', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Moi House', 'description' => 'Junior boys house', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Moi House - Girls', 'description' => 'Junior girls house', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Unity House', 'description' => 'Mixed house for special needs', 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('houses')->insert($houses);

        // Dormitories for each house
        $dormitories = [];
        $dormNames = ['Dorm A', 'Dorm B', 'Dorm C'];
        foreach (DB::table('houses')->pluck('id') as $houseId) {
            foreach ($dormNames as $name) {
                $dormitories[] = [
                    'house_id' => $houseId,
                    'name' => $name,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        DB::table('dormitories')->insert($dormitories);

        // Rooms for each dormitory
        $rooms = [];
        $roomCapacities = [4, 6, 8];
        foreach (DB::table('dormitories')->pluck('id') as $dormId) {
            for ($i = 1; $i <= 4; $i++) {
                $rooms[] = [
                    'dormitory_id' => $dormId,
                    'name' => 'Room ' . $i,
                    'capacity' => $roomCapacities[array_rand($roomCapacities)],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        DB::table('rooms')->insert($rooms);

        // Beds for each room
        $beds = [];
        $bedStatuses = ['available', 'occupied', 'maintenance'];
        foreach (DB::table('rooms')->pluck('id') as $roomId) {
            $room = DB::table('rooms')->find($roomId);
            for ($i = 1; $i <= $room->capacity; $i++) {
                $beds[] = [
                    'room_id' => $roomId,
                    'bed_number' => 'BED-' . $roomId . '-' . $i,
                    'status' => $bedStatuses[array_rand($bedStatuses)],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        DB::table('beds')->insert($beds);

        // Boarding Resources
        $resources = [
            ['name' => 'Mattress - Single', 'category' => 'Furniture', 'status' => 'available', 'notes' => 'New stock', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Blanket - Heavy', 'category' => 'Bedding', 'status' => 'available', 'notes' => 'Winter collection', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Lockers - 4-door', 'category' => 'Furniture', 'status' => 'available', 'notes' => 'For seniors', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Dining Tables', 'category' => 'Furniture', 'status' => 'available', 'notes' => 'Main dining hall', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Dining Chairs', 'category' => 'Furniture', 'status' => 'available', 'notes' => 'Set of 20', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kitchen Stove - Industrial', 'category' => 'Kitchen', 'status' => 'available', 'notes' => '6 burner', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Fridge - Large', 'category' => 'Kitchen', 'status' => 'available', 'notes' => '500L capacity', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cleaning Supplies Kit', 'category' => 'Cleaning', 'status' => 'available', 'notes' => 'Mops, brooms, buckets', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Water Dispenser', 'category' => 'Utility', 'status' => 'maintenance', 'notes' => 'Needs repair', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'First Aid Kit', 'category' => 'Medical', 'status' => 'available', 'notes' => 'For boarding house', 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('boarding_resources')->insert($resources);
    }
}
