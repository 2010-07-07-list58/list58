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

class MysqlError extends Exception {
    public $mysql_errno = 0;
    public $mysql_error = '';
}

function mysql_query_or_error($query, $link) {
    $result = @mysql_query($query, $link);
    
    if ($result === FALSE) {
        $mysql_errno = mysql_errno($link);
        $mysql_error = mysql_error($link);
        
        $error = new MysqlError(
            sprintf(
                'Mysql query error: %s',
                $mysql_error
            )
        );
        $error->mysql_errno = $mysql_errno;
        $error->mysql_error = $mysql_error;
        
        throw $error;
    }
    
    return $result;
}

function mysql_escape_in_like_expr($str) {
    $escaped =
            str_replace('_', '\\_', 
                str_replace('%', '\\%', 
                    str_replace('\\', '\\\\', $str)));
    
    return $escaped;
}

function mysql_real_escape_like_expr_string($str, $link) {
    $escaped = mysql_real_escape_string(
        sprintf('%%%s%%', mysql_escape_in_like_expr($str)),
        $link
    );
    
    return $escaped;
}

function mysql_quote_like_expr_string($str, $link) {
    $quoted = sprintf(
        '\'%s\'',
        mysql_real_escape_like_expr_string(
            $str,
            $link
        )
    );
    
    return $quoted;
}

