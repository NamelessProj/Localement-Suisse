<?php
require_once './components/cartManager.php';

$title = "Localement Suisse | Login admin";
include './components/header.php';

require_once './db.php';

if(isset($_SESSION['user']['admin']) && !empty($_SESSION['user']['admin']) && isset($_SESSION['user']['id']) && !empty($_SESSION['user']['id']) && is_numeric($_SESSION['user']['id'])){
    // IF THE ADMIN SESSION IS ALREADY OPEN, WE CHECK IF THE USER IS AN ADMIN
    $sql = "SELECT * FROM users WHERE user_id = :user_id AND typ_id = :type_id";
    $req = $db->prepare($sql);
    $req->bindValue(':user_id', $_SESSION['user']['id'], PDO::PARAM_INT);
    $req->bindValue(':type_id', 2, PDO::PARAM_INT);
    $req->execute();
    $user = $req->fetch();
    if(!$user){
        header("Location: index.php");
        die("The user is not an admin.");
    }else{
        header("Location: adminHome.php");
        die("The user is an admin and is already logged in.");
    }
}

if(!empty($_POST)){
    if(isset($_POST['email']) && !empty($_POST['email']) && isset($_POST['password']) && !empty($_POST['password'])){
        $email = strip_tags($_POST['email']);
        $password = strip_tags($_POST['password']);

        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) die($tr->translate("Un problème est survenue lors de la conmnexion.")); // CHECKING IF THE MAIL IS VALID

        $sql = "SELECT * FROM users WHERE user_mail = :email AND typ_id = :typ_id";
        $req = $db->prepare($sql);
        $req->bindValue(':email', $email);
        $req->bindValue(':typ_id', 2, PDO::PARAM_INT);
        $req->execute();
        $user = $req->fetch();

        if(!$user || !password_verify($password, $user->user_password)){
            header("Location: adminLogin.php");
            die($tr->translate("Les informations sont incorrects."));
        }

        // THE USER'S INFORMATIONS ARE CORRECT
        $_SESSION['user']['admin'] = $user->user_uniqid;

        header("Location: adminHome.php");
        die();
    }
}

include './components/navbar.php';

?>

<header class="hero">
    <div class="hero-infos">
        <h1><?= $tr->translate("Se connecter au tableau de bords admin") ?></h1>
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

            <footer class="form-footer">
                <button type="submit"><?= $tr->translate("Se connecter au tableau de bords") ?></button>
            </footer>
        </div>
    </form>
</section>

<?php include './components/footer.php'; ?>