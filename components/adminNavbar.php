<?php
if(!isset($adminCurrentPage)) $adminCurrentPage = "";
$adminNavItems = [
    "Accueil" => "adminHome.php",
    "Activer des vendeurs" => "adminActiveSeller.php",
    "Vendeurs" => "adminSeller.php",
];
?>
<header>
    <nav id="adminNav">
        <ul>
            <?php while($currentItem = current($adminNavItems)): ?>
                <li>
                    <a href="./<?= $currentItem ?>" <?php if(key($adminNavItems) == $adminCurrentPage){echo 'class="active"';} ?>>
                        <?= key($adminNavItems) ?>
                    </a>
                </li>
            <?php
                next($adminNavItems);
            endwhile;
            ?>
        </ul>
    </nav>
</header>