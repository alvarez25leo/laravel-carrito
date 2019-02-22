<?php

namespace Manuel\Laravcart;

use Illuminate\Support\Collection as IlluminateCollection;
use Exception;

class Collection extends IlluminateCollection {

    /**
     * All items
     *
     * @var Array
     */
    protected $articulos;

    /**
     * Required fields. User must supply these fields
     *
     * @var array
     */
    protected $camposRequeridos = [
        'id',
        'nombre',
        'precio',
        'cantidad'
    ];

    public function ingresarArticulos(array $articulos) {
        $this->articulos = $articulos;
    }

    public function obtenerArticulos() {
        return $this->articulos;
    }

    /**
     * Find an item in cart
     *
     * @return array
     */
    public function encontrarArticulo($key) {
        return isset($this->articulos[$key]) ? $this->articulos[$key] : null;
    }

    public function existe($articulo) {
        if ($this->encontrarArticulo($articulo['id'])) {
            return true;
        }

        return false;
    }

    public function insertar(array $articulo) {
        $this->validarArticulo($articulo);

        $this->articulos[$articulo['id']] = (object) $articulo;

        return $this->articulos;
    }

    // Alias of insert
    public function actualizar(array $articulo) {
        return $this->insertar($articulo);
    }

    /**
     * Verify all required fields are exist
     *
     * @param  Array  $item
     * @return Void
     */
    public function validarArticulo(array $articulo) {
        $campos = array_diff_key(array_flip($this->camposRequeridos), $articulo);

        if ($campos) {
            throw new Exception('Faltan algunos campos obligatorios: ' . implode(",", array_keys($campos)));
        }

        if ($articulo['cantidad'] < 1) {
            throw new Exception('La cantidad no puede ser inferior a 1.');
        }

        if (!is_numeric($articulo['precio'])) {
            throw new Exception('El precio debe ser un número numérico');
        }
    }

}
