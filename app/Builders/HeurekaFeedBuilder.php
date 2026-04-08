<?php

namespace App\Builders;

use App\Models\XmlFeed;

class HeurekaFeedBuilder implements Builder
{
    public function build(XmlFeed $feed, iterable $variants): string
    {
        $xml = new \XMLWriter();
        $xml->openMemory();
        $xml->startDocument('1.0', 'UTF-8');
        $xml->setIndent(true);

        $xml->startElement('SHOP');

        foreach ($variants as $variant) {
            $product = $variant->product;
            $globalProduct = $product?->globalProduct;

            if (! $product || ! $globalProduct) {
                continue;
            }

            $price = $variant->activePriceHistory?->price;

            if ($price === null) {
                continue;
            }

            $xml->startElement('SHOPITEM');

            $xml->writeElement('ITEM_ID', $variant->sku ?? (string) $variant->id);
            $xml->writeElement('PRODUCTNAME', trim("{$product->name} {$variant->name}"));
            $xml->writeElement('PRODUCT', $product->name);

            if ($product->description) {
                $xml->writeElement('DESCRIPTION', strip_tags($product->description));
            }

            $xml->writeElement(
                'URL',
                route('products.product', ['globalProduct' => $globalProduct->slug])
            );

            $imageUrl = $variant->getFirstMediaUrl('tenant_product_variants')
                ?: $product->getFirstMediaUrl('global_products');

            if ($imageUrl) {
                $xml->writeElement('IMGURL', $imageUrl);

                $additionalImages = $variant->getMedia('images')
                    ->skip(1)
                    ->merge($product->getMedia('images')->skip(1));

                foreach ($additionalImages->take(5) as $media) {
                    $xml->writeElement('IMGURL_ALTERNATIVE', $media->getUrl());
                }
            }

            $xml->writeElement('PRICE_VAT', number_format((float) $price, 2, '.', ''));
            $xml->writeElement('CURRENCY', $feed->currency);

            if ($variant->ean) {
                $xml->writeElement('EAN', $variant->ean);
            }

            $category = $globalProduct->category;
            if ($category) {
                $xml->writeElement('CATEGORYTEXT', $category->name);
            }

            if ($variant->stock_quantity > 0) {
                $xml->writeElement('ITEM_TYPE', 'new');
                $xml->writeElement('AVAILABILITY', 'in_stock');
            } else {
                $xml->writeElement('AVAILABILITY', 'out_of_stock');
            }

            foreach ($variant->variantAttributes as $itemAttribute) {
                $xml->startElement('PARAM');
                $xml->writeElement('PARAM_NAME', $itemAttribute->attribute->name ?? '');
                $xml->writeElement('VAL', $itemAttribute->attributeValue->value ?? '');
                $xml->endElement();
            }

            $xml->endElement(); // SHOPITEM
        }

        $xml->endElement(); // SHOP
        $xml->endDocument();

        return $xml->outputMemory();
    }
}
