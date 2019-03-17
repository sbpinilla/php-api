
CREATE DATABASE IF NOT EXISTS CURSO_ANGULAR;
USE CURSO_ANGULAR;

CREATE TABLE productos (
    id          int(255) auto_increment not null,
    nombre      varchar(255) not null,
    descripcion text,
    precio      varchar(255),
    imagen      varchar (255),
    CONSTRAINT pk_producto PRIMARY KEY(id|) 
)ENGINE=InnoDb;

