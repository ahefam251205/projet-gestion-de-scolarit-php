<?php
session_start();
include('cadre.php');

// Connexion avec mysqli (à adapter selon ta config)
$conn = new mysqli("localhost", "root", "", "test");
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}
?>

<link rel="stylesheet" href="theme.css">

<div class="corp">
<img src="titre_img/affich_note.png" class="position_titre">
<pre>
<?php
if (isset($_GET['nomcl'])) {
    $nomcl = $conn->real_escape_string($_GET['nomcl']);
    $_SESSION['nomcl'] = $nomcl;

    $query = "SELECT DISTINCT promotion FROM classe WHERE nom='$nomcl' ORDER BY promotion DESC";
    $result = $conn->query($query);
    ?>
    <form action="afficher_note.php" method="POST" class="formulaire">
        Veuillez choisir la promotion et le semestre pour <?php echo htmlspecialchars($nomcl); ?> : <br><br>
        <fieldset>
            <legend>Critères d'affichage</legend>
            <pre>
Promotion      : <select name="promotion">
<?php while ($row = $result->fetch_assoc()) {
    echo '<option value="' . htmlspecialchars($row['promotion']) . '">' . htmlspecialchars($row['promotion']) . '</option>';
} ?>
</select><br><br>

Semestre       : <select name="radiosem">
<?php for ($i = 1; $i <= 4; $i++) {
    echo '<option value="' . $i . '">Semestre ' . $i . '</option>';
} ?>
</select><br><br>

<input type="submit" value="Afficher">
            </pre>
        </fieldset>
    </form>
<?php
}

if (isset($_POST['radiosem'])) {
    $nomcl = $_SESSION['nomcl'];
    $_SESSION['semestre'] = $_POST['radiosem'];
    $semestre = intval($_SESSION['semestre']);
    $promo = $conn->real_escape_string($_POST['promotion']);

    $query = "
        SELECT DISTINCT m.nommat 
        FROM matiere m
        JOIN enseignement e ON m.codemat = e.codemat
        JOIN classe c ON e.codecl = c.codecl
        WHERE c.nom = '$nomcl' AND e.numsem = '$semestre' AND c.promotion = '$promo'";
    
    $result = $conn->query($query);
    ?>
    <form method="post" action="afficher_note.php" class="formulaire">
        <fieldset>
            <legend>Matières étudiées</legend>
            <pre>
<?php
    $i = 1;
    while ($row = $result->fetch_assoc()) {
        echo '<input type="radio" name="radio" value="' . htmlspecialchars($row['nommat']) . '" id="choix' . $i . '" /> ';
        echo '<label for="choix' . $i . '">' . htmlspecialchars($row['nommat']) . '</label><br><br>';
        $i++;
    }
?>
            </pre>
        </fieldset>
        <input type="submit" value="Afficher les notes">
    </form>
<?php
} elseif (isset($_POST['radio'])) {
    $nommat = $conn->real_escape_string($_POST['radio']);
    $semestre = intval($_SESSION['semestre']);
    $nomcl = $conn->real_escape_string($_SESSION['nomcl']);

    $query = "
        SELECT e.nomel, e.prenomel, c.nom, m.nommat, d.date_dev, d.coeficient, ev.note 
        FROM eleve e
        JOIN classe c ON e.codecl = c.codecl
        JOIN evaluation ev ON e.numel = ev.numel
        JOIN devoir d ON ev.numdev = d.numdev
        JOIN matiere m ON d.codemat = m.codemat
        WHERE m.nommat = '$nommat' AND c.nom = '$nomcl' AND d.numsem = '$semestre'";

    $result = $conn->query($query);
    ?>
    <center>
        <table id="rounded-corner">
            <thead>
                <tr>
                    <th class="rounded-company">Nom d'élève</th>
                    <th class="rounded-q2">Prénom</th>
                    <th class="rounded-q2">Classe</th>
                    <th class="rounded-q2">Matière</th>
                    <th class="rounded-q2">Date du devoir</th>
                    <th class="rounded-q2">Coefficient</th>
                    <th class="rounded-q4">Note</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <td colspan="6" class="rounded-foot-left"><em>&nbsp;</em></td>
                    <td class="rounded-foot-right">&nbsp;</td>
                </tr>
            </tfoot>
            <tbody>
    <?php
    while ($row = $result->fetch_assoc()) {
        echo '<tr>
            <td>' . htmlspecialchars($row['nomel']) . '</td>
            <td>' . htmlspecialchars($row['prenomel']) . '</td>
            <td>' . htmlspecialchars($row['nom']) . '</td>
            <td>' . htmlspecialchars($row['nommat']) . '</td>
            <td>' . htmlspecialchars($row['date_dev']) . '</td>
            <td>' . htmlspecialchars($row['coeficient']) . '</td>
            <td>' . htmlspecialchars($row['note']) . '</td>
        </tr>';
    }
    ?>
            </tbody>
        </table>
    </center>
<?php
}
?>
</pre>
</div>
</body>
</html>
