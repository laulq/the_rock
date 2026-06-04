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
</head>

<body>
    <?php include 'composants/header.php'; ?>
    <main>

    <?php if (!isset($_SESSION['user'])): ?><!-- Quand l'utilisateur n'est pas connecté !-->

        <section id="top-radios">
            <h1>TOP DU MOMENT</h1>

            <div id="top-radios-liste">
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
        <section id="categories">
            <h1>CATÉGORIES</h1>
            <div class="choisir_categorie"></div>
            <button><img src="placeholder_hard_rock" alt="Sélection de radios hard rock"></button>
            <button><img src="placeholder_metal" alt="Sélection de radios metal"></button>
            <button><img src="placeholder_alternatif" alt="Sélection de radios alternatif"></button>
            <div id="filtres-radios">
                <h2>FILTRER PAR</h2>
                <div>
                    <button><img src="placeholder_popularite" alt="">POPULARITÉ</button>
                    <select name="tag" id="select_tag">
                        <img src="placeholder_tag" alt="">
                        <option value="tag">TAG</option>
                        <option value="grunge">GRUNGE</option>
                        <option value="pop_rock">POP ROCK</option>
                        <option value="90s">90's</option>
                        <option value="2000s">2000's</option>
                        <option value="2010s">2010's</option>
                    </select>
                    <button><img src="placeholder_nom" alt="">NOM</button>
                    <select name="pays" id="select_pays">
                        <img src="placeholder_pays" alt="">
                        <option value="pays">PAYS</option>
                        <option value="france">FRANCE</option>
                        <option value="allemagne">ALLEMAGNE</option>
                        <option value="espagne">ESPAGNE</option>
                        <option value="royaume_uni">ROUYAUME UNI</option>
                        <option value="quebec">QUÉBEC</option>
                        <option value="japon">JAPON</option>
                        <option value="italie">ITALIE</option>
                        <option value="etats_unis">ÉTATS UNIS</option>
                    </select>
                </div>
            </div>
            <div id="radios-liste"></div>
            <?php
            $liste_radios = $radios->getRadios();
            $rang = 1;
            foreach ($liste_radios as $radio): ?>
                <div class="radio-carte">
                    <img src="<?= htmlspecialchars($radio['image_radio']) ?>" alt="<?= htmlspecialchars($radio['nom_radio']) ?>">
                    <p class="radio-nom">
                        <?= htmlspecialchars($radio['nom_radio']) ?>
                    </p>
                    <p class="radio-pays">
                        <?= htmlspecialchars($radio['localisation_radio']) ?>
                    </p>
                </div>
                <?php $rang++; endforeach; ?>
        </section>

    <?php else: ?><!--Quand l'utilisateur est connecté!-->
        <section id="mes-radios">
            <h1>MES RADIOS</h1>
            <div id="mes-radios-liste">
                <?php
                $mes_radios = $radios->getRadiosUtilisateur($_SESSION['user']['id_compte']);
                foreach ($mes_radios as $radio): ?>
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
                <?php endforeach; ?>

                <!-- Carte "suivante" avec bouton play -->
                <div class="radio-carte radio-carte-suivante">
                    <img src="placeholder_radio_suivante" alt="placeholder">
                    <a href="#" class="bouton-play"><img src="placeholder_bouton_play" alt="play"></a>
                </div>
            </div>
            <div id="recommandations-liste">
                <?php
                $recommandations = $radios->getRecommandations($_SESSION['user']['id_compte']);
                foreach ($recommandations as $radio): ?>
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
                <?php endforeach; ?>

                <!-- Carte "suivante" avec bouton play -->
                <div class="radio-carte radio-carte-suivante">
                    <img src="placeholder_radio_suivante" alt="placeholder">
                    <a href="#" class="bouton-play"><img src="placeholder_bouton_play" alt="play"></a>
                </div>
            </div>
            <a href="#top-radios" class="bouton-principal">DÉCOUVRIR TOUTES NOS RADIOS</a>
        </section>
        <section id="top-radios">
            <h1>TOP DU MOMENT</h1>

            <div id="top-radios-liste">
                <?php
                $top_radios = $radios->getTopRadios();
                $rang = 1;
                foreach ($top_radios as $radio): ?>
                    <div class="radio-carte">
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
        <section id="categories">
            <h1>CATÉGORIES</h1>
            <div class="choisir_categorie"></div>
            <button><img src="placeholder_hard_rock" alt="Sélection de radios hard rock"></button>
            <button><img src="placeholder_metal" alt="Sélection de radios metal"></button>
            <button><img src="placeholder_alternatif" alt="Sélection de radios alternatif"></button>
            <div id="filtres-radios">
                <h2>FILTRER PAR</h2>
                <div>
                    <button><img src="placeholder_popularite" alt="">POPULARITÉ</button>
                    <select name="tag" id="select_tag">
                        <img src="placeholder_tag" alt="">
                        <option value="tag">TAG</option>
                        <option value="grunge">GRUNGE</option>
                        <option value="pop_rock">POP ROCK</option>
                        <option value="90s">90's</option>
                        <option value="2000s">2000's</option>
                        <option value="2010s">2010's</option>
                    </select>
                    <button><img src="placeholder_nom" alt="">NOM</button>
                    <select name="pays" id="select_pays">
                        <img src="placeholder_pays" alt="">
                        <option value="pays">PAYS</option>
                        <option value="france">FRANCE</option>
                        <option value="allemagne">ALLEMAGNE</option>
                        <option value="espagne">ESPAGNE</option>
                        <option value="royaume_uni">ROUYAUME UNI</option>
                        <option value="quebec">QUÉBEC</option>
                        <option value="japon">JAPON</option>
                        <option value="italie">ITALIE</option>
                        <option value="etats_unis">ÉTATS UNIS</option>
                    </select>
                </div>
            </div>
            <div id="radios-liste"></div>
            <?php
            $liste_radios = $radios->getRadios();
            $rang = 1;
            foreach ($liste_radios as $radio){?>
                <div class="radio-carte">
                    <img src="<?= htmlspecialchars($radio['image_radio']) ?>" alt="<?= htmlspecialchars($radio['nom_radio']) ?>">
                    <p class="radio-nom">
                        <?= htmlspecialchars($radio['nom_radio']) ?>
                    </p>
                    <p class="radio-pays">
                        <?= htmlspecialchars($radio['localisation_radio']) ?>
                    </p>
                </div>
                <?php $rang++; }?>
        </section>
    <?php endif; ?>

    </main>

    <?php include 'composants/lecteur.php'; ?>
    <?php include 'composants/footer.php'; ?>
</body>
</html>