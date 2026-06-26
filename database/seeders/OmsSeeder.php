<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Database\Seeder;

class OmsSeeder extends Seeder
{
    public function run(): void
    {
        $orderService = app(OrderService::class);
        if (Customer::query()->exists()) {
            $this->command?->warn('Database already has data. Skipping seed.');

            return;
        }

        $customers = collect([
            Customer::query()->create([
                'name' => 'Acme Corp',
                'email' => 'orders@acme.example',
                'phone' => '+1 555-0100',
                'address_line1' => '123 Industrial Blvd',
                'city' => 'San Francisco',
                'postal_code' => '94107',
                'country' => 'USA',
            ]),
            Customer::query()->create([
                'name' => 'Globex Ltd',
                'email' => 'procurement@globex.example',
                'phone' => '+44 20 7946 0958',
                'address_line1' => '42 Market Street',
                'city' => 'London',
                'postal_code' => 'EC1A 1BB',
                'country' => 'UK',
            ]),
            Customer::query()->create([
                'name' => 'Initech',
                'email' => 'buying@initech.example',
                'phone' => '+1 555-0199',
                'address_line1' => '789 Office Park',
                'city' => 'Austin',
                'postal_code' => '73301',
                'country' => 'USA',
            ]),
        ]);

        $products = collect([
            Product::query()->create(['sku' => 'SKU-001', 'name' => 'Wireless Mouse', 'description' => 'Ergonomic wireless mouse', 'price' => 29.99, 'stock_quantity' => 150]),
            Product::query()->create(['sku' => 'SKU-002', 'name' => 'Mechanical Keyboard', 'description' => 'RGB mechanical keyboard', 'price' => 89.99, 'stock_quantity' => 75]),
            Product::query()->create(['sku' => 'SKU-003', 'name' => 'USB-C Hub', 'description' => '7-in-1 USB-C hub', 'price' => 49.99, 'stock_quantity' => 200]),
            Product::query()->create(['sku' => 'SKU-004', 'name' => 'Monitor Stand', 'description' => 'Adjustable monitor stand', 'price' => 39.99, 'stock_quantity' => 8]),
            Product::query()->create(['sku' => 'SKU-005', 'name' => 'Webcam HD', 'description' => '1080p webcam with mic', 'price' => 59.99, 'stock_quantity' => 45]),
        ]);

        $order1 = $orderService->createOrder($customers[0]->id, 'Rush delivery requested');
        $orderService->addItem($order1, $products[0]->id, 10);
        $orderService->addItem($order1, $products[2]->id, 5);
        $orderService->setShippingAndTax($order1, '15.00', '12.50');
        $orderService->submitOrder($order1);
        $orderService->updateStatus($order1, OrderStatus::Confirmed);

        $order2 = $orderService->createOrder($customers[1]->id);
        $orderService->addItem($order2, $products[1]->id, 3);
        $orderService->addItem($order2, $products[4]->id, 2);
        $orderService->submitOrder($order2);

        $order3 = $orderService->createOrder($customers[2]->id, 'Draft order for review');
        $orderService->addItem($order3, $products[3]->id, 2);

        $this->command?->info('Seeded 3 customers, 5 products, and 3 sample orders.');
    }
}
