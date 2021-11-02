<?php

namespace Tests\Unit;

use App\Models\Category;
use PHPUnit\Framework\TestCase;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CategoryTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testFillableAttributes() {
        $fillable = ['name', 'description', 'is_active'];
        $category = new Category();
        $this->assertEquals($fillable, $category->getFillable());
    }

    public function testDatesAttributes() {   
        $dates = ['deleted_at', 'created_at', 'updated_at'];
        $category = new Category();
        $categoryDates = $category->getDates();
        
        foreach ($dates as $date) {
            $this->assertContains($date, $categoryDates);
        }
        $this->assertCount(count($dates), $categoryDates);
    }

    public function testTypeKey() {
        $keyType = 'string';
        $category = new Category();
        $this->assertEquals($keyType, $category->getKeyType());
    }

    public function testIncrementing() {
        $category = new Category();
        $this->assertFalse($category->incrementing);
    }

    public function testUseAllTraits() {
        $traits = [
            SoftDeletes::class, Uuid::class
        ];
        $categoryTraits = array_keys(class_uses(Category::class));
        $this->assertEquals($traits, $categoryTraits);
    }
}
