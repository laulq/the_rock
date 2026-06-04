<header>
    <nav id="header-nav" class="header-nav">
        <a class="header-nav-lien <?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'current' : '' ?>" href="index.php">ACCUEIL</a>
        <a class="header-nav-lien <?= basename($_SERVER['PHP_SELF']) === 'liste.php'   ? 'current' : '' ?>" href="liste.php">LES RADIOS</a>
    </nav>

    <input type="search" id="header-recherche" class="header-recherche" placeholder="TROUVER MA RADIO"> <!-- Loupe dans le ::before en CSS -->

    <?php if (!isset($_SESSION['user'])) : ?>
        <a class="header-profil" href="profil.php"><img src="placeholder_profil" alt="profil"></a>
    <?php else : ?>
        <p class="header-bonjour">BONJOUR <a class="header-profil-lien" href="profil.php"><?= htmlspecialchars($_SESSION['user']['pseudo_compte']) ?> <img src="placeholder_fleche" alt="flèche"></a></p>
    <?php endif; ?>
</header>