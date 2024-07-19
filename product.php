<?php
require_once './components/cartManager.php';

// CHECK IF THERE'S AN ID
if(!isset($_GET['id']) || !is_numeric($_GET['id']) || empty($_GET['id'])){
    // NO ID
    header('location: ./index.php');
    exit("There's no product ID");
}

$id = strip_tags($_GET['id']);
$splide = 1;

require_once './db.php';

// SELECTING THE PRODUCT
$sql = "SELECT * FROM products WHERE pro_id = :id AND pro_is_closed = 0";
$req = $db->prepare($sql);
$req->bindValue(':id', $id, PDO::PARAM_INT);
$req->execute();
$thisProduct = $req->fetch();

$nameProduct = '404';
if($thisProduct) $nameProduct = strip_tags($thisProduct->pro_name);

$title = "Localement Suisse | $nameProduct";
include './components/header.php';
include './components/navbar.php';

// CHECK FOR THE SELLER. IF IT'S A PRODUCT OF THE SELLER MORE OPTIONS WILL BE DISPLAY
$sellerId = 0;
if(isset($_SESSION['id'], $_SESSION['type']) && !is_numeric(intval($_SESSION['id'])) && !empty($_SESSION['id']) && !empty($_SESSION['type']) && $_SESSION['type'] === 'v') $sellerId = $_SESSION['id'];

if(!$thisProduct){ // IF THERE'S NO PRODUCT
    http_response_code(404);
    ?>
    <div class="error">
        <p><?= $tr->translate("This product ID ($id) does not exist, sorry.") ?></p>
        <a href='./index.php'><?= $tr->translate("Revenir") ?></a>
    </div>
<?php
    exit();
}

// GETTING THE IMAGES OF THE PRODUCT
$sql = "SELECT * FROM images WHERE pro_id = :id ORDER BY img_first DESC";
$req = $db->prepare($sql);
$req->bindValue(':id', $thisProduct->pro_id, PDO::PARAM_INT);
$req->execute();
$images = $req->fetchAll();

// GETTING THE SELLERS DATA
$sql = "SELECT * FROM sellers WHERE seller_id = :id";
$req = $db->prepare($sql);
$req->bindValue(':id', $thisProduct->seller_id, PDO::PARAM_INT);
$req->execute();
$seller = $req->fetch();

$sellerImg = ($seller->seller_img !== '' && file_exists(__DIR__."imgs/sellers/".$seller->seller_img.".jpg")) ? "sellers/".$seller->seller_img.".jpg" : "default.jpg"; // GETTING SELLER'S IMAGE

?>

<header class="hero product-hero">
    <div class="hero-infos">
        <div class="splide" id="splide1" role="group" aria-label="Pictures of: <?= $nameProduct ?>">
            <div class="splide__track">
                <ul class="splide__list">
                    <?php if($images): ?>
                        <?php
                        foreach ($images as $img):
                            $productImage = (file_exists(__DIR__."/imgs/products/".$img->img_name.".jpg")) ? "products/".$img->img_name.".jpg" : "default.jpg";
                            $productMiniImg = (file_exists(__DIR__."/imgs/products/mini_".$img->img_name.".jpg")) ? "products/mini_".$img->img_name.".jpg" : "mini_default.jpg";
                            ?>
                            <li class="splide__slide">
                                <div class="slider-container blur-load" style="background-image: url('./imgs/<?= $productMiniImg ?>')";>
                                    <img src="./imgs/<?= $productImage ?>" alt="">
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="splide__slide">
                            <div class="slider-container">
                                <img src="./imgs/products/default.jpg" alt="There's no image for the product: <?= ucfirst(strip_tags($thisProduct->pro_name)) ?>">
                            </div>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>

    <div class="hero-infos hero-infos-more">
        <a class="seller-img" href="./seller.php?id=<?= $seller->seller_id ?>">
            <img src="./imgs/<?= strip_tags($sellerImg) ?>" alt="The seller is <?= strip_tags($seller->seller_name) ?>">
        </a>
        <h2><?= $tr->translate(ucfirst(strip_tags($thisProduct->pro_name))) ?></h2>
        <?php if(isset($_SESSION['user']['id']) && !empty($_SESSION['user']['id']) && is_numeric($_SESSION['user']['id']) && !isset($_SESSION['user']['type']) && !isset($_SESSION['user']['uniq'])): ?>
        <section>
            <?php
            $svgPath = "M225.8 468.2l-2.5-2.3L48.1 303.2C17.4 274.7 0 234.7 0 192.8v-3.3c0-70.4 50-130.8 119.2-144C158.6 37.9 198.9 47 231 69.6c9 6.4 17.4 13.8 25 22.3c4.2-4.8 8.7-9.2 13.5-13.3c3.7-3.2 7.5-6.2 11.5-9c0 0 0 0 0 0C313.1 47 353.4 37.9 392.8 45.4C462 58.6 512 119.1 512 189.5v3.3c0 41.9-17.4 81.9-48.1 110.4L288.7 465.9l-2.5 2.3c-8.2 7.6-19 11.9-30.2 11.9s-22-4.2-30.2-11.9zM239.1 145c-.4-.3-.7-.7-1-1.1l-17.8-20c0 0-.1-.1-.1-.1c0 0 0 0 0 0c-23.1-25.9-58-37.7-92-31.2C81.6 101.5 48 142.1 48 189.5v3.3c0 28.5 11.9 55.8 32.8 75.2L256 430.7 431.2 268c20.9-19.4 32.8-46.7 32.8-75.2v-3.3c0-47.3-33.6-88-80.1-96.9c-34-6.5-69 5.4-92 31.2c0 0 0 0-.1 .1s0 0-.1 .1l-17.8 20c-.3 .4-.7 .7-1 1.1c-4.5 4.5-10.6 7-16.9 7s-12.4-2.5-16.9-7z";
            $sql = "SELECT * FROM favorites WHERE user_id = :user_id AND pro_id = :pro_id";
            $req = $db->prepare($sql);
            $req->bindValue(':user_id', $_SESSION['user']['id'], PDO::PARAM_INT);
            $req->bindValue(':pro_id', $thisProduct->pro_id, PDO::PARAM_INT);
            $req->execute();
            if($req->rowCount() > 0) $svgPath = "M47.6 300.4L228.3 469.1c7.5 7 17.4 10.9 27.7 10.9s20.2-3.9 27.7-10.9L464.4 300.4c30.4-28.3 47.6-68 47.6-109.5v-5.8c0-69.9-50.5-129.5-119.4-141C347 36.5 300.6 51.4 268 84L256 96 244 84c-32.6-32.6-79-47.5-124.6-39.9C50.5 55.6 0 115.2 0 185.1v5.8c0 41.5 17.2 81.2 47.6 109.5z";
            ?>
            <a class="no-style button" href="toggleFav.php?id=<?= $thisProduct->pro_id ?>">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor" width="25">
                    <path d="<?= $svgPath ?>"/>
                </svg>
                <span><?= $tr->translate("Ajouter aux favoris") ?></span>
            </a>
        </section>
            <br>
        <?php endif; ?>
        <section class="is-flex">
            <?php
            // GETTING THE PRICE
            $price = priceFormatting($thisProduct->pro_price);
            if($thisProduct->pro_in_sale === 1):
                $price = priceFormatting($thisProduct->pro_sale_price);
            ?>
            <p class="price price-sale"><?= priceFormatting($thisProduct->pro_price) ?></p>
            <?php endif; ?>
            <h4 class="price"><?= $price ?></h4>
        </section>
        <?php if($thisProduct->seller_id === $sellerId): ?>
        <section style="margin-block: 1rem;">
            <a class="no-style button" href="edit.php?id=<?= $thisProduct->pro_id ?>"><?= $tr->translate("Modifier le produit") ?></a>
        </section>
        <?php endif; ?>
        <section>
            <a href="./addToCart.php?id=<?= $thisProduct->pro_id ?>" class="no-style button" id="addToBasket"><?= $tr->translate("Ajouter au panier") ?></a>
        </section>
    </div>
</header>

<main id="cont">
    <section class="container tag-container">
        <?php
        // GETTING THE CATEGORIES OF THE PRODUCT
        $sql = "SElECT subcategories.subcat_id, subcategories.subcat_name FROM subcategories LEFT JOIN cats_products ON subcategories.subcat_id = cats_products.subcat_id WHERE cats_products.pro_id = :id";
        $req = $db->prepare($sql);
        $req->bindValue(':id', $id, PDO::PARAM_INT);
        $req->execute();
        $categories = $req->fetchAll();
        foreach ($categories as $category):
        ?>
            <a href="./search.php?cat=<?= strip_tags($category->subcat_id) ?>" class="no-style tag"><?= $tr->translate(ucfirst(strip_tags($category->subcat_name))) ?></a>
        <?php endforeach; ?>
    </section>
    <section class="container">
        <h3><?= $tr->translate("Description") ?></h3>
        <pre style='text-wrap: wrap; font-family: "Quicksand", "OpenSans", system-ui, sans-serif;'><?= $tr->translate(strip_tags($thisProduct->pro_description)) ?></pre>
    </section>
</main>

<script src="./js/product.js" defer></script>
<?php include './components/footer.php'?>