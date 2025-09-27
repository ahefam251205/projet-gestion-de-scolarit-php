<?php
session_start();
include('cadre.php');

$conn = new mysqli("localhost", "root", "", "test");
if ($conn->connect_error) die("Connexion échouée : " . $conn->connect_error);

// Fonctions utilitaires
function Edition() {
    return isset($_SESSION['admin']) ? '<th>Modifier</th><th>Supprimer</th>' : '';
}
function rond() { return 'rounded-q1'; }
function colspan($a, $b) { return isset($_SESSION['admin']) ? $b : $a; }

$data = $conn->query("SELECT DISTINCT promotion FROM classe ORDER BY promotion DESC");
?>

<link rel="stylesheet" href="theme.css">

<div class="corp">
<center>

<?php
if (isset($_POST['nomcl'], $_POST['radiosem'], $_POST['promotion'])):
    $nomcl = $conn->real_escape_string($_POST['nomcl']);
    $semestre = intval($_POST['radiosem']);
    $promo = $conn->real_escape_string($_POST['promotion']);

    $sql = "
        SELECT enseignement.id, classe.nom AS nomcl, nommat, prof.nom AS nomprof, numsem, promotion 
        FROM enseignement
        JOIN classe ON enseignement.codecl = classe.codecl
        JOIN matiere ON matiere.codemat = enseignement.codemat
        JOIN prof ON prof.numprof = enseignement.numprof
        WHERE classe.nom = '$nomcl' AND promotion = '$promo' AND numsem = $semestre
    ";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0):
?>
<table class="table">
    <thead>
        <tr>
            <?= Edition() ?>
            <th class="<?= rond() ?>">Classe</th>
            <th>Promotion</th>
            <th>Matière</th>
            <th>Professeur</th>
            <th>Semestre</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($a = $result->fetch_assoc()): ?>
            <tr>
                <?php if (isset($_SESSION['admin'])): ?>
                    <td><a href="modif_enseign.php?modif_ensein=<?= $a['id'] ?>">Modifier</a></td>
                    <td><a href="modif_enseign.php?supp_ensein=<?= $a['id'] ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette entrée ?\nTous les enregistrements liés seront perdus.')">Supprimer</a></td>
                <?php endif; ?>
                <td><?= htmlspecialchars($a['nomcl']) ?></td>
                <td><?= htmlspecialchars($a['promotion']) ?></td>
                <td><?= htmlspecialchars($a['nommat']) ?></td>
                <td><?= htmlspecialchars($a['nomprof']) ?></td>
                <td>S<?= intval($a['numsem']) ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
<br><a href="afficher_enseign.php" class="btn btn-primary">Revenir à la page précédente</a>
<?php
    else:
        echo "<p>Aucun résultat trouvé pour cette recherche.</p>";
    endif;
else:
    $retour = $conn->query("SELECT DISTINCT nom FROM classe");
?>
<form method="post" action="afficher_enseign.php" class="login-card" style="max-width:500px;margin:auto;">
    <h4>Critères d'affichage</h4>

    <label>Classe :</label>
    <select name="nomcl" class="form-control" required>
        <?php while ($a = $retour->fetch_assoc()): ?>
            <option value="<?= htmlspecialchars($a['nom']) ?>"><?= htmlspecialchars($a['nom']) ?></option>
        <?php endwhile; ?>
    </select><br/>

    <label>Promotion :</label>
    <select name="promotion" class="form-control" required>
        <?php while ($a = $data->fetch_assoc()): ?>
            <option value="<?= htmlspecialchars($a['promotion']) ?>"><?= htmlspecialchars($a['promotion']) ?></option>
        <?php endwhile; ?>
    </select><br/>

    <label>Semestre :</label>
    <select name="radiosem" class="form-control" required>
        <?php for ($i = 1; $i <= 4; $i++): ?>
            <option value="<?= $i ?>">Semestre <?= $i ?></option>
        <?php endfor; ?>
    </select><br/>

    <input type="submit" value="Afficher" class="btn btn-gold-blue">
</form>
<?php endif; ?>

</center>
</div>
