<nav class="navbar">
    <header>
        <div class="cont">
            <button type="button" class="navbar-toggle no-style" data-target="#navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>

                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            <a href="./index.php" class="nav-logo">
                <img src="./imgs/logo.jpeg" alt="Localement Suisse">
            </a>

            <div class="search-wrap">
                <form id="menuNavSearchBar" accept-charset="UTF-8" action="./search.php" method="get" role="search">
                    <input list="productsDatalist" type="search" name="q" id="search-nav" placeholder="<?= $tr->translate("Rechercher") ?>" aria-label="Rechercher" autocapitalize="off" autocomplete="off" autocorrect="off" spellcheck="false" minlength="1" <?php if(isset($_GET['q']) || !empty($_GET['q'])){echo "value='".strip_tags($_GET['q'])."'";} ?>>

                    <button type="button" class="search-cross no-style">
                        <svg role="img" xmlns="http://www.w3.org/2000/svg" height="20" aria-hidden="true" viewBox="0 0 384 512" focusable="false">
                            <path d="M376.6 84.5c11.3-13.6 9.5-33.8-4.1-45.1s-33.8-9.5-45.1 4.1L192 206 56.6 43.5C45.3 29.9 25.1 28.1 11.5 39.4S-3.9 70.9 7.4 84.5L150.3 256 7.4 427.5c-11.3 13.6-9.5 33.8 4.1 45.1s33.8 9.5 45.1-4.1L192 306 327.4 468.5c11.3 13.6 31.5 15.4 45.1 4.1s15.4-31.5 4.1-45.1L233.7 256 376.6 84.5z"/>
                        </svg>
                    </button>

                    <button type="submit" class="search-submit" aria-label="Rechercher">
                        <svg xmlns="http://www.w3.org/2000/svg" height="96%" fill="currentColor" viewBox="0 0 512 512">
                            <path d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z"/>
                        </svg>
                    </button>
                </form>
            </div>

            <?php $link = isset($_SESSION['user']['id']) ? 'account' : 'login'; ?>
            <a href="./<?= $link ?>.php" class="nav-items nav-items-account">
                <span class="sr-only"><?= $tr->translate("Mon compte") ?></span>
                <span class="profil">
                    <svg xmlns="http://www.w3.org/2000/svg" height="60%" width="60%" fill="currentColor" viewBox="0 0 448 512">
                        <path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z"/>
                    </svg>
                </span>
            </a>

            <?php if(!isset($_SESSION['user']['type'])): ?>
            <a href="./cart.php" class="nav-items nav-items-basket">
                <span class="sr-only"><?= $tr->translate("Mon panier") ?></span>
                <?php
                // GETTING THE NUMBER OF ITEMS IN THE USER'S CART
                $sql = "SELECT COUNT(*) AS count FROM products_carts LEFT JOIN carts ON carts.cart_id = products_carts.cart_id WHERE carts.status_id = :status_id AND (carts.user_id = :user_id OR carts.cookie_id = :cookie_id)";
                $req = $db->prepare($sql);
                $req->bindValue(':status_id', 1, PDO::PARAM_INT);
                $req->bindValue(':user_id', $userId, PDO::PARAM_INT);
                $req->bindValue(':cookie_id', $cookieId);
                $req->execute();
                $numberProducts = $req->fetch()->count;
                ?>
                <span class="cart-number"><?php if($numberProducts && $numberProducts > 0){ echo number_format($numberProducts, 0, '.', ' '); } ?></span>
                <div class="cart">
                    <svg xmlns="http://www.w3.org/2000/svg" height="60%" width="60%" fill="currentColor" viewBox="0 0 576 512">
                        <path d="M0 24C0 10.7 10.7 0 24 0H69.5c22 0 41.5 12.8 50.6 32h411c26.3 0 45.5 25 38.6 50.4l-41 152.3c-8.5 31.4-37 53.3-69.5 53.3H170.7l5.4 28.5c2.2 11.3 12.1 19.5 23.6 19.5H488c13.3 0 24 10.7 24 24s-10.7 24-24 24H199.7c-34.6 0-64.3-24.6-70.7-58.5L77.4 54.5c-.7-3.8-4-6.5-7.9-6.5H24C10.7 48 0 37.3 0 24zM128 464a48 48 0 1 1 96 0 48 48 0 1 1 -96 0zm336-48a48 48 0 1 1 0 96 48 48 0 1 1 0-96z"/>
                    </svg>
                </div>
            </a>
            <?php endif; ?>
        </div>
    </header>

    <div class="navbar-collapse" id="navbar-collapse-1">
        <ul class="nav">
            <li>
                <a href="./search.php">
                    <?= $tr->translate("Boutique") ?>
                </a>
            </li>
            <li>
                <a href="./categories.php">
                    <?= $tr->translate("Catégories") ?>
                </a>
            </li>
            <li>
                <a href="./sellers.php">
                    <?= $tr->translate("Commerçants") ?>
                </a>
            </li>
            <li>
                <a href="./blogs.php">
                    <?= $tr->translate("Blogs") ?>
                </a>
            </li>
            <li>
                <a href="./faq.php">
                    <?= $tr->translate("FAQ") ?>
                </a>
            </li>
            <li>
                <a href="./contact.php">
                    <?= $tr->translate("Contact") ?>
                </a>
            </li>
        </ul>
    </div>
</nav>

<datalist id="productsDatalist"></datalist>