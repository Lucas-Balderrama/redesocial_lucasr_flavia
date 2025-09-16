<?php
session_start();
session_unset();
$_SESSION['nome'] = '';
header('Location: index.php');
exit;
?>