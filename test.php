<?php
require_once './db.php';

if(isset($_POST['send']) && isset($_FILES)) {
    $allowed_img_extension = array("png", "jpg", "jpeg");
    $fileData = pathinfo($_FILES['firstImag']['name']);
    $type = strtolower($fileData['extension']);
    if (!in_array($type, $allowed_img_extension)) {
        die("L'extension d'une ou plusieurs image(s) n'est pas valide.");
    }
    $finalImageWidth = 2000;
    $handle = fopen($_FILES['firstImag']['tmp_name'], "r");
    $image = new Imagick();
    $image->readImageFile($handle);
    $imgWidth = $image->getImageWidth();
    $image->setImageFormat("jpg");
    if($imgWidth < $finalImageWidth) $finalImageWidth = $imgWidth;
    $image->scaleImage($finalImageWidth, 0, false, false);
    file_put_contents("./imgs/products/".uniqid().".jpg", $image);
    $image->destroy();
    echo $imgWidth;
}

if(isset($_POST['send2'])){
    echo count($_POST['cat']);
    var_dump($_POST['cat']);
    echo '<br>';
    foreach ($_POST['cat'] as $cat) {
        echo $cat;
    }
}

$image = new Imagick();
$image->newImage(1, 1, new ImagickPixel('#ffffff'));
$image->setImageFormat('png');
$pngData = $image->getImagesBlob();
echo strpos($pngData, "\x89PNG\r\n\x1a\n") === 0 ? 'Ok' : 'Failed';


function floattostr($val){
    preg_match("#^([\+\-]|)([0-9]*)(\.([0-9]*?)|)(0*)$#", trim($val), $o);
    return $o[1].sprintf('%d', $o[2]).($o[3]!='.'?$o[3]:'');
}

$price = floattostr('00500.88');

if(!preg_match('/^[0-9]+.[0-9]{0,2}$/', $price)) die("Les informations sont incorrect.");

echo '<br>';
if(str_contains($price, '.')){
    $ex = explode('.', $price);
    $ex = array_reverse($ex);
    $totalCharacterAfterComma = strlen(trim($ex[0])); // GETTING THE NUMBER OF NUMBERS AFTER THE DOT
    if($totalCharacterAfterComma < 2){
        for($i = 0; $i < $totalCharacterAfterComma; $i++){
            $price .= '0';
        }
    }
    echo 'ok '.$price;
}else{
    $price.='00';
    echo $price;
}

?>

<form method="post" enctype="multipart/form-data">
    <input type="file" name="firstImag" id="firstImag" accept="image/*">
    <button type="submit" name="send">Send</button>
</form>

<form method="post">
    <h4>Catégories</h4>
    <?php
    $sql = "SELECT * FROM categories";
    $req = $db->query($sql);
    $categories = $req->fetchAll();

    foreach($categories as $category):
        ?>
        <fieldset>
            <legend><?= ucfirst(strip_tags($category->cat_name)) ?></legend>
            <?php
            $sql = "SELECT subcat_name, subcat_id FROM categories LEFT JOIN subcategories ON subcategories.cat_id = categories.cat_id WHERE subcategories.cat_id = :idCat";
            $req = $db->prepare($sql);
            $req->bindValue(":idCat", $category->cat_id);
            $req->execute();
            $subcategories = $req->fetchAll();
            foreach($subcategories as $subcategory):
                ?>
                <label for="cat_<?= strip_tags($subcategory->subcat_id) ?>" class="label_cont" title="<?= strip_tags($subcategory->subcat_name) ?>">
                    <span><?= ucfirst(strip_tags($subcategory->subcat_name)) ?></span>
                    <input type="checkbox" name="cat[]" id="cat_<?= strip_tags($subcategory->subcat_id) ?>" value="<?= strip_tags($subcategory->subcat_id) ?>">
                    <span class="checkmark"></span>
                </label>
            <?php endforeach; ?>
        </fieldset>
    <?php endforeach; ?>
    <button type="submit" name="send2">Send</button>
</form>