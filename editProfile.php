<?php
require_once './components/cartManager.php';

// CHECK IF THERE'S AN ID
if(!isset($_SESSION['user']['id']) || empty($_SESSION['user']['id']) || !is_numeric($_SESSION['user']['id']) || !isset($_SESSION['user']['uniq']) || empty($_SESSION['user']['uniq']) || $_SESSION['user']['type'] !== "v"){
    // NO ID
    header('location: ./index.php');
    die("There's no seller account ID");
}

$id = strip_tags($_SESSION['user']['id']);

require_once './db.php';

if(!empty($_POST) && isset($_POST['send'])){
    if(isset($_POST['name'], $_POST['email'], $_POST['street'], $_POST['city'], $_POST['canton'], $_POST["bio"], $_POST["links"]) && !empty($_POST['name']) && !empty($_POST['email']) && !empty($_POST['street']) && !empty($_POST['city']) && !empty($_POST['canton']) && !empty($_POST['bio']) && !empty($_POST['links'])){
        // FORM IS COMPLETE
        // GETTING ALL DATA
        $sellername = strip_tags($_POST['name']);
        if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) die("L'adresse email n'est pas valide.");
        $street = strip_tags(trim($_POST['street']));
        $city = strip_tags(trim($_POST['city']));
        $canton = strip_tags(trim($_POST['canton']));
        $bio = strip_tags($_POST['bio']);

        $links = strip_tags($_POST['links']);
        $links = str_replace("\n", ' ', $links);
        $links = preg_replace('/\s+/', ' ', $links); /* TODO: CHECK IF IT WOULDN'T BETTER TO PUT IT THAT AS LAST STEP */
        $links = str_replace(",", '', $links);

        // IF EMAIL ISN'T USED, WE CREATE NEW USER
        $sql = "UPDATE sellers SET seller_name = :username, seller_mail = :email, seller_address_street = :street, seller_address_city = :city, seller_address_canton = :canton, seller_bio = :bio, seller_socials = :links WHERE seller_id = :id";
        $req = $db->prepare($sql);
        $req->bindValue(':username', $sellername);
        $req->bindValue(':email', $_POST['email']);
        $req->bindValue(':street', $street);
        $req->bindValue(':city', $city);
        $req->bindValue(':canton', $canton);
        $req->bindValue(':bio', $bio);
        $req->bindValue(':links', $links);
        $req->bindValue(':id', $id, PDO::PARAM_INT);
        $req->execute();

        // HANDLE IMG

        // HANDLE IMG
        // CHECKING FOR THE PRESENTATION IMG OF PRODUCT
        if(isset($_FILES['img']['name']) && !empty($_FILES['img']['name'] && $_FILES['img']['error'] === 0)){
            // GETTING THE PREVIOUS IMG
            $sql = "SELECT seller_img FROM sellers WHERE seller_id = :id";
            $req = $db->prepare($sql);
            $req->bindValue(':id', $id, PDO::PARAM_INT);
            $req->execute();
            $sellerImg = $req->fetch()->seller_img;

            require_once './imagesUpload.php';
            $uploadFile = uploadImage($_FILES['img'], "sellers");

            if($uploadFile['error']){
                die($tr->translate("Une erreur est survenue lors du téléchargement de l'image."));
            }

            // DELETING THE PREVIOUS IMG BEFORE UPLOADING THE NEW ONE
            if(file_exists(__DIR__."/imgs/sellers/".$sellerImg.".jpg")) unlink(__DIR__."/imgs/sellers/".$sellerImg.".jpg");
            if(file_exists(__DIR__."/imgs/sellers/mini_".$sellerImg.".jpg")) unlink(__DIR__."/imgs/sellers/mini_".$sellerImg.".jpg");

            $sql = "UPDATE sellers SET seller_img = :img"; // INSERTING THE IMG INTO THE DB
            $req = $db->prepare($sql);
            $req->bindValue(':img', $uploadFile['name']);
            if(!$req->execute()){
                die($tr->translate("Oups, une erreur est survenue."));
            }
        }

    }else echo($tr->translate("Le formulaire n'est pas valide."));
}

// SETTING A NEW PASSWORD
if(!empty($_POST) && isset($_POST['newPassword'])){
    if(isset($_POST['password']) && !empty($_POST['password'])){
        $password = strip_tags($_POST['password']);
        $password = password_hash($password, PASSWORD_ARGON2ID); // HASH PASSWORD

        $sql = "UPDATE sellers SET seller_password = :password WHERE seller_id = :id";
        $req = $db->prepare($sql);
        $req->bindValue(':password', $password);
        $req->bindValue(':id', $id, PDO::PARAM_INT);
        $req->execute(); // UPDATING THE PASSWORD OF THE USER
    }else echo($tr->translate("Le formulaire n'est pas valide."));
}

// GETTING ALL DATA OF THE USER
$sql = "SELECT * FROM sellers WHERE seller_id = :id";
$req = $db->prepare($sql);
$req->bindValue(':id', $id, PDO::PARAM_INT);
$req->execute();
$seller = $req->fetch();

if(!$seller){
    header('Location: index.php');
    die();
}

$title = "Localement Suisse | Edit ".ucfirst(strip_tags($seller->seller_name));
include './components/header.php';

$sellerImg = $seller->seller_img !== '' ? "sellers/".$seller->seller_img : "default.jpg";

include './components/navbar.php';
?>

<header class="hero">
    <picture class="bg-img">
        <img src="./imgs/<?= strip_tags($sellerImg) ?>" alt="">
    </picture>
    <div class="hero-content">
        <h1><?= $tr->translate("Modifier votre profile") ?>: <span class="important"><?= ucfirst(strip_tags($seller->seller_name)) ?></span></h1>
        <p>
            <a class="no-style button" href="./sellerAccount.php"><?= $tr->translate("Revenir") ?></a>
        </p>
    </div>
</header>

<main id="cont">
    <section>
        <form method="post" enctype="multipart/form-data">
            <div class="form-container">
                <div class="user-box">
                    <input type="email" inputmode="email" name="email" id="email" value="<?= strip_tags($seller->seller_mail) ?>" required>
                    <label>Email</label>
                </div>

                <div class="user-box">
                    <input type="text" name="name" id="name" value="<?= strip_tags($seller->seller_name) ?>" required>
                    <label><?= $tr->translate("Votre nom de vendeur") ?></label>
                </div>

                <div class="user-box">
                    <?php $descText = $tr->translate("Votre description"); ?>
                    <label><?= $descText ?></label>
                    <textarea name="description" id="description" cols="30" rows="10" placeholder="<?= $descText ?>"><?= $seller->seller_bio ?></textarea>
                </div>

                <fieldset>
                    <legend><?= $tr->translate("Adresse") ?></legend>

                    <div class="user-box">
                        <input type="text" name="street" id="street" value="<?= strip_tags($seller->seller_address_street) ?>" required>
                        <label><?= $tr->translate("Rue et N⁰") ?></label>
                    </div>
                    <div class="user-box">
                        <input type="text" name="city" id="city" value="<?= strip_tags($seller->seller_address_city) ?>" required>
                        <label><?= $tr->translate("Ville") ?></label>
                    </div>
                    <div class="user-box">
                        <input type="text" name="canton" id="canton" value="<?= strip_tags($seller->seller_address_canton) ?>" required>
                        <label><?= $tr->translate("Canton") ?></label>
                    </div>
                </fieldset>

                <div class="user-box">
                    <?php $rsText = $tr->translate("Vos réseaux sociaux"); ?>
                    <label><?= $rsText ?></label>
                    <textarea name="links" id="links" cols="30" rows="10" placeholder="<?= $rsText ?>"><?= $seller->seller_socials ?></textarea>
                    <p><?= $tr->translate("Séparez chaque lien avec un espace.") ?></p>
                </div>

                <br>

                <fieldset>
                    <?php $notObligText = $tr->translate("Cette option n'est pas obligatoire."); ?>
                    <p style="margin-top: 1.5rem;"><?= $notObligText ?></p>
                    <div class="user-box" style="margin-top: 1rem;">
                        <input type="file" name="firstImag" id="firstImag" accept="image/png, image/jpeg">
                        <label><?= $tr->translate("Changer votre image") ?></label>
                    </div>
                </fieldset>

                <footer class="form-footer">
                    <button type="submit" name="send"><?= $tr->translate("Modifier votre profile") ?></button>
                </footer>
            </div>
        </form>
    </section>

    <div class="divider"></div>

    <section>
        <form method="post">
            <fieldset class="form-container">
                <div class="user-box">
                    <input type="password" name="password" id="password" required>
                    <label><?= $tr->translate("Entrez votre nouveau mot de passe") ?></label>
                </div>

                <footer class="form-footer">
                    <button type="submit" name="newPassword"><?= $tr->translate("Changer votre mot de passe") ?></button>
                </footer>
            </fieldset>
        </form>
    </section>
</main>

<?php include './components/footer.php'; ?>