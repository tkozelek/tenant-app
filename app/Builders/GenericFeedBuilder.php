<?php

namespace App\Builders;

use App\Models\XmlFeed;

class GenericFeedBuilder implements Builder
{
    public function build(XmlFeed $feed, iterable $variants): string
    {
        $xml = new \XMLWriter;
        $xml->openMemory();
        $xml->startDocument('1.0', 'UTF-8');
        $xml->setIndent(true);

        $xml->startElement('products');

        foreach ($variants as $variant) {
            $product = $variant->product;
            $globalProduct = $product?->globalProduct;

            if (! $product || ! $globalProduct) {
                continue;
            }

            $price = $variant->activePriceHistory?->price;
            $originalPrice = $variant->activePriceHistory?->original_price;

            $xml->startElement('product');

            $xml->writeElement('id', $variant->sku ?? (string) $variant->id);
            $xml->writeElement('name', trim("{$product->name} {$variant->name}"));

            if ($product->description) {
                $xml->writeElement('description', strip_tags($product->description));
            }

            $xml->writeElement(
                'url',
                $variant->url ?? route('products.product', ['globalProduct' => $globalProduct->slug])
            );

            $imageUrl = $variant->getFirstMediaUrl('tenant_product_variants')
                ?: $product->getFirstMediaUrl('global_products');

            if ($imageUrl) {
                $xml->writeElement('image_url', $imageUrl);
            }

            if ($price !== null) {
                $xml->writeElement('price', number_format((float) $price, 2, '.', ''));
            }

            if ($originalPrice !== null) {
                $xml->writeElement('original_price', number_format((float) $originalPrice, 2, '.', ''));
            }

            $xml->writeElement('currency', $feed->currency);
            $xml->writeElement('stock', (string) $variant->stock_quantity);

            if ($variant->ean) {
                $xml->writeElement('ean', $variant->ean);
            }

            if ($variant->sku) {
                $xml->writeElement('sku', $variant->sku);
            }

            $category = $globalProduct->category;
            if ($category) {
                $xml->writeElement('category', $category->name);
            }

            if ($variant->variantAttributes->isNotEmpty()) {
                $xml->startElement('attributes');

                foreach ($variant->variantAttributes as $va) {
                    $xml->startElement('attribute');
                    $xml->writeElement('name', $va->attribute->name ?? '');
                    $xml->writeElement('value', $va->attributeValue->value ?? '');
                    $xml->endElement(); // attribute
                }

                $xml->endElement(); // attributes
            }

            $xml->endElement(); // product
        }

        $xml->endElement(); // products
        $xml->endDocument();

        return $xml->outputMemory();
    }
}
