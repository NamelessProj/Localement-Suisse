<?php
require_once './components/cartManager.php';

require_once './db.php';

$title = "Localement Suisse | Register Seller";
include './components/header.php';

// IF THE SESSION IS SET, THE USER DOESN'T NEED TO BE HERE
if(isset($_SESSION['user']['id'])){
    header("Location: ./account.php");
    die();
}

if(!empty($_POST)){
    if(isset($_POST['name'], $_POST['email'], $_POST['password'], $_POST['street'], $_POST['city'], $_POST['canton'], $_POST["bio"], $_POST["links"]) && !empty($_POST['name']) && !empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['street']) && !empty($_POST['city']) && !empty($_POST['canton']) && !empty($_POST['bio']) && !empty($_POST['links'])){
        // FORM IS COMPLETE
        // GETTING ALL DATA
        if(!filter_var(strip_tags($_POST['email']), FILTER_VALIDATE_EMAIL)) die($tr->translate("L'adresse email n'est pas valide."));
        $sellername = strip_tags(trim($_POST['name']));
        $password = strip_tags($_POST['password']);
        $street = strip_tags(trim($_POST['street']));
        $city = strip_tags(trim($_POST['city']));
        $canton = strip_tags(trim($_POST['canton']));
        $bio = strip_tags($_POST['bio']);

        /* CHECK IF PASSWORD HAS:
         * - AT LEST 1 LOWER CHARACTER
         * - AT LEST 1 UPPER CHARACTER
         * - AT LEST 1 NUMBER
         * - AT LEST 1 SPECIAL CHARACTER FROM THE LIST (@$!%*?&)
         * - THERE'S NO SPACE
         * - AT LEST 8 CHARACTERS
        */
        if(!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)){
            die("The password does not appear to be valid.");
        }

        // FORMATTING THE SOCIALS LINKS OF THE SELLER
        // WE GET RID OF USELESS "SPACES", "COMA" AND "RETURN" TO GET A SIMPLE STRING
        $links = strip_tags($_POST['links']);
        $links = str_replace("\n", ' ', $links);
        $links = preg_replace('/\s+/', ' ', $links); /* TODO: CHECK IF IT WOULDN'T BETTER TO PUT IT THAT AS LAST STEP */
        $links = str_replace(",", '', $links);

        // HASH PASSWORD
        $password = password_hash($password, PASSWORD_ARGON2ID);

        $sql = "SELECT * FROM sellers WHERE seller_mail = :email";
        $req = $db->prepare($sql);
        $req->bindValue(':email', $_POST['email']);
        $req->execute();
        $mailIsUsed = $req->fetch();

        if($mailIsUsed) die($tr->translate("L'adresse mail est déjà utilisée.")); // CHECK IF EMAIL IS ALREADY USED

        $uniqId = md5(uniqid()); // GETTING A UNIQ ID FOR SECURITY


        // IF EMAIL ISN'T USED, WE CREATE NEW SELLER
        $sql = "INSERT INTO sellers (seller_name, seller_mail, seller_password, seller_address_street, seller_address_city, seller_address_canton, seller_bio, seller_socials,seller_uniqid, seller_date_added) VALUES (:username, :email, :password, :street, :city, :canton, :bio, :links, :uniqid, :dateAdded)";
        $req = $db->prepare($sql);
        $req->bindValue(':username', $sellername);
        $req->bindValue(':email', $_POST['email']);
        $req->bindValue(':password', $password);
        $req->bindValue(':street', $street);
        $req->bindValue(':city', $city);
        $req->bindValue(':canton', $canton);
        $req->bindValue(':bio', $bio);
        $req->bindValue(':links', $links);
        $req->bindValue(':uniqid', $uniqId);
        $req->bindValue(':dateAdded', date('Y-m-d H:i:s'));
        $req->execute();

        $sellerId = $db->lastInsertId();

        // IF THE SELLER WANT HIS ADDRESS TO BE DISPLAYED ON HIS PAGE
        if(isset($_POST['addressVisible']) && !empty($_POST['addressVisible'])){
            $visibleCode = $_POST['addressVisible'] == 1 ? 1 : 0;
        }else $visibleCode = 0;
        $sql = "UPDATE sellers SET seller_address_visible = :visible_code WHERE seller_id = :seller_id";
        $req = $db->prepare($sql);
        $req->bindValue(':visible_code', $visibleCode, PDO::PARAM_INT);
        $req->bindValue(':seller_id', $sellerId, PDO::PARAM_INT);
        $req->execute();

        // GET ID OF NEW USER AND SETTING IT FOR THE SESSION
        $_SESSION["user"] = [
            "id" => $sellerId,
            "uniq" => $uniqId,
            "type" => 'v' // WE SPECIFY THAT THIS IS A SELLER
        ];

        // SENDING A MAIL TO THE ADMIN TO INFORM THEM THERE'S A NEW SELLER READY TO GET ACTIVATED
        require_once './components/sendMail.php';

        $replyTo = [
            "address" => "",
            "name" => "Do not reply to this email."
        ];

        $to = [ // ADDRESS OF THE ADMIN
            "address" => "pintokevin2002@hotmail.com",
            "name" => "Admin Localement Suisse"
        ];

        $msgBody = [
            "html" => "New Seller.<br>He's ready to get activated.<br><br>Seller name: <b>$sellername</b><br>Seller Id: <b>$sellerId</b>",
            "alt" => "New Seller. He's ready to get activated. Seller name: $sellername, Seller Id: $sellerId"
        ];

        $mailIsSend = sendMail($to, $replyTo,"New Seller", $msgBody);

        // HANDLING IMG
        // CHECKING FOR THE IMG OF THE SELLER
        if(isset($_FILES['img']['name']) && !empty($_FILES['img']['name'] && $_FILES['img']['error'] === 0)){
            require_once './imagesUpload.php';
            $uploadFile = uploadImage($_FILES['img'], "sellers");

            if($uploadFile['error']){
                die($tr->translate("Une erreur est survenue lors du téléchargement de l'image."));
            }

            $sql = "UPDATE sellers SET seller_img = :img WHERE seller_id = :id"; // INSERTING THE IMG INTO THE DB
            $req = $db->prepare($sql);
            $req->bindValue(':img', $uploadFile['name']);
            $req->bindValue(':id', $sellerId, PDO::PARAM_INT);
            if(!$req->execute()){
                die($tr->translate("Oups, une erreur est survenue."));
            }
        }

        header('Location: ./sellerAccount.php');
        die();
    }else die($tr->translate("Le formulaire n'est pas valide."));
}

include './components/navbar.php';

?>

<header class="hero">
    <div class="hero-infos hero-infos-solo">
        <h1><?= $tr->translate("S'enrengistrer comme vendeur") ?></h1>
        <a href="./register.php" class="hero-link" style="--_link-color: var(--clr-800);">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" fill="currentColor" height="20">
                <path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z"/>
            </svg>
            <span><?= $tr->translate("S'enrengistrer comme client") ?></span>
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

            <label for="addressVisible" class="label_cont">
                <span><?= $tr->translate("Afficher votre adresse") ?></span>
                <input type="checkbox" name="addressVisible" id="addressVisible" value="1" checked>
                <span class="checkmark"></span>
            </label>

            <div class="user-box">
                <textarea name="bio" id="bio" cols="30" rows="10" placeholder="<?= $tr->translate("Votre biographie") ?>"></textarea>
            </div>

            <div class="user-box">
                <textarea name="links" id="links" cols="30" rows="10" placeholder="<?= $tr->translate("Vos réseaux sociaux") ?>"></textarea>
                <p><?= $tr->translate("Séparer chaque lien avec un retour à la ligne.") ?></p>
            </div>

            <br>

            <div class="user-box" style="margin-top: 1rem;">
                <input type="file" name="img" id="img" accept="image/png, image/jpeg" required>
                <label><?= $tr->translate("Votre image de présentation") ?></label>
            </div>

            <footer class="form-footer">
                <button type="submit"><?= $tr->translate("S'enrengistrer comme vendeur") ?></button>
            </footer>
        </div>
    </form>
</section>

<?php include './components/footer.php'; ?>
