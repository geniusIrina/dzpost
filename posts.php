<?php
$message = $_GET["message"] ?? null;
$error = $_GET["error"] ?? null;
$actionWord = "Добавить";
$action = "create";
$title = '';
$content = '';
$id = null;

$posts = json_decode(file_get_contents('blog.json'), true);

if (isset($_GET['action']) && $_GET['action'] == 'update') {
    $id = $_GET['id'] ?? '';

    if (array_key_exists($id, $posts)) {
        $title = $posts[$id]['title'];
        $content = $posts[$id]['content'];
        $actionWord = "Изменить";
        $action = "save";
    } else {
        header('Location: posts.php?error=Нет такого поста');
        exit;
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'save') {
    $id = $_POST['id'] ?? '';

    if (empty($_POST['title']) || empty($_POST['content'])) {
        header('Location: posts.php?error=Заполните все поля');
        exit;
    }

    $errors = [];

    if (empty(trim($_POST['title']))) {
        $errors[] = "Заголовок не может быть пустым";
    } elseif (mb_strlen(trim($_POST['title'])) < 3) {
        $errors[] = "Заголовок должен быть не менее 3 символов";
    }

    if (empty(trim($_POST['content']))) {
        $errors[] = "Текст поста не может быть пустым";
    } elseif (mb_strlen(trim($_POST['content'])) < 5) {
        $errors[] = "Текст поста должен содержать не менее 5 символов";
    }

    if (!empty($errors)) {
        $error_message = implode("; ", $errors);
        header('Location: posts.php?error=' . urlencode($error_message));
        exit;
    }

    $title = htmlspecialchars($_POST['title']);
    $content = htmlspecialchars($_POST['content']);

    if (array_key_exists($id, $posts)) {
        $posts[$id] = [
            'title' => $title,
            'content' => $content
        ];
    }

    file_put_contents('blog.json', json_encode($posts, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    header('Location: posts.php?message=Пост изменен');
    exit;
}

if (isset($_GET['action']) && $_GET['action'] == 'create') {
    
    $errors = [];

    if (empty(trim($_POST['title']))) {
        $errors[] = "Заголовок не может быть пустым";
    } elseif (mb_strlen(trim($_POST['title'])) < 3) {
        $errors[] = "Заголовок должен быть не менее 3 символов";
    }

    if (empty(trim($_POST['content']))) {
        $errors[] = "Текст поста не может быть пустым";
    } elseif (mb_strlen(trim($_POST['content'])) < 5) {
        $errors[] = "Текст поста должен содержать не менее 5 символов";
    }

    if (!empty($errors)) {
        $error_message = implode("; ", $errors);
        header('Location: posts.php?error=' . urlencode($error_message));
        exit;
    }

    if (!empty($_FILES)) {
        $imageinfo = getimagesize($_FILES['image']['tmp_name']);

        if (!$imageinfo) {
            header("Location: posts.php?error=" . urlencode("Загружайте только изображения"));
            exit;
        }

        if ($imageinfo['mime'] != 'image/png'
            && $imageinfo['mime'] != 'image/gif'
            && $imageinfo['mime'] != 'image/jpeg'
            && $imageinfo['mime'] != 'image/webp') {
            echo "Можно загружать только jpg, png, gif, webp файлы.";
            exit;
        }

        if ($_FILES["image"]["size"] > 1024 * 5 * 1024) {
            echo("Размер файла не больше 5 Мб");
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
            array_unshift($posts, [
                'title' => htmlspecialchars(trim($_POST['title'])),
                'content' => htmlspecialchars(trim($_POST['content'])),
                'image' => $_FILES['image']['name'],
                'likes' => 0
            ]);
            $message = "Файл загружен";
        } else {
            echo "Sorry, there was an error uploading your file.";
            die();
        }
    } else {
        array_unshift($posts, [
            'title' => htmlspecialchars(trim($_POST['title'])),
            'content' => htmlspecialchars(trim($_POST['content'])),
            'likes' => 0
        ]);
    }

    file_put_contents('blog.json', json_encode($posts, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    header('Location: posts.php?message=Пост добавлен');
    exit;
}

if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $id = $_GET['id'] ?? '';

    if (array_key_exists($id, $posts)) {
        unset($posts[$id]);
    }

    file_put_contents('blog.json', json_encode($posts, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    header('Location: posts.php?message=Пост удален');
    exit;
}
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
    <div style="color: green">
        <b><?= htmlspecialchars($message) ?></b>
    </div>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div style="color: red">
        <b><?= htmlspecialchars($error) ?></b>
    </div>
<?php endif; ?>

<div><?=$actionWord?> пост</div>
<form action="?action=<?=$action?>" method="post"  enctype="multipart/form-data">
    <input type="text" name="title" value="<?=$title?>" placeholder="Введите название поста"><br>
    <input type="text" hidden name="id" value="<?=$id?>">
    <textarea name="content" cols="30" rows="3"><?=$content?></textarea><br>
    <input type="file" name="image">
    <input type="submit" value="<?=$actionWord?>">
</form>

<?php if (!empty($posts)): ?>
    <?php foreach ($posts as $id => $post): ?>

        <div>
            <h2><a href="post.php?id=<?= $id ?>"><?= $post['title'] ?></a>
                <a href="posts.php?action=update&id=<?= $id ?>">[edit]</a>
                <a href="posts.php?action=delete&id=<?= $id ?>">[X]</a>
            </h2>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    Нет постов.
<?php endif;?>
</body>
</html>
