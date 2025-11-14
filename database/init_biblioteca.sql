-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 13-11-2025 a las 18:43:00
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `biblioteca_colaborativa`
--
CREATE DATABASE IF NOT EXISTS `biblioteca_colaborativa` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `biblioteca_colaborativa`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `apuntes`
--

CREATE TABLE `apuntes` (
  `idApuntes` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `ruta_archivo` varchar(255) NOT NULL,
  `idUsuario` int(11) NOT NULL,
  `fecha_subida` date NOT NULL,
  `descripcion` text NOT NULL,
  `semestre` int(11) NOT NULL,
  `universidad` text NOT NULL,
  `carrera` text NOT NULL,
  `etiquetas` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `apuntes_materias`
--

CREATE TABLE `apuntes_materias` (
  `idApuntes_materias` int(11) NOT NULL,
  `idmaterias` int(11) NOT NULL,
  `idApuntes` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comentarios`
--

CREATE TABLE `comentarios` (
  `idcomentarios` int(11) NOT NULL,
  `contenido` text NOT NULL,
  `fecha_comentario` date NOT NULL,
  `idUsuario` int(11) NOT NULL,
  `idApunte` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grados_academicos`
--

CREATE TABLE `grados_academicos` (
  `idGrados_Academicos` int(11) NOT NULL,
  `titulo` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `grados_academicos`
--

INSERT INTO `grados_academicos` (`idGrados_Academicos`, `titulo`) VALUES
(1, 'Bachillerato'),
(2, 'Licenciatura'),
(3, 'Maestría'),
(4, 'Doctorado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materias`
--

CREATE TABLE `materias` (
  `idmaterias` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `puntuaciones`
--

CREATE TABLE `puntuaciones` (
  `idPuntuaciones` int(11) NOT NULL,
  `puntuacion` int(11) NOT NULL,
  `idUsuario` int(11) NOT NULL,
  `idApunte` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `idUsuario` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido_paterno` varchar(100) NOT NULL,
  `apellido_materno` varchar(100) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `escuela` varchar(255) NOT NULL,
  `idGrado_Academico` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios_credenciales`
--

CREATE TABLE `usuarios_credenciales` (
  `Usuario_idUsuario` int(11) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `contrasena` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `apuntes`
--
ALTER TABLE `apuntes`
  ADD PRIMARY KEY (`idApuntes`),
  ADD KEY `fk_Apuntes_Usuarios1_idx` (`idUsuario`);

--
-- Indices de la tabla `apuntes_materias`
--
ALTER TABLE `apuntes_materias`
  ADD PRIMARY KEY (`idApuntes_materias`),
  ADD KEY `fk_Apuntes_materias_materias1_idx` (`idmaterias`),
  ADD KEY `fk_Apuntes_materias_Apuntes1_idx` (`idApuntes`);

--
-- Indices de la tabla `comentarios`
--
ALTER TABLE `comentarios`
  ADD PRIMARY KEY (`idcomentarios`),
  ADD KEY `fk_comentarios_Usuarios1_idx` (`idUsuario`),
  ADD KEY `fk_comentarios_Apuntes1_idx` (`idApunte`);

--
-- Indices de la tabla `grados_academicos`
--
ALTER TABLE `grados_academicos`
  ADD PRIMARY KEY (`idGrados_Academicos`);

--
-- Indices de la tabla `materias`
--
ALTER TABLE `materias`
  ADD PRIMARY KEY (`idmaterias`);

--
-- Indices de la tabla `puntuaciones`
--
ALTER TABLE `puntuaciones`
  ADD PRIMARY KEY (`idPuntuaciones`),
  ADD KEY `fk_Puntuaciones_Usuarios1_idx` (`idUsuario`),
  ADD KEY `fk_Puntuaciones_Apuntes1_idx` (`idApunte`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`idUsuario`),
  ADD KEY `fk_Usuarios_Grados_Academicos1_idx` (`idGrado_Academico`);

--
-- Indices de la tabla `usuarios_credenciales`
--
ALTER TABLE `usuarios_credenciales`
  ADD PRIMARY KEY (`Usuario_idUsuario`),
  ADD KEY `fk_Usuario_Credenciales_Usuario_idx` (`Usuario_idUsuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `apuntes`
--
ALTER TABLE `apuntes`
  MODIFY `idApuntes` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `apuntes_materias`
--
ALTER TABLE `apuntes_materias`
  MODIFY `idApuntes_materias` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `comentarios`
--
ALTER TABLE `comentarios`
  MODIFY `idcomentarios` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `grados_academicos`
--
ALTER TABLE `grados_academicos`
  MODIFY `idGrados_Academicos` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `materias`
--
ALTER TABLE `materias`
  MODIFY `idmaterias` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `puntuaciones`
--
ALTER TABLE `puntuaciones`
  MODIFY `idPuntuaciones` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `idUsuario` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `apuntes`
--
ALTER TABLE `apuntes`
  ADD CONSTRAINT `fk_Apuntes_Usuarios1` FOREIGN KEY (`idUsuario`) REFERENCES `usuarios` (`idUsuario`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `apuntes_materias`
--
ALTER TABLE `apuntes_materias`
  ADD CONSTRAINT `fk_Apuntes_materias_Apuntes1` FOREIGN KEY (`idApuntes`) REFERENCES `apuntes` (`idApuntes`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Apuntes_materias_materias1` FOREIGN KEY (`idmaterias`) REFERENCES `materias` (`idmaterias`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `comentarios`
--
ALTER TABLE `comentarios`
  ADD CONSTRAINT `fk_comentarios_Apuntes1` FOREIGN KEY (`idApunte`) REFERENCES `apuntes` (`idApuntes`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_comentarios_Usuarios1` FOREIGN KEY (`idUsuario`) REFERENCES `usuarios` (`idUsuario`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `puntuaciones`
--
ALTER TABLE `puntuaciones`
  ADD CONSTRAINT `fk_Puntuaciones_Apuntes1` FOREIGN KEY (`idApunte`) REFERENCES `apuntes` (`idApuntes`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Puntuaciones_Usuarios1` FOREIGN KEY (`idUsuario`) REFERENCES `usuarios` (`idUsuario`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_Usuarios_Grados_Academicos1` FOREIGN KEY (`idGrado_Academico`) REFERENCES `grados_academicos` (`idGrados_Academicos`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `usuarios_credenciales`
--
ALTER TABLE `usuarios_credenciales`
  ADD CONSTRAINT `fk_Usuario_Credenciales_Usuario` FOREIGN KEY (`Usuario_idUsuario`) REFERENCES `usuarios` (`idUsuario`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
