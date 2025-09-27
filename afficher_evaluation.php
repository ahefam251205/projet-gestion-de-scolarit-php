<?php
session_start();
include('cadre.php');

$conn = new mysqli("localhost", "root", "", "test");
if ($conn->connect_error) die("Erreur de connexion : " . $conn->connect_error);

// Récupération des promotions
$data = $conn->query("SELECT DISTINCT promotion FROM classe ORDER BY promotion DESC");
?>

<link rel="stylesheet" href="theme.css">

<div class="corp">
<h1 class="page-title">Afficher les évaluations</h1>

<?php
if (isset($_POST['nomcl']) && isset($_POST['radiosem']) && isset($_POST['promotion'])) {
    // Stockage des critères
    $_SESSION['semestre'] = $_POST['radiosem'];
    $_SESSION['promo'] = $_POST['promotion'];
    $_SESSION['classe'] = $_POST['nomcl'];

    $nomcl = $_POST['nomcl'];
    $semestre = $_SESSION['semestre'];
    $promo = $_SESSION['promo'];

    // Récupération des matières correspondant à la classe, promo et semestre
    $donnee = $conn->query("
        SELECT matiere.nommat 
        FROM matiere
        JOIN enseignement ON matiere.codemat = enseignement.codemat
        JOIN classe ON enseignement.codecl = classe.codecl
        WHERE classe.nom = '$nomcl' 
          AND classe.promotion = '$promo'
          AND enseignement.numsem = '$semestre'
    ");
?>

<form method="post" action="afficher_evaluation.php" class="formulaire">
    <fieldset>
        <legend>Matières correspondantes</legend>
        <?php 
        $i = 6; 
        while ($a = $donnee->fetch_assoc()) {
            echo '<input type="radio" name="radio" value="' . htmlspecialchars($a['nommat']) . '" id="choix' . $i . '" />';
            echo '<label for="choix' . $i . '">' . htmlspecialchars($a['nommat']) . '</label><br/><br/>';
            $i++;
        }
        ?>
        <input class="btn btn-gold-blue" type="submit" value="Afficher les devoirs">
    </fieldset>
</form>

<?php
} elseif (isset($_POST['radio'])) {
    $semestre = $_SESSION['semestre'];
    $nommat = $_POST['radio'];
    $_SESSION['radio_matiere'] = $nommat;
    $nomcl = $_SESSION['classe'];
    $promo = $_SESSION['promo'];

    // Récupération des devoirs correspondant à la matière, semestre, classe et promo
    $donnee = $conn->query("
        SELECT devoir.numdev, devoir.date_dev, matiere.nommat, classe.nom, devoir.coeficient, devoir.numsem, devoir.n_devoir
        FROM devoir
        JOIN matiere ON matiere.codemat = devoir.codemat
        JOIN classe ON classe.codecl = devoir.codecl
        WHERE classe.nom = '$nomcl'
          AND devoir.numsem = '$semestre'
          AND matiere.nommat = '$nommat'
          AND classe.promotion = '$promo'
    ");
?>

<h2>Choisissez le devoir pour lequel vous voulez voir l'évaluation</h2>
<div class="table-responsive">
<table class="table">
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
        <?php while ($a = $donnee->fetch_assoc()) {
            echo '<tr>
                <td><a class="btn btn-gold-blue btn-sm" href="afficher_evaluation.php?affich_eval=' . $a['numdev'] . '">Voir l\'évaluation</a></td>
                <td>' . htmlspecialchars($a['nommat']) . '</td>
                <td>' . htmlspecialchars($a['date_dev']) . '</td>
                <td>' . htmlspecialchars($a['nom']) . '</td>
                <td>' . htmlspecialchars($a['coeficient']) . '</td>
                <td>S' . htmlspecialchars($a['numsem']) . '</td>
                <td>' . htmlspecialchars($a['n_devoir']) . '</td>
            </tr>';
        } ?>
    </tbody>
</table>
<br/><a class="btn btn-gold-blue mt-2" href="afficher_evaluation.php">Revenir à la page principale</a>
</div>

<?php
} elseif (isset($_GET['affich_eval'])) {
    $numdev = $_GET['affich_eval'];

    // Récupération des notes pour le devoir sélectionné
    $donnee = $conn->query("
        SELECT evaluation.numeval, devoir.date_dev, matiere.nommat, classe.nom, eleve.nomel, eleve.prenomel, evaluation.note,
               devoir.coeficient, devoir.numsem, classe.promotion, devoir.n_devoir
        FROM evaluation
        JOIN devoir ON evaluation.numdev = devoir.numdev
        JOIN eleve ON eleve.numel = evaluation.numel
        JOIN matiere ON matiere.codemat = devoir.codemat
        JOIN classe ON classe.codecl = devoir.codecl
        WHERE devoir.numdev = '$numdev'
    ");
?>

<div class="table-responsive">
<table class="table">
    <thead>
        <tr>
            <th>Nom</th><th>Prénom</th><th>Classe</th><th>Promotion</th>
            <th>Matière</th><th>Date devoir</th><th>Coefficient</th><th>Semestre</th>
            <th>N° de devoir</th><th>Note</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($a = $donnee->fetch_assoc()) {
            echo '<tr>
                <td>' . htmlspecialchars($a['nomel']) . '</td>
                <td>' . htmlspecialchars($a['prenomel']) . '</td>
                <td>' . htmlspecialchars($a['nom']) . '</td>
                <td>' . htmlspecialchars($a['promotion']) . '</td>
                <td>' . htmlspecialchars($a['nommat']) . '</td>
                <td>' . htmlspecialchars($a['date_dev']) . '</td>
                <td>' . htmlspecialchars($a['coeficient']) . '</td>
                <td>S' . htmlspecialchars($a['numsem']) . '</td>
                <td>' . htmlspecialchars($a['n_devoir']) . '</td>
                <td>' . htmlspecialchars($a['note']) . '</td>
            </tr>';
        } ?>
    </tbody>
</table>
<br/><a class="btn btn-gold-blue mt-2" href="afficher_evaluation.php">Revenir à la page principale</a>
</div>

<?php
} else {
    // Formulaire principal
    $retour = $conn->query("SELECT DISTINCT nom FROM classe");
?>
<form method="post" action="afficher_evaluation.php" class="formulaire">
    <fieldset>
        <legend>Critères d'affichage</legend>
        Promotion :
        <select name="promotion">
            <?php while ($a = $data->fetch_assoc()) {
                echo '<option value="' . htmlspecialchars($a['promotion']) . '">' . htmlspecialchars($a['promotion']) . '</option>';
            } ?>
        </select><br/><br/>
        Classe :
        <select name="nomcl">
            <?php while ($a = $retour->fetch_assoc()) {
                echo '<option value="' . htmlspecialchars($a['nom']) . '">' . htmlspecialchars($a['nom']) . '</option>';
            } ?>
        </select><br/><br/>
        Semestre :
        <select name="radiosem">
            <?php for ($i=1; $i<=4; $i++) {
                echo '<option value="' . $i . '">Semestre ' . $i . '</option>';
            } ?>
        </select><br/><br/>
        <input class="btn btn-gold-blue" type="submit" value="Afficher les matières">
    </fieldset>
</form>
<?php
} // fin else principal
?>
</div>
