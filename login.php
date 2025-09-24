<?php
session_start();
require_once("connect.php");

$error = "";

// Redirection si déjà connecté
if ((isset($_SESSION['admin']) && $_SESSION['admin'] === true) ||
    isset($_SESSION['etudiant']) || isset($_SESSION['prof'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $usertype = trim($_POST['type'] ?? '');

    if (!empty($username) && !empty($password) && !empty($usertype)) {
        $stmt = $conn->prepare("SELECT * FROM login WHERE pseudo = ? AND type = ?");
        $stmt->bind_param("ss", $username, $usertype);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            if ($password === $user['passe']) { // ⚡ mot de passe en clair
                // Définir la session selon le type
                if ($usertype === 'admin') {
                    $_SESSION['admin'] = true;
                } elseif ($usertype === 'etudiant') {
                    $_SESSION['etudiant'] = $user['Num']; // Num correspond à l’ID élève
                } elseif ($usertype === 'prof') {
                    $_SESSION['prof'] = $user['Num'];
                }
                $_SESSION['username'] = $user['pseudo'];
                $_SESSION['type'] = $user['type'];

                header("Location: index.php");
                exit();
            } else {
                $error = "Mot de passe incorrect.";
            }
        } else {
            $error = "Utilisateur non trouvé pour ce type.";
        }
        $stmt->close();
    } else {
        $error = "Veuillez remplir tous les champs.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Connexion - Bob school</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="theme.css">
<style>
.login-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background: linear-gradient(135deg,#1e3c72,#2a5298);
}

.login-card {
    background: white;
    padding: 30px;
    border-radius: 20px;
    width: 350px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}

.alert {
    text-align: center;
    font-weight: bold;
}

</style>
</head>
<body>
<div class="login-wrapper">
<div class="login-card">
    <h3 class="mb-4 text-center"><i class="bi bi-university-fill"></i> Bob School</h3>

    <?php if (!empty($error)) : ?>
        <div class="alert alert-danger mb-3"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3 input-group">
            <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
            <input type="text" name="username" class="form-control" placeholder="Nom d'utilisateur" required autofocus>
        </div>

        <div class="mb-3 input-group">
            <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
            <input type="password" name="password" class="form-control" placeholder="Mot de passe" required>
        </div>

        <div class="mb-4 input-group">
            <span class="input-group-text"><i class="bi bi-person-badge-fill"></i></span>
            <select name="type" class="form-select" required>
                <option value="">-- Choisir le type --</option>
                <option value="admin">Admin</option>
                <option value="etudiant">Étudiant</option>
                <option value="prof">Prof</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-box-arrow-in-right"></i> Se connecter</button>
    </form>
</div>
</div>
</body>
</html>
