<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_products(): void
    {
        $this->getJson('/api/products')->assertStatus(401);
    }

    public function test_authenticated_user_can_list_products(): void
    {
        Sanctum::actingAs(User::factory()->create());

        Product::create([
            'sku' => 'BED-001',
            'name' => 'Wooden Bed Frame',
            'price' => 250.00,
            'stock_quantity' => 10,
        ]);

        $this->getJson('/api/products')
            ->assertStatus(200)
            ->assertJsonCount(1);
    }

    public function test_creating_an_order_calculates_the_total_and_links_items(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $customer = Customer::create([
            'name' => 'Ahsan Hafeez',
            'email' => 'ahsan@example.com',
        ]);

        $product = Product::create([
            'sku' => 'BED-001',
            'name' => 'Wooden Bed Frame',
            'price' => 250.00,
            'stock_quantity' => 10,
        ]);

        $response = $this->postJson('/api/orders', [
            'customer_id' => $customer->id,
            'items' => [
                ['product_id' => $product->id, 'quantity' => 3],
            ],
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('total_amount', 750)
            ->assertJsonCount(1, 'items');

        $this->assertDatabaseHas('orders', [
            'customer_id' => $customer->id,
            'total_amount' => 750,
        ]);
    }

    public function test_creating_an_order_without_items_fails_validation(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $customer = Customer::create(['name' => 'Ahsan Hafeez', 'email' => 'ahsan2@example.com']);

        $this->postJson('/api/orders', [
            'customer_id' => $customer->id,
            'items' => [],
        ])->assertStatus(422);
    }
}
