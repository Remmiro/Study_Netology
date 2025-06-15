<?php
// Объявляем функцию readLine только если она ещё не объявлена
if (!function_exists('readLine')) {
    function readLine($prompt) {
        echo $prompt;
        return trim(fgets(STDIN));
    }
}

// Функция для заглавной буквы с поддержкой многобайтовых символов
if (!function_exists('mb_ucfirst')) {
    function mb_ucfirst($string, $encoding='UTF-8') {
        $firstChar = mb_substr($string, 0, 1, $encoding);
        $then = mb_substr($string, 1, null, $encoding);
        return mb_strtoupper($firstChar, $encoding) . $then;
    }
}

// Запрос данных у пользователя
$имя = readLine('Введите имя: ');
$фамилия = readLine('Введите фамилию: ');
$отчество = readLine('Введите отчество: ');

// Формируем полное имя с заглавной буквы в начале каждого слова
$fullName = mb_ucfirst($фамилия) . ' ' . mb_ucfirst($имя) . ' ' . mb_ucfirst($отчество);

// Получаем инициалы (первая буква каждого имени с точкой)
$surnameInitials = '';
if ($фамилия !== '') {
    $surnameInitials .= mb_ucfirst($фамилия);
}
if ($имя !== '') {
    $surnameInitials .= ' ' . mb_strtoupper(mb_substr($имя, 0, 1)) . '.';
}
if ($отчество !== '') {
    $surnameInitials .= ' ' . mb_strtoupper(mb_substr($отчество, 0, 1)) . '.';
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
echo "Полное имя: " . $fullName . PHP_EOL;
echo "Фамилия и инициалы: " . $surnameAndInitials . PHP_EOL;
echo "Аббревиатура: " . $fio . PHP_EOL;
?>
