CREATE DATABASE IF NOT EXISTS innoscripta_news;
CREATE USER 'innoscripta_news'@'localhost' IDENTIFIED BY 'inn05cr1pta';
GRANT ALL ON innoscripta_news.* TO 'innoscripta_news'@'%';

SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));
/* Make sure the privileges are installed */
FLUSH PRIVILEGES;

USE innoscripta_news;
