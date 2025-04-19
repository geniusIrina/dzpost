<?php
// var_dump($_GET);
$result = null;
$message = '';

if (!empty($_GET)) 
{
    $massa = (int) $_GET['massa'];
    $rost = (int) $_GET['rost'];
    if ($rost == 0) {
        header('Location: /');
        exit;
    }
    $result = $massa / ($rost * $rost) * 10000;
    $result = round($result, 1, PHP_ROUND_HALF_UP);

    switch (true) {
        case $result < 18.5:
            $message = 'Недостаточный вес';
            break;
        case $result >= 18.5 && $result < 24.9:
            $message = 'Нормальный вес';
            break;
        case $result >= 25 && $result < 29.9:
            $message = 'Избыточный вес';
            break;
        case $result >= 30 && $result < 34.9:
            $message = 'Ожирение';
            break;
        default:
            $message = 'Значение вне диапазона';
            break;
    }
} else {
    $message = 'Пожалуйста, введите корректные значения';
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <title>Калькулятор ИМТ</title>
</head>
<body>
    <p>Введите ваш вес в кг и рост в см</p>
    <form action="">
        <input type="text" name="massa" placeholder="Масса в кг" value="">
        <input type="text" name="rost" placeholder="Рост в см" value="">
        <input type="submit" value="Рассчитать">
    </form>
    <?php if ($result !== null): ?>
        <p>Ваш ИМТ: <?= $result ?></p>
        <p>Результат: <?= $message ?></p>
    <?php endif; ?>
</body>
</html>