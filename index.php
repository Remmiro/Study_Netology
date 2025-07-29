<?php
// Убираем declare(strict_types=1);

// Объявляем константы
const OPERATION_EXIT = 0;
const OPERATION_ADD = 1;
const OPERATION_DELETE = 2;
const OPERATION_PRINT = 3;

// Массив описаний операций
$operations = [
    OPERATION_EXIT => OPERATION_EXIT . '. Завершить программу.',
    OPERATION_ADD => OPERATION_ADD . '. Добавить товар в список покупок.',
    OPERATION_DELETE => OPERATION_DELETE . '. Удалить товар из списка покупок.',
    OPERATION_PRINT => OPERATION_PRINT . '. Отобразить список покупок.',
];

$items = array();

/**
 * Выводит текущий список покупок.
 */
function displayItems($items) {
    if (count($items) > 0) {
        echo 'Ваш список покупок:' . PHP_EOL;
        echo implode("\n", $items) . PHP_EOL;
        echo 'Всего ' . count($items) . ' позиций.' . PHP_EOL;
    } else {
        echo 'Ваш список покупок пуст.' . PHP_EOL;
    }
}

/**
 * Запрашивает у пользователя операцию и возвращает её номер.
 */
function getOperationNumber($operations, $items) {
    do {
        system('clear'); // или system('cls') на Windows
        displayItems($items);
        echo 'Выберите операцию для выполнения:' . PHP_EOL;
        foreach ($operations as $key => $desc) {
            echo "$key. $desc" . PHP_EOL;
        }
        echo '> ';
        $input = trim(fgets(STDIN));
        if (isset($operations[$input])) {
            return (int)$input;
        } else {
            echo 'Некорректный ввод. Попробуйте снова.' . PHP_EOL;
        }
    } while (true);
}

/**
 * Обработка добавления товара.
 */
function addItem(&$items) {
    echo "Введите название товара для добавления:\n> ";
    $name = trim(fgets(STDIN));
    if ($name !== '') {
        $items[] = $name;
        echo "Товар '$name' добавлен.\n";
    } else {
        echo "Пустое название. Товар не добавлен.\n";
    }
}

/**
 * Обработка удаления товара.
 */
function deleteItem(&$items) {
    if (count($items) === 0) {
        echo "Список пуст. Удалять нечего.\n";
        return;
    }
    displayItems($items);
    echo "Введите название товара для удаления:\n> ";
    $name = trim(fgets(STDIN));
    if (($key = array_search($name, $items)) !== false) {
        unset($items[$key]);
        $items = array_values($items);
        echo "Товар '$name' удален.\n";
    } else {
        echo "Товар '$name' не найден.\n";
    }
}

/**
 * Выводит список покупок.
 */
function printItems($items) {
    displayItems($items);
    echo "Нажмите Enter для продолжения...";
    fgets(STDIN);
}

// Основной цикл
do {
    $operationNumber = getOperationNumber($operations, $items);

    echo 'Выбрана операция: '  . (isset($operations[$operationNumber]) ? $operations[$operationNumber] : '') . PHP_EOL;

    switch ($operationNumber) {
        case OPERATION_ADD:
            addItem($items);
            break;

        case OPERATION_DELETE:
            deleteItem($items);
            break;

        case OPERATION_PRINT:
            printItems($items);
            break;

        case OPERATION_EXIT:
            // Выход из цикла
            break;

        default:
            // Не должно произойти
            break;
    }

    echo "\n ----- \n";

} while ($operationNumber != OPERATION_EXIT);

echo 'Программа завершена' . PHP_EOL;