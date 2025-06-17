<?php
session_start();
require 'dbcon.php';

// Check admin
if (!isset($_SESSION['user']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php");
    exit;
}

$error = '';
$editMode = false;
$editQuestion = null;

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $db_connection->prepare("DELETE FROM questions WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: admin.php");
    exit;
}

// Handle Edit request (to show edit form)
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $db_connection->prepare("SELECT * FROM questions WHERE id = ?");
    $stmt->execute([$id]);
    $editQuestion = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($editQuestion) {
        $editMode = true;
    } else {
        $error = "Question not found.";
    }
}

// Handle form submission (add or update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question = trim($_POST['question'] ?? '');
    $answer = trim($_POST['answer'] ?? '');
    $hint = trim($_POST['hint'] ?? '');
    $roomId = (int)($_POST['roomId'] ?? 0);

    if ($question === '' || $answer === '' || $roomId <= 0) {
        $error = "Please fill in question, answer and a valid room ID.";
    } else {
        if (isset($_POST['id']) && $_POST['id'] !== '') {
            // Update existing question
            $id = (int)$_POST['id'];
            $update = $db_connection->prepare("UPDATE questions SET question = ?, answer = ?, hint = ?, roomId = ? WHERE id = ?");
            if ($update->execute([$question, $answer, $hint, $roomId, $id])) {
                header("Location: admin.php");
                exit;
            } else {
                $error = "Error updating the question.";
            }
        } else {
            // Insert new question
            $insert = $db_connection->prepare("INSERT INTO questions (question, answer, hint, roomId) VALUES (?, ?, ?, ?)");
            if ($insert->execute([$question, $answer, $hint, $roomId])) {
                header("Location: admin.php");
                exit;
            } else {
                $error = "Error adding the question.";
            }
        }
    }
}

// Fetch all questions
$stmt = $db_connection->query("SELECT * FROM questions ORDER BY id ASC");
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Admin - Question Management</title>
<style>
 body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  max-width: 900px;
  margin: 40px auto;
  padding: 20px;
  background-color: #f0f2f5;
  color: #333;
}

h1 {
  text-align: center;
  color: #222;
  margin-bottom: 30px;
}

.error {
  background-color: #f8d7da;
  border: 1px solid #f5c2c7;
  color: #842029;
  padding: 12px 15px;
  border-radius: 6px;
  margin-bottom: 25px;
  font-weight: 600;
}

form {
  background-color: #fff;
  border-radius: 8px;
  padding: 25px 30px;
  box-shadow: 0 4px 12px rgb(0 0 0 / 0.1);
  margin-bottom: 40px;
}

form label {
  display: block;
  margin-top: 15px;
  font-weight: 600;
  font-size: 1rem;
  color: #555;
}

form input[type="text"],
form input[type="number"] {
  width: 100%;
  padding: 10px 14px;
  margin-top: 6px;
  border-radius: 6px;
  border: 1.5px solid #ccc;
  font-size: 1rem;
  transition: border-color 0.3s ease;
}

form input[type="text"]:focus,
form input[type="number"]:focus {
  border-color: #0c6efc;
  outline: none;
  box-shadow: 0 0 6px #0c6efcaa;
}

form button {
  margin-top: 25px;
  background-color: #0c6efc;
  color: white;
  border: none;
  padding: 12px 28px;
  border-radius: 7px;
  font-size: 1.1rem;
  cursor: pointer;
  transition: background-color 0.3s ease;
  font-weight: 700;
}

form button:hover {
  background-color: #094fc0;
}

form a {
  margin-left: 15px;
  font-size: 1rem;
  color: #555;
  text-decoration: none;
  padding: 12px 20px;
  border-radius: 7px;
  background-color: #e2e6ea;
  transition: background-color 0.3s ease;
}

form a:hover {
  background-color: #cacfd6;
}

table {
  width: 100%;
  border-collapse: collapse;
  background-color: white;
  box-shadow: 0 3px 10px rgb(0 0 0 / 0.1);
  border-radius: 8px;
  overflow: hidden;
}

th, td {
  padding: 14px 20px;
  text-align: left;
  border-bottom: 1px solid #eee;
  font-size: 1rem;
  color: #444;
}

th {
  background-color: #0c6efc;
  color: white;
  font-weight: 700;
}

tbody tr:hover {
  background-color: #f5f9ff;
}

.actions a {
  margin-right: 10px;
  text-decoration: none;
  padding: 7px 15px;
  border-radius: 6px;
  font-weight: 600;
  font-size: 0.95rem;
  transition: background-color 0.3s ease;
  color: white;
  display: inline-block;
}

.actions a:nth-child(1) { /* Edit button */
  background-color: #198754; /* bootstrap green */
}

.actions a:nth-child(1):hover {
  background-color: #146c43;
}

.actions a.delete {
  background-color: #d9534f; /* bootstrap red */
}

.actions a.delete:hover {
  background-color: #b02a37;
}

</style>
</head>
<body>

<h1>Admin - Question Management</h1>

<?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<a href="index.php" style="display:inline-block; margin:10px 0; font-weight:bold; color:#0c6efc; text-decoration:none;">
  ← Terug naar Home
</a>

<!-- Add/Edit form -->
<form method="post" action="admin.php">
    <?php if ($editMode): ?>
        <input type="hidden" name="id" value="<?= (int)$editQuestion['id'] ?>">
    <?php endif; ?>

    <label for="question">Question:</label>
    <input type="text" id="question" name="question" required value="<?= ($editMode && isset($editQuestion['question'])) ? htmlspecialchars($editQuestion['question']) : '' ?>">

    <label for="answer">Answer:</label>
    <input type="text" id="answer" name="answer" required value="<?= ($editMode && isset($editQuestion['answer'])) ? htmlspecialchars($editQuestion['answer']) : '' ?>">

    <label for="hint">Hint (optional):</label>
    <input type="text" id="hint" name="hint" value="<?= ($editMode && isset($editQuestion['hint'])) ? htmlspecialchars($editQuestion['hint']) : '' ?>">

    <label for="roomId">Room ID:</label>
    <input type="number" id="roomId" name="roomId" required min="1" value="<?= ($editMode && isset($editQuestion['roomId'])) ? (int)$editQuestion['roomId'] : '' ?>">

    <button type="submit"><?= $editMode ? 'Update Question' : 'Add Question' ?></button>
    <?php if ($editMode): ?>
        <a href="admin.php" style="margin-left: 10px;">Cancel</a>
    <?php endif; ?>
</form>

<!-- Question list -->
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Question</th>
            <th>Answer</th>
            <th>Hint</th>
            <th>Room ID</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($questions) === 0): ?>
            <tr><td colspan="6" style="text-align:center;">No questions found.</td></tr>
        <?php else: ?>
            <?php foreach ($questions as $q): ?>
            <tr>
                <td><?= (int)$q['id'] ?></td>
                <td><?= htmlspecialchars($q['question']) ?></td>
                <td><?= htmlspecialchars($q['answer']) ?></td>
                <td><?= htmlspecialchars($q['hint']) ?></td>
                <td><?= (int)$q['roomId'] ?></td>
                <td class="actions">
                    <a href="admin.php?edit=<?= (int)$q['id'] ?>">Edit</a>
                    <a href="admin.php?delete=<?= (int)$q['id'] ?>" class="delete" onclick="return confirm('Are you sure you want to delete this question?');">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
