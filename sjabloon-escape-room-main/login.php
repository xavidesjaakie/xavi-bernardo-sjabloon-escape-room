<?php
session_start();
require 'dbcon.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $naam = trim($_POST['naam']);
    $wachtwoord = $_POST['wachtwoord'];

    if (empty($naam) || empty($wachtwoord)) {
        $error = "Vul zowel naam als wachtwoord in.";
    } else {
        // Zoek gebruiker op naam
        $stmt = $db_connection->prepare("SELECT * FROM profile WHERE naam = ?");
        $stmt->execute([$naam]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Vergelijk wachtwoorden letterlijk (NIET veilig, maar simpel)
            if ($wachtwoord === $user['wachtwoord']) {
                $_SESSION['user'] = $user['naam'];

                // Zet admin flag als username 'xavikaas' is
                if ($user['naam'] === 'xavikaas') {
                    $_SESSION['is_admin'] = true;
                } else {
                    $_SESSION['is_admin'] = false;
                }

                header("Location: index.php");
                exit;
            } else {
                $error = "Wachtwoord is onjuist.";
            }
        } else {
            $error = "Gebruiker niet gevonden.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8" />
  <title>Inloggen</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      max-width: 400px;
      margin: 50px auto;
      padding: 20px;
      background-color: #f4f4f4;
      border-radius: 10px;
    }
    input[type=text], input[type=password] {
      width: 100%;
      padding: 10px;
      margin: 8px 0 20px 0;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    button {
      width: 100%;
      padding: 12px;
      background-color: #0c6efc;
      color: white;
      border: none;
      border-radius: 5px;
      font-size: 1rem;
      cursor: pointer;
    }
    button:hover {
      background-color: #094fc0;
    }
    .error {
      color: red;
      margin-bottom: 15px;
    }
    .register-link {
      margin-top: 15px;
      text-align: center;
    }
  </style>
</head>
<body>

  <h2>Inloggen</h2>

  <?php if (isset($error)): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="post" action="">
    <label>Naam:</label>
    <input type="text" name="naam" required>

    <label>Wachtwoord:</label>
    <input type="password" name="wachtwoord" required>

    <button type="submit">Inloggen</button>
  </form>

  <div class="register-link">
    Nog geen account? <a href="register.php">Registreer hier</a>
  </div>

</body>
</html>
