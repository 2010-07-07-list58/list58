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

if (!ini_get('display_errors')) {
    ini_set('display_errors', 1);
}
error_reporting(E_ALL);

# BEGIN Google Chrome Frame

require_once dirname(__FILE__).'/class.gcf.php';

if(!gcf__check_xhtml_support()) {
    // броузер не поддерживает XHTML.
    //
    //      хотя XHTML нам в List58 и не нужен...
    //      но отсутвие такой простой технологии говорит о том
    //      что броузер совсем плохой (вероятнее всего это Microsoft Internet Explorer)
    //
    
    gcf__show_xhtml_error();
    
    return;
}

# END Google Chrome Frame

require_once dirname(__FILE__).'/../src/class.main.ns17829.php';

$main = new main__ns17829();

$main->run();

