<?php
require_once './components/cartManager.php';

require_once './db.php';

if(!isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id'])){
    // NO ID
    header('location: index.php');
    die("There's no product ID");
}

if(!isset($_SESSION['user']['id'], $_SESSION['user']['type']) || empty($_SESSION['user']['id']) || !is_numeric(intval($_SESSION['user']['id'])) || !isset($_SESSION['user']['uniq']) || empty($_SESSION['user']['uniq']) || $_SESSION['user']['type'] !== 'v'){
    header('Location: search.php');
    die("The user shouldn't be here");
}

$sql = "SELECT products.pro_id FROM products LEFT JOIN sellers ON sellers.seller_id = products.seller_id WHERE products.pro_id = :id_pro AND sellers.seller_id = :id_seller AND sellers.seller_uniqid = :uniq";
$req = $db->prepare($sql);
$req->bindParam(':id_pro', $_GET['id'], PDO::PARAM_INT);
$req->bindParam(':id_seller', $_SESSION['user']['id'], PDO::PARAM_INT);
$req->bindValue(':uniq', $_SESSION['user']['uniq']);
$req->execute();
$user = $req->fetch();

// WE CHECK IF THE SELLER IS AUTHORIZED TO DELETE THE PRODUCT
if(!$user){
    header('Location: 404.php');
    die("The user shouldn't be able to delete this image");
}

// GETTING ALL IMAGES OF THE PRODUCT
$sql = "SELECT * FROM images WHERE pro_id = :id";
$req = $db->prepare($sql);
$req->bindValue(':id', $_GET['id'], PDO::PARAM_INT);
$req->execute();
$images = $req->fetchAll();

// DELETING EVERY IMAGES OF THE PRODUCT
foreach($images as $image){
    $error = false;
    if($image){ // IF THE FILE EXIST, WE PROCEED TO DELETE IT
        $sql = "DELETE FROM images WHERE img_id = :id_img";
        $req = $db->prepare($sql);
        $req->bindValue(':id_img', $image->img_id, PDO::PARAM_INT);
        if(!$req->execute()){
            $error = true;
            $_SESSION['error'] = "La suppression d'une ou plusieurs image(s) a échouée.";
        }
        if(!$error){
            if(file_exists(__DIR__."/imgs/products/".$image->img_name.".jpg")) unlink(__DIR__."/imgs/products/".$image->img_name.".jpg"); // DELETING THE FILE FROM THE FOLDER
            if(file_exists(__DIR__."/imgs/products/mini_".$image->img_name.".jpg")) unlink(__DIR__."/imgs/products/mini_".$image->img_name.".jpg"); // DELETING THE MINI FILE FROM THE FOLDER
        }
    }
}

// DELETING ALL THE RELATION WITH HIS CATEGORIES
$sql = "DELETE FROM cats_products WHERE pro_id = :id";
$req = $db->prepare($sql);
$req->bindValue(':id', $_GET['id'], PDO::PARAM_INT);
if(!$req->execute()){
    $_SESSION['error'] = "La suppression des relations avec les catégoties a rencontées un problème.";
}

// DELETING THE PRODUCT
$sql = "DELETE FROM products WHERE pro_id = :id_pro";
$req = $db->prepare($sql);
$req->bindParam(':id_pro', $_GET['id'], PDO::PARAM_INT);
if(!$req->execute()){
    $_SESSION['error'] = "Le produit n'a pas pu être supprimé.";
}

$previousPage = $_SERVER['HTTP_REFERER'] ?? 'index.php';
header('Location: '.$previousPage); // GOING TO THE PREVIOUS PAGE