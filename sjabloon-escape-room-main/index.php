<?php
session_start();
require 'dbcon.php';

// Uitloggen als uitlogformulier is verzonden
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}

// Controleer of gebruiker is ingelogd
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Haal top 5 scores op uit leaderboard
$stmt = $db_connection->prepare("
    SELECT naam, tijd_over, datum 
    FROM leaderboard 
    ORDER BY tijd_over DESC 
    LIMIT 5
");
$stmt->execute();
$topScores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Escape Room Home</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body {
      background-image: url('images/home.webp');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      min-height: 100vh;
      margin: 0;
      font-family: Arial, sans-serif;
      color: white;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding-top: 60px;
    }

    h1 {
      font-size: 2.5rem;
      margin-bottom: 10px;
    }

    p {
      font-size: 1.2rem;
      margin-bottom: 30px;
    }

    form, .logout {
      margin: 10px;
    }

    button {
      padding: 12px 20px;
      font-size: 1rem;
      background-color: #0c6efc;
      border: none;
      border-radius: 5px;
      color: white;
      cursor: pointer;
    }

    button:hover {
      background-color: #094fc0;
    }

    .welcome {
      position: absolute;
      top: 20px;
      right: 20px;
      background-color: rgba(0,0,0,0.6);
      padding: 10px 15px;
      border-radius: 10px;
    }

    .leaderboard {
      margin-top: 40px;
      background-color: rgba(0,0,0,0.6);
      padding: 20px;
      border-radius: 10px;
      width: 90%;
      max-width: 600px;
    }

    .leaderboard table {
      width: 100%;
      color: white;
      border-collapse: collapse;
    }

    .leaderboard th, .leaderboard td {
      padding: 10px;
      border-bottom: 1px solid white;
      text-align: left;
    }
  </style>
</head>
<body>

  <div class="welcome">
    👋 Welkom, <strong><?= htmlspecialchars($_SESSION['user']) ?></strong>!
  </div>

  <h1>Welkom bij de Escape Room</h1>
  <p>Probeer minimaal 2 kamers op te lossen in 60 seconden!</p>

  <form action="game.php" method="post">
    <button type="submit">Start de Escape Room</button>
  </form>

  <!-- Uitlogformulier -->
  <form method="post" class="logout">
    <button type="submit" name="logout">Uitloggen</button>
  </form>
<?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
    <a href="admin.php">Admin Panel</a>
<?php endif; ?>

  <div class="leaderboard">
    <h2>🏆 Leaderboard – Beste Tijden</h2>
    <table>
      <thead>
        <tr>
          <th>Naam</th>
          <th>Tijd over</th>
          <th>Datum</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($topScores)): ?>
          <?php foreach ($topScores as $row): ?>
            <tr>
              <td><?= htmlspecialchars($row['naam']) ?></td>
          <td><?= number_format($row['tijd_over'], 3) ?>s</td>

              <td><?= date("d-m-Y H:i", strtotime($row['datum'])) ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="3">Nog geen scores beschikbaar.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

</body>
</html>
