<?php
namespace Radios;

use PDO;
use PDOException;
use Exception;

/**
 * Administrer - Gestion des radios
 */
class Administrer
{
    private $myHost;
    private $myDb;
    private $myUser;
    private $myPass;

    /**
     * Constructeur
     *
     * @param string $myHost
     * @param string $myDb
     * @param string $myUser
     * @param string $myPass
     *
     * @return Administrer
     */
    function __construct($myHost = null, $myDb = null, $myUser = null, $myPass = null)
    {
        $this->myHost = $myHost;
        $this->myDb   = $myDb;
        $this->myUser = $myUser;
        $this->myPass = $myPass;

        if ($this->getRadios() === null) {
            $this->installerBaseDeDonnees();
        }
    }

    /**
     * Connexion à la base de données
     *
     * @return PDO
     */
    private function connexion(): PDO
    {
        $pdo = new PDO("mysql:host=" . $this->myHost . ";dbname=" . $this->myDb, $this->myUser, $this->myPass);
        $pdo->query("SET NAMES utf8");
        $pdo->query("SET CHARACTER SET 'utf8'");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }

    /**
     * Installer la base de données
     *
     * @return Administrer
     */
    public function installerBaseDeDonnees(): Administrer
    {
        try {
            $pdo = new PDO("mysql:host=" . $this->myHost, $this->myUser, $this->myPass);
            $pdo->query("CREATE DATABASE IF NOT EXISTS " . $this->myDb . " DEFAULT CHARACTER SET utf8 COLLATE utf8_bin");
            $pdo = null;
            $pdo = $this->connexion();

            $requeteSQL = <<<EOF
                CREATE TABLE IF NOT EXISTS Radio (
                    id_radio            SMALLINT        PRIMARY KEY AUTO_INCREMENT,
                    nom_radio           VARCHAR(50)     NOT NULL,
                    slogan_radio        VARCHAR(50),
                    url_radio           VARCHAR(255)    NOT NULL,
                    image_radio         VARCHAR(255),
                    description_radio   VARCHAR(255),
                    localisation_radio  VARCHAR(50)
                );

                CREATE TABLE IF NOT EXISTS Tag (
                    id_tag              SMALLINT        PRIMARY KEY AUTO_INCREMENT,
                    nom_tag             VARCHAR(50)     NOT NULL UNIQUE
                );

                CREATE TABLE IF NOT EXISTS Radio_Tag (
                    id_radio            SMALLINT,
                    id_tag              SMALLINT,
                    PRIMARY KEY (id_radio, id_tag),
                    FOREIGN KEY (id_radio) REFERENCES Radio(id_radio) ON DELETE CASCADE,
                    FOREIGN KEY (id_tag)   REFERENCES Tag(id_tag)     ON DELETE CASCADE
                );

                CREATE TABLE IF NOT EXISTS Salon_Chat (
                    id_salon            SMALLINT        PRIMARY KEY AUTO_INCREMENT,
                    id_radio            SMALLINT        NOT NULL UNIQUE,
                    nom_salon           VARCHAR(50),
                    FOREIGN KEY (id_radio) REFERENCES Radio(id_radio) ON DELETE CASCADE
                );

                CREATE TABLE IF NOT EXISTS Commentaire (
                    id_commentaire      INT             PRIMARY KEY AUTO_INCREMENT,
                    id_compte           SMALLINT        NOT NULL,
                    id_salon            SMALLINT        NOT NULL,
                    contenu_commentaire VARCHAR(500)    NOT NULL,
                    date_commentaire    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (id_compte) REFERENCES Compte(id_compte)    ON DELETE CASCADE,
                    FOREIGN KEY (id_salon)  REFERENCES Salon_Chat(id_salon) ON DELETE CASCADE
                );

                CREATE TABLE IF NOT EXISTS Like_Commentaire (
                    id_compte       SMALLINT,
                    id_commentaire  INT,
                    PRIMARY KEY (id_compte, id_commentaire),
                    FOREIGN KEY (id_compte)      REFERENCES Compte(id_compte)           ON DELETE CASCADE,
                    FOREIGN KEY (id_commentaire) REFERENCES Commentaire(id_commentaire) ON DELETE CASCADE
                );

                CREATE TABLE IF NOT EXISTS Lecture (
                    id_compte           SMALLINT,
                    id_radio            SMALLINT,
                    date_lecture        DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (id_compte, id_radio, date_lecture),
                    FOREIGN KEY (id_compte) REFERENCES Compte(id_compte) ON DELETE CASCADE,
                    FOREIGN KEY (id_radio)  REFERENCES Radio(id_radio)   ON DELETE CASCADE
                );

                CREATE TABLE IF NOT EXISTS Favori (
                    id_compte           SMALLINT,
                    id_radio            SMALLINT,
                    date_favori         DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (id_compte, id_radio),
                    FOREIGN KEY (id_compte) REFERENCES Compte(id_compte) ON DELETE CASCADE,
                    FOREIGN KEY (id_radio)  REFERENCES Radio(id_radio)   ON DELETE CASCADE
                );

                CREATE TABLE IF NOT EXISTS Follow (
                    id_compte           SMALLINT,
                    id_radio            SMALLINT,
                    date_follow         DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (id_compte, id_radio),
                    FOREIGN KEY (id_compte) REFERENCES Compte(id_compte) ON DELETE CASCADE,
                    FOREIGN KEY (id_radio)  REFERENCES Radio(id_radio)   ON DELETE CASCADE
                );

                CREATE TABLE IF NOT EXISTS Partage (
                    id_compte           SMALLINT,
                    id_radio            SMALLINT,
                    date_partage        DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (id_compte, id_radio),
                    FOREIGN KEY (id_compte) REFERENCES Compte(id_compte) ON DELETE CASCADE,
                    FOREIGN KEY (id_radio)  REFERENCES Radio(id_radio)   ON DELETE CASCADE
                );

                CREATE TABLE IF NOT EXISTS Message (
                    id_message          INT             PRIMARY KEY AUTO_INCREMENT,
                    id_compte           SMALLINT        NOT NULL,
                    id_radio            SMALLINT        NOT NULL,
                    contenu_message     VARCHAR(500)    NOT NULL,
                    date_message        DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (id_compte) REFERENCES Compte(id_compte) ON DELETE CASCADE,
                    FOREIGN KEY (id_radio)  REFERENCES Radio(id_radio)   ON DELETE CASCADE
                );
EOF;
            $pdo->exec($requeteSQL);
            $pdo = null;

        } catch (Exception $e) {
            echo($e->getMessage());
        }

        return $this;
    }

    /**
     * Obtenir toutes les radios
     *
     * @return array|null
     */
    public function getRadios(): ?array
    {
        $radios = null;
        try {
            $pdo        = $this->connexion();
            $requeteSQL = "SELECT * FROM Radio";
            $statement  = $pdo->query($requeteSQL);
            $radios     = $statement->fetchAll(PDO::FETCH_ASSOC);
            $pdo        = null;
        } catch (Exception $e) {
            $radios = null;
        }
        return $radios;
    }

    /**
     * Obtenir une radio par son id
     *
     * @param int $id
     * @return array|null
     */
    public function getRadio(int $id): ?array
    {
        $radio = null;
        try {
            $pdo        = $this->connexion();
            $requeteSQL = "SELECT * FROM Radio WHERE id_radio = :id";
            $statement  = $pdo->prepare($requeteSQL);
            $statement->execute([':id' => $id]);
            $ligne = $statement->fetch(PDO::FETCH_ASSOC);
            $radio = $ligne ?: null;
            $pdo   = null;
        } catch (Exception $e) {
            $radio = null;
        }
        return $radio;
    }

    /**
     * Obtenir le top 5 des radios par nombre d'écoutes sur la dernière semaine
     *
     * @return array|null
     */
    public function getTopRadios(): ?array
    {
        $radios = null;
        try {
            $pdo        = $this->connexion();
            $requeteSQL = "SELECT r.*, COUNT(l.id_radio) as nb_ecoutes
                           FROM Radio r
                           LEFT JOIN Lecture l ON r.id_radio = l.id_radio
                            AND l.date_lecture >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                           GROUP BY r.id_radio
                           ORDER BY nb_ecoutes DESC
                           LIMIT 5";
            $statement  = $pdo->query($requeteSQL);
            $radios     = $statement->fetchAll(PDO::FETCH_ASSOC);
            $pdo        = null;
        } catch (Exception $e) {
            $radios = null;
        }
        return $radios;
    }

    /**
     * Obtenir les dernières écoutes d'un utilisateur
     *
     * @param int $id_compte
     * @return array|null
     */
    public function getDernieresEcoutes(int $id_compte): ?array
    {
        $ecoutes = null;
        try {
            $pdo        = $this->connexion();
            $requeteSQL = "SELECT r.*, l.date_lecture
                           FROM Radio r
                           JOIN Lecture l ON r.id_radio = l.id_radio
                           WHERE l.id_compte = :id
                           ORDER BY l.date_lecture DESC
                           LIMIT 3";
            $statement  = $pdo->prepare($requeteSQL);
            $statement->execute([':id' => $id_compte]);
            $ecoutes = $statement->fetchAll(PDO::FETCH_ASSOC);
            $pdo     = null;
        } catch (Exception $e) {
            $ecoutes = null;
        }
        return $ecoutes;
    }

    /**
     * Obtenir les recommandations pour un utilisateur (radios non écoutées, triées par popularité)
     *
     * @param int $id_compte
     * @return array|null
     */
    public function getRecommandations(int $id_compte): ?array
    {
        $recommandations = null;
        try {
            $pdo        = $this->connexion();
            $requeteSQL = "SELECT r.*, COUNT(l2.id_radio) as nb_ecoutes
                           FROM Radio r
                           LEFT JOIN Lecture l2 ON r.id_radio = l2.id_radio
                           WHERE r.id_radio NOT IN (
                               SELECT DISTINCT id_radio FROM Lecture WHERE id_compte = :id
                           )
                           GROUP BY r.id_radio
                           ORDER BY nb_ecoutes DESC
                           LIMIT 4";
            $statement  = $pdo->prepare($requeteSQL);
            $statement->execute([':id' => $id_compte]);
            $recommandations = $statement->fetchAll(PDO::FETCH_ASSOC);
            $pdo             = null;
        } catch (Exception $e) {
            $recommandations = null;
        }
        return $recommandations;
    }

    /**
     * Obtenir les tendances (radios les plus followées)
     *
     * @return array|null
     */
    public function getTendances(): ?array
    {
        $tendances = null;
        try {
            $pdo        = $this->connexion();
            $requeteSQL = "SELECT r.*, COUNT(f.id_radio) as nb_abonnes
                           FROM Radio r
                           LEFT JOIN Follow f ON r.id_radio = f.id_radio
                           GROUP BY r.id_radio
                           ORDER BY nb_abonnes DESC
                           LIMIT 7";
            $statement  = $pdo->query($requeteSQL);
            $tendances  = $statement->fetchAll(PDO::FETCH_ASSOC);
            $pdo        = null;
        } catch (Exception $e) {
            $tendances = null;
        }
        return $tendances;
    }

    /**
     * Vérifier si un utilisateur suit une radio
     *
     * @param int $id_compte
     * @param int $id_radio
     * @return bool
     */
    public function estSuivie(int $id_compte, int $id_radio): bool
    {
        try {
            $pdo        = $this->connexion();
            $requeteSQL = "SELECT * FROM Follow WHERE id_compte = :id_compte AND id_radio = :id_radio";
            $statement  = $pdo->prepare($requeteSQL);
            $statement->execute([':id_compte' => $id_compte, ':id_radio' => $id_radio]);
            $result = $statement->fetch();
            $pdo    = null;
            return $result !== false;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Suivre / ne plus suivre une radio
     *
     * @param int $id_compte
     * @param int $id_radio
     * @return bool
     */
    public function toggleFollow(int $id_compte, int $id_radio): bool
    {
        $succes = false;
        try {
            $pdo        = $this->connexion();
            $requeteSQL = "SELECT * FROM Follow WHERE id_compte = :id_compte AND id_radio = :id_radio";
            $statement  = $pdo->prepare($requeteSQL);
            $statement->execute([':id_compte' => $id_compte, ':id_radio' => $id_radio]);

            if ($statement->fetch()) {
                $requeteSQL = "DELETE FROM Follow WHERE id_compte = :id_compte AND id_radio = :id_radio";
            } else {
                $requeteSQL = "INSERT INTO Follow (id_compte, id_radio) VALUES (:id_compte, :id_radio)";
            }

            $statement = $pdo->prepare($requeteSQL);
            $succes    = $statement->execute([':id_compte' => $id_compte, ':id_radio' => $id_radio]);
            $pdo       = null;
        } catch (Exception $e) {
            $succes = false;
        }
        return $succes;
    }

    /**
     * Enregistrer une écoute
     *
     * @param int $id_compte
     * @param int $id_radio
     * @return bool
     */
    public function enregistrerEcoute(int $id_compte, int $id_radio): bool
    {
        $succes = false;
        try {
            $pdo        = $this->connexion();
            $requeteSQL = "INSERT INTO Lecture (id_compte, id_radio) VALUES (:id_compte, :id_radio)";
            $statement  = $pdo->prepare($requeteSQL);
            $succes     = $statement->execute([':id_compte' => $id_compte, ':id_radio' => $id_radio]);
            $pdo        = null;
        } catch (Exception $e) {
            $succes = false;
        }
        return $succes;
    }

    /**
     * Ajouter une radio
     *
     * @param string $nom
     * @param string $slogan
     * @param string $url
     * @param string $image
     * @param string $localisation
     * @return bool
     */
    public function ajouterRadio(string $nom, string $slogan, string $url, string $image, string $localisation): bool
    {
        $succes = false;
        try {
            $pdo        = $this->connexion();
            $requeteSQL = "INSERT INTO Radio (nom_radio, slogan_radio, url_radio, image_radio, localisation_radio)
                        VALUES (:nom, :slogan, :url, :image, :localisation)";
            $statement  = $pdo->prepare($requeteSQL);
            $succes     = $statement->execute([
                ':nom'          => $nom,
                ':slogan'       => $slogan,
                ':url'          => $url,
                ':image'        => 'images/radios/' . $image,
                ':localisation' => $localisation
            ]);
            $pdo = null;
        } catch (Exception $e) {
            $succes = false;
        }
        return $succes;
    }

    /**
     * Modifier une radio
     *
     * @param int $id
     * @param string $nom
     * @param string $slogan
     * @param string $url
     * @param string $image
     * @param string $localisation
     * @return bool
     */
    public function modifierRadio(int $id, string $nom, string $slogan, string $url, string $image, string $localisation): bool
    {
        $succes = false;
        try {
            $pdo        = $this->connexion();
            $requeteSQL = "UPDATE Radio SET
                            nom_radio          = :nom,
                            slogan_radio       = :slogan,
                            url_radio          = :url,
                            image_radio        = :image,
                            localisation_radio = :localisation
                        WHERE id_radio = :id";
            $statement  = $pdo->prepare($requeteSQL);
            $succes     = $statement->execute([
                ':nom'          => $nom,
                ':slogan'       => $slogan,
                ':url'          => $url,
                ':image'        => 'images/radios/' . $image,
                ':localisation' => $localisation,
                ':id'           => $id
            ]);
            $pdo = null;
        } catch (Exception $e) {
            $succes = false;
        }
        return $succes;
    }

    /**
     * Supprimer une radio
     *
     * @param int $id
     * @return bool
     */
    public function supprimerRadio(int $id): bool
    {
        $succes = false;
        try {
            $pdo        = $this->connexion();
            $requeteSQL = "DELETE FROM Radio WHERE id_radio = :id";
            $statement  = $pdo->prepare($requeteSQL);
            $succes     = $statement->execute([':id' => $id]);
            $pdo        = null;
        } catch (Exception $e) {
            $succes = false;
        }
        return $succes;
    }

    /**
     * Obtenir les tags d'une radio
     *
     * @param int $id_radio
     * @return array|null
     */
    public function getTagsRadio(int $id_radio): ?array
    {
        $tags = null;
        try {
            $pdo        = $this->connexion();
            $requeteSQL = "SELECT t.* FROM Tag t
                           JOIN Radio_Tag rt ON t.id_tag = rt.id_tag
                           WHERE rt.id_radio = :id";
            $statement  = $pdo->prepare($requeteSQL);
            $statement->execute([':id' => $id_radio]);
            $tags = $statement->fetchAll(PDO::FETCH_ASSOC);
            $pdo  = null;
        } catch (Exception $e) {
            $tags = null;
        }
        return $tags;
    }

    /**
     * Ajouter un tag à une radio
     *
     * @param int $id_radio
     * @param int $id_tag
     * @return bool
     */
    public function ajouterTagRadio(int $id_radio, int $id_tag): bool
    {
        $succes = false;
        try {
            $pdo        = $this->connexion();
            $requeteSQL = "INSERT IGNORE INTO Radio_Tag (id_radio, id_tag) VALUES (:id_radio, :id_tag)";
            $statement  = $pdo->prepare($requeteSQL);
            $succes     = $statement->execute([':id_radio' => $id_radio, ':id_tag' => $id_tag]);
            $pdo        = null;
        } catch (Exception $e) {
            $succes = false;
        }
        return $succes;
    }

    /**
     * Ajouter un commentaire
     *
     * @param int $id_compte
     * @param int $id_salon
     * @param string $contenu
     * @return bool
     */
    public function ajouterCommentaire(int $id_compte, int $id_salon, string $contenu): bool
    {
        $succes = false;
        try {
            $pdo        = $this->connexion();
            $requeteSQL = "INSERT INTO Commentaire (id_compte, id_salon, contenu_commentaire)
                           VALUES (:id_compte, :id_salon, :contenu)";
            $statement  = $pdo->prepare($requeteSQL);
            $succes     = $statement->execute([
                ':id_compte' => $id_compte,
                ':id_salon'  => $id_salon,
                ':contenu'   => $contenu
            ]);
            $pdo = null;
        } catch (Exception $e) {
            $succes = false;
        }
        return $succes;
    }

    /**
     * Obtenir les commentaires d'un salon
     *
     * @param int $id_salon
     * @return array|null
     */
    public function getCommentaires(int $id_salon): ?array
    {
        $commentaires = null;
        try {
            $pdo        = $this->connexion();
            $requeteSQL = "SELECT c.*, r.nom_radio, r.url_radio, r.image_radio, COUNT(lc.id_commentaire) as nb_likes
                           FROM Commentaire c
                           JOIN Compte co ON c.id_compte = co.id_compte
                           LEFT JOIN Like_Commentaire lc ON c.id_commentaire = lc.id_commentaire
                           WHERE c.id_salon = :id_salon
                           GROUP BY c.id_commentaire
                           ORDER BY c.date_commentaire ASC";
            $statement  = $pdo->prepare($requeteSQL);
            $statement->execute([':id_salon' => $id_salon]);
            $commentaires = $statement->fetchAll(PDO::FETCH_ASSOC);
            $pdo          = null;
        } catch (Exception $e) {
            $commentaires = null;
        }
        return $commentaires;
    }

    /**
     * Liker / unliker un commentaire
     *
     * @param int $id_compte
     * @param int $id_commentaire
     * @return bool
     */
    public function toggleLikeCommentaire(int $id_compte, int $id_commentaire): bool
    {
        $succes = false;
        try {
            $pdo        = $this->connexion();
            $requeteSQL = "SELECT * FROM Like_Commentaire WHERE id_compte = :id_compte AND id_commentaire = :id_commentaire";
            $statement  = $pdo->prepare($requeteSQL);
            $statement->execute([':id_compte' => $id_compte, ':id_commentaire' => $id_commentaire]);

            if ($statement->fetch()) {
                $requeteSQL = "DELETE FROM Like_Commentaire WHERE id_compte = :id_compte AND id_commentaire = :id_commentaire";
            } else {
                $requeteSQL = "INSERT INTO Like_Commentaire (id_compte, id_commentaire) VALUES (:id_compte, :id_commentaire)";
            }

            $statement = $pdo->prepare($requeteSQL);
            $succes    = $statement->execute([':id_compte' => $id_compte, ':id_commentaire' => $id_commentaire]);
            $pdo       = null;
        } catch (Exception $e) {
            $succes = false;
        }
        return $succes;
    }

    /**
     * Vérifier si un utilisateur a liké un commentaire
     *
     * @param int $id_compte
     * @param int $id_commentaire
     * @return bool
     */
    public function estLike(int $id_compte, int $id_commentaire): bool
    {
        try {
            $pdo        = $this->connexion();
            $requeteSQL = "SELECT * FROM Like_Commentaire WHERE id_compte = :id_compte AND id_commentaire = :id_commentaire";
            $statement  = $pdo->prepare($requeteSQL);
            $statement->execute([':id_compte' => $id_compte, ':id_commentaire' => $id_commentaire]);
            $result = $statement->fetch();
            $pdo    = null;
            return $result !== false;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Obtenir les commentaires d'un utilisateur
     *
     * @param int $id_compte
     * @return array|null
     */
    public function getCommentairesUtilisateur(int $id_compte): ?array
    {
        $commentaires = null;
        try {
            $pdo        = $this->connexion();
            $requeteSQL = "SELECT c.*, r.nom_radio, r.url_radio, r.image_radio, COUNT(lc.id_commentaire) as nb_likes
                        FROM Commentaire c
                        JOIN Salon_Chat s  ON c.id_salon   = s.id_salon
                        JOIN Radio r       ON s.id_radio   = r.id_radio
                        JOIN Compte co     ON c.id_compte  = co.id_compte
                        LEFT JOIN Like_Commentaire lc ON c.id_commentaire = lc.id_commentaire
                        WHERE c.id_compte = :id_compte
                        GROUP BY c.id_commentaire
                        ORDER BY c.date_commentaire DESC";
            $statement  = $pdo->prepare($requeteSQL);
            $statement->execute([':id_compte' => $id_compte]);
            $commentaires = $statement->fetchAll(PDO::FETCH_ASSOC);
            $pdo          = null;
        } catch (Exception $e) {
            $commentaires = null;
        }
        return $commentaires;
    }

    /**
     * Obtenir le salon d'une radio
     *
     * @param int $id_radio
     * @return array|null
     */
    public function getSalonRadio(int $id_radio): ?array
    {
        $salon = null;
        try {
            $pdo        = $this->connexion();
            $requeteSQL = "SELECT * FROM Salon_Chat WHERE id_radio = :id_radio";
            $statement  = $pdo->prepare($requeteSQL);
            $statement->execute([':id_radio' => $id_radio]);
            $ligne = $statement->fetch(PDO::FETCH_ASSOC);
            $salon = $ligne ?: null;
            $pdo   = null;
        } catch (Exception $e) {
            $salon = null;
        }
        return $salon;
    }

    /**
     * Obtenir le nombre d'abonnés d'une radio
     *
     * @param int $id_radio
     * @return int
     */
    public function getNbAbonnes(int $id_radio): int
    {
        try {
            $pdo        = $this->connexion();
            $requeteSQL = "SELECT COUNT(*) as nb FROM Follow WHERE id_radio = :id_radio";
            $statement  = $pdo->prepare($requeteSQL);
            $statement->execute([':id_radio' => $id_radio]);
            $ligne = $statement->fetch(PDO::FETCH_ASSOC);
            $pdo   = null;
            return (int)($ligne['nb'] ?? 0);
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Obtenir des suggestions de radios (même tags, hors radio actuelle)
     *
     * @param int $id_radio
     * @return array|null
     */
    public function getSuggestions(int $id_radio): ?array
    {
        $suggestions = null;
        try {
            $pdo        = $this->connexion();
            $requeteSQL = "SELECT DISTINCT r.* FROM Radio r
                        JOIN Radio_Tag rt ON r.id_radio = rt.id_radio
                        WHERE rt.id_tag IN (
                            SELECT id_tag FROM Radio_Tag WHERE id_radio = :id_radio
                        )
                        AND r.id_radio != :id_radio
                        LIMIT 5";
            $statement  = $pdo->prepare($requeteSQL);
            $statement->execute([':id_radio' => $id_radio]);
            $suggestions = $statement->fetchAll(PDO::FETCH_ASSOC);
            $pdo         = null;
        } catch (Exception $e) {
            $suggestions = null;
        }
        return $suggestions;
    }

    /**
     * Vérifier si une radio est en favori
     *
     * @param int $id_compte
     * @param int $id_radio
     * @return bool
     */
    public function estFavori(int $id_compte, int $id_radio): bool
    {
        try {
            $pdo        = $this->connexion();
            $requeteSQL = "SELECT * FROM Favori WHERE id_compte = :id_compte AND id_radio = :id_radio";
            $statement  = $pdo->prepare($requeteSQL);
            $statement->execute([':id_compte' => $id_compte, ':id_radio' => $id_radio]);
            $result = $statement->fetch();
            $pdo    = null;
            return $result !== false;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Ajouter / retirer un favori
     *
     * @param int $id_compte
     * @param int $id_radio
     * @return bool
     */
    public function toggleFavori(int $id_compte, int $id_radio): bool
    {
        $succes = false;
        try {
            $pdo        = $this->connexion();
            $requeteSQL = "SELECT * FROM Favori WHERE id_compte = :id_compte AND id_radio = :id_radio";
            $statement  = $pdo->prepare($requeteSQL);
            $statement->execute([':id_compte' => $id_compte, ':id_radio' => $id_radio]);

            if ($statement->fetch()) {
                $requeteSQL = "DELETE FROM Favori WHERE id_compte = :id_compte AND id_radio = :id_radio";
            } else {
                $requeteSQL = "INSERT INTO Favori (id_compte, id_radio) VALUES (:id_compte, :id_radio)";
            }

            $statement = $pdo->prepare($requeteSQL);
            $succes    = $statement->execute([':id_compte' => $id_compte, ':id_radio' => $id_radio]);
            $pdo       = null;
        } catch (Exception $e) {
            $succes = false;
        }
        return $succes;
    }
}