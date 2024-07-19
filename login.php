<?php
require_once './components/cartManager.php';

require_once './db.php';

$title = "Localement Suisse | Login";
include './components/header.php';

// IF THE USER'S ALREADY LOGGED HE SHOULDN'T BE HERE
if(isset($_SESSION['user']['id'])){
    $previousPage = $_SERVER['HTTP_REFERER'] ?? 'index.php';
    header('Location: '.$previousPage); // GOING TO THE PREVIOUS PAGE
    die();
}

if(!empty($_POST)){
    // SETTING WHERE WE SHOULD LOGGED THE USER
    $table = 'users';
    $mailText = 'user_mail';
    if(isset($_POST['connectAsSeller']) && !empty($_POST['connectAsSeller'])){
        $table = 'sellers';
        $mailText = 'seller_mail';
    }

    // CHECKING IF EVERY FIELD IS FILLED
    if(isset($_POST['email'], $_POST['password']) && !empty($_POST['email']) && !empty($_POST['password'])){
        if(!filter_var(strip_tags($_POST['email']), FILTER_VALIDATE_EMAIL)) die("L'adresse email n'est pas valide."); // CHECKING IF THE MAIL IS VALID

        // CHECKING IF THE USER EXIST IN THE DB
        $sql = "SELECT * FROM $table WHERE $mailText = :email";
        $req = $db->prepare($sql);
        $req->bindValue(':email', $_POST['email']);
        $req->execute();
        $user = $req->fetch();

        if(!$user) die("Les informations de connections sont incorectes.");

        // IF EMAIL DOES EXIST IN DB
        if($table === 'sellers'){
            // THE SELLER EXIST SO WE CHECK THE PASSWORD
            if(!password_verify($_POST['password'], $user->seller_password)) die("Les informations de connections sont incorectes.");

            $userId = $user->seller_id;

            // WE CONNECT THE USER
            $_SESSION["user"] = [
                "id" => $userId,
                "uniq" => $user->seller_uniqid,
                "type" => "v"
            ];
        }else{
            // THE USER EXIST SO WE CHECK THE PASSWORD
            if(!password_verify($_POST['password'], $user->user_password)) die("Les informations de connections sont incorectes.");

            $userId = $user->user_id;

            // WE CONNECT THE USER
            $_SESSION["user"] = [
                "id" => $userId,
                "uniq" => $user->user_uniqid
            ];

            // CHECK IF THE USER HAD A CART WHILE NOT CONNECTED
            $sql = "SELECT * FROM carts WHERE cookie_id = :cookie_id AND status_id = :status_id ORDER BY cart_date DESC";
            $req = $db->prepare($sql);
            $req->bindValue(':cookie_id', $_COOKIE['cartId']);
            $req->bindValue(':status_id', 1, PDO::PARAM_INT);
            $req->execute();
            $cart = $req->fetch();

            if($cart){
                $sql = "UPDATE carts SET user_id = :user_id WHERE cart_id = :cart_id";
                $req = $db->prepare($sql);
                $req->bindValue(':user_id', $userId, PDO::PARAM_INT);
                $req->bindValue(':cart_id', $cart->cart_id, PDO::PARAM_INT);
                $req->execute();
            }
        }

        header("Location: account.php"); // WE REDIRECT THE USER TO HIS ACCOUNT
        exit();
    }else die($tr->translate("Le formulaire n'est pas valide."));
}

include './components/navbar.php';

?>

<header class="hero">
    <div class="hero-infos hero-infos-solo">
        <h1><?= $tr->translate("Se connecter") ?></h1>
        <a href="./register.php" class="hero-link" style="--_link-color: var(--clr-800);">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" fill="currentColor" height="20">
                <path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z"/>
            </svg>
            <span><?= $tr->translate("S'enrengistrer") ?></span>
        </a>
    </div>
</header>

<section>
    <form method="post">
        <div class="form-container">
            <div class="user-box">
                <input type="email" inputmode="email" name="email" id="email" required>
                <label>Email</label>
            </div>

            <div class="user-box">
                <input type="password" inputmode="password" name="password" id="password" required>
                <label><?= $tr->translate("Mot de passe") ?></label>
            </div>

            <div class="user-box">
                <input type="checkbox" name="connectAsSeller" id="connectAsSeller" value="seller">
                <label for="connectAsSeller"><?= $tr->translate("Se connecter en tant que vendeur") ?></label>
            </div>

            <footer class="form-footer">
                <button type="submit"><?= $tr->translate("Se connecter") ?></button>
            </footer>
        </div>
    </form>
</section>

<?php include './components/footer.php'; ?>