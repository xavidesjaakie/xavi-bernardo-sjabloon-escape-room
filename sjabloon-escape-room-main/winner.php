<?php
session_start();
require 'functions.php';

$timeLeft = null;

if (isset($_SESSION['user']) && isset($_SESSION['start_time'])) {
    $elapsed = microtime(true) - $_SESSION['start_time'];
    $timeLeft = max(0, 60 - $elapsed);

    saveScore($_SESSION['user'], $timeLeft);
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8" />
  <title>Gewonnen</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      text-align: center;
      background-image: url('images/win-page.webp');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      min-height: 100vh;
      margin: 0;
      padding: 0;
      color: white;
    }
    h1 {
      margin-top: 100px;
      font-size: 2.5rem;
    }
    .time-left {
      font-size: 1.5rem;
      margin-top: 20px;
      font-weight: bold;
    }
    a.button {
      display: inline-block;
      margin-top: 30px;
      padding: 12px 24px;
      background-color: #0c6efc;
      color: white;
      text-decoration: none;
      border-radius: 6px;
      font-size: 1.2rem;
    }
    a.button:hover {
      background-color: #094fc0;
    }
  </style>
</head>
<body>

<h1>🎉 Gefeliciteerd, <?= htmlspecialchars($_SESSION['user']) ?>! Je hebt gewonnen!</h1>

<?php if ($timeLeft !== null): ?>
    <div class="time-left">
        Je eindtijd is: <?= number_format($timeLeft, 3) ?> seconden over
    </div>
<?php endif; ?>

<a class="button" href="reset.php">Opnieuw spelen</a>

</body>
</html>
