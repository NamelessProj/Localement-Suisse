<?php
require_once './components/cartManager.php';

require_once './db.php';

// CHECK IF THE USER IS LOGIN AND AN ADMIN
if(isset($_SESSION['user']['admin']) && !empty($_SESSION['user']['admin']) && isset($_SESSION['user']['id']) && !empty($_SESSION['user']['id']) && is_numeric($_SESSION['user']['id'])){
    // IF THE ADMIN SESSION IS ALREADY OPEN, WE CHECK IF THE USER IS AN ADMIN
    $sql = "SELECT * FROM users WHERE user_id = :user_id AND typ_id = :type_id AND user_uniqid = :uniqid";
    $req = $db->prepare($sql);
    $req->bindValue(':user_id', $_SESSION['user']['id'], PDO::PARAM_INT);
    $req->bindValue(':type_id', 2, PDO::PARAM_INT);
    $req->bindValue(':uniqid', $_SESSION['user']['admin']);
    $req->execute();
    $user = $req->fetch();
    if(!$user){
        header("Location: index.php");
        die("The user is not an admin.");
    }
}else{
    header("Location: index.php");
    die("The user is not an admin.");
}

// CHECK IF THERE'S AN ID
if(isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id'])){
    $id = $_GET['id'];

    // IF THERE'S AN ID, WE CHECK IF THE SELLER IS ACTIVATE OR NOT
    $sql = "SELECT seller_is_activated FROM sellers WHERE seller_id = :id";
    $req = $db->prepare($sql);
    $req->bindValue(':id', $id, PDO::PARAM_INT);
    $req->execute();
    $seller = $req->fetch();

    if($seller){ // CHECK IF THERE'S ACTUALLY A SELLER WITH THAT ID
        $activeCode = $seller->seller_is_activated === 1 ? 0 : 1; // IF IS ACTIVATE, WE DEACTIVATE HIM AND REVERSE

        $sql = "UPDATE sellers SET seller_is_activated = :activateCode WHERE seller_id = :id";
        $req = $db->prepare($sql);
        $req->bindValue(':activateCode', $activeCode, PDO::PARAM_INT);
        $req->bindValue(':id', $id, PDO::PARAM_INT);
        $req->execute();
    }
}

$previousPage = $_SERVER['HTTP_REFERER'] ?? 'index.php';
header('Location: '.$previousPage); // GOING TO THE PREVIOUS PAGE