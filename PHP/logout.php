<?php

session_start();

// Détruire toutes les variables de session.
session_destroy();
unset($_SESSION);

header('Location: login.php');
?>