<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photoUpload'])) {
    $targetDir = "uploads/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    $targetFile = $targetDir . basename($_FILES["photoUpload"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    
    // Verificar si es una imagen real
    $check = getimagesize($_FILES["photoUpload"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "El archivo no es una imagen.";
        $uploadOk = 0;
    }
    
    // Verificar tamaño (max 5MB)
    if ($_FILES["photoUpload"]["size"] > 5000000) {
        echo "Lo siento, tu archivo es demasiado grande.";
        $uploadOk = 0;
    }
    
    // Permitir ciertos formatos
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif") {
        echo "Solo se permiten archivos JPG, JPEG, PNG y GIF.";
        $uploadOk = 0;
    }
    
    // Subir el archivo si todo está bien
    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["photoUpload"]["tmp_name"], $targetFile)) {
            echo "La imagen ". htmlspecialchars(basename($_FILES["photoUpload"]["name"])). " se ha subido.";
        } else {
            echo "Hubo un error al subir tu archivo.";
        }
    }
}
?>
