<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Enquiry;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_public_storefront_pages_load(): void
    {
        $this->get('/')->assertOk();
        $this->get('/shop')->assertOk();
        $this->get('/shop?category=sarees&sort=price_low&sale=1')->assertOk();
        $this->get('/about')->assertOk();
        $this->get('/contact')->assertOk();
        $this->get('/wishlist')->assertOk();

        $this->get(route('product.show', Product::first()))->assertOk();
    }

    public function test_whatsapp_enquiry_is_logged_and_redirects(): void
    {
        $product = Product::first();
        $before = Enquiry::count();

        $this->get(route('product.enquire', ['product' => $product, 'color' => 'Maroon', 'qty' => 2]))
            ->assertRedirectContains('wa.me');

        $this->assertSame($before + 1, Enquiry::count());
    }

    public function test_review_submission_is_pending_approval(): void
    {
        $product = Product::first();

        $this->post(route('product.review', $product), [
            'author_name' => 'Test Buyer',
            'rating' => 5,
            'body' => 'Lovely product, highly recommend!',
        ])->assertRedirect();

        $this->assertTrue(
            Review::where('author_name', 'Test Buyer')->where('is_approved', false)->exists()
        );
    }

    public function test_admin_panel_requires_admin(): void
    {
        $this->get('/admin')->assertRedirect('/login');

        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    public function test_all_admin_pages_load(): void
    {
        $admin = User::where('is_admin', true)->first();
        $this->actingAs($admin);

        $routes = [
            route('admin.dashboard'),
            route('admin.products.index'),
            route('admin.products.create'),
            route('admin.products.edit', Product::first()),
            route('admin.categories.index'),
            route('admin.categories.create'),
            route('admin.categories.edit', Category::first()),
            route('admin.promotions.index'),
            route('admin.promotions.create'),
            route('admin.promotions.edit', Promotion::first()),
            route('admin.enquiries.index'),
            route('admin.reviews.index'),
            route('admin.settings.edit'),
        ];

        foreach ($routes as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_admin_can_create_product_with_variants_and_images(): void
    {
        $admin = User::where('is_admin', true)->first();

        $this->actingAs($admin)->post(route('admin.products.store'), [
            'name' => 'Test Saree',
            'price' => 1999,
            'sale_price' => 1499,
            'stock_status' => 'in_stock',
            'sizes' => 'S, M, L',
            'colors' => 'Red, Blue',
            'image_urls' => "https://example.com/a.jpg\nhttps://example.com/b.jpg",
            'is_active' => '1',
        ])->assertRedirect(route('admin.products.index'));

        $product = Product::where('name', 'Test Saree')->first();
        $this->assertNotNull($product);
        $this->assertSame(['S', 'M', 'L'], $product->sizes);
        $this->assertSame(2, $product->images()->count());
    }

    public function test_admin_can_save_settings(): void
    {
        $admin = User::where('is_admin', true)->first();

        $this->actingAs($admin)->put(route('admin.settings.update'), [
            'site_name' => 'Navanari Boutique',
            'show_prices' => '0',
            'currency_symbol' => '₹',
        ])->assertRedirect();

        $this->assertSame('Navanari Boutique', setting('site_name'));
    }
}
