<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrintControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_printers_returns_an_array(): void
    {
        $user = User::factory()->create();

        $res = $this->actingAs($user)->getJson('/print/printers');

        $res->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonStructure(['success', 'printers']);

        $this->assertIsArray($res->json('printers'));
    }

    public function test_test_print_returns_well_formed_json(): void
    {
        // Whether a physical printer exists is environment-dependent (none on CI),
        // so we only assert the endpoint responds with a well-formed JSON envelope
        // and never an unhandled 500 / HTML error page.
        $user = User::factory()->create();

        $res = $this->actingAs($user)->getJson('/print/test');

        $this->assertContains($res->status(), [200, 500]);
        $res->assertJsonStructure(['success', 'message']);
        $this->assertIsBool($res->json('success'));
    }
}
