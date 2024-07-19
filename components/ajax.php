<?php
require_once '../db.php';

if(isset($_GET['search']) && !empty($_GET['search'])){
    $query = strip_tags($_GET['search']);
    $query = strtolower($query);

    $sql = "SELECT products.pro_name FROM products LEFT JOIN sellers ON sellers.seller_id = products.pro_id WHERE LOWER(products.pro_name) LIKE :query AND pro_is_closed = 0 AND pro_stock > 0 AND sellers.seller_is_closed = 0 LIMIT 10";
    $req = $db->prepare($sql);
    $req->bindValue(':query', '%'.$query.'%');
    $req->execute();
    $products = $req->fetchAll();
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($products);
}