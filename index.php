<?php
require_once './components/cartManager.php';
require_once './db.php';

$title = "Localement Suisse | Home";
$splide = 1;
include './components/header.php';
include './components/navbar.php';

$seed = date("dmY"); // SEED FOR THE RANDOM SELECTION OF THE DAY

// FUNCTION TO GENERATE A RANDOM NUMBER BASED ON A SEED
function randomNumberGenerator($min, $max, $seed = 0):int{
    if($seed > 0) srand($seed);
    return mt_rand($min, $max);
}

$todayDate = date("Y-m-d");
// COUNTING ALL THE PRODUCTS TO KNOW WHICH ONE CAN BE DISPLAYED FOR THE DAY SELECTION
$sql = "SELECT count(*) FROM products LEFT JOIN sellers ON sellers.seller_id = products.seller_id WHERE pro_is_closed = 0 AND sellers.seller_is_activated = 1 AND sellers.seller_is_closed = 0 AND products.pro_stock > 0 AND products.pro_date_added < :today";
$req = $db->prepare($sql);
$req->bindValue(":today", $todayDate);
$req->execute();
$totalProducts = $req->fetchColumn();

if($totalProducts > 9) $totalProducts = $totalProducts - 9; // SO THERE WILL BE 10 PRODUCTS

$randomNumber = randomNumberGenerator(1, $totalProducts, $seed); // MIN & MAX ARE INCLUDED IN THE POSSIBILITIES

// SELECTING TODAY'S SPECIAL SELECTION
$sql = "SELECT * FROM products LEFT JOIN sellers ON sellers.seller_id = products.seller_id LEFT JOIN images ON products.pro_id = images.pro_id WHERE products.pro_is_closed = 0 AND products.pro_id >= :minId AND sellers.seller_is_activated = 1 AND sellers.seller_is_closed = 0 AND products.pro_stock > 0 AND products.pro_date_added < :today AND images.img_first = 1 ORDER BY products.pro_id DESC LIMIT 10";
$req = $db->prepare($sql);
$req->bindValue(":minId", $randomNumber, PDO::PARAM_INT);
$req->bindValue(":today", $todayDate);
$req->execute();
$products = $req->fetchAll();

?>

<header class="hero hero-primary" style="--_opacity: .5;">
    <picture class="bg-img">
        <img src="./imgs/mount_01.jpg" alt="">
    </picture>
    <div class="hero-content">
        <h1><?= $tr->translate("Consommer local dans toute la") ?> <span class="important"><?= $tr->translate("Suisse") ?></span></h1>
        <div class="description">
            <p><?= $tr->translate("Bienvenue sur le plus grand marché artisanal qui présente toutes les variétés de produits différents disponibles pour vous aider à consommer suisse au quotidien.") ?></p>
            <p><?= $tr->translate("Commandez en quelques clics et faites vous livrer des produits de qualité en mains propres en Suisse.") ?></p>
        </div>
    </div>
</header>

<main id="cont">
    <!-- ======== DAILY SELECTION ======== -->
    <section>
        <h2><?= $tr->translate("La sélection du jour") ?></h2>
        <div class="splide" id="splide1" role="group" aria-label="Daily selection ">
            <div class="splide__track">
                <ul class="splide__list">
                    <?php
                    // DISPLAY TODAY SELECTION
                    foreach ($products as $product):
                        $proName = $tr->translate(strip_tags($product->pro_name));

                        $imgLink = ($product->img_name !== '' && file_exists(__DIR__."/imgs/products/".$product->img_name.".jpg")) ? "products/".$product->img_name.".jpg" : "default.jpg";
                        $miniImgLink = ($product->img_name !== '' && file_exists(__DIR__."/imgs/products/mini_".$product->img_name.".jpg")) ? "products/mini_".$product->img_name.".jpg" : "mini_default.jpg";
                        ?>
                    <li class="splide__slide">
                        <a href="./product.php?id=<?= $product->pro_id ?>" class="slider-container" title="<?= $proName ?>">
                            <div class="product-img blur-load" style="background-image: url('./imgs/<?= $miniImgLink ?>');">
                                <img src="./imgs/<?= $imgLink ?>" alt="<?= strip_tags($product->pro_name) ?>">
                            </div>
                            <div class="slide-content">
                                <p class="slide-title"><?= $proName ?></p>
                                <?php
                                // GETTING THE PRICE
                                $price = $product->pro_in_sale === 1 ? $product->pro_sale_price : $product->pro_price;
                                if($product->pro_in_sale === 1):
                                ?>
                                <small class="price price-sale"><?= priceFormatting($product->pro_price) ?></small>
                                <?php endif; ?>
                                <p class="price"><?= priceFormatting($price) ?></p>
                            </div>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </section>



    <!-- ======== LAST ARRIVED ======== -->
    <section>
        <h2><?= $tr->translate("Les derniers arrivés") ?></h2>
        <div class="splide" id="splide2" role="group" aria-label="Lasts added products">
            <div class="splide__track">
                <ul class="splide__list">
                    <?php
                    $sql = "SELECT * FROM products LEFT JOIN sellers ON sellers.seller_id = products.seller_id LEFT JOIN images ON images.pro_id = products.pro_id WHERE products.pro_is_closed = 0 AND sellers.seller_is_activated = 1 AND images.img_first = 1 ORDER BY products.pro_date_added DESC LIMIT 10";
                    $req = $db->query($sql);
                    $products = $req->fetchAll();
                    foreach ($products as $product):
                        $proName = $tr->translate(strip_tags($product->pro_name));

                        $imgLink = ($product->img_name !== '' && file_exists(__DIR__."/imgs/products/".$product->img_name.".jpg")) ? "products/".$product->img_name.".jpg" : "default.jpg";
                        $miniImgLink = ($product->img_name !== '' && file_exists(__DIR__."/imgs/products/mini_".$product->img_name.".jpg")) ? "products/mini_".$product->img_name.".jpg" : "mini_default.jpg";
                    ?>
                    <li class="splide__slide">
                        <a href="./product.php?id=<?= $product->pro_id ?>" class="slider-container" title="<?= $proName ?>">
                            <div class="product-img blur-load" style="background-image: url('./imgs/<?= $miniImgLink ?>');">
                                <img src="./imgs/<?= $imgLink ?>" alt="<?= strip_tags($product->pro_name) ?>">
                            </div>
                            <div class="slide-content">
                                <p class="slide-title"><strong><?= $proName ?></strong></p>
                                <p class="price"><?= priceFormatting($product->pro_price) ?></p>
                            </div>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </section>
</main>

<script src="./js/home.js" defer></script>
<?php include './components/footer.php'?>