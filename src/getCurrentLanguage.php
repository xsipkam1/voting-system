<?php
session_start();
echo isset($_SESSION['currentLanguage']) ? $_SESSION['currentLanguage'] : 'sk';
?>
