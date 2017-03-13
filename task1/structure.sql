-- Таблица Категорий
CREATE TABLE `category` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT, 
  `name` varchar(100) NOT NULL, -- Имя категории
  `enabled` varchar(1) NOT NULL DEFAULT 'Y', -- Флаг активности категории для soft удаления
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

-- Таблица с лентой новостей
CREATE TABLE `feed` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL, -- заголовок ленты
  `content` varchar(243) NOT NULL, -- содержимое ленты
  `enabled` varchar(1) NOT NULL DEFAULT 'Y', -- Флаг активности новости для soft удаления
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

-- Таблица для связи между лентой и категорией, у ленты может быть несколько категорий
CREATE TABLE `feed_category` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(11) unsigned NOT NULL,
  `feed_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_category_feed` (`category_id`,`feed_id`), -- обеспечение уникальность сочитания новость - категорий
  KEY `idx_feed_id` (`feed_id`), -- индекс для Join и условия в Select
  KEY `idx_category_id` (`category_id`), -- индекс для Join и условия в Select
  CONSTRAINT `feed_category_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE, -- внешний ключ для hard удаления
  CONSTRAINT `feed_category_ibfk_2` FOREIGN KEY (`feed_id`) REFERENCES `feed` (`id`) ON DELETE CASCADE ON UPDATE CASCADE -- внешний ключ для hard удаления
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

-- таблица "лайков" на новости
CREATE TABLE `feed_user_like` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL, -- id пользователя
  `feed_id` int(11) unsigned NOT NULL, -- id новости
  `islike` tinyint(1) DEFAULT NULL COMMENT '1- like, 2-unlike, null - no user desicion', -- флаг наличия "лайка"
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`feed_id`), -- обеспечение уникальность сочитания пользователь  - ность 
  KEY `idx_user_id` (`user_id`), -- индекс для Join и условия в Select 
  KEY `idx_feed_id` (`feed_id`), -- индекс для Join и условия в Select
  CONSTRAINT `feed_user_like_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE, -- внешний ключ для hard удаления
  CONSTRAINT `feed_user_like_ibfk_2` FOREIGN KEY (`feed_id`) REFERENCES `feed` (`id`) ON DELETE CASCADE ON UPDATE CASCADE -- внешний ключ для hard удаления
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

-- Запросы

-- Запрос выводит список оценивших пост пользователей.
Select 
  `user_id`,
  `feed_id`,
  `users`.`name`,
  CASE `feed_user_like`.`islike` WHEN 1 THEN "LIKE" 
  WHEN 2 THEN "NOT LIKE" ELSE "" END islike
From `feed_user_like`
Inner Join `users` on `users`.`id` = `feed_user_like`.`user_id`
Where 
  `feed_id` IN (1,2) And 
  IFNULL(`feed_user_like`.`islike`,0) > 0

-- Лента должна иметь фильтр по категориям. 
Select Distinct
  `feed`.`id`,
  `feed`.`name`,
  `feed`.`content`
From `feed`
Inner Join `feed_category` On `feed_category`.`feed_id` = `feed`.`id`
Inner Join `category` On `category`.`id` = `feed_category`.`category_id`
Where  `category`.`id` IN (1,3) -- Фильтр по категориям
And `category`.`enabled` = 'Y' -- only actived categories

-- like (islike = 1 = LIKE)
Insert Into `feed_user_like` (`user_id`,`feed_id`,`islike`) Values(3,1,1)
On DUPLICATE KEY Update `islike`=1;

-- unlike (islike = 2 = NOT LIKE)
Insert Into `feed_user_like` (`user_id`,`feed_id`,`islike`) Values(3,1,2)
On DUPLICATE KEY Update `islike`=2;


-- Лента
select  `id`, `name` from `feed`;

-- update content on feed 
update `feed` set `content` = "content" where `id`=1;
-- link feed to category
insert into `feed_category` (`category_id`, `feed_id`) values (1,1);
-- unlink all feeds from specified category
delete from `feed_category` where `category_id` = 1; -- IN (1,2);
-- unlink specified feed(-s) from specified category(-ies)
delete from `feed_category` where `category_id`= 1 and `feed_id` = 1;
delete from `feed_category` where `category_id` IN (1,2) and `feed_id` IN (1,2);
-- unlink specified feed from all category or categories 
delete from `feed_category` where `feed_id` = 1 -- IN (1,2);

-- new category
insert into `category` (`name`) values ("new category name");
-- update category
update `category` set `name` = "updated category name" where `id` = 1;
-- new feed
insert into `feed` (`name`,`content`) values ("new feed name","new feed content");
-- update feed
update `feed` set `name` = "new feed name" where `id` = 1
update `feed` set `content` = "new feed content" where `id` = 1 


-- soft delete feed
update `feed` set `enabled` = 'N'
-- soft delete category
update `category` set `enabled` = 'N'
-- hard delete feed and links by FK
delete from `feed` where `id` = 1;
-- hard delete category and links by FK
delete from `category` where `id` = 1;
