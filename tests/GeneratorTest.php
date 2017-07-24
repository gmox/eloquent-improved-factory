<?php

use EloquentExtensions\Generator\Generator;
use EloquentExtensions\Generator\Decorator;
use Illuminate\Database\Eloquent\Collection;
use Tests\Fixtures\Models\MockModel;
use Tests\Fixtures\TestCase;

class GeneratorTest extends TestCase
{
    /**
     * @group generator-tests
     *
     * @test
     */
    public function it_should_create_an_instance_of_itself_via_builder()
    {
        $generator = Generator::buildFromModel('Test');

        $this->assertInstanceOf(Generator::class, $generator);
    }

    /**
     * @group generator-tests
     *
     * @test
     */
    public function it_should_add_a_decorator_to_the_generator()
    {
        $generator = Generator::buildFromModel(MockModel::class);

        $mockDecorator = \Mockery::mock(Decorator::class)
            ->shouldReceive('__invoke')
            ->once()
            ->andReturnUsing( function($generator) {
                $generator->foo = 1;
            })->getMock();

        $generator->with($mockDecorator);

        $this->assertEquals(1, $generator->make()->foo);
    }

    /**
     * @group generator-tests
     *
     * @test
     */
    public function it_should_add_a_decorator_to_the_generator_only_once()
    {
        $generator = Generator::buildFromModel(MockModel::class);

        $mockDecorator = \Mockery::mock(Decorator::class)
            ->shouldReceive('__invoke')
            ->once()
            ->andReturnUsing( function($model) {
                $model->foo += 1;
            })->getMock();

        $generator->with($mockDecorator);

        $generator->with($mockDecorator);

        // should have applied only once
        $this->assertEquals(1, $generator->make()->foo);
    }

    /**
     * @group generator-tests
     *
     * @test
     */
    public function it_should_get_the_model_as_immutable()
    {
        $generator = Generator::buildFromModel(MockModel::class);

        $firstDecorator = \Mockery::mock(Decorator::class)
            ->shouldReceive('__invoke')
            ->twice() // twice: one for each model generation
            ->andReturnUsing( function($generator) {
                $generator->foo = 3;
            })->getMock();

        $secondDecorator = \Mockery::mock(Decorator::class)
            ->shouldReceive('__invoke')
            ->once()
            ->andReturnUsing( function($model) {
                $model->foo = 2;
            })->getMock();

        $generator->with($firstDecorator);

        $firstModel = $generator->make();

        $this->assertEquals(3, $firstModel->foo);

        $generator->with($secondDecorator);

        $secondModel = $generator->make();

        $this->assertEquals(3, $firstModel->foo);

        $this->assertEquals(2, $secondModel->foo);

        $this->assertNotSame($firstModel, $secondModel);
    }

    /**
     * @group generator-tests
     *
     * @test
     */
    public function it_should_make_many_models_when_count_is_specified()
    {
        $count = mt_rand(2, 7);

        $generator = Generator::buildFromModel(MockModel::class);

        $models = $generator->make($count);

        $this->assertCount($count, $models);
    }

    /**
     * @group generator-tests
     *
     * @test
     */
    public function it_should_make_one_model_when_count_not_specified()
    {
        $generator = Generator::buildFromModel(MockModel::class);

        $model = $generator->make();

        $this->assertInstanceOf(MockModel::class, $model);
    }

    /**
     * @group generator-tests
     *
     * @test
     */
    public function it_should_make_one_model_when_negative_count_is_specified()
    {
        $generator = Generator::buildFromModel(MockModel::class);

        $model = $generator->make(-1);

        $this->assertInstanceOf(MockModel::class, $model);
    }

    /**
     * @group generator-tests
     *
     * @test
     */
    public function it_should_create_one_model_when_no_count_is_specified()
    {
        $generator = Generator::buildFromModel(MockModel::class);

        $model = $generator->create();

        $this->assertInstanceOf(MockModel::class, $model);
        $this->assertEquals(1, $_SERVER['__eloquent.saved']);
    }

    /**
     * @group generator-tests
     *
     * @test
     */
    public function it_should_create_one_model_when_negative_count_is_specified()
    {
        $generator = Generator::buildFromModel(MockModel::class);

        $model = $generator->create(-1);

        $this->assertInstanceOf(MockModel::class, $model);
        $this->assertEquals(1, $_SERVER['__eloquent.saved']);
    }

    /**
     * @group generator-tests
     *
     * @test
     */
    public function it_should_create_many_models_when_count_is_specified()
    {
        $generator = Generator::buildFromModel(MockModel::class);

        $models = $generator->create(5);

        $this->assertInstanceOf(Collection::class, $models);
        $this->assertEquals(5, $_SERVER['__eloquent.saved']);
    }
}
