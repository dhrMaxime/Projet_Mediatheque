<?php
require_once '../config/db.php';

$message = '';
$erreur = '';

if (isset($_GET['action']) && $_GET['action'] === 'rendre' && !empty($_GET['id_emprunt']) && !empty($_GET['id_livre'])) {
    $id_emprunt = (int)$_GET['id_emprunt'];
    $id_livre = (int)$_GET['id_livre'];

    try {
        $pdo->beginTransaction();

        // Mettre à jour la date de retour dans EMPRUNT
        $stmtEmprunt = $pdo->prepare("UPDATE EMPRUNT SET date_retour = CURDATE() WHERE id_emprunt = :id_emprunt");
        $stmtEmprunt->execute(['id_emprunt' => $id_emprunt]);

        // Remettre disponible = 1 dans LIVRE
        $stmtLivre = $pdo->prepare("UPDATE LIVRE SET disponible = 1 WHERE id_livre = :id_livre");
        $stmtLivre->execute(['id_livre' => $id_livre]);

        $pdo->commit();
        $message = "Le retour du livre a bien été enregistré !";
    } catch (PDOException $e) {
        $pdo->rollBack();
        // Affiche l'erreur réelle au lieu de la cacher
        $erreur = "Erreur SQL : " . $e->getMessage(); 
    }
}

// Récupération des emprunts EN COURS
$sql = "SELECT 
            e.id_emprunt,
            e.id_livre,
            e.date_emprunt,
            e.date_retour_prevue,
            l.titre AS livre_titre,
            a.nom AS adherent_nom,
            a.prenom AS adherent_prenom
        FROM EMPRUNT e
        JOIN LIVRE l ON e.id_livre = l.id_livre
        JOIN ADHERENT a ON e.id_adherent = a.id_adherent
        WHERE e.date_retour IS NULL
        ORDER BY e.date_retour_prevue ASC";

$stmt = $pdo->query($sql);
$empruntsEnCours = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des retours - Médiathèque</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <header>
        <h1>📖 Médiathèque</h1>
        <nav>
            <a href="index.php">Accueil</a>
            <a href="livres.php">Livres</a>
            <a href="adherents.php">Adhérents</a>
            <a href="emprunts.php">Emprunts</a>
            <a href="emprunter.php">Nouveau emprunt</a>
            <a href="retour.php" class="active">Retours</a>
        </nav>
    </header>

    <main class="container">
        <h2>Gestion des retours de livres</h2>

        <?php if ($message): ?><p style="color: green; font-weight: bold;"><?= htmlspecialchars($message) ?></p><?php endif; ?>
        <?php if ($erreur): ?><p style="color: red; font-weight: bold;"><?= htmlspecialchars($erreur) ?></p><?php endif; ?>

        <?php if (empty($empruntsEnCours)): ?>
            <p>Aucun emprunt en cours à retourner pour le moment.</p>
        <?php else: ?>
            <table border="1" cellpadding="10" cellspacing="0" style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr>
                        <th>Livre</th>
                        <th>Emprunteur</th>
                        <th>Date d'emprunt</th>
                        <th>Date retour prévue</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($empruntsEnCours as $e): ?>
                        <?php $estEnRetard = strtotime($e['date_retour_prevue']) < strtotime(date('Y-m-d')); ?>
                        <tr style="<?= $estEnRetard ? 'background-color: #fff0f0;' : '' ?>">
                            <td><?= htmlspecialchars($e['livre_titre']) ?></td>
                            <td><?= htmlspecialchars($e['adherent_nom'] . ' ' . $e['adherent_prenom']) ?></td>
                            <td><?= date('d/m/Y', strtotime($e['date_emprunt'])) ?></td>
                            <td>
                                <?= date('d/m/Y', strtotime($e['date_retour_prevue'])) ?>
                                <?php if ($estEnRetard): ?>
                                    <strong style="color: red;"> (En retard)</strong>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="retour.php?action=rendre&id_emprunt=<?= $e['id_emprunt'] ?>&id_livre=<?= $e['id_livre'] ?>" 
                                   onclick="return confirm('Confirmer le retour de ce livre ?');"
                                   style="padding: 5px 10px; background-color: #28a745; color: white; text-decoration: none; border-radius: 3px;">
                                    Valider le retour
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </main>
</body>
</html>