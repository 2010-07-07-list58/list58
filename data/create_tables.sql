# data/create_tables.sql
#
# команда для смены кодировки:
#     charset utf8
#


# базовый список пользователей:
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


# список активных сессий:
CREATE TABLE IF NOT EXISTS `user_sessions` (
    `login` VARCHAR(100),
    `session` VARCHAR(100),
    PRIMARY KEY(`login`, `session`),
    KEY `user_sessions(login)` (`login`),
    KEY `user_sessions(session)` (`session`)
);


# список групп (полномочий) пользователей:
CREATE TABLE IF NOT EXISTS `user_groups` (
    `login` VARCHAR(100),
    `group` VARCHAR(100),
    PRIMARY KEY (`login`, `group`),
    KEY `user_groups(login)` (`login`),
    KEY `user_groups(group)` (`group`)
);

############################################################
--                                                        --
-- пояснение к группам полномочий                         --
--                                                        --
--   группа "search_items" -- доступ к функции поиска людей
--                                                        --
--   группа "new_items -- доступ к возможности добавления людей
--                                                        --
--   группа "mod_items" -- возможность редактировать      --
--                        чужие данные                    --
--                                                        --
--   группа "adm" -- административные полномочия:         --
--                   редактирование участников системы    --
--                   и прочие полномочия                  --
--                                                        --
############################################################


# список ограничений по ip:
CREATE TABLE IF NOT EXISTS `user_ips` (
    `login` VARCHAR(100),
    `ip` VARCHAR(100),
    PRIMARY KEY (`login`, `ip`),
    KEY `user_groups(login)` (`login`),
    KEY `user_groups(ip)` (`ip`)
);



