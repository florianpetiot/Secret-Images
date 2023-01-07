<?php

require 'global.php';

$mdp = $_POST['input1'];
$mdp = hash("sha256", $mdp);
$filename = $_FILES['file']['name'];

$location = "temp/".$filename;

move_uploaded_file($_FILES['file']['tmp_name'], $location);

$img = imagecreatefrompng($location);

$width = imagesx($img);

$mdpImg = "";

// recuperer les 2 bits directement apres la signature
$bit = 21;

$pixel = imagecolorat($img, $bit % $width, (int)($bit / $width));
$green = ($pixel >> 8) & 0xFF;
$blue = $pixel & 0xFF;

$mdpImg .= $green % 2;
$mdpImg .= $blue % 2;

while (true) {
    $bit += 1;

    $pixel = imagecolorat($img, $bit % $width, (int)($bit / $width));
    $red = ($pixel >> 16) & 0xFF;
    $green = ($pixel >> 8) & 0xFF;
    $blue = $pixel & 0xFF;

    $mdpImg .= $red % 2;
    $mdpImg .= $green % 2;
    $mdpImg .= $blue % 2;

    if (strlen($mdpImg) >= 8 &&
    strpos(binToStr(substr($mdpImg, 0, strlen($mdpImg) - (strlen($mdpImg) % 8))), '<mp>') !== false) {
        break;
    }

}

$longueur = substr($mdpImg, strlen($mdpImg) - (strlen($mdpImg) % 8));


$mdpImg = binToStr(substr($mdpImg, 0, strlen($mdpImg) - (strlen($mdpImg) % 8)));
// enlever le <mp> a la fin
$mdpImg = intval(substr($mdpImg, 0, -4));

if ($mdpImg != $mdp) {
    echo "err_mdp";
    exit();
}


// recuperer la longueur du texte

while (true) {
    $bit += 1;

    $pixel = imagecolorat($img, $bit % $width, (int)($bit / $width));
    $red = ($pixel >> 16) & 0xFF;
    $green = ($pixel >> 8) & 0xFF;
    $blue = $pixel & 0xFF;

    $longueur .= $red % 2;
    $longueur .= $green % 2;
    $longueur .= $blue % 2;

    if (strlen($longueur) >= 8 &&
    strpos(binToStr(substr($longueur, 0, strlen($longueur) - (strlen($longueur) % 8))), '<0b>') !== false) {
        break;
    }

}

$texte = substr($longueur, strlen($longueur) - (strlen($longueur) % 8));

$longueur = binToStr(substr($longueur, 0, strlen($longueur) - (strlen($longueur) % 8)));
// enlever le <0b> a la fin
$longueur = intval(substr($longueur, 0, -4));

// recuperer le texte

for ($bit2 = $bit+1; $bit2 < $bit+1+($longueur/3); $bit2 = $bit2+1) {
    $pixel = imagecolorat($img, $bit2 % $width, (int)($bit2 / $width));
    $red = ($pixel >> 16) & 0xFF;
    $green = ($pixel >> 8) & 0xFF;
    $blue = $pixel & 0xFF;

    $texte .= $red % 2;
    $texte .= $green % 2;
    $texte .= $blue % 2;

}

echo binToStr(substr($texte, 0, strlen($texte) - (strlen($texte) % 8)));

unlink($location);


?>