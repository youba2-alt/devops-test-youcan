<?php

if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    http_response_code(404);
    error_log("Invalid request method");
    exit;
}

if( empty($_GET['store_id']) || empty($_GET['s3_image_id']) || empty($_GET['image_desired_path']) ){
    http_response_code(400);
    error_log("Missing parameters");
    exit();
}


$mounted_stores_dir = "/stores/";
$images_temp_dir = "/tmp/stores/";
$store_id =   $_GET['store_id']; // store-1
$s3_image_id = $_GET['s3_image_id']; // store-1/image.jpg
$image_desired_path = $_GET['image_desired_path']; // mounted-stores-dir => XY/Z/store-1/categorie/
$sizes = [ [1920,1080], [1280,720], [640, 360] ]; 
$sizes_suffixes = ["lg", "md", "sm"];
$imageUrl = "https://mybucket-youcan-test.s3.amazonaws.com/stores/".$store_id."/".$s3_image_id;

$image_data = file_get_contents($imageUrl);

if( $image_data === false ) {
    http_response_code(404);
    error_log("Failed to get image from: ".$imageUrl);
    exit();
}


$img_destination = $images_temp_dir.strval(intval(time()+rand(), 4)).$s3_image_id;


if( file_put_contents( $img_destination, $image_data)===false ){
    http_response_code(500);
    error_log("Failed to save image to: ".$img_destination);
    exit();
}

$imageType = exif_imagetype($img_destination);

if ($imageType === false) {
    error_log("Failed to detect the image type");
    http_response_code(400);
    exit();
}

switch ($imageType) {
    case IMAGETYPE_JPEG:
        $srcImage = imagecreatefromjpeg($img_destination);
        break;
    case IMAGETYPE_PNG:
        $srcImage = imagecreatefrompng($img_destination);
        break;
    case IMAGETYPE_BMP:
        $srcImage = imagecreatefrombmp($img_destination);
        break;
    // Add more cases for other image types as needed
    default:
        error_log("unsupported image type! sw");
        http_response_code(400);
        exit();
}
unlink($img_destination); # delete temp image
$metadata = explode(".", $s3_image_id);


if (!is_dir($mounted_stores_dir.$image_desired_path)) {
    if (!mkdir($mounted_stores_dir.$image_desired_path, 0777, true)) {
        http_response_code(500);
        error_log(("Failed to create directory ".$mounted_stores_dir.$image_desired_path));
        exit();
    }
}



foreach( $sizes as $k=>$size ){
    error_log("resizing to: ");

    $resizedImage = imagecreatetruecolor($size[0], $size[1]);
    imagecopyresized($resizedImage, $srcImage, 0, 0, 0, 0, $size[0], $size[1], imagesx($srcImage), imagesy($srcImage));
    $outputFile = $mounted_stores_dir.$image_desired_path.$metadata[0]."-".$sizes_suffixes[$k].".".$metadata[1];
    switch ($imageType) {
        case IMAGETYPE_JPEG:
            imagejpeg($resizedImage, $outputFile, 100); // Save the resized image as JPEG
            error_log("saved to: ".$outputFile );
            break;
        case IMAGETYPE_PNG:
            imagepng($resizedImage, $outputFile, 9); // Save the resized image as PNG
            break;
        case IMAGETYPE_BMP:
            imagebmp($resizedImage, $outputFile); // Save the resized image as GIF
            break;
    }
}

imagedestroy($srcImage);
imagedestroy($resizedImage);

http_response_code(200);
return 1;

?>