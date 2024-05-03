#------------------------------------------------------------
#        Script MySQL.
#-----------------------------------------------------------

#------------------------------------------------------------
# Table: Utilisateur
#------------------------------------------------------------

CREATE TABLE Utilisateur(
        idUtilisateur Int AUTO_INCREMENT NOT NULL ,
        pseudo    Varchar (50) NOT NULL ,
    	mdp       Varchar (50) NOT NULL 
	,CONSTRAINT Utilisateur_PK PRIMARY KEY (idUtilisateur)
)ENGINE=InnoDB;

#------------------------------------------------------------
# Table: Recette
#------------------------------------------------------------

CREATE TABLE Recette (
    idRecette INT AUTO_INCREMENT NOT NULL,
    image_path Varchar (250) NOT NULL,
    nom Varchar (250) NOT NULL,
    nbpersonne INT NOT NULL,
    tpsPrep FLOAT NOT NULL,
    tpsRep FLOAT NOT NULL,
    tpsCuis FLOAT NOT NULL,
    idUtilisateur INT NOT NULL,
    CONSTRAINT Recette_PK PRIMARY KEY (idRecette),
    CONSTRAINT Recette_Utilisateur_FK FOREIGN KEY (idUtilisateur) REFERENCES Utilisateur(idUtilisateur)
) ENGINE=InnoDB;

#------------------------------------------------------------
# Table: Ustensiles
#------------------------------------------------------------

CREATE TABLE Ustensiles(
        idUstensiles Int  Auto_increment  NOT NULL ,
        libelle      Varchar (50) NOT NULL ,
        pathImg      Varchar (50) NOT NULL
	,CONSTRAINT Ustensiles_PK PRIMARY KEY (idUstensiles)
)ENGINE=InnoDB;

#------------------------------------------------------------
# Table: Etape
#------------------------------------------------------------

CREATE TABLE Etape(
        idEtape   Int Auto_increment NOT NULL ,
        numEtape  Int NOT NULL ,
        texte     Varchar (50) NOT NULL ,
        idRecette Int NOT NULL
	,CONSTRAINT Etape_PK PRIMARY KEY (idEtape,numEtape,texte)

	,CONSTRAINT Etape_Recette_FK FOREIGN KEY (idRecette) REFERENCES Recette(idRecette)
)ENGINE=InnoDB;

#------------------------------------------------------------
# Table: Commentaire
#------------------------------------------------------------

CREATE TABLE Commentaire(
        idCommentaire Int  Auto_increment  NOT NULL ,
        texte         Varchar (50) NOT NULL ,
        note          Int NOT NULL ,
    	idUtilisateur Int NOT NULL,
        idRecette     Int NOT NULL
	,CONSTRAINT Commentaire_PK PRIMARY KEY (idCommentaire)

	,CONSTRAINT Commentaire_Recette_FK FOREIGN KEY (idRecette) REFERENCES Recette(idRecette)
    ,CONSTRAINT Commentaire_Utilisateur_FK FOREIGN KEY (idUtilisateur) REFERENCES Utilisateur(idUtilisateur)
)ENGINE=InnoDB;

#------------------------------------------------------------
# Table: utilise
#------------------------------------------------------------

CREATE TABLE utilise(
        idUstensiles Int NOT NULL ,
        idRecette    Int NOT NULL
	,CONSTRAINT relation6_PK PRIMARY KEY (idUstensiles,idRecette)

	,CONSTRAINT relation6_Ustensiles_FK FOREIGN KEY (idUstensiles) REFERENCES Ustensiles(idUstensiles)
	,CONSTRAINT relation6_Recette0_FK FOREIGN KEY (idRecette) REFERENCES Recette(idRecette)
)ENGINE=InnoDB;

#------------------------------------------------------------
# Table: Tag
#------------------------------------------------------------
CREATE TABLE Tag (
    idTag INT AUTO_INCREMENT NOT NULL,
    libelle VARCHAR(50) NOT NULL,
    PRIMARY KEY (idTag)
) ENGINE=InnoDB;

#------------------------------------------------------------
# Table: Recette_Tag
#------------------------------------------------------------
CREATE TABLE Recette_Tag (
    idRecette INT NOT NULL,
    idTag INT NOT NULL,
    PRIMARY KEY (idRecette, idTag),
    FOREIGN KEY (idRecette) REFERENCES Recette(idRecette),
    FOREIGN KEY (idTag) REFERENCES Tag(idTag)
) ENGINE=InnoDB;

# Table Aliment
CREATE TABLE Aliment (
    idAliment INT AUTO_INCREMENT NOT NULL,
    nom VARCHAR(50) NOT NULL,
    PRIMARY KEY (idAliment)
) ENGINE=InnoDB;

# Table Recette_Aliment
CREATE TABLE Recette_Aliment (
    idRecette INT NOT NULL,
    idAliment INT NOT NULL,
    PRIMARY KEY (idRecette, idAliment),
    FOREIGN KEY (idRecette) REFERENCES Recette(idRecette),
    FOREIGN KEY (idAliment) REFERENCES Aliment(idAliment)
) ENGINE=InnoDB;
