<?php require_once 'composants/init.php'; ?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>The Rock | Nos radios</title>
    <meta name="description" content="a compléter">

    <link rel="stylesheet" href="./css/styles.css">
    <script src="scripts/carrousel.js" type="module"></script>
    <script src="scripts/filtre.js" type="module"></script>
</head>

<body>
    <?php include 'composants/header.php'; ?>
    <main>

        <?php if (!isset($_SESSION['user'])): ?><!-- Quand l'utilisateur n'est pas connecté !-->

            <section class="top-radios">
                <h1 class="titre">TOP DU MOMENT</h1>

                <div class="top-radios-liste">
                    <?php
                    $top_radios = $radios->getTopRadios();
                    $rang = 1;
                    foreach ($top_radios as $radio): ?>
                        <div class="radio-carte">
                            <span class="radio-rang"><?= $rang ?></span>
                            <img src="<?= htmlspecialchars($radio['image_radio']) ?>"
                                alt="<?= htmlspecialchars($radio['nom_radio']) ?>">
                            <p class="radio-nom"><?= htmlspecialchars($radio['nom_radio']) ?></p>
                            <p class="radio-pays"><?= htmlspecialchars($radio['localisation_radio']) ?></p>
                        </div>
                        <?php $rang++; endforeach; ?>
                </div>
            </section>
            <section class="categories">
                <h1 class="titre">CATÉGORIES</h1>
                <ul class="choisir_categorie">
                    <li><button><img src="./images/illus/hard_rock_selection.webp" alt="Sélection de radios hard rock"></button></li>
                    <li><button><img src="./images/illus/metal_selection.webp" alt="Sélection de radios metal"></button></li>
                    <li><button><img src="./images/illus/alternatif_selection.webp" alt="Sélection de radios alternatif"></button></li>
                </ul>
                <div class="filtres-radios">
                    <h2 class="sous-titre">FILTRER PAR</h2>
                    <div>
                        <button>
                            <img src="./images/icônes/medal_white.svg" alt="">
                            POPULARITÉ
                        </button>

                        <div class="dropdown">
                            <button class="dropdown-btn">
                                <img src="./images/icônes/tag.svg" alt="">
                                TAG
                            </button>
                            <ul class="dropdown-menu">
                                <li><button>GRUNGE</button></li>
                                <li><button>POP ROCK</button></li>
                                <li><button>90's</button></li>
                                <li><button>2000's</button></li>
                                <li><button>2010's</button></li>
                            </ul>
                        </div>

                        <button>
                            <img src="./images/icônes/AZ.svg" alt="">
                            NOM
                        </button>

                        <div class="dropdown">
                            <button class="dropdown-btn">
                                <img src="./images/icônes/earth.svg" alt="">
                                PAYS
                            </button>
                            <ul class="dropdown-menu">
                                <li><button>FRANCE</button></li>
                                <li><button>ALLEMAGNE</button></li>
                                <li><button>ESPAGNE</button></li>
                                <li><button>ROYAUME UNI</button></li>
                                <li><button>QUÉBEC</button></li>
                                <li><button>JAPON</button></li>
                                <li><button>ITALIE</button></li>
                                <li><button>ÉTATS UNIS</button></li>
                            </ul>
                        </div>
                    </div>
                </div>
                </div>
                <ul class="radios-liste"></ul>
                <?php
                $liste_radios = $radios->getRadios();
                $rang = 1;
                foreach ($liste_radios as $radio): ?>
                    <li class="radio-carte">
                        <img src="<?= htmlspecialchars($radio['image_radio']) ?>"
                            alt="<?= htmlspecialchars($radio['nom_radio']) ?>">
                        <p class="radio-nom">
                            <?= htmlspecialchars($radio['nom_radio']) ?>
                        </p>
                        <p class="radio-pays">
                            <?= htmlspecialchars($radio['localisation_radio']) ?>
                        </p>
                    </li>
                    <?php $rang++; endforeach; ?>
            </section>

        <?php else: ?><!--Quand l'utilisateur est connecté!-->
            <section class="mes-radios">
                <h1 class="titre">MES RADIOS</h1>
                <ul class="mes-radios-liste">
                    <?php
                    $mes_radios = $radios->getRadiosUtilisateur($_SESSION['user']['id_compte']);
                    foreach ($mes_radios as $radio): ?>
                        <li class="radio-carte">
                            <img src="<?= htmlspecialchars($radio['image_radio']) ?>"
                                alt="<?= htmlspecialchars($radio['nom_radio']) ?>">
                            <p class="radio-nom">
                                <?= htmlspecialchars($radio['nom_radio']) ?>
                            </p>
                            <p class="radio-pays">
                                <?= htmlspecialchars($radio['localisation_radio']) ?>
                            </p>
                        </li>
                    <?php endforeach; ?>

                    <!-- Carte "suivante" avec bouton play -->
                    <li class="radio-carte radio-carte-suivante">
                        <img src="placeholder_radio_suivante" alt="placeholder">
                        <a href="#" class="bouton-play"><img src="placeholder_bouton_play" alt="play"></a>
                    </li>
                </ul>
                <ul class="recommandations-liste">
                    <?php
                    $recommandations = $radios->getRecommandations($_SESSION['user']['id_compte']);
                    foreach ($recommandations as $radio): ?>
                        <li class="radio-carte">
                            <img src="<?= htmlspecialchars($radio['image_radio']) ?>"
                                alt="<?= htmlspecialchars($radio['nom_radio']) ?>">
                            <p class="radio-nom">
                                <?= htmlspecialchars($radio['nom_radio']) ?>
                            </p>
                            <p class="radio-pays">
                                <?= htmlspecialchars($radio['localisation_radio']) ?>
                            </p>
                        </li>
                    <?php endforeach; ?>

                    <!-- Carte "suivante" avec bouton play -->
                    <li class="radio-carte radio-carte-suivante">
                        <img src="placeholder_radio_suivante" alt="placeholder">
                        <a href="#" class="bouton-play"><img src="placeholder_bouton_play" alt="play"></a>
                    </li>
                </ul>
                <a href="#top-radios" class="bouton-principal">DÉCOUVRIR TOUTES NOS RADIOS</a>
            </section>
            <section class="top-radios">
                <h1 class="titre">TOP DU MOMENT</h1>

                <ul class="top-radios-liste">
                    <?php
                    $top_radios = $radios->getTopRadios();
                    $rang = 1;
                    foreach ($top_radios as $radio): ?>
                        <li class="radio-carte">
                            <span class="radio-rang">
                                <?= $rang ?>
                            </span>
                            <img src="<?= htmlspecialchars($radio['image_radio']) ?>"
                                alt="<?= htmlspecialchars($radio['nom_radio']) ?>">
                            <p class="radio-nom">
                                <?= htmlspecialchars($radio['nom_radio']) ?>
                            </p>
                            <p class="radio-pays">
                                <?= htmlspecialchars($radio['localisation_radio']) ?>
                            </p>
                        </div>
                        <?php $rang++; endforeach; ?>
                </div>
            </section>
            <section class="categories">
                <h1 class="titre">CATÉGORIES</h1>
                <div class="choisir_categorie"></div>
                <button><img src="./images/illus/hard_rock_selection.webp" alt="Sélection de radios hard rock"></button>
                <button><img src="./images/illus/metal_selection.webp" alt="Sélection de radios metal"></button>
                <button><img src="./images/illus/alternatif_selection.webp" alt="Sélection de radios alternatif"></button>
                <div class="filtres-radios">
                    <h2 class="sous-titre">FILTRER PAR</h2>
                    <div>
                        <button>
                            <img src="./images/icônes/popularite.svg" alt="">
                            POPULARITÉ
                        </button>

                        <div class="dropdown">
                            <button class="dropdown-btn">
                                <img src="./images/icônes/tag.svg" alt="">
                                TAG
                                <span class="dropdown-arrow">▾</span>
                            </button>
                            <ul class="dropdown-menu">
                                <li><button>GRUNGE</button></li>
                                <li><button>POP ROCK</button></li>
                                <li><button>90's</button></li>
                                <li><button>2000's</button></li>
                                <li><button>2010's</button></li>
                            </ul>
                        </div>

                        <button>
                            <img src="./images/icônes/AZ.svg" alt="">
                            NOM
                        </button>

                        <div class="dropdown">
                            <button class="dropdown-btn">
                                <img src="./images/icônes/earth.svg" alt="">
                                PAYS
                                <span class="dropdown-arrow">▾</span>
                            </button>
                            <ul class="dropdown-menu">
                                <li><button>FRANCE</button></li>
                                <li><button>ALLEMAGNE</button></li>
                                <li><button>ESPAGNE</button></li>
                                <li><button>ROYAUME UNI</button></li>
                                <li><button>QUÉBEC</button></li>
                                <li><button>JAPON</button></li>
                                <li><button>ITALIE</button></li>
                                <li><button>ÉTATS UNIS</button></li>
                            </ul>
                        </div>
                    </div>
                </div>
                </div>
                <div class="radios-liste"></div>
                <?php
                $liste_radios = $radios->getRadios();
                $rang = 1;
                foreach ($liste_radios as $radio) { ?>
                    <div class="radio-carte">
                        <img src="<?= htmlspecialchars($radio['image_radio']) ?>"
                            alt="<?= htmlspecialchars($radio['nom_radio']) ?>">
                        <p class="radio-nom">
                            <?= htmlspecialchars($radio['nom_radio']) ?>
                        </p>
                        <p class="radio-pays">
                            <?= htmlspecialchars($radio['localisation_radio']) ?>
                        </p>
                    </div>
                    <?php $rang++;
                } ?>
            </section>
        <?php endif; ?>

    </main>

    <?php include 'composants/lecteur.php'; ?>
    <?php include 'composants/footer.php'; ?>
</body>

</html>