<?php
require_once './components/cartManager.php';

require_once './db.php';

$title = "Localement Suisse | Register";
include './components/header.php';

// IF THE SESSION IS SET, THE USER DOESN'T NEED TO BE HERE
if(isset($_SESSION['user']['id'])){
    header("Location: ./account.php");
    die();
}

if(!empty($_POST)){
    if(isset($_POST['name'], $_POST['email'], $_POST['password'], $_POST['street'], $_POST['city'], $_POST['canton']) && !empty($_POST['name']) && !empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['street']) && !empty($_POST['city']) && !empty($_POST['canton'])){
        // FORM IS COMPLETE
        // GETTING ALL DATA
        $username = strip_tags(trim($_POST['name']));
        if(!filter_var(strip_tags($_POST['email']), FILTER_VALIDATE_EMAIL)) die("L'adresse email n'est pas valide.");
        $password = strip_tags($_POST['password']);
        $street = strip_tags(trim($_POST['street']));
        $city = strip_tags(trim($_POST['city']));
        $canton = strip_tags(trim($_POST['canton']));

        /* CHECK IF PASSWORD HAS:
         * - AT LEST 1 LOWER CHARACTER
         * - AT LEST 1 UPPER CHARACTER
         * - AT LEST 1 NUMBER
         * - AT LEST 1 SPECIAL CHARACTER FROM THE LIST (@$!%*?&)
         * - THERE'S NO SPACE
         * - AT LEST 8 CHARACTERS
        */
        if(!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)){
            die("Your password doesn't meet the requirements.");
        }

        // HASH PASSWORD
        $password = password_hash($password, PASSWORD_ARGON2ID);

        // CHECK IF EMAIL IS ALREADY USED
        $sql = "SELECT * FROM users WHERE user_mail = :email";
        $req = $db->prepare($sql);
        $req->bindValue(':email', $_POST['email']);
        $req->execute();
        $mailIsUsed = $req->fetch();

        if($mailIsUsed) die("L'adresse mail est déjà utilisée."); // CHECKING IF THE MAIL IS ALREADY USED

        $uniqId = md5(uniqid()); // GETTING A UNIQ ID FOR SECURITY


        // IF EMAIL ISN'T USED, WE CREATE NEW USER
        $sql = "INSERT INTO users (user_uniqid, user_pseudo, user_mail, user_password, user_address_street, user_address_city, user_address_canton, typ_id) VALUES (:uniqid, :username, :email, :password, :street, :city, :canton, :typeId)";
        $req = $db->prepare($sql);
        $req->bindValue(':uniqid', $uniqId);
        $req->bindValue(':username', $username);
        $req->bindValue(':email', $_POST['email']);
        $req->bindValue(':password', $password);
        $req->bindValue(':street', $street);
        $req->bindValue(':city', $city);
        $req->bindValue(':canton', $canton);
        $req->bindValue(':typeId', 1, PDO::PARAM_INT);
        $req->execute();

        // GET ID OF NEW USER AND SETTING IT FOR THE SESSION
        $_SESSION["user"] = [
            "id" => $db->lastInsertId()
        ];

        header('Location: ./index.php');
    }else die("Le formulaire n'est pas valide.");
}

include './components/navbar.php';

?>

<header class="hero">
    <div class="hero-infos hero-infos-solo">
        <h1><?= $tr->translate("S'enrengistrer comme client") ?></h1>
        <a href="./registerSeller.php" class="hero-link" style="--_link-color: var(--clr-800);">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" fill="currentColor" height="20">
                <path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z"/>
            </svg>
            <span><?= $tr->translate("S'enrengistrer comme vendeur") ?></span>
        </a>
        <a href="./login.php" class="hero-link" style="--_link-color: var(--clr-800);">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" fill="currentColor" height="20">
                <path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z"/>
            </svg>
            <span><?= $tr->translate("Se connecter") ?></span>
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
                <input type="text" name="name" id="name" required>
                <label><?= $tr->translate("Nom") ?></label>
            </div>

            <p><?= $tr->translate("Votre mot de passe doit contenir au minimum:") ?></p>
            <ul>
                <li><?= $tr->translate("8 caractères") ?></li>
                <li><?= $tr->translate("1 caractère minuscule") ?></li>
                <li><?= $tr->translate("1 caractère majuscule") ?></li>
                <li><?= $tr->translate("1 caractère spécial (@$!%*?&)") ?></li>
                <li><?= $tr->translate("Pas d'espace autorisé") ?></li>
            </ul>
            <div class="user-box">
                <input type="password" inputmode="password" name="password" id="password" required>
                <label><?= $tr->translate("Mot de passe") ?></label>
            </div>

            <fieldset>
                <legend><?= $tr->translate("Adresse") ?></legend>

                <div class="user-box">
                    <input type="text" name="street" id="street" required>
                    <label><?= $tr->translate("Rue et N⁰") ?></label>
                </div>
                <div class="user-box">
                    <input type="text" name="city" id="city" required>
                    <label><?= $tr->translate("Ville") ?></label>
                </div>
                <div class="user-box">
                    <input type="text" name="canton" id="canton" required>
                    <label><?= $tr->translate("Canton") ?></label>
                </div>
            </fieldset>

            <footer class="form-footer">
                <button type="submit"><?= $tr->translate("S'enrengistrer") ?></button>
            </footer>
        </div>
    </form>
</section>

<?php include './components/footer.php'; ?>
