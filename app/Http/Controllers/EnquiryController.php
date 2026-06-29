<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use App\Models\Product;
use App\Support\Settings;
use Illuminate\Http\Request;

class EnquiryController extends Controller
{
    /**
     * Log a product enquiry and redirect the visitor to WhatsApp with a
     * pre-filled message containing the product details and link.
     */
    public function whatsapp(Request $request, Product $product)
    {
        abort_unless($product->is_active, 404);

        $size = $request->get('size');
        $color = $request->get('color');
        $qty = max(1, (int) $request->get('qty', 1));

        // Resolve the chosen variant (if any) for accurate pricing.
        $variantModel = null;
        if ($variantId = $request->get('variant')) {
            $variantModel = $product->variants()->find($variantId);
        }

        $variant = array_filter([
            'size' => $size,
            'color' => $color,
            'qty' => $qty,
            'variant_id' => $variantModel?->id,
        ]);

        Enquiry::create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'variant' => $variant,
            'source' => 'whatsapp',
            'status' => 'new',
        ]);

        $message = $this->buildMessage($product, $size, $color, $qty, $variantModel);

        return redirect()->away(whatsapp_link($message));
    }

    protected function buildMessage(Product $product, ?string $size, ?string $color, int $qty, ?\App\Models\ProductVariant $variant = null): string
    {
        $lines = [];
        $lines[] = "Hello ".Settings::get('site_name', 'Navanari').'! 👋';
        $lines[] = "I'm interested in this product:";
        $lines[] = '';
        $lines[] = "🛍️ *{$product->name}*";

        if ($product->sku) {
            $lines[] = "Code: {$product->sku}";
        }
        if ($product->price_visible) {
            $unit = $variant ? $variant->effective_price : $product->effective_price;
            $regular = $variant ? (float) $variant->price : (float) $product->price;
            $onSale = $variant ? $variant->is_on_sale : $product->is_on_sale;
            $lines[] = 'Price: '.money($unit).($onSale ? ' (was '.money($regular).')' : '');
        }
        if ($size) {
            $lines[] = "Size: {$size}";
        }
        if ($color) {
            $lines[] = "Colour: {$color}";
        }
        $lines[] = "Quantity: {$qty}";
        $lines[] = '';
        $lines[] = 'Product link: '.route('product.show', $product);
        $lines[] = '';
        $lines[] = 'Could you share more details and availability?';

        return implode("\n", $lines);
    }
}
