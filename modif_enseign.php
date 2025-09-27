<?php
session_start();
include('cadre.php');
echo '<div class="corp">';

$conn = new mysqli("localhost", "root", "", "test");
if ($conn->connect_error) die("Échec de la connexion : " . $conn->connect_error);
$conn->set_charset("utf8");

// Fonction pour sélectionner l'option par défaut
function selectedOption($valeur, $default) {
    return $valeur == $default ? 'selected' : '';
}

// ===================== MODIFICATION =====================
if (isset($_GET['modif_ensein'])) {
    $id = intval($_GET['modif_ensein']);

    // Récupération de l'enseignement
    $stmt = $conn->prepare("
        SELECT e.id, e.numprof, e.codemat, e.numsem,
               p.nom AS nomp, p.prenom,
               c.nom AS nomcl, c.promotion,
               m.nommat
        FROM enseignement e
        JOIN prof p ON p.numprof = e.numprof
        JOIN classe c ON c.codecl = e.codecl
        JOIN matiere m ON m.codemat = e.codemat
        WHERE e.id = ?
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $ligne = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$ligne) {
        echo "<p>Enseignement introuvable.</p>";
        exit;
    }

    // Liste des profs et matières
    $professeurs = $conn->query("SELECT numprof, nom, prenom FROM prof");
    $matieres = $conn->query("SELECT codemat, nommat FROM matiere");
    ?>

<link rel="stylesheet" href="theme.css">

<h2>Modifier un enseignement</h2>
<form action="modif_enseign.php" method="POST" class="formulaire">
    <label>Matière :</label>
    <select name="codemat">
        <?php while($m = $matieres->fetch_assoc()): ?>
            <option value="<?= $m['codemat'] ?>" <?= selectedOption($m['codemat'], $ligne['codemat']) ?>>
                <?= htmlspecialchars($m['nommat']) ?>
            </option>
        <?php endwhile; ?>
    </select><br/><br/>

    <label>Professeur :</label>
    <select name="numprof">
        <?php while($p = $professeurs->fetch_assoc()): ?>
            <option value="<?= $p['numprof'] ?>" <?= selectedOption($p['numprof'], $ligne['numprof']) ?>>
                <?= htmlspecialchars($p['nom'].' '.$p['prenom']) ?>
            </option>
        <?php endwhile; ?>
    </select><br/><br/>

    <p>Classe : <?= htmlspecialchars($ligne['nomcl']) ?></p>
    <p>Promotion : <?= htmlspecialchars($ligne['promotion']) ?></p>
    <p>Semestre : <?= htmlspecialchars($ligne['numsem']) ?></p>

    <input type="hidden" name="id" value="<?= $id ?>">
    <input type="hidden" name="codecl" value="<?= $ligne['codecl'] ?>">
    <input type="hidden" name="numsem" value="<?= $ligne['numsem'] ?>">

    <center><input type="submit" value="Modifier" class="btn btn-primary"></center>
</form>
<br/><br/><a href="afficher_devoir.php" class="btn btn-secondary">Revenir à la page précédente</a>

<?php
// ===================== TRAITEMENT POST =====================
} elseif (isset($_POST['numprof'], $_POST['codemat'], $_POST['id'])) {
    $id = intval($_POST['id']);
    $numprof = intval($_POST['numprof']);
    $codemat = $_POST['codemat'];
    $codecl = $_POST['codecl'];
    $numsem = $_POST['numsem'];

    // Vérifier si l'enseignement existe déjà
    $stmt_check = $conn->prepare("SELECT COUNT(*) AS nb FROM enseignement WHERE numprof=? AND codemat=? AND codecl=? AND id!=?");
    $stmt_check->bind_param("isis", $numprof, $codemat, $codecl, $id);
    $stmt_check->execute();
    $nb = $stmt_check->get_result()->fetch_assoc()['nb'];
    $stmt_check->close();

    if ($nb != 0) {
        echo '<script>alert("Erreur : cet enseignement existe déjà !");</script>';
    } else {
        // Mise à jour de l'enseignement
        $stmt_update = $conn->prepare("UPDATE enseignement SET numprof=?, codemat=? WHERE id=?");
        $stmt_update->bind_param("isi", $numprof, $codemat, $id);
        $stmt_update->execute();
        $stmt_update->close();

        // Supprimer les devoirs et évaluations liés
        $stmt_devoirs = $conn->prepare("SELECT numdev FROM devoir WHERE codemat=? AND codecl=? AND numsem=?");
        $stmt_devoirs->bind_param("ssi", $codemat, $codecl, $numsem);
        $stmt_devoirs->execute();
        $res = $stmt_devoirs->get_result();
        while ($d = $res->fetch_assoc()) {
            $numdev = $d['numdev'];
            $stmt_del_eval = $conn->prepare("DELETE FROM evaluation WHERE numdev=?");
            $stmt_del_eval->bind_param("i", $numdev);
            $stmt_del_eval->execute();
            $stmt_del_eval->close();

            $stmt_del_dev = $conn->prepare("DELETE FROM devoir WHERE numdev=?");
            $stmt_del_dev->bind_param("i", $numdev);
            $stmt_del_dev->execute();
            $stmt_del_dev->close();
        }
        $stmt_devoirs->close();

        echo '<script>alert("Modifié avec succès !\nTous les devoirs et évaluations liés ont été supprimés.");</script>';
    }

    echo '<br/><br/><a href="modif_enseign.php?modif_ensein='.$id.'" class="btn btn-secondary">Revenir à la page précédente</a>';

// ===================== SUPPRESSION =====================
} elseif (isset($_GET['supp_ensein'])) {
    $id = intval($_GET['supp_ensein']);

    // Récupérer les infos pour supprimer les devoirs
    $stmt_info = $conn->prepare("SELECT codemat, codecl, numsem FROM enseignement WHERE id=?");
    $stmt_info->bind_param("i", $id);
    $stmt_info->execute();
    $info = $stmt_info->get_result()->fetch_assoc();
    $stmt_info->close();

    if ($info) {
        $codemat = $info['codemat'];
        $codecl = $info['codecl'];
        $numsem = $info['numsem'];

        // Supprimer devoirs et évaluations
        $stmt_devoirs = $conn->prepare("SELECT numdev FROM devoir WHERE codemat=? AND codecl=? AND numsem=?");
        $stmt_devoirs->bind_param("ssi", $codemat, $codecl, $numsem);
        $stmt_devoirs->execute();
        $res = $stmt_devoirs->get_result();
        while ($d = $res->fetch_assoc()) {
            $numdev = $d['numdev'];
            $stmt_del_eval = $conn->prepare("DELETE FROM evaluation WHERE numdev=?");
            $stmt_del_eval->bind_param("i", $numdev);
            $stmt_del_eval->execute();
            $stmt_del_eval->close();

            $stmt_del_dev = $conn->prepare("DELETE FROM devoir WHERE numdev=?");
            $stmt_del_dev->bind_param("i", $numdev);
            $stmt_del_dev->execute();
            $stmt_del_dev->close();
        }
        $stmt_devoirs->close();
    }

    // Supprimer l'enseignement
    $stmt_del = $conn->prepare("DELETE FROM enseignement WHERE id=?");
    $stmt_del->bind_param("i", $id);
    $stmt_del->execute();
    $stmt_del->close();

    echo '<script>alert("Supprimé avec succès !\nTous les devoirs et évaluations liés ont été supprimés.");</script>';
    echo '<br/><br/><a href="index.php" class="btn btn-secondary">Revenir à la page principale</a>';
}

$conn->close();
echo '</div>';
?>
