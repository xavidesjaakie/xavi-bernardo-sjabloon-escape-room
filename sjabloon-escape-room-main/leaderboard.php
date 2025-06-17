<?php
require 'dbcon.php';

$stmt = $db_connection->query("SELECT naam, tijd_over FROM leaderboard ORDER BY tijd_over DESC LIMIT 10");
$leaders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8" />
    <title>Leaderboard</title>
</head>
<body>
<h2>🏆 Leaderboard</h2>
<table border="1" cellpadding="8" cellspacing="0">
    <thead>
        <tr>
            <th>Naam</th>
            <th>Tijd over (s)</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($leaders as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row['naam']) ?></td>
            <td><?= number_format($row['tijd_over'], 3) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<a href="index.php">Terug naar home</a>
</body>
</html>
