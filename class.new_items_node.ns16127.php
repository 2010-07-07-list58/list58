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

require_once dirname(__FILE__).'/class.node_base.ns8054.php';
require_once dirname(__FILE__).'/class.node.ns21085.php';

class new_items_node__ns16127 extends node__ns21085 {
    protected $_node_base__need_check_auth = TRUE;
    
    protected $_new_items_node__show_form = TRUE;
    protected $_new_items_node__message_html = '';
    
    protected function _node_base__on_add_check_perms() {
        parent::_node_base__on_add_check_perms();
        
        $this->_node_base__add_check_perms(
            array(
                // требуется разрешение на поиск Элементов Данных:
                'search_items' => TRUE,
                // требуется разрешение на создание Элементов Данных:
                'new_items' => TRUE,
            )
        );
    }
    
    protected function _node_base__on_init() {
        parent::_node_base__on_init();
        
        // TODO: обработка формы
    }
    
    protected function _node__get_title() {
        $parent_title = parent::_node__get_title();
        
        return 'Новые Данные - '.$parent_title;
    }
    
    protected function _node__get_head() {
        $parent_head = parent::_node__get_head();
        
        $html = '';
        
        $html .=
            $parent_head.
            '<link rel="stylesheet" type="text/css" href="/media/new_items_node/css/style.css" />'.
            '<script type="application/javascript" src="/media/new_items_node/js/autofocus.js" /></script>';
        
        return $html;
    }
    
    protected function _node__get_aside() {
        $form_html = '';
        
        if($this->_new_items_node__show_form) {
            $form_html =
                '<form action="'.htmlspecialchars("?node=".urlencode($this->get_arg('node'))).'" method="post">'.
                    '<h2 class="TextAlignCenter">Новые Данные</h2>'.
                    '<hr />'.
                    '<p>'.
                        '<input class="FloatRight Margin5Px" type="text" '.
                            'name="family_name" '.
                            'id="_new_items_node__family_name" '.
                            'value="" />'.
                        '<label class="FloatLeft Margin5Px" '.
                            'for="_new_items_node__family_name" >'.
                            'Фамилия: '.
                        '</label>'.
                        '<div class="ClearBoth"></div>'.
                    '</p>'.
                    '<p>'.
                        '<input class="FloatRight Margin5Px" type="text" '.
                            'name="given_name" '.
                            'id="_new_items_node__given_name" '.
                            'value="" />'.
                        '<label class="FloatLeft Margin5Px" '.
                            'for="_new_items_node__given_name" >'.
                            'Имя: '.
                        '</label>'.
                        '<div class="ClearBoth"></div>'.
                    '</p>'.
                    '<p>'.
                        '<input class="FloatRight Margin5Px" type="text" '.
                            'name="patronymic_name" '.
                            'id="_new_items_node__patronymic_name" '.
                            'value="" />'.
                        '<label class="FloatLeft Margin5Px" '.
                            'for="_new_items_node__patronymic_name" >'.
                            'Отчество: '.
                        '</label>'.
                        '<div class="ClearBoth"></div>'.
                    '</p>'.
                    // TODO: ...
                    '<hr />'.
                    '<p>'.
                        '<input type="hidden" '.
                            'name="post_key" '.
                            'value="'.htmlspecialchars($_SESSION['post_key']).'" />'.
                        '<input class="FloatLeft Margin5Px" type="submit" value="Создать" />'.
                        '<input class="FloatLeft Margin5Px" type="reset" value="Сброс" />'.
                        '<div class="ClearBoth"></div>'.
                    '</p>'.
                '</form>';
        }
        
        $html = '';
        
        $html .=
            '<div class="SmallFrame">'.
                $this->_new_items_node__message_html.
                $form_html.
            '</div>';
        
        return $html;
    }
}




