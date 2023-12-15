<?php

if (isset($_POST["image"])) {
    $data = $_POST["image"];
    $image_array_1 = explode(";", $data);
    $image_array_2 = explode(",", $image_array_1[1]);
    $data = base64_decode($image_array_2[1]);
    $randomNumber = mt_rand(10000000, 99999999);
    $imageName = "assets/images/" . $randomNumber . '.png'; // Relative path for web display

    // Adjusted absolute directory path
    $directory = $_SERVER['DOCUMENT_ROOT'] . "/web-tooling/finalproject/assets/images/" . $randomNumber . '.png';

    // Ensure the directory exists
    $imageDirectory = $_SERVER['DOCUMENT_ROOT'] . "/web-tooling/finalproject/assets/images/";
    if (!is_dir($imageDirectory)) {
        mkdir($imageDirectory, 0777, true);
    }

    file_put_contents($directory, $data); // Write data to file
    echo '<input type="hidden" value="' . $randomNumber . '" id="eqImg"/>';
    echo '<img src="' . $imageName . '" class="img-thumbnail" value"' . $randomNumber . '" width="250" height="250" />';
}
?>