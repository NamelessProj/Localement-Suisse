<?php
require_once './components/cartManager.php';

require_once './db.php';

$title = "Localement Suisse | FAQ";
include './components/header.php';
include './components/navbar.php';

$sql = "SELECT faq_title_$lang AS title, faq_text_$lang AS text FROM faqs";
$req = $db->query($sql);
$faqs = $req->fetchAll();

$faqNumber = 0;

?>

<header class="hero">
    <div class="hero-infos hero-infos-solo">
        <h1><?= $tr->translate("FAQs") ?></h1>
    </div>
</header>

<main id="cont">
    <section>
        <?php if($faqs): ?>
        <div class="accordeon">

            <?php
            foreach($faqs as $faq):
                $faqNumber++;
                $currentFaq = $faqNumber;
                if(strlen($faqNumber) < 2) $currentFaq = '0'.$faqNumber;
                ?>
            <div role="button" class="item <?php if($faqNumber === 1){echo 'open';} ?>">
                <p class="number"><?= $currentFaq ?></p>
                <p class="text"><?= $faq->title ?></p>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="icon">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                </svg>
                <div class="hidden-box">
                    <p><?= $faq->text ?></p>
                </div>
            </div>
            <?php endforeach; ?>

        </div>
        <?php else: ?>
        <p><?= $tr->translate("Il n'y pas encore de FAQ de disponible.") ?></p>
        <?php endif; ?>
    </section>
</main>

<?php include './components/footer.php'?>