<?php
// Включаем отображение ошибок
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * Генерирует расписание на указанный месяц.
 * Первый рабочий день месяца — 1 число, если оно не выходной.
 * Далее рабочие дни через два дня, перенос на понедельник после выходных.
 *
 * @param int $year Год
 * @param int $month Месяц
 * @param int|null $prevLastWorkDayIndex Индекс последнего рабочего дня предыдущего месяца (по ссылке)
 * @return array Массив с названием месяца и расписанием
 */
function generateSchedule($year, $month, &$prevLastWorkDayIndex = null) {
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

    // Определяем первый рабочий день
    if ($month == date('m') && $year == date('Y')) {
        // Для текущего месяца можно оставить как есть или задать по условию
    }

    // Первый день — рабочий или переносим на понедельник если выходной
    if (!$schedule[1]['isWeekend']) {
        $schedule[1]['isWork'] = true;
        $lastWorkDayIdx = 1;
    } else {
        // Переносим на следующий понедельник
        for ($d=2; $d <= $daysInMonth; $d++) {
            if ($schedule[$d]['dayOfWeek'] == 1) { // Понедельник
                if (!$schedule[$d]['isWeekend']) {
                    $schedule[$d]['isWork'] = true;
                    $lastWorkDayIdx = $d;
                }
                break;
            }
        }
    }

    // Продолжаем через два дня после последнего рабочего дня
    for ($d=$lastWorkDayIdx+2; $d <=$daysInMonth; ) {
        if (!$schedule[$d]['isWeekend']) {
            if (!$schedule[$d]['isWork']) {
                $schedule[$d]['isWork'] = true;
                $lastWorkDayIdx = $d;
            }
            break; // После добавления одного рабочего дня — выходим (можно расширить по логике)
        } else {
            // Переносим на следующий понедельник после выходного
            for ($nd=$d+1; $nd <=$daysInMonth; ++$nd) {
                if ($schedule[$nd]['dayOfWeek'] == 1) { // Понедельник
                    if (!$schedule[$nd]['isWork']) {
                        $schedule[$nd]['isWork'] = true;
                        $lastWorkDayIdx = $nd;
                    }
                    break;
                }
            }
            break;
        }
        break; // чтобы не зациклиться
    }

    return ['monthName' => date('F', strtotime("$year-$month-01")), 'schedule' => &$schedule];
}

/**
 * Выводит расписание с выделением рабочих дней.
 */
function displaySchedule($monthName, &$schedule) {
    echo "Расписание на месяц: {$monthName}\n";
    echo "Дни:\n";

    static$counter=0;

    foreach ($schedule as &$dayInfo) {
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

// Получение параметров командной строки (можно передавать стартовые месяц/год и количество месяцев)
$args= getopt("y:m:n:", ["year:", "month:", "count:"]);

$startYear= isset($args['y']) ? intval($args['y']) : (isset($args['year']) ? intval($args['year']) : date('Y'));
$startMonth= isset($args['m']) ? intval($args['m']) : (isset($args['month']) ? intval($args['month']) : date('m'));
$count= isset($args['n']) ? intval($args['n']) : (isset($args['count']) ? intval($args['count']) : 1);

$lastWorkDayIndex=null;

for($i=0;$i<$count;$i++){
    list('monthName'=>$monthName,'schedule'=>$sched)=generateSchedule($startYear,$startMonth,$lastWorkDayIndex);

    displaySchedule($monthName,$sched);

    // Обновляем индекс последнего рабочего дня для следующего месяца:
    for ($k=count($sched);$k>=1;$k--) {
        if ($sched[$k]['isWork']){
            $lastWorkDayIndex=$k;
            break;
        }
    }

    // Переходим к следующему месяцу:
    ++$startMonth;
    if ($startMonth>12){
        $startMonth=1;
        ++$startYear;
    }
}
?>