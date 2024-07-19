<?php
session_start();

require_once 'vendor/autoload.php';
require_once 'components/languageManager.php';

// GETTING THE ID OF THE CURRENT CART OF THE USER
$cookieId = (!isset($_COOKIE['cartId']) || empty($_COOKIE['cartId'])) ? md5(uniqid()) : $_COOKIE['cartId'];

setcookie('cartId', $cookieId, strtotime( '+30 days' ));

$userId = (isset($_SESSION['user']['id']) && !empty($_SESSION['user']['id']) && is_numeric($_SESSION['user']['id'])) ? $_SESSION['user']['id'] : 0; // GETTING THE ID OF THE USER


// FUNCTION TO FORMAT NUMBERS
function priceFormatting($price, $numAfterComa = 2, $thousandSeparator = ' ', $decimalSeparator = '.'){
    $returnPrice = strip_tags($price);
    if(is_numeric($returnPrice)){
        while(strlen($returnPrice) < $numAfterComa + 1){
            $returnPrice = "0".$returnPrice;
        }
        return number_format(substr_replace($returnPrice, '.', $numAfterComa * -1, 0), $numAfterComa, $decimalSeparator, $thousandSeparator);
    }else return 0;
}