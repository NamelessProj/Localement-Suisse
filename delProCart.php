<?php
require_once './components/cartManager.php';

if(isset($_SESSION['user']['type'])){
    header('Location: index.php');
    die("Sellers don't shop");
}

require_once './db.php';

if(isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id'])){
    $id = $_GET['id'];

    // GETTING THE CURRENT CART OF THE USER
    $sql = "SELECT cart_id FROM carts WHERE status_id = 1 AND (user_id = :user_id OR cookie_id = :cookie_id)";
    $req = $db->prepare($sql);
    $req->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $req->bindValue(':cookie_id', $cookieId);
    $req->execute();
    $cartId = $req->fetch()->cart_id;

    // REMOVING THE PRODUCT FROM THE CART
    $sql = "DELETE FROM products_carts WHERE pro_id = :pro_id AND cart_id = :cart_id";
    $req = $db->prepare($sql);
    $req->bindValue(':pro_id', $id, PDO::PARAM_INT);
    $req->bindValue(':cart_id', $cartId, PDO::PARAM_INT);
    $req->execute();

    // PREPARING TO UPDATE THE CART WITH THE NEW TOTAL PRICE
    $sql = "SELECT * FROM products_carts WHERE cart_id = :cart_id";
    $req = $db->prepare($sql);
    $req->bindValue(':cart_id', $cartId, PDO::PARAM_INT);
    $req->execute();
    $cartsPro = $req->fetchAll();

    $totalPrice = 0;

    foreach($cartsPro as $cartPro){
        $price = $cartPro->quantity * $cartPro->price;
        $totalPrice += $price;
    }

    // UPDATING THE CART WITH THE NEW TOTAL PRICE
    $sql = "UPDATE carts SET cart_total = :price WHERE cart_id = :cart_id";
    $req = $db->prepare($sql);
    $req->bindValue(':price', $totalPrice, PDO::PARAM_INT);
    $req->bindValue(':cart_id', $cartId, PDO::PARAM_INT);
    $req->execute();
}

header('Location: ./cart.php'); // GOING TO THE CART OF THE USER