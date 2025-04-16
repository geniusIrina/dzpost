<?php
$posts = json_decode(file_get_contents('blog.json'), true);
$id = $_GET['id'] ?? '';

if (!array_key_exists($id, $posts)) {
    $message = "Нет такого поста";
} else {
    $post = $posts[$id];
}

if (isset($_GET['action']) && $_GET['action'] == 'addlike') {
    $posts[$id]['likes']++;

    file_put_contents('blog.json', json_encode($posts, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    $response = [
            'status' => 'ok',
            'likes' => $posts[$id]['likes'],
    ];

    echo json_encode($response);
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



<?php if (!empty($post)): ?>
    <div>
        <h2><?= $post['title'] ?></h2>
        <p>
            <?php if (isset($post['image'])):?>
                <img src="images/big/<?= $post['image']  ?>" alt="" width="200" style="float:left">
                <?php endif;?>
            <?= $post['content'] ?></p>
        <button onclick="addlike(<?=$id?>)">LIKES: <span id="<?=$id?>"><?=$post['likes']?></span></button>

    </div>
<?php else: ?>
    <div>
        <?= $message ?>
    </div>
<?php endif;?>
<script>
    function addlike(id) {
        (
            async () => {
                const response = await fetch('?action=addlike&id='+id);
                const answer = await response.json();
                document.getElementById(id).innerText = answer.likes;
            }
        )();
    }
</script>
</body>
</html>
