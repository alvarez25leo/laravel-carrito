<?php
namespace Manuel\Laravcart\Facades;

use Illuminate\Support\Facades\Facade;

class Laravcart extends Facade {
    protected static function getFacadeAccessor() { return 'carrito'; }
}


