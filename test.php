<?php
session_start();
require_once 'composants/param.inc.php';
require_once 'src/Utilisateurs/Administrer.php';
require_once 'src/Radios/Administrer.php';

$utilisateurs = new Utilisateurs\Administrer(MYHOST, MYDB, MYUSER, MYPASS);

$utilisateurs->inscrire("test", "test@test.com", "password");

/*c'est en dur, mais il faut récupérer les infos du formulaire, ajouter le nom et toutes les autres infos*/
