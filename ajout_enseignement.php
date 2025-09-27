<?php
session_start();
include('cadre.php');

$conn = new mysqli("localhost", "root", "", "test");
if ($conn->connect_error) die("Erreur de connexion : " . $conn->connect_error);
?>

<link rel="stylesheet" href="theme.css">

<div class="corp">
<center>

<?php
if (isset($_POST['nomcl'])):
    $_SESSION['nomcl'] = $_POST['nomcl'];
    $nomcl = $_POST['nomcl'];
    $promo = $_POST['promotion'];
    $_SESSION['promo'] = $promo;

    $stmt = $conn->prepare("SELECT codemat, nommat FROM matiere INNER JOIN classe ON matiere.codecl = classe.codecl WHERE classe.nom = ? AND promotion = ?");
    $stmt->bind_param("ss", $nomcl, $promo);
    $stmt->execute();
    $donnee = $stmt->get_result();

    $prof = $conn->query("SELECT numprof, nom, prenom FROM prof");
?>
<div class="login-card" style="max-width:500px;margin:auto;">
<h4>Ajout d'un enseignement</h4>
<form action="ajout_enseignement.php" method="POST">
    <label>Matière :</label>
    <select name="choix_mat" class="form-control">
        <?php while ($a = $donnee->fetch_assoc()): ?>
            <option value="<?= htmlspecialchars($a['codemat']) ?>"><?= htmlspecialchars($a['nommat']) ?></option>
        <?php endwhile; ?>
    </select><br/>

    <label>Enseignant :</label>
    <select name="n_prof" class="form-control">
        <?php while ($prof2 = $prof->fetch_assoc()): ?>
            <option value="<?= htmlspecialchars($prof2['numprof']) ?>"><?= htmlspecialchars($prof2['nom'].' '.$prof2['prenom']) ?></option>
        <?php endwhile; ?>
    </select><br/>

    <label>Semestre :</label>
    <select name="semestre" class="form-control">
        <?php for ($i = 1; $i <= 4; $i++): ?>
            <option value="<?= $i ?>">Semestre <?= $i ?></option>
        <?php endfor; ?>
    </select><br/>

    <input type="submit" value="Ajouter" class="btn btn-gold-blue">
</form>
</div>
<?php
    $stmt->close();
elseif (isset($_POST['semestre'])):
    $semestre = (int)$_POST['semestre'];
    $codemat = $_POST['choix_mat'];
    $nomcl = $_SESSION['nomcl'];
    $n_prof = $_POST['n_prof'];
    $promo = $_SESSION['promo'];

    $stmt = $conn->prepare("SELECT codecl FROM classe WHERE nom = ? AND promotion = ?");
    $stmt->bind_param("ss", $nomcl, $promo);
    $stmt->execute();
    $res = $stmt->get_result();
    $codeclasse = $res->fetch_assoc();
    $codecl = $codeclasse['codecl'] ?? null;
    $stmt->close();

    if (!$codecl) {
        echo '<div class="alert alert-danger">Classe introuvable</div>';
        exit;
    }

    $stmt = $conn->prepare("SELECT COUNT(*) as nb FROM enseignement WHERE codecl = ? AND codemat = ? AND numsem = ?");
    $stmt->bind_param("isi", $codecl, $codemat, $semestre);
    $stmt->execute();
    $res = $stmt->get_result();
    $nb = $res->fetch_assoc()['nb'];
    $stmt->close();

    if ($nb > 0) {
        echo '<script>alert("Erreur d\'insertion : enseignement déjà existant");</script>';
    } else {
        $stmt = $conn->prepare("INSERT INTO enseignement(codecl, codemat, numprof, numsem) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isis", $codecl, $codemat, $n_prof, $semestre);
        if ($stmt->execute()) echo '<script>alert("Ajouté avec succès!");</script>';
        else echo '<div class="alert alert-danger">Erreur lors de l\'insertion : '.$conn->error.'</div>';
        $stmt->close();
    }
    echo '<br/><a href="ajout_enseignement.php" class="btn btn-primary">Revenir à la page précédente</a>';

else:
    $data = $conn->query("SELECT DISTINCT promotion FROM classe ORDER BY promotion DESC");
    $donnee = $conn->query("SELECT DISTINCT nom FROM classe");
?>
<div class="login-card" style="max-width:400px;margin:auto;">
<h4>Critères d'ajout</h4>
<form action="ajout_enseignement.php" method="POST">
    <label>Classe :</label>
    <select name="nomcl" class="form-control">
        <?php while ($a = $donnee->fetch_assoc()): ?>
            <option value="<?= htmlspecialchars($a['nom']) ?>"><?= htmlspecialchars($a['nom']) ?></option>
        <?php endwhile; ?>
    </select><br/>

    <label>Promotion :</label>
    <select name="promotion" class="form-control">
        <?php while ($a = $data->fetch_assoc()): ?>
            <option value="<?= htmlspecialchars($a['promotion']) ?>"><?= htmlspecialchars($a['promotion']) ?></option>
        <?php endwhile; ?>
    </select><br/>

    <input type="submit" value="Afficher" class="btn btn-gold-blue">
</form>
</div>
<?php endif; $conn->close(); ?>
</center>
</div>
