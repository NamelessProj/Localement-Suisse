<?php
session_start();
if(isset($_SESSION['user'])){
    unset($_SESSION['user']); // WE DELETE THE USER'S SESSION
}
$previousPage = $_SERVER['HTTP_REFERER'] ?? 'index.php';
header('Location: '.$previousPage); // GOING TO THE PREVIOUS PAGE