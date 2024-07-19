<?php
require_once './components/cartManager.php';

require_once './db.php';

$title = "Localement Suisse | Products";
include './components/header.php';
include './components/navbar.php';

$query = '';
$product_per_page = 1; // NUMBER OF PRODUCT PER PAGE

$error_msg = "Nous n'avons pas trouvé de produits correspondants à votre recherche.";

// QUERY PART
$sqlQuerySelect = "SELECT * FROM";
$sqlQueryFirst = "products LEFT JOIN sellers ON products.seller_id = sellers.seller_id LEFT JOIN images ON images.pro_id = products.pro_id";
$sqlQueryLast = "sellers.seller_is_closed = 0 AND sellers.seller_is_activated = 1 AND products.pro_stock > 0 AND products.pro_is_closed = 0 AND (images.img_first = 1 OR images.img_first IS NULL)";
$sqlQueryLimit = "LIMIT :limit, :limitPerPage";

$queryPlus = "WHERE";
$catId = ''; //  ID OF CATEGORY IF IT'S NOT DEFINED
if(isset($_GET['cat']) && !empty($_GET['cat']) && is_numeric($_GET['cat'])){
    // IF THERE'S A CATEGORY SET FOR THE SEARCH QUERY, WE ADD THAT SETTING TO THE SQL QUERY
    $queryPlus = "LEFT JOIN cats_products ON cats_products.pro_id = products.pro_id WHERE cats_products.subcat_id = :subcatId AND";
    $catId = strip_tags($_GET['cat']);
}

if(isset($_GET['q']) || !empty($_GET['q']) && $_GET['q'] !== ''){
    // IF WE HAVE A QUERY SET (a search) WE SEARCH THE PRODUCTS WHO'S LOOK LIKE THE QUERY
    $queryIsSet = 1;

    $sql = "SELECT COUNT(*) AS count FROM ".$sqlQueryFirst." ".$queryPlus." LOWER(products.pro_name) LIKE :query AND ".$sqlQueryLast;
    $req = $db->prepare($sql);
    $req->bindValue(':query', '%'.strtolower(strip_tags($_GET['q'])).'%');
}else{
    // ELSE, WE JUST LOOK FOR PRODUCTS
    $sql = "SELECT COUNT(*) AS count FROM ".$sqlQueryFirst." ".$queryPlus." ".$sqlQueryLast;
    $req = $db->prepare($sql);
}
if($queryPlus !== "WHERE"){
    // IF WE HAVE A CATEGORY TO LOOK AT, WE HAVE BIND THE VALUE
    $req->bindValue(':subcatId', $catId, PDO::PARAM_INT);
}
$req->execute();
$nbTotalProducts = $req->fetch()->count; // WE GET THE NUMBER OF PRODUCTS THAT CORRESPOND THE QUERY

$text = $nbTotalProducts > 1 ? "produits trouvés" : "produit trouvé";

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

// CHECK IF THERE'S A QUERY
if(!isset($_GET['q']) || empty($_GET['q'])){
    // NO SEARCH QUERY

    $sql = $sqlQuerySelect." ".$sqlQueryFirst." ".$queryPlus." ".$sqlQueryLast." ORDER BY products.pro_date_added DESC ".$sqlQueryLimit;
    $req = $db->prepare($sql);
}else{
    // A SEARCH QUERY
    $query = strtolower(strip_tags($_GET['q'])); // WE PUT THE QUERY IN LOWERCASE TO CHECK FOR SIMILAR TEXT IN THE DB

    $sql = $sqlQuerySelect." ".$sqlQueryFirst." ".$queryPlus." LOWER(products.pro_name) LIKE :query AND ".$sqlQueryLast." ".$sqlQueryLimit;
    $req = $db->prepare($sql);
    $req->bindValue(':query', '%'.$query.'%');
}

if($queryPlus !== "WHERE"){
    // IF THERE'S A CATEGORY SPECIFY, WE ADD THE bindvalue() MISSING
    $req->bindValue(':subcatId', $catId, PDO::PARAM_INT);
}

$req->bindValue(':limit', $limit, PDO::PARAM_INT);
$req->bindValue(':limitPerPage', $product_per_page, PDO::PARAM_INT);
$req->execute();
$products = $req->fetchAll(); // WE GET ALL PRODUCTS
$nbProduct = count($products); // WE GET THE NUMBER OF PRODUCT, IT'S JUST IN CASE THERE'S LESS PRODUCT THAN $product_per_page
if($nbProduct < 1){
    $error_msg = "Nous n'avons pas trouvé de produits correspondants à votre requête.";
}

?>

    <header class="hero <?php if($page > 1){echo "hero-minimized";} ?>">
        <div class="hero-content hero-infos">
            <h1><?= $tr->translate("Tous nos") ?> <span class="important"><?= $tr->translate("Produits") ?></span></h1>
            <h3 style="color: var(--clr-900);"><?= number_format($nbTotalProducts, 0, '.', ' ')." ".$tr->translate($text) ?></h3>
        </div>
    </header>

    <main id="cont">
        <section class="container tag-container">
            <?php
            // GETTING ALL CATEGORIES FOR THE FILTER
            $sql = "SELECT * FROM categories";
            $req = $db->query($sql);
            $categories = $req->fetchAll();

            $linkCat = isset($queryIsSet) ? "q=".strip_tags($_GET['q']) : '';
            if($linkCat !== "") $linkCat .= "&";
            $linkCat .= "cat=";

            foreach($categories as $category):
            ?>
            <fieldset>
                <legend><?= $tr->translate(ucfirst(strip_tags($category->cat_name))) ?></legend>
                <?php
                // GETTING ALL SUBCATEGORIES FOR THE FILTER
                $sql = "SELECT subcat_name, subcat_id FROM categories LEFT JOIN subcategories ON subcategories.cat_id = categories.cat_id WHERE subcategories.cat_id = :idCat";
                $req = $db->prepare($sql);
                $req->bindValue(":idCat", $category->cat_id);
                $req->execute();
                $subcategories = $req->fetchAll();
                foreach($subcategories as $subcategory):
                ?>
                    <a href="./search.php?<?= $linkCat.$subcategory->subcat_id ?>" <?php if(is_numeric($catId) && intval($catId) === $subcategory->subcat_id){echo 'class="active"';} ?> ><?= $tr->translate(ucfirst(strip_tags($subcategory->subcat_name))) ?></a>
                <?php endforeach; ?>
            </fieldset>
            <?php endforeach; ?>
        </section>
        <section class="container">
            <?php
            // IF THERE'S NO PRODUCT FOR THE USER'S REQUEST
            if(!$products):
                ?>
                <p style="margin: 0 auto; text-align: center; text-wrap: balance;"><?= $tr->translate($error_msg) ?></p>
            <?php else: // IF THERE'S AT LEAST 1 PRODUCT ?>
                <ul class="section-cont section-cont-grid">
                    <?php
                    // DISPLAYING ALL PRODUCTS
                    foreach ($products as $product):
                        $proName = $tr->translate(strip_tags($product->pro_name));

                        $imgLink = ($product->img_name !== '' && file_exists(__DIR__."/imgs/products/".$product->img_name.".jpg")) ? "products/".$product->img_name.".jpg" : "default.jpg";
                        $miniImgLink = ($product->img_name !== '' && file_exists(__DIR__."/imgs/products/mini_".$product->img_name.".jpg")) ? "products/mini_".$product->img_name.".jpg" : "mini_default.jpg";
                        ?>
                        <li class="splide__slide">
                            <a href="./product.php?id=<?= $product->pro_id ?>" class="slider-container" title="<?= $proName ?>">
                                <div class="product-img blur-load" style="background-image: url('./imgs/<?= $miniImgLink ?>');">
                                    <img src="./imgs/<?= $imgLink ?>" alt="" loading="lazy">
                                </div>
                                <div class="slide-content">
                                    <p class="slide-title"><strong><?= $proName ?></strong></p>
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
            <?php
            endif;

            // PAGINATION SYSTEM
            $link = isset($queryIsSet) ? "q=".strip_tags($_GET['q']) : '';
            if($queryPlus !== "WHERE"){
                if($link !== "") $link .= "&";
                $link .= "cat=".$catId;
            }
            if($link !== '') $link.="&";

            $linkPath = "search.php?".$link;

            include './components/pagination.php';
            ?>
        </section>
    </main>

<?php include './components/footer.php'?>