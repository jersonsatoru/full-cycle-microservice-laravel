<?php

namespace Tests\Unit;

use App\Models\Category;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        $this->category = new Category();
    }

    public function testCategoryFillableAttributes()
    {
        $this->assertEqualsCanonicalizing(
            ['name', 'is_active', 'description'],
            $this->category->getFillable(),
        );
    }

    public function testCategoryCastsAttributes()
    {
        $this->assertEqualsCanonicalizing(
            ['id' => 'string', 'is_active' => 'boolean'],
            $this->category->getCasts(),
        );
    }

    public function testIfHasTraits()
    {
        $categoriesTraits = array_keys(class_uses(Category::class));
        $this->assertEquals(
            $categoriesTraits,
            [SoftDeletes::class, \App\Models\Traits\Uuid::class]
        );
    }

    public function testIncrementsAttributes()
    {
        $this->assertFalse($this->category->getIncrementing());
    }

    public function testDatesAttributes()
    {
        $dates = ['updated_at', 'deleted_at', 'created_at'];
        foreach($dates as $date) {
            $this->assertContains($date, $this->category->getDates());
        }
        $this->assertCount(count($dates), $this->category->getDates());
    }
}
