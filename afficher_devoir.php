<?php
session_start();
include('cadre.php');

$connection = mysqli_connect("localhost", "root", "", "test");
if (!$connection) die("Erreur de connexion : " . mysqli_connect_error());

$data = mysqli_query($connection, "SELECT DISTINCT promotion FROM classe ORDER BY promotion DESC");
?>

<link rel="stylesheet" href="theme.css">

<div class="corp">
    <center>
    <?php
    if (isset($_POST['nomcl'], $_POST['radiosem'], $_POST['promotion'])):
        $_SESSION['semestre'] = $_POST['radiosem'];
        $nomcl = mysqli_real_escape_string($connection, $_POST['nomcl']);
        $semestre = $_SESSION['semestre'];
        $promo = mysqli_real_escape_string($connection, $_POST['promotion']);
        $_SESSION['promo'] = $promo;

        $donnee = mysqli_query($connection, "
            SELECT nommat
            FROM matiere
            JOIN enseignement ON matiere.codemat = enseignement.codemat
            JOIN classe ON enseignement.codecl = classe.codecl
            WHERE classe.nom = '$nomcl' 
            AND promotion = '$promo' 
            AND enseignement.numsem = '$semestre'
        ");
        $_SESSION['classe'] = $nomcl;
    ?>
    <form method="post" action="afficher_devoir.php" class="login-card" style="max-width:500px;margin:auto;">
        <h4>Les matières étudiées par la classe choisie</h4>
        <p>Matière : </p>
        <?php
        while ($a = mysqli_fetch_array($donnee)):
            $nommat = htmlspecialchars($a['nommat']);
        ?>
            <div style="margin-bottom:12px;">
                <input type="radio" name="radio" value="<?= $nommat ?>" id="choix_<?= $nommat ?>" class="form-control" />
                <label for="choix_<?= $nommat ?>"><?= $nommat ?></label>
            </div>
        <?php endwhile; ?>
        <input type="submit" value="Afficher les devoirs" class="btn btn-gold-blue">
    </form>

    <?php
    elseif (isset($_POST['radio'])):
        $semestre = $_SESSION['semestre'];
        $nommat = mysqli_real_escape_string($connection, $_POST['radio']);
        $nomcl = mysqli_real_escape_string($connection, $_SESSION['classe']);
        $promo = mysqli_real_escape_string($connection, $_SESSION['promo']);

        $donnee = mysqli_query($connection, "
            SELECT devoir.numdev, devoir.date_dev, matiere.nommat, classe.nom, devoir.coeficient, devoir.numsem, devoir.n_devoir 
            FROM devoir
            JOIN matiere ON matiere.codemat = devoir.codemat
            JOIN classe ON classe.codecl = devoir.codecl
            WHERE classe.nom = '$nomcl'
            AND devoir.numsem = '$semestre'
            AND matiere.nommat = '$nommat'
            AND promotion = '$promo'
        ");
    ?>
    <table class="table" style="max-width:1000px;margin:auto;">
        <thead>
            <tr>
                <?php if (isset($_SESSION['admin'])): ?>
                    <th>Modifier</th>
                    <th>Supprimer</th>
                <?php endif; ?>
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
                <?php if (isset($_SESSION['admin'])): ?>
                    <td><a href="modif_devoir.php?modif_dev=<?= $a['numdev'] ?>" class="btn btn-gold-blue">Modifier</a></td>
                    <td><a href="modif_devoir.php?supp_dev=<?= $a['numdev'] ?>" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette entrée ?')">Supprimer</a></td>
                <?php endif; ?>
                <td><?= htmlspecialchars($a['nommat']) ?></td>
                <td><?= htmlspecialchars($a['date_dev']) ?></td>
                <td><?= htmlspecialchars($a['nom']) ?></td>
                <td><?= htmlspecialchars($a['coeficient']) ?></td>
                <td>S<?= htmlspecialchars($a['numsem']) ?></td>
                <td><?= htmlspecialchars($a['n_devoir']) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <br/><a href="afficher_devoir.php" class="btn btn-primary">Revenir à la page principale !</a>

    <?php
    else:
        $retour = mysqli_query($connection, "SELECT DISTINCT nom FROM classe");
    ?>
    <form method="post" action="afficher_devoir.php" class="login-card" style="max-width:500px;margin:auto;">
        <h4>Veuillez choisir le Semestre, la promotion et la classe :</h4>
        <label>Promotion :</label>
        <select name="promotion" required class="form-control">
            <?php while ($a = mysqli_fetch_array($data)): ?>
                <option value="<?= htmlspecialchars($a['promotion']) ?>"><?= htmlspecialchars($a['promotion']) ?></option>
            <?php endwhile; ?>
        </select>
        <label>Classe :</label>
        <select name="nomcl" required class="form-control">
            <?php while ($a = mysqli_fetch_array($retour)): ?>
                <option value="<?= htmlspecialchars($a['nom']) ?>"><?= htmlspecialchars($a['nom']) ?></option>
            <?php endwhile; ?>
        </select>
        <label>Semestre :</label>
        <select name="radiosem" required class="form-control">
            <?php for ($i = 1; $i <= 4; $i++): ?>
                <option value="<?= $i ?>">Semestre <?= $i ?></option>
            <?php endfor; ?>
        </select>
        <br/>
        <input type="submit" value="Afficher les matières" class="btn btn-gold-blue">
    </form>
    <?php endif; ?>
    </center>
</div>
