<?php
namespace App\Controllers;

use Illuminate\Database\Query\Builder;

class Controller {
    protected $table;

    public function __construct(Builder $table)
    {
        $this->table = $table;
    }
}