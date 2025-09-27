<?php
session_start();
require_once('connect.php'); // Connexion mysqli, variable $conn
include('cadre.php'); // Layout

// Fonctions utilitaires pour récupérer promotions et classes
function getPromotions($conn) {
    $res = $conn->query("SELECT DISTINCT promotion FROM classe ORDER BY promotion DESC");
    $promos = [];
    while ($row = $res->fetch_assoc()) $promos[] = $row['promotion'];
    return $promos;
}

function getClasses($conn) {
    $res = $conn->query("SELECT DISTINCT nom FROM classe ORDER BY nom");
    $classes = [];
    while ($row = $res->fetch_assoc()) $classes[] = $row['nom'];
    return $classes;
}

$promotions = getPromotions($conn);
$classes = getClasses($conn);
?>

<link rel="stylesheet" href="theme.css">

<div class="container my-5">
    <div class="card p-4 shadow-lg">

<?php
// ------------------------------
// Étape 2 : afficher les notes finales
// ------------------------------
if (isset($_POST['codemat'], $_POST['nomclasse'], $_POST['promo'], $_POST['semestre'])) {

    $codemat = $_POST['codemat'];
    $nomcl = $_POST['nomclasse'];
    $promo = $_POST['promo'];
    $semestre = $_POST['semestre'];

    $sql = "SELECT eleve.nomel, eleve.prenomel, classe.nom AS classe, classe.promotion, matiere.nommat, bulletin.numsem, bulletin.notefinal
            FROM eleve
            JOIN bulletin ON eleve.numel = bulletin.numel
            JOIN matiere ON matiere.codemat = bulletin.codemat
            JOIN classe ON classe.codecl = eleve.codecl
            WHERE matiere.codemat = ?
              AND bulletin.numsem = ?
              AND eleve.codecl = (SELECT codecl FROM classe WHERE nom=? AND promotion=?)
            ORDER BY eleve.nomel, eleve.prenomel";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siss", $codemat, $semestre, $nomcl, $promo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo '<div class="alert alert-warning text-center">Aucune note finale trouvée pour cette matière.</div>';
    } else {
        echo '<div class="table-responsive">
                <table class="table table-striped styled-table">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Classe</th>
                            <th>Promotion</th>
                            <th>Matière</th>
                            <th>Semestre</th>
                            <th>Note Finale</th>
                        </tr>
                    </thead>
                    <tbody>';
        while ($row = $result->fetch_assoc()) {
            echo '<tr>
                    <td>'.htmlspecialchars($row['nomel']).'</td>
                    <td>'.htmlspecialchars($row['prenomel']).'</td>
                    <td>'.htmlspecialchars($row['classe']).'</td>
                    <td>'.htmlspecialchars($row['promotion']).'</td>
                    <td>'.htmlspecialchars($row['nommat']).'</td>
                    <td>'.htmlspecialchars($row['numsem']).'</td>
                    <td>'.htmlspecialchars($row['notefinal']).'</td>
                  </tr>';
        }
        echo '</tbody></table></div>';
    }

    echo '<div class="text-center mt-3">
            <a href="afficher_bullettin.php" class="btn btn-grad">⬅️ Revenir à la sélection</a>
          </div>';
    $stmt->close();

// ------------------------------
// Étape 1 : choisir la matière
// ------------------------------
} elseif (isset($_POST['nomcl'], $_POST['promotion'], $_POST['radiosem'])) {

    $nomcl = $_POST['nomcl'];
    $promo = $_POST['promotion'];
    $semestre = $_POST['radiosem'];

    $sql = "SELECT matiere.codemat, matiere.nommat
            FROM enseignement
            JOIN matiere ON enseignement.codemat = matiere.codemat
            WHERE enseignement.codecl = (SELECT codecl FROM classe WHERE nom=? AND promotion=?)
              AND enseignement.numsem=?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $nomcl, $promo, $semestre);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo '<div class="alert alert-warning text-center">Aucune matière trouvée pour cette sélection.</div>';
        echo '<div class="text-center mt-3">
                <a href="afficher_bullettin.php" class="btn btn-grad">⬅️ Revenir à la sélection</a>
              </div>';
    } else {
        echo '<form method="post" action="afficher_bullettin.php" class="formulaire p-3">';
        echo '<h4 class="text-center mb-3">Choisir la matière pour : '.htmlspecialchars($nomcl).' '.htmlspecialchars($promo).'</h4>';
        echo '<div class="mb-3">
                <label>Matière :</label>
                <select name="codemat" class="form-select">';
        while ($row = $result->fetch_assoc()) {
            echo '<option value="'.htmlspecialchars($row['codemat']).'">'.htmlspecialchars($row['nommat']).'</option>';
        }
        echo '</select>
              <input type="hidden" name="nomclasse" value="'.htmlspecialchars($nomcl).'">
              <input type="hidden" name="promo" value="'.htmlspecialchars($promo).'">
              <input type="hidden" name="semestre" value="'.htmlspecialchars($semestre).'">
              </div>
              <button type="submit" class="btn btn-grad w-100">Afficher les notes finales</button>
              <div class="text-center mt-3">
                <a href="afficher_bullettin.php" class="btn btn-secondary">⬅️ Revenir à la sélection</a>
              </div>
        </form>';
    }
    $stmt->close();

// ------------------------------
// Formulaire initial
// ------------------------------
} else {
    ?>
    <form method="post" action="afficher_bullettin.php" class="formulaire p-3">
        <h4 class="text-center mb-3">Afficher les bulletins</h4>
        <div class="mb-3">
            <label>Promotion :</label>
            <select name="promotion" class="form-select" required>
                <?php foreach ($promotions as $p): ?>
                    <option value="<?= htmlspecialchars($p) ?>"><?= htmlspecialchars($p) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label>Classe :</label>
            <select name="nomcl" class="form-select" required>
                <?php foreach ($classes as $c): ?>
                    <option value="<?= htmlspecialchars($c) ?>"><?= htmlspecialchars($c) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label>Semestre :</label>
            <select name="radiosem" class="form-select" required>
                <?php for ($i=1; $i<=4; $i++): ?>
                    <option value="<?= $i ?>">Semestre <?= $i ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-grad w-100">Afficher les matières</button>
    </form>
<?php
}
?>
</div>
