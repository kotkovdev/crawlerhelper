<?php
namespace App\Controllers;

use Illuminate\Database\Query\Builder;

class Controller {
    protected $table;

    public function __construct(Builder $table = null)
    {
        $this->table = $table;
    }

    public function checkLock() {
        if (file_exists(LOCK_FILE)) {
            return true;
        } else {{
            return false;
        }}
    }
}