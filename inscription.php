<?php
session_start();
require_once("connect.php"); // Connexion à la base $conn

$message = "";

// Soumission du formulaire
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $Num = intval($_POST['Num'] ?? 0);
    $pseudo = trim($_POST['pseudo'] ?? '');
    $passe = trim($_POST['passe'] ?? '');
    $type = trim($_POST['type'] ?? '');

    if ($Num && $pseudo && $passe && $type) {
        // Vérifier si le pseudo existe déjà
        $stmt = $conn->prepare("SELECT * FROM login WHERE pseudo = ?");
        $stmt->bind_param("s", $pseudo);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows > 0) {
            $message = "⚠️ Ce pseudo est déjà utilisé.";
        } else {
            // Hasher le mot de passe
            $hash = password_hash($passe, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO login (Num, pseudo, passe, type) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $Num, $pseudo, $hash, $type);
            if ($stmt->execute()) {
                $message = "✅ Compte créé avec succès !";
            } else {
                $message = "❌ Erreur lors de l'inscription.";
            }
        }
        $stmt->close();
    } else {
        $message = "⚠️ Veuillez remplir tous les champs.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Inscription - Bob Université</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="theme.css">
</head>
<body>
<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card p-4 shadow-lg" style="width: 400px;">
        <h3 class="text-center mb-3">Créer un nouveau compte</h3>

        <?php if ($message): ?>
            <div class="alert alert-info text-center"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label>Numéro (Num)</label>
                <input type="number" name="Num" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Pseudo</label>
                <input type="text" name="pseudo" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Mot de passe</label>
                <input type="password" name="passe" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Type</label>
                <select name="type" class="form-select" required>
                    <option value="">-- Choisir le type --</option>
                    <option value="admin">Admin</option>
                    <option value="etudiant">Étudiant</option>
                    <option value="prof">Prof</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-100">Créer le compte</button>
        </form>
        <div class="text-center mt-3">
            <a href="liste_utilisateur.php" class="btn btn-secondary">Voir la liste des comptes</a>
        </div>
    </div>
</div>
</body>
</html>
