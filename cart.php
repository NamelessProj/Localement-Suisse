<?php
require_once './components/cartManager.php';

$title = "Localement Suisse | Cart";
include './components/header.php';

if(isset($_SESSION['user']['type'])){
    header('Location: index.php');
    die("Sellers don't shop");
}

require_once './db.php';

// GETTING THE ID OF THE CURRENT ACTIVE CART OF THE USER
$sql = "SELECT cart_id FROM carts WHERE status_id = 1 AND (user_id = :user_id OR cookie_id = :cookie_id)";
$req = $db->prepare($sql);
$req->bindValue(':user_id', $userId, PDO::PARAM_INT);
$req->bindValue(':cookie_id', $cookieId);
$req->execute();
$cartId = $req->fetch()->cart_id;

// ACTION IF WE WANT TO ADD OR REMOVE 1 OF AN ITEM FROM THE USER CART
if(isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id']) && isset($_GET['action']) && !empty($_GET['action'])){
    $id = $_GET['id'];

    // GETTING THE CURRENT QUANTITY OF THE PRODUCT
    $sql = "SELECT quantity, price FROM products_carts WHERE cart_id = :cart_id AND pro_id = :pro_id";
    $req = $db->prepare($sql);
    $req->bindValue(':cart_id', $cartId, PDO::PARAM_INT);
    $req->bindValue(':pro_id', $id, PDO::PARAM_INT);
    $req->execute();
    $product = $req->fetch();

    if(!$product){
        // IN CASE THE PRODUCT ISN'T IN THE USER CART, WE REDIRECT THE USER TO HIS CART WITHOUT ANY MODIFICATIONS
        header('Location: ./cart.php');
        die("The product is not in the user's cart.");
    }

    $quantity = $product->quantity;
    $price = $product->price;

    switch($_GET['action']){
        case 'add':
            $newQuantity = $quantity + 1;
            $newPrice = $price * $newQuantity;

            // ADDING 1 TO A PRODUCT IN CART
            $sql = "UPDATE products_carts SET quantity = :quantity WHERE pro_id = :pro_id AND cart_id = :cart_id";
            $req = $db->prepare($sql);
            $req->bindValue(':quantity', $newQuantity, PDO::PARAM_INT);
            $req->bindValue(':pro_id', $id, PDO::PARAM_INT);
            $req->bindValue(':cart_id', $cartId, PDO::PARAM_INT);
            $req->execute();
            break;

        case 'remove':
            $newQuantity = $quantity - 1;
            $newPrice = $price * $newQuantity;

            // REMOVING 1 PRODUCT IN CART
            if($newQuantity < 1){ // IF THE QUANTITY OF THE PRODUCT SHOULD BE 0, WE DELETE THE PRODUCT FROM THE CART
                $sql = "DELETE FROM products_carts WHERE cart_id = :cart_id AND pro_id = :pro_id";
                $req = $db->prepare($sql);
                $req->bindValue(':cart_id', $cartId, PDO::PARAM_INT);
                $req->bindValue(':pro_id', $id, PDO::PARAM_INT);
                $req->execute();

            }else{ // ELSE, WE REMOVE 1 FROM THE QUANTITY OF THE PRODUCT
                $sql = "UPDATE products_carts SET quantity = :quantity WHERE pro_id = :pro_id AND cart_id = :cart_id";
                $req = $db->prepare($sql);
                $req->bindValue(':quantity', $newQuantity, PDO::PARAM_INT);
                $req->bindValue(':pro_id', $id, PDO::PARAM_INT);
                $req->bindValue(':cart_id', $cartId, PDO::PARAM_INT);
                $req->execute();
            }
            break;
        default:
            break;
    }

    if(isset($newPrice)){
        // UPDATING CART WITH THE NEW TOTAL IF WE HAVE DONE A MODIFICATION
        $sql = "SELECT * FROM products_carts WHERE cart_id = :cart_id";
        $req = $db->prepare($sql);
        $req->bindValue(':cart_id', $cartId, PDO::PARAM_INT);
        $req->execute();
        $cartsPro = $req->fetchAll();

        $totalPrice = 0;

        foreach($cartsPro as $cartPro){
            $price = $cartPro->quantity * $cartPro->price;
            $totalPrice += $price;
        }

        // UPDATING THE CART WITH THE NEW TOTAL
        $sql = "UPDATE carts SET cart_total = :price WHERE cart_id = :cart_id";
        $req = $db->prepare($sql);
        $req->bindValue(':price', $totalPrice, PDO::PARAM_INT);
        $req->bindValue(':cart_id', $cartId, PDO::PARAM_INT);
        $req->execute();
    }

    // WE RELOAD THE PAGE WITHOUT ANYTHING HAPPENING
    header('Location: ./cart.php');
    die();
}

// GETTING ALL ITEMS IN THE USER'S CART
$sql = "SELECT * FROM products_carts LEFT JOIN carts ON carts.cart_id = products_carts.cart_id LEFT JOIN products ON products.pro_id = products_carts.pro_id LEFT JOIN images ON images.pro_id = products.pro_id WHERE carts.status_id = :status_id AND images.img_first = :img_code AND( carts.user_id = :user_id OR carts.cookie_id = :cookie_id)";
$req = $db->prepare($sql);
$req->bindValue(':status_id', 1, PDO::PARAM_INT);
$req->bindValue(':img_code', 1, PDO::PARAM_INT);
$req->bindValue(':user_id', $userId, PDO::PARAM_INT);
$req->bindValue(':cookie_id', $cookieId);
$req->execute();
$carts = $req->fetchAll();
$numItems = count($carts);

if($numItems > 0){
    $cartTotal = strip_tags($carts[0]->cart_total);
}

$text = "produit";
if($numItems > 1) $text.="s";

include './components/navbar.php';
?>

    <header class="hero hero-minimized">
        <div class="hero-infos hero-content">
            <h1><span class="important"><?= $tr->translate("Votre panier") ?></span></h1>
            <h3 style="color: var(--clr-900);"><?= number_format($numItems, 0, '.', ' ')." ".$tr->translate($text) ?></h3>
        </div>
    </header>

    <main id="cont">
        <section>
            <?php if($numItems > 0): ?>
                <ul>
                    <?php
                    foreach($carts as $product):
                        $proName = ucfirst(strip_tags($product->pro_name));

                        $imgLink = ($product->img_name !== '' && file_exists(__DIR__."/imgs/products/".$product->img_name.".jpg")) ? "products/".$product->img_name.".jpg" : "default.jpg";
                        $miniImgLink = ($product->img_name !== '' && file_exists(__DIR__."/imgs/products/mini_".$product->img_name.".jpg")) ? "products/mini_".$product->img_name.".jpg" : "mini_default.jpg";
                        ?>
                        <li class="cart-item">
                            <div class="cart-section">
                                <img src="./imgs/<?= $imgLink ?>" alt="">
                            </div>
                            <div class="cart-section">
                                <a href="./product.php?id=<?= $product->pro_id ?>"><?= $trAuto->translate($proName) ?></a>
                            </div>
                            <div class="cart-section">
                                <?php
                                // GETTING THE DATA OF THE CURRENT PRODUCT
                                $sql = "SELECT * FROM products WHERE pro_id = :pro_id";
                                $req = $db->prepare($sql);
                                $req->bindValue(':pro_id', $product->pro_id, PDO::PARAM_INT);
                                $req->execute();
                                $currentProduct = $req->fetch();

                                // CHECKING IF THE PRICE IS THE RIGHT ONE
                                if($product->price !== $currentProduct->pro_price){
                                    $newProductPrice = $currentProduct->pro_in_sale === 1 ? $currentProduct->pro_sale_price : $currentProduct->pro_price;
                                    // IF THE PRICE IS DIFFERENT, WE UPDATE TO THE NEW PRICE
                                    $sql = "UPDATE products_carts SET price = :price WHERE pro_cart_id = :pro_cart_id";
                                    $req = $db->prepare($sql);
                                    $req->bindValue(':price', $newProductPrice, PDO::PARAM_INT);
                                    $req->bindValue(':pro_cart_id', $product->pro_cart_id, PDO::PARAM_INT);
                                    $req->execute();

                                    // AND WE UPDATE THE TOTAL OF THE CART
                                    $sql = "SELECT * FROM products_carts WHERE cart_id = :cart_id";
                                    $req = $db->prepare($sql);
                                    $req->bindValue(':cart_id', $cartId, PDO::PARAM_INT);
                                    $req->execute();
                                    $cartsPro = $req->fetchAll();

                                    $totalPrice = 0;

                                    foreach($cartsPro as $cartPro){
                                        $price = $cartPro->quantity * $cartPro->price;
                                        $totalPrice += $price;
                                    }

                                    // UPDATING THE CART WITH THE NEW TOTAL
                                    $sql = "UPDATE carts SET cart_total = :price WHERE cart_id = :cart_id";
                                    $req = $db->prepare($sql);
                                    $req->bindValue(':price', $totalPrice, PDO::PARAM_INT);
                                    $req->bindValue(':cart_id', $cartId, PDO::PARAM_INT);
                                    $req->execute();
                                }

                                // PRICE
                                $price = $product->pro_in_sale === 1 ? $product->pro_sale_price : $product->pro_price;
                                if($product->pro_in_sale === 1):
                                ?>
                                <p class="price price-sale"><?= priceFormatting($product->pro_price) ?></p>
                                <?php endif; ?>
                                <p class="price"><?= priceFormatting($price) ?></p>
                            </div>
                            <div class="cart-section">
                                <div class="quantity">
                                    <span><?= strip_tags($product->quantity) ?></span>
                                    <a href="./cart.php?id=<?= $product->pro_id ?>&action=add" class="no-style button">
                                        <span class="sr-only"><?= $tr->translate("Ajouter un") ?></span>
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor" width="15">
                                            <path d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32V224H48c-17.7 0-32 14.3-32 32s14.3 32 32 32H192V432c0 17.7 14.3 32 32 32s32-14.3 32-32V288H400c17.7 0 32-14.3 32-32s-14.3-32-32-32H256V80z"/>
                                        </svg>
                                    </a>
                                    <a href="./cart.php?id=<?= $product->pro_id ?>&action=remove" class="no-style button">
                                        <span class="sr-only"><?= $tr->translate("Enlever un") ?></span>
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor" width="15">
                                            <path d="M432 256c0 17.7-14.3 32-32 32L48 288c-17.7 0-32-14.3-32-32s14.3-32 32-32l352 0c17.7 0 32 14.3 32 32z"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                            <div class="cart-section">
                                <a class="del-link no-style" href="delProCart.php?id=<?= strip_tags($product->pro_id) ?>">
                                    <h3 class="sr-only"><?= $tr->translate("Supprimer le produit: $proName") ?></h3>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor" height="25">
                                        <path d="M135.2 17.7C140.6 6.8 151.7 0 163.8 0H284.2c12.1 0 23.2 6.8 28.6 17.7L320 32h96c17.7 0 32 14.3 32 32s-14.3 32-32 32H32C14.3 96 0 81.7 0 64S14.3 32 32 32h96l7.2-14.3zM32 128H416V448c0 35.3-28.7 64-64 64H96c-35.3 0-64-28.7-64-64V128zm96 64c-8.8 0-16 7.2-16 16V432c0 8.8 7.2 16 16 16s16-7.2 16-16V208c0-8.8-7.2-16-16-16zm96 0c-8.8 0-16 7.2-16 16V432c0 8.8 7.2 16 16 16s16-7.2 16-16V208c0-8.8-7.2-16-16-16zm96 0c-8.8 0-16 7.2-16 16V432c0 8.8 7.2 16 16 16s16-7.2 16-16V208c0-8.8-7.2-16-16-16z"/>
                                    </svg>
                                </a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <div class="divider"></div>

                <div>
                    <?php
                    // GETTING ALL ITEMS IN THE USER'S CART
                    $sql = "SELECT cart_total FROM carts WHERE cart_id = :cart_id";
                    $req = $db->prepare($sql);
                    $req->bindValue(':cart_id', $cartId, PDO::PARAM_INT);
                    $req->execute();

                    // GETTING THE TOTAL PRICE
                    $cartTotalPrice = $req->fetch()->cart_total;
                    ?>
                    <h3><span class="price"><?= priceFormatting($cartTotalPrice) ?></span></h3>
                </div>
            <?php else: ?>
                <p><?= $tr->translate("Votre panier est vide.") ?></p>
            <?php endif; ?>
        </section>
    </main>

<?php include './components/footer.php'?>