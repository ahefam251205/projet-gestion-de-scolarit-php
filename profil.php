<?php
session_start();
require_once("connect.php");

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$message = "";

// Récupération infos utilisateur
$username = $_SESSION['username'];
$type = $_SESSION['type'];
$stmt = $conn->prepare("SELECT pseudo, passe, type FROM login WHERE pseudo=? AND type=?");
$stmt->bind_param("ss", $username, $type);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Modification pseudo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['newpseudo'])) {
    $newpseudo = trim($_POST['newpseudo']);
    if ($newpseudo) {
        $stmt = $conn->prepare("UPDATE login SET pseudo=? WHERE pseudo=? AND type=?");
        $stmt->bind_param("sss", $newpseudo, $username, $type);
        if ($stmt->execute()) {
            $_SESSION['username'] = $newpseudo;
            $message = "Pseudo mis à jour avec succès !";
        }
        $stmt->close();
    } else {
        $message = "Pseudo vide !";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Profil</title>
<link rel="stylesheet" href="bootstrap.min.css">
</head>
<body class="p-5">
<div class="container w-50">
<h2>Mon profil</h2>
<?php if($message) echo '<div class="alert alert-info">'.$message.'</div>'; ?>
<form method="POST">
    <div class="mb-3">
        <label>Pseudo actuel : <?= htmlspecialchars($username) ?></label>
    </div>
    <div class="mb-3">
        <label>Nouveau pseudo</label>
        <input type="text" name="newpseudo" class="form-control">
    </div>
    <div class="mb-3">
        <label>Type : <?= htmlspecialchars($type) ?></label>
    </div>
    <button type="submit" class="btn btn-primary">Modifier</button>
    <a href="index.php" class="btn btn-secondary">Retour</a>
</form>
</div>
</body>
</html>
