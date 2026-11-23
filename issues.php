<?php
require 'lib.php';

$books = load('books');
$readers = load('readers');
$issues = load('issues');
$error = '';

$bookTitles = array_column($books, 'title', 'id');
$readerNames = array_column($readers, 'name', 'id');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['return'])) {
        // Возврат книги: ставлюлюлюлюлюлюлюлюлюлюл дату возврата
        $id = (int) $_POST['return'];
        foreach ($issues as &$issue) {
            if ($issue['id'] === $id) {
                $issue['returned'] = date('Y-m-d');
            }
        }
        unset($issue);
        save('issues', $issues);
        go('issues.php');
    }

    // Выдача книги: проверяю наличие свободных экземпляров
    $bookId   = (int) $_POST['book'];
    $readerId = (int) $_POST['reader'];

    $copies = 0;
    foreach ($books as $b) {
        if ($b['id'] === $bookId) {
            $copies = $b['copies'];
        }
    }

    if (on_hands($issues, $bookId) >= $copies) {
        $error = 'Свободных экземпляров нет';
    } else {
        $issues[] = [
            'id' => next_id($issues),
            'book' => $bookId,
            'reader' => $readerId,
            'date' => date('Y-m-d'),
            'returned' => null,
        ];
        save('issues', $issues);
        go('issues.php');
    }
}

$title = 'Выдача и возврат книг';
require 'header.php';
?>

<form method="post">
    <select name="book">
        <?php foreach ($books as $b): ?>
            <option value="<?= $b['id'] ?>"><?= h($b['title']) ?></option>
        <?php endforeach; ?>
    </select>
    <select name="reader">
        <?php foreach ($readers as $r): ?>
            <option value="<?= $r['id'] ?>"><?= h($r['name']) ?></option>
        <?php endforeach; ?>
    </select>
    <button>Выдать</button>
</form>

<p class="error"><?= h($error) ?></p>

<div class="table-wrap">
<table>
    <tr>
        <th>№</th>
        <th>Книга</th>
        <th>Читатель</th>
        <th>Выдана</th>
        <th>Возвращена</th>
        <th></th>
    </tr>
    <?php foreach (array_reverse($issues) as $i): ?>
        <tr>
            <td><?= $i['id'] ?></td>
            <td><?= h($bookTitles[$i['book']] ?? '-') ?></td>
            <td><?= h($readerNames[$i['reader']] ?? '-') ?></td>
            <td><?= fmt_date($i['date']) ?></td>
            <td><?= fmt_date($i['returned']) ?></td>
            <td>
                <?php if (!$i['returned']): ?>
                    <form method="post">
                        <button name="return" value="<?= $i['id'] ?>">Принять</button>
                    </form>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
</div>

<?php require 'footer.php'; ?>