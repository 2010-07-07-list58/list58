# data/create_tables.sql
#
# команда для смены кодировки:
#     charset utf8
#


# базовый список пользователей:
CREATE TABLE IF NOT EXISTS `users_base` (
    `login` varchar(100),
    `password` varchar(255),
    `mail` varchar(255),
    `reg_date` bigint,
    `name` varchar(255),
    `lastname` varchar(255),
    `org` varchar(255),
    `comments` text,
    PRIMARY KEY (`login`),
    KEY `users_base(password)` (`password`),
    KEY `users_base(mail)` (`mail`),
    KEY `users_base(reg_date)` (`reg_date`),
    KEY `users_base(name)` (`name`),
    KEY `users_base(lastname)` (`lastname`),
    KEY `users_base(org)` (`org`),
    KEY `users_base(comments)` (`comments`(1000)));


# список активных сессий:
CREATE TABLE IF NOT EXISTS `user_sessions` (
    `login` varchar(100),
    `session` varchar(100),
    PRIMARY KEY(`login`, `session`),
    KEY `user_sessions(login)` (`login`),
    KEY `user_sessions(session)` (`session`));


# список групп (полномочий) пользователей:
CREATE TABLE IF NOT EXISTS `user_groups` (
    `login` varchar(100),
    `group` varchar(100),
    PRIMARY KEY (`login`, `group`),
    KEY `user_groups(login)` (`login`),
    KEY `user_groups(group)` (`group`));

############################################################
-- пояснение к группам полномочий                         --
--                                                        --
--   группа "search" -- доступ к функции поиска людей     --
--                                                        --
--   группа "add -- доступ к возможности добавления людей --
--                                                        --
--   группа "moderate" -- возможность редактировать       --
--                        чужие данные                    --
--                                                        --
--   группа "adm" -- административные полномочия:         --
--                   редактирование участников системы    --
--                   и прочие полномочия                  --
--                                                        --
--                                                        --
############################################################


# список ограничений по ip:
CREATE TABLE IF NOT EXISTS `user_ips` (
    `login` varchar(100),
    `ip` varchar(100),
    PRIMARY KEY (`login`, `ip`),
    KEY `user_groups(login)` (`login`),
    KEY `user_groups(ip)` (`ip`));



