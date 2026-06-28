<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Promotion;
use App\Models\Review;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedAdmin();
        $this->seedSettings();
        $this->seedPromotions();
        $categories = $this->seedCategories();
        $this->seedProducts($categories);
    }

    protected function seedAdmin(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@navanari.test'],
            [
                'name' => 'Navanari Admin',
                'password' => Hash::make('password'),
                'is_admin' => true,
            ],
        );
    }

    protected function seedSettings(): void
    {
        $defaults = [
            'site_name' => 'Navanari',
            'site_tagline' => 'Where Every Woman Shines',
            'site_description' => 'Navanari is your destination for elegant dresses, exquisite sarees, fine jewellery and curated essentials — handpicked for the modern woman.',
            'logo' => '',
            'currency_symbol' => '₹',
            'show_prices' => '1',
            'whatsapp_number' => env('WHATSAPP_NUMBER', '919999999999'),
            'contact_phone' => '+91 99999 99999',
            'contact_email' => 'hello@navanari.com',
            'contact_address' => 'Boutique No. 9, Fashion Street, Mumbai, India',
            'instagram_url' => 'https://instagram.com/',
            'facebook_url' => 'https://facebook.com/',
            'youtube_url' => '',
            'pinterest_url' => '',
            'primary_color' => '#be185d',
            'accent_color' => '#d4af37',
            'hero_heading' => 'Drape Yourself in Elegance',
            'hero_subheading' => 'Discover sarees, dresses & jewellery curated for the woman who owns every room she walks into.',
            'hero_cta_text' => 'Explore Collection',
            'hero_image' => 'https://images.unsplash.com/photo-1610030469983-98e550d6193c?q=80&w=1600&auto=format&fit=crop',
            'about_text' => 'Born from a love of timeless craftsmanship, Navanari brings together India\'s finest weaves, contemporary silhouettes and statement jewellery. Every piece is chosen to make you feel effortlessly beautiful.',
            'footer_note' => 'Handpicked elegance, delivered with love.',
            'shipping_note' => 'Free shipping across India on orders above ₹2999.',
            'announcement' => '✦ Monsoon Edit is live — up to 40% off on festive wear ✦',
        ];

        foreach ($defaults as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }

    protected function seedPromotions(): void
    {
        if (Promotion::count() > 0) {
            return;
        }

        Promotion::create([
            'title' => 'The Festive Saree Edit',
            'subtitle' => 'Luxe silks & handlooms for the celebration season',
            'image' => 'https://images.unsplash.com/photo-1583391733956-6c78276477e2?q=80&w=1600&auto=format&fit=crop',
            'link' => '/shop?category=sarees',
            'button_text' => 'Shop Sarees',
            'position' => 'hero',
            'sort_order' => 1,
        ]);

        Promotion::create([
            'title' => 'Jewellery That Speaks',
            'subtitle' => 'Statement pieces, everyday sparkle',
            'image' => 'https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?q=80&w=1600&auto=format&fit=crop',
            'link' => '/shop?category=jewellery',
            'button_text' => 'Discover',
            'position' => 'hero',
            'sort_order' => 2,
        ]);

        Promotion::create([
            'title' => 'New Season, New You',
            'subtitle' => 'Fresh arrivals every week',
            'image' => 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?q=80&w=1600&auto=format&fit=crop',
            'link' => '/shop',
            'button_text' => 'Shop New In',
            'position' => 'strip',
            'sort_order' => 1,
        ]);
    }

    protected function seedCategories(): array
    {
        $data = [
            ['name' => 'Sarees', 'key' => 'sarees', 'desc' => 'Handwoven silks, georgettes & timeless drapes.', 'img' => 'https://images.unsplash.com/photo-1610030469983-98e550d6193c?q=80&w=1200&auto=format&fit=crop', 'featured' => true],
            ['name' => 'Dresses', 'key' => 'dresses', 'desc' => 'Anarkalis, gowns, kurtis & western wear.', 'img' => 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?q=80&w=1200&auto=format&fit=crop', 'featured' => true],
            ['name' => 'Jewellery', 'key' => 'jewellery', 'desc' => 'Earrings, necklaces, bangles & bridal sets.', 'img' => 'https://images.unsplash.com/photo-1535632066927-ab7c9ab60908?q=80&w=1200&auto=format&fit=crop', 'featured' => true],
            ['name' => 'Footwear', 'key' => 'footwear', 'desc' => 'Juttis, heels & embellished flats.', 'img' => 'https://images.unsplash.com/photo-1543163521-1bf539c55dd2?q=80&w=1200&auto=format&fit=crop', 'featured' => true],
            ['name' => 'Bags & Clutches', 'key' => 'bags', 'desc' => 'Potlis, clutches & everyday totes.', 'img' => 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?q=80&w=1200&auto=format&fit=crop', 'featured' => false],
            ['name' => 'Beauty & Essentials', 'key' => 'beauty', 'desc' => 'Cosmetics, skincare & must-have add-ons.', 'img' => 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?q=80&w=1200&auto=format&fit=crop', 'featured' => false],
        ];

        $map = [];
        $order = 1;
        foreach ($data as $row) {
            $cat = Category::updateOrCreate(
                ['slug' => $row['key']],
                [
                    'name' => $row['name'],
                    'description' => $row['desc'],
                    'image' => $row['img'],
                    'is_active' => true,
                    'is_featured' => $row['featured'],
                    'sort_order' => $order++,
                ],
            );
            $map[$row['key']] = $cat;
        }

        return $map;
    }

    protected function seedProducts(array $categories): void
    {
        if (Product::count() > 0) {
            return;
        }

        $catalog = [
            ['cat' => 'sarees', 'name' => 'Banarasi Silk Saree — Royal Maroon', 'price' => 6499, 'sale' => 4999, 'brand' => 'Navanari Heritage', 'material' => 'Pure Banarasi Silk', 'sizes' => null, 'colors' => ['Maroon', 'Emerald', 'Royal Blue'], 'featured' => true, 'imgs' => ['https://images.unsplash.com/photo-1610030469983-98e550d6193c?q=80&w=1000&auto=format&fit=crop', 'https://images.unsplash.com/photo-1583391733956-6c78276477e2?q=80&w=1000&auto=format&fit=crop']],
            ['cat' => 'sarees', 'name' => 'Organza Floral Saree — Blush Pink', 'price' => 3899, 'sale' => null, 'brand' => 'Navanari', 'material' => 'Organza', 'sizes' => null, 'colors' => ['Blush', 'Lavender', 'Mint'], 'featured' => true, 'imgs' => ['https://images.unsplash.com/photo-1614886137372-3c1b0d9b9c6f?q=80&w=1000&auto=format&fit=crop']],
            ['cat' => 'dresses', 'name' => 'Embroidered Anarkali Gown — Wine', 'price' => 5499, 'sale' => 4299, 'brand' => 'Navanari Couture', 'material' => 'Georgette', 'sizes' => ['S', 'M', 'L', 'XL'], 'colors' => ['Wine', 'Teal', 'Black'], 'featured' => true, 'imgs' => ['https://images.unsplash.com/photo-1595777457583-95e059d581b8?q=80&w=1000&auto=format&fit=crop']],
            ['cat' => 'dresses', 'name' => 'Floral Maxi Dress — Summer Bloom', 'price' => 2299, 'sale' => 1799, 'brand' => 'Navanari', 'material' => 'Rayon', 'sizes' => ['XS', 'S', 'M', 'L', 'XL'], 'colors' => ['Yellow', 'Coral', 'Sky Blue'], 'featured' => false, 'imgs' => ['https://images.unsplash.com/photo-1490481651871-ab68de25d43d?q=80&w=1000&auto=format&fit=crop']],
            ['cat' => 'dresses', 'name' => 'Cotton Straight Kurti — Indigo Block Print', 'price' => 1299, 'sale' => null, 'brand' => 'Navanari', 'material' => 'Cotton', 'sizes' => ['S', 'M', 'L', 'XL', 'XXL'], 'colors' => ['Indigo', 'Rust', 'Olive'], 'featured' => false, 'imgs' => ['https://images.unsplash.com/photo-1602173574767-37ac01994b2a?q=80&w=1000&auto=format&fit=crop']],
            ['cat' => 'jewellery', 'name' => 'Kundan Bridal Necklace Set', 'price' => 4599, 'sale' => 3499, 'brand' => 'Navanari Jewels', 'material' => 'Gold-plated Kundan', 'sizes' => null, 'colors' => ['Gold', 'Rose Gold'], 'featured' => true, 'imgs' => ['https://images.unsplash.com/photo-1535632066927-ab7c9ab60908?q=80&w=1000&auto=format&fit=crop']],
            ['cat' => 'jewellery', 'name' => 'Jhumka Earrings — Antique Gold', 'price' => 899, 'sale' => 649, 'brand' => 'Navanari Jewels', 'material' => 'Brass, Antique finish', 'sizes' => null, 'colors' => ['Gold', 'Silver Oxidised'], 'featured' => true, 'imgs' => ['https://images.unsplash.com/photo-1611591437281-460bfbe1220a?q=80&w=1000&auto=format&fit=crop']],
            ['cat' => 'footwear', 'name' => 'Embroidered Juttis — Ivory Pearl', 'price' => 1499, 'sale' => null, 'brand' => 'Navanari', 'material' => 'Velvet, Hand-embroidered', 'sizes' => ['36', '37', '38', '39', '40'], 'colors' => ['Ivory', 'Red', 'Royal Blue'], 'featured' => false, 'imgs' => ['https://images.unsplash.com/photo-1543163521-1bf539c55dd2?q=80&w=1000&auto=format&fit=crop']],
            ['cat' => 'bags', 'name' => 'Silk Potli Bag — Gold Tassel', 'price' => 999, 'sale' => 749, 'brand' => 'Navanari', 'material' => 'Silk', 'sizes' => null, 'colors' => ['Gold', 'Maroon', 'Emerald'], 'featured' => false, 'imgs' => ['https://images.unsplash.com/photo-1584917865442-de89df76afd3?q=80&w=1000&auto=format&fit=crop']],
            ['cat' => 'beauty', 'name' => 'Velvet Matte Lipstick — Rose Affair', 'price' => 599, 'sale' => 449, 'brand' => 'Navanari Beauty', 'material' => 'Cruelty-free', 'sizes' => null, 'colors' => ['Rose', 'Plum', 'Nude', 'Crimson'], 'featured' => true, 'imgs' => ['https://images.unsplash.com/photo-1586495777744-4413f21062fa?q=80&w=1000&auto=format&fit=crop']],
        ];

        $reviewers = [
            ['Ananya R.', 5, 'Absolutely stunning!', 'The fabric quality exceeded my expectations. Got so many compliments.'],
            ['Priya S.', 4, 'Beautiful piece', 'Loved the colour and fit. Delivery was quick too.'],
            ['Meera K.', 5, 'Worth every rupee', 'Elegant and comfortable. Will shop again from Navanari.'],
        ];

        foreach ($catalog as $item) {
            $category = $categories[$item['cat']] ?? null;
            $product = Product::create([
                'category_id' => $category?->id,
                'name' => $item['name'],
                'sku' => 'NAV-'.strtoupper(Str::random(6)),
                'brand' => $item['brand'],
                'short_description' => 'A Navanari signature piece — crafted from '.$item['material'].'.',
                'description' => "<p>Add timeless charm to your wardrobe with the <strong>{$item['name']}</strong>. Thoughtfully crafted from {$item['material']}, this piece blends traditional artistry with a contemporary finish.</p><p>Perfect for festive occasions, celebrations and gifting. Pair it with your favourite Navanari accessories to complete the look.</p><ul><li>Premium {$item['material']}</li><li>Handpicked quality</li><li>Easy care &amp; long-lasting</li></ul>",
                'price' => $item['price'],
                'sale_price' => $item['sale'],
                'show_price' => true,
                'sizes' => $item['sizes'],
                'colors' => $item['colors'],
                'material' => $item['material'],
                'stock_status' => 'in_stock',
                'is_featured' => $item['featured'],
                'is_active' => true,
            ]);

            foreach ($item['imgs'] as $i => $url) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'path' => $url,
                    'is_primary' => $i === 0,
                    'sort_order' => $i,
                ]);
            }

            foreach (array_slice($reviewers, 0, rand(1, 3)) as $r) {
                Review::create([
                    'product_id' => $product->id,
                    'author_name' => $r[0],
                    'rating' => $r[1],
                    'title' => $r[2],
                    'body' => $r[3],
                    'is_approved' => true,
                ]);
            }
        }
    }
}
