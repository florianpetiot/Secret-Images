<?php
 
require 'global.php';


//  get the name of the uploaded file
$filename = $_FILES['file']['name'];

// choose a location to save the file
$location = "temp/".$filename;

// save the file on the server
move_uploaded_file($_FILES['file']['tmp_name'], $location);

// open the image
$img = imagecreatefrompng($location);

// get the dimensions of the image
$width = imagesx($img);

// verifier la précense d'une signature
$signature = "";


for ($bit = 0; $bit < 64/3; $bit += 1) {
    $pixel = imagecolorat($img, $bit % $width, (int)($bit / $width));
    $red = ($pixel >> 16) & 0xFF;
    $green = ($pixel >> 8) & 0xFF;
    $blue = $pixel & 0xFF;

    $signature .= $red % 2;
    $signature .= $green % 2;
    $signature .= $blue % 2;
}


// rogner le texte modulo 8

if (binToStr(substr($signature, 0, strlen($signature) - (strlen($signature) % 8))) == '<0b><mp>') {
    unlink($location);
    echo "mdp";
    exit();
}

if (binToStr(substr($signature, 0, strlen($signature) - (strlen($signature) % 8))) != '<0b>') {
    unlink($location);
    echo "err_signature";
    exit();
}


// RECUPERER LA LONGUEUR DU TEXTE
$longueur = substr($signature, -(strlen($signature) % 8));
$bit = 33/3;

while (true) {
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
    $bit += 1;
}

$bit += 1;

# garder les bits qui ne serait pas passés dans le test ligne 54 (a cause du modulo 8)
$texte = substr($longueur, strlen($longueur) - (strlen($longueur) % 8));


// recuperer la longueur du texte en int
$longueur = binToStr(substr($longueur, 0, strlen($longueur) - (strlen($longueur) % 8)));
// rogner de 4 a droite pour enlever le <0b>
$longueur = intval(substr($longueur, 0, -4));


for ($bit2 = $bit; $bit2 < $bit+($longueur/3); $bit2 = $bit2+1) {
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
