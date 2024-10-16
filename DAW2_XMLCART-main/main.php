<?php

echo "INIT EXECUTION<br><br>";


include 'cart.php';
include 'product.php';
include 'user.php';


AddToCart('2', 10);



//UserRegister('DNI','Nombre');


/* 
http://localhost/index.php?action=add&id_prod=1&quantity=3 añadir item

http://localhost/index.php?action=remove&id_prod=1 eliminar item

http://localhost/index.php?action=view ver carrito

http://localhost/index.php?action=update&id_prod=1&quantity=5 actualizar carrito

http://localhost/index.php?action=login&username=usuario1&password=1234 validar usuarios

http://localhost/index.php?action=register&username=nuevoUsuario&password=pass1234 registrar usuarios

http://localhost/index.php?action=user_info&username=usuario1 ver info del user



*/


?>