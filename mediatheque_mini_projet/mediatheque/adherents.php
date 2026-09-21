<?php
require_once '../config/db.php';

$message = '';
$erreur = '';

// Ajout d'un nouvel adhérent
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'ajouter') {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if (!empty($nom) && !empty($prenom) && !empty($email)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO ADHERENT (nom, prenom, email, date_inscription) VALUES (:nom, :prenom, :email, CURDATE())");
            $stmt->execute([
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $email
            ]);
            $message = "Adhérent ajouté avec succès !";
        } catch (PDOException $e) {
            $erreur = "Erreur : cet e-mail est déjà utilisé.";
        }
    } else {
        $erreur = "Veuillez remplir tous les champs.";
    }
}

// Récupération de la liste des adhérents
$stmt = $pdo->query("SELECT id_adherent, nom, prenom, email, date_inscription FROM ADHERENT ORDER BY nom ASC, prenom ASC");
$adherents = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Adhérents - Médiathèque</title>
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
        <h2>Gestion des adhérents</h2>

        <?php if ($message): ?><p style="color: green; font-weight: bold;"><?= htmlspecialchars($message) ?></p><?php endif; ?>
        <?php if ($erreur): ?><p style="color: red; font-weight: bold;"><?= htmlspecialchars($erreur) ?></p><?php endif; ?>

        <!-- Bouton pour afficher/masquer le formulaire d'ajout -->
        <button type="button" onclick="toggleForm()" style="background-color: #28a745; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; margin-bottom: 20px;">
            + Ajouter un adhérent
        </button>

        <div id="form-ajout" style="display: none; background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #ddd;">
            <h3>Nouvel adhérent</h3>
            <form method="POST" action="adherents.php" style="display: flex; gap: 10px; flex-wrap: wrap;">
                <input type="hidden" name="action" value="ajouter">
                <input type="text" name="nom" placeholder="Nom" required style="padding: 8px;">
                <input type="text" name="prenom" placeholder="Prénom" required style="padding: 8px;">
                <input type="email" name="email" placeholder="Email" required style="padding: 8px;">
                <button type="submit" style="padding: 8px 15px; background-color: #007bff; color: white; border: none; border-radius: 3px; cursor: pointer;">Enregistrer</button>
            </form>
        </div>

        <table border="1" cellpadding="10" cellspacing="0" style="width:100%; border-collapse:collapse;">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Date d'inscription</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($adherents as $adherent): ?>
                    <tr>
                        <td><?= htmlspecialchars($adherent['nom']) ?></td>
                        <td><?= htmlspecialchars($adherent['prenom']) ?></td>
                        <td><?= htmlspecialchars($adherent['email']) ?></td>
                        <td><?= date('d/m/Y', strtotime($adherent['date_inscription'])) ?></td>
                        <td>
                            <a href="adherent_detail.php?id=<?= $adherent['id_adherent'] ?>" style="padding: 5px 10px; background: #e0e0e0; border: 1px solid #ccc; text-decoration: none; color: #333; border-radius: 3px;">
                                Voir
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>

    <script>
    function toggleForm() {
        var form = document.getElementById('form-ajout');
        form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'block' : 'none';
    }
    </script>
</body>
</html>