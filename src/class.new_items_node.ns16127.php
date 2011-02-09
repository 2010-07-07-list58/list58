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
require_once dirname(__FILE__).'/utils/class.parse_form.ns31025.php';
require_once dirname(__FILE__).'/utils/class.cached_time.ns29922.php';

class form_error__ns16127
        extends Exception {}

class new_items_node__ns16127 extends node__ns21085 {
    protected $_base_node__need_db = TRUE;
    protected $_base_node__need_check_auth = TRUE;
    
    protected $_new_items_node__show_form = TRUE;
    protected $_new_items_node__message_html = '';
    
    protected $_new_items_node__item_deleted = 0;
    protected $_new_items_node__given_name;
    protected $_new_items_node__family_name;
    protected $_new_items_node__patronymic_name;
    protected $_new_items_node__birthday;
    protected $_new_items_node__birth_year;
    protected $_new_items_node__birth_month;
    protected $_new_items_node__birth_day;
    protected $_new_items_node__sex;
    protected $_new_items_node__sex_enum;
    protected $_new_items_node__passport_ser;
    protected $_new_items_node__passport_no;
    protected $_new_items_node__passport_dep;
    protected $_new_items_node__passport_day;
    protected $_new_items_node__residence_city;
    protected $_new_items_node__residence;
    protected $_new_items_node__phone;
    protected $_new_items_node__phone2;
    protected $_new_items_node__about;
    protected $_new_items_node__comments;
    
    protected function _base_node__on_add_check_perms() {
        parent::_base_node__on_add_check_perms();
        
        $this->_base_node__add_check_perms(
            array(
                // требуется разрешение на просмотр Элементов Данных:
                'view_items' => TRUE,
                // требуется разрешение на создание Элементов Данных:
                'new_items' => TRUE,
            )
        );
    }
    
    protected function _new_items_node__parse_form() {
        if(
            !$this->_new_items_node__given_name &&
            !$this->_new_items_node__family_name &&
            !$this->_new_items_node__passport_no &&
            !$this->_new_items_node__residence &&
            !$this->_new_items_node__phone &&
            !$this->_new_items_node__phone2 &&
            !$this->_new_items_node__about &&
            !$this->_new_items_node__comments
        ) {
            throw new form_error__ns16127(
                'Пожалуйста, укажите хотя бы какую-нибудь основную информацию'
            );
        }
        
        if($this->_new_items_node__birthday) {
            try {
                list(
                    $this->_new_items_node__birth_year,
                    $this->_new_items_node__birth_month,
                    $this->_new_items_node__birth_day,
                    $this->_new_items_node__birthday,
                ) = parse_ru_day_with_normalize__ns31025($this->_new_items_node__birthday);
            } catch (parse_error__ns31025 $e) {
                throw new form_error__ns16127(
                    '\'Дата рождения\' указана неверно'
                );
            }
        }
        
        if($this->_new_items_node__sex) {
            if($this->_new_items_node__sex == 'Мужской') {
                $this->_new_items_node__sex_enum = 1;
            } elseif($this->_new_items_node__sex == 'Женский') {
                $this->_new_items_node__sex_enum = 2;
            } else {
                throw new form_error__ns16127(
                    '\'Пол\' указан неверно'
                );
            }
        } else {
            $this->_new_items_node__sex_enum = 0;
        }
        
        if($this->_new_items_node__passport_ser) {
            try {
                $this->_new_items_node__passport_ser = normalize_ser_no__ns31025(
                    $this->_new_items_node__passport_ser
                );
            } catch (parse_error__ns31025 $e) {
                throw new form_error__ns16127(
                    '\'Серия паспорта\' указана неверно'
                );
            }
        }
        
        if($this->_new_items_node__passport_no) {
            try {
                $this->_new_items_node__passport_no = normalize_ser_no__ns31025(
                    $this->_new_items_node__passport_no
                );
            } catch (parse_error__ns31025 $e) {
                throw new form_error__ns16127(
                    '\'Номер паспорта\' указан неверно'
                );
            }
        }
        
        if($this->_new_items_node__passport_day) {
            try {
                $this->_new_items_node__passport_day = normalize_ru_day__ns31025(
                    $this->_new_items_node__passport_day
                );
            } catch (parse_error__ns31025 $e) {
                throw new form_error__ns16127(
                    '\'Дата выдачи паспорта\' указана неверно'
                );
            }
        }
        
        if($this->_new_items_node__phone) {
            try {
                $this->_new_items_node__phone = normalize_phone__ns31025(
                    $this->_new_items_node__phone
                );
            } catch (parse_error__ns31025 $e) {
                throw new form_error__ns16127(
                    '\'Телефон\' указан неверно'
                );
            }
        }
        
        if($this->_new_items_node__phone2) {
            try {
                $this->_new_items_node__phone2 = normalize_phone__ns31025(
                    $this->_new_items_node__phone2
                );
            } catch (parse_error__ns31025 $e) {
                throw new form_error__ns16127(
                    '\'Дополнительный Телефон\' указан неверно'
                );
            }
        }
    }
    
    protected function _new_items_node__into_db() {
        $item_owner = $_SESSION['reg_data']['login'];
        $item_created = get_time__ns29922();
        
        try {
            $result = mysql_query_or_error(
                sprintf(
                    'INSERT INTO `items_base` ('.
                        '`item_owner`, '.
                        '`item_created`, '.
                        '`item_modified`, '.
                        '`item_deleted`, '.
                        '`given_name`, '.
                        '`family_name`, '.
                        '`patronymic_name`, '.
                        '`birth_year`, '.
                        '`birth_month`, '.
                        '`birth_day`, '.
                        '`sex`, '.
                        '`passport_ser`, '.
                        '`passport_no`, '.
                        '`passport_dep`, '.
                        '`passport_day`, '.
                        '`residence_city`, '.
                        '`residence`, '.
                        '`phone`, '.
                        '`phone2`, '.
                        '`about`, '.
                        '`comments`'.
                    ') '.
                    'VALUES ('.
                        '\'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', '.
                        '\'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', '.
                        '\'%s\''.
                    ')',
                    mysql_real_escape_string($item_owner, $this->_base_node__db_link),
                    mysql_real_escape_string($item_created, $this->_base_node__db_link),
                    mysql_real_escape_string($item_created, $this->_base_node__db_link),
                    mysql_real_escape_string($this->_new_items_node__item_deleted, $this->_base_node__db_link),
                    mysql_real_escape_string($this->_new_items_node__given_name, $this->_base_node__db_link),
                    mysql_real_escape_string($this->_new_items_node__family_name, $this->_base_node__db_link),
                    mysql_real_escape_string($this->_new_items_node__patronymic_name, $this->_base_node__db_link),
                    mysql_real_escape_string($this->_new_items_node__birth_year, $this->_base_node__db_link),
                    mysql_real_escape_string($this->_new_items_node__birth_month, $this->_base_node__db_link),
                    mysql_real_escape_string($this->_new_items_node__birth_day, $this->_base_node__db_link),
                    mysql_real_escape_string($this->_new_items_node__sex_enum, $this->_base_node__db_link),
                    mysql_real_escape_string($this->_new_items_node__passport_ser, $this->_base_node__db_link),
                    mysql_real_escape_string($this->_new_items_node__passport_no, $this->_base_node__db_link),
                    mysql_real_escape_string($this->_new_items_node__passport_dep, $this->_base_node__db_link),
                    mysql_real_escape_string($this->_new_items_node__passport_day, $this->_base_node__db_link),
                    mysql_real_escape_string($this->_new_items_node__residence_city, $this->_base_node__db_link),
                    mysql_real_escape_string($this->_new_items_node__residence, $this->_base_node__db_link),
                    mysql_real_escape_string($this->_new_items_node__phone, $this->_base_node__db_link),
                    mysql_real_escape_string($this->_new_items_node__phone2, $this->_base_node__db_link),
                    mysql_real_escape_string($this->_new_items_node__about, $this->_base_node__db_link),
                    mysql_real_escape_string($this->_new_items_node__comments, $this->_base_node__db_link)
                ),
                $this->_base_node__db_link
            );
        } catch(MysqlError $e) {
            throw new form_error__ns16127(
                sprintf(
                    'Ошибка при сохранении данных внутри Базы Данных (%s)',
                    $e->mysql_error
                )
            );
        }
    }
    
    protected function _base_node__on_init() {
        parent::_base_node__on_init();
        
        $this->_new_items_node__given_name = trim($this->post_arg('given_name'));
        $this->_new_items_node__family_name = trim($this->post_arg('family_name'));
        $this->_new_items_node__patronymic_name = trim($this->post_arg('patronymic_name'));
        $this->_new_items_node__birthday = trim($this->post_arg('birthday'));
        $this->_new_items_node__sex = trim($this->post_arg('sex'));
        $this->_new_items_node__passport_ser = trim($this->post_arg('passport_ser'));
        $this->_new_items_node__passport_no = trim($this->post_arg('passport_no'));
        $this->_new_items_node__passport_dep = trim($this->post_arg('passport_dep'));
        $this->_new_items_node__passport_day = trim($this->post_arg('passport_day'));
        $this->_new_items_node__residence_city = trim($this->post_arg('residence_city', 'Пенза'));
        $this->_new_items_node__residence = trim($this->post_arg('residence'));
        $this->_new_items_node__phone = trim($this->post_arg('phone'));
        $this->_new_items_node__phone2 = trim($this->post_arg('phone2'));
        $this->_new_items_node__about = trim($this->post_arg('about'));
        $this->_new_items_node__comments = trim($this->post_arg('comments'));
        
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            try{
                // обработать форму:
                $this->_new_items_node__parse_form();
                // поместить значения в Базу Данных:
                $this->_new_items_node__into_db();
                
                // успех!!
                $this->_new_items_node__message_html .=
                    '<p class="SuccessColor TextAlignCenter">'.
                        'Запись успешно добавлена...'.
                    '</p>'.
                    '<p class="SuccessColor TextAlignCenter">'.
                        'Новая запись!'.
                    '</p>';
                
                @header('Refresh: 1;url=?node='.urlencode($this->get_arg('node')));
                $this->_new_items_node__show_form = FALSE;
            } catch(form_error__ns16127 $e) {
                $message = $e->getMessage();
                
                $this->_new_items_node__message_html .=
                    '<p class="ErrorColor TextAlignCenter">'.
                        htmlspecialchars($message).
                    '</p>';
            }
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
            '<link rel="stylesheet" href="/media/new_items_node/css/style.css" />'.
            '<script src="/media/new_items_node/js/autofocus.js"></script>';
        
        return $html;
    }
    
    protected function _node__get_aside() {
        $form_html = '';
        
        if($this->_new_items_node__show_form) {
            $form_html =
                '<form action="'.htmlspecialchars('?node='.urlencode($this->get_arg('node'))).'" method="post">'.
                    '<h2 class="TextAlignCenter">Новые Данные</h2>'.
                    '<div class="GroupFrame">'.
                        '<div>'.
                            '<label class="FloatLeft Margin5Px" '.
                                    'for="_new_items_node__family_name" >'.
                                'Фамилия: '.
                            '</label>'.
                            '<input class="FloatRight Margin5Px Width300Px" '.
                                'type="text" '.
                                'name="family_name" '.
                                'id="_new_items_node__family_name" '.
                                'value="'.htmlspecialchars($this->_new_items_node__family_name).'" />'.
                            '<div class="ClearBoth"></div>'.
                        '</div>'.
                        '<div>'.
                            '<label class="FloatLeft Margin5Px" '.
                                    'for="_new_items_node__given_name" >'.
                                'Имя: '.
                            '</label>'.
                            '<input class="FloatRight Margin5Px Width300Px" '.
                                'type="text" '.
                                'name="given_name" '.
                                'id="_new_items_node__given_name" '.
                                'value="'.htmlspecialchars($this->_new_items_node__given_name).'" />'.
                            '<div class="ClearBoth"></div>'.
                        '</div>'.
                        '<div>'.
                            '<label class="FloatLeft Margin5Px" '.
                                    'for="_new_items_node__patronymic_name" >'.
                                'Отчество: '.
                            '</label>'.
                            '<input class="FloatRight Margin5Px Width300Px" '.
                                'type="text" '.
                                'name="patronymic_name" '.
                                'id="_new_items_node__patronymic_name" '.
                                'value="'.htmlspecialchars($this->_new_items_node__patronymic_name).'" />'.
                            '<div class="ClearBoth"></div>'.
                        '</div>'.
                    '</div>'.
                    '<div class="GroupFrame">'.
                        '<div>'.
                            '<label class="FloatLeft Margin5Px" '.
                                    'for="_new_items_node__birthday" >'.
                                'Дата рождения: <br />'.
                                '<span class="FontSize07Em">'.
                                    '(Например: 27.09.1983) '.
                                '</span>'.
                            '</label>'.
                            '<input class="FloatRight Margin5Px Width300Px" '.
                                'type="text" '.
                                'name="birthday" '.
                                'id="_new_items_node__birthday" '.
                                'value="'.htmlspecialchars($this->_new_items_node__birthday).'" />'.
                            '<div class="ClearBoth"></div>'.
                        '</div>'.
                        '<div>'.
                            '<label class="FloatLeft Margin5Px" '.
                                    'for="_new_items_node__sex" >'.
                                'Пол: '.
                            '</label>'.
                            '<select class="FloatRight Margin5Px Width300Px" '.
                                    'name="sex" '.
                                    'id="_new_items_node__sex">'.
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
                                '<option value="Мужской">Мужской</option>'.
                                '<option value="Женский">Женский</option>'.
                            '</select>'.
                            '<div class="ClearBoth"></div>'.
                        '</div>'.
                    '</div>'.
                    '<div class="GroupFrame">'.
                        '<div>'.
                            '<label class="FloatLeft Margin5Px" '.
                                    'for="_new_items_node__passport_ser" >'.
                                'Серия паспорта: '.
                            '</label>'.
                            '<input class="FloatRight Margin5Px Width300Px" '.
                                'type="text" '.
                                'name="passport_ser" '.
                                'id="_new_items_node__passport_ser" '.
                                'value="'.htmlspecialchars($this->_new_items_node__passport_ser).'" />'.
                            '<div class="ClearBoth"></div>'.
                        '</div>'.
                        '<div>'.
                            '<label class="FloatLeft Margin5Px" '.
                                    'for="_new_items_node__passport_no" >'.
                                'Номер паспорта: '.
                            '</label>'.
                            '<input class="FloatRight Margin5Px Width300Px" '.
                                'type="text" '.
                                'name="passport_no" '.
                                'id="_new_items_node__passport_no" '.
                                'value="'.htmlspecialchars($this->_new_items_node__passport_no).'" />'.
                            '<div class="ClearBoth"></div>'.
                        '</div>'.
                        '<div>'.
                            '<label class="FloatLeft Margin5Px" '.
                                    'for="_new_items_node__passport_dep" >'.
                                'Кем выдан паспорт: '.
                            '</label>'.
                            '<input class="FloatRight Margin5Px Width300Px" '.
                                'type="text" '.
                                'name="passport_dep" '.
                                'id="_new_items_node__passport_dep" '.
                                'value="'.htmlspecialchars($this->_new_items_node__passport_dep).'" />'.
                            '<div class="ClearBoth"></div>'.
                        '</div>'.
                        '<div>'.
                            '<label class="FloatLeft Margin5Px" '.
                                    'for="_new_items_node__passport_day" >'.
                                'Дата выдачи паспорта: <br />'.
                                '<span class="FontSize07Em">'.
                                    '(Например: 27.09.2003) '.
                                '</span>'.
                            '</label>'.
                            '<input class="FloatRight Margin5Px Width300Px" '.
                                'type="text" '.
                                'name="passport_day" '.
                                'id="_new_items_node__passport_day" '.
                                'value="'.htmlspecialchars($this->_new_items_node__passport_day).'" />'.
                            '<div class="ClearBoth"></div>'.
                        '</div>'.
                        '<div>'.
                            '<label class="FloatLeft Margin5Px" '.
                                    'for="_new_items_node__residence_city" >'.
                                'Город: '.
                            '</label>'.
                            '<input class="FloatRight Margin5Px Width300Px" '.
                                'type="text" '.
                                'name="residence_city" '.
                                'id="_new_items_node__residence_city" '.
                                'value="'.htmlspecialchars($this->_new_items_node__residence_city).'" />'.
                            '<div class="ClearBoth"></div>'.
                        '</div>'.
                        '<div>'.
                            '<label class="FloatLeft Margin5Px"'.
                                    ' for="_new_items_node__residence" >'.
                                'Адрес: <br />'.
                                '<span class="FontSize07Em">'.
                                    '(Например: Королева 181б-354) '.
                                '</span>'.
                            '</label>'.
                            '<textarea class="FloatRight Margin5Px Width300Px"'.
                                    'rows="4" '.
                                    'name="residence" '.
                                    'id="_new_items_node__residence">'.
                                htmlspecialchars($this->_new_items_node__residence).
                            '</textarea>'.
                            '<div class="ClearBoth"></div>'.
                        '</div>'.
                    '</div>'.
                    '<div class="GroupFrame">'.
                        '<div>'.
                            '<label class="FloatLeft Margin5Px" '.
                                    'for="_new_items_node__phone" >'.
                                'Телефон: <br />'.
                                '<span class="FontSize07Em">'.
                                    '(Например: <br />'.
                                    '+78412123456, или 88412123456, или 123456) '.
                                '</span>'.
                            '</label>'.
                            '<input class="FloatRight Margin5Px Width300Px" '.
                                'type="text" '.
                                'name="phone" '.
                                'id="_new_items_node__phone" '.
                                'value="'.htmlspecialchars($this->_new_items_node__phone).'" />'.
                            '<div class="ClearBoth"></div>'.
                        '</div>'.
                        '<div>'.
                            '<label class="FloatLeft Margin5Px" '.
                                    'for="_new_items_node__phone2" >'.
                                'Дополнительный Телефон: <br />'.
                                '<span class="FontSize07Em">'.
                                    '(Например: <br />'.
                                    '+78412123456, или 88412123456, или 123456) '.
                                '</span>'.
                            '</label>'.
                            '<input class="FloatRight Margin5Px Width300Px" '.
                                'type="text" '.
                                'name="phone2" '.
                                'id="_new_items_node__phone2" '.
                                'value="'.htmlspecialchars($this->_new_items_node__phone2).'" />'.
                            '<div class="ClearBoth"></div>'.
                        '</div>'.
                        '<div>'.
                            '<label class="FloatLeft Margin5Px"'.
                                    ' for="_new_items_node__about" >'.
                                'Дополнительное описание: <br />'.
                                '<span class="FontSize07Em">'.
                                    '(Произвольная информация) '.
                                '</span>'.
                            '</label>'.
                            '<textarea class="FloatRight Margin5Px Width300Px"'.
                                    'rows="6" '.
                                    'name="about" '.
                                    'id="_new_items_node__about">'.
                                htmlspecialchars($this->_new_items_node__about).
                            '</textarea>'.
                            '<div class="ClearBoth"></div>'.
                        '</div>'.
                        '<div>'.
                            '<label class="FloatLeft Margin5Px"'.
                                    ' for="_new_items_node__comments" >'.
                                'Примечание: <br />'.
                                '<span class="FontSize07Em">'.
                                    '(Произвольная информация. <br />'.
                                    'Например, причина попадания в список) '.
                                '</span>'.
                            '</label>'.
                            '<textarea class="FloatRight Margin5Px Width300Px"'.
                                    'rows="10" '.
                                    'name="comments" '.
                                    'id="_new_items_node__comments">'.
                                htmlspecialchars($this->_new_items_node__comments).
                            '</textarea>'.
                            '<div class="ClearBoth"></div>'.
                        '</div>'.
                    '</div>'.
                    '<div>'.
                        '<input type="hidden" '.
                            'name="post_token" '.
                            'value="'.htmlspecialchars($_SESSION['post_token']).'" />'.
                        '<input class="FloatLeft Margin5Px" type="submit" value="Создать" />'.
                        '<input class="FloatLeft Margin5Px" type="reset" value="Сброс" />'.
                        '<div class="ClearBoth"></div>'.
                    '</div>'.
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

