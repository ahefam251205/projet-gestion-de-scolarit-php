<?php
session_start();
include('cadre.php'); // menu/navigation/etc.

$mysqli = new mysqli("localhost", "root", "", "test");
if ($mysqli->connect_error) {
    die("Erreur de connexion : " . $mysqli->connect_error);
}
$mysqli->set_charset("utf8");
?>

<link rel="stylesheet" href="theme.css">


<div class="container my-5">
    <h2 class="text-center mb-4 fancy-title">Ajouter une matière</h2>

    <div class="form-card shadow-lg p-4">
        <?php
        // Étape 1 : sélection promotion et classe
        if (!isset($_POST['nommat']) && !isset($_POST['promotion'])) {
            $resultPromo = $mysqli->query("SELECT DISTINCT promotion FROM classe ORDER BY promotion DESC");
            $resultClasse = $mysqli->query("SELECT DISTINCT nom FROM classe");

            echo '<form action="" method="POST">';
            
            echo '<div class="input-group">';
            echo '<span class="input-icon">🎓</span>';
            echo '<select name="promotion" class="form-select" required>';
            echo '<option value="">-- Choisir la promotion --</option>';
            while ($row = $resultPromo->fetch_assoc()) {
                echo '<option value="' . htmlspecialchars($row['promotion']) . '">' . htmlspecialchars($row['promotion']) . '</option>';
            }
            echo '</select></div>';

            echo '<div class="input-group">';
            echo '<span class="input-icon">🏫</span>';
            echo '<select name="nomcl" class="form-select" required>';
            echo '<option value="">-- Choisir la classe --</option>';
            while ($row = $resultClasse->fetch_assoc()) {
                echo '<option value="' . htmlspecialchars($row['nom']) . '">' . htmlspecialchars($row['nom']) . '</option>';
            }
            echo '</select></div>';

            echo '<button type="submit" class="btn-grad w-100 mt-3">Suivant</button>';
            echo '</form>';
        }

        // Étape 2 : Formulaire de saisie matière
        else if (isset($_POST['promotion']) && !isset($_POST['nommat'])) {
            $_SESSION['promo'] = $_POST['promotion'];
            $_SESSION['nomcl'] = $_POST['nomcl'];

            echo '<form action="" method="POST">';
            echo '<div class="input-group">';
            echo '<span class="input-icon">📝</span>';
            echo '<input type="text" name="nommat" class="form-control" placeholder="Nom de la matière" required>';
            echo '</div>';
            echo '<button type="submit" class="btn-grad w-100 mt-3">Ajouter la matière</button>';
            echo '</form>';
        }

        // Étape 3 : Traitement ajout matière
        else if (isset($_POST['nommat'])) {
            $nommat = htmlspecialchars(trim($_POST['nommat']));
            $promo = $_SESSION['promo'];
            $nomcl = $_SESSION['nomcl'];

            if (!empty($nommat)) {
                $stmt = $mysqli->prepare("SELECT codecl FROM classe WHERE nom = ? AND promotion = ?");
                $stmt->bind_param("ss", $nomcl, $promo);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();

                if ($row) {
                    $codecl = $row['codecl'];
                    $stmt2 = $mysqli->prepare("SELECT COUNT(*) as nb FROM matiere WHERE nommat = ? AND codecl = ?");
                    $stmt2->bind_param("si", $nommat, $codecl);
                    $stmt2->execute();
                    $res2 = $stmt2->get_result()->fetch_assoc();

                    if ($res2['nb'] > 0) {
                        echo '<div class="alert alert-warning">⚠️ Cette matière existe déjà pour cette classe.</div>';
                    } else {
                        $stmt3 = $mysqli->prepare("INSERT INTO matiere (nommat, codecl) VALUES (?, ?)");
                        $stmt3->bind_param("si", $nommat, $codecl);
                        if ($stmt3->execute()) {
                            echo '<div class="alert alert-success">✅ Matière ajoutée avec succès !</div>';
                        } else {
                            echo '<div class="alert alert-danger">❌ Erreur lors de l\'insertion.</div>';
                        }
                    }
                } else {
                    echo '<div class="alert alert-danger">❌ Classe introuvable.</div>';
                }
            } else {
                echo '<div class="alert alert-danger">❌ Veuillez remplir tous les champs.</div>';
            }

            echo '<a href="ajout_matiere.php" class="btn btn-secondary mt-3 w-100">Revenir à la page précédente</a>';
        }
        ?>
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
.input-group select {
    width: 100%;
    padding: 12px 15px 12px 45px;
    border-radius: 12px;
    border: 1px solid #ccc;
    transition: all 0.3s;
}

.input-group input:focus,
.input-group select:focus {
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
    .input-group select {
        padding-left: 40px;
    }
}
</style>
