<?php
// Start sessie alleen als deze nog niet gestart is
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Databaseverbinding
require 'dbcon.php';

/**
 * Start een nieuwe game:
 * - Zet starttijd
 * - Reset voltooide kamers
 */
function startGame() {
    $_SESSION['start_time'] = time();
    $_SESSION['completed_rooms'] = [];
}

/**
 * Controleer of de tijd op is (60s), tenzij je in kamer 3 bent.
 * Zo ja, stuur naar gameover.php
 */
function checkTime($currentRoomId) {
    if (!isset($_SESSION['start_time'])) {
        header("Location: gameover.php");
        exit;
    }

    $elapsed = time() - $_SESSION['start_time'];

    if ($elapsed > 60 && $currentRoomId < 3) {
        header("Location: gameover.php");
        exit;
    }
}

/**
 * Sla een kamer op als voltooid (voeg toe aan session array)
 */
function completeRoom($roomId) {
    if (!in_array($roomId, $_SESSION['completed_rooms'])) {
        $_SESSION['completed_rooms'][] = $roomId;
    }
}

/**
 * Beoordeel of je gewonnen hebt (kamer 3 gehaald),
 * zo ja: score opslaan en naar winner.php
 * zo nee: naar gameover.php
 */
function evaluateGame() {
    $completed = $_SESSION['completed_rooms'] ?? [];

    if (in_array(3, $completed)) {
        // Resterende tijd berekenen
        $elapsed = time() - ($_SESSION['start_time'] ?? time());
        $timeLeft = max(0, 60 - $elapsed);

        // Score opslaan
        if (isset($_SESSION['user_id'])) {
            saveScore($_SESSION['user_id'], $timeLeft);
        }

        header("Location: winner.php");
    } else {
        header("Location: gameover.php");
    }
    exit;
}

/**
 * Sla de score op in de `leaderboard`-tabel
 * @param int $userId Gebruiker-ID uit session
 * @param int $timeLeft Tijd over in seconden
 */

function saveScore($naam, $tijd_over) {
    require 'dbcon.php';

    // Eerst de huidige beste score ophalen voor deze gebruiker
    $stmt = $db_connection->prepare("SELECT tijd_over FROM leaderboard WHERE naam = ? ORDER BY tijd_over DESC LIMIT 1");
    $stmt->execute([$naam]);
    $besteScore = $stmt->fetchColumn();

    // Alleen opslaan als deze score beter is (hoger) dan de huidige beste, of als er nog geen score is
    if ($besteScore === false || $tijd_over > $besteScore) {
        $datum = date('Y-m-d H:i:s');
        $insert = $db_connection->prepare("INSERT INTO leaderboard (naam, tijd_over, datum) VALUES (?, ?, ?)");
        if (!$insert->execute([$naam, $tijd_over, $datum])) {
            $error = $insert->errorInfo();
            error_log("Fout bij opslaan score: " . $error[2]);
        }
    }
}
?>


