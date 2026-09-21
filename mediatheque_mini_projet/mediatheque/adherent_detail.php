<?php
require_once '../config/db.php';

$id = (int)($_GET['id'] ?? 0);
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if (!empty($nom) && !empty($prenom) && !empty($email)) {
        $stmt = $pdo->prepare("UPDATE ADHERENT SET nom = :nom, prenom = :prenom, email = :email WHERE id_adherent = :id");
        $stmt->execute(['nom' => $nom, 'prenom' => $prenom, 'email' => $email, 'id' => $id]);
        $message = "Adhérent mis à jour avec succès.";
    }
}

$stmt = $pdo->prepare("SELECT * FROM ADHERENT WHERE id_adherent = :id");
$stmt->execute(['id' => $id]);
$adherent = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$adherent) {
    header('Location: adherents.php');
    exit;
}

// Historique des emprunts de cet adhérent
$stmtEmprunts = $pdo->prepare("
    SELECT e.*, l.titre 
    FROM EMPRUNT e 
    JOIN LIVRE l ON e.id_livre = l.id_livre 
    WHERE e.id_adherent = :id 
    ORDER BY e.date_emprunt DESC
");
$stmtEmprunts->execute(['id' => $id]);
$historique = $stmtEmprunts->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails Adhérent - Médiathèque</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <header>
        <h1>📖 Médiathèque</h1>
        <nav>
            <a href="index.php">Accueil</a>
            <a href="livres.php">Livres</a>
            <a href="adherents.php" class="active">Adhérents</a>
            <a href="emprunts.php">Emprunts</a>
            <a href="emprunter.php">Nouveau emprunt</a>
            <a href="retour.php">Retours</a>
        </nav>
    </header>

    <main class="container">
        <h2>Fiche de <?= htmlspecialchars($adherent['prenom'] . ' ' . $adherent['nom']) ?></h2>
        <?php if ($message): ?><p style="color: green; font-weight: bold;"><?= htmlspecialchars($message) ?></p><?php endif; ?>

        <form method="POST" style="max-width: 500px; margin-bottom: 30px;">
            <h3>Modifier les informations</h3>
            <div style="margin-bottom: 10px;">
                <label>Nom :</label>
                <input type="text" name="nom" value="<?= htmlspecialchars($adherent['nom']) ?>" required>
            </div>
            <div style="margin-bottom: 10px;">
                <label>Prénom :</label>
                <input type="text" name="prenom" value="<?= htmlspecialchars($adherent['prenom']) ?>" required>
            </div>
            <div style="margin-bottom: 15px;">
                <label>Email :</label>
                <input type="email" name="email" value="<?= htmlspecialchars($adherent['email']) ?>" required>
            </div>
            <button type="submit">Enregistrer les modifications</button>
        </form>

        <h3>Historique des emprunts</h3>
        <table border="1" cellpadding="10" cellspacing="0" style="width:100%; border-collapse:collapse;">
            <thead>
                <tr>
                    <th>Livre</th>
                    <th>Date d'emprunt</th>
                    <th>Date retour prévue</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($historique as $e): ?>
                    <tr>
                        <td><?= htmlspecialchars($e['titre']) ?></td>
                        <td><?= date('d/m/Y', strtotime($e['date_emprunt'])) ?></td>
                        <td><?= date('d/m/Y', strtotime($e['date_retour_prevue'])) ?></td>
                        <td><?= $e['date_retour'] ? 'Rendu le ' . date('d/m/Y', strtotime($e['date_retour'])) : 'En cours' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</body>
</html>