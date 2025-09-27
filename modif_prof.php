<?php
session_start();
include("header.php");
include("connect.php"); // Connexion mysqli sécurisée

$message = '';

// Récupérer le prof si on clique sur modifier
if(isset($_GET['modif_prof'])){
    $id = (int)$_GET['modif_prof'];
    $stmt = $conn->prepare("SELECT * FROM prof WHERE numprof=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $prof = $result->fetch_assoc();
}

// Traitement du formulaire
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])){
    $id = (int)$_POST['id'];
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $adresse = nl2br(htmlspecialchars($_POST['adresse']));
    $telephone = htmlspecialchars($_POST['telephone']);

    if($nom && $prenom && $adresse && $telephone){
        $stmt = $conn->prepare("UPDATE prof SET nom=?, prenom=?, adresse=?, telephone=? WHERE numprof=?");
        $stmt->bind_param("ssssi", $nom, $prenom, $adresse, $telephone, $id);
        if($stmt->execute()){
            $message = '<div class="alert alert-success text-center">✅ Modifié avec succès !</div>';
        } else {
            $message = '<div class="alert alert-danger text-center">❌ Erreur lors de la modification.</div>';
        }
        $stmt->close();
    } else {
        $message = '<div class="alert alert-danger text-center">❌ Tous les champs sont obligatoires.</div>';
    }
}

// Suppression
if(isset($_GET['supp_prof'])){
    $id = (int)$_GET['supp_prof'];
    $stmt = $conn->prepare("DELETE FROM prof WHERE numprof=?");
    $stmt->bind_param("i", $id);
    if($stmt->execute()){
        echo '<script>alert("Supprimé avec succès !");window.location.href="liste_prof.php";</script>';
        exit;
    } else {
        echo '<script>alert("Erreur lors de la suppression !");window.location.href="liste_prof.php";</script>';
        exit;
    }
}
?>

<link rel="stylesheet" href="theme.css">

<div class="container my-5">
    <h1 class="text-center mb-4" style="color: #1e3c72; text-shadow: 2px 2px 8px rgba(0,0,0,0.2);">
        Modifier un professeur
    </h1>

    <?= $message ?? '' ?>

    <?php if(isset($prof)): ?>
    <form method="POST" action="modif_prof.php?modif_prof=<?= $prof['numprof'] ?>">
        <input type="hidden" name="id" value="<?= $prof['numprof'] ?>">

        <div class="mb-3">
            <label>Nom :</label>
            <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($prof['nom']) ?>" required>
        </div>

        <div class="mb-3">
            <label>Prénom :</label>
            <input type="text" name="prenom" class="form-control" value="<?= htmlspecialchars($prof['prenom']) ?>" required>
        </div>

        <div class="mb-3">
            <label>Adresse :</label>
            <textarea name="adresse" class="form-control" required><?= htmlspecialchars($prof['adresse']) ?></textarea>
        </div>

        <div class="mb-3">
            <label>Téléphone :</label>
            <input type="text" name="telephone" class="form-control" value="<?= htmlspecialchars($prof['telephone']) ?>" required>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-grad">Modifier</button>
            <a href="javascript:history.back()" class="btn btn-grad ms-2">⬅ Retour</a>
            <a href="modif_prof.php?supp_prof=<?= $prof['numprof'] ?>" class="btn btn-grad ms-2"
               onclick="return confirm('Voulez-vous vraiment supprimer ce professeur ?');">Supprimer</a>
        </div>
    </form>
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
