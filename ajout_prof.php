<?php
session_start();
include('cadre.php');

$mysqli = new mysqli("localhost", "root", "", "TEST");
if ($mysqli->connect_error) {
    die("Erreur de connexion : " . $mysqli->connect_error);
}
$mysqli->set_charset("utf8");
?>

<link rel="stylesheet" href="theme.css">


<div class="container my-5">
    <h2 class="text-center mb-4 fancy-title">Ajouter un professeur</h2>

    <div class="form-card shadow-lg p-4">
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!empty($_POST['nom']) && !empty($_POST['prenom']) && !empty($_POST['adresse']) &&
            !empty($_POST['telephone']) && !empty($_POST['pseudo']) && !empty($_POST['passe'])) {

            $nom = htmlspecialchars(trim($_POST['nom']));
            $prenom = htmlspecialchars(trim($_POST['prenom']));
            $adresse = htmlspecialchars(trim($_POST['adresse']));
            $telephone = htmlspecialchars(trim($_POST['telephone']));
            $pseudo = htmlspecialchars(trim($_POST['pseudo']));
            $passe = htmlspecialchars(trim($_POST['passe']));

            $stmt = $mysqli->prepare("SELECT COUNT(*) FROM prof WHERE nom = ? AND prenom = ?");
            $stmt->bind_param("ss", $nom, $prenom);
            $stmt->execute();
            $stmt->bind_result($nb);
            $stmt->fetch();
            $stmt->close();

            if ($nb > 0) {
                echo '<div class="alert alert-danger">⚠️ Ce professeur existe déjà.</div>';
            } else {
                $stmt = $mysqli->prepare("INSERT INTO prof (nom, prenom, adresse, telephone) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $nom, $prenom, $adresse, $telephone);
                if ($stmt->execute()) {
                    $numprof = $mysqli->insert_id;
                    $stmt->close();

                    $stmt = $mysqli->prepare("INSERT INTO login (Num, pseudo, passe, type) VALUES (?, ?, ?, 'prof')");
                    $stmt->bind_param("iss", $numprof, $pseudo, $passe);
                    if ($stmt->execute()) {
                        echo '<div class="alert alert-success">✅ Professeur ajouté avec succès !</div>';
                    } else {
                        echo '<div class="alert alert-danger">❌ Erreur lors de l\'ajout dans login.</div>';
                    }
                    $stmt->close();
                } else {
                    echo '<div class="alert alert-danger">❌ Erreur lors de l\'ajout du professeur.</div>';
                }
            }
        } else {
            echo '<div class="alert alert-warning">⚠️ Veuillez remplir tous les champs.</div>';
        }

        echo '<a href="ajout_prof.php" class="btn btn-grad w-100 mt-3">Revenir à la page précédente</a>';

    } else {
    ?>
        <form action="" method="POST">
            <div class="input-group">
                <span class="input-icon">👤</span>
                <input type="text" name="nom" placeholder="Nom" required>
            </div>

            <div class="input-group">
                <span class="input-icon">📝</span>
                <input type="text" name="prenom" placeholder="Prénom" required>
            </div>

            <div class="input-group">
                <span class="input-icon">🏠</span>
                <textarea name="adresse" placeholder="Adresse" required></textarea>
            </div>

            <div class="input-group">
                <span class="input-icon">📞</span>
                <input type="text" name="telephone" placeholder="Téléphone" required>
            </div>

            <div class="input-group">
                <span class="input-icon">💻</span>
                <input type="text" name="pseudo" placeholder="Pseudo" required>
            </div>

            <div class="input-group">
                <span class="input-icon">🔒</span>
                <input type="password" name="passe" placeholder="Mot de passe" required>
            </div>

            <button type="submit" class="btn-grad w-100 mt-3">Ajouter</button>
        </form>
    <?php } ?>
    </div>
</div>

<style>
/* Container */
.container {
    max-width: 500px;
    margin: auto;
}

/* Title */
.fancy-title {
    color: #1e3c72;
    text-shadow: 2px 2px 10px rgba(0,0,0,0.2);
    font-family: 'Segoe UI', sans-serif;
}

/* Form card */
.form-card {
    border-radius: 20px;
    background: #fff;
    padding: 20px;
    transition: all 0.3s ease;
}

.form-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}

/* Input group */
.input-group {
    position: relative;
    margin-bottom: 20px;
}

.input-icon {
    position: absolute;
    top: 50%;
    left: 15px;
    transform: translateY(-50%);
    font-size: 18px;
}

.input-group input,
.input-group textarea {
    width: 100%;
    padding: 12px 15px 12px 45px;
    border-radius: 12px;
    border: 1px solid #ccc;
    transition: all 0.3s;
}

.input-group input:focus,
.input-group textarea:focus {
    border-color: #ffdd57;
    box-shadow: 0 0 12px rgba(255,221,87,0.5);
    outline: none;
}

/* Button gradient */
.btn-grad {
    background: linear-gradient(45deg, #ffdd57, #1e3c72);
    color: white;
    border: none;
    border-radius: 30px;
    padding: 12px 30px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-grad:hover {
    background: linear-gradient(45deg, #1e3c72, #ffdd57);
    transform: scale(1.05);
    box-shadow: 0 8px 20px rgba(0,0,0,0.3);
}

/* Alerts */
.alert {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 10px;
    text-align: center;
    font-weight: bold;
    animation: fadeIn 0.5s ease-in-out;
}

.alert-success { background-color: #d4edda; color: #155724; }
.alert-warning { background-color: #fff3cd; color: #856404; }
.alert-danger { background-color: #f8d7da; color: #721c24; }

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Responsive */
@media (max-width: 576px) {
    .input-group input,
    .input-group textarea {
        padding-left: 40px;
    }
}
</style>
