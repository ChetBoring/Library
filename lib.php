<?php

const DATA_DIR = __DIR__ . '/data/';

// Чтение таблицы из файла (общая блокировка).
function load(string $name): array
{
    $file = DATA_DIR . $name . '.json';

    if (!file_exists($file)) {
        return [];
    }

    $fp = fopen($file, 'r');
    flock($fp, LOCK_SH);
    $json = stream_get_contents($fp);
    flock($fp, LOCK_UN);
    fclose($fp);

    return json_decode($json, true) ?: [];
}

// Запись таблицы в файл (эксклюзивная блокировка).
function save(string $name, array $rows): void
{
    $fp = fopen(DATA_DIR . $name . '.json', 'c');
    flock($fp, LOCK_EX);
    ftruncate($fp, 0);
    fwrite($fp, json_encode(
        array_values($rows),
        JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
    ));
    fflush($fp);
    flock($fp, LOCK_UN);
    fclose($fp);
}

// Новый идентификатор записи.
function next_id(array $rows): int
{
    if (!$rows) {
        return 1;
    }
    return max(array_column($rows, 'id')) + 1;
}

// Безопасный вывод текста в HTML.
function h(?string $text): string
{
    return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

// Перенаправление на страницу.
function go(string $page): void
{
    header('Location: ' . $page);
    exit;
}

// // Сколько экземпляров книги сейчас на руках у читателей.
function on_hands(array $issues, int $bookId): int
{
    return count(array_filter($issues, function ($i) use ($bookId) {
        return $i['book'] === $bookId && !$i['returned'];
    }));
}

// Приведение телефона к виду +7 xxx xxx-xx-xx (null, если номер неверный).
function format_phone(string $raw): ?string
{
    $d = preg_replace('/\D/', '', $raw);

    if (strlen($d) === 10) {
        $d = '7' . $d;
    }
    if (strlen($d) !== 11 || !in_array($d[0], ['7', '8'], true)) {
        return null;
    }

    return sprintf(
        '+7 %s %s-%s-%s',
        substr($d, 1, 3),
        substr($d, 4, 3),
        substr($d, 7, 2),
        substr($d, 9, 2)
    );
}

// Дата из хранилища (гггг-мм-дд) в формат дд.мм.гггг.
function fmt_date(?string $date): string
{
    if (!$date) {
        return '-';
    }
    return date('d.m.Y', strtotime($date));
}