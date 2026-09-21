<?php
require_once '../config/db.php';

$nbLivres = $pdo->query("SELECT COUNT(*) FROM LIVRE WHERE disponible = 1")->fetchColumn();
$nbAdherents = $pdo->query("SELECT COUNT(*) FROM ADHERENT")->fetchColumn();
$nbEmprunts = $pdo->query("SELECT COUNT(*) FROM EMPRUNT WHERE date_retour IS NULL")->fetchColumn();
$nbRetards = $pdo->query("SELECT COUNT(*) FROM EMPRUNT WHERE date_retour IS NULL AND date_retour_prevue < CURDATE()")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil - Médiathèque</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <header>
        <h1>📖 Médiathèque</h1>
        <nav>
            <a href="index.php" class="active">Accueil</a>
            <a href="livres.php">Livres</a>
            <a href="adherents.php">Adhérents</a>
            <a href="emprunts.php">Emprunts</a>
            <a href="emprunter.php">Nouveau emprunt</a>
            <a href="retour.php">Retours</a>
        </nav>
    </header>

    <main class="container">
        <!-- Bannière visuelle -->
        <div style="background: #e0e7ff; padding: 30px; border-radius: 8px; display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px;">
            <div>
                <h2>Bienvenue sur votre espace de gestion</h2>
                <p style="color: #4b5563; margin-top: 8px;">Gérez facilement votre catalogue de livres, vos adhérents et le suivi des prêts au même endroit.</p>
                <div style="margin-top: 15px; display: flex; gap: 10px;">
                    <a href="emprunter.php" style="background: #4f46e5; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none;">+ Créer un emprunt</a>
                    <a href="livres.php" style="background: white; color: #4f46e5; padding: 8px 15px; border-radius: 5px; text-decoration: none; border: 1px solid #4f46e5;">Voir le catalogue</a>
                </div>
            </div>
            <img src="https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?auto=format&fit=crop&w=300&q=80" alt="Bibliothèque" style="width: 250px; height: 160px; object-fit: cover; border-radius: 8px;">
        </div>

        <h3>Vue d'ensemble</h3>
        <div class="stats-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px;">
            <div class="card blue" style="background: white; padding: 20px; border-radius: 8px; text-align: center;">
                <h3 style="font-size: 2rem; color: #3b82f6;"><?= $nbLivres ?></h3>
                <p>Livres disponibles</p>
            </div>
            <div class="card green" style="background: white; padding: 20px; border-radius: 8px; text-align: center;">
                <h3 style="font-size: 2rem; color: #10b981;"><?= $nbAdherents ?></h3>
                <p>Adhérents inscrits</p>
            </div>
            <div class="card orange" style="background: white; padding: 20px; border-radius: 8px; text-align: center;">
                <h3 style="font-size: 2rem; color: #f59e0b;"><?= $nbEmprunts ?></h3>
                <p>Emprunts en cours</p>
            </div>
            <div class="card red" style="background: white; padding: 20px; border-radius: 8px; text-align: center;">
                <h3 style="font-size: 2rem; color: #ef4444;"><?= $nbRetards ?></h3>
                <p>Emprunts en retard</p>
            </div>
        </div>
    </main>
</body>
</html>