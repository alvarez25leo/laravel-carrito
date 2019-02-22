<?php

namespace Manuel\Laravcart;

use Illuminate\Support\ServiceProvider;

class LaravcartServiceProvider extends ServiceProvider {

    public function register() {
        $this->app->bind('carrito', function() {
            return new \Manuel\Laravcart\Laravcart;
        });
    }

}
