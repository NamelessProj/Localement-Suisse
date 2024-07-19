<?php
require_once './components/cartManager.php';

$title = "Localement Suisse | Ajouter un produit";
include './components/header.php';

// CHECK IF THERE'S AN ID
if(!isset($_SESSION['user']['id']) || !is_numeric($_SESSION['user']['id']) || empty($_SESSION['user']['id']) || $_SESSION['user']['type'] !== "v" || !isset($_SESSION['user']['uniq']) || empty($_SESSION['user']['uniq'])) {
    // NO ID
    header('location: ./sellers.php');
    die("There's no seller account ID");
}

require_once './db.php';

// CHECK IF THE SELLER IS AUTHORIZED TO ADD A NUMBER
$sql = "SELECT * FROM sellers WHERE seller_id = :id AND seller_uniqid = :uniqid AND seller_is_closed = 0 AND seller_is_activated = 1";
$req = $db->prepare($sql);
$req->bindValue(':id', $_SESSION['user']['id']);
$req->bindValue(':uniqid', $_SESSION['user']['uniq']);
$req->execute();
$sellerIsReal = $req->rowCount();

if($sellerIsReal === 0){
    header('location: ./index.php');
    die("The seller isn't authorized to add a product");
}

if(!empty($_POST)){
    if(isset($_POST['name'], $_POST['price'], $_POST['stock'], $_POST['description'], $_POST['cat'], $_FILES['firstImag']) && !empty($_POST['name']) && !empty($_POST['price']) && !empty($_POST['stock']) && !empty($_POST['description']) && !empty($_FILES['firstImag']) && !empty($_POST['cat'])){
        $name = strip_tags(trim($_POST['name']));
        $price = strip_tags(trim($_POST['price']));
        $stock = strip_tags(trim($_POST['stock']));
        $description = strip_tags($_POST['description']);
        $cat = $_POST['cat'];

        // CHECK IF THERE'S AT LEAST 1 CATEGORY SELECTED
        if(count($cat) < 1) die($tr->translate("Sélectionner au moins une categorie."));

        // CHECK IF PRICE AND STOCK ARE NUMBERS
        if(!is_numeric($price) || !is_numeric($stock)) die($tr->translate("Les informations sont incorrect."));

        // TRANSFORM THE PRICE TO REMOVE DOT: 34.50 -> 3450
        function floattostr($val){
            preg_match("#^([\+\-]|)([0-9]*)(\.([0-9]*?)|)(0*)$#", trim($val), $o);
            return $o[1].sprintf('%d', $o[2]).($o[3]!='.'?$o[3]:'');
        }

        $price = floattostr($price);

        if(!preg_match('/^[0-9]+.[0-9]{0,2}$/', $price)) die($tr->translate("Les informations sont incorrect.")); // CHECK IF PRICE GOT AT LEST 1 NUMBER BEFORE THE DOT AND 0, 1 OR 2 NUMBERS AFTER THE DOT

        // TRANSFORM THE PRICE TO REMOVE DOT: 34.50 -> 3450
        if(str_contains($price, '.')){ // CHECK IF PRICE HAS A DOT
            $ex = explode('.', $price);
            $ex = array_reverse($ex);
            $totalCharacterAfterComma = strlen(trim($ex[0])); // GETTING THE NUMBER OF NUMBERS AFTER THE DOT

            // ADDING THE 0 NEEDED
            if($totalCharacterAfterComma < 2){
                for($i = 0; $i < $totalCharacterAfterComma; $i++){
                    $price .= '0';
                }
            }
        }else $price .= '00';

        $price = str_replace('.', '', $price); // GETTING RID OF THE DOT

        $stock = intval($stock); // TRANSFORMING INTO A NUMBER

        // INSERTING THE NEW PRODUCT
        $sql = "INSERT INTO products (pro_name, pro_price, pro_stock, pro_description, pro_date_added, seller_id) VALUES (:name, :price, :stock, :description, :date, :seller_id)";
        $req = $db->prepare($sql);
        $req->bindValue(':name', $name);
        $req->bindValue(':price', $price, PDO::PARAM_INT);
        $req->bindValue(':stock', $stock, PDO::PARAM_INT);
        $req->bindValue(':description', $description);
        $req->bindValue(':date', date('Y-m-d'));
        $req->bindValue(':seller_id', $_SESSION['user']['id'], PDO::PARAM_INT);

        // EXECUTE REQUEST
        if(!$req->execute()){
            die($tr->translate("Oups, une erreur est survenue."));
        }
        $idProduct = $db->lastInsertId(); // GETTING THE ID OF THE NEW PRODUCT

        // HANDLE CATEGORIES
        foreach($cat as $value){
            if(is_numeric($value)){ // WE SKIP EVERYTHING THAT IS NOT A NUMBER
                $sql = "INSERT INTO cats_products (subcat_id, pro_id) VALUES (:subcat_id, :product_id)";
                $req = $db->prepare($sql);
                $req->bindValue(':subcat_id', strip_tags($value), PDO::PARAM_INT);
                $req->bindValue(':product_id', $idProduct, PDO::PARAM_INT);
                if(!$req->execute()){
                    die($tr->translate("Oups, une erreur est survenue."));
                }
            }
        }

        // HANDLING IMG
        require_once './imagesUpload.php';

        // CHECKING FOR THE PRESENTATION IMG OF PRODUCT
        if(isset($_FILES['firstImag']['name']) && !empty($_FILES['firstImag']['name']) && $_FILES['firstImag']['error'] === 0){
            $uploadFile = uploadImage($_FILES["firstImag"], "products");

            if($uploadFile['error']){
                die($tr->translate("Une erreur est survenue lors du téléchargement de l'image."));
            }

            $sql = "INSERT INTO images (img_name, img_first, pro_id) VALUES (:name, :first, :idProduct)"; // INSERTING THE IMG INTO THE DB
            $req = $db->prepare($sql);
            $req->bindValue(':name', $uploadFile['name']);
            $req->bindValue(':first', 1, PDO::PARAM_INT);
            $req->bindValue(':idProduct', $idProduct, PDO::PARAM_INT);
            if(!$req->execute()){
                die($tr->translate("Oups, une erreur est survenue."));
            }
        }

        // CHECKING FOR THE OTHER IMG
        if(isset($_FILES['imgs']) && !empty($_FILES['imgs']['name'][0])){
            $imgs = $_FILES['imgs'];
            $nbImgs = count($imgs['name']); // GETTING THE NUMBER OF IMG
            for($i = 0; $i < $nbImgs; $i++){
                $uploadFile = uploadImage($_FILES['imgs'], "products", $i);

                if($uploadFile['error']){
                    die($tr->translate("Une erreur est survenue lors du téléchargement d'une ou plusieurs image(s)."));
                }

                $sql = "INSERT INTO images (img_name, pro_id) VALUES (:name, :idProduct)"; // INSERTING THE IMG INTO THE DB
                $req = $db->prepare($sql);
                $req->bindValue(':name', $uploadFile['name']);
                $req->bindValue(':idProduct', $idProduct, PDO::PARAM_INT);
                if(!$req->execute()){
                    die($tr->translate("Oups, une erreur est survenue."));
                }
            }
        }

        header('location: ./product.php?id='.$idProduct); // REDIRECT TO THE PAGE OPF THE NEW PRODUCT
        die();

    }else die($tr->translate("Veuillez remplir le formulaire."));
}

include './components/navbar.php';

?>

<header class="hero">
    <div class="hero-infos hero-content">
        <h1><?= $tr->translate("Ajouter un") ?> <span class="important"><?= $tr->translate("Produit") ?></span></h1>
    </div>
</header>

<section>
    <form method="post" enctype="multipart/form-data">
        <div class="form-container">
            <div class="user-box">
                <input type="text" name="name" id="name" required>
                <label><?= $tr->translate("Nom du produit") ?></label>
            </div>

            <div class="user-box">
                <input type="text" inputmode="numeric" name="price" id="price" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" required>
                <label><?= $tr->translate("Prix") ?></label>
            </div>

            <div class="user-box">
                <input type="text" inputmode="numeric" name="stock" id="stock" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1');" required>
                <label><?= $tr->translate("Stock") ?></label>
            </div>

            <div class="user-box">
                <textarea name="description" id="description" cols="30" rows="10" placeholder="<?= $tr->translate("Description de votre produit") ?>"></textarea>
            </div>

            <br>
            <p><?= $tr->translate("Ne mettez pas d'images plus grandes que 2000 par 2000.") ?></p>
            <div class="user-box" style="margin-top: 1rem;">
                <input type="file" name="firstImag" id="firstImag" accept="image/png, image/jpeg" required>
                <label><?= $tr->translate("Image principale du produit") ?></label>
            </div>

            <div class="user-box">
                <input type="file" name="imgs[]" id="imgs" multiple accept="image/png, image/jpeg">
                <label><?= $tr->translate("Images de présentation de votre produit") ?></label>
            </div>
        </div>

        <br>
        <div class="divider"></div>
        <br>

        <section>
            <h4><?= $tr->translate("Catégories") ?></h4>
            <?php
            $sql = "SELECT * FROM categories"; // GETTING ALL CATEGORIES
            $req = $db->query($sql);
            $categories = $req->fetchAll();

            foreach($categories as $category):
                ?>
                <fieldset>
                    <legend><?= ucfirst(strip_tags($category->cat_name)) ?></legend>
                    <?php
                    $sql = "SELECT subcat_name, subcat_id FROM categories LEFT JOIN subcategories ON subcategories.cat_id = categories.cat_id WHERE subcategories.cat_id = :idCat"; // GETTING ALL SUBCATEGORIES BY CATEGORY
                    $req = $db->prepare($sql);
                    $req->bindValue(":idCat", $category->cat_id, PDO::PARAM_INT);
                    $req->execute();
                    $subcategories = $req->fetchAll();
                    foreach($subcategories as $subcategory):
                        ?>
                        <label for="cat_<?= strip_tags($subcategory->subcat_id) ?>" class="label_cont" title="<?= strip_tags($subcategory->subcat_name) ?>">
                            <span><?= $tr->translate(ucfirst(strip_tags($subcategory->subcat_name))) ?></span>
                            <input type="checkbox" name="cat[]" id="cat_<?= strip_tags($subcategory->subcat_id) ?>" value="<?= strip_tags($subcategory->subcat_id) ?>">
                            <span class="checkmark"></span>
                        </label>
                    <?php endforeach; ?>
                </fieldset>
            <?php endforeach; ?>
        </section>

        <footer class="form-footer">
            <button type="submit"><?= $tr->translate("Ajouter le produit") ?></button>
        </footer>
    </form>
</section>

<?php include './components/footer.php'; ?>
