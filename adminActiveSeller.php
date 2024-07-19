<?php
require_once './components/cartManager.php';

$adminCurrentPage = "Activer des vendeurs";
$title = "Localement Suisse | Admin - ".$adminCurrentPage;
include './components/header.php';

require_once './db.php';

// CHECK IF THE USER IS LOGIN AND AN ADMIN
if(isset($_SESSION['user']['admin']) && !empty($_SESSION['user']['admin']) && isset($_SESSION['user']['id']) && !empty($_SESSION['user']['id']) && is_numeric($_SESSION['user']['id'])){
    // IF THE ADMIN SESSION IS ALREADY OPEN, WE CHECK IF THE USER IS AN ADMIN
    $sql = "SELECT * FROM users WHERE user_id = :user_id AND typ_id = :type_id AND user_uniqid = :uniqid";
    $req = $db->prepare($sql);
    $req->bindValue(':user_id', $_SESSION['user']['id'], PDO::PARAM_INT);
    $req->bindValue(':type_id', 2, PDO::PARAM_INT);
    $req->bindValue(':uniqid', $_SESSION['user']['admin']);
    $req->execute();
    $user = $req->fetch();
    if(!$user){
        header("Location: index.php");
        die("The user is not an admin.");
    }
}else{
    header("Location: index.php");
    die("The user is not an admin.");
}

$error_msg = "Aucun, tout les vendeurs sont activés.";

$product_per_page = 20; // NUMBER OF SELLERS PER PAGE

$sql = "SELECT COUNT(*) AS count FROM sellers WHERE seller_is_activated = :activatedCode AND seller_is_closed = :closedCode";
$req = $db->prepare($sql);
$req->bindValue(':activatedCode', 0, PDO::PARAM_INT);
$req->bindValue(':closedCode', 0, PDO::PARAM_INT);
$req->execute();
$nbTotalProducts = $req->fetch()->count; // WE GET THE NUMBER OF SELLERS THAT CORRESPOND THE QUERY

$pages_count = ceil($nbTotalProducts / $product_per_page); // WE GET THE NUMBER OF PAGE MAX

$page = 1; // SETTING THE CURRENT PAGE TO 1
if(isset($_GET['page']) && !empty($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 1) $page = $_GET['page']; // IF A PAGE NUMBER IS GIVEN FROM THE $_GET, WE CHECK IF IT'S A VALID NUMBER AND IF IT IS, WE MAKE IT THE CURRENT PAGE NUMBER
if(is_numeric($page) && $page > $pages_count){
    $text = $pages_count > 1 ? "pages" : "page";
    $error_msg = $nbTotalProducts > 0 ? "Cette page n'est pas valide. Il n'y a que ".$pages_count." ".$text."." : "Il n'y a pas de vendeurs à approuver.";
}

// WE DEFINE THE LIMIT FOR THE SQL QUERY
// IF WE'RE AT THE FIRST PAGE, WE WANT THE FIRST ELEMENT TO SHOW SO THE LIMIT IS SET TO 0
// IF WE'RE NOT AT THE FIRST PAGE, WE NEED TO SUBTRACT 1 TO THE PAGE NUMBER TO GET THE FIRST ELEMENT OF THE PAGE AND THEN MULTIPLY THAT BY THE NUMBER OF PRODUCT PER PAGE TO GET THE PRODUCT OF THE CURRENT PAGE
$limit = $page === 1 ? 0 : ($page - 1) * $product_per_page;

// GETTING ALL THE NON-ACTIVATE SELLERS
$sql = "SELECT * FROM sellers WHERE seller_is_activated = :activatedCode AND seller_is_closed = :closedCode ORDER BY seller_date_added DESC LIMIT :limit, :limitPerPage";
$req = $db->prepare($sql);
$req->bindValue(':activatedCode', 0, PDO::PARAM_INT);
$req->bindValue(':closedCode', 0, PDO::PARAM_INT);
$req->bindValue(':limit', $limit, PDO::PARAM_INT);
$req->bindValue(':limitPerPage', $product_per_page, PDO::PARAM_INT);
$req->execute();
$sellers = $req->fetchAll();

include './components/navbar.php';

?>

    <header class="hero">
        <div class="hero-infos">
            <h1><?= $tr->translate("Activer les vendeurs") ?></h1>
        </div>
    </header>

    <main id="cont">
        <section>
            <?php include './components/adminNavbar.php'; ?>
        </section>

        <section>
            <h3><?= $tr->translate("Vendeurs a approuver") ?></h3>
            <br>
            <?php
            if($sellers):
                $activateText = $tr->translate("Activer le vendeur");
                ?>
            <ul class="admin-list">
                <?php foreach($sellers as $seller): ?>
                <li>
                    <div>
                        <a href="./seller.php?id=<?= $seller->seller_id ?>"><?= ucfirst($seller->seller_name) ?></a>
                    </div>
                    <div>
                        <p><?= $seller->seller_date_added ?></p>
                    </div>
                    <div>
                        <a href="adminToggleActivatedSeller.php?id=<?= $seller->seller_id ?>" class="no-style button"><?= $activateText ?></a>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
            <br>
            <?php else: ?>
            <p><?= $tr->translate($error_msg) ?></p>
            <?php if($nbTotalProducts > 0): ?>
            <a class="no-style button" href="./adminActiveSeller.php"><?= $tr->translate("Revenir à la page 1") ?></a>
            <?php endif; ?>
            <?php
            endif;

            // PAGINATION SYSTEM
            $linkPath = "adminActiveSeller.php?";

            include './components/pagination.php';

            ?>
        </section>
    </main>

<?php include './components/footer.php'; ?>
