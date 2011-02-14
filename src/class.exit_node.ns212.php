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

require_once dirname(__FILE__).'/class.base_node.ns8054.php';
require_once dirname(__FILE__).'/class.node.ns21085.php';
require_once dirname(__FILE__).'/utils/class.mysql_tools.php';

class exit_node__ns212 extends node__ns21085 {
    protected $_base_node__need_check_auth = TRUE;
    protected $_base_node__need_check_post_token_for_get = TRUE;
    
    protected $_exit_node__login;
    protected $_exit_node__clean_all;
    
    protected function _base_node__on_init() {
        parent::_base_node__on_init();
        
        $this->_exit_node__login = $_SESSION['reg_data']['login'];
        $this->_exit_node__clean_all = $this->get_arg('clean_all')?TRUE:FALSE;
        
        $this->_base_node__clean_auth();
        
        if($this->_exit_node__clean_all) {
            mysql_query_or_error(
                sprintf(
                    'DELETE FROM `user_sessions` WHERE `login` = \'%s\'',
                    mysql_real_escape_string($this->_exit_node__login, $this->_base_node__db_link)
                ),
                $this->_base_node__db_link
            );
        }
        
        @header('Refresh: 1;url=?');
    }
    
    protected function _node__get_title() {
        $parent_title = parent::_node__get_title();
        
        return 'Выход - '.$parent_title;
    }
    
    protected function _node__get_head() {
        $parent_head = parent::_node__get_head();
        
        $html = '';
        
        $html .=
            $parent_head.
            '<link rel="stylesheet" href="/media/about_node/css/style.css" />';
        
        return $html;
    }
    
    protected function _node__get_aside() {
        $html = '';
        
        $html .=
            '<div class="SmallFrame">'.
            (
                $this->_exit_node__clean_all?
                '<p>Выход с закрытием всех сессий ['.htmlspecialchars($this->_exit_node__login).']...</p>':
                '<p>Выход из сессии ['.htmlspecialchars($this->_exit_node__login).']...</p>'
            ).
            '</div>';
        
        return $html;
    }
}

