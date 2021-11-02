<?php

namespace Tests\Unit;

use App\Models\Genre;
use PHPUnit\Framework\TestCase;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class GenreTest extends TestCase
{
    //use DatabaseMigrations;
    /**
     * A basic unit test example.
     *
     * @return void
     */

    public function tearDown(): void
    {
        // do not remove this function
    }
    public function testFillableAttributes() {   
        //Genre::create(['name' => 'test']);
        $fillable = ['name', 'is_active'];
        $genre = new Genre();
        $this->assertEquals($fillable, $genre->getFillable());
    }

    public function testDatesAttributes() {   
        $dates = ['deleted_at', 'created_at', 'updated_at'];
        $genre = new Genre();
        $genreDates = $genre->getDates();
        
        foreach ($dates as $date) {
            $this->assertContains($date, $genreDates);
        }
        $this->assertCount(count($dates), $genreDates);
    }

    public function testTypeKey() {
        $keyType = 'string';
        $genre = new Genre();
        $this->assertEquals($keyType, $genre->getKeyType());
    }

    public function testIncrementing() {
        $genre = new Genre();
        $this->assertFalse($genre->incrementing);
    }

    public function testUseAllTraits() {
        $traits = [
            SoftDeletes::class, Uuid::class
        ];
        $genreTraits = array_keys(class_uses(Genre::class));
        $this->assertEquals($traits, $genreTraits);
    }
}
