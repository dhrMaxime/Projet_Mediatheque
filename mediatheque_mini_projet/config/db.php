<?php 
// config/db.php
$host = '127.0.0.1'; // au lieu de 'localhost'
$dbname = 'mediatheque';
$username = 'root';
$password = ''; // Laisse vide sur XAMPP/WAMP par défaut (ou mets ton mot de passe)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}