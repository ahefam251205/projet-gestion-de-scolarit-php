<?php
session_start();
include('cadre.php');

// Connexion MySQLi
$mysqli = new mysqli("localhost", "root", "", "test");
if ($mysqli->connect_error) die("Erreur de connexion : " . $mysqli->connect_error);

// Fonction helper pour sélectionner l'option par défaut
function choixpardefault($valeur, $valeurComparaison) {
    return ($valeur == $valeurComparaison) ? "selected" : "";
}

$message = '';

// Récupération de la classe
if (isset($_GET['modif_classe'])) {
    $id = (int)$_GET['modif_classe'];
    $res = $mysqli->query("SELECT * FROM classe WHERE codecl=$id");
    if ($res->num_rows === 0) die("Classe introuvable !");
    $classe = $res->fetch_assoc();
}

// Traitement du formulaire POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['nom'], $_POST['promo'], $_POST['prof'])) {
    $id = (int)$_POST['id'];
    $nom = $mysqli->real_escape_string(trim($_POST['nom']));
    $promo = $mysqli->real_escape_string(trim($_POST['promo']));
    $prof = (int)$_POST['prof'];

    $update = $mysqli->query("UPDATE classe SET nom='$nom', promotion='$promo', numprofcoord=$prof WHERE codecl=$id");

    if ($update) {
        $message = "✅ Classe modifiée avec succès !";

        // Recharger les infos de la classe
        $res = $mysqli->query("SELECT * FROM classe WHERE codecl=$id");
        $classe = $res->fetch_assoc();
    } else {
        $message = "❌ Erreur lors de la modification : " . $mysqli->error;
    }
}

// Traitement de la suppression
if (isset($_GET['supprimer'])) {
    $idSup = (int)$_GET['supprimer'];
    $mysqli->query("DELETE FROM classe WHERE codecl=$idSup");
    echo '<script>alert("Classe supprimée avec succès !"); window.location="affiche_classe.php";</script>';
    exit;
}

// Récupération des profs
$profs = $mysqli->query("SELECT numprof, nom FROM prof ORDER BY nom ASC");
?>

<link rel="stylesheet" href="theme.css">

<div class="container my-5">
    <div class="card p-4 shadow-lg">
        <h1 class="text-center mb-4">Modifier la classe</h1>

        <?php if ($message): ?>
            <div class="alert <?= strpos($message,'✅')!==false?'alert-success':'alert-danger'; ?> text-center">
                <?= htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="post" action="modif_classe.php" class="formulaire" style="max-width:600px; margin:auto;">
            <input type="hidden" name="id" value="<?= $classe['codecl'] ?>">

            <div class="mb-3">
                <label>Nom de la classe :</label>
                <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($classe['nom']) ?>" required>
            </div>

            <div class="mb-3">
                <label>Promotion :</label>
                <input type="text" name="promo" class="form-control" value="<?= htmlspecialchars($classe['promotion']) ?>" required>
            </div>

            <div class="mb-3">
                <label>Prof coordonnateur :</label>
                <select name="prof" class="form-select" required>
                    <option value="">-- Choisir un prof --</option>
                    <?php while ($prof = $profs->fetch_assoc()): ?>
                        <option value="<?= $prof['numprof'] ?>" <?= choixpardefault($prof['numprof'],$classe['numprofcoord']) ?>>
                            <?= htmlspecialchars($prof['nom']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="text-center mt-3">
                <button type="submit" class="btn btn-grad">Modifier</button>
                <a href="modif_classe.php?supprimer=<?= $classe['codecl'] ?>" class="btn btn-grad" style="background:#c0392b;">Supprimer</a>
                <a href="affiche_classe.php" class="btn btn-grad" style="background:#888;">⬅ Retour</a>
            </div>
        </form>
    </div>
</div>

<style>
.btn-grad {
    background: linear-gradient(45deg, #ffdd57, #1e3c72);
    color:white;
    border:none;
    border-radius:30px;
    padding:10px 25px;
    font-weight:bold;
    transition: all 0.3s;
    text-decoration:none;
    display:inline-block;
    margin:5px;
}
.btn-grad:hover {
    background: linear-gradient(45deg, #1e3c72, #ffdd57);
    transform: scale(1.05);
    box-shadow:0 5px 15px rgba(0,0,0,0.3);
    color:white;
}
.alert { padding:10px 15px; border-radius:10px; margin-bottom:20px; font-weight:bold; }
.alert-success { background-color: rgba(46,204,113,0.2); color:#27ae60; }
.alert-danger { background-color: rgba(231,76,60,0.2); color:#c0392b; }
</style>

<?php
$mysqli->close();
?>
