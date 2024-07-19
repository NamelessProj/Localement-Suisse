<?php
require_once './components/cartManager.php';

$title = "Localement Suisse | Blogs";
include './components/header.php';

// FROM: https://stackoverflow.com/questions/79960/how-to-truncate-a-string-in-php-to-the-word-closest-to-a-certain-number-of-chara
function tokenTruncate($string, $textWidth){
    $parts = preg_split('/([\s\n\r]+)/u', $string, 0, PREG_SPLIT_DELIM_CAPTURE);
    $partsCount = count($parts);

    $length = 0;
    $last_part = 0;
    for(; $last_part < $partsCount; ++$last_part){
        $length += strlen($parts[$last_part]);
        if($length > $textWidth) break;
    }
    $returnString = implode(array_slice($parts, 0, $last_part));
    if(strlen($string) > strlen($returnString)) $returnString.="...";
    return $returnString;
}

require_once './db.php';
// GETTING ALL BLOGS
$sql = "SELECT * FROM blogs ORDER BY blo_date DESC";
$req = $db->query($sql);
$blogs = $req->fetchAll();

$knowMoretext = $tr->translate("Lire plus");

include './components/navbar.php';
?>

<header class="hero">
    <div class="hero-infos hero-infos-solo">
        <h1><?= $tr->translate("Blogs") ?></h1>
    </div>
</header>

<main class="cont">
    <section class="container">
        <?php if($blogs): ?>
        <ul class="accordeon">
            <?php foreach($blogs as $blog): ?>
            <li class="blog">
                <h3><?= $trAuto->translate(strip_tags($blog->blo_title)) ?></h3>
                <p><?= $trAuto->translate(tokenTruncate(strip_tags($blog->blo_text), 200)) ?></p>
                <div class="blog-date">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor" width="20">
                        <path d="M464 256A208 208 0 1 1 48 256a208 208 0 1 1 416 0zM0 256a256 256 0 1 0 512 0A256 256 0 1 0 0 256zM232 120V256c0 8 4 15.5 10.7 20l96 64c11 7.4 25.9 4.4 33.3-6.7s4.4-25.9-6.7-33.3L280 243.2V120c0-13.3-10.7-24-24-24s-24 10.7-24 24z"/>
                    </svg>
                    <span><?= $blog->blo_date ?></span>
                </div>
                <a class="no-style button hero-link" href="./blog.php?id=<?= $blog->blo_id ?>" style="--_padding: 15px; --_svg-translate: 10px; --_is-hidden: hidden;">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" fill="currentColor" height="20">
                        <path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z"/>
                    </svg>
                    <span><?= $knowMoretext ?></span>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php else: ?>
        <p style="margin: 0 auto; text-align: center; text-wrap: balance;"><?= $tr->translate("Il n'y a pas encore de blogs.") ?></p>
        <?php endif; ?>
    </section>
</main>

<?php include './components/footer.php'?>