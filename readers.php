<?php
require 'lib.php';

$readers = load('readers');
$error = '';
$name = '';
$phone = '';

// Обработка форм - удаление и добавление читателя
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete'])) {
        $id = (int) $_POST['delete'];
        $readers = array_filter($readers, fn($r) => $r['id'] !== $id);
    } else {
        $name  = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $fmt   = format_phone($phone);

        if (mb_strlen($name) < 2) {
            $error = 'Введите ФИО читателя';
        } elseif ($fmt === null) {
            $error = 'Неверный номер телефона. Пример: +7 900 111-22-33';
        } else {
            $readers[] = [
                'id'    => next_id($readers),
                'name'  => $name,
                'phone' => $fmt,
            ];
            save('readers', $readers);
            go('readers.php');
        }
    }

    if (isset($_POST['delete'])) {
        save('readers', $readers);
        go('readers.php');
    }
}

$title = 'Читатели';
require 'header.php';
?>

<div class="table-wrap">
<table>
    <tr>
        <th>№</th>
        <th>ФИО</th>
        <th>Телефон</th>
        <th></th>
    </tr>
    <?php foreach ($readers as $r): ?>
        <tr>
            <td><?= $r['id'] ?></td>
            <td><?= h($r['name']) ?></td>
            <td><?= h($r['phone']) ?></td>
            <td>
                <form method="post">
                    <button name="delete" value="<?= $r['id'] ?>">Удалить</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
</div>

<h3>Добавить читателя</h3>
<p class="error"><?= h($error) ?></p>
<form method="post">
    <input name="name" placeholder="ФИО" value="<?= h($name) ?>" required>
    <input name="phone" type="tel" placeholder="+7 900 111-22-33" value="<?= h($phone) ?>" required>
    <button>Добавить</button>
</form>

<?php require 'footer.php'; ?>