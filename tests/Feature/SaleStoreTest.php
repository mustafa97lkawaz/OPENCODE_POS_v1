<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Products;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaleStoreTest extends TestCase
{
    use RefreshDatabase;

    /** Mirror the real POS frontend, which sends X-Requested-With (controller uses $request->ajax()). */
    private array $ajax = ['X-Requested-With' => 'XMLHttpRequest'];

    private function makeProduct(int $stock = 10, float $price = 5.00): Products
    {
        $cat = Category::create(['Category_name' => 'Test Cat', 'Created_by' => 'tester']);

        return Products::create([
            'Product_name' => 'Test Product',
            'category_id'  => $cat->id,
            'sku'          => 'SKU-' . uniqid(),
            'sell_price'   => $price,
            'stock_qty'    => $stock,
            'Status'       => 'مفعل',
            'Created_by'   => 'tester',
        ]);
    }

    public function test_valid_sale_decrements_stock_and_writes_rows(): void
    {
        $user = User::factory()->create();
        $p    = $this->makeProduct(stock: 10, price: 5.00);

        $items = [['product_id' => $p->id, 'name' => $p->Product_name, 'price' => 5.00, 'stock' => 10, 'qty' => 3]];

        $res = $this->actingAs($user)->postJson('/sales', [
            'payment_method' => 'cash',
            'items_json'     => json_encode($items),
            'subtotal'       => 15.00,
            'tax_amount'     => 0,
            'discount'       => 0,
            'total'          => 15.00,
            'cash_amount'    => 15.00,
            'card_amount'    => 0,
        ], $this->ajax);

        $res->assertOk()->assertJson(['success' => true]);
        $this->assertEquals(7, $p->fresh()->stock_qty);          // 10 - 3
        $this->assertCount(1, Sale::all());
        $this->assertCount(1, SaleItem::all());
        // server-side price is used, not client-posted
        $this->assertEquals(15.00, SaleItem::first()->total);
    }

    public function test_oversell_is_rejected_and_nothing_is_written(): void
    {
        $user = User::factory()->create();
        $p    = $this->makeProduct(stock: 2, price: 5.00);

        $items = [['product_id' => $p->id, 'name' => $p->Product_name, 'price' => 5.00, 'stock' => 99, 'qty' => 5]];

        $res = $this->actingAs($user)->postJson('/sales', [
            'payment_method' => 'cash',
            'items_json'     => json_encode($items),
            'subtotal'       => 25.00,
            'tax_amount'     => 0,
            'discount'       => 0,
            'total'          => 25.00,
            'cash_amount'    => 25.00,
            'card_amount'    => 0,
        ], $this->ajax);

        $res->assertStatus(422)->assertJson(['success' => false]);
        $this->assertEquals(2, $p->fresh()->stock_qty);   // unchanged
        $this->assertCount(0, Sale::all());
        $this->assertCount(0, SaleItem::all());
    }

    public function test_missing_payment_method_fails_validation(): void
    {
        $user = User::factory()->create();
        $p    = $this->makeProduct();

        $res = $this->actingAs($user)->postJson('/sales', [
            'items_json' => json_encode([['product_id' => $p->id, 'qty' => 1]]),
        ], $this->ajax);

        $res->assertStatus(422); // StoreSaleRequest rejects (payment_method required)
        $this->assertCount(0, Sale::all());
    }

    public function test_second_sale_cannot_oversell_remaining_stock(): void
    {
        // Sequential proxy for the concurrency case: true parallel txns can't be
        // simulated on :memory: SQLite, but server-side re-validation against
        // *current* stock (not client value) is the property that prevents oversell.
        $user = User::factory()->create();
        $p    = $this->makeProduct(stock: 4, price: 2.00);

        $mk = fn ($qty) => $this->actingAs($user)->postJson('/sales', [
            'payment_method' => 'cash',
            'items_json'     => json_encode([['product_id' => $p->id, 'name' => 'x', 'price' => 2.00, 'stock' => 999, 'qty' => $qty]]),
            'subtotal'       => 2.00 * $qty,
            'total'          => 2.00 * $qty,
            'cash_amount'    => 2.00 * $qty,
        ], $this->ajax);

        $mk(3)->assertOk();                        // stock 4 -> 1
        $mk(3)->assertStatus(422);                 // only 1 left -> rejected
        $this->assertEquals(1, $p->fresh()->stock_qty);
        $this->assertCount(1, Sale::all());
    }
}
