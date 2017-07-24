<?php

namespace EloquentExtensions\Generator;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Generator
{
    /**
     * @var array
     */
    protected $decorators;

    /**
     * @var string
     */
    protected $model;

    /**
     * Generator constructor. Private to allow for a builder
     *
     * @param string  $modelName  The name of the eloquent model  class.
     */
    private function __construct(string $modelName)
    {
        $this->model      = $modelName;
        $this->decorators = [];
    }

    /**
     * @param Decorator  $decorator  The decorator to add to the generator
     */
    public function with(Decorator $decorator)
    {
        if (!in_array($decorator, $this->decorators, true)) {
            $this->decorators[] = $decorator;
        }
    }

    /**
     * @param int  $count  The number of models to make
     * @return Model|Collection
     */
    public function make(int $count = 1)
    {
        if ($count <= 1) {
            return $this->makeModel();
        }

        $model = new $this->model;

        return $model->newCollection(array_map( function () {
            return $this->makeModel();
        }, range(1, $count)));
    }

    /**
     * Makes and persists models to the database
     *
     * @param int  $count  The number of models to create
     * @return Model|Collection
     */
    public function create(int $count = 1)
    {
        $models = $this->make($count);

        if (count($models) === 1) {
            $models->save();
        } else {
            $models->each->save();
        }

        return $models;
    }

    /*
     * The construction point for the builder.
     *
     * @param string  $modelName  The name of the eloquent model class.
     */
    public static function buildFromModel(string $modelName) : Generator
    {
        return new self($modelName);
    }

    /**
     * @return Model
     */
    protected function makeModel() : Model
    {
        $model = new $this->model;

        foreach ($this->decorators as $decorator) {
            $decorator($model);
        }

        return $model;
    }
}
