<?php
// Шапка страницы -- подключается в начале каждой страницы.
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($title) ?> - Библиотека</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav>
    <a href="index.php">Книги</a>
    <a href="readers.php">Читатели</a>
    <a href="issues.php">Выдача</a>
</nav>
<main>
    <h2><?= h($title) ?></h2>
