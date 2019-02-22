<?php

namespace Manuel\Laravcart;

use Exception;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface;

class Laravcart implements LaravcartInterface {

    const CARTSUFFIX = '_cart';

    protected $session;
    protected $collection;
    protected $nombre = "laravcart";

    public function __construct($nombre = null, SessionStorageInterface $storage = null) {
        $this->session = new Session($storage);

        $this->collection = new Collection();

        if ($nombre) {
            $this->crearCarrito($nombre);
        }
    }

    public function crearCarrito($nombre) {
        if (empty($nombre)) {
            throw new InvalidArgumentException('El nombre del carrito no puede estar vacío.');
        }

        $this->nombre = $nombre . self::CARTSUFFIX;
    }

    public function obtenerCarrito() {
        return $this->nombre;
    }

    public function named($nombre) {
        $this->crearCarrito($nombre);

        return $this;
    }

    public function agregar(array $producto) {
        $this->collection->validarArticulo($producto);

        // If item already added, increment the quantity
        if ($this->existe($producto['id'])) {
            $item = $this->obtener($producto['id']);

            return $this->actualizarCantidad($item->id, $item->cantidad + $producto['cantidad']);
        }

        $this->collection->ingresarArticulos($this->session->get($this->obtenerCarrito(), []));

        $articulo = $this->collection->insertar($producto);

        $this->session->set($this->obtenerCarrito(), $articulo);

        return $this->collection->make($articulo);
    }

    public function actualizar(array $producto) {
        $this->collection->ingresarArticulos($this->session->get($this->obtenerCarrito(), []));

        if (!isset($producto['id'])) {
            throw new Exception('El id es requerido.');
        }

        if (!$this->existe($producto['id'])) {
            throw new Exception('No hay artículo en el carrito de compras con id: ' . $producto['id']);
        }

        $articulo = array_merge((array) $this->obtener($producto['id']), $producto);

        $articulos = $this->collection->insertar($articulo);

        $this->session->set($this->obtenerCarrito(), $articulos);

        return $this->collection->make($articulos);
    }

    public function actualizarCantidad($id, $cantidad) {
        $articulo = (array) $this->obtener($id);

        $articulo['cantidad'] = $cantidad;

        return $this->actualizar($articulo);
    }

    public function actualizarPrecio($id, $precio) {
        $articulo = (array) $this->obtener($id);

        $articulo['precio'] = $precio;

        return $this->actualizar($articulo);
    }

    public function eliminar($id) {
        $articulos = $this->session->get($this->obtenerCarrito(), []);

        unset($articulos[$id]);

        $this->session->set($this->obtenerCarrito(), $articulos);

        return $this->collection->make($articulos);
    }

    public function articulos() {
        return $this->obtenerArticulos();
    }

    public function obtenerArticulos() {
        return $this->collection->make($this->session->get($this->obtenerCarrito()));
    }

    public function obtener($id) {
        $this->collection->ingresarArticulos($this->session->get($this->obtenerCarrito(), []));

        return $this->collection->encontrarArticulo($id);
    }

    public function existe($id) {
        $this->collection->ingresarArticulos($this->session->get($this->obtenerCarrito(), []));

        return $this->collection->encontrarArticulo($id) ? true : false;
    }

    public function count() {
        $articulos = $this->obtenerArticulos();
        return $articulos->count();
    }

    public function obtenerTotal() {
        $articulos = $this->obtenerArticulos();
        return $articulos->sum(function($articulo) {
                    return $articulo->precio * $articulo->cantidad;
                });
    }

    public function totalCantidad() {
        $articulos = $this->obtenerArticulos();

        return $articulos->sum(function($articulos) {
                    return $articulos->cantidad;
                });
    }

    public function copy($carrito) {
        if (is_object($carrito)) {
            if (!$carrito instanceof \Manuel\Laravcart\Laravcart) {
                throw new InvalidArgumentException("El argumento debe ser una instancia de " . get_class($this));
            }

            $articulos = $this->session->get($carrito->obtenerCarrito(), []);
        } else {
            if (!$this->session->has($carrito . self::CARTSUFFIX)) {
                throw new Exception('El carrito no existe: ' . $carrito);
            }

            $articulos = $this->session->get($carrito . self::CARTSUFFIX, []);
        }

        $this->session->set($this->obtenerCarrito(), $articulos);
    }

    public function flash() {
        $this->limpiar();
    }

    public function limpiar() {
        $this->session->remove($this->obtenerCarrito());
    }

}
