<?php
session_start();
session_destroy(); // Zrušení session
header('Location: index.html'); // Přesměrování na hlavní stránku
exit();
?>
