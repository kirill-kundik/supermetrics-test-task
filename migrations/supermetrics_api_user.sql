CREATE DATABASE IF NOT EXISTS test_db COLLATE utf8_general_ci;

CREATE USER 'test_user'@'localhost' IDENTIFIED BY 'test_pass';
GRANT CREATE, ALTER, INDEX, LOCK TABLES, REFERENCES, UPDATE, DELETE, DROP, SELECT, INSERT ON `test_db`.* TO 'test_user';

FLUSH PRIVILEGES;

USE test_db;

-- DROP TABLE IF EXISTS supermetrics_api_user; -- If you want a clean run

CREATE TABLE IF NOT EXISTS supermetrics_api_user
(
	id int auto_increment
		primary key,
	email varchar(1024) not null,
	name varchar(1024) not null,
	client_id varchar(1024) not null,
	sl_token varchar(1024) null,
	token_expired_at datetime null,
	constraint supermetrics_api_user_email_uindex
		unique (email)
);