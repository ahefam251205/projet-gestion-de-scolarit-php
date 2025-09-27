<?php
session_start();
include("header.php");
include("connect.php"); // $conn = new mysqli(...)

$message = '';

// Récupérer la matière si modif_matiere est défini
if (isset($_GET['modif_matiere'])) {
    $id = (int)$_GET['modif_matiere'];
    $stmt = $conn->prepare("SELECT m.codemat, m.nommat, c.nom AS classe, c.promotion
                            FROM matiere m
                            JOIN classe c ON m.codecl = c.codecl
                            WHERE m.codemat = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $matiere = $result->fetch_assoc();
    $stmt->close();
}

// Traitement du formulaire pour modification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $nommat = htmlspecialchars(trim($_POST['nommat']));

    if ($nommat !== '') {
        $stmt = $conn->prepare("UPDATE matiere SET nommat=? WHERE codemat=?");
        $stmt->bind_param("si", $nommat, $id);
        if ($stmt->execute()) {
            $message = '<div class="alert alert-success text-center">✅ Matière modifiée avec succès !</div>';
        } else {
            $message = '<div class="alert alert-danger text-center">❌ Erreur lors de la modification.</div>';
        }
        $stmt->close();
    } else {
        $message = '<div class="alert alert-danger text-center">❌ Le nom de la matière est obligatoire.</div>';
    }
}

// Suppression
if (isset($_GET['supp_matiere'])) {
    $id = (int)$_GET['supp_matiere'];
    $stmt = $conn->prepare("DELETE FROM matiere WHERE codemat=?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo '<script>
            alert("Matière supprimée avec succès !");
            window.location.href="affiche_matiere.php";
        </script>';
        exit;
    } else {
        echo '<script>
            alert("Erreur lors de la suppression !");
            window.location.href="affiche_matiere.php";
        </script>';
        exit;
    }
}
?>

<link rel="stylesheet" href="theme.css">

<div class="container my-5">
    <h1 class="text-center mb-4" style="color: #1e3c72; text-shadow: 2px 2px 8px rgba(0,0,0,0.2);">
        Modifier une matière
    </h1>

    <?= $message ?? '' ?>

    <?php if (isset($matiere)): ?>
        <form method="POST" action="modif_matiere.php?modif_matiere=<?= $matiere['codemat'] ?>">
            <input type="hidden" name="id" value="<?= $matiere['codemat'] ?>">

            <div class="mb-3">
                <label>Matière :</label>
                <input type="text" name="nommat" class="form-control" value="<?= htmlspecialchars($matiere['nommat']) ?>" required>
            </div>

            <div class="mb-3">
                <label>Classe :</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($matiere['classe']) ?>" disabled>
            </div>

            <div class="mb-3">
                <label>Promotion :</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($matiere['promotion']) ?>" disabled>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-grad">Modifier</button>
                <a href="listematière.php" class="btn btn-grad ms-2">⬅ Retour à la liste</a>
                <a href="modif_matiere.php?supp_matiere=<?= $matiere['codemat'] ?>" class="btn btn-grad ms-2"
                   onclick="return confirm('Voulez-vous vraiment supprimer cette matière ?');">Supprimer</a>
            </div>
        </form>
    <?php else: ?>
        <div class="alert alert-danger text-center">❌ Matière introuvable.</div>
        <div class="text-center mt-3">
            <a href="affiche_matiere.php" class="btn btn-grad">⬅ Retour à la liste</a>
        </div>
    <?php endif; ?>
</div>

<style>
.btn-grad {
    background: linear-gradient(45deg, #ffdd57, #1e3c72);
    color: white;
    border: none;
    border-radius: 30px;
    padding: 10px 25px;
    font-weight: bold;
    transition: all 0.3s;
    text-decoration: none;
    display: inline-block;
}
.btn-grad:hover {
    background: linear-gradient(45deg, #1e3c72, #ffdd57);
    transform: scale(1.05);
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    color:white;
}
.alert {
    padding: 10px 15px;
    border-radius: 10px;
    margin-bottom: 20px;
    font-weight: bold;
}
.alert-success { background-color: rgba(46, 204, 113, 0.2); color: #27ae60; }
.alert-danger { background-color: rgba(231, 76, 60, 0.2); color: #c0392b; }
</style>
