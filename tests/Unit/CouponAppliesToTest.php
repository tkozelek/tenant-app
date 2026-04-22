<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\GlobalProduct;
use App\Models\Tenant;
use App\Models\TenantProduct;
use App\Models\TenantProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CouponAppliesToTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;

    private Coupon $coupon;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tenant = Tenant::factory()->create();
        $this->coupon = Coupon::factory()->create([
            'tenant_id' => $this->tenant->id,
            'is_active' => true,
            'starts_at' => now()->subDay(),
            'expires_at' => now()->addDays(7),
        ]);
    }

    private function makeVariant(?Category $category = null): TenantProductVariant
    {
        $globalProduct = GlobalProduct::factory()->create([
            'category_id' => ($category ?? Category::factory()->create())->id,
        ]);
        $product = TenantProduct::factory()->create([
            'tenant_id' => $this->tenant->id,
            'global_product_id' => $globalProduct->id,
        ]);

        return TenantProductVariant::factory()->for($product, 'product')->create();
    }

    public function test_applies_to_variant_with_no_restrictions(): void
    {
        $variant = $this->makeVariant();

        $this->assertTrue($this->coupon->appliesToVariant($variant));
    }

    public function test_applies_to_variant_when_variant_is_included(): void
    {
        $variant = $this->makeVariant();
        $this->coupon->productVariants()->attach($variant->id);

        $this->assertTrue($this->coupon->appliesToVariant($variant));
    }

    public function test_does_not_apply_to_variant_when_different_variant_is_included(): void
    {
        $included = $this->makeVariant();
        $other = $this->makeVariant();
        $this->coupon->productVariants()->attach($included->id);

        $this->assertFalse($this->coupon->appliesToVariant($other));
    }

    public function test_applies_to_variant_whose_category_is_restricted(): void
    {
        $category = Category::factory()->create();
        $variant = $this->makeVariant($category);
        $this->coupon->categories()->attach($category->id);

        $this->assertTrue($this->coupon->appliesToVariant($variant));
    }

    public function test_applies_to_variant_in_subcategory_of_restricted_category(): void
    {
        $parent = Category::factory()->create();
        $child = Category::factory()->create(['parent_id' => $parent->id]);
        $variant = $this->makeVariant($child);
        $this->coupon->categories()->attach($parent->id);

        $this->assertTrue($this->coupon->appliesToVariant($variant));
    }

    public function test_does_not_apply_to_variant_in_unrelated_category(): void
    {
        $restricted = Category::factory()->create();
        $unrelated = Category::factory()->create();
        $this->coupon->categories()->attach($restricted->id);

        $variant = $this->makeVariant($unrelated);

        $this->assertFalse($this->coupon->appliesToVariant($variant));
    }

    public function test_applies_to_category_with_no_restrictions(): void
    {
        $category = Category::factory()->create();

        $this->assertTrue($this->coupon->appliesToCategory($category));
    }

    public function test_applies_to_restricted_category(): void
    {
        $category = Category::factory()->create();
        $this->coupon->categories()->attach($category->id);

        $this->assertTrue($this->coupon->appliesToCategory($category));
    }

    public function test_applies_to_subcategory_of_restricted_category(): void
    {
        $parent = Category::factory()->create();
        $child = Category::factory()->create(['parent_id' => $parent->id]);
        $this->coupon->categories()->attach($parent->id);

        $this->assertTrue($this->coupon->appliesToCategory($child));
    }

    public function test_does_not_apply_to_unrelated_category_when_restriction_set(): void
    {
        $restricted = Category::factory()->create();
        $unrelated = Category::factory()->create();
        $this->coupon->categories()->attach($restricted->id);

        $this->assertFalse($this->coupon->appliesToCategory($unrelated));
    }

    public function test_does_not_apply_to_category_when_only_variant_restriction_set(): void
    {
        $variant = $this->makeVariant();
        $this->coupon->productVariants()->attach($variant->id);

        $anyCategory = Category::factory()->create();

        $this->assertFalse($this->coupon->appliesToCategory($anyCategory));
    }
}
