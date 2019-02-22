<?php

namespace Manuel\Laravcart;

interface LaravcartInterface {

    public function crearCarrito($nombre); // setCart

    public function obtenerCarrito();

    public function agregar(Array $producto);

    public function actualizar(Array $producto);

    public function eliminar($id);

    public function obtenerArticulos();

    public function obtener($id);

    public function existe($id);

    public function limpiar();

    public function totalCantidad();

    public function obtenerTotal();
}
