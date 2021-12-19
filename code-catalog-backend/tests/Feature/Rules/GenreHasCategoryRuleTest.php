<?php

namespace Tests\Feature\Models\Rules;

use App\Models\Category;
use App\Models\Genre;
use App\Rules\GenreHasCategoryRule;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class GenreHasCategoryRuleTest extends TestCase
{
    use DatabaseMigrations;

    private $factoryCategory;
    private $factoryGenre;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->factoryCategory = factory(Category::class, 4)->create();
        $this->factoryGenre = factory(Genre::class, 2)->create();
        
        $this->factoryGenre[0]->categories()->sync([
            $this->factoryCategory[0]->id,
            $this->factoryCategory[1]->id
        ]);
        $this->factoryGenre[1]->categories()->sync([
            $this->factoryCategory[2]->id
        ]);
    }

    public function testPassesReturnTrue() {
        $rule = new GenreHasCategoryRule([
            $this->factoryCategory[0]->id
        ]);
        $isValid = $rule->passes('', [
            $this->factoryGenre[0]->id
        ]);
        $this->assertTrue($isValid);

        $rule = new GenreHasCategoryRule([
            $this->factoryCategory[2]->id
        ]);
        $isValid = $rule->passes('', [
            $this->factoryGenre[1]->id
        ]);
        $this->assertTrue($isValid);

        $rule = new GenreHasCategoryRule([
            $this->factoryCategory[0]->id,
            $this->factoryCategory[1]->id
        ]);
        $isValid = $rule->passes('', [
            $this->factoryGenre[0]->id
        ]);
        $this->assertTrue($isValid);

        $rule = new GenreHasCategoryRule([
            $this->factoryCategory[0]->id,
            $this->factoryCategory[2]->id
        ]);
        $isValid = $rule->passes('', [
            $this->factoryGenre[0]->id,
            $this->factoryGenre[1]->id
        ]);
        $this->assertTrue($isValid);

        $rule = new GenreHasCategoryRule([
            $this->factoryCategory[0]->id,
            $this->factoryCategory[1]->id,
            $this->factoryCategory[2]->id
        ]);
        $isValid = $rule->passes('', [
            $this->factoryGenre[0]->id,
            $this->factoryGenre[1]->id
        ]);
        $this->assertTrue($isValid);
    }

    public function testPassesReturnFalse() {
        $rule = new GenreHasCategoryRule([
            $this->factoryCategory[0]->id
        ]);
        $isValid = $rule->passes('', [
            $this->factoryGenre[1]->id
        ]);
        $this->assertFalse($isValid);

        $rule = new GenreHasCategoryRule([
            $this->factoryCategory[2]->id
        ]);
        $isValid = $rule->passes('', [
            $this->factoryGenre[0]->id,
            $this->factoryGenre[1]->id
        ]);
        $this->assertFalse($isValid);

        $rule = new GenreHasCategoryRule([
            $this->factoryCategory[3]->id
        ]);
        $isValid = $rule->passes('', [
            $this->factoryGenre[0]->id
        ]);
        $this->assertFalse($isValid);
    }
}
