<?php
require_once './components/cartManager.php';

$title = "Localement Suisse | 404";
include './components/header.php';
require_once './db.php';
include './components/navbar.php';
?>

<header class="hero er404Hero">
    <picture class="bg-img">
        <img src="./imgs/mount_01.jpg" alt="">
    </picture>
    <div class="hero-content">
        <h1 class="important">404</h1>
    </div>
</header>

<main id="main" class="er404">
    <section class="content-center">
        <h3><?= $tr->translate("Oops, nous avons pas pu trouvé cette ressource.") ?></h3>
        <p><?= $tr->translate("Réessayer plus tard.") ?></p>
        <a href="./index.php" class="no-style button"><?= $tr->translate("Revenir à l'accueil") ?></a>
    </section>
</main>

<?php include './components/footer.php'?>