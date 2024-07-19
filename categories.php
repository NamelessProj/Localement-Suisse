<?php
require_once './components/cartManager.php';

require_once './db.php';

$title = "Localement Suisse | Les catégories";
include './components/header.php';
include './components/navbar.php';

$sql = "SELECT * FROM categories"; // GETTING ALL CATEGORIES
$req = $db->query($sql);
$categories = $req->fetchAll();
?>

    <header class="hero">
        <div class="hero-content hero-infos">
            <h1><?= $tr->translate("Toutes nos") ?> <span class="important"><?= $tr->translate("Catégories") ?></span></h1>
        </div>
    </header>

    <main id="cont">
        <section>
            <?php
            foreach ($categories as $category){
                $sql = "SELECT subcat_name, subcat_id FROM categories LEFT JOIN subcategories ON subcategories.cat_id = categories.cat_id WHERE subcategories.cat_id = :idCat"; // GETTING ALL SUBCATEGORIES BY CATEGORY
                $req = $db->prepare($sql);
                $req->bindValue(":idCat", $category->cat_id);
                $req->execute();
                $subcategories = $req->fetchAll();
                ?>
                <div class="category">
                    <h3><?= $tr->translate(ucfirst(strip_tags($category->cat_name))) ?></h3>
                    <ul>
                        <?php foreach ($subcategories as $subcategory): ?>
                        <li>
                            <a href="./category.php?id=<?= $subcategory->subcat_id ?>"><?= $tr->translate(ucfirst(strip_tags($subcategory->subcat_name))) ?></a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php } ?>
        </section>
    </main>

<?php include './components/footer.php'?>