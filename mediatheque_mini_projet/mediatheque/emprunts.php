<?php
require_once '../config/db.php';

// Récupération de tous les emprunts avec les infos du livre et de l'adhérent
$sql = "SELECT 
            e.id_emprunt,
            e.date_emprunt,
            e.date_retour_prevue,
            e.date_retour,
            l.titre AS livre_titre,
            a.nom AS adherent_nom,
            a.prenom AS adherent_prenom
        FROM EMPRUNT e
        JOIN LIVRE l ON e.id_livre = l.id_livre
        JOIN ADHERENT a ON e.id_adherent = a.id_adherent
        ORDER BY e.date_emprunt DESC";

$stmt = $pdo->query($sql);
$emprunts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des emprunts - Médiathèque</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <header>
        <h1>📖 Médiathèque</h1>
        <nav>
            <a href="index.php">Accueil</a>
            <a href="livres.php">Livres</a>
            <a href="adherents.php">Adhérents</a>
            <a href="emprunts.php" class="active">Emprunts</a>
            <a href="emprunter.php">Nouveau emprunt</a>
            <a href="retour.php">Retours</a>
        </nav>
    </header>

    <main class="container">
        <h2>Historique des emprunts</h2>

        <table border="1" cellpadding="10" cellspacing="0" style="width:100%; border-collapse:collapse;">
            <thead>
                <tr>
                    <th>Livre</th>
                    <th>Adhérent</th>
                    <th>Date d'emprunt</th>
                    <th>Retour prévu</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($emprunts as $e): ?>
                    <?php 
                        // Calcul du statut de l'emprunt
                        $estRendu = !empty($e['date_retour']);
                        $estEnRetard = !$estRendu && (strtotime($e['date_retour_prevue']) < strtotime(date('Y-m-d')));
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($e['livre_titre']) ?></td>
                        <td><?= htmlspecialchars($e['adherent_nom'] . ' ' . $e['adherent_prenom']) ?></td>
                        <td><?= date('d/m/Y', strtotime($e['date_emprunt'])) ?></td>
                        <td><?= date('d/m/Y', strtotime($e['date_retour_prevue'])) ?></td>
                        <td>
                            <?php if ($estRendu): ?>
                                <span class="badge" style="background:#6c757d; color:white; padding:3px 8px; border-radius:3px;">
                                    Rendu le <?= date('d/m/Y', strtotime($e['date_retour'])) ?>
                                </span>
                            <?php elseif ($estEnRetard): ?>
                                <span class="badge" style="background:#dc3545; color:white; padding:3px 8px; border-radius:3px;">
                                    En retard
                                </span>
                            <?php else: ?>
                                <span class="badge" style="background:#ffc107; color:#333; padding:3px 8px; border-radius:3px;">
                                    En cours
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</body>
</html>