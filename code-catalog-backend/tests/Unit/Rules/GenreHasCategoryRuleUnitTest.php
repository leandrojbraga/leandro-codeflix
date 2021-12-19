<?php
declare(strict_types=1);

namespace Tests\Unit\Rules;

use App\Rules\GenreHasCategoryRule;
use Mockery\MockInterface;
use Tests\TestCase;

class GenreHasCategoryRuleUnitTest extends TestCase
{   
    private $reflectionClass;

    protected function setUp(): void
    {
        parent::setUp();
        $this->reflectionClass = new \ReflectionClass(GenreHasCategoryRule::class);
    }
    
    protected function createRuleMock(array $categoriesId): MockInterface
    {
        return \Mockery::mock(GenreHasCategoryRule::class, [$categoriesId])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
    }

    public function testCategoriesIdField() {
        $rule = new GenreHasCategoryRule([1,1,2,2]);

        $reflectionProperty = $this->reflectionClass->getProperty('categoriesId');
        $reflectionProperty->setAccessible(true);

        $categoriesId = $reflectionProperty->getValue($rule);
        $this->assertEqualsCanonicalizing([1,2], $categoriesId);

    }

    public function testGenresIdField() {
        $rule = $this->createRuleMock([]);
        $rule->shouldReceive('getRelationCategories')
            ->withAnyArgs()
            ->andReturnNull();

        $rule->passes('', [1,1,2,2]);

        $reflectionProperty = $this->reflectionClass->getProperty('genresId');
        $reflectionProperty->setAccessible(true);

        $genresId = $reflectionProperty->getValue($rule);
        $this->assertEqualsCanonicalizing([1,2], $genresId);

    }

    public function testPassesReturnFalseWhenCategoryOrGenreIsArrayEmpty() {
        $rule = $this->createRuleMock([1]);
        $this->assertFalse($rule->passes('',[]));

        $rule = $this->createRuleMock([]);
        $this->assertFalse($rule->passes('',[1]));

    }

    public function testPassesReturnFalseWhenGetRelationCategoriesIsEmpty() {
        $rule = $this->createRuleMock([1]);
        $rule->shouldReceive('getRelationCategories')
            ->withAnyArgs()
            ->andReturn(collect());

        $this->assertFalse($rule->passes('',[1]));

    }

    public function testPassesReturnFalseWhenHasCategoriesWithoutGenres() {
        $rule = $this->createRuleMock([1,2]);
        $rule->shouldReceive('getRelationCategories')
            ->withAnyArgs()
            ->andReturn(collect(['category_id' => 1]));

        $this->assertFalse($rule->passes('',[1]));

    }

    public function testPassesReturnTrue() {
        $rule = $this->createRuleMock([1,2]);
        $rule->shouldReceive('getRelationCategories')
            ->withAnyArgs()
            ->andReturn(collect([
                ['category_id' => 1],
                ['category_id' => 2]
            ]));

        $this->assertTrue($rule->passes('',[1]));

        $rule = $this->createRuleMock([1,2]);
        $rule->shouldReceive('getRelationCategories')
            ->withAnyArgs()
            ->andReturn(collect([
                ['category_id' => 1],
                ['category_id' => 2],
                ['category_id' => 1],
                ['category_id' => 2]
            ]));

        $this->assertTrue($rule->passes('',[1]));

    }
}