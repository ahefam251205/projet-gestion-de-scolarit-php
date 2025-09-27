<?php
session_start();
include('cadre.php');

$conn = mysqli_connect("localhost", "root", "", "test");
if (!$conn) die("Erreur de connexion : " . mysqli_connect_error());
?>

<link rel="stylesheet" href="theme.css">

<div class="corp">
<center>

<?php
// Étape 1 : sélection de la matière
if (isset($_POST['nomcl']) && isset($_POST['radiosem'])):
    $_SESSION['semestre'] = $_POST['radiosem'];
    $nomcl = $_POST['nomcl'];
    $semestre = $_SESSION['semestre'];
    $promo = $_POST['promotion'] ?? $_SESSION['promo'];
    $_SESSION['promo'] = $promo;

    $donnee = mysqli_query($conn, "
        SELECT DISTINCT matiere.nommat 
        FROM matiere
        JOIN enseignement ON matiere.codemat = enseignement.codemat
        JOIN classe ON enseignement.codecl = classe.codecl
        WHERE classe.nom='$nomcl' AND promotion='$promo' AND enseignement.numsem='$semestre'
    ");
    $_SESSION['classe'] = $nomcl;
?>

<div class="login-card" style="max-width:400px;margin:auto;">
<h4>Choisissez la matière</h4>
<form method="post" action="ajout_eval.php">
    <?php $i = 6; while ($a = mysqli_fetch_array($donnee)): ?>
        <div class="form-check">
            <input type="radio" name="radio" value="<?= $a['nommat'] ?>" id="choix<?= $i ?>" class="form-check-input" />
            <label for="choix<?= $i ?>" class="form-check-label"><?= $a['nommat'] ?></label>
        </div>
    <?php $i++; endwhile; ?>
    <br/>
    <input type="submit" class="btn btn-grad" value="Afficher les devoirs">
</form>
</div>

<?php
// Étape 2 : affichage des devoirs
elseif (isset($_POST['radio'])):
    $semestre = $_SESSION['semestre'];
    $nommat = $_POST['radio'];
    $_SESSION['radio_matiere'] = $nommat;
    $nomcl = $_SESSION['classe'];
    $promo = $_SESSION['promo'];

    $donnee = mysqli_query($conn, "
        SELECT numdev, date_dev, nommat, nom, coeficient, numsem, n_devoir 
        FROM devoir
        JOIN matiere ON matiere.codemat = devoir.codemat
        JOIN classe ON classe.codecl = devoir.codecl
        WHERE classe.nom='$nomcl' AND devoir.numsem='$semestre' AND matiere.nommat='$nommat' AND promotion='$promo'
    ");
?>

<div class="table-responsive">
<table class="table styled-table">
    <thead>
        <tr>
            <th>Evaluation</th>
            <th>Matière</th>
            <th>Date devoir</th>
            <th>Classe</th>
            <th>Coefficient</th>
            <th>Semestre</th>
            <th>1er/2ème devoir</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($a = mysqli_fetch_array($donnee)): ?>
        <tr>
            <td><a class="btn btn-grad btn-sm" href="ajout_eval.php?ajout_eval=<?= $a['numdev'] ?>">Ajouter évaluation</a></td>
            <td><?= $a['nommat'] ?></td>
            <td><?= $a['date_dev'] ?></td>
            <td><?= $a['nom'] ?></td>
            <td><?= $a['coeficient'] ?></td>
            <td>S<?= $a['numsem'] ?></td>
            <td><?= $a['n_devoir'] ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
<br/>
<a class="btn btn-grad" href="ajout_eval.php">Revenir à la page principale</a>
</div>

<?php
// Étape 3 : ajout note
elseif (isset($_POST['numel'])):
    $numel = $_POST['numel'];
    $numdev = $_POST['numdev'];
    $nomcl = $_SESSION['classe'];
    $promo = $_SESSION['promo'];
    $note = str_replace(",", ".", $_POST['note']);

    $compteRes = mysqli_query($conn, "SELECT COUNT(*) as nb FROM evaluation WHERE numdev='$numdev' AND numel='$numel'");
    $compte = mysqli_fetch_array($compteRes);

    if ($compte['nb'] > 0):
        echo '<div class="alert alert-danger">Erreur : note déjà existante !</div>';
    else:
        mysqli_query($conn, "INSERT INTO evaluation(numdev,numel,note) VALUES('$numdev','$numel','$note')");
        echo '<div class="alert alert-success">Note ajoutée avec succès !</div>';
    endif;
    echo '<br/><a class="btn btn-grad" href="ajout_eval.php">Revenir à la page principale</a>';

// Étape 4 : formulaire ajout note
elseif (isset($_GET['ajout_eval'])):
    $semestre = $_SESSION['semestre'];
    $nommat = $_SESSION['radio_matiere'];
    $nomcl = $_SESSION['classe'];
    $promo = $_SESSION['promo'];
    $numdev = $_GET['ajout_eval'];

    $donnee = mysqli_fetch_array(mysqli_query($conn, "SELECT date_dev, coeficient, n_devoir FROM devoir WHERE numdev='$numdev'"));
    $data = mysqli_query($conn, "SELECT numel, nomel, prenomel 
        FROM eleve 
        WHERE codecl = (SELECT codecl FROM classe WHERE nom='$nomcl' AND promotion='$promo')"
    );
?>
<div class="login-card" style="max-width:500px;margin:auto;">
<form method="POST" action="ajout_eval.php">
    <p>Filière : <?= $nomcl ?> - <?= $promo ?></p>
    <p>Matière : <?= $nommat ?></p>
    <p>Semestre : S<?= $semestre ?></p>
    <p>Date devoir : <?= $donnee['date_dev'] ?></p>
    <p>Coefficient : <?= $donnee['coeficient'] ?></p>
    <p>Devoir N° : <?= $donnee['n_devoir'] ?></p>

    <label>Etudiant :</label>
    <select name="numel" class="form-control">
        <?php while ($a = mysqli_fetch_array($data)): ?>
            <option value="<?= $a['numel'] ?>"><?= $a['nomel'].' '.$a['prenomel'] ?></option>
        <?php endwhile; ?>
    </select><br/>

    <label>Note :</label>
    <input type="text" name="note" class="form-control"><br/>

    <input type="hidden" name="numdev" value="<?= $numdev ?>">
    <input type="submit" class="btn btn-grad" value="Ajouter la note">
</form>
<br/><a class="btn btn-grad" href="ajout_eval.php">Revenir à la page principale</a>
</div>

<?php
// Étape 5 : formulaire principal
else:
    $data = mysqli_query($conn, "SELECT DISTINCT promotion FROM classe ORDER BY promotion DESC");
?>
<div class="login-card" style="max-width:400px;margin:auto;">
<h4>Ajouter une évaluation</h4>
<form method="post" action="ajout_eval.php">
    <label>Promotion :</label>
    <select name="promotion" class="form-control">
        <?php while ($a = mysqli_fetch_array($data)): ?>
            <option value="<?= $a['promotion'] ?>"><?= $a['promotion'] ?></option>
        <?php endwhile; ?>
    </select><br/>

    <label>Classe :</label>
    <select name="nomcl" class="form-control">
        <?php
        $data = mysqli_query($conn, "SELECT DISTINCT nom FROM classe");
        while ($a = mysqli_fetch_array($data)):
        ?>
            <option value="<?= $a['nom'] ?>"><?= $a['nom'] ?></option>
        <?php endwhile; ?>
    </select><br/>

    <label>Semestre :</label>
    <select name="radiosem" class="form-control">
        <?php for ($i=1; $i<=4; $i++): ?>
            <option value="<?= $i ?>">Semestre <?= $i ?></option>
        <?php endfor; ?>
    </select><br/>

    <input type="submit" class="btn btn-grad" value="Afficher les matières">
</form>
</div>
<?php endif; ?>
</center>
</div>
