
CREATE DATABASE BiblioTec
ON PRIMARY
(
	NAME = 'BiblioTecDB',
	FILENAME = 'D:\Respaldo\Escritorio\BiblioTec\bd\BiblioTec.mdf'
)
LOG ON
(
	NAME = 'BiblioTecDB_log',
	FILENAME = 'D:\Respaldo\Escritorio\BiblioTec\bd\BiblioTec.ldf'
);

use BiblioTec;

CREATE TABLE [Usuario](
  [idUsuario] int NOT NULL,
  [nom_Us] varchar NOT NULL,
  [apell_Us] varchar NOT NULL,
  [carrera_Us] varchar NOT NULL,
  [semestre_Us] varchar,
  [correo_Us] varchar NOT NULL,
  [contra_Us] varchar NOT NULL,
  [url_Usuario] varchar NOT NULL,
  CONSTRAINT PK_USU  PRIMARY KEY([idUsuario])
)
GO

CREATE TABLE [Publicaciones](
  [idPub] int NOT NULL,
  [idUsuario] int NOT NULL,
  [titulo_Pub] varchar NOT NULL,
  [fecha_Pub] date NOT NULL,
  [descrip_Pub] varchar,
  [calif_Pub] decimal NOT NULL,
  [carrera_Pub] varchar NOT NULL,
  [materia_Pub] varchar NOT NULL,
  tipo_pub varchar NOT NULL,
  [archivo_Pub] varchar NOT NULL,
  [url_Pub] varchar NOT NULL,
  CONSTRAINT PK_PUB PRIMARY KEY([idPub])
)
GO

CREATE TABLE [Insignia](
  [idInsignia] int NOT NULL,
  [tipo_Insig] varchar NOT NULL,
  [descrip_Insig] varchar NOT NULL,
  CONSTRAINT PK_INS PRIMARY KEY([idInsignia])
)
GO

CREATE TABLE [Usuario_Insignia]
  ([idInsignia] int NOT NULL, [idUsuario] int NOT NULL, cant int NOT NULL)
GO

CREATE TABLE [Comentario](
  [idComent] int NOT NULL,
  [idPub] int NOT NULL,
  [idUsuario] int NOT NULL,
  [text_Coment] varchar NOT NULL,
  [fecha_Coment] date NOT NULL,
  [url_Coment] varchar NOT NULL,
  CONSTRAINT PK_COM PRIMARY KEY([idComent])
)
GO

CREATE TABLE [Administrador](
  [idAdmin] int NOT NULL,
  [coreo_Admin] varchar NOT NULL,
  [contra_Admin] varchar NOT NULL,
  CONSTRAINT PK_ADMIN PRIMARY KEY([idAdmin])
)
GO

CREATE TABLE [ReportePublicación](
  [idReporte] int NOT NULL,
  [idPub] int NOT NULL,
  [fecha_Report] date NOT NULL,
  [motivo_Report] varchar NOT NULL,
  [estado_Report] bit NOT NULL,
  CONSTRAINT PK_PUB PRIMARY KEY([idReporte])
)
GO

CREATE TABLE [ReporteComentario](
  [idReporteCom] int NOT NULL,
  [idComent] int NOT NULL,
  [fecha_Report ] date NOT NULL,
  [motivo_Report] varchar NOT NULL,
  [estado_Report] bit NOT NULL,
  CONSTRAINT PK_REPCOM PRIMARY KEY([idReporteCom])
)
GO

ALTER TABLE [Comentario]
  ADD CONSTRAINT [Comentario_Publicaciones_idPub_fkey]
    FOREIGN KEY ([idPub]) REFERENCES [Publicaciones] ([idPub])
GO

ALTER TABLE [Publicaciones]
  ADD CONSTRAINT [Publicaciones_Usuario_idUsuario_fkey]
    FOREIGN KEY ([idUsuario]) REFERENCES [Usuario] ([idUsuario])
GO

ALTER TABLE [Comentario]
  ADD CONSTRAINT [Comentario_Usuario_idUsuario_fkey]
    FOREIGN KEY ([idUsuario]) REFERENCES [Usuario] ([idUsuario])
GO

ALTER TABLE [Usuario_Insignia]
  ADD CONSTRAINT [Usuario_Insignia_Insignia_idInsignia_fkey]
    FOREIGN KEY ([idInsignia]) REFERENCES [Insignia] ([idInsignia])
GO

ALTER TABLE [Usuario_Insignia]
  ADD CONSTRAINT [Usuario_Insignia_Usuario_idUsuario_fkey]
    FOREIGN KEY ([idUsuario]) REFERENCES [Usuario] ([idUsuario])
GO

ALTER TABLE [ReportePublicación]
  ADD CONSTRAINT [ReportePublicación_Publicaciones_idPub_fkey]
    FOREIGN KEY ([idPub]) REFERENCES [Publicaciones] ([idPub])
GO

ALTER TABLE [ReporteComentario]
  ADD CONSTRAINT [ReporteComentario_Comentario_idComent_fkey]
    FOREIGN KEY ([idComent]) REFERENCES [Comentario] ([idComent])
GO



--PRUEBAS DE CONSULTAS
SELECT * FROM Usuario;

SELECT @@SERVERNAME