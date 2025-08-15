/*
SQLyog Community v13.3.0 (64 bit)
MySQL - 10.4.32-MariaDB : Database - proyectosolo
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`proyectosolo` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `proyectosolo`;

/*Table structure for table `t_departamento` */

DROP TABLE IF EXISTS `t_departamento`;

CREATE TABLE `t_departamento` (
  `id_departamento` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_departamento` varchar(50) DEFAULT NULL,
  `sub_departamento` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_departamento`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `t_departamento` */

insert  into `t_departamento`(`id_departamento`,`nombre_departamento`,`sub_departamento`) values 
(7,'Vendedor ',NULL),
(8,'Ayudante',NULL),
(9,'Cliente',NULL),
(10,'Supervisor',NULL);

/*Table structure for table `t_especificaciones` */

DROP TABLE IF EXISTS `t_especificaciones`;

CREATE TABLE `t_especificaciones` (
  `id_marca` int(30) NOT NULL AUTO_INCREMENT,
  `marca` varchar(40) NOT NULL,
  `modelo` varchar(30) NOT NULL,
  `Descripcion` text DEFAULT NULL,
  `imagen` longblob DEFAULT NULL,
  PRIMARY KEY (`id_marca`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `t_especificaciones` */

insert  into `t_especificaciones`(`id_marca`,`marca`,`modelo`,`Descripcion`,`imagen`) values 
(19,'Asus','DDR4',NULL,NULL),
(20,'Samsung','DDR5',NULL,NULL),
(21,'Samsung','M.2 1TB',NULL,NULL),
(22,'HP','intel i7 13500HX',NULL,NULL),
(23,'Samsung','M.2',NULL,NULL),
(24,'Dell','G15',NULL,NULL),
(25,'Kinstong','DDR5',NULL,NULL);

/*Table structure for table `t_garantia` */

DROP TABLE IF EXISTS `t_garantia`;

CREATE TABLE `t_garantia` (
  `Id_Garantia` bigint(11) NOT NULL AUTO_INCREMENT,
  `Tiempo` varchar(40) NOT NULL,
  PRIMARY KEY (`Id_Garantia`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `t_garantia` */

insert  into `t_garantia`(`Id_Garantia`,`Tiempo`) values 
(15,'1 mes'),
(17,'1 año'),
(18,'6 meses');

/*Table structure for table `t_productos` */

DROP TABLE IF EXISTS `t_productos`;

CREATE TABLE `t_productos` (
  `Id_producto` int(15) NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `categoria` text NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `estado` varchar(15) NOT NULL,
  `Inventario_Minimo` int(50) NOT NULL,
  `Precio_Compra` float NOT NULL,
  `id_marca` int(40) NOT NULL,
  `Id_Garantia` bigint(11) DEFAULT NULL,
  `Regalia` tinyint(1) DEFAULT NULL,
  `Id_Cliente` int(15) DEFAULT NULL,
  `Tipo_Almacenamiento` varchar(20) NOT NULL,
  `ISV` float DEFAULT NULL,
  `ISV_Turismo` float DEFAULT NULL,
  `Descuento` float NOT NULL,
  `Precio_Venta1` float DEFAULT NULL,
  `Precio_Venta2` float DEFAULT NULL,
  `Precio_Venta3` float DEFAULT NULL,
  `Precio_Venta4` float DEFAULT NULL,
  `Precio_Venta5` float DEFAULT NULL,
  PRIMARY KEY (`Id_producto`),
  KEY `fk_id_marca` (`id_marca`),
  KEY `fk_id_garantia` (`Id_Garantia`),
  CONSTRAINT `fk_id_garantia` FOREIGN KEY (`Id_Garantia`) REFERENCES `t_garantia` (`Id_Garantia`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_id_marca` FOREIGN KEY (`id_marca`) REFERENCES `t_especificaciones` (`id_marca`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `t_productos` */

insert  into `t_productos`(`Id_producto`,`fecha`,`nombre`,`categoria`,`codigo`,`estado`,`Inventario_Minimo`,`Precio_Compra`,`id_marca`,`Id_Garantia`,`Regalia`,`Id_Cliente`,`Tipo_Almacenamiento`,`ISV`,`ISV_Turismo`,`Descuento`,`Precio_Venta1`,`Precio_Venta2`,`Precio_Venta3`,`Precio_Venta4`,`Precio_Venta5`) values 
(53,'2025-06-04','Disco Solido','Pieza PC','008091','Nuevo',3,4000,23,15,NULL,NULL,'Vitrina',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),
(54,'2025-06-24','Procesador','Pieza PC','008092','Nuevo',6,5000,22,17,NULL,NULL,'Bodega',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),
(55,'2025-06-25','Computadora','PC Escritorio Gaming','008093','Nuevo',1,20000,24,18,NULL,NULL,'Vitrina',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),
(56,'2025-07-27','Ram','Pieza PC','008094','Nuevo',5,4000,25,15,NULL,NULL,'Vitrina',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL);

/*Table structure for table `t_rol` */

DROP TABLE IF EXISTS `t_rol`;

CREATE TABLE `t_rol` (
  `id_rol` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_rol` varchar(50) DEFAULT NULL,
  `privilegio` varchar(50) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  PRIMARY KEY (`id_rol`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `t_rol` */

insert  into `t_rol`(`id_rol`,`nombre_rol`,`privilegio`,`descripcion`) values 
(11,'Gerente',NULL,NULL),
(12,'Lider',NULL,NULL);

/*Table structure for table `t_usuario` */

DROP TABLE IF EXISTS `t_usuario`;

CREATE TABLE `t_usuario` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `fecha_registro` date DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `DNI` bigint(15) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `usuario` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `correo` varchar(50) DEFAULT NULL,
  `telefono` int(11) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `genero` varchar(10) DEFAULT NULL,
  `id_rol` int(11) DEFAULT NULL,
  `id_departamento` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_usuario`),
  KEY `fk_usuario_rol` (`id_rol`),
  KEY `fk_us_departamento` (`id_departamento`),
  CONSTRAINT `fk_usuario_departamento` FOREIGN KEY (`id_departamento`) REFERENCES `t_departamento` (`id_departamento`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_usuario_rol` FOREIGN KEY (`id_rol`) REFERENCES `t_rol` (`id_rol`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `t_usuario` */

insert  into `t_usuario`(`id_usuario`,`fecha_registro`,`fecha_nacimiento`,`DNI`,`nombre`,`usuario`,`password`,`correo`,`telefono`,`direccion`,`genero`,`id_rol`,`id_departamento`) values 
(22,'2025-07-12','2025-07-11',1601200600206,'Darwin Fabricio Guzman Pineda','Darwin','$2y$10$KbzRA7h9Wv0Gb/nI4.pyd.z6.2tWHwy9cH6o5EQjSa1SdxOL1JaXu','darwin.guzmap06@gmail.com',33932760,NULL,'Masculino',12,10),
(32,'2025-07-12','2006-01-20',1601200600409,'Dereeck Baruc Lainez Castellan','Dereeck','$2y$10$/5qGILKYT89YqyJ6.RDcdeQNMDCcirLEwt3vIqfBt1WDQSyYCzSei','dereeck.lainez@gmail.com',11223344,NULL,'Masculino',12,7);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
