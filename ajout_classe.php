<?php
session_start();
include('cadre.php');

// Connexion sécurisée avec mysqli
$mysqli = new mysqli("localhost", "root", "", "test");
if ($mysqli->connect_error) {
    die("Erreur de connexion : " . $mysqli->connect_error);
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['numprof'], $_POST['nomcl'], $_POST['promotion'])) {
    $nomcl = $mysqli->real_escape_string(trim($_POST['nomcl']));
    $numprof = (int) $_POST['numprof'];
    $promotion = $mysqli->real_escape_string(trim($_POST['promotion']));

    // Vérifier si la classe existe déjà
    $check = $mysqli->query("SELECT COUNT(*) AS nb FROM classe WHERE nom='$nomcl' AND promotion='$promotion'");
    $row = $check->fetch_assoc();

    if ($row['nb'] > 0) {
        $message = "❌ Classe déjà enregistrée pour cette promotion.";
    } else {
        $insert = $mysqli->query("INSERT INTO classe (nom, numprofcoord, promotion) VALUES ('$nomcl', $numprof, '$promotion')");
        if ($insert) {
            $message = "✅ Classe ajoutée avec succès !";
        } else {
            $message = "❌ Une erreur est survenue lors de l'ajout.";
        }
    }
}

// Récupération des profs pour la liste déroulante
$profs = $mysqli->query("SELECT numprof, nom FROM prof ORDER BY nom ASC");
?>

<link rel="stylesheet" href="theme.css">

<div class="container my-5">
    <div class="card p-4 shadow-lg">
        <h1 class="text-center mb-4" style="color: #1e3c72; text-shadow: 2px 2px 8px rgba(0,0,0,0.2);">
            Ajout d'une classe
        </h1>

        <?php if ($message): ?>
            <div class="alert <?php echo strpos($message, '✅') !== false ? 'alert-success' : 'alert-danger'; ?> text-center">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="post" action="ajout_classe.php">
            <div class="mb-3">
                <label>Nom de la classe :</label>
                <input type="text" name="nomcl" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Promotion :</label>
                <input type="text" name="promotion" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Prof coordonnateur :</label>
                <select name="numprof" class="form-select" required>
                    <option value="">-- Choisir un prof --</option>
                    <?php while ($prof = $profs->fetch_assoc()): ?>
                        <option value="<?php echo $prof['numprof']; ?>">
                            <?php echo htmlspecialchars($prof['nom']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-grad">Ajouter</button>
            </div>
        </form>

        <div class="text-center mt-3">
            <a href="index.php" class="btn btn-grad">⬅ Revenir à l'accueil</a>
        </div>
    </div>
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
