<?php

namespace ElCaminas;
use \PDO;
use \ElCaminas\Producto;
class Carrito
{
    protected $connect;
    /** Sin parámetros. Sólo crea la variable de sesión
    */
    public function __construct()
    {
        global $connect;
        $this->connect = $connect;
        if (!isset($_SESSION['carrito'])){
            $_SESSION['carrito'] = array();
        }

    }
    public function addItem($id, $cantidad){
        $_SESSION['carrito'][$id] = $cantidad;
    }
    public function deleteItem($id){
      unset($_SESSION['carrito'][$id]);
    }
    public function empty(){
      unset($_SESSION['carrito']);
      self::__construct();
    }
    public function howMany(){
      return count($_SESSION['carrito']);
    }
    public function getTotal()
    {
      $total = 0;
      foreach($_SESSION['carrito'] as $key => $cantidad){
        $producto = new Producto($key);
        $subtotal = $producto->getPrecioReal() * $cantidad;
        $total += $subtotal;
      }
      return  $total;
    }
    public function toHtml(){
      //NO USAR, de momento
      $str = <<<heredoc
      <table class="table">
        <thead> <tr> <th>#</th> <th>Producto</th> <th>Cantidad</th> <th>Precio</th> <th>Total</th></tr> </thead>
        <tbody>
heredoc;
      if ($this->howMany() > 0){
        $i = 0;
        $suma = 0;
        foreach($_SESSION['carrito'] as $key => $cantidad){
          $producto = new Producto($key);
          $i++;
          $subtotal = $producto->getPrecioReal() * $cantidad;
          $suma += $subtotal;
          $subtotalTexto = number_format($subtotal , 2, ',', ' ') ;
          $str .= "<tr><th scope='row'>$i</th><td><a href='" .  $producto->getUrl() . "'>" . $producto->getNombre() . "</a>";
          $str .= "<a class='open-modal' title='Haga clic para ver el detalle del producto'  href='" .  $producto->getUrl() . "'>";
          $str .= "<span style='color:#000' class='fa fa-external-link'></span>";
          $str .= "</a></td><td>$cantidad</td><td>" .  $producto->getPrecioReal() ." €</td><td>$subtotalTexto €</td>";
          $str .= "<td><a href='./carro.php?action=delete&id=" . $key . "'>borrar</a></td>";
          $str .= "</tr>";
        }
      }
      echo "<tr><td><b>TOTAL:</b></td><td> <b>$subtotal</b></td><td> </td></tr>";
      //echo "</table>";

      $str .= <<<heredoc
        </tbody>
      </table>
heredoc;
      return $str;
    }
}
