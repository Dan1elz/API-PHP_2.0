CREATE DATABASE bd_api_teste_2;

USE bd_api_teste_2;

CREATE TABLE tb_user(
	id_user INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    name_user VARCHAR(20) NOT NULL,
    lastname_user VARCHAR(20) NOT NULL,
    email_user VARCHAR(100) NOT NULL,
	password_user VARCHAR(32) NOT NULL
);

CREATE TABLE tokens (
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    user_id INT,
    token VARCHAR(455) UNIQUE,
    expiration_time TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
