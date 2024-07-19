<?php
require_once './components/cartManager.php';

$title = "Localement Suisse | Contact";
include './components/header.php';

if(isset($_POST)){
    if(isset($_POST['email'], $_POST['name'], $_POST['msg']) && !empty($_POST['email']) && !empty($_POST['name']) && !empty($_POST['msg'])){
        if(!filter_var(strip_tags($_POST['email']), FILTER_VALIDATE_EMAIL)) die($tr->translate("L'adresse email n'est pas valide."));
        $name = ucfirst(strip_tags(trim($_POST['name'])));
        $msg = strip_tags($_POST['msg']);

        require_once './components/sendMail.php';

        $replyTo = [
            "address" => strip_tags($_POST['email']),
            "name" => $name
        ];

        $to = [
            "address" => "pintokevin2002@hotmail.com",
            "name" => "Admin Localement Suisse"
        ];

        $msgBody = [
            "html" => "<b>From $name</b><br>Probably in <i>".ucfirst($lang)."</i><br><br>".nl2br($msg),
            "alt" => "From $name Probably in $lang - $msg"
        ];

        $mailIsSend = sendMail($to, $replyTo,"Contact Form", $msgBody);
    }
}
require_once './db.php';
include './components/navbar.php';
?>

<header class="hero">
    <div class="hero-infos hero-infos-solo">
        <h1><?= $tr->translate("Nous contacter") ?></h1>
    </div>
</header>

<?php if(isset($mailIsSend) && $mailIsSend['error'] !== ''): ?>
    <div class="notification error">
        <p><?= $mailIsSend['error'] ?></p>
    </div>
<?php endif; ?>

<section>
    <form method="post">
        <div class="form-container">
            <div class="user-box">
                <input type="email" inputmode="email" name="email" id="email" required>
                <label><?= $tr->translate("Votre Email") ?></label>
            </div>

            <div class="user-box">
                <input type="text" name="name" id="name" required>
                <label><?= $tr->translate("Votre nom") ?></label>
            </div>

            <div class="user-box">
                <textarea name="msg" id="msg" cols="30" rows="10" placeholder="<?= $tr->translate("Votre message") ?>..." required></textarea>
            </div>

            <footer class="form-footer">
                <button type="submit"><?= $tr->translate("Nous contacter") ?></button>
            </footer>
        </div>
    </form>
</section>

<?php include './components/footer.php' ?>