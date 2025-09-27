<?php
session_start();
require_once("connect.php");

// Suppression si demandé
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM login WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: liste_login.php");
    exit();
}

// Récupérer tous les comptes
$result = $conn->query("SELECT * FROM login ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Liste des comptes - Bob Université</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="theme.css">
</head>
<body>
<div class="container my-5">
    <h3 class="text-center mb-4">Liste des comptes utilisateurs</h3>
    <div class="text-end mb-3">
        <a href="inscription.php" class="btn btn-success">➕ Ajouter un compte</a>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="table-primary">
                <tr>
                    <th>ID</th>
                    <th>Num</th>
                    <th>Pseudo</th>
                    <th>Type</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['Num']) ?></td>
                        <td><?= htmlspecialchars($row['pseudo']) ?></td>
                        <td><?= htmlspecialchars($row['type']) ?></td>
                        <td>
                            <a href="liste_login.php?delete=<?= $row['id'] ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('Supprimer ce compte ?');">
                               Supprimer
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                <?php if ($result->num_rows === 0): ?>
                    <tr><td colspan="5" class="text-center">Aucun compte trouvé.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
