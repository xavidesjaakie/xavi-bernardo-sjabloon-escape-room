<?php
session_start();
require 'functions.php'; // nodig voor startGame()

// Start nieuwe game-gegevens
startGame();

header("Location: index.php");
exit;
