<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Controllers\Api\BasicCrudController;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Mockery;
use Mockery\Mock;
use Tests\Stubs\Controllers\CategoryControllerStub;
use Tests\Stubs\Models\CategoryStub;
use Tests\TestCase;

class BasicCrudControllerTest extends TestCase
{

    private $controller;

    protected function setUp() : void
    {
        parent::setUp();
        CategoryStub::dropTable();
        CategoryStub::createTable();
        $this->controller = new CategoryControllerStub();
    }

    protected function tearDown() : void
    {
        CategoryStub::dropTable();
        parent::tearDown();
    }

    public function testIndex()
    {
        $c = CategoryStub::create([
            'name' => 'Batatainha',
            'description' => 'Batatainhazinha',
            'is_active' => true,
        ]);

        $result = $this->controller->index()->toArray();

        $this->assertEquals([$c->toArray()], $result);
    }

    public function testInvalidationStoreDatabase()
    {
        $this->expectException(ValidationException::class);
        $request = \Mockery::mock(Request::class);
        $request->shouldReceive('all')
                ->once()
                ->andReturn(['name' => '']);

        $this->controller->store($request);
    }

    public function testStore()
    {
        $request = \Mockery::mock(Request::class);
        $request->shouldReceive('all')
                ->once()
                ->andReturn([
                    'name' => 'Jerson',
                    'description' => 'Desc',
                    'is_active' => false,
                ]);

        $category = $this->controller->store($request);
        $this->assertEquals(
            CategoryStub::all()->first()->toArray(),
            $category->toArray()
        );
    }

    public function testIfFindOrFailFetchModel()
    {
        $category = CategoryStub::create([
            'name' => 'Jerson',
            'description' => 'Desc',
            'is_active' => false,
        ]);

        $reflectionClass = new \ReflectionClass(BasicCrudController::class);
        $reflectionMethod = $reflectionClass->getMethod('findOrFail');
        $reflectionMethod->setAccessible(true);

        $result = $reflectionMethod->invokeArgs($this->controller, [$category->id]);
        $this->assertInstanceOf(CategoryStub::class, $result);
    }

    public function testIfFindOrFailThrowException()
    {
        $this->expectException(ModelNotFoundException::class);
        $reflectionClass = new \ReflectionClass(BasicCrudController::class);
        $reflextionMethod = $reflectionClass->getMethod('findOrFail');
        $reflextionMethod->setAccessible(true);

        $reflextionMethod->invokeArgs($this->controller, [1]);
    }

    public function testShow()
    {
        $category = CategoryStub::create([
            'name' => 'Jerson',
            'description' => 'Desc',
            'is_active' => true,
        ]);

        $category->refresh();
        $result = $this->controller->show($category->id);
        $this->assertEquals($category->toArray(), $result->toArray());
    }

    public function testInvalidationUpdateDatabase()
    {
        $this->expectException(ValidationException::class);
        $category = CategoryStub::create([
            'name' => 'Jerson',
            'description' => 'Desc',
            'is_active' => true,
        ]);

        $request = \Mockery::mock(Request::class);
        $request->shouldReceive('all')
                ->once()
                ->andReturn([
                    'name' => '',
                    'description' => 'Desc 2',
                    'is_active' => false,
                ]);

        $this->controller->update($request, $category->id);
    }

    public function testUpdate()
    {
        $category = CategoryStub::create([
            'name' => 'Jerson',
            'description' => 'Desc',
            'is_active' => true,
        ]);

        $request = \Mockery::mock(Request::class);
        $request->shouldReceive('all')
                ->once()
                ->andReturn([
                    'name' => 'Jerson 2',
                    'description' => 'Desc 2',
                    'is_active' => false,
                ]);

        $result = $this->controller->update($request, $category->id);
        $this->assertEquals($result->name, 'Jerson 2');
        $this->assertEquals($result->description, 'Desc 2');
        $this->assertEquals($result->is_active, false);
    }

    public function testDestroy()
    {
        $this->expectException(ModelNotFoundException::class);
        $category = CategoryStub::create([
            'name' => 'Jerson 2',
            'description' => 'Desc 2',
            'is_active' => false,
        ]);

        $this->controller->destroy($category->id);
        CategoryStub::where(['id' => $category->id])->firstOrFail();
    }
} 