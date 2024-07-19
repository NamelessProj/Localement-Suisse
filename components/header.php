<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Bienvenue sur le site de Localement Suisse.">
    <meta property="og:title" content="<?= $title?? 'Localement Suisse'; ?>">
    <meta property="og:image" content="./imgs/logo.jpeg">
    <meta property="og:description" content="Bienvenue sur le site de Localement Suisse.">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $title?? 'Localement Suisse'; ?></title>
    <link rel="shortcut icon" href="./imgs/logo.jpeg" type="image/x-icon">
    <?php if(isset($splide)) echo '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css">'; ?>
    <link rel="stylesheet" href="./css/style.css">
    <?php if(isset($construction)) echo '<link rel="stylesheet" href="./css/construction.css">'; ?>
</head>
<body>