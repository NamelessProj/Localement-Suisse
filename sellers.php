<?php
require_once './components/cartManager.php';

require_once './db.php';

$title = "Localement Suisse | Sellers";
include './components/header.php';
include './components/navbar.php';

$error_msg = "Aucun, tout les vendeurs sont activés.";

$product_per_page = 1; // NUMBER OF SELLERS PER PAGE

$sql = "SELECT COUNT(*) AS count FROM sellers WHERE seller_is_activated = :activatedCode AND seller_is_closed = :closedCode";
$req = $db->prepare($sql);
$req->bindValue(':activatedCode', 1, PDO::PARAM_INT);
$req->bindValue(':closedCode', 0, PDO::PARAM_INT);
$req->execute();
$nbTotalProducts = $req->fetch()->count; // WE GET THE NUMBER OF SELLERS THAT CORRESPOND THE QUERY

$pages_count = ceil($nbTotalProducts / $product_per_page); // WE GET THE NUMBER OF PAGE MAX

$page = 1; // SETTING THE CURRENT PAGE TO 1
if(isset($_GET['page']) && !empty($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 1) $page = $_GET['page']; // IF A PAGE NUMBER IS GIVEN FROM THE $_GET, WE CHECK IF IT'S A VALID NUMBER AND IF IT IS, WE MAKE IT THE CURRENT PAGE NUMBER
if(is_numeric($page) && $page > $pages_count){
    $text = $pages_count > 1 ? "pages" : "page";
    $error_msg = "Cette page n'est pas valide. Il n'y a que ".$pages_count." ".$text.".";
}

// WE DEFINE THE LIMIT FOR THE SQL QUERY
// IF WE'RE AT THE FIRST PAGE, WE WANT THE FIRST ELEMENT TO SHOW SO THE LIMIT IS SET TO 0
// IF WE'RE NOT AT THE FIRST PAGE, WE NEED TO SUBTRACT 1 TO THE PAGE NUMBER TO GET THE FIRST ELEMENT OF THE PAGE AND THEN MULTIPLY THAT BY THE NUMBER OF PRODUCT PER PAGE TO GET THE PRODUCT OF THE CURRENT PAGE
$limit = $page === 1 ? 0 : ($page - 1) * $product_per_page;

// GETTING ALL THE NON-ACTIVATE SELLERS
$sql = "SELECT sellers.seller_name, sellers.seller_id, sellers.seller_img, count(products.pro_id) AS count FROM sellers LEFT JOIN products ON sellers.seller_id = products.seller_id WHERE seller_is_activated = :activatedCode AND seller_is_closed = :closedCode AND products.pro_is_closed = :pro_close AND products.pro_stock > :pro_stock GROUP BY sellers.seller_id ORDER BY seller_date_added DESC LIMIT :limit, :limitPerPage";
$req = $db->prepare($sql);
$req->bindValue(':activatedCode', 1, PDO::PARAM_INT);
$req->bindValue(':closedCode', 0, PDO::PARAM_INT);
$req->bindValue(':pro_close', 0, PDO::PARAM_INT);
$req->bindValue(':pro_stock', 0, PDO::PARAM_INT);
$req->bindValue(':limit', $limit, PDO::PARAM_INT);
$req->bindValue(':limitPerPage', $product_per_page, PDO::PARAM_INT);
$req->execute();
$sellers = $req->fetchAll();

?>

<header class="hero <?php if($page > 1){ echo "hero-minimized"; } ?>">
    <div class="hero-content hero-infos">
        <h1><?= $tr->translate("Tous nos") ?> <span class="important"><?= $tr->translate("Vendeurs") ?></span></h1>
    </div>
</header>

<main id="cont">
    <section>
        <?php if($sellers): ?>
        <ul class="section-cont section-cont-grid">
            <?php
            // DISPLAYING ALL THE SELLERS
            foreach ($sellers as $seller):
                // SETTING THE SELLER'S IMAGE
                $sellerImg = ($seller->seller_img && file_exists(__DIR__."/imgs/sellers/".$seller->seller_img.".jpg")) ? "sellers/".$seller->seller_img.".jpg" : "default.jpg";
                $sellerMiniImg = ($seller->seller_img && file_exists(__DIR__."/imgs/sellers/mini_".$seller->seller_img.".jpg")) ? "sellers/mini_".$seller->seller_img.".jpg" : "mini_default.jpg";
                ?>
            <li class="splide__slide">
                <a href="./seller.php?id=<?= $seller->seller_id ?>" class="slider-container all-visible">
                    <div class="product-img blur-load" style="background-image: url('./imgs/'<?= $sellerMiniImg ?>);">
                        <img src="./imgs/<?= strip_tags($sellerImg) ?>" alt="">
                    </div>
                    <div class="slide-content">
                        <p class="slide-title"><strong><?= ucfirst(strip_tags($seller->seller_name)) ?></strong></p>
                        <p>
                            <?php
                            $text = 'produit';
                            if($seller->count > 1) $text .= 's'; // ADDING AN "S" IF THERE'S MORE THAN 1 PRODUCT
                            echo number_format($seller->count, 0, '.', ' ')." ".$tr->translate($text);
                            ?>
                        </p>
                    </div>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php else: ?>
        <p><?= $error_msg ?></p>
        <?php
        endif;

        // PAGINATION SYSTEM
        $linkPath = "sellers.php?";

        include './components/pagination.php';
        ?>
    </section>
</main>

<?php include './components/footer.php'?>