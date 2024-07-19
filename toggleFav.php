<?php
require_once './components/cartManager.php';

require_once './db.php';

if(!isset($_SESSION['user']['id']) || empty($_SESSION['user']['id']) || !is_numeric($_SESSION['user']['id'])){
    // NO ID
    header('location: index.php');
    die("There's no user ID");
}

if(!isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id'])){
    // NO ID
    header('location: index.php');
    die("There's no product ID");
}

$sql = "SELECT * FROM favorites WHERE user_id = :user_id AND pro_id = :product_id";
$req = $db->prepare($sql);
$req->bindValue(':user_id', $_SESSION['user']['id'], PDO::PARAM_INT);
$req->bindValue(':product_id', $_GET['id'], PDO::PARAM_INT);
$req->execute();
$product = $req->fetch();

// IF THE PRODUCT IS ALREADY IN THE FAVORITES, WE REMOVE IT
$sql = $product ? "DELETE FROM favorites WHERE user_id = :user_id AND pro_id = :product_id" : "INSERT INTO favorites (user_id, pro_id) VALUES (:user_id, :product_id)";
$req = $db->prepare($sql);
$req->bindValue(':user_id', $_SESSION['user']['id'], PDO::PARAM_INT);
$req->bindValue(':product_id', $_GET['id'], PDO::PARAM_INT);
$req->execute();

$previousPage = $_SERVER['HTTP_REFERER'] ?? 'index.php';
header('Location: '.$previousPage); // GOING TO THE PREVIOUS PAGE