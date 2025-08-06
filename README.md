# Página de Amor para Sara

![Preview](preview.jpg)

Una página web romántica dedicada a Sara, con diseño en tonos rosas pastel y blanco, que permite subir fotos y expresar sentimientos.

## Características Principales

- 💌 Carta de amor personalizada
- 📸 Galería de fotos con función de subir imágenes
- ❤️ Lista de razones por las que amas a Sara
- ⏳ Contador de tiempo juntos
- 💝 Diseño romántico en rosa pastel y blanco
- 📱 Diseño responsive para todos los dispositivos

## Cómo Personalizar

1. **Carta de amor**: Edita el texto en `index.html` dentro de la sección `love-letter`
2. **Razones de amor**: Modifica la lista en la sección `reasons`
3. **Fecha de inicio**: Cambia la fecha en el script JavaScript (`startDate`)
4. **Colores**: Ajusta los tonos en `style.css`

## Funcionalidad de Subir Fotos

La página incluye:
- Frontend para seleccionar y previsualizar fotos
- Diseño del formulario de subida
- (Opcional) Para subir fotos al servidor, necesitarás:
  - Un servidor con PHP
  - El archivo `upload.php` (proporcionado abajo)

## Archivo upload.php (opcional)

```php
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