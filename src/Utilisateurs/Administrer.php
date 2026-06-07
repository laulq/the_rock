<?php
namespace Utilisateurs;

use PDO;
use PDOException;
use Exception;

/**
 * Administrer - Gestion des utilisateurs
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

        if ($this->getUtilisateurs() === null) {
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
                CREATE TABLE IF NOT EXISTS Compte (
                    id_compte           SMALLINT        PRIMARY KEY AUTO_INCREMENT,
                    pseudo_compte       VARCHAR(50)     NOT NULL UNIQUE,
                    nom_compte          VARCHAR(50),
                    prenom_compte       VARCHAR(50),
                    adresse_compte      VARCHAR(255)    NOT NULL,
                    code_verif          VARCHAR(4),
                    verifie_compte      BOOLEAN         NOT NULL DEFAULT FALSE,
                    mdp_compte          VARCHAR(255)    NOT NULL
                );

                CREATE TABLE IF NOT EXISTS Personnalisation_Profil (
                    id_compte           SMALLINT        PRIMARY KEY,
                    description_profil  VARCHAR(250),
                    photo_profil        VARCHAR(255),
                    FOREIGN KEY (id_compte) REFERENCES Compte(id_compte) ON DELETE CASCADE
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
     * Obtenir tous les utilisateurs
     *
     * @return array|null
     */
    public function getUtilisateurs(): ?array
    {
        $utilisateurs = null;
        try {
            $pdo = $this->connexion();
            $requeteSQL = "SELECT * FROM Compte";
            $statement  = $pdo->query($requeteSQL);
            $utilisateurs = $statement->fetchAll(PDO::FETCH_ASSOC);
            $pdo = null;
        } catch (Exception $e) {
            $utilisateurs = null;
        }
        return $utilisateurs;
    }

    /**
     * Obtenir un utilisateur par son id
     *
     * @param int $id
     * @return array|null
     */
    public function getUtilisateur(int $id): ?array
    {
        $utilisateur = null;
        try {
            $pdo = $this->connexion();
            $requeteSQL = "SELECT * FROM Compte WHERE id_compte = :id";
            $statement  = $pdo->prepare($requeteSQL);
            $statement->execute([':id' => $id]);
            $ligne = $statement->fetch(PDO::FETCH_ASSOC);
            $utilisateur = $ligne ?: null;
            $pdo = null;
        } catch (Exception $e) {
            $utilisateur = null;
        }
        return $utilisateur;
    }

    /**
     * Ajouter un utilisateur
     *
     * @param string $pseudo
     * @param string $adresse
     * @param string $mdp
     * @param string $code_verif
     * @return bool
     */
    public function ajouterUtilisateur(string $pseudo, string $adresse, string $mdp, string $code_verif): bool
    {
        $succes = false;
        try {
            $pdo = $this->connexion();
            $requeteSQL = "INSERT INTO Compte (pseudo_compte, adresse_compte, mdp_compte, code_verif, verifie_compte)
                           VALUES (:pseudo, :adresse, :mdp, :code_verif, FALSE)";
            $statement  = $pdo->prepare($requeteSQL);
            $succes = $statement->execute([
                ':pseudo'     => $pseudo,
                ':adresse'    => $adresse,
                ':mdp'        => password_hash($mdp, PASSWORD_DEFAULT),
                ':code_verif' => $code_verif
            ]);
            $pdo = null;
        } catch (Exception $e) {
            $succes = false;
        }
        return $succes;
    }

    /**
     * Modifier un utilisateur
     *
     * @param int $id
     * @param string $pseudo
     * @param string $nom
     * @param string $prenom
     * @param string $adresse
     * @return bool
     */
    public function modifierUtilisateur(int $id, string $pseudo, string $nom, string $prenom, string $adresse): bool
    {
        $succes = false;
        try {
            $pdo = $this->connexion();
            $requeteSQL = "UPDATE Compte SET
                            pseudo_compte   = :pseudo,
                            nom_compte      = :nom,
                            prenom_compte   = :prenom,
                            adresse_compte  = :adresse
                           WHERE id_compte  = :id";
            $statement  = $pdo->prepare($requeteSQL);
            $succes = $statement->execute([
                ':pseudo'  => $pseudo,
                ':nom'     => $nom,
                ':prenom'  => $prenom,
                ':adresse' => $adresse,
                ':id'      => $id
            ]);
            $pdo = null;
        } catch (Exception $e) {
            $succes = false;
        }
        return $succes;
    }

    /**
     * Supprimer un utilisateur
     *
     * @param int $id
     * @return bool
     */
    public function supprimerUtilisateur(int $id): bool
    {
        $succes = false;
        try {
            $pdo = $this->connexion();
            $requeteSQL = "DELETE FROM Compte WHERE id_compte = :id";
            $statement  = $pdo->prepare($requeteSQL);
            $succes = $statement->execute([':id' => $id]);
            $pdo = null;
        } catch (Exception $e) {
            $succes = false;
        }
        return $succes;
    }

    /**
     * Vérifier les identifiants de connexion
     *
     * @param string $pseudo
     * @param string $mdp
     * @return array|null
     */
    public function connexionUtilisateur(string $pseudo, string $mdp): ?array
    {
        $utilisateur = null;
        try {
            $pdo = $this->connexion();
            $requeteSQL = "SELECT * FROM Compte WHERE pseudo_compte = :pseudo";
            $statement  = $pdo->prepare($requeteSQL);
            $statement->execute([':pseudo' => $pseudo]);
            $ligne = $statement->fetch(PDO::FETCH_ASSOC);

            if ($ligne && password_verify($mdp, $ligne['mdp_compte']) && $ligne['verifie_compte']) {
                $utilisateur = $ligne;
            }
            $pdo = null;
        } catch (Exception $e) {
            $utilisateur = null;
        }
        return $utilisateur;
    }

    /**
     * Vérifier le code de vérification
     *
     * @param string $pseudo
     * @param string $code_verif
     * @return bool
     */
    public function verifierCode(string $pseudo, string $code_verif): bool
    {
        $succes = false;
        try {
            $pdo = $this->connexion();
            $requeteSQL = "SELECT * FROM Compte WHERE pseudo_compte = :pseudo AND code_verif = :code_verif";
            $statement  = $pdo->prepare($requeteSQL);
            $statement->execute([':pseudo' => $pseudo, ':code_verif' => $code_verif]);

            if ($statement->fetch()) {
                $requeteSQL = "UPDATE Compte SET verifie_compte = TRUE, code_verif = NULL WHERE pseudo_compte = :pseudo";
                $statement  = $pdo->prepare($requeteSQL);
                $succes = $statement->execute([':pseudo' => $pseudo]);
            }
            $pdo = null;
        } catch (Exception $e) {
            $succes = false;
        }
        return $succes;
    }

    /**
     * Obtenir le profil d'un utilisateur
     *
     * @param int $id
     * @return array|null
     */
    public function getProfil(int $id): ?array
    {
        $profil = null;
        try {
            $pdo = $this->connexion();
            $requeteSQL = "SELECT * FROM Personnalisation_Profil WHERE id_compte = :id";
            $statement  = $pdo->prepare($requeteSQL);
            $statement->execute([':id' => $id]);
            $ligne = $statement->fetch(PDO::FETCH_ASSOC);
            $profil = $ligne ?: null;
            $pdo = null;
        } catch (Exception $e) {
            $profil = null;
        }
        return $profil;
    }

    /**
     * Modifier le profil d'un utilisateur
     *
     * @param int $id
     * @param string $description
     * @param string $photo
     * @return bool
     */
    public function modifierProfil(int $id, string $description, string $photo): bool
    {
        $succes = false;
        try {
            $pdo = $this->connexion();
            $requeteSQL = "INSERT INTO Personnalisation_Profil (id_compte, description_profil, photo_profil)
                           VALUES (:id, :description, :photo)
                           ON DUPLICATE KEY UPDATE
                            description_profil = :description,
                            photo_profil       = :photo";
            $statement  = $pdo->prepare($requeteSQL);
            $succes = $statement->execute([
                ':id'          => $id,
                ':description' => $description,
                ':photo'       => $photo
            ]);
            $pdo = null;
        } catch (Exception $e) {
            $succes = false;
        }
        return $succes;
    }
}

