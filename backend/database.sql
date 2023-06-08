create database law;

create table user(
    firstname varchar(255),
    lastname varchar(255),
    username varchar(255),
    email varchar(255),
    password varchar(255),
    int id not null auto_increment,
    primary key(id)
);
