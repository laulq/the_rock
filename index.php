<?php
require_once 'composants/init.php';

// Traitement du follow avant tout affichage
if (isset($_GET['follow']) && isset($_SESSION['user'])) {
    $radios->toggleFollow($_SESSION['user']['id_compte'], (int) $_GET['follow']);
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>The Rock | LE site 100% Rock</title>
    <meta name="description" content="Découvrez les meilleures radios rock en France et à l'international.">

    <link rel="stylesheet" href="./css/styles.css">
    <script src="scripts/carrousel.js" type="module"></script>
</head>

<body>
    <?php include 'composants/header.php'; ?>

    <main>
        <section id="carrousel" class="hero">
            <div id="carrousel-wrapper">
                <div class="carrousel-slide-infos">
                    <h1>THE ROCK</h1>
                    <p class="carrousel-slide-soustitre">LE MEILLEUR DU ROCK ET MÉTAL</p>
                    <span class="texte">
                        <p>Découvrez les meilleurs radios en France comme à l'international.</p>
                        <p>Du rock alternatif au hardmétal, trouvez votre bonheur.</p>
                    </span>
                </div>

                <div class="carrousel-slide current">
                    <img src="./images/illus/banniere_1_mobile.webp" alt="placeholder">
                </div>
                <div class="carrousel-slide">
                    <img src="./images/illus/banniere_2_mobile.webp" alt="placeholder">
                </div>
                <div class="carrousel-slide">
                    <img src="./images/illus/banniere_3_mobile.webp" alt="placeholder">
                </div>
                <div class="carrousel-slide">
                    <img src="./images/illus/banniere_4_mobile.webp" alt="placeholder">
                </div>

                <div id="carrousel-points">
                    <span class="carrousel-point current"></span>
                    <span class="carrousel-point"></span>
                    <span class="carrousel-point"></span>
                    <span class="carrousel-point"></span>
                </div>
            </div>

            <div id="carrousel-cta">
                <?php if (!isset($_SESSION['user'])): ?>
                    <div class="carrousel-cta-slide current">
                        <a href="profil.php" class="bouton-principal">SE CONNECTER</a>
                        <a href="profil.php" class="bouton-secondaire">S'INSCRIRE</a>
                    </div>
                <?php else: ?>
                    <div class="carrousel-cta-slide current">
                        <a href="liste.php" class="bouton-principal">PARCOURIR LES RADIOS</a>
                    </div>
                <?php endif; ?>

                <!-- "?genre" pas définitif -->
                <div class="carrousel-cta-slide">
                    <a href="liste.php?genre=metal" class="bouton-principal">DÉCOUVREZ NOS RADIOS MÉTAL</a>
                </div>
                <div class="carrousel-cta-slide">
                    <a href="liste.php?genre=alternatif" class="bouton-principal">DÉCOUVREZ NOS RADIOS ALTERNATIF</a>
                </div>
                <div class="carrousel-cta-slide">
                    <a href="liste.php?genre=hardrock" class="bouton-principal">DÉCOUVREZ NOS RADIOS HARDROCK</a>
                </div>
            </div>
        </section>

        <?php if (!isset($_SESSION['user'])): ?>

            <section id="top-radios" class="top-radios">
                <h2>TOP DU MOMENT</h2>

                <div id="top-radios-liste">
                    <?php
                    $top_radios = $radios->getTopRadios();
                    $rang = 1;
                    foreach ($top_radios as $radio): ?>
                        <a href="radio.php?id=<?= $radio['id_radio'] ?>" class="radio-carte">
                            <span class="radio-rang"><?= $rang ?></span>
                            <img src="<?= htmlspecialchars($radio['image_radio']) ?>"
                                alt="<?= htmlspecialchars($radio['nom_radio']) ?>">
                            <p class="radio-nom"><?= htmlspecialchars($radio['nom_radio']) ?></p>
                            <p class="radio-pays"><?= htmlspecialchars($radio['localisation_radio']) ?></p>
                        </a>
                        <?php $rang++; endforeach; ?>
                </div>

                <a href="liste.php" class="bouton-principal">DÉCOUVRIR TOUTES NOS RADIOS</a>
            </section>

            <section id="cta-connexion">
                <div id="cta-connexion-texte">
                    <h2>VOUS EN VOULEZ PLUS ?</h2>
                    <img src="./images/illus/rock_illu_mobile.svg" alt="illustration">
                    <p>Connectez-vous, et profitez de plus de 20 radios internationales.</p>
                    <p>Sauvegardez vos radios, commentez et passez un bon moment !</p>
                    <div id="cta-connexion-boutons">
                        <a href="profil.php" class="bouton-principal">SE CONNECTER</a>
                        <a href="profil.php" class="bouton-secondaire">S'INSCRIRE</a>
                    </div>
                </div>
            </section>

        <?php else: ?>

            <section id="dernieres-ecoutes">
                <h2>MES DERNIÈRES ÉCOUTES</h2>

                <div id="dernieres-ecoutes-liste">
                    <?php
                    $dernieres_ecoutes = $radios->getDernieresEcoutes($_SESSION['user']['id_compte']);
                    foreach ($dernieres_ecoutes as $ecoute): ?>
                        <a href="radio.php?id=<?= $ecoute['id_radio'] ?>" class="radio-carte">
                            <img src="<?= htmlspecialchars($ecoute['image_radio']) ?>"
                                alt="<?= htmlspecialchars($ecoute['nom_radio']) ?>">
                            <p class="radio-nom"><?= htmlspecialchars($ecoute['nom_radio']) ?></p>
                            <p class="radio-pays"><?= htmlspecialchars($ecoute['localisation_radio']) ?></p>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>

            <section id="recommandations">
                <h2>RECOMMANDATIONS</h2>

                <div id="recommandations-liste">
                    <?php
                    $recommandations = $radios->getRecommandations($_SESSION['user']['id_compte']);
                    foreach ($recommandations as $radio): ?>
                        <a href="radio.php?id=<?= $radio['id_radio'] ?>" class="radio-carte">
                            <img src="<?= htmlspecialchars($radio['image_radio']) ?>"
                                alt="<?= htmlspecialchars($radio['nom_radio']) ?>">
                            <p class="radio-nom"><?= htmlspecialchars($radio['nom_radio']) ?></p>
                            <p class="radio-pays"><?= htmlspecialchars($radio['localisation_radio']) ?></p>
                        </a>
                    <?php endforeach; ?>

                    <!-- Carte "suivante" avec bouton play -->
                    <div class="radio-carte radio-carte-suivante">
                        <img src="placeholder_radio_suivante" alt="placeholder">
                        <a href="#" class="bouton-play"><img src="placeholder_bouton_play" alt="play"></a>
                    </div>
                </div>

                <a href="mes-radios.php" class="bouton-principal">ACCÉDER À MES RADIOS</a>
            </section>

            <aside id="tendances">
                <h2>TENDANCES</h2>

                <div id="tendances-liste">
                    <?php
                    $tendances = $radios->getTendances();
                    foreach ($tendances as $radio):
                        $estSuivie = $radios->estSuivie($_SESSION['user']['id_compte'], $radio['id_radio']);
                    ?>
                        <div class="tendance-carte">
                            <a href="radio.php?id=<?= $radio['id_radio'] ?>" class="tendance-carte-lien">
                                <img src="<?= htmlspecialchars($radio['image_radio']) ?>"
                                    alt="<?= htmlspecialchars($radio['nom_radio']) ?>">
                                <div class="tendance-carte-infos">
                                    <p class="radio-nom"><?= htmlspecialchars($radio['nom_radio']) ?></p>
                                    <p class="radio-abonnes"><?= $radio['nb_abonnes'] ?> ABONNÉS</p>
                                </div>
                            </a>
                            <a href="?follow=<?= $radio['id_radio'] ?>" class="bouton-suivre <?= $estSuivie ? 'suivie' : '' ?>">
                                <?= $estSuivie ? 'SUIVIE' : 'SUIVRE +' ?>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </aside>

        <?php endif; ?>
    </main>

    <?php include 'composants/lecteur.php'; ?>
    <?php include 'composants/footer.php'; ?>
</body>

</html>