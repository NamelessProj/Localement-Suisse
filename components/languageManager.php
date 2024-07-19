<?php
use Stichoza\GoogleTranslate\GoogleTranslate;

$languageArray = ["fr", "en", "de", "it"];

// CHECK IF THE GIVEN LANGUAGE IS ONE AUTHORIZED, ELSE WE PUT 'fr' BY DEFAULT
$lang = (isset($_COOKIE['lang']) && !empty($_COOKIE['lang']) && in_array(strtolower($_COOKIE['lang']), $languageArray)) ? trim(strtolower($_COOKIE['lang'])) : 'fr';

setcookie('lang', $lang, strtotime( '+30 days' ));

// SETTING THE TRANSLATOR
$tr = new GoogleTranslate($lang);
$tr->setSource('fr');
$tr->setTarget($lang);

$trAuto = new GoogleTranslate();
$trAuto->setSource();
$trAuto->setTarget($lang);