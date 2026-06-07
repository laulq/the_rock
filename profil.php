<?php
require_once 'composants/init.php';

// Récupération du nombre de radios suivies pour l'affichage du profil
$nb_suivies = 0;
$profil = null;
$abonnements = null;
$historique = null;
$commentaires_user = null;

// Traitement des actions avant tout affichage
if (isset($_SESSION['user'])) {
    $id_compte = $_SESSION['user']['id_compte'];

    // Déconnexion
    if (isset($_GET['deconnexion'])) {
        session_destroy();
        header('Location: index.php');
        exit;
    }

    // Toggle follow depuis le profil
    if (isset($_GET['follow'])) {
        $radios->toggleFollow($id_compte, (int)$_GET['follow']);
        header('Location: profil.php?onglet=' . ($_GET['onglet'] ?? 'abonnements'));
        exit;
    }

    // Toggle like commentaire
    if (isset($_GET['like'])) {
        $radios->toggleLikeCommentaire($id_compte, (int)$_GET['like']);
        header('Location: profil.php?onglet=commentaires');
        exit;
    }

    // Suppression commentaire
    if (isset($_GET['supprimer_commentaire'])) {
        // On vérifie que le commentaire appartient bien à l'utilisateur
        $radios->supprimerCommentaire($id_compte, (int)$_GET['supprimer_commentaire']);
        header('Location: profil.php?onglet=commentaires');
        exit;
    }

    // Sauvegarde paramètres profil
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'parametres') {
        $utilisateurs->modifierUtilisateur(
            $id_compte,
            htmlspecialchars($_POST['pseudo']),
            $_SESSION['user']['nom_compte'],
            $_SESSION['user']['prenom_compte'],
            $_SESSION['user']['adresse_compte']
        );
        $utilisateurs->modifierProfil($id_compte, '', htmlspecialchars($_POST['photo_profil']));

        $_SESSION['user'] = $utilisateurs->getUtilisateur($id_compte);
        header('Location: profil.php');
        exit;
    }

    // Chargement des données
    $profil              = $utilisateurs->getProfil($id_compte);
    $abonnements         = $radios->getAbonnements($id_compte);
    $historique          = $radios->getDernieresEcoutes($id_compte);
    $commentaires_user   = $radios->getCommentairesUtilisateur($id_compte);
    $nb_suivies          = count($abonnements ?? []);

} else {

    // Traitement inscription
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'inscription') {
        $code_verif = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $succes = $utilisateurs->ajouterUtilisateur(
            htmlspecialchars($_POST['pseudo']),
            htmlspecialchars($_POST['nom'] ?? ''),
            htmlspecialchars($_POST['prenom'] ?? ''),
            htmlspecialchars($_POST['adresse_mail']),
            $_POST['mdp'],
            $code_verif
        );
        if ($succes) {
            // TODO: envoyer le mail avec $code_verif
            $_SESSION['pseudo_en_attente'] = htmlspecialchars($_POST['pseudo']);
            header('Location: profil.php?etape=verification');
            exit;
        }
        $erreur_inscription = "Une erreur est survenue, veuillez réessayer.";
    }

    // Traitement vérification code
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'verification') {
        $code = $_POST['c1'] . $_POST['c2'] . $_POST['c3'] . $_POST['c4'];
        $succes = $utilisateurs->verifierCode($_SESSION['pseudo_en_attente'] ?? '', $code);
        if ($succes) {
            unset($_SESSION['pseudo_en_attente']);
            header('Location: profil.php?etape=connexion&verifie=1');
            exit;
        }
        $erreur_verification = "Code incorrect, veuillez réessayer.";
    }

    // Traitement connexion
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'connexion') {
        $utilisateur = $utilisateurs->connexionUtilisateur(
            htmlspecialchars($_POST['pseudo']),
            $_POST['mdp']
        );
        if ($utilisateur) {
            $_SESSION['user'] = $utilisateur;
            header('Location: index.php');
            exit;
        }
        $erreur_connexion = "Identifiants incorrects ou compte non vérifié.";
    }
}

// Détermination de l'onglet actif (profil connecté)
$onglet = $_GET['onglet'] ?? 'abonnements';

// Détermination de l'étape (non connecté)
$etape = $_GET['etape'] ?? 'inscription';   
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>The Rock | Mon profil</title>
        <meta name="description" content="a compléter">

        <link rel="stylesheet" href="./css/styles.css">
    </head>

    <body>
        <?php include 'composants/header.php'; ?>

        <main>
            <?php if (!isset($_SESSION['user'])) : ?>

                <?php if ($etape === 'verification') : ?>

                    <!-- ÉTAPE 3 : Vérification du code -->
                    <section class="verification">
                        <h1>INSCRIPTION</h1>
                        <h2>Un instant !</h2>
                        <p>Confirmez votre inscription en renseignant le code envoyé à votre adresse mail.</p>

                        <?php if (isset($erreur_verification)) : ?>
                            <p class="erreur"><?= $erreur_verification ?></p>
                        <?php endif; ?>

                        <form method="POST" action="profil.php?etape=verification" id="form-verification">
                            <input type="hidden" name="action" value="verification">
                            <div class="verification-code">
                                <input type="text" name="c1" maxlength="1" required>
                                <input type="text" name="c2" maxlength="1" required>
                                <input type="text" name="c3" maxlength="1" required>
                                <input type="text" name="c4" maxlength="1" required>
                            </div>
                            <button type="submit" class="bouton-principal">VALIDER</button>
                        </form>

                        <a href="index.php" class="lien-retour">◄ RETOUR À L'ACCUEIL</a>
                    </section>

                <?php elseif ($etape === 'connexion') : ?>

                    <!-- ÉTAPE 2 : Connexion -->
                    <section class="connexion">
                        <h1>CONNEXION</h1>

                        <?php if (isset($_GET['verifie'])) : ?>
                            <p class="succes">Compte vérifié ! Vous pouvez maintenant vous connecter.</p>
                        <?php endif; ?>

                        <?php if (isset($erreur_connexion)) : ?>
                            <p class="erreur"><?= $erreur_connexion ?></p>
                        <?php endif; ?>

                        <form method="POST" action="profil.php?etape=connexion" id="form-connexion">
                            <input type="hidden" name="action" value="connexion">
                            <div class="form-champ">
                                <label for="pseudo">ADRESSE MAIL</label>
                                <input type="text" id="pseudo" name="pseudo" required>
                            </div>
                            <div class="form-champ">
                                <label for="mdp">MOT DE PASSE</label>
                                <input type="password" id="mdp" name="mdp" required>
                                <a href="#" class="lien-mdp-oublie">MOT DE PASSE OUBLIÉ</a>
                            </div>
                            <button type="submit" class="bouton-principal">SE CONNECTER</button>
                        </form>

                        <hr>
                        <p>OU</p>
                        <a href="profil.php?etape=inscription" class="bouton-secondaire">CRÉER MON COMPTE</a>
                    </section>

                <?php else : ?>

                    <!-- ÉTAPE 1 : Inscription (par défaut) -->
                    <section class="inscription">
                        <h1>INSCRIPTION</h1>

                        <?php if (isset($erreur_inscription)) : ?>
                            <p class="erreur"><?= $erreur_inscription ?></p>
                        <?php endif; ?>

                        <form method="POST" action="profil.php?etape=inscription" id="form-inscription">
                            <input type="hidden" name="action" value="inscription">
                            <div class="form-champ">
                                <label for="adresse_mail">ADRESSE MAIL</label>
                                <input type="email" id="adresse_mail" name="adresse_mail" required>
                            </div>
                            <div class="form-champ">
                                <label for="pseudo">PSEUDO</label>
                                <input type="text" id="pseudo" name="pseudo" required>
                            </div>
                            <div class="form-champ">
                                <label for="mdp">MOT DE PASSE</label>
                                <input type="password" id="mdp" name="mdp" minlength="8" required>
                                <span class="form-indication">8 CARACTÈRES MINIMUM</span>
                            </div>
                            <button type="submit" class="bouton-principal">CRÉER MON COMPTE</button>
                            <p class="form-mentions">EN VOUS INSCRIVANT, VOUS ACCEPTEZ LES CONDITIONS D'UTILISATION ET LA POLITIQUE DE CONFIDENTIALITÉ</p>
                        </form>

                        <hr>
                        <p>OU</p>
                        <a href="profil.php?etape=connexion" class="bouton-secondaire">J'AI DÉJÀ UN COMPTE</a>
                    </section>

                <?php endif; ?>

            <?php else : ?>

                <?php if (isset($_GET['parametres'])) : ?>

                    <!-- VUE PARAMÈTRES -->
                    <section class="profil-banniere">
                        <img src="<?= htmlspecialchars($profil['banniere_profil'] ?? 'placeholder_banniere') ?>" alt="bannière">
                        <div class="profil-identite">
                            <img src="<?= htmlspecialchars($profil['photo_profil'] ?? 'placeholder_photo') ?>" alt="photo de profil">
                            <div>
                                <p class="profil-pseudo"><?= htmlspecialchars($_SESSION['user']['pseudo_compte']) ?></p>
                                <p class="profil-nb-suivies"><?= $nb_suivies ?> RADIOS SUIVIES</p>
                            </div>
                        </div>
                    </section>

                    <section class="parametres">
                        <form method="POST" action="profil.php?parametres" id="form-parametres">
                            <input type="hidden" name="action" value="parametres">

                            <div class="parametres-photo">
                                <p>PHOTO DE PROFIL</p>
                                <!-- Les photos sont des choix prédéfinis -->
                                <div class="parametres-photos-liste">
                                    <label>
                                        <input type="radio" name="photo_profil" value="images/profil/photo1.png">
                                        <img src="images/profil/photo1.png" alt="photo 1">
                                    </label>
                                    <label>
                                        <input type="radio" name="photo_profil" value="images/profil/photo2.png">
                                        <img src="images/profil/photo2.png" alt="photo 2">
                                    </label>
                                    <label>
                                        <input type="radio" name="photo_profil" value="images/profil/photo3.png">
                                        <img src="images/profil/photo3.png" alt="photo 3">
                                    </label>
                                    <label>
                                        <input type="radio" name="photo_profil" value="images/profil/photo4.png">
                                        <img src="images/profil/photo4.png" alt="photo 4">
                                    </label>
                                </div>
                            </div>

                            <div class="parametres-banniere">
                                <p>BANNIERE</p>
                                <div class="parametres-bannieres-liste">
                                    <label>
                                        <input type="radio" name="banniere_profil" value="images/bannieres/banniere1.png">
                                        <img src="images/bannieres/banniere1.png" alt="bannière 1">
                                    </label>
                                    <label>
                                        <input type="radio" name="banniere_profil" value="images/bannieres/banniere2.png">
                                        <img src="images/bannieres/banniere2.png" alt="bannière 2">
                                    </label>
                                    <label>
                                        <input type="radio" name="banniere_profil" value="images/bannieres/banniere3.png">
                                        <img src="images/bannieres/banniere3.png" alt="bannière 3">
                                    </label>
                                </div>
                            </div>

                            <div class="parametres-infos">
                                <p>INFORMATIONS PERSONNELLES</p>
                                <div class="form-champ">
                                    <label for="pseudo">PSEUDO</label>
                                    <input type="text" id="pseudo" name="pseudo" value="<?= htmlspecialchars($_SESSION['user']['pseudo_compte']) ?>" required>
                                </div>
                                <div class="form-champ">
                                    <label for="mail">E-MAIL</label>
                                    <input type="email" id="mail" name="mail" value="<?= htmlspecialchars($_SESSION['user']['adresse_compte']) ?>" required>
                                </div>
                            </div>

                            <div class="parametres-boutons">
                                <a href="profil.php" class="bouton-secondaire">ANNULER</a>
                                <button type="submit" class="bouton-principal">VALIDER</button>
                            </div>
                        </form>
                    </section>

                <?php else : ?>

                    <!-- VUE PROFIL PRINCIPAL -->
                    <section class="profil-banniere">
                        <img src="<?= htmlspecialchars($profil['banniere_profil'] ?? 'placeholder_banniere') ?>" alt="bannière">
                        <div class="profil-identite">
                            <img src="<?= htmlspecialchars($profil['photo_profil'] ?? 'placeholder_photo') ?>" alt="photo de profil">
                            <div>
                                <p class="profil-pseudo"><?= htmlspecialchars($_SESSION['user']['pseudo_compte']) ?></p>
                                <p class="profil-nb-suivies"><?= $nb_suivies ?> RADIOS SUIVIES</p>
                            </div>
                        </div>
                        <a href="profil.php?parametres" class="bouton-parametres"><img src="placeholder_engrenage" alt="paramètres"></a>
                    </section>

                    <!-- ONGLETS -->
                    <nav class="profil-onglets">
                        <a href="profil.php?onglet=abonnements" class="<?= $onglet === 'abonnements' ? 'current' : '' ?>">MES ABONNEMENTS</a>
                        <a href="profil.php?onglet=historique"  class="<?= $onglet === 'historique'  ? 'current' : '' ?>">HISTORIQUE</a>
                        <a href="profil.php?onglet=commentaires" class="<?= $onglet === 'commentaires' ? 'current' : '' ?>">COMMENTAIRES</a>
                    </nav>

                    <?php if ($onglet === 'abonnements') : ?>

                        <section class="profil-abonnements">
                            <div class="profil-abonnements-liste">
                                <?php foreach ($abonnements as $radio) : ?>
                                    <div class="radio-carte">
                                        <img src="<?= htmlspecialchars($radio['image_radio']) ?>" alt="<?= htmlspecialchars($radio['nom_radio']) ?>">
                                        <p class="radio-nom"><?= htmlspecialchars($radio['nom_radio']) ?></p>
                                        <p class="radio-pays"><?= htmlspecialchars($radio['localisation_radio']) ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </section>

                    <?php elseif ($onglet === 'historique') : ?>

                        <section class="profil-historique">
                            <div class="profil-historique-liste">
                                <?php foreach ($historique as $ecoute) : ?>
                                    <div class="radio-carte">
                                        <img src="<?= htmlspecialchars($ecoute['image_radio']) ?>" alt="<?= htmlspecialchars($ecoute['nom_radio']) ?>">
                                        <p class="radio-nom"><?= htmlspecialchars($ecoute['nom_radio']) ?></p>
                                        <p class="radio-pays"><?= htmlspecialchars($ecoute['localisation_radio']) ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </section>

                    <?php elseif ($onglet === 'commentaires') : ?>

                        <section class="profil-commentaires">
                            <div class="profil-commentaires-liste">
                                <?php foreach ($commentaires_user as $commentaire) :
                                    $estLike = $radios->estLike($id_compte, $commentaire['id_commentaire']);
                                ?>
                                    <div class="commentaire-carte">
                                        <img src="<?= htmlspecialchars($commentaire['image_radio']) ?>" alt="<?= htmlspecialchars($commentaire['nom_radio']) ?>">
                                        <div class="commentaire-carte-infos">
                                            <p class="commentaire-radio"><?= htmlspecialchars($commentaire['nom_radio']) ?></p>
                                            <p class="commentaire-date"><?= htmlspecialchars($commentaire['date_commentaire']) ?></p>
                                            <p class="commentaire-contenu"><?= htmlspecialchars($commentaire['contenu_commentaire']) ?></p>
                                            <div class="commentaire-actions">
                                                <a href="profil.php?onglet=commentaires&modifier=<?= $commentaire['id_commentaire'] ?>">MODIFIER</a>
                                                <a href="profil.php?onglet=commentaires&supprimer_commentaire=<?= $commentaire['id_commentaire'] ?>">SUPPRIMER</a>
                                            </div>
                                        </div>
                                        <a href="profil.php?onglet=commentaires&like=<?= $commentaire['id_commentaire'] ?>" class="commentaire-like <?= $estLike ? 'like-actif' : '' ?>">
                                            <?= $commentaire['nb_likes'] ?>
                                            <img src="placeholder_like" alt="like">
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </section>

                    <?php endif; ?>

                <?php endif; ?>

            <?php endif; ?>
        </main>

        <?php include 'composants/lecteur.php'; ?>
        <?php include 'composants/footer.php'; ?>
    </body>
</html>