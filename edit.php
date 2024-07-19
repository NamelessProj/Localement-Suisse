<?php
require_once './components/cartManager.php';

$title = "Localement Suisse | Edit a product";
include './components/header.php';

if(!isset($_SESSION['user']['id'], $_SESSION['user']['type']) || empty($_SESSION['user']['id']) || !is_numeric($_SESSION['user']['id']) || !isset($_SESSION['user']['uniq']) || empty($_SESSION['user']['uniq']) || $_SESSION['user']['type'] !== 'v'){
    header('Location: index.php');
    die();
}

if(!isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id'])){
    // NO ID
    $previous = "index.php";
    if(isset($_SERVER['HTTP_REFERER'])) $previous = $_SERVER['HTTP_REFERER'];
    header('Location: '.$previous);
    die("There's no category ID");
}

$idProduct = $_GET['id'];

require_once './db.php';

// CHECKING IF THE PRODUCT IS FROM THE SELLER
$sql = "SELECT * FROM products LEFT JOIN sellers ON sellers.seller_id = products.seller_id WHERE products.pro_id = :id AND sellers.seller_id = :seller_id AND products.pro_is_closed = 0 AND sellers.seller_uniqid = :uniq";
$req = $db->prepare($sql);
$req->bindValue(':id', $idProduct, PDO::PARAM_INT);
$req->bindValue(':seller_id', $_SESSION['user']['id'], PDO::PARAM_INT);
$req->bindValue(':uniq', $_SESSION['user']['uniq']);
$req->execute();
$thisProduct = $req->fetch();

if(!$thisProduct){
    header('Location: index.php');
    die("The product is not from this seller");
}

require_once './imagesUpload.php';

if(!empty($_POST)){
    if(isset($_POST['name'], $_POST['price'], $_POST['stock'], $_POST['description'], $_POST['cat'], $_FILES['firstImag']) && !empty($_POST['name']) && !empty($_POST['price']) && !empty($_POST['stock']) && !empty($_POST['description']) && !empty($_POST['cat']) && !empty($_FILES['firstImag'])){
        $name = strip_tags(trim($_POST['name']));
        $price = strip_tags(trim($_POST['price']));
        $stock = strip_tags(trim($_POST['stock']));
        $description = strip_tags($_POST['description']);
        $cat = $_POST['cat'];

        // CHECK IF THERE'S AT LEAST 1 CATEGORY SELECTED
        if(count($cat) < 1) die($tr->translate("Sélectionner au moins une categorie."));

        // CHECKING IF PRICE AND STOCK ARE NUMBERS
        if(!is_numeric($price) || !is_numeric($stock)) die($tr->translate("Les informations sont incorrect."));

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

        $sql = "UPDATE products SET pro_name = :name, pro_price = :price, pro_stock = :stock, pro_description = :description WHERE pro_id = :id";
        $req = $db->prepare($sql);
        $req->bindValue(':name', $name);
        $req->bindValue(':price', $price, PDO::PARAM_INT);
        $req->bindValue(':stock', $stock, PDO::PARAM_INT);
        $req->bindValue(':description', $description);
        $req->bindValue(':id', $idProduct, PDO::PARAM_INT);

        // EXECUTE REQUEST
        if(!$req->execute()){
            die($tr->translate("Oups, une erreur est survenue."));
        }


        // HANDLE CATEGORIES
        // DELETING ALL RELATION BETWEEN THE PRODUCT AND CATEGORIES
        $sql = "DELETE FROM cats_products WHERE pro_id = :id";
        $req = $db->prepare($sql);
        $req->bindValue(':id', $idProduct, PDO::PARAM_INT);
        if(!$req->execute()){
            die($tr->translate("Oups, une erreur est survenue."));
        }

        // ADDING ALL THE RELATION BETWEEN THE PRODUCT AND THE CATEGORIES
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

        $sql = "SELECT * FROM images WHERE pro_id = :id AND img_first = 1";
        $req = $db->prepare($sql);
        $req->bindValue(':id', $idProduct, PDO::PARAM_INT);
        $req->execute();
        $previousImg = $req->fetch(); // GETTING THE PREVIOUS IMAGE

        if(isset($_FILES['firstImag']['name']) && !empty($_FILES['firstImag']['name'])){
            $uploadFile = uploadImage($_FILES['firstImag'], "products");

            if($uploadFile['error']){
                die($tr->translate("Une erreur est survenue lors du téléchargement de l'image."));
            }

            // INSERTING THE IMG INTO THE DB
            $sql = "INSERT INTO images (img_name, img_first, pro_id) VALUES (:name, :first, :idProduct)";
            $req = $db->prepare($sql);
            $req->bindValue(':name', $uploadFile['name']);
            $req->bindValue(':first', 1, PDO::PARAM_INT);
            $req->bindValue(':idProduct', $idProduct, PDO::PARAM_INT);
            if(!$req->execute()){
                die($tr->translate("Oups, une erreur est survenue."));
            }

            if(file_exists(__DIR__."/imgs/products/".$previousImg->img_name.".jpg")) unlink(__DIR__."/imgs/products/".$previousImg->img_name.".jpg"); // DELETING THE FILE FROM THE FOLDER
            if(file_exists(__DIR__."/imgs/products/mini_".$previousImg->img_name.".jpg")) unlink(__DIR__."/imgs/products/mini_".$previousImg->img_name.".jpg"); // DELETING THE MINI FILE FROM THE FOLDER

            // DEL THE PREVIOUS FIRST PICTURE IN THE DB
            if($previousImg){
                $sql = "DELETE FROM images WHERE img_id = :id_img";
                $req = $db->prepare($sql);
                $req->bindValue(':id_img', $previousImg->img_id, PDO::PARAM_INT);
                if(!$req->execute()){
                    die($tr->translate("Oups, une erreur est survenue."));
                }
            }
        }
    }
}

// HANDLE ADDING NEW IMAGES TO PRESENT THE PRODUCT
if(!empty($_POST) && isset($_POST['addImg']) && isset($_FILES)){
    if(isset($_FILES['addNewImg']['name']) && !empty($_FILES['addNewImg']['name']) && $_FILES['addNewImg']['error'] === 0){
        $uploadFile = uploadImage($_FILES['addNewImg'], "products");

        if($uploadFile['error']){
            die($tr->translate("Une erreur est survenue lors du téléchargement de l'image."));
        }

        // ADDING THE FILE TO THE DB
        $sql = "INSERT INTO images (img_name, pro_id) VALUES (:name, :idProduct)";
        $req = $db->prepare($sql);
        $req->bindValue(':name', $uploadFile['name']);
        $req->bindValue(':idProduct', $idProduct, PDO::PARAM_INT);
        if(!$req->execute()){
            die($tr->translate("Oups, une erreur est survenue."));
        }
    }
}

$sql = "SELECT * FROM products WHERE pro_id = :id AND seller_id = :seller_id";
$req = $db->prepare($sql);
$req->bindValue(':id', $idProduct, PDO::PARAM_INT);
$req->bindValue(':seller_id', $_SESSION['user']['id'], PDO::PARAM_INT);
$req->execute();
$thisProduct = $req->fetch(); // GETTING THE PRODUCTS DATA

// GETTING THE FIRST IMG OF THE PRODUCT
$imgLink = "default.jpg";
$sql = "SELECT * FROM images WHERE pro_id = :pro_id AND img_first = 1 LIMIT 1";
$req = $db->prepare($sql);
$req->bindValue(':pro_id', $thisProduct->pro_id, PDO::PARAM_INT);
$req->execute();
$images = $req->fetch();
$imgLink = ($images && file_exists(__DIR__."/imgs/products/".$images->img_name.".jpg")) ? "products/".$images->img_name.".jpg" : "default.jpg";

include './components/navbar.php';

?>

<header class="hero">
    <picture class="bg-img">
        <img src="./imgs/<?= $imgLink ?>" alt="">
    </picture>
    <div class="hero-content">
        <h1><?= $tr->translate("Modifier le  produit") ?>: <span class="important"><?= ucfirst(strip_tags($thisProduct->pro_name)) ?></span></h1>
        <p>
            <a class="no-style button" href="./product.php?id=<?= $thisProduct->pro_id ?>"><?= $tr->translate("Revenir") ?></a>
        </p>
    </div>
</header>

<main id="cont">
    <section>
        <form method="post" enctype="multipart/form-data">
            <div class="form-container">
                <div class="user-box">
                    <input type="text" name="name" id="name" value="<?= ucfirst(strip_tags($thisProduct->pro_name)) ?>" required>
                    <label><?= $tr->translate("Nom du produit") ?></label>
                </div>

                <div class="user-box">
                    <input type="text" inputmode="numeric" name="price" id="price" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" value="<?= priceFormatting($thisProduct->pro_price, 2, '') ?>" required>
                    <label><?= $tr->translate("Prix") ?></label>
                </div>

                <div class="user-box">
                    <input type="text" inputmode="numeric" name="stock" id="stock" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1');" value="<?= strip_tags($thisProduct->pro_stock) ?>" required>
                    <label><?= $tr->translate("Stock") ?></label>
                </div>

                <div class="user-box">
                    <textarea name="description" id="description" cols="30" rows="10" placeholder="<?= $tr->translate("Description de votre produit") ?>"><?= $thisProduct->pro_description ?></textarea>
                </div>

                <p style="margin-top: 1.5rem;"><?= $tr->translate("Cette option n'est pas obligatoire.") ?></p>
                <div class="user-box" style="margin-top: 1rem;">
                    <input type="file" name="firstImag" id="firstImag" accept="image/png, image/jpeg">
                    <label><?= $tr->translate("Changer l'image principale du produit") ?></label>
                </div>
            </div>

            <section>
                <h4><?= $tr->translate("Catégories") ?></h4>
                <p><?= $tr->translate("Il faut au minimum 1 catégorie.") ?></p>
                <?php
                // GETTING ALL CATEGORIES LINKED TO THE PRODUCT
                $sql = "SELECT subcat_id FROM cats_products WHERE pro_id = :id";
                $req = $db->prepare($sql);
                $req->bindValue(":id", $idProduct, PDO::PARAM_INT);
                $req->execute();
                $subCatId = $req->fetchAll();
                $arraySubId = array();

                // PUTTING ALL ID IN AN ARRAY TO CHECK IF THE PRODUCT HAS THAT ID
                foreach($subCatId as $value){
                    array_push($arraySubId, $value->subcat_id);
                }

                $sql = "SELECT * FROM categories"; // GETTING ALL CATEGORIES
                $req = $db->query($sql);
                $categories = $req->fetchAll();

                foreach($categories as $category):
                    ?>
                    <fieldset>
                        <legend><?= $tr->translate(ucfirst(strip_tags($category->cat_name))) ?></legend>
                        <?php
                        $sql = "SELECT subcat_name, subcat_id FROM categories LEFT JOIN subcategories ON subcategories.cat_id = categories.cat_id WHERE subcategories.cat_id = :idCat"; // GETTING ALL SUBCATEGORIES BY CATEGORY
                        $req = $db->prepare($sql);
                        $req->bindValue(":idCat", $category->cat_id, PDO::PARAM_INT);
                        $req->execute();
                        $subcategories = $req->fetchAll();
                        foreach($subcategories as $subcategory):
                            $catName = ucfirst(strip_tags($subcategory->subcat_name));
                            $tradName = $tr->translate($catName);
                            ?>
                            <label for="cat_<?= strip_tags($subcategory->subcat_id) ?>" class="label_cont" title="<?= $tradName ?>">
                                <span><?= $tradName ?></span>
                                <input type="checkbox" name="cat[]" id="cat_<?= strip_tags($subcategory->subcat_id) ?>" value="<?= strip_tags($subcategory->subcat_id) ?>" <?php if(in_array($subcategory->subcat_id, $arraySubId)){echo "checked";} ?>>
                                <span class="checkmark"></span>
                            </label>
                        <?php endforeach; ?>
                    </fieldset>
                <?php endforeach; ?>
            </section>

            <footer class="form-footer">
                <button type="submit"><?= $tr->translate("Modifier le produit") ?></button>
            </footer>
        </form>
    </section>

    <div class="divider"></div>

    <section>
        <ul class="edit-img-grid">
            <?php
            $delText = $tr->translate("Supprimer");
            $sql = "SELECT * FROM images WHERE pro_id = :id AND img_first = 0";
            $req = $db->prepare($sql);
            $req->bindValue(':id', $thisProduct->pro_id, PDO::PARAM_INT);
            $req->execute();
            $images = $req->fetchAll();
            foreach($images as $image):
                ?>
                <li>
                    <div class="grid-element">
                        <img src="./imgs/products/<?= $image->img_name ?>" alt="">
                    </div>
                    <div class="grid-element">
                        <a class="no-style button" href="./deleteImage.php?id=<?= $image->img_id ?>"><?= $delText ?></a>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>

        <form method="post" enctype="multipart/form-data">
            <div class="form-container">
                <fieldset>
                    <legend><?= $tr->translate("Ajouter une nouvelle image") ?></legend>
                    <div class="user-box">
                        <input type="file" name="addNewImg" id="addNewImg" accept="image/png, image/jpeg" required>
                        <label><?= $tr->translate("Ajouter une image") ?></label>
                    </div>

                    <footer class="form-footer">
                        <button name="addImg" type="submit"><?= $tr->translate("Ajouter l'image") ?></button>
                    </footer>
                </fieldset>
            </div>
        </form>
    </section>
</main>

<?php include './components/footer.php'; ?>
