<?php session_start(); session_destroy(); ?>
<!DOCTYPE html>
<html>
<head><title>Game Over</title></head>
<style>body {
  font-family: Arial, sans-serif;
  text-align: center;
  background-image: url('images/verlies-page.webp'); /* Zorg dat het pad klopt */
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  min-height: 100vh;
  margin: 0;
  padding: 0;
}</style>
<body>
<h1>😢 Helaas, je hebt verloren!</h1>
<a href="index.php">Probeer opnieuw</a>
</body>
</html>
