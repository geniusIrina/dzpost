<?php
include 'calculator_functions.php';

$arg1 = 0;
$arg2 = 0;
$result = "";

if (!empty($_GET)) {
    $arg1 = (int)$_GET['arg1'];
    $arg2 = (int)$_GET['arg2'];
    $operation = $_GET['operation'];
    
    $result = calculate($operation, $arg1, $arg2);
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Калькулятор</title>
</head>
<body>
<?php include __DIR__ . "/menu.php"?>
Калькулятор<br>
<form action="" method="get">
    <input type="text" name="arg1" value="<?=$arg1?>">
    <input type="submit" name="operation" value="+">
    <input type="submit" name="operation" value="-">
    <input type="submit" name="operation" value="*">
    <input type="submit" name="operation" value="/">
    <input type="text" name="arg2" value="<?=$arg2?>">
    =
    <input type="text" name="result" readonly value="<?=$result?>">
</form>
</body>
</html>