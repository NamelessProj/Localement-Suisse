<?php
require_once './components/cartManager.php';

// CHECK IF THERE'S AN ID
if(!isset($_SESSION['user']['id']) || empty($_SESSION['user']['id']) || !is_numeric(intval($_SESSION['user']['id'])) || $_SESSION['user']['type'] !== "v"){
    // NO ID
    header('location: ./sellers.php');
    die("There's no seller account ID");
}

$id = strip_tags($_SESSION['user']['id']);

require_once './db.php';

// GETTING DATA OF SELLER
$sql = "SELECT * FROM sellers WHERE seller_id = :id";
$req = $db->prepare($sql);
$req->bindValue(':id', $id, PDO::PARAM_INT);
$req->execute();
$seller = $req->fetch();

$sellerName = '404';
if($seller) $sellerName = ucfirst(strip_tags($seller->seller_name));

$title = "Localement Suisse | $sellerName";
include './components/header.php';
include './components/navbar.php';

if(!$seller){
    // IF THERE'S NO SELLER
    http_response_code(404);
    ?>
    <div class="error">
        <p><?= $tr->translate("Le vendeur avec l'id ($id) n'existe pas.") ?></p>
        <a href='./index.php'><?= $tr->translate("Revenir") ?></a>
    </div>
    <?php
    die();
}

// GETTING ALL PRODUCTS OF SELLER
$sql = "SELECT * FROM products LEFT JOIN sellers ON sellers.seller_id = products.seller_id WHERE products.seller_id = :id AND products.pro_is_closed = 0 AND sellers.seller_is_closed = 0 AND products.pro_stock > 0 ORDER BY pro_date_added DESC";
$req = $db->prepare($sql);
$req->bindValue(":id", $id, PDO::PARAM_INT);
$req->execute();
$products = $req->fetchAll();

// SETTING SELLER'S IMAGE
$sellerImg = ($seller->seller_img && file_exists(__DIR__."/imgs/sellers/".$seller->seller_img.".jpg")) ? "sellers/".$seller->seller_img.".jpg" : "default.jpg";
$sellerMiniImg = ($seller->seller_img && file_exists(__DIR__."/imgs/sellers/mini_".$seller->seller_img.".jpg")) ? "sellers/mini_".$seller->seller_img.".jpg" : "mini_default.jpg";

?>

<?php
// CHECKING FOR ERRORS
if(isset($_SESSION['error']) && !empty($_SESSION['error'])):
    ?>
<div class="notification error">
    <p><?= $tr->translate(strip_tags($_SESSION['error'])) ?></p>
</div>
<?php
    unset($_SESSION['error']); // DELETING THE ERRORS LOG TO AVOID HAVING IT TO BE DISPLAYED MORE THAN ONCE
endif;
?>

<header class="hero">
    <div class="hero-infos hero-infos-more">
        <div class="blur-load" style="background-image: url('./imgs/<?= $sellerMiniImg ?>');">
            <img src="./imgs/<?= strip_tags($sellerImg) ?>" alt="" style="height: 100%;">
        </div>
    </div>
    <div class="hero-infos hero-infos-more">
        <h1><?= $sellerName ?></h1>

        <?php
        // SHOW IF THE SELLER IS NOT ACTIVATED
        if($seller->seller_is_activated === 0){
            ?>
            <p><?= $tr->translate("Votre compte n'est pas encore activé. Notre équipe fait au plus vite pour analyser votre profil.") ?></p>
            <?php }else{ ?>
            <a href="./addProduct.php" class="hero-link" style="--_link-color: var(--clr-800);">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="cuurentColor" height="20">
                    <path d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32V224H48c-17.7 0-32 14.3-32 32s14.3 32 32 32H192V432c0 17.7 14.3 32 32 32s32-14.3 32-32V288H400c17.7 0 32-14.3 32-32s-14.3-32-32-32H256V80z"/>
                </svg>
                <span><?= $tr->translate("Ajouter un produit") ?></span>
            </a>
            <br>
        <?php } ?>

        <a href="./editProfile.php" class="hero-link" style="--_link-color: var(--clr-800);">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor" height="20">
                <path d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160V416c0 53 43 96 96 96H352c53 0 96-43 96-96V320c0-17.7-14.3-32-32-32s-32 14.3-32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32H96z"/>
            </svg>
            <span><?= $tr->translate("Modifier le profil") ?></span>
        </a>
        <br>
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
        <ul class="section-cont section-cont-grid">
            <?php
            // DISPLAYING ALL PRODUCTS OF THE SELLER
            foreach ($products as $product):
                $sql = "SELECT * FROM images WHERE pro_id = :pro_id AND img_first = 1 LIMIT 1";
                $req = $db->prepare($sql);
                $req->bindValue(':pro_id', $product->pro_id, PDO::PARAM_INT);
                $req->execute();
                $images = $req->fetch();

                $proName = $tr->translate(ucfirst(strip_tags($product->pro_name)));

                // SETTING THE PRODUCT'S IMAGE
                $imgLink = ($images->img_name && file_exists(__DIR__."/imgs/products/".$images->img_name.".jpg")) ? "products/".$images->img_name.".jpg" : "default.jpg";
                $imgLinkMini = ($images->img_name && file_exists(__DIR__."/imgs/products/mini_".$images->img_name.".jpg")) ? "products/mini_".$images->img_name.".jpg" : "mini_default.jpg";
                ?>
                <li class="splide__slide">
                    <a href="./product.php?id=<?= $product->pro_id ?>" class="slider-container" title="<?= $proName ?>">
                        <button type="button" data-id="<?= $product->pro_id ?>" class="del-content"><?= $tr->translate("Supprimer") ?></button>
                        <div class="product-img blur-load" style="background-image: url('./imgs/<?= $imgLinkMini ?>');">
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
    </section>
</main>

<script>
    const allDelButtons = document.querySelectorAll('.del-content');
    allDelButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            e.stopImmediatePropagation();
            e.stopPropagation();
            e.preventDefault();
            if(!confirm("<?= $tr->translate("Êtes vous sûr de vouloir supprimer ce produit.") ?>\n<?= $tr->translate("Vous ne pourrez pas faire marche arrière!") ?>")) return;
            const url = `./delProduct.php?id=${button.dataset.id}`;
            window.location.href = url;
        });
    });
</script>

<?php include './components/footer.php'; ?>