<?php

require 'global.php';

$mdp = $_POST['input1'];
if (strlen($mdp) > 0) {
    $mdp = hash("sha256", $mdp);   
}
$texte = $_POST['input2'];
$filename = $_FILES['file']['name'];

$location = "temp/".$filename;

move_uploaded_file($_FILES['file']['tmp_name'], $location);

$img = imagecreatefrompng($location);

$width = imagesx($img);


$signature = "<0b>";
if (strlen($mdp) > 0) {
    $signature .= "<mp>".$mdp."<mp>";
}
$signature .= strval(strlen(strToBin($texte)))."<0b>";

$final_bin = strToBin($signature).strToBin($texte);


// verifier si l'image est assez grande pour contenir le texte
if (strlen($final_bin) > $width * $width) {
    echo "err_small";
    exit;
}


for ($bit = 0; $bit < strlen($final_bin)/3; $bit+=1) {
    $rgb = imagecolorat($img, $bit % $width, (int)($bit / $width));
    $red = ($rgb >> 16) & 0xFF;
    $green = ($rgb >> 8) & 0xFF;
    $blue = $rgb & 0xFF;

    $red = $red - ($red % 2) + substr($final_bin, $bit*3, 1);
    $green = $green - ($green % 2) + substr($final_bin, ($bit*3 + 1) % strlen($final_bin), 1);
    $blue = $blue - ($blue % 2) + substr($final_bin, ($bit*3 + 2) % strlen($final_bin), 1);

    $newcolor = imagecolorallocate($img, $red, $green, $blue);
    imagesetpixel($img, $bit % $width, (int)($bit / $width), $newcolor);
}


imagepng($img, $location);

echo $location


?>