<?php

namespace Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;

class MockModel extends Model
{
    /**
     * @return \Mockery\MockInterface
     */
    public function getConnection()
    {
        $mock = \Mockery::mock('Illuminate\Database\Connection');
        return $mock;
    }

    /**
     * @param array  $options
     */
    public function save(array $options = [])
    {
        $_SERVER['__eloquent.saved'] = $_SERVER['__eloquent.saved'] ?? 0;

        $_SERVER['__eloquent.saved']++;
    }
}
