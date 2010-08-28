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

class form_error__ns16127
        extends Exception {}

class new_items_node__ns16127 extends node__ns21085 {
    protected $_node_base__need_check_auth = TRUE;
    
    protected $_new_items_node__show_form = TRUE;
    protected $_new_items_node__message_html = '';
    
    protected $_new_items_node__given_name;
    protected $_new_items_node__family_name;
    protected $_new_items_node__patronymic_name;
    protected $_new_items_node__birthday;
    protected $_new_items_node__sex;
    protected $_new_items_node__passport_ser;
    protected $_new_items_node__passport_no;
    protected $_new_items_node__passport_dep;
    protected $_new_items_node__passport_day;
    protected $_new_items_node__residence;
    protected $_new_items_node__phone;
    protected $_new_items_node__about;
    protected $_new_items_node__comments;
    
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
    
    protected function _new_items_node__check_form() {
        if(
            !$this->_new_items_node__given_name &&
            !$this->_new_items_node__family_name &&
            !$this->_new_items_node__passport_no &&
            !$this->_new_items_node__residence &&
            !$this->_new_items_node__phone &&
            !$this->_new_items_node__about &&
            !$this->_new_items_node__comments
        ) {
            throw new form_error__ns16127(
                'Пожалуйста, укажите хотя бы какую-нибудь основную информацию'
            );
        }
        
        // TODO: проверка корректности заполнения
        
        throw new form_error__ns16127('[ЗАГЛУШКА] Это функция ещё не реализована!');
    }
    
    protected function _node_base__on_init() {
        parent::_node_base__on_init();
        
        try{
            if($_SERVER['REQUEST_METHOD'] == 'POST') {
                $this->_new_items_node__given_name = $this->post_arg('given_name');
                $this->_new_items_node__family_name = $this->post_arg('family_name');
                $this->_new_items_node__patronymic_name = $this->post_arg('patronymic_name');
                $this->_new_items_node__birthday = $this->post_arg('birthday');
                $this->_new_items_node__sex = $this->post_arg('sex');
                $this->_new_items_node__passport_ser = $this->post_arg('passport_ser');
                $this->_new_items_node__passport_no = $this->post_arg('passport_no');
                $this->_new_items_node__passport_dep = $this->post_arg('passport_dep');
                $this->_new_items_node__passport_day = $this->post_arg('passport_day');
                $this->_new_items_node__residence = $this->post_arg('residence');
                $this->_new_items_node__phone = $this->post_arg('phone');
                $this->_new_items_node__about = $this->post_arg('about');
                $this->_new_items_node__comments = $this->post_arg('comments');
                
                $this->_new_items_node__check_form();
                
                // TODO: обработка формы
                
                $this->_new_items_node__message_html .=
                    '<p class="SuccessColor TextAlignCenter">'.
                        'Запись успешно добавлена...'.
                    '</p>'.
                    '<p class="SuccessColor TextAlignCenter">'.
                        'Новая запись!'.
                    '</p>';
                
                @header('Refresh: 1;url=?node='.urlencode($this->get_arg('node')));
                $this->_new_items_node__show_form = FALSE;
            }
        } catch(form_error__ns16127 $e) {
            $message = $e->getMessage();
            
            $this->_new_items_node__message_html .=
                '<p class="ErrorColor TextAlignCenter">'.
                    htmlspecialchars($message).
                '</p>';
        }
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
                '<form action="'.htmlspecialchars('?node='.urlencode($this->get_arg('node'))).'" method="post">'.
                    '<h2 class="TextAlignCenter">Новые Данные</h2>'.
                    '<hr />'.
                    '<div class="GroupFrame">'.
                        '<p>'.
                            '<input class="FloatRight Margin5Px Width300Px" '.
                                'type="text" '.
                                'name="family_name" '.
                                'id="_new_items_node__family_name" '.
                                'value="'.htmlspecialchars($this->_new_items_node__family_name).'" />'.
                            '<label class="FloatLeft Margin5Px" '.
                                    'for="_new_items_node__family_name" >'.
                                'Фамилия: '.
                            '</label>'.
                            '<div class="ClearBoth"></div>'.
                        '</p>'.
                        '<p>'.
                            '<input class="FloatRight Margin5Px Width300Px" '.
                                'type="text" '.
                                'name="given_name" '.
                                'id="_new_items_node__given_name" '.
                                'value="'.htmlspecialchars($this->_new_items_node__given_name).'" />'.
                            '<label class="FloatLeft Margin5Px" '.
                                    'for="_new_items_node__given_name" >'.
                                'Имя: '.
                            '</label>'.
                            '<div class="ClearBoth"></div>'.
                        '</p>'.
                        '<p>'.
                            '<input class="FloatRight Margin5Px Width300Px" '.
                                'type="text" '.
                                'name="patronymic_name" '.
                                'id="_new_items_node__patronymic_name" '.
                                'value="'.htmlspecialchars($this->_new_items_node__patronymic_name).'" />'.
                            '<label class="FloatLeft Margin5Px" '.
                                    'for="_new_items_node__patronymic_name" >'.
                                'Отчество: '.
                            '</label>'.
                            '<div class="ClearBoth"></div>'.
                        '</p>'.
                    '</div>'.
                    '<div class="GroupFrame">'.
                        '<p>'.
                            '<input class="FloatRight Margin5Px Width300Px" '.
                                'type="text" '.
                                'name="birthday" '.
                                'id="_new_items_node__birthday" '.
                                'value="'.htmlspecialchars($this->_new_items_node__birthday).'" />'.
                            '<label class="FloatLeft Margin5Px" '.
                                    'for="_new_items_node__birthday" >'.
                                'Дата рождения: <br />'.
                                '<span class="FontSize07Em">'.
                                    '(Например: 27.09.1983) '.
                                '</span>'.
                            '</label>'.
                            '<div class="ClearBoth"></div>'.
                        '</p>'.
                        '<p>'.
                            '<select class="FloatRight Margin5Px Width300Px" '.
                                    'name="sex" '.
                                    'id="_new_items_node__sex" />'.
                                ($this->_new_items_node__sex?
                                    '<option value="'.
                                            htmlspecialchars($this->_new_items_node__sex).
                                    '">'.
                                        htmlspecialchars(
                                            sprintf('(Выбрано: %s)', $this->_new_items_node__sex)
                                        ).
                                    '</option>':
                                    ''
                                ).
                                '<option></option>'.
                                '<option value="Male">Мужской</option>'.
                                '<option value="Female">Женский</option>'.
                            '</select>'.
                            '<label class="FloatLeft Margin5Px" '.
                                    'for="_new_items_node__sex" >'.
                                'Пол: '.
                            '</label>'.
                            '<div class="ClearBoth"></div>'.
                        '</p>'.
                    '</div>'.
                    '<div class="GroupFrame">'.
                        '<p>'.
                            '<input class="FloatRight Margin5Px Width300Px" '.
                                'type="text" '.
                                'name="passport_ser" '.
                                'id="_new_items_node__passport_ser" '.
                                'value="'.htmlspecialchars($this->_new_items_node__passport_ser).'" />'.
                            '<label class="FloatLeft Margin5Px" '.
                                    'for="_new_items_node__passport_ser" >'.
                                'Серия паспорта: '.
                            '</label>'.
                            '<div class="ClearBoth"></div>'.
                        '</p>'.
                        '<p>'.
                            '<input class="FloatRight Margin5Px Width300Px" '.
                                'type="text" '.
                                'name="passport_no" '.
                                'id="_new_items_node__passport_no" '.
                                'value="'.htmlspecialchars($this->_new_items_node__passport_no).'" />'.
                            '<label class="FloatLeft Margin5Px" '.
                                    'for="_new_items_node__passport_no" >'.
                                'Номер паспорта: '.
                            '</label>'.
                            '<div class="ClearBoth"></div>'.
                        '</p>'.
                        '<p>'.
                            '<input class="FloatRight Margin5Px Width300Px" '.
                                'type="text" '.
                                'name="passport_dep" '.
                                'id="_new_items_node__passport_dep" '.
                                'value="'.htmlspecialchars($this->_new_items_node__passport_dep).'" />'.
                            '<label class="FloatLeft Margin5Px" '.
                                    'for="_new_items_node__passport_dep" >'.
                                'Кем выдан паспорт: '.
                            '</label>'.
                            '<div class="ClearBoth"></div>'.
                        '</p>'.
                        '<p>'.
                            '<input class="FloatRight Margin5Px Width300Px" '.
                                'type="text" '.
                                'name="passport_day" '.
                                'id="_new_items_node__passport_day" '.
                                'value="'.htmlspecialchars($this->_new_items_node__passport_day).'" />'.
                            '<label class="FloatLeft Margin5Px" '.
                                    'for="_new_items_node__passport_day" >'.
                                'Дата выдачи паспорта: <br />'.
                                '<span class="FontSize07Em">'.
                                    '(Например: 27.09.2003) '.
                                '</span>'.
                            '</label>'.
                            '<div class="ClearBoth"></div>'.
                        '</p>'.
                        '<p>'.
                            '<textarea class="FloatRight Margin5Px Width300Px"'.
                                    'rows="4" '.
                                    'name="residence" '.
                                    'id="_new_items_node__residence" />'.
                                htmlspecialchars($this->_new_items_node__residence).
                            '</textarea>'.
                            '<label class="FloatLeft Margin5Px"'.
                                    ' for="_new_items_node__residence" >'.
                                'Адрес регистрации: <br />'.
                                '<span class="FontSize07Em">'.
                                    '(Например: <br />'.
                                    '622014, Свердловская область, <br />'.
                                    'г. Нижний Тагил, ул. Королева, <br />'.
                                    'д.181, кв.354) '.
                                '</span>'.
                            '</label>'.
                            '<div class="ClearBoth"></div>'.
                        '</p>'.
                    '</div>'.
                    '<div class="GroupFrame">'.
                        '<p>'.
                            '<input class="FloatRight Margin5Px Width300Px" '.
                                'type="text" '.
                                'name="phone" '.
                                'id="_new_items_node__phone" '.
                                'value="'.htmlspecialchars($this->_new_items_node__phone).'" />'.
                            '<label class="FloatLeft Margin5Px" '.
                                    'for="_new_items_node__phone" >'.
                                'Телефон: <br />'.
                                '<span class="FontSize07Em">'.
                                    '(Например: <br />'.
                                    '+78412123456, или 88412123456, или 123456) '.
                                '</span>'.
                            '</label>'.
                            '<div class="ClearBoth"></div>'.
                        '</p>'.
                        '<p>'.
                            '<textarea class="FloatRight Margin5Px Width300Px"'.
                                    'rows="6" '.
                                    'name="about" '.
                                    'id="_new_items_node__about" />'.
                                htmlspecialchars($this->_new_items_node__about).
                            '</textarea>'.
                            '<label class="FloatLeft Margin5Px"'.
                                    ' for="_new_items_node__about" >'.
                                'Дополнительное описание: <br />'.
                                '<span class="FontSize07Em">'.
                                    '(Произвольная информация) '.
                                '</span>'.
                            '</label>'.
                            '<div class="ClearBoth"></div>'.
                        '</p>'.
                        '<p>'.
                            '<textarea class="FloatRight Margin5Px Width300Px"'.
                                    'rows="10" '.
                                    'name="comments" '.
                                    'id="_new_items_node__comments" />'.
                                htmlspecialchars($this->_new_items_node__comments).
                            '</textarea>'.
                            '<label class="FloatLeft Margin5Px"'.
                                    ' for="_new_items_node__comments" >'.
                                'Примечание: <br />'.
                                '<span class="FontSize07Em">'.
                                    '(Произвольная информация. <br />'.
                                    'Например, причина попадания в список) '.
                                '</span>'.
                            '</label>'.
                            '<div class="ClearBoth"></div>'.
                        '</p>'.
                    '</div>'.
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




