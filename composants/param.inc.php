<?php
if ($_SERVER['HTTP_HOST'] === 'la-perso.univ-lemans.fr') {
    // Configuration pour le serveur la-perso.univ-lemans.fr
    define("MYHOST", "localhost");
    define("MYUSER", "mmi1_i2503125");
    define("MYPASS", "7234BB");
    define("MYDB",   "mmi1_i2503125_therockdb");
} else {
    // Configuration pour le serveur XAMPP (pc perso)
    define("MYHOST", "localhost");
    define("MYUSER", "root");
    define("MYPASS", "");
    define("MYDB",   "sae203");
}