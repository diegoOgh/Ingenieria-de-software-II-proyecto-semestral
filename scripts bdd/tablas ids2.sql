-- ========================================================
-- 1. CREACIÓN DE LA PRIMERA BASE DE DATOS (GESTIÓN ESCOLAR)
-- ========================================================
CREATE DATABASE IF NOT EXISTS gestion_escolar;
USE gestion_escolar;

-- Tabla de Funcionarios
CREATE TABLE funcionarios (
    id_funcionario INT AUTO_INCREMENT PRIMARY KEY,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

-- Tabla de Alumnos
CREATE TABLE alumnos (
    nro_matricula INT PRIMARY KEY, -- Usamos el número de matrícula como llave primaria
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    curso VARCHAR(50) NOT NULL
) ENGINE=InnoDB;


-- ========================================================
-- 2. CREACIÓN DE LA SEGUNDA BASE DE DATOS (CONFLICTOS)
-- ========================================================
CREATE DATABASE IF NOT EXISTS gestion_conflictos;
USE gestion_conflictos;

-- Tabla Principal de Conflictos
CREATE TABLE casos_conflicto (
    id_conflicto INT AUTO_INCREMENT PRIMARY KEY,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    descripcion TEXT NOT NULL,
    estado_caso ENUM('abierto', 'en proceso', 'cerrado') DEFAULT 'abierto',
    id_funcionario_cargo INT NOT NULL,
    
    -- Llave foránea apuntando a la otra Base de Datos (gestion_escolar)
    FOREIGN KEY (id_funcionario_cargo) 
        REFERENCES gestion_escolar.funcionarios(id_funcionario)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Tabla Intermedia para relacionar Alumnos y Conflictos (Muchos a Muchos)
CREATE TABLE detalle_alumnos_conflicto (
    id_conflicto INT NOT NULL,
    nro_matricula_alumno INT NOT NULL,
    PRIMARY KEY (id_conflicto, nro_matricula_alumno),
    
    -- Llave foránea interna
    FOREIGN KEY (id_conflicto) 
        REFERENCES casos_conflicto(id_conflicto) 
        ON DELETE CASCADE,
        
    -- Llave foránea cruzada a la otra Base de Datos (gestion_escolar)
    FOREIGN KEY (nro_matricula_alumno) 
        REFERENCES gestion_escolar.alumnos(nro_matricula)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;
