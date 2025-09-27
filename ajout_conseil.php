<?php
session_start();
include('cadre.php');

// Connexion sécurisée
$mysqli = new mysqli("localhost", "root", "", "test");
if ($mysqli->connect_error) {
    die("Connexion échouée : " . $mysqli->connect_error);
}
$mysqli->set_charset("utf8");
?>

<link rel="stylesheet" href="theme.css">

<div class="container my-5">
    <div class="card p-4 shadow-lg">
        <h1 class="text-center mb-4" style="color: #1e3c72; text-shadow: 2px 2px 8px rgba(0,0,0,0.2);">
            Ajout d'un Conseil de Classe
        </h1>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nomcl'], $_POST['promotion'], $_POST['radiosem'])) {
    $nomcl = $_POST['nomcl'];
    $promo = $_POST['promotion'];
    $semestre = $_POST['radiosem'];

    // Obtenir codecl
    $stmt = $mysqli->prepare("SELECT codecl FROM classe WHERE nom = ? AND promotion = ?");
    $stmt->bind_param("ss", $nomcl, $promo);
    $stmt->execute();
    $result = $stmt->get_result();
    $codecl = $result->fetch_assoc()['codecl'] ?? null;

    if (!$codecl) {
        echo '<div class="alert alert-danger text-center">Classe introuvable !</div>';
    } else {
        // Vérifier si le conseil existe déjà
        $check = $mysqli->prepare("SELECT COUNT(*) as nb FROM conseil WHERE numsem = ? AND codecl = ?");
        $check->bind_param("ii", $semestre, $codecl);
        $check->execute();
        $nb = $check->get_result()->fetch_assoc()['nb'];

        if ($nb > 0) {
            echo '<div class="alert alert-warning text-center">❌ Ce conseil existe déjà pour cette classe et ce semestre.</div>';
        } else {
            // Insertion dans conseil
            $insert = $mysqli->prepare("INSERT INTO conseil (numsem, codecl) VALUES (?, ?)");
            $insert->bind_param("ii", $semestre, $codecl);
            $insert->execute();

            // Calculer les moyennes et remplir le bulletin
            $query = "
                SELECT e.numel, m.codemat, AVG(ev.note) AS moyen
                FROM eleve e
                JOIN evaluation ev ON ev.numel = e.numel
                JOIN devoir d ON d.numdev = ev.numdev
                JOIN matiere m ON m.codemat = d.codemat
                WHERE d.codecl = ? AND d.numsem = ?
                GROUP BY e.numel, m.codemat
            ";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("ii", $codecl, $semestre);
            $stmt->execute();
            $bulletins = $stmt->get_result();

            $insertBulletin = $mysqli->prepare("INSERT INTO bulletin (numsem, numel, codemat, notefinal) VALUES (?, ?, ?, ?)");

            while ($row = $bulletins->fetch_assoc()) {
                $insertBulletin->bind_param(
                    "iiid",
                    $semestre,
                    $row['numel'],
                    $row['codemat'],
                    $row['moyen']
                );
                $insertBulletin->execute();
            }

            echo '<div class="alert alert-success text-center">✅ Conseil ajouté et bulletins générés avec succès !</div>';
        }
    }

    echo '<div class="text-center mt-3"><a href="ajout_conseil.php" class="btn btn-grad">⬅️ Retour</a></div>';

} else {
    // Formulaire
    $promotions = $mysqli->query("SELECT DISTINCT promotion FROM classe ORDER BY promotion DESC");
    $classes = $mysqli->query("SELECT DISTINCT nom FROM classe");
    ?>
    <form method="post" action="ajout_conseil.php" class="p-3">
        <div class="mb-3">
            <label class="form-label">Promotion :</label>
            <select name="promotion" class="form-select" required>
                <?php while ($row = $promotions->fetch_assoc()) : ?>
                    <option value="<?= htmlspecialchars($row['promotion']) ?>"><?= htmlspecialchars($row['promotion']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Classe :</label>
            <select name="nomcl" class="form-select" required>
                <?php while ($row = $classes->fetch_assoc()) : ?>
                    <option value="<?= htmlspecialchars($row['nom']) ?>"><?= htmlspecialchars($row['nom']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Semestre :</label>
            <select name="radiosem" class="form-select" required>
                <?php for ($i = 1; $i <= 4; $i++) : ?>
                    <option value="<?= $i ?>">Semestre <?= $i ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-grad w-100">Valider le Conseil</button>
        </div>
    </form>
<?php } ?>
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
.alert-warning { background-color: rgba(241, 196, 15, 0.2); color: #f39c12; }
.alert-danger { background-color: rgba(231, 76, 60, 0.2); color: #c0392b; }
</style>
