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

for ($bit = 0; $bit < 32; $bit=$bit+3) {
    $rgb = imagecolorat($img, $bit % $width, (int)($bit / $width));
    $red = ($rgb >> 16) & 0xFF;
    $green = ($rgb >> 8) & 0xFF;
    $blue = $rgb & 0xFF;

    $signature .= $red % 2;
    $signature .= $green % 2;
    $signature .= $blue % 2;
}


// RECUPERER LA LONGUEUR DU TEXTE
$longueur = substr($signature, -(strlen($signature) % 8));
$bit = 33;

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
    $bit += 3;
}



$total = strlen(substr($longueur, 0, strlen($longueur) - (strlen($longueur) % 8)));


// recuperer la longueur du texte en int
$longueur = binToStr(substr($longueur, 0, strlen($longueur) - (strlen($longueur) % 8)));
// on a "XXX<0b>"

// rogner de 4 a droite pour enlever le <0b>
$longueur = intval(substr($longueur, 0, -4));

$total += $longueur;

// brouiller les pixels
for ($bit=0; $bit < $total; $bit=$bit+3) {
    $pixel = imagecolorat($img, $bit % $width, (int)($bit / $width));
    $red = ($pixel >> 16) & 0xFF;
    $green = ($pixel >> 8) & 0xFF;
    $blue = $pixel & 0xFF;

    $red -= $red % 2;
    $green -= $green % 2;
    $blue -= $blue % 2;

    // changer la couleur du pixel
    $newcolor = imagecolorallocate($img, $red, $green, $blue);
    imagesetpixel($img, $bit % $width, (int)($bit / $width), $newcolor);
}


// enregistrer l'image
imagepng($img, $location);

echo $location;

?>
