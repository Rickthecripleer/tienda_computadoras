drop database if exists tienda_computadoras;
create database tienda_computadoras;
use tienda_computadoras;

create table roles(
    id int not null primary key auto_increment,
    nombre varchar(255) not null
);

create table usuarios(
    id int not null primary key auto_increment,
    nombre varchar(255) not null,
    email varchar(255) not null,
    password varchar(255) not null,
    rol_id int not null,
    fecha_creacion datetime not null default current_timestamp(),
    foreign key (rol_id) references roles(id)
    on delete restrict on update cascade
);

CREATE TABLE productos (
    id int NOT NULL PRIMARY KEY AUTO_INCREMENT,
    nombre varchar(255) NOT NULL,
    descripcion text NOT NULL,
    precio decimal(10,2) NOT NULL,
    imagen varchar(255) NOT NULL,
    usuario_id int NOT NULL,
    stock int NOT NULL DEFAULT 0,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
);

create table pedidos(
    id int not null primary key auto_increment,
    usuario_id int not null,
    fecha_creacion datetime not null default current_timestamp(),
    foreign key (usuario_id) references usuarios(id)
    on delete restrict on update cascade
);

create table pedidos_productos(
    pedido_id int not null,
    producto_id int not null,
    cantidad int not null,
    primary key (pedido_id, producto_id),
    foreign key (pedido_id) references pedidos(id),
    foreign key (producto_id) references productos(id)
);

insert into roles values (1,'Administrador');
insert into roles values (2,'Empleado');
insert into roles values (3,'Cliente');


--para actualizar el rol a administrador--
UPDATE usuarios
SET rol_id = 1
WHERE id = 1;