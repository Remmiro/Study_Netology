<?php
// Запрос данных у пользователя (можно заменить на получение из формы или другого источника)
$имя = isset($_POST['имя']) ? trim($_POST['имя']) : '';
$фамилия = isset($_POST['фамилия']) ? trim($_POST['фамилия']) : '';
$отчество = isset($_POST['отчество']) ? trim($_POST['отчество']) : '';

// Проверяем, существует ли функция mb_ucfirst, и объявляем её при необходимости
if (!function_exists('mb_ucfirst')) {
    function mb_ucfirst($string, $encoding='UTF-8') {
        $firstChar = mb_substr($string, 0, 1, $encoding);
        $then = mb_substr($string, 1, null, $encoding);
        return mb_strtoupper($firstChar, $encoding) . $then;
    }
}

// Формируем полное имя с заглавной буквы в начале каждого слова
$fullName = mb_ucfirst($фамилия) . ' ' . mb_ucfirst($имя) . ' ' . mb_ucfirst($отчество);

// Получаем инициалы (первая буква каждого имени с точкой)
$initials = '';
if ($имя !== '') {
    $initials .= mb_strtoupper(mb_substr($имя, 0, 1)) . '.';
}
if ($отчество !== '') {
    $initials .= mb_strtoupper(mb_substr($отчество, 0, 1)) . '.';
}

// Формируем строку "Фамилия И.О."
$surnameAndInitials = mb_ucfirst($фамилия) . ' ' .
                        (mb_strlen($имя) > 0 ? mb_strtoupper(mb_substr($имя, 0, 1)) . '.' : '') .
                        (mb_strlen($отчество) > 0 ? mb_strtoupper(mb_substr($отчество, 0, 1)) . '.' : '');

// Формируем аббревиатуру из первых букв ФИО
$fio = '';
if ($имя !== '') {
    $fio .= mb_strtoupper(mb_substr($имя, 0, 1));
}
if ($отчество !== '') {
    $fio .= mb_strtoupper(mb_substr($отчество, 0, 1));
}

// Вывод результатов
echo "Полное имя: " . htmlspecialchars($fullName) . "<br>";
echo "Фамилия и инициалы: " . htmlspecialchars($surnameAndInitials) . "<br>";
echo "Аббревиатура: " . htmlspecialchars($fio);
?>