<?php
// Включаем отображение ошибок
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * Генерирует расписание работы на указанный месяц.
 *
 * @param int $year Год
 * @param int $month Месяц
 * @param bool $isFirstMonth Первый месяц (для учета переноса)
 * @param int|null &$prevLastWorkDayIndex Индекс последнего рабочего дня предыдущего месяца (по ссылке)
 * @return array Массив с названием месяца и расписанием
 */
function generateSchedule($year, $month, $isFirstMonth = true, $prevLastWorkDayIndex = null) {
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $schedule = [];

    // Создаем массив дат
    for ($day=1; $day <= $daysInMonth; $day++) {
        $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $day);
        $weekday = date('N', strtotime($dateStr));
        $schedule[$day] = [
            'date' => $dateStr,
            'isWeekend' => ($weekday >=6),
            'dayOfWeek' => $weekday,
            'isWork' => false,
        ];
    }

    if ($isFirstMonth) {
        // Первый день — рабочий или переносим на понедельник если выходной
        if ($schedule[1]['isWeekend']) {
            for ($d=2; $d <= $daysInMonth; $d++) {
                if ($schedule[$d]['dayOfWeek'] == 1) { // Понедельник
                    $schedule[$d]['isWork'] = true;
                    break;
                }
            }
        } else {
            // Первый день — рабочий
            $schedule[1]['isWork'] = true;
        }

        // Далее через два дня после последнего рабочего дня
        for ($d=1; ; ) {
            // Находим последний рабочий день
            if (!$schedule[$d]['isWork']) break;
            $lastWorkDay = $d;

            // Следующий через два дня
            $nextD = $lastWorkDay + 2;
            if ($nextD > $daysInMonth) break;

            if (!$schedule[$nextD]['isWeekend']) {
                if (!$schedule[$nextD]['isWork']) {
                    $schedule[$nextD]['isWork'] = true;
                }
                // Продолжаем цикл с этого дня
                //$d=$nextD; // Можно оставить для продолжения цепочки
                // Или продолжить? В данном случае лучше завершить цикл.
            } else {
                // Переносим на следующий понедельник после выходного
                for ($nd=$nextD+1; $nd <=$daysInMonth; ++$nd) {
                    if ($schedule[$nd]['dayOfWeek'] == 1) {
                        if (!$schedule[$nd]['isWork']) {
                            $schedule[$nd]['isWork'] = true;
                        }
                        break;
                    }
                }
            }
            break;
        }
    } else {
        // Для следующих месяцев: продолжаем цепочку с учетом предыдущего последнего рабочего дня
        if ($prevLastWorkDayIndex !== null && isset($schedule[$prevLastWorkDayIndex])) {
            for ($d=$prevLastWorkDayIndex+1; isset($schedule[$d]);$d++) {
                if (!$schedule[$d]['isWeekend']) {
                    // Можно сделать так:
                    if (!$schedule[1]['isWork']) {
                        // Или оставить первый день как есть?
                        break;
                    }
                }
            }
        } else {
            // Нет информации о предыдущем месяце - начинаем с первого не выходного дня
            for ($d=1; isset($schedule[$d]);$d++) {
                if (!$schedule[$d]['isWeekend']) break;
            }
        }

        // По условию делаем первый день месяца рабочим (если не выходной)
        if (isset($schedule[1]) && !$schedule[1]['isWeekend']) {
            $schedule[1]['isWork'] = true;
        }
    }

    return ['monthName' => date('F', strtotime("$year-$month-01")), 'schedule' => &$schedule];
}

/**
 * Выводит расписание с выделением рабочих дней.
 */
function displaySchedule($monthName, $schedule) {
    echo "Расписание на месяц: $monthName\n";
    echo "Дни:\n";

    static$counter=0;

    foreach ($schedule as $dayInfo) {
        if($dayInfo['isWork']){
            echo "\033[32m";   // Зеленый цвет для рабочих дней
            echo '+ ';
        }else{
            echo "\033[0m";   // Стандартный цвет для остальных дней
            echo '  ';
        }

        printf("%2s ", date('j', strtotime($dayInfo['date'])));

        echo "\033[0m";

        if(++$counter %7==0){
            echo "\n";
        }
    }
    echo "\n\n";
}

// Основная часть скрипта

// Получение параметров командной строки
$args= getopt("y:m:n:", ["year:", "month:", "count:"]);

$year= isset($args['y']) ? intval($args['y']) : (isset($args['year']) ? intval($args['year']) : date('Y'));
$month= isset($args['m']) ? intval($args['m']) : (isset($args['month']) ? intval($args['month']) : date('m'));
$count= isset($args['n']) ? intval($args['n']) : (isset($args['count']) ? intval($args['count']) : 1);

$lastWorkDayIndex=null;

for($i=0;$i<$count;$i++){
    list('monthName'=>$monthName,'schedule'=>$sched)=generateSchedule($year,$month,$i==0,$lastWorkDayIndex);

    displaySchedule($monthName,$sched);

    // Обновляем индекс последнего рабочего дня для следующего месяца:
    for ($k=count($sched);$k>=1;$k--) {
        if ($sched[$k]['isWork']){
            $lastWorkDayIndex=$k;
            break;
        }
    }

    # Переходим к следующему месяцу:
    ++$month;
    if ($month>12){
        $month=1;
        ++$year;
    }
}
