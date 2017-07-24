<?php

namespace EloquentExtensions\Generator;

use Illuminate\Database\Eloquent\Model;

abstract class Decorator
{
    abstract public function __invoke(Model $builder);
}
