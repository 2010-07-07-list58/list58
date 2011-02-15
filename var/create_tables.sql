-- data/create_tables.sql
--
-- команда для смены кодировки:
--     charset utf8
--

-- базовый список пользователей:
CREATE TABLE IF NOT EXISTS `users_base` (
    `login` VARCHAR(100),
    `password` VARCHAR(255),
    `mail` VARCHAR(255),
    `reg_date` bigint,
    `name` VARCHAR(255),
    `lastname` VARCHAR(255),
    `org` VARCHAR(255),
    `comments` TEXT,
    PRIMARY KEY (`login`),
    KEY `users_base(password)` (`password`),
    KEY `users_base(mail)` (`mail`),
    KEY `users_base(reg_date)` (`reg_date`),
    KEY `users_base(name)` (`name`),
    KEY `users_base(lastname)` (`lastname`),
    KEY `users_base(org)` (`org`),
    KEY `users_base(comments)` (`comments`(1000))
);

-- список активных сессий:
CREATE TABLE IF NOT EXISTS `user_sessions` (
    `login` VARCHAR(100),
    `session` VARCHAR(100),
    `login_time` BIGINT,
    `login_ip` VARCHAR(100),
    `login_browser` TEXT,
    `last_time` BIGINT,
    `last_ip` VARCHAR(100),
    `last_browser` TEXT,
    `last_query` TEXT,
    PRIMARY KEY(`login`, `session`),
    KEY `user_sessions(login)` (`login`),
    KEY `user_sessions(session)` (`session`),
    KEY `user_sessions(login_time)` (`login_time`),
    KEY `user_sessions(login_ip)` (`login_ip`),
    KEY `user_sessions(login_browser)` (`login_browser`(1000)),
    KEY `user_sessions(last_time)` (`last_time`),
    KEY `user_sessions(last_ip)` (`last_ip`),
    KEY `user_sessions(last_browser)` (`last_browser`(1000)),
    KEY `user_sessions(last_query)` (`last_query`(1000))
);

-- список ограничений по ip:
CREATE TABLE IF NOT EXISTS `user_ips` (
    `id` BIGINT NOT NULL AUTO_INCREMENT,
    `login` VARCHAR(100),
    `ip` VARCHAR(100),
    `time` BIGINT,
    PRIMARY KEY (`id`),
    KEY `user_ips(login)` (`login`),
    KEY `user_ips(ip)` (`ip`),
    KEY `user_ips(time)` (`time`)
);

-- список групп (полномочий) пользователей:
CREATE TABLE IF NOT EXISTS `user_groups` (
    `login` VARCHAR(100),
    `group` VARCHAR(100),
    PRIMARY KEY (`login`, `group`),
    KEY `user_groups(login)` (`login`),
    KEY `user_groups(group)` (`group`)
);

-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--
-- пояснение к группам полномочий
--
--   группа 'view_items':
--          'view_items': доступ к функции просмотра людей
--
--   группа 'search_items':
--          'view_items'+'search_items':
--              доступ к функции поиска людей
--
--   группа 'new_items':
--          'view_items'+'new_items':
--                  доступ к возможности добавления новых людей
--
--   группа 'mod_items':
--          'view_items'+'mod_items':
--              возможность редактировать данные о людях
--              (как минимум данные, вдалец которых)
--
--  группа 'mod_other_items':
--          'view_items'+'mod_items'+'mod_other_items':
--              возможность редактировать чужие данные о людях
--
--  группа 'adm':
--          административные полномочия.
--          редактирование участников системы
--
--  группа 'multisession':
--          'multisession': разрешает использовать одновременно несколько сессий
--
--  группа 'ip_limit':
--          'ip_limit': запрещает доступ к системе от посторонних ip-адресов
--
--  группа 'auto_ip_limit':
--          'ip_limit'+'auto_ip_limit': автоматически корректирует ip-адрес
--              из которого разрешается использовать систему
--
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

-- база данных людей
CREATE TABLE IF NOT EXISTS `items_base` (
    `id` BIGINT NOT NULL AUTO_INCREMENT,
    `item_owner` VARCHAR(100), -- владелец-создатель записи о человеке
    `item_created` BIGINT, -- время создения записи
    `item_modified` BIGINT, -- время последнего изменения записи
    `item_deleted` INTEGER, -- '1' если запись удалена
    `given_name` VARCHAR(255),
    `family_name` VARCHAR(255),
    `patronymic_name` VARCHAR(255),
    `birth_year` INTEGER,
    `birth_month` INTEGER,
    `birth_day` INTEGER,
    `sex` INTEGER, -- 0: <None> -- 1: Male -- 2: Female
    `passport_ser` VARCHAR(255),
    `passport_no` VARCHAR(255),
    `passport_dep` VARCHAR(255),
    `passport_day` VARCHAR(255),
    `residence_city` VARCHAR(255),
    `residence` VARCHAR(255),
    `phone` VARCHAR(255),
    `phone2` VARCHAR(255),
    `about` TEXT,
    `comments` TEXT,
    PRIMARY KEY (`id`),
    KEY `items_base(item_owner)` (`item_owner`),
    KEY `items_base(item_created)` (`item_created`),
    KEY `items_base(item_modified)` (`item_modified`),
    KEY `items_base(item_deleted)` (`item_deleted`),
    KEY `items_base(given_name)` (`given_name`),
    KEY `items_base(family_name)` (`family_name`),
    KEY `items_base(patronymic_name)` (`patronymic_name`),
    KEY `items_base(birth_year)` (`birth_year`),
    KEY `items_base(birth_month)` (`birth_month`),
    KEY `items_base(birth_day)` (`birth_day`),
    KEY `items_base(sex)` (`sex`),
    KEY `items_base(passport_ser)` (`passport_ser`),
    KEY `items_base(passport_no)` (`passport_no`),
    KEY `items_base(passport_dep)` (`passport_dep`),
    KEY `items_base(passport_day)` (`passport_day`),
    KEY `items_base(residence_city)` (`residence_city`),
    KEY `items_base(residence)` (`residence`),
    KEY `items_base(phone)` (`phone`),
    KEY `items_base(phone2)` (`phone2`),
    KEY `users_base(about)` (`about`(1000)),
    KEY `users_base(comments)` (`comments`(1000))
);

