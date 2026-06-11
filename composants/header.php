<header>
    <nav id="header-nav" class="header-nav">
        <a class="header-nav-lien <?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'current' : '' ?>" href="index.php">ACCUEIL</a>
        <a class="header-nav-lien <?= basename($_SERVER['PHP_SELF']) === 'liste.php'   ? 'current' : '' ?>" href="liste.php">LES RADIOS</a>
    </nav>


    <?php if (!isset($_SESSION['user'])) : ?>
        <a class="header-profil" href="profil.php"><img src="./images/icônes/Avatar.svg" alt="profil"></a>
    <?php else : ?>
        <p class="header-bonjour">BONJOUR <a class="header-profil-lien" href="profil.php"><?= htmlspecialchars($_SESSION['user']['pseudo_compte']) ?> <img src="./images/icônes/phone_arrow_bottom.svg" alt="flèche"></a></p>
    <?php endif; ?>
</header>