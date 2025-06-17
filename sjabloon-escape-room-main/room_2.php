<?php
require_once('functions.php');
require_once('dbcon.php');

$roomId = 2;
checkTime($roomId);

if (!isset($_SESSION['start_time'])) {
    startGame();
}

completeRoom($roomId);

try {
    // Nu ook 1 vraag ophalen, net als room 1
    $stmt = $db_connection->query("SELECT * FROM questions WHERE roomId = $roomId LIMIT 1");
    $question = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Databasefout: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <title>Escape Room <?php echo $roomId; ?></title>
  <link rel="stylesheet" href="style.css">
</head>
<body data-starttime="<?php echo $_SESSION['start_time']; ?>">

  <div class="room-status">🧩 Je bent in Room <?php echo $roomId; ?></div>
  <div id="timer" class="timer"></div>

  <div class="question-box" id="questionBox"
    data-question="<?php echo htmlspecialchars($question['question']); ?>"
    data-answer="<?php echo htmlspecialchars($question['answer']); ?>"
    data-hint="<?php echo htmlspecialchars($question['hint']); ?>">
    ❓
  </div>
<style>
  body {
    background-image: url('images/gyarods.webp'); /* of .png, afhankelijk van het bestand */
    background-size: cover;       /* zorgt dat de afbeelding de hele achtergrond vult */
    background-position: center;  /* center de afbeelding */
    background-repeat: no-repeat; /* voorkomt herhaling */
    min-height: 100vh;            /* zorgt dat de body minstens schermhoogte is */
    margin: 0;                    /* verwijdert standaardmarges */
  }
</style>
  <!-- Modal -->
  <section class="overlay" id="overlay" onclick="closeModal()"></section>
  <section class="modal" id="modal">
    <h2>Escape Room Vraag</h2>
    <p id="question"></p>

    <input type="text" id="answer" placeholder="Typ je antwoord">
    <button onclick="checkAnswer(<?php echo $roomId; ?>)">Verzenden</button>
    <button id="hintButton" onclick="showHint()">Hint</button>

    <p id="feedback"></p>
    <p id="hintText" style="display: none; font-style: italic; color: #555;"></p>
  </section>

  <script src="app.js"></script>
</body>
</html>
