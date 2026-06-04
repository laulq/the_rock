<?php
session_start();
require_once 'composants/param.inc.php';
require_once 'src/Utilisateurs/Administrer.php';
require_once 'src/Radios/Administrer.php';

$utilisateurs = new Utilisateurs\Administrer(MYHOST, MYDB, MYUSER, MYPASS);
$radios       = new Radios\Administrer(MYHOST, MYDB, MYUSER, MYPASS);