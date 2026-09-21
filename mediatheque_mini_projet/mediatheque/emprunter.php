<?php
require_once '../config/db.php';

$message = '';
$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_adherent = (int)($_POST['id_adherent'] ?? 0);
    $id_livre = (int)($_POST['id_livre'] ?? 0);
    $duree_jours = (int)($_POST['duree'] ?? 14);

    if ($id_adherent > 0 && $id_livre > 0) {
        try {
            $pdo->beginTransaction();

            $date_emprunt = date('Y-m-d');
            $date_retour_prevue = date('Y-m-d', strtotime("+$duree_jours days"));

            $stmtEmprunt = $pdo->prepare("INSERT INTO EMPRUNT (id_adherent, id_livre, date_emprunt, date_retour_prevue) VALUES (:id_adherent, :id_livre, :date_emprunt, :date_retour_prevue)");
            $stmtEmprunt->execute([
                'id_adherent' => $id_adherent,
                'id_livre' => $id_livre,
                'date_emprunt' => $date_emprunt,
                'date_retour_prevue' => $date_retour_prevue
            ]);

            $stmtLivre = $pdo->prepare("UPDATE LIVRE SET disponible = 0 WHERE id_livre = :id_livre");
            $stmtLivre->execute(['id_livre' => $id_livre]);

            $pdo->commit();
            $message = "L'emprunt a été enregistré avec succès !";
        } catch (PDOException $e) {
            $pdo->rollBack();
            $erreur = "Erreur lors de l'enregistrement.";
        }
    } else {
        $erreur = "Veuillez remplir tous les champs.";
    }
}

$adherents = $pdo->query("SELECT id_adherent, nom, prenom FROM ADHERENT ORDER BY nom ASC")->fetchAll(PDO::FETCH_ASSOC);
$livres = $pdo->query("SELECT id_livre, titre FROM LIVRE WHERE disponible = 1 ORDER BY titre ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouveau emprunt - Médiathèque</title>
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
            <a href="emprunter.php" class="active">Nouveau emprunt</a>
            <a href="retour.php">Retours</a>
        </nav>
    </header>

    <main class="container" style="display: flex; flex-direction: column; align-items: center; margin-top: 20px;">
        <div style="width: 100%; max-width: 550px; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: 1px solid #ddd;">
            <h2 style="text-align: center; margin-bottom: 20px;">Enregistrer un nouvel emprunt</h2>

            <?php if ($message): ?><p style="color: green; font-weight: bold; text-align: center;"><?= htmlspecialchars($message) ?></p><?php endif; ?>
            <?php if ($erreur): ?><p style="color: red; font-weight: bold; text-align: center;"><?= htmlspecialchars($erreur) ?></p><?php endif; ?>

            <form method="POST" action="emprunter.php" style="display: flex; flex-direction: column; gap: 15px;">
                <div>
                    <label for="id_adherent">Adhérent :</label>
                    <select name="id_adherent" id="id_adherent" required style="width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 5px;">
                        <option value="">-- Choisir un adhérent --</option>
                        <?php foreach ($adherents as $adherent): ?>
                            <option value="<?= $adherent['id_adherent'] ?>">
                                <?= htmlspecialchars($adherent['nom'] . ' ' . $adherent['prenom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="id_livre">Livre disponible :</label>
                    <select name="id_livre" id="id_livre" required style="width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 5px;">
                        <option value="">-- Choisir un livre --</option>
                        <?php foreach ($livres as $livre): ?>
                            <option value="<?= $livre['id_livre'] ?>">
                                <?= htmlspecialchars($livre['titre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="duree">Durée de l'emprunt (jours) :</label>
                    <input type="number" name="duree" id="duree" value="14" min="1" max="60" required style="width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 5px;">
                </div>

                <button type="submit" style="padding: 12px; background-color: #4f46e5; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 1rem; font-weight: bold; margin-top: 10px;">
                    Valider l'emprunt
                </button>
            </form>
        </div>
    </main>
</body>
</html>