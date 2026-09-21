<?php
require_once '../config/db.php';

$recherche = isset($_GET['recherche']) ? trim($_GET['recherche']) : '';

$sql = "SELECT 
            l.id_livre, l.titre, l.isbn, l.annee_publication, l.disponible,
            c.libelle AS categorie,
            GROUP_CONCAT(CONCAT(a.prenom, ' ', a.nom) SEPARATOR ', ') AS auteurs
        FROM LIVRE l
        LEFT JOIN CATEGORIE c ON l.id_categorie = c.id_categorie
        LEFT JOIN LIVRE_AUTEUR la ON l.id_livre = la.id_livre
        LEFT JOIN AUTEUR a ON la.id_auteur = a.id_auteur";

if (!empty($recherche)) {
    $sql .= " WHERE l.titre LIKE :q OR c.libelle LIKE :q OR a.nom LIKE :q OR a.prenom LIKE :q";
}

$sql .= " GROUP BY l.id_livre";

if (!empty($recherche)) {
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['q' => "%$recherche%"]);
} else {
    $stmt = $pdo->query($sql);
}

$livres = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des livres - Médiathèque</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <header>
        <h1>📖 Médiathèque</h1>
        <nav>
            <a href="index.php">Accueil</a>
            <a href="livres.php" class="active">Livres</a>
            <a href="adherents.php">Adhérents</a>
            <a href="emprunts.php">Emprunts</a>
            <a href="emprunter.php">Nouveau emprunt</a>
            <a href="retour.php">Retours</a>
        </nav>
    </header>

    <main class="container">
        <h2>Liste des livres</h2>

        <!-- Barre de recherche réalignée -->
        <form method="GET" action="livres.php" style="margin-bottom: 25px; display: flex; gap: 10px; max-width: 600px;">
            <input type="text" name="recherche" placeholder="Rechercher un livre, auteur, catégorie..." value="<?= htmlspecialchars($recherche) ?>" style="flex: 1; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
            <button type="submit" style="padding: 10px 20px; background-color: #4f46e5; color: white; border: none; border-radius: 5px; cursor: pointer;">Rechercher</button>
        </form>

        <table border="1" cellpadding="10" cellspacing="0" style="width:100%; border-collapse:collapse;">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Auteur(s)</th>
                    <th>Catégorie</th>
                    <th>Année</th>
                    <th>ISBN</th>
                    <th>Disponibilité</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($livres as $livre): ?>
                    <tr>
                        <td><?= htmlspecialchars($livre['titre']) ?></td>
                        <td><?= htmlspecialchars($livre['auteurs'] ?? 'Auteur inconnu') ?></td>
                        <td><?= htmlspecialchars($livre['categorie'] ?? 'Non classé') ?></td>
                        <td><?= htmlspecialchars($livre['annee_publication']) ?></td>
                        <td><?= htmlspecialchars($livre['isbn']) ?></td>
                        <td>
                            <?php if ($livre['disponible']): ?>
                                <span class="badge disponible">Disponible</span>
                            <?php else: ?>
                                <span class="badge emprunte">Emprunté</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</body>
</html>