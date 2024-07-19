<?php
require_once './components/cartManager.php';

// CHECK IF THERE'S AN ID
if(!isset($_GET['id']) || !is_numeric(intval($_GET['id'])) || empty($_GET['id'])){
    // NO ID
    header('location: ./sellers.php');
    exit("There's no seller ID");
}

$id = strip_tags($_GET['id']);

require_once './db.php';

// GETTING DATA OF THE SELLER
$sql = "SELECT * FROM sellers WHERE seller_id = :id AND seller_is_closed = 0";
$req = $db->prepare($sql);
$req->bindValue(':id', $id, PDO::PARAM_INT);
$req->execute();
$seller = $req->fetch();

// GETTING THE NUMBER OF PRODUCT THE SELLER HAS
$sql = "SELECT COUNT(*) AS count FROM products WHERE seller_id = :id AND pro_is_closed = :closed_code AND pro_stock > :min_stock";
$req = $db->prepare($sql);
$req->bindValue(':id', $id, PDO::PARAM_INT);
$req->bindValue(':closed_code', 0, PDO::PARAM_INT);
$req->bindValue(':min_stock', 0, PDO::PARAM_INT);
$req->execute();
$nbTotalProducts = $req->fetch()->count; // WE GET THE NUMBER OF PRODUCTS THAT CORRESPOND THE QUERY

$product_per_page = 5; // NUMBER OF PRODUCT PER PAGE
$pages_count = ceil($nbTotalProducts / $product_per_page); // WE GET THE NUMBER OF PAGE MAX

$page = 1; // SETTING THE CURRENT PAGE TO 1
if(isset($_GET['page']) && !empty($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 1) $page = $_GET['page']; // IF A PAGE NUMBER IS GIVEN FROM THE $_GET, WE CHECK IF IT'S A VALID NUMBER AND IF IT IS, WE MAKE IT THE CURRENT PAGE NUMBER
if(is_numeric($page) && $page > $pages_count){
    $error_msg = "Cette page n'est pas valide.";
}

// WE DEFINE THE LIMIT FOR THE SQL QUERY
// IF WE'RE AT THE FIRST PAGE, WE WANT THE FIRST ELEMENT TO SHOW SO THE LIMIT IS SET TO 0
// IF WE'RE NOT AT THE FIRST PAGE, WE NEED TO SUBTRACT 1 TO THE PAGE NUMBER TO GET THE FIRST ELEMENT OF THE PAGE AND THEN MULTIPLY THAT BY THE NUMBER OF PRODUCT PER PAGE TO GET THE PRODUCT OF THE CURRENT PAGE
$limit = $page === 1 ? 0 : ($page - 1) * $product_per_page;

// GETTING ALL THE SELLER'S PRODUCTS
$sql = "SELECT * FROM products WHERE seller_id = :id AND pro_is_closed = 0 AND pro_stock > 0 ORDER BY pro_date_added DESC LIMIT :limit, :limitPerPage";
$req = $db->prepare($sql);
$req->bindValue(':id', $id, PDO::PARAM_INT);
$req->bindValue(':limit', $limit, PDO::PARAM_INT);
$req->bindValue(':limitPerPage', $product_per_page, PDO::PARAM_INT);
$req->execute();
$products = $req->fetchAll();

$nameSeller = '404';
if($seller) $nameSeller = ucfirst(strip_tags($seller->seller_name));

$title = "Localement Suisse | $nameSeller";
include './components/header.php';
include './components/navbar.php';

if(!$seller){
    // THERE'S NO SELLER
    http_response_code(404);
?>
    <div class="error">
        <p><?= $tr->translate("Le vendeur avec l'id ($id) n'existe pas.") ?></p>
        <a href='./sellers.php'><?= $tr->translate("Revenir") ?></a>
    </div>
<?php
    exit();
}

// SETTING THE SELLER'S IMAGE
$sellerImg = ($seller->seller_img && file_exists(__DIR__."/imgs/sellers/".$seller->seller_img.".jpg")) ? "sellers/".$seller->seller_img.".jpg" : "default.jpg";
$sellerMiniImg = ($seller->seller_img && file_exists(__DIR__."/imgs/sellers/mini_".$seller->seller_img.".jpg")) ? "sellers/mini_".$seller->seller_img.".jpg" : "mini_default.jpg";

?>

<header class="hero <?php if($page > 1){echo "hero-minimized";} ?>">
    <div class="hero-infos hero-infos<?php if($page === 1){echo "-more";} ?>">
        <?php if($page === 1): ?>
        <div class="blur-load" style="background-image: url('<?= $sellerMiniImg ?>');">
            <img src="./imgs/<?= strip_tags($sellerImg) ?>" alt="" style="height: 100%;">
        </div>
        <?php endif; ?>
        <h1><?= $nameSeller ?></h1>
    </div>

    <?php if($page === 1): ?>
    <div class="hero-infos hero-infos-more">
        <?php if($seller->seller_address_visible === 1): ?>
        <p>
            <?= $trAuto->translate(strip_tags($seller->seller_address_canton).", ".strip_tags($seller->seller_address_city).", ".strip_tags($seller->seller_address_street)) ?>
        </p>
        <?php endif; ?>
        <p>
            <?php
            // SETTING ALL THE SELLER'S LINKS
            $links = $seller->seller_socials;
            $links = explode(' ', $links);
            $linkText = $tr->translate("Lien");
            foreach($links as $link):
            ?>
                <a href="<?= strip_tags($link) ?>" target="_blank" style="--_link-color: var(--clr-800); display: flex; align-items: center; width: fit-content; font-weight: 700">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" fill="currentColor" height="20">
                        <path d="M579.8 267.7c56.5-56.5 56.5-148 0-204.5c-50-50-128.8-56.5-186.3-15.4l-1.6 1.1c-14.4 10.3-17.7 30.3-7.4 44.6s30.3 17.7 44.6 7.4l1.6-1.1c32.1-22.9 76-19.3 103.8 8.6c31.5 31.5 31.5 82.5 0 114L422.3 334.8c-31.5 31.5-82.5 31.5-114 0c-27.9-27.9-31.5-71.8-8.6-103.8l1.1-1.6c10.3-14.4 6.9-34.4-7.4-44.6s-34.4-6.9-44.6 7.4l-1.1 1.6C206.5 251.2 213 330 263 380c56.5 56.5 148 56.5 204.5 0L579.8 267.7zM60.2 244.3c-56.5 56.5-56.5 148 0 204.5c50 50 128.8 56.5 186.3 15.4l1.6-1.1c14.4-10.3 17.7-30.3 7.4-44.6s-30.3-17.7-44.6-7.4l-1.6 1.1c-32.1 22.9-76 19.3-103.8-8.6C74 372 74 321 105.5 289.5L217.7 177.2c31.5-31.5 82.5-31.5 114 0c27.9 27.9 31.5 71.8 8.6 103.9l-1.1 1.6c-10.3 14.4-6.9 34.4 7.4 44.6s34.4 6.9 44.6-7.4l1.1-1.6C433.5 260.8 427 182 377 132c-56.5-56.5-148-56.5-204.5 0L60.2 244.3z"/>
                    </svg>
                    <span>&nbsp;<?= $linkText ?></span>
                </a>
            <?php endforeach; ?>
        </p>
    </div>
    <?php endif; ?>
</header>

<main id="cont">
    <?php if($page === 1): ?>
    <section class="seller-description text-content">
        <h3><?= $tr->translate("Biographie") ?></h3>
        <?php
        $Parsedown = new Parsedown();
        $Parsedown->setSafeMode(true);
        ?>
        <?= $Parsedown->text($trAuto->translate(strip_tags($seller->seller_bio))) ?>
    </section>
    <?php endif; ?>

    <section>
        <?php if($products): ?>
        <ul class="section-cont section-cont-grid">
            <?php
            // DISPLAYING ALL THE PRODUCTS OF THE SELLER
            foreach ($products as $product):
                $sql = "SELECT * FROM images WHERE pro_id = :pro_id AND img_first = 1 LIMIT 1";
                $req = $db->prepare($sql);
                $req->bindValue(':pro_id', $product->pro_id, PDO::PARAM_INT);
                $req->execute();
                $images = $req->fetch();

                $proName = $tr->translate(ucfirst(strip_tags($product->pro_name)));

                // SETTING THE PRODUCT IMAGE
                $imgLink = ($images && file_exists(__DIR__."/imgs/products/".$images->img_name.".jpg")) ? "products/".$images->img_name.".jpg" : "default.jpg";
                $productMiniImg = ($images && file_exists(__DIR__."/imgs/products/mini_".$images->img_name.".jpg")) ? "products/mini_".$images->img_name.".jpg" : "mini_default.jpg";
                ?>
                <li class="splide__slide">
                    <a href="./product.php?id=<?= $product->pro_id ?>" class="slider-container" title="<?= $proName ?>">
                        <div class="product-img blur-load" style="background-image: url('./imgs/<?= $productMiniImg ?>');">
                            <img src="./imgs/<?= $imgLink ?>" alt="" loading="lazy">
                        </div>
                        <div class="slide-content">
                            <p class="slide-title"><strong><?= $proName ?></strong></p>
                            <p class="price"><?= priceFormatting($product->pro_price) ?></p>
                        </div>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
        <?php else: ?>
            <p style="margin: 0 auto; text-align: center; text-wrap: balance;"><?= $tr->translate($error_msg) ?></p>
        <?php endif; ?>
    </section>

    <section>
        <?php
        $linkPath = "seller.php?id=".$seller->seller_id."&";
        include './components/pagination.php';
        ?>
    </section>
</main>

<?php include './components/footer.php'; ?>