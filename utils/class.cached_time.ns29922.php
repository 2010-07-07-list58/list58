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


$time_cache__ns29922 = NULL;

function get_time__ns29922() {
    // кэшируемое получение времени
    // пояснение:
    //      при многократном вызове функции
    //      будет выдаваться одно и тоже значение времени,
    //      предотвратив неоднозначность в генерируемых данных
    
    global $time_cache__ns29922;
    
    if($time_cache__ns29922) {
        return $time_cache__ns29922;
    } else {
        $time = @time();
        
        $time_cache__ns29922 = $time;
        
        return $time;
    }
}

function new_token__ns29922() {
    $token = sprintf('%s-%s', get_time__ns29922(), rand());
    
    return $token;
}


