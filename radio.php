<?php
require_once 'composants/init.php';

// Redirection si pas d'id
if (!isset($_GET['id'])) {
    header('Location: liste.php');
    exit;
}

$id_radio = (int)$_GET['id'];
$radio    = $radios->getRadio($id_radio);

// Redirection si pas de radio
if (!$radio) {
    header('Location: liste.php');
    exit;
}

// Traitement des actions avant tout affichage (même méthode que profil)
if (isset($_SESSION['user'])) {
    $id_compte = $_SESSION['user']['id_compte'];

    // Toggle favori
    if (isset($_GET['favori'])) {
        $radios->toggleFavori($id_compte, $id_radio);
        header('Location: radio.php?id=' . $id_radio);
        exit;
    }

    // Ajout commentaire
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'commenter') {
        $salon = $radios->getSalonRadio($id_radio);
        if ($salon) {
            $radios->ajouterCommentaire($id_compte, $salon['id_salon'], htmlspecialchars($_POST['contenu']));
        }
        header('Location: radio.php?id=' . $id_radio);
        exit;
    }

    // Toggle like commentaire
    if (isset($_GET['like'])) {
        $radios->toggleLikeCommentaire($id_compte, (int)$_GET['like']);
        header('Location: radio.php?id=' . $id_radio);
        exit;
    }
}

// On charge les données
$tags          = $radios->getTagsRadio($id_radio);
$salon         = $radios->getSalonRadio($id_radio);
$commentaires  = $salon ? $radios->getCommentaires($salon['id_salon']) : [];
$suggestions   = $radios->getSuggestions($id_radio);
$nbAbonnes    = $radios->getNbAbonnes($id_radio);
$estFavori     = isset($_SESSION['user']) ? $radios->estFavori($_SESSION['user']['id_compte'], $id_radio) : false;
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>The Rock | <?= htmlspecialchars($radio['nom_radio']) ?></title> <!-- le titre de la page change selon le nom de la radio -->
        <meta name="description" content="a compléter">

        <link rel="stylesheet" href="./css/styles.css">
    </head>

    <body>
        <?php include 'composants/header.php'; ?>

        <main>
            <div class="radio-page">

                <div class="radio-contenu">

                    <!-- EN-TÊTE RADIO -->
                    <section class="radio-entete">
                        <img class="carte" src="<?= htmlspecialchars($radio['image_radio'] ?? 'placeholder_radio') ?>" alt="<?= htmlspecialchars($radio['nom_radio']) ?>">

                        <div class="radio-entete-infos">
                            <h1><?= htmlspecialchars($radio['nom_radio']) ?></h1>
                            <p class="radio-abonnes"><?= $nbAbonnes ?> ABONNÉS</p>

                            <div class="radio-actions">

                            <!-- Favori -->
                                <?php if (isset($_SESSION['user'])) : ?>
                                    <a href="radio.php?id=<?= $id_radio ?>&favori=1" id="bouton-favori" class="<?= $estFavori ? 'actif' : '' ?>">
                                        <img src="./images/icônes/favorite.svg" alt="favori">
                                    </a>
                                <?php else : ?>
                                    <a href="profil.php" class="bouton-favori">
                                        <img src="./images/icônes/favorite.svg" alt="favori">
                                    </a>
                                <?php endif; ?>

                                <!-- Bouton play -->
                                <a href="#" class="bouton-play-radio"><img src="./images/icônes/PLAY_red_button.svg" alt="play"></a>

                                <!-- Partage -->
                                <a href="#" class="bouton-partage">
                                    <img src="./images/icônes/share.svg" alt="partager">
                                </a>

                                <!-- Tags -->
                                <div class="radio-tags">
                                    <?php foreach ($tags as $tag) : ?>
                                        <a href="liste.php?tag=<?= htmlspecialchars($tag['nom_tag']) ?>" class="radio-tag">
                                            <img src="./images/icônes/tag.svg" alt="tag">
                                            <?= htmlspecialchars(strtoupper($tag['nom_tag'])) ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- À PROPOS -->
                    <section class="radio-apropos">
                        <h2 class="sous-titre">A PROPOS</h2>
                        <p><?= htmlspecialchars($radio['slogan_radio'] ?? 'Aucune description disponible.') ?></p>
                    </section>

                    <!-- COMMENTAIRES -->
                    <section class="radio-commentaires">
                        <h2 class="titre">COMMENTAIRES</h2>

                        <!-- Formulaire d'ajout (à modifier) -->
                        <?php if (isset($_SESSION['user'])) : ?>
                            <div class="commentaire-form">
                                <img src="<?= htmlspecialchars($profil['photo_profil'] ?? 'placeholder_photo') ?>" alt="photo de profil">
                                <form method="POST" action="radio.php?id=<?= $id_radio ?>">
                                    <input type="hidden" name="action" value="commenter">
                                    <input type="text" name="contenu" placeholder="Écrire un commentaire" required>
                                    <button type="submit" class="bouton-principal">PUBLIER</button>
                                </form>
                            </div>
                        <?php endif; ?>

                        <!-- Liste des commentaires -->
                        <div class="commentaires-liste">
                            <?php foreach ($commentaires as $commentaire) :
                                $estLike = isset($_SESSION['user'])
                                    ? $radios->estLike($_SESSION['user']['id_compte'], $commentaire['id_commentaire'])
                                    : false;
                            ?>
                                <div class="commentaire-carte">
                                    <img src="placeholder_photo_profil" alt="<?= htmlspecialchars($commentaire['pseudo_compte']) ?>">
                                    <div class="commentaire-carte-infos">
                                        <p class="commentaire-pseudo"><?= htmlspecialchars($commentaire['pseudo_compte']) ?></p>
                                        <p class="commentaire-date"><?= htmlspecialchars($commentaire['date_commentaire']) ?></p>
                                        <p class="commentaire-contenu"><?= htmlspecialchars($commentaire['contenu_commentaire']) ?></p>
                                    </div>
                                    <a href="radio.php?id=<?= $id_radio ?>&like=<?= $commentaire['id_commentaire'] ?>"
                                       class="commentaire-like <?= $estLike ? 'like-actif' : '' ?>">
                                        <?= $commentaire['nb_likes'] ?>
                                        <img src="placeholder_like" alt="like">
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>

                </div>

                <!-- SUGGESTIONS -->
                <aside class="radio-suggestions">
                    <h2>VOUS AIMEREZ AUSSI...</h2>
                    <div class="radio-suggestions-liste">
                        <?php foreach ($suggestions as $suggestion) : ?>
                            <a href="radio.php?id=<?= $suggestion['id_radio'] ?>" class="suggestion-carte">
                                <img src="<?= htmlspecialchars($suggestion['image_radio'] ?? 'placeholder_radio') ?>" alt="<?= htmlspecialchars($suggestion['nom_radio']) ?>">
                                <p class="radio-nom"><?= htmlspecialchars(strtoupper($suggestion['nom_radio'])) ?></p>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <a href="liste.php" class="bouton-principal">VOIR TOUT</a>
                </aside>

            </div>
        </main>

        <?php include 'composants/lecteur.php'; ?>
        <?php include 'composants/footer.php'; ?>
    </body>
</html>