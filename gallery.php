<?php
$message = $_GET["message"] ?? null;

require __DIR__ . '/SimpleImage.php';

$messages = [
        'ok' => 'Файл загружен',
];

if (!empty($_POST) && !empty($_FILES)) {
    $imageinfo = getimagesize($_FILES['image']['tmp_name']);

    if (!$imageinfo) {
        header("Location: gallery.php?message=ok");
        exit;
    }

    if ($imageinfo['mime'] != 'image/png'
        && $imageinfo['mime'] != 'image/gif'
        && $imageinfo['mime'] != 'image/jpeg'
        && $imageinfo['mime'] != 'image/webp') {
        echo "Можно загружать только jpg-файлы, неверное содержание файла, не изображение.";
        exit;
    }

    if ($_FILES["image"]["size"] > 1024 * 5 * 1024) {
        echo("Размер файла не больше 5 мб");
        exit;
    }

    $blacklist = [".php", ".phtml", ".php3", ".php4"];
    foreach ($blacklist as $item) {
        if (preg_match("/$item\$/i", $_FILES['image']['name'])) {
            echo "Загрузка php-файлов запрещена!";
            exit;
        }
    }


    if (move_uploaded_file($_FILES['image']['tmp_name'], "images/big/" . $_FILES['image']['name'])) {

        $image = new \claviska\SimpleImage();
        $image
            ->fromFile('images/big/' . $_FILES['image']['name'])
            ->autoOrient()
            ->resize(150, 100)
            ->toFile('images/small/' . $_FILES['image']['name'], 'image/jpeg') ;


        $message = "Файл загружен";
    } else {
        echo "Sorry, there was an error uploading your file.";
        die();
    }

}

$images = array_slice(scandir(__DIR__ . '/images/big'), 2);

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<body>
<?php include __DIR__ . "/menu.php" ?>

<?php if (isset($message)): ?>
    <div>
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<?php foreach ($images as $image): ?>
    <a href="images/big/<?= $image; ?>">
        <img src="images/small/<?= $image; ?>" alt="" width="150">
    </a>

<?php endforeach; ?>
<form action="" enctype="multipart/form-data" method="post">
    <input type="file" name="image">
    <input type="submit" value="Загрузить" name="load">
</form>
</body>
</html>
