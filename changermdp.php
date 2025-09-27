<?php
session_start();
require_once("connect.php");

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$message = "";

$username = $_SESSION['username'];
$type = $_SESSION['type'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ancien = trim($_POST['ancien']);
    $nouveau = trim($_POST['nouveau']);
    $confirmer = trim($_POST['confirmer']);

    if ($nouveau !== $confirmer) {
        $message = "Les mots de passe ne correspondent pas.";
    } else {
        // Vérification de l'ancien mot de passe
        $stmt = $conn->prepare("SELECT passe FROM login WHERE pseudo=? AND type=?");
        $stmt->bind_param("ss", $username, $type);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if ($ancien === $user['passe']) {
            $stmt = $conn->prepare("UPDATE login SET passe=? WHERE pseudo=? AND type=?");
            $stmt->bind_param("sss", $nouveau, $username, $type);
            if ($stmt->execute()) {
                $message = "Mot de passe changé avec succès !";
            }
            $stmt->close();
        } else {
            $message = "Ancien mot de passe incorrect.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Changer mot de passe</title>
<link rel="stylesheet" href="bootstrap.min.css">
</head>
<body class="p-5">
<div class="container w-50">
<h2>Changer mot de passe</h2>
<?php if($message) echo '<div class="alert alert-info">'.$message.'</div>'; ?>
<form method="POST">
    <div class="mb-3"><label>Ancien mot de passe</label><input type="password" name="ancien" class="form-control" required></div>
    <div class="mb-3"><label>Nouveau mot de passe</label><input type="password" name="nouveau" class="form-control" required></div>
    <div class="mb-3"><label>Confirmer nouveau mot de passe</label><input type="password" name="confirmer" class="form-control" required></div>
    <button type="submit" class="btn btn-success">Changer</button>
    <a href="index.php" class="btn btn-secondary">Retour</a>
</form>
</div>
</body>
</html>
