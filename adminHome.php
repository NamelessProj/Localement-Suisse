<?php
require_once './components/cartManager.php';

$adminCurrentPage = "Accueil";
$title = "Localement Suisse | Admin - ".$adminCurrentPage;
include './components/header.php';

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

include './components/navbar.php';


// GETTING EVERYTHING FOR THE STATS

// NUMBERS OF PRODUCTS
$sql = "SELECT COUNT(*) AS count FROM products LEFT JOIN sellers ON sellers.seller_id = products.seller_id WHERE pro_is_closed = 0 AND sellers.seller_is_activated = 1 AND sellers.seller_is_closed = 0 AND products.pro_stock > 0";
$req = $db->prepare($sql);
$req->execute();
$nbProducts = $req->fetch()->count;

// NUMBERS OF SELLERS
$sql = "SELECT COUNT(*) AS count FROM sellers WHERE sellers.seller_is_activated = 1 AND sellers.seller_is_closed = 0";
$req = $db->prepare($sql);
$req->execute();
$nbSellers = $req->fetch()->count;

// NUMBERS OF USERS
$sql = "SELECT COUNT(*) AS count FROM users";
$req = $db->prepare($sql);
$req->execute();
$nbUsers = $req->fetch()->count;

?>

<header class="hero">
    <div class="hero-infos">
        <h1><?= $tr->translate("Tableau de bords") ?></h1>
    </div>
</header>

<main id="cont">
    <section>
        <?php include './components/adminNavbar.php'; ?>
    </section>

    <section>
        <h3><?= $tr->translate("Statistiques") ?></h3>
        <br>
        <ul class="cards-list">
            <li class="card">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" fill="currentColor" height="30">
                    <path d="M253.3 35.1c6.1-11.8 1.5-26.3-10.2-32.4s-26.3-1.5-32.4 10.2L117.6 192H32c-17.7 0-32 14.3-32 32s14.3 32 32 32L83.9 463.5C91 492 116.6 512 146 512H430c29.4 0 55-20 62.1-48.5L544 256c17.7 0 32-14.3 32-32s-14.3-32-32-32H458.4L365.3 12.9C359.2 1.2 344.7-3.4 332.9 2.7s-16.3 20.6-10.2 32.4L404.3 192H171.7L253.3 35.1zM192 304v96c0 8.8-7.2 16-16 16s-16-7.2-16-16V304c0-8.8 7.2-16 16-16s16 7.2 16 16zm96-16c8.8 0 16 7.2 16 16v96c0 8.8-7.2 16-16 16s-16-7.2-16-16V304c0-8.8 7.2-16 16-16zm128 16v96c0 8.8-7.2 16-16 16s-16-7.2-16-16V304c0-8.8 7.2-16 16-16s16 7.2 16 16z"/>
                </svg>
                <h3 class="card-title"><?= $tr->translate("Nombre de produits") ?></h3>
                <p class="card-text"><?= number_format($nbProducts, 0, '.', ' ') ?></p>
            </li>
            <li class="card">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor" height="30">
                    <path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z"/>
                </svg>
                <h3 class="card-title"><?= $tr->translate("Nombre de vendeurs") ?></h3>
                <p class="card-text"><?= number_format($nbSellers, 0, '.', ' ') ?></p>
            </li>
            <li class="card">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"  fill="currentColor" height="30">
                    <path d="M96 128a128 128 0 1 1 256 0A128 128 0 1 1 96 128zM0 482.3C0 383.8 79.8 304 178.3 304h91.4C368.2 304 448 383.8 448 482.3c0 16.4-13.3 29.7-29.7 29.7H29.7C13.3 512 0 498.7 0 482.3zM609.3 512H471.4c5.4-9.4 8.6-20.3 8.6-32v-8c0-60.7-27.1-115.2-69.8-151.8c2.4-.1 4.7-.2 7.1-.2h61.4C567.8 320 640 392.2 640 481.3c0 17-13.8 30.7-30.7 30.7zM432 256c-31 0-59-12.6-79.3-32.9C372.4 196.5 384 163.6 384 128c0-26.8-6.6-52.1-18.3-74.3C384.3 40.1 407.2 32 432 32c61.9 0 112 50.1 112 112s-50.1 112-112 112z"/>
                </svg>
                <h3 class="card-title"><?= $tr->translate("Nombre de clients") ?></h3>
                <p class="card-text"><?= number_format($nbUsers, 0, '.', ' ') ?></p>
            </li>
        </ul>
    </section>
</main>

<?php include './components/footer.php'; ?>
