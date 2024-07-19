<?php
function resizeImage($fileName, $file, $directory, $maxWidth = 150){
    $finalImageWidth = 1920;

    $handle = fopen($file, "r");
    $image = new Imagick();
    $image->readImageFile($handle);
    $imgWidth = $image->getImageWidth();
    $image->setImageFormat("jpg");
    if($imgWidth < $maxWidth) $finalImageWidth = $imgWidth;
    $image->scaleImage($finalImageWidth, 0, false, false);
    $finalPath = __DIR__."/imgs/".$directory."/".$fileName.".jpg";

    if(!file_put_contents($finalPath, $image)){
        return false;
    }

    $image->destroy();
    chmod($finalPath, 0644);
    return true;
}

function uploadImage($file, $directory, $multiple = -1, $maxSize = 4_000_000){
    $returnArray = [
        "error" => false,
        "name" => ""
    ];

    $allowedExts = [
        "jpeg" => "image/jpeg",
        "jpg" => "image/jpeg",
        "png" => "image/png"
    ];

    if($multiple === -1){
        $fileName = $file["name"];
        $fileType = $file["type"];
        $fileSize = $file["size"];
        $fileTmp = $file['tmp_name'];
        $fileError = $file["error"];
    }else{
        $fileName = $file["name"][$multiple];
        $fileType = $file["type"][$multiple];
        $fileSize = $file["size"][$multiple];
        $fileTmp = $file['tmp_name'][$multiple];
        $fileError = $file["error"][$multiple];
    }
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    // CHECK IF EXTENSION IS AUTHORIZED
    if(!array_key_exists($fileExtension, $allowedExts) || !in_array($fileType, $allowedExts)){
        $returnArray["error"] = true;
    }

    // CHECK IF THERE'S ERRORS
    if($fileError !== 0){
        $returnArray["error"] = true;
    }

    //  CHECK IF THE FILE IS TOO BIG OR TOO SMALL / EMPTY
    if($fileSize > $maxSize || $fileSize < 1){
        $returnArray["error"] = true;
    }

    if(!$returnArray["error"]){ // IF THERE'S NO PROBLEM, WE UPLOAD THE FILE
        $newName = md5(uniqid()); // GENERATE UNIQ NAME FOR IMG
        $returnArray["name"] = $newName;

        // UPLOADING THE NORMAL FILE
        if(!resizeImage($newName, $fileTmp, $directory, 1920)){
            $returnArray["error"] = true;
        }

        // UPLOADING THE MINI FILE
        if(!resizeImage("mini_".$newName, $fileTmp, $directory)){
            $returnArray["error"] = true;
        }
    }

    return $returnArray;
}