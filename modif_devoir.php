<?php
session_start();
include('cadre.php');
include('calendrier.html');

$conn = new mysqli("localhost", "root", "", "test");
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}
$conn->set_charset("utf8");

echo '<div class="corp">';

if (isset($_GET['modif_dev'])) {
    $id = intval($_GET['modif_dev']);

    // Récupération du devoir
    $sql = "SELECT devoir.*, classe.nom AS nom_classe, classe.promotion, matiere.nommat
            FROM devoir
            JOIN classe ON classe.codecl = devoir.codecl
            JOIN matiere ON matiere.codemat = devoir.codemat
            WHERE devoir.numdev = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "<p>Devoir introuvable.</p>";
        exit;
    }
    $ligne = $result->fetch_assoc();
    $stmt->close();
    $date = $ligne['date_dev'];
    ?>

<link rel="stylesheet" href="theme.css">

<h1 class="text-center mb-4">Modifier un devoir</h1>
<form action="modif_devoir.php" method="POST" class="needs-validation" novalidate>
    <div class="mb-3">
        <label>Matière :</label>
        <input type="text" class="form-control" value="<?= htmlspecialchars($ligne['nommat']) ?>" disabled>
    </div>
    <div class="mb-3">
        <label>Classe :</label>
        <input type="text" class="form-control" value="<?= htmlspecialchars($ligne['nom_classe']) ?>" disabled>
    </div>
    <div class="mb-3">
        <label>Promotion :</label>
        <input type="text" class="form-control" value="<?= htmlspecialchars($ligne['promotion']) ?>" disabled>
    </div>
    <div class="mb-3">
        <label>Coefficient :</label>
        <input type="number" name="coeficient" class="form-control" value="<?= htmlspecialchars($ligne['coeficient']) ?>" required>
    </div>
    <div class="mb-3">
        <label>Semestre :</label>
        <input type="text" class="form-control" value="<?= htmlspecialchars($ligne['numsem']) ?>" disabled>
    </div>
    <div class="mb-3">
        <label>Devoir N° :</label>
        <input type="number" name="n_devoir" class="form-control" value="<?= htmlspecialchars($ligne['n_devoir']) ?>" min="1" max="2" required>
    </div>
    <div class="mb-3">
        <label>Date du devoir :</label>
        <input type="text" name="date" class="form-control calendrier" value="<?= htmlspecialchars($date) ?>" required>
    </div>
    <input type="hidden" name="id" value="<?= $id ?>">
    <div class="text-center mt-3">
        <button type="submit" class="btn btn-grad">Modifier</button>
        <a href="afficher_devoir.php" class="btn btn-secondary ms-2">⬅ Retour</a>
        <a href="modif_devoir.php?supp_dev=<?= $id ?>" class="btn btn-danger ms-2" onclick="return confirm('Voulez-vous vraiment supprimer ce devoir ?');">Supprimer</a>
    </div>
</form>

<?php
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $n_devoir = intval($_POST['n_devoir']);
    $date = trim($_POST['date']);
    $coeficient = floatval($_POST['coeficient']);

    if (($n_devoir === 1 || $n_devoir === 2) && !empty($date) && $coeficient > 0) {
        // Vérifier qu'un autre devoir identique n'existe pas
        $sql_check = "SELECT COUNT(*) AS nb FROM devoir WHERE n_devoir = ? AND date_dev = ? AND numdev != ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("isi", $n_devoir, $date, $id);
        $stmt_check->execute();
        $count = $stmt_check->get_result()->fetch_assoc()['nb'];
        $stmt_check->close();

        if ($count != 0) {
            echo '<div class="alert alert-danger text-center mt-3">Erreur : ce devoir existe déjà.</div>';
        } else {
            // Mise à jour
            $sql_update = "UPDATE devoir SET n_devoir=?, coeficient=?, date_dev=? WHERE numdev=?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("idsi", $n_devoir, $coeficient, $date, $id);
            if ($stmt_update->execute()) {
                echo '<div class="alert alert-success text-center mt-3">Modifié avec succès !</div>';
            } else {
                echo '<div class="alert alert-danger text-center mt-3">Erreur lors de la modification : '.$stmt_update->error.'</div>';
            }
            $stmt_update->close();
        }
    } else {
        echo '<div class="alert alert-danger text-center mt-3">Tous les champs doivent être correctement remplis.</div>';
    }
    echo '<div class="text-center mt-2"><a href="modif_devoir.php?modif_dev='.$id.'" class="btn btn-secondary">Revenir</a></div>';

} elseif (isset($_GET['supp_dev'])) {
    $id = intval($_GET['supp_dev']);
    // Supprimer évaluations associées
    $stmt1 = $conn->prepare("DELETE FROM evaluation WHERE numdev = ?");
    $stmt1->bind_param("i", $id);
    $stmt1->execute();
    $stmt1->close();

    // Supprimer le devoir
    $stmt2 = $conn->prepare("DELETE FROM devoir WHERE numdev = ?");
    $stmt2->bind_param("i", $id);
    if ($stmt2->execute()) {
        echo '<div class="alert alert-success text-center mt-3">Supprimé avec succès ! Toutes les évaluations associées ont été supprimées.</div>';
    } else {
        echo '<div class="alert alert-danger text-center mt-3">Erreur lors de la suppression : '.$stmt2->error.'</div>';
    }
    $stmt2->close();
    echo '<div class="text-center mt-2"><a href="afficher_devoir.php" class="btn btn-secondary">Revenir à la liste</a></div>';

} else {
    echo '<p>Aucune action spécifiée.</p>';
    echo '<div class="text-center mt-2"><a href="afficher_devoir.php" class="btn btn-secondary">Revenir</a></div>';
}

echo '</div>';
$conn->close();
?>
