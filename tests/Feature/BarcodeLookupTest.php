<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Products;
use App\Models\ProductVariation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BarcodeLookupTest extends TestCase
{
    use RefreshDatabase;

    private function makeProduct(string $barcode): Products
    {
        $cat = Category::create(['Category_name' => 'Cat', 'Created_by' => 'tester']);

        return Products::create([
            'Product_name' => 'Barcoded Product',
            'category_id'  => $cat->id,
            'sku'          => 'SKU-' . uniqid(),
            'barcode'      => $barcode,
            'sell_price'   => 9.99,
            'stock_qty'    => 5,
            'Status'       => 'مفعل',
            'Created_by'   => 'tester',
        ]);
    }

    public function test_existing_barcode_returns_product(): void
    {
        $user = User::factory()->create();
        $p    = $this->makeProduct('111222333');

        $this->actingAs($user)
            ->getJson('/pos/products/barcode/111222333')
            ->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonPath('product.id', $p->id)
            ->assertJsonPath('product.barcode', '111222333');
    }

    public function test_variation_barcode_returns_parent_product(): void
    {
        $user = User::factory()->create();
        $p    = $this->makeProduct('PARENT-1');

        ProductVariation::create([
            'product_id'      => $p->id,
            'variation_value' => 'Large',
            'extra_price'     => 2.00,
            'stock_qty'       => 3,
            'barcode'         => 'VAR-XL-9',
        ]);

        $this->actingAs($user)
            ->getJson('/pos/products/barcode/VAR-XL-9')
            ->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonPath('product.id', $p->id);
    }

    public function test_unknown_barcode_returns_failure(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->getJson('/pos/products/barcode/DOES-NOT-EXIST')
            ->assertOk()
            ->assertJson(['success' => false]);
    }
}
