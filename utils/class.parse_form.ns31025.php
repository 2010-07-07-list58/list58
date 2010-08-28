<?php

/*
    This file is part of List58.

    List58 is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    List58 is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with List58.  If not, see <http://www.gnu.org/licenses/>.

*/

class parse_error__ns31025
        extends Exception {}

function parse_day_check_intervals__ns31025($year, $month, $day) {
    if($year < 1800) {
        throw new parse_error__ns31025();
    }
    
    if($month < 1 || $month > 12) {
        throw new parse_error__ns31025();
    }
    
    if($day < 1 || $day > 31) {
        throw new parse_error__ns31025();
    }
}

function parse_ru_day__ns31025($str) {
    // распознавание "дня" (год, месяц, день) -- по Российским правилам
    
    $day_list = explode('.', $str);
    if(sizeof($day_list) != 3) {
        throw new parse_error__ns31025();
    }
    
    $day = @intval($day_list[0]);
    $month = @intval($day_list[1]);
    $year = @intval($day_list[2]);
    
    parse_day_check_intervals__ns31025($year, $month, $day);
    
    if(
        sprintf('%s.%02s.%s', $day, $month, $year) == $str ||
        sprintf('%02s.%02s.%s', $day, $month, $year) == $str
    ) {
        return array($year, $month, $day);
    } else {
        throw new parse_error__ns31025();
    }
}

function parse_iso_day__ns31025($str) {
    // распознавание "дня" (год, месяц, день) -- по международным правилам
    
    $day_list = explode('-', $str);
    if(sizeof($day_list) != 3) {
        throw new parse_error__ns31025();
    }
    
    $year = @intval($day_list[0]);
    $month = @intval($day_list[1]);
    $day = @intval($day_list[2]);
    
    parse_day_check_intervals__ns31025($year, $month, $day);
    
    if(sprintf('%s-%02s-%02s', $year, $month, $day) == $str) {
         return array($year, $month, $day);
    } else {
        throw new parse_error__ns31025();
    }
}


function parse_day__ns31025($str) {
    // распознавание "дня" (год, месяц, день) -- в универсальном виде
    
    try {
        return parse_ru_day__ns31025($str);
    } catch(parse_error__ns31025 $e) {
        return parse_iso_day__ns31025($str);
    }
}

function contruct_ru_day__ns31025($year, $month, $day) {
    // наоборот: собрать строку-"дня" из ранее распознанного
    
    return sprintf('%02s.%02s.%s', $day, $month, $year);
}

function normalize_ru_day__ns31025($str) {
    // нормализировать "день" -- по Российским правилам
    
    list($year, $month, $day) = parse_day__ns31025($str);
    
    return contruct_ru_day__ns31025($year, $month, $day);
}

function parse_ru_day_with_normalize__ns31025($str) {
    // распознавание "день" и нормазилировать
    
    list($year, $month, $day) = parse_day__ns31025($str);
    
    return array(
        $year, $month, $day,
        contruct_ru_day__ns31025($year, $month, $day)
    );
}

function normalize_ser_no__ns31025($str) {
    // нормализировать "серию" или "номер"
    
    return str_replace(' ', '', $str);
}


