<?php
require_once './components/cartManager.php';

require_once './db.php';

if(!isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id'])){
    // NO ID
    header('location: index.php');
    die("There's no image ID");
}

if(!isset($_SESSION['user']['id'], $_SESSION['user']['type']) || empty($_SESSION['user']['id']) || !is_numeric(intval($_SESSION['user']['id'])) || !isset($_SESSION['user']['uniq']) || empty($_SESSION['user']['uniq']) || $_SESSION['user']['type'] !== 'v'){
    header('Location: index.php');
    die("The user shouldn't be here");
}

// CHECKING IF THE SELLER IS AUTHORIZED TO DELETE THAT IMAGE
$sql = "SELECT * FROM sellers LEFT JOIN products ON products.seller_id = sellers.seller_id LEFT JOIN images ON images.pro_id = products.pro_id WHERE images.img_id = :img_id AND sellers.seller_id = :seller_id AND sellers.seller_uniqid = :uniq";
$req = $db->prepare($sql);
$req->bindValue(':img_id', $_GET['id'], PDO::PARAM_INT);
$req->bindValue(':seller_id', $_SESSION['user']['id'], PDO::PARAM_INT);
$req->bindValue(':uniq', $_SESSION['user']['uniq']);
$req->execute();

if($req->rowCount() === 0){
    header('Location: index.php');
    die("The user shouldn't be able to delete this image");
}

// GETTING THE FILE
$sql = "SELECT * FROM images WHERE img_id = :id";
$req = $db->prepare($sql);
$req->bindValue(':id', $_GET['id'], PDO::PARAM_INT);
$req->execute();
$image = $req->fetch();

if($image){
    // IF THE FILE EXIST, WE PROCEED TO DELETE IT FROM THE FOLDER
    if(file_exists(__DIR__."/imgs/products/".$image->img_name.".jpg")) unlink(__DIR__."/imgs/products/".$image->img_name.".jpg");
    if(file_exists(__DIR__."/imgs/products/mini_".$image->img_name.".jpg")) unlink(__DIR__."/imgs/products/mini_".$image->img_name.".jpg");

    // IF THE FILE EXIST, WE PROCEED TO DELETE IT FROM THE DB
    $sql = "DELETE FROM images WHERE img_id = :id_img";
    $req = $db->prepare($sql);
    $req->bindValue(':id_img', $image->img_id, PDO::PARAM_INT);
    if(!$req->execute()){
        $_SESSION['error'] = "L'image n'a pas pu être supprimé.";
    }
}

$previousPage = $_SERVER['HTTP_REFERER'] ?? 'index.php';
header('Location: '.$previousPage); // WE GO TO THE PREVIOUS PAGE