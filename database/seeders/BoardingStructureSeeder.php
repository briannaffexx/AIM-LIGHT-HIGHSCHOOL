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

        // Asset Inventory (uses correct columns from migration)
        $resources = [
            ['name' => 'Mattress - Single',        'category' => 'boarding',   'total_quantity' => 50, 'assigned_quantity' => 40, 'status' => 'good',             'condition_notes' => 'New stock',            'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Blanket - Heavy',           'category' => 'boarding',   'total_quantity' => 60, 'assigned_quantity' => 50, 'status' => 'good',             'condition_notes' => 'Winter collection',     'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Lockers - 4-door',          'category' => 'furniture',  'total_quantity' => 20, 'assigned_quantity' => 18, 'status' => 'good',             'condition_notes' => 'For seniors',           'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Dining Tables',             'category' => 'furniture',  'total_quantity' => 10, 'assigned_quantity' => 10, 'status' => 'good',             'condition_notes' => 'Main dining hall',      'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Dining Chairs',             'category' => 'furniture',  'total_quantity' => 80, 'assigned_quantity' => 80, 'status' => 'good',             'condition_notes' => 'Set of 80',             'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kitchen Stove - Industrial','category' => 'other',      'total_quantity' => 2,  'assigned_quantity' => 2,  'status' => 'good',             'condition_notes' => '6 burner',              'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Fridge - Large',            'category' => 'other',      'total_quantity' => 3,  'assigned_quantity' => 3,  'status' => 'good',             'condition_notes' => '500L capacity',         'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cleaning Supplies Kit',     'category' => 'other',      'total_quantity' => 10, 'assigned_quantity' => 8,  'status' => 'good',             'condition_notes' => 'Mops, brooms, buckets', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Water Dispenser',           'category' => 'other',      'total_quantity' => 4,  'assigned_quantity' => 3,  'status' => 'need_replacement', 'condition_notes' => 'Needs repair',          'created_at' => now(), 'updated_at' => now()],
            ['name' => 'First Aid Kit',             'category' => 'other',      'total_quantity' => 5,  'assigned_quantity' => 5,  'status' => 'good',             'condition_notes' => 'For boarding house',    'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Lab Microscopes',           'category' => 'lab',        'total_quantity' => 20, 'assigned_quantity' => 18, 'status' => 'good',             'condition_notes' => 'Biology lab',           'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Desktop Computers',         'category' => 'computers',  'total_quantity' => 30, 'assigned_quantity' => 28, 'status' => 'good',             'condition_notes' => 'ICT lab',               'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Football (Size 5)',         'category' => 'sports',     'total_quantity' => 10, 'assigned_quantity' => 8,  'status' => 'good',             'condition_notes' => 'PE department',         'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('inventory')->insert($resources);
    }
}
