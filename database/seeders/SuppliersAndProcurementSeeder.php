<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SuppliersAndProcurementSeeder extends Seeder
{
    public function run(): void
    {
        // Suppliers
        $suppliers = [
            ['name' => 'Kenya Stationers Ltd', 'contact_person' => 'John Mwangi', 'phone' => '0712345671', 'email' => 'info@kenyastationers.co.ke', 'address' => 'Nairobi, Kenya', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Educational Books Ltd', 'contact_person' => 'Mary Wanjiru', 'phone' => '0723456722', 'email' => 'sales@educationalbooks.co.ke', 'address' => 'Mombasa, Kenya', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Catering Supplies Co.', 'contact_person' => 'Peter Kiprop', 'phone' => '0734567833', 'email' => 'info@cateringsupplies.co.ke', 'address' => 'Kisumu, Kenya', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Furniture World', 'contact_person' => 'Grace Akinyi', 'phone' => '0745678944', 'email' => 'info@furnitureworld.co.ke', 'address' => 'Nakuru, Kenya', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ICT Solutions Kenya', 'contact_person' => 'David Ochieng', 'phone' => '0756789055', 'email' => 'sales@ictsolutions.co.ke', 'address' => 'Eldoret, Kenya', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Building Materials Ltd', 'contact_person' => 'Sarah Njeri', 'phone' => '0767890166', 'email' => 'info@buildingmaterials.co.ke', 'address' => 'Thika, Kenya', 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('suppliers')->insert($suppliers);

        $users = DB::table('users')->pluck('id')->toArray();
        $supplierIds = DB::table('suppliers')->pluck('id')->toArray();

        // Purchase Requests
        $items = ['Chairs', 'Tables', 'Books', 'Stationery', 'Computers', 'Projectors', 'Lab Equipment',
                  'Sports Equipment', 'Mattresses', 'Blankets', 'Kitchen Equipment', 'Cleaning Supplies'];
        $requests = [];
        for ($i = 1; $i <= 15; $i++) {
            $statuses = ['pending', 'approved', 'rejected', 'ordered'];
            $status = $statuses[array_rand($statuses)];

            $requests[] = [
                'requested_by' => $users[array_rand($users)],
                'item_name' => $items[array_rand($items)] . ' - ' . chr(65 + $i),
                'quantity' => rand(5, 50),
                'estimated_cost' => rand(1000, 50000),
                'request_date' => now()->subDays(rand(1, 30))->toDateString(),
                'approval_date' => $status !== 'pending' ? now()->subDays(rand(1, 15))->toDateString() : null,
                'status' => $status,
                'approved_by' => $status !== 'pending' ? $users[array_rand($users)] : null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('purchase_requests')->insert($requests);

        // Purchase Orders (for approved and ordered requests)
        $approvedRequests = DB::table('purchase_requests')
            ->whereIn('status', ['approved', 'ordered'])
            ->pluck('id')
            ->toArray();

        foreach ($approvedRequests as $requestId) {
            if (rand(0, 1) === 1) {
                DB::table('purchase_orders')->insert([
                    'purchase_request_id' => $requestId,
                    'supplier_id' => $supplierIds[array_rand($supplierIds)],
                    'order_number' => 'PO-' . date('Y') . '-' . strtoupper(uniqid()),
                    'order_date' => now()->subDays(rand(1, 15))->toDateString(),
                    'total_amount' => rand(5000, 60000),
                    'status' => ['ordered', 'received', 'pending'][array_rand(['ordered', 'received', 'pending'])],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
