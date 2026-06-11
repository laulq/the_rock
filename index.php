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
        <section class="carrousel" class="hero">
            <div class="carrousel-wrapper">
                <div class="carrousel-slide-infos">
                    <h1 class="carrousel-slide-soustitre">THE ROCK</h1>
                    <p class="carrousel-slide-soustitre">LE MEILLEUR DU ROCK ET MÉTAL</p>
                    <p class="texte">Découvrez les meilleurs radios à l'international.</p>
                    <p class="texte only-desktop">Du rock alternatif au hardmétal, trouvez votre bonheur.</p>
                </div>

                <div class="carrousel-slide current">
                    <picture>
                        <source media="(min-width: 768px)" srcset="./images/illus/banniere_1_desktop.webp">
                        <img src="./images/illus/banniere_1_mobile.webp" alt="Photo de Linkin Park en concert">
                    </picture>
                </div>
                <div class="carrousel-slide">
                    <picture>
                        <source media="(min-width: 768px)" srcset="./images/illus/banniere_2_desktop.webp">
                        <img src="./images/illus/banniere_2_mobile.webp" alt="Bannière rock 2">
                    </picture>
                </div>
                <div class="carrousel-slide">
                    <picture>
                        <source media="(min-width: 768px)" srcset="./images/illus/banniere_3_desktop.webp">
                        <img src="./images/illus/banniere_3_mobile.webp" alt="Bannière rock 3">
                    </picture>
                </div>
                <div class="carrousel-slide">
                    <picture>
                        <source media="(min-width: 768px)" srcset="./images/illus/banniere_4_desktop.webp">
                        <img src="./images/illus/banniere_4_mobile.webp" alt="Bannière rock 4">
                    </picture>
                </div>

                <div class="carrousel-points" role="tablist" aria-label="Slides du carrousel">
                    <button class="carrousel-point current" role="tab" aria-selected="true"
                        aria-label="Slide 1"></button>
                    <button class="carrousel-point" role="tab" aria-selected="false" aria-label="Slide 2"></button>
                    <button class="carrousel-point" role="tab" aria-selected="false" aria-label="Slide 3"></button>
                    <button class="carrousel-point" role="tab" aria-selected="false" aria-label="Slide 4"></button>
                </div>
            </div>

            <div class="carrousel-cta">
                <?php if (!isset($_SESSION['user'])): ?>
                    <div class="carrousel-cta-slide current">
                        <a href="profil.php" class="bouton-principal carrousel-slide-infos">SE CONNECTER</a>
                        <a href="inscription.php" class="bouton-secondaire only-desktop">S'INSCRIRE</a>
                    </div>
                <?php else: ?>
                    <div class="carrousel-cta-slide current">
                        <a href="liste.php" class="bouton-principal">PARCOURIR LES RADIOS</a>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <?php if (!isset($_SESSION['user'])): ?>

            <section class="section-radios">
                <div class="header-top-radios">
                    <h2 class="titre">TOP DU MOMENT</h2>
                    <a href="liste.php" class="bouton-principal only-mobile">VOIR TOUT</a>
                </div>

                <ul class="scroll-radios-liste">
                    <?php
                    $top_radios = $radios->getTopRadios();
                    $rang = 1;
                    foreach ($top_radios as $radio): ?>
                        <li>
                            <a href="radio.php?id=<?= $radio['id_radio'] ?>" class="radio-carte">
                                <span class="radio-rang"><?= $rang ?></span>
                                <img class="top-carte carte" src="<?= htmlspecialchars($radio['image_radio']) ?>"
                                    alt="<?= htmlspecialchars($radio['nom_radio']) ?>">
                                <p class="radio-nom"><?= htmlspecialchars($radio['nom_radio']) ?></p>
                                <p class="radio-pays"><?= htmlspecialchars($radio['localisation_radio']) ?></p>
                            </a>
                        </li>
                        <?php $rang++; endforeach; ?>
                </ul>
                <div class="fleches-navigation">
                    <a href="#" class="bouton-nav-left"><img src="./images/icônes/phone_arrow_left.svg"
                            alt="parcourez les radios"></a>
                    <a href="#" class="bouton-nav-right"><img src="./images/icônes/phone_arrow_right.svg"
                            alt="parcourez les radios"></a>
                </div>
                <a href="liste.php" class="bouton-principal only-desktop">DÉCOUVRIR TOUTES NOS RADIOS</a>
            </section>

            <section>
                <h2 class="titre">VOUS EN VOULEZ PLUS ?</h2>
                <div class="cta-connexion-global">
                    <div class="illu-texte">
                        <img class="illu-connexion" src="./images/illus/rock_illu_mobile.svg" alt="illustration">
                        <ul>
                            <li class="texte">Connectez-vous, et profitez de plus de 20 radios internationales.</li>
                            <li class="texte">Sauvegardez vos radios, commentez et passez un bon moment !</li>
                        </ul>
                    </div>
                    <ul class="cta-connexion-boutons">
                        <li><a href="profil.php" class="bouton-principal">SE CONNECTER</a></li>
                        <li><a href="inscription.php" class="bouton-secondaire">S'INSCRIRE</a></li>
                    </ul>
                </div>
            </section>

        <?php else: ?>

            <section class="section-radios">
                <h2 class="titre">MES DERNIÈRES ÉCOUTES</h2>

                <ul class="scroll-radios-liste">
                    <?php
                    $dernieres_ecoutes = $radios->getDernieresEcoutes($_SESSION['user']['id_compte']);
                    foreach ($dernieres_ecoutes as $ecoute): ?>
                        <li>
                            <a href="radio.php?id=<?= $ecoute['id_radio'] ?>" class="radio-carte">
                                <img class="carte" src="<?= htmlspecialchars($ecoute['image_radio']) ?>"
                                    alt="<?= htmlspecialchars($ecoute['nom_radio']) ?>">
                                <p class="radio-nom"><?= htmlspecialchars($ecoute['nom_radio']) ?></p>
                                <p class="radio-pays"><?= htmlspecialchars($ecoute['localisation_radio']) ?></p>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </section>

            <section class="recommandations">
                <h2 class="titre">RECOMMANDATIONS</h2>

                <ul class="scroll-radios-liste">
                    <?php
                    $recommandations = $radios->getRecommandations($_SESSION['user']['id_compte']);
                    foreach ($recommandations as $radio): ?>
                        <li>
                            <a href="radio.php?id=<?= $radio['id_radio'] ?>" class="radio-carte">
                                <img class="carte" src="<?= htmlspecialchars($radio['image_radio']) ?>"
                                    alt="<?= htmlspecialchars($radio['nom_radio']) ?>">
                                <p class="radio-nom"><?= htmlspecialchars($radio['nom_radio']) ?></p>
                                <p class="radio-pays"><?= htmlspecialchars($radio['localisation_radio']) ?></p>
                            </a>
                        </li>
                    <?php endforeach; ?>

                </ul>

                <a href="mes-radios.php" class="bouton-principal">ACCÉDER À MES RADIOS</a>
            </section>

            <aside class="tendances">
                <h2 class="titre">TENDANCES</h2>

                <ul class="tendances-liste">
                    <?php
                    $tendances = $radios->getTendances();
                    foreach ($tendances as $radio):
                        $estSuivie = $radios->estSuivie($_SESSION['user']['id_compte'], $radio['id_radio']);
                        ?>
                        <li class="tendance-carte">
                            <a href="radio.php?id=<?= $radio['id_radio'] ?>" class="tendance-carte-lien">
                                <img class="carte" src="<?= htmlspecialchars($radio['image_radio']) ?>"
                                    alt="<?= htmlspecialchars($radio['nom_radio']) ?>">
                                <div class="tendance-carte-infos">
                                    <p class="radio-nom"><?= htmlspecialchars($radio['nom_radio']) ?></p>
                                    <p class="radio-abonnes"><?= $radio['nb_abonnes'] ?> ABONNÉS</p>
                                </div>
                            </a>
                            <a href="?follow=<?= $radio['id_radio'] ?>"
                                class="bouton-principal <?= $estSuivie ? 'suivie' : '' ?>">
                                <?= $estSuivie ? 'SUIVIE' : 'SUIVRE +' ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </aside>

        <?php endif; ?>
    </main>

    <?php include 'composants/lecteur.php'; ?>
    <?php include 'composants/footer.php'; ?>
</body>

</html>