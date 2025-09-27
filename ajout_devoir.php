<?php
session_start();
include('cadre.php');

$conn = mysqli_connect("localhost", "root", "", "test");
if (!$conn) die("Erreur de connexion à la base : " . mysqli_connect_error());
?>

<link rel="stylesheet" href="theme.css">

<div class="corp">
<center>

<?php
// ==== Étape 1 : Sélection classe/promotion -> Formulaire matières ====
if (isset($_POST['nomcl'], $_POST['promotion'])):

    $_SESSION['nomcl'] = $_POST['nomcl'];
    $_SESSION['promo'] = $_POST['promotion'];
    $nomcl = $_POST['nomcl'];
    $promo = $_POST['promotion'];

    $sql = "SELECT codemat, nommat FROM matiere 
            INNER JOIN classe ON matiere.codecl = classe.codecl 
            WHERE nom = ? AND promotion = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $nomcl, $promo);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
?>

<form action="ajout_devoir.php" method="POST" class="login-card" style="max-width:500px;margin:auto;">
    <h4>Ajouter un devoir</h4>

    <label>Matière :</label>
    <select name="choix_mat" id="choix" class="form-control" required>
    <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <option value="<?= htmlspecialchars($row['codemat']) ?>"><?= htmlspecialchars($row['nommat']) ?></option>
    <?php endwhile; ?>
    </select><br/>

    <label>Date du devoir :</label>
    <input type="date" name="date" class="form-control" required><br/>

    <label>Coefficient :</label>
    <select name="coefficient" class="form-control">
        <?php for ($i=1; $i<=15; $i++): ?>
            <option value="<?= $i ?>"><?= $i ?></option>
        <?php endfor; ?>
    </select><br/>

    <label>Semestre :</label>
    <select name="semestre" class="form-control">
        <?php for ($i=1; $i<=4; $i++): ?>
            <option value="<?= $i ?>">Semestre <?= $i ?></option>
        <?php endfor; ?>
    </select><br/>

    <label>1er / 2ème Devoir :</label><br/>
    <input type="radio" name="devoir" value="1" id="choix1" required /> <label for="choix1">1er devoir</label>
    <input type="radio" name="devoir" value="2" id="choix2" /> <label for="choix2">2ème devoir</label><br/><br/>

    <input type="submit" value="Ajouter" class="btn btn-gold-blue">
</form>
<?php
    mysqli_stmt_close($stmt);

// ==== Étape 2 : Soumission du devoir ====
elseif (isset($_POST['date'])):

    $codemat = $_POST['choix_mat'] ?? null;
    $coefficient = (int)($_POST['coefficient'] ?? null);
    $semestre = (int)($_POST['semestre'] ?? null);
    $n_devoir = (int)($_POST['devoir'] ?? null);
    $date = $_POST['date'] ?? null;
    $nomcl = $_SESSION['nomcl'] ?? null;
    $promo = $_SESSION['promo'] ?? null;

    if (!$codemat || !$date || !$coefficient || !$semestre || !$n_devoir || !$nomcl || !$promo) {
        echo "<h2>Erreur : informations manquantes.</h2><a href='ajout_devoir.php'>Revenir</a>";
        exit;
    }

    // Récupérer codecl
    $stmt = mysqli_prepare($conn, "SELECT codecl FROM classe WHERE nom = ? AND promotion = ?");
    mysqli_stmt_bind_param($stmt, "ss", $nomcl, $promo);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($res);
    if (!$row) { echo "<h2>Classe introuvable.</h2>"; exit; }
    $codecl = $row['codecl'];
    mysqli_stmt_close($stmt);

    // Vérifier enseignement
    $stmt = mysqli_prepare($conn, "SELECT COUNT(*) as nb FROM enseignement WHERE codecl = ? AND codemat = ? AND numsem = ?");
    mysqli_stmt_bind_param($stmt, "isi", $codecl, $codemat, $semestre);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt);
    if ($row['nb'] == 0) { echo "<h2>Erreur : Cet enseignement n'existe pas.</h2>"; exit; }

    // Vérifier doublon devoir
    $stmt = mysqli_prepare($conn, "SELECT COUNT(*) as nb FROM devoir WHERE codecl = ? AND codemat = ? AND numsem = ? AND n_devoir = ?");
    mysqli_stmt_bind_param($stmt, "isii", $codecl, $codemat, $semestre, $n_devoir);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt);
    if ($row['nb'] > 0) { echo "<h2>Erreur : Devoir déjà enregistré.</h2>"; exit; }

    // Insertion
    $stmt = mysqli_prepare($conn, "INSERT INTO devoir(date_dev, coeficient, codemat, codecl, numsem, n_devoir) VALUES (?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sisiii", $date, $coefficient, $codemat, $codecl, $semestre, $n_devoir);
    if (mysqli_stmt_execute($stmt)) echo "<h2>Insertion réussie !</h2>";
    else echo "<h2>Erreur lors de l'insertion : " . mysqli_error($conn) . "</h2>";
    mysqli_stmt_close($stmt);

    echo '<br/><a href="ajout_devoir.php" class="btn btn-primary">Revenir</a>';

// ==== Étape 3 : Formulaire initial ====
else:
    $res_promo = mysqli_query($conn, "SELECT DISTINCT promotion FROM classe ORDER BY promotion DESC");
    $res_classe = mysqli_query($conn, "SELECT DISTINCT nom FROM classe");
?>
<form action="ajout_devoir.php" method="POST" class="login-card" style="max-width:500px;margin:auto;">
    <h4>Classe / Promotion</h4>
    <label>Promotion :</label>
    <select name="promotion" class="form-control" required>
        <option value="">-- Choisir une promotion --</option>
        <?php while ($row = mysqli_fetch_assoc($res_promo)): ?>
            <option value="<?= htmlspecialchars($row['promotion']) ?>"><?= htmlspecialchars($row['promotion']) ?></option>
        <?php endwhile; ?>
    </select><br/>
    <label>Classe :</label>
    <select name="nomcl" class="form-control" required>
        <option value="">-- Choisir une classe --</option>
        <?php while ($row = mysqli_fetch_assoc($res_classe)): ?>
            <option value="<?= htmlspecialchars($row['nom']) ?>"><?= htmlspecialchars($row['nom']) ?></option>
        <?php endwhile; ?>
    </select><br/>
    <input type="submit" value="Suivant" class="btn btn-gold-blue">
</form>
<?php endif; ?>

</center>
</div>
