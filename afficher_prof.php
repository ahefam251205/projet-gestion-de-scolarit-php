<?php
session_start();
include('cadre.php');

// Connexion sécurisée avec MySQLi (MySQL obsolète)
$conn = mysqli_connect("localhost", "root", "", "test");
if (!$conn) {
    die("Connexion échouée : " . mysqli_connect_error());
}

echo '<div class="corp">';

if (isset($_GET['matiere'])) {
    $id = mysqli_real_escape_string($conn, $_GET['matiere']);

    $query = "SELECT prof.nom, prenom, nommat, classe.nom AS nomcl, promotion, numsem 
              FROM prof 
              JOIN enseignement ON enseignement.numprof = prof.numprof 
              JOIN matiere ON matiere.codemat = enseignement.codemat 
              JOIN classe ON classe.codecl = enseignement.codecl 
              WHERE prof.numprof = '$id' 
              ORDER BY promotion DESC";

    $result = mysqli_query($conn, $query);
    ?>

<link rel="stylesheet" href="theme.css">

    <center><h1>Matières enseignées par cet enseignant</h1></center>
    <table id="rounded-corner">
    <thead>
        <tr>
            <th class="rounded-company">Nom</th>
            <th class="rounded-q2">Prénom</th>
            <th class="rounded-q2">Matière</th>
            <th class="rounded-q2">Classe</th>
            <th class="rounded-q2">Promotion</th>
            <th class="rounded-q4">Semestre</th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <td colspan="5" class="rounded-foot-left">&nbsp;</td>
            <td class="rounded-foot-right">&nbsp;</td>
        </tr>
    </tfoot>
    <tbody>
    <?php
    while ($a = mysqli_fetch_assoc($result)) {
        echo '<tr><td>' . htmlspecialchars($a['nom']) . '</td><td>' . htmlspecialchars($a['prenom']) . '</td><td>' . htmlspecialchars($a['nommat']) . '</td><td>' . htmlspecialchars($a['nomcl']) . '</td><td>' . htmlspecialchars($a['promotion']) . '</td><td>' . htmlspecialchars($a['numsem']) . '</td></tr>';
    }
    ?>
    </tbody>
    </table>
    <?php 
}

else if (isset($_GET['classe'])) {
    $id = mysqli_real_escape_string($conn, $_GET['classe']);

    $query = "SELECT prof.nom, prenom, classe.nom AS nomcl, promotion 
              FROM prof 
              JOIN classe ON prof.numprof = classe.numprofcoord 
              WHERE prof.numprof = '$id' 
              ORDER BY promotion DESC";

    $result = mysqli_query($conn, $query);
    ?>
    <center><h1>Classe(s) coordonnée(s) par cet enseignant</h1></center>
    <table id="rounded-corner">
    <thead>
        <tr>
            <th class="rounded-company">Nom</th>
            <th class="rounded-q2">Prénom</th>
            <th class="rounded-q2">Classe coordonnée</th>
            <th class="rounded-q4">Promotion</th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <td colspan="3" class="rounded-foot-left">&nbsp;</td>
            <td class="rounded-foot-right">&nbsp;</td>
        </tr>
    </tfoot>
    <tbody>
    <?php
    while ($a = mysqli_fetch_assoc($result)) {
        echo '<tr><td>' . htmlspecialchars($a['nom']) . '</td><td>' . htmlspecialchars($a['prenom']) . '</td><td>' . htmlspecialchars($a['nomcl']) . '</td><td>' . htmlspecialchars($a['promotion']) . '</td></tr>';
    }
    ?>
    </tbody>
    </table>
    <?php
}
?>
</div>
