<?php
require_once './components/cartManager.php';

// CHECK IF THERE'S AN ID
if(!isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id'])){
    header("Location: blogs.php");
    die("There's no blog ID.");
}

// TODO: REDO THE DB TO DELL THE LANGUAGE PART OF blogs -> blo_text, blo_title

$id = $_GET['id'];

require_once './db.php';
// GETTING THE BLOG
$sql = "SELECT * FROM blogs WHERE blo_id = :id";
$req = $db->prepare($sql);
$req->bindValue(':id', $id, PDO::PARAM_INT);
$req->execute();
$blog = $req->fetch();

$titleName = $blog ? $trAuto->translate(strip_tags($blog->blo_title)) : '404';

$title = "Localement Suisse | Blog - ".$titleName;
include './components/header.php';
include './components/navbar.php';
?>

<header class="hero">
    <div class="hero-infos hero-infos-solo">
        <h1><?= $titleName ?></h1>
    </div>
</header>

<main class="cont">
    <section class="container">
        <?php
        if($blog):
            $Parsedown = new Parsedown();
            $Parsedown->setSafeMode(true);
            ?>
            <div class="text-content">
                <?= $Parsedown->text($trAuto->translate(strip_tags($blog->blo_text))) ?>
            </div>
        <?php else: ?>
            <p style="margin: 3rem auto; text-align: center; text-wrap: balance;"><?= $tr->translate("Il n'y a pas de blog correspondant à cette ID.") ?></p>
        <?php endif; ?>
    </section>
</main>

<script>
    const titles = document.querySelectorAll('.text-content :is(h1, h2, h3, h4, h5, h6)');
    titles.forEach(title => {
        let url = title.innerText;
        url = url.replace(/\s+/g, '-').toLocaleLowerCase();
        url = url.replaceAll("'", '');
        title.id = url;
    });
</script>

<?php include './components/footer.php'?>