<?php
require_once './components/cartManager.php';

// CHECK IF THERE'S AN ID
if(!isset($_SESSION['user']['id']) || !is_numeric($_SESSION['user']['id']) || empty($_SESSION['user']['id'])){
    // NO ID
    header('location: ./index.php');
    die("There's no account ID");
}

if(isset($_SESSION['user']['type']) && $_SESSION['user']['type'] === 'v'){
    header('location: ./sellerAccount.php');
    die();
}

$id = $_SESSION['user']['id'];

require_once './db.php';

$sql = "SELECT * FROM users WHERE user_id = :id";
$req = $db->prepare($sql);
$req->bindValue(':id', $id, PDO::PARAM_INT);
$req->execute();
$user = $req->fetch();


$userName = '404';
if($user) $userName = strip_tags($user->user_pseudo);

$title = "Localement Suisse | $userName";
include './components/header.php';
include './components/navbar.php';

if(!$user){
    http_response_code(404);
    ?>
    <div class="error">
        <p><?= $tr->translate("Cette id d'utilisateur ($id) n'existe pas, désolé.") ?></p>
        <a href='./index.php'><?= $tr->translate("Revenir") ?></a>
    </div>
    <?php
    exit();
}
?>

<header class="hero">
    <div class="hero-infos hero-infos-more">
        <h1><?= $userName ?></h1>
        <?php if($user->typ_id === 2): ?>
        <a href="./adminLogin.php" class="hero-link" style="--_link-color: var(--clr-800);">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor" height="20">
                <path d="M96 128a128 128 0 1 0 256 0A128 128 0 1 0 96 128zm94.5 200.2l18.6 31L175.8 483.1l-36-146.9c-2-8.1-9.8-13.4-17.9-11.3C51.9 342.4 0 405.8 0 481.3c0 17 13.8 30.7 30.7 30.7H162.5c0 0 0 0 .1 0H168 280h5.5c0 0 0 0 .1 0H417.3c17 0 30.7-13.8 30.7-30.7c0-75.5-51.9-138.9-121.9-156.4c-8.1-2-15.9 3.3-17.9 11.3l-36 146.9L238.9 359.2l18.6-31c6.4-10.7-1.3-24.2-13.7-24.2H224 204.3c-12.4 0-20.1 13.6-13.7 24.2z"/>
            </svg>
            <span><?= $tr->translate("Tableau Admin") ?></span>
        </a>
        <br>
        <?php endif; ?>
        <a href="./logout.php" class="hero-link" style="--_link-color: var(--clr-800);">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" fill="currentColor" height="20">
                <path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z"/>
            </svg>
            <span><?= $tr->translate("Se déconnecter") ?></span>
        </a>
    </div>
</header>

<main id="cont">
    <section>
        <h3><?= $tr->translate("Vos favoris") ?></h3>
        <?php
        $sql = "SELECT * FROM favorites WHERE user_id = :id";
        $req = $db->prepare($sql);
        $req->bindValue(':id', $id, PDO::PARAM_INT);
        $req->execute();
        $favorites = $req->fetchAll();
        if(count($favorites) > 0):
            ?>
        <ul class="section-cont section-cont-grid">
        <?php
            foreach($favorites as $favorite):
                $sql = "SELECT * FROM products WHERE pro_id = :id";
                $req = $db->prepare($sql);
                $req->bindValue(':id', $favorite->pro_id, PDO::PARAM_INT);
                $req->execute();
                $product = $req->fetch();

                $proName = $tr->translate(ucfirst(strip_tags($product->pro_name)));

                $sql = "SELECT * FROM images WHERE pro_id = :id AND img_first = 1";
                $req = $db->prepare($sql);
                $req->bindValue(':id', $favorite->pro_id, PDO::PARAM_INT);
                $req->execute();
                $images = $req->fetch();
                $imgLink = ($images && file_exists(__DIR__."/imgs/products/".$images->img_name.".jpg")) ? "products/".$images->img_name.".jpg" : "default.jpg";
                $miniImgLink = ($images && file_exists(__DIR__."/imgs/products/mini_".$images->img_name.".jpg")) ? "products/mini_".$images->img_name.".jpg" : "mini_default.jpg";
        ?>
                <li class="splide__slide">
                    <a href="./product.php?id=<?= $product->pro_id ?>" class="slider-container" title="<?= $proName ?>">
                        <div class="product-img blur-load" style="background-image: url('./imgs/<?= $miniImgLink ?>');">
                            <img src="./imgs/<?= $imgLink ?>" alt="" loading="lazy">
                        </div>
                        <div class="slide-content">
                            <p class="slide-title"><strong><?= $proName ?></strong></p>
                            <p class="price"><?= substr_replace(strip_tags($product->pro_price), '.', -2, 0) ?></p>
                        </div>
                    </a>
                </li>
        <?php endforeach; ?>
        </ul>
        <?php else: ?>
        <p><?= $tr->translate("Vous n'avez pas de produits dans vos favoris.") ?></p>
        <?php endif; ?>
    </section>
</main>

<?php include './components/footer.php'; ?>