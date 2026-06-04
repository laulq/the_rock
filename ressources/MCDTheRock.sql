CREATE TABLE Compte (
    id_compte           SMALLINT        PRIMARY KEY AUTO_INCREMENT,
    pseudo_compte       VARCHAR(50)     NOT NULL UNIQUE,
    nom_compte          VARCHAR(50)     NOT NULL,
    prenom_compte       VARCHAR(50)     NOT NULL,
    adresse_compte      VARCHAR(255)    NOT NULL,
    code_verif          VARCHAR(4),
    verifie_compte      BOOLEAN         NOT NULL DEFAULT FALSE,
    mdp_compte          VARCHAR(255)    NOT NULL
);

CREATE TABLE Personnalisation_Profil (
    id_compte           SMALLINT        PRIMARY KEY,
    description_profil  VARCHAR(250),
    photo_profil        VARCHAR(255),
    FOREIGN KEY (id_compte) REFERENCES Compte(id_compte) ON DELETE CASCADE
);

CREATE TABLE Radio (
    id_radio            SMALLINT        PRIMARY KEY AUTO_INCREMENT,
    nom_radio           VARCHAR(50)     NOT NULL,
    slogan_radio        VARCHAR(50),
    url_radio           VARCHAR(255)    NOT NULL,
    image_radio         VARCHAR(255),
    description_radio   VARCHAR(255),
    localisation_radio  VARCHAR(50)
);

CREATE TABLE Tag (
    id_tag              SMALLINT        PRIMARY KEY AUTO_INCREMENT,
    nom_tag             VARCHAR(50)     NOT NULL UNIQUE
);

CREATE TABLE Radio_Tag (
    id_radio            SMALLINT,
    id_tag              SMALLINT,
    PRIMARY KEY (id_radio, id_tag),
    FOREIGN KEY (id_radio) REFERENCES Radio(id_radio) ON DELETE CASCADE,
    FOREIGN KEY (id_tag)   REFERENCES Tag(id_tag)     ON DELETE CASCADE
);

CREATE TABLE Salon_Chat (
    id_salon            SMALLINT        PRIMARY KEY AUTO_INCREMENT,
    id_radio            SMALLINT        NOT NULL UNIQUE,
    nom_salon           VARCHAR(50),
    FOREIGN KEY (id_radio) REFERENCES Radio(id_radio) ON DELETE CASCADE
);

CREATE TABLE Commentaire (
    id_commentaire      INT             PRIMARY KEY AUTO_INCREMENT,
    id_compte           SMALLINT        NOT NULL,
    id_salon            SMALLINT        NOT NULL,
    contenu_commentaire VARCHAR(500)    NOT NULL,

    date_commentaire    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_compte) REFERENCES Compte(id_compte) ON DELETE CASCADE,
    FOREIGN KEY (id_salon)  REFERENCES Salon_Chat(id_salon) ON DELETE CASCADE
);

CREATE TABLE Like_Commentaire (
    id_compte       SMALLINT,
    id_commentaire  INT,
    PRIMARY KEY (id_compte, id_commentaire),
    FOREIGN KEY (id_compte)      REFERENCES Compte(id_compte)           ON DELETE CASCADE,
    FOREIGN KEY (id_commentaire) REFERENCES Commentaire(id_commentaire) ON DELETE CASCADE
);

CREATE TABLE Lecture (
    id_compte           SMALLINT,
    id_radio            SMALLINT,
    date_lecture        DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_compte, id_radio, date_lecture),
    FOREIGN KEY (id_compte) REFERENCES Compte(id_compte) ON DELETE CASCADE,
    FOREIGN KEY (id_radio)  REFERENCES Radio(id_radio)   ON DELETE CASCADE
);

CREATE TABLE Favori (
    id_compte           SMALLINT,
    id_radio            SMALLINT,
    date_favori         DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_compte, id_radio),
    FOREIGN KEY (id_compte) REFERENCES Compte(id_compte) ON DELETE CASCADE,
    FOREIGN KEY (id_radio)  REFERENCES Radio(id_radio)   ON DELETE CASCADE
);

CREATE TABLE Follow (
    id_compte           SMALLINT,
    id_radio            SMALLINT,
    date_follow         DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_compte, id_radio),
    FOREIGN KEY (id_compte) REFERENCES Compte(id_compte) ON DELETE CASCADE,
    FOREIGN KEY (id_radio)  REFERENCES Radio(id_radio)   ON DELETE CASCADE
);

CREATE TABLE Partage (
    id_compte           SMALLINT,
    id_radio            SMALLINT,
    date_partage        DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_compte, id_radio),
    FOREIGN KEY (id_compte) REFERENCES Compte(id_compte) ON DELETE CASCADE,
    FOREIGN KEY (id_radio)  REFERENCES Radio(id_radio)   ON DELETE CASCADE
);

CREATE TABLE Message (
    id_message          INT             PRIMARY KEY AUTO_INCREMENT,
    id_compte           SMALLINT        NOT NULL,
    id_radio            SMALLINT        NOT NULL,
    contenu_message     VARCHAR(500)    NOT NULL,
    date_message        DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_compte) REFERENCES Compte(id_compte) ON DELETE CASCADE,
    FOREIGN KEY (id_radio)  REFERENCES Radio(id_radio)   ON DELETE CASCADE
);