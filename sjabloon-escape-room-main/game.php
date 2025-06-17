
<?php
session_start();

// Start timer als die nog niet bestaat
if (!isset($_SESSION['start_time'])) {
    $_SESSION['start_time'] = microtime(true);
}

// Hier komt je game-logica verder...
require_once('functions.php');
startGame();
header("Location: room_1.php");
exit;
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8" />
    <title>Escape Room Spel</title>
</head>
<body>
    <h1>Escape Room</h1>
    <p>Speel het spel...</p>
</body>
</html>
