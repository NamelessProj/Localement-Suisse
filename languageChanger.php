<?php
if(isset($_GET['lang']) && !empty($_GET['lang'])){
    $currentLanguage = trim(strtolower($_GET['lang'])); // GETTING THE LANGUAGE
    $lang = match($currentLanguage) {
        'fr' => 'fr',
        'it' => 'it',
        'de' => 'de',
        default => 'en',
    };

    setcookie('lang', $lang, strtotime( '+30 days' )); // SETTING THE LANGUAGE IN THE COOKIE OF THE USER
}

$previousPage = $_SERVER['HTTP_REFERER'] ?? 'index.php';
header('Location: '.$previousPage); // GOING TO THE PREVIOUS PAGE