<?php
namespace App\Controllers;

class WGET {
    private $command;


    private function exec() {
        exec($this->command);
    }
}