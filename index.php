<?php
require 'lib.php';

$books = load('books');
$issues = load('issues');

// Обработка форм: удаление и добавление книги
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete'])) {
        $id = (int) $_POST['delete'];
        $books = array_filter($books, fn($b) => $b['id'] !== $id);
    } elseif (trim($_POST['title'] ?? '') !== '') {
        $books[] = [
            'id' => next_id($books),
            'title' => trim($_POST['title']),
            'author' => trim($_POST['author']),
            'year' => (int) $_POST['year'],
            'copies' => max(1, (int) $_POST['copies']),
        ];
    }
    save('books', $books);
    go('index.php');
}

// Поиск по названию или автору
$query = trim($_GET['q'] ?? '');
if ($query !== '') {
    $books = array_filter($books, function ($b) use ($query) {
        return mb_stripos($b['title'] . ' ' . $b['author'], $query) !== false;
    });
}

$title = 'Каталог книг';
require 'header.php';
?>

<form method="get">
    <input name="q" placeholder="Название или автор" value="<?= h($query) ?>">
    <button>Найти</button>
</form>

<div class="table-wrap">
<table>
    <tr>
        <th>№</th>
        <th>Название</th>
        <th>Автор</th>
        <th>Год</th>
        <th>В наличии</th>
        <th></th>
    </tr>
    <?php foreach ($books as $b): ?>
        <tr>
            <td><?= $b['id'] ?></td>
            <td><?= h($b['title']) ?></td>
            <td><?= h($b['author']) ?></td>
            <td><?= $b['year'] ?></td>
            <td><?= $b['copies'] - on_hands($issues, $b['id']) ?> из <?= $b['copies'] ?></td>
            <td>
                <form method="post">
                    <button name="delete" value="<?= $b['id'] ?>">Удалить</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
</div>

<h3>Добавить книгу</h3>
<form method="post">
    <input name="title" placeholder="Название" required>
    <input name="author" placeholder="Автор">
    <input name="year" type="number" placeholder="Год">
    <input name="copies" type="number" value="1" min="1">
    <button>Добавить</button>
</form>

<?php require 'footer.php'; ?>