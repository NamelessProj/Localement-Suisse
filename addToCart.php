<?php
require_once './components/cartManager.php';

// IF IT'S A SELLER, HE SHOULDN'T BE HERE
if(isset($_SESSION['user']['type']) && $_SESSION['user']['type'] === 'v'){
    header('location: ./index.php');
    die();
}

// CHECK IF THERE'S A PRODUCT ID
if(!isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id'])){
    // NO ID
    $previousPage = $_SERVER['HTTP_REFERER'] ?? 'index.php';
    header('Location: '.$previousPage); // GOING TO THE PREVIOUS PAGE
    exit("There's no product ID");
}

require_once './db.php';

// CHECK IF USER ALREADY HAVE A CART
$sql = "SELECT * FROM carts WHERE user_id = :user_id OR cookie_id = :cookie_id AND status_id = :status_id";
$req = $db->prepare($sql);
$req->bindValue(':user_id', $userId, PDO::PARAM_INT);
$req->bindValue(':cookie_id', $cookieId, PDO::PARAM_INT);
$req->bindValue(':status_id', 1, PDO::PARAM_INT);
$req->execute();
$cart = $req->fetch();

$sql = "SELECT pro_price, pro_in_sale, pro_sale_price FROM products WHERE pro_id = :id";
$req = $db->prepare($sql);
$req->bindValue(':id', $_GET['id'], PDO::PARAM_INT);
$req->execute();
$product = $req->fetch();

// IF THE PRODUCT IS IN SALE, THE PRICE WILL BE THE pro_sale_price ELSE IT WILL BE pro_price
$proPrice = $product->pro_in_sale === 1 ? $product->pro_sale_price : $product->pro_price; // GETTING THE PRICE OF THE PRODUCT

if($cart){ // IF THE USER ALREADY HAVE A CART OPEN
    $cartId = $cart->cart_id;

    // CHECK IF THE PRODUCT IS ALREADY IN USER'S CART
    $sql = "SELECT * FROM products_carts WHERE cart_id = :cart_id AND pro_id = :pro_id";
    $req = $db->prepare($sql);
    $req->bindValue(':cart_id', $cartId, PDO::PARAM_INT);
    $req->bindValue(':pro_id', $_GET['id'], PDO::PARAM_INT);
    $req->execute();
    $productExist = $req->fetch();

    if($productExist){
        // CHECK IF THE PRICE IS CORRECT, (IF THE PRODUCT IS IN SALE)
        if($productExist->price !== $proPrice){
            $sql = "UPDATE products_carts SET price = :price WHERE pro_id = :pro_id AND cart_id = :cart_id";
            $req = $db->prepare($sql);
            $req->bindValue(':price', $proPrice, PDO::PARAM_INT);
            $req->bindValue(':pro_id', $_GET['id'], PDO::PARAM_INT);
            $req->bindValue(':cart_id', $cartId, PDO::PARAM_INT);
            $req->execute();
        }

        // IF THE PRODUCT IS ALREADY IN THE USER'S CART WE ADD 1 OF THE PRODUCT
        $sql = "UPDATE products_carts SET quantity = :quantity WHERE pro_id = :pro_id AND cart_id = :cart_id";
        $req = $db->prepare($sql);
        $req->bindValue(':quantity', $productExist->quantity + 1, PDO::PARAM_INT);
        $req->bindValue(':pro_id', $_GET['id'], PDO::PARAM_INT);
        $req->bindValue(':cart_id', $cartId, PDO::PARAM_INT);
        $req->execute();

        // UPDATING CART WITH THE NEW TOTAL
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

        // UPDATING THE CART WITH THE NEW TOTAL
        $sql = "UPDATE carts SET cart_total = :price WHERE cart_id = :cart_id";
        $req = $db->prepare($sql);
        $req->bindValue(':price', $totalPrice, PDO::PARAM_INT);
        $req->bindValue(':cart_id', $cartId, PDO::PARAM_INT);
        $req->execute();

    }else{
        // THE PRODUCT ISN'T IN THE USER'S CART
        $sql = "INSERT INTO products_carts (cart_id, pro_id, quantity, price) VALUES (:cart_id, :product_id, :quantity, :price)";
        $req = $db->prepare($sql);
        $req->bindValue(':cart_id', $cart->cart_id, PDO::PARAM_INT);
        $req->bindValue(':product_id', $_GET['id'], PDO::PARAM_INT);
        $req->bindValue(':quantity', 1, PDO::PARAM_INT);
        $req->bindValue(':price', $proPrice, PDO::PARAM_INT);
        $req->execute();

        $total = $cart->cart_total + $proPrice;

        $sql = "UPDATE carts SET cart_total = :total WHERE cart_id = :cart_id";
        $req = $db->prepare($sql);
        $req->bindValue(':total', $total, PDO::PARAM_INT);
        $req->bindValue(':cart_id', $cartId, PDO::PARAM_INT);
        $req->execute();
    }

}else{
    // THE USER DON'T HAVE AN OPEN CART YET SO WE CREATE IT
    $sql = "INSERT INTO carts (cart_date, status_id, cookie_id) VALUES (:date, :status_id, :cookie_id)";
    $userId = $_SESSION['user']['id'] ?? 0; // IF THE USER IS LOGGED, WE GET IS ID ELSE, WE PUT A 0
    $req = $db->prepare($sql);
    $req->bindValue(':date', date('Y-m-d'));
    $req->bindValue(':status_id', 1, PDO::PARAM_INT);
    $req->bindValue(':cookie_id', $cookieId);
    $req->execute();

    $cartId = $db->lastInsertId();

    // IF THE USER IS LOGGED, WE CONNECT THE USER TO THE CART
    if($userId !== 0){
        $sql = "UPDATE carts SET user_id = :user_id WHERE cart_id = :cart_id";
        $req = $db->prepare($sql);
        $req->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $req->bindValue(':cart_id', $cartId, PDO::PARAM_INT);
        $req->execute();
    }

    $sql = "INSERT INTO products_carts (cart_id, pro_id, quantity, price) VALUE (:cart_id, :product_id, :quantity, :price)";
    $req = $db->prepare($sql);
    $req->bindValue(':cart_id', $cartId, PDO::PARAM_INT);
    $req->bindValue(':product_id', $_GET['id'], PDO::PARAM_INT);
    $req->bindValue(':quantity', 1, PDO::PARAM_INT);
    $req->bindValue(':price', $proPrice, PDO::PARAM_INT);
    $req->execute();

    $total = $cart->cart_total + $proPrice;

    $sql = "UPDATE carts SET cart_total = :total WHERE cart_id = :cart_id";
    $req = $db->prepare($sql);
    $req->bindValue(':total', $total, PDO::PARAM_INT);
    $req->bindValue(':cart_id', $cartId, PDO::PARAM_INT);
    $req->execute();
}

$previousPage = $_SERVER['HTTP_REFERER'] ?? 'index.php';
header('Location: '.$previousPage); // GOING TO THE PREVIOUS PAGE