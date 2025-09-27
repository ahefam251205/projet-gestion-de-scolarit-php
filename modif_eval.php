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
if (isset($_GET['modif_eval'])) {
    $id = intval($_GET['modif_eval']);

    // Récupération des données de l'évaluation
    $stmt = $conn->prepare("
        SELECT evaluation.numeval, evaluation.numel, evaluation.note,
               eleve.nomel, eleve.prenomel, eleve.codecl,
               classe.nom AS nom_classe, classe.promotion,
               devoir.numdev, devoir.codemat, devoir.date_dev, devoir.coeficient, devoir.numsem, devoir.n_devoir,
               matiere.nommat
        FROM evaluation
        JOIN eleve ON eleve.numel = evaluation.numel
        JOIN classe ON eleve.codecl = classe.codecl
        JOIN devoir ON devoir.numdev = evaluation.numdev
        JOIN matiere ON matiere.codemat = devoir.codemat
        WHERE evaluation.numeval = ?
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $ligne = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$ligne) {
        echo "<p>Évaluation introuvable.</p>";
        exit;
    }

    // Récupération des élèves de la même classe
    $stmt2 = $conn->prepare("SELECT numel, nomel, prenomel FROM eleve WHERE codecl = ?");
    // Vérifier si codecl est int ou string
    $stmt2->bind_param("s", $ligne['codecl']); // 's' si codecl est VARCHAR
    $stmt2->execute();
    $eleves = $stmt2->get_result();
    $stmt2->close();
    ?>

<link rel="stylesheet" href="theme.css">

<h2>Modifier une évaluation</h2>
<form action="modif_eval.php" method="POST" class="formulaire">
    <p><strong>Matière :</strong> <?= htmlspecialchars($ligne['nommat']) ?></p>
    <p><strong>Classe :</strong> <?= htmlspecialchars($ligne['nom_classe']) ?></p>
    <p><strong>Promotion :</strong> <?= htmlspecialchars($ligne['promotion']) ?></p>
    <p><strong>Date du devoir :</strong> <?= htmlspecialchars($ligne['date_dev']) ?></p>
    <p><strong>Coefficient :</strong> <?= htmlspecialchars($ligne['coeficient']) ?></p>
    <p><strong>Semestre :</strong> S<?= htmlspecialchars($ligne['numsem']) ?></p>
    <p><strong>Devoir N° :</strong> <?= htmlspecialchars($ligne['n_devoir']) ?></p>

    <label for="numel">Étudiant :</label>
    <select name="numel" id="numel" class="form-select">
        <?php while ($a = $eleves->fetch_assoc()) : ?>
            <option value="<?= $a['numel'] ?>" <?= selectedOption($a['numel'], $ligne['numel']) ?>>
                <?= htmlspecialchars($a['nomel'].' '.$a['prenomel']) ?>
            </option>
        <?php endwhile; ?>
    </select><br/>

    <label for="note">Note :</label>
    <input type="text" name="note" id="note" class="form-control" value="<?= htmlspecialchars($ligne['note']) ?>"><br/>

    <input type="hidden" name="id" value="<?= $id ?>">
    <center><input type="submit" value="Modifier" class="btn btn-primary"></center>
</form>

<br/><br/><a href="afficher_evaluation.php" class="btn btn-secondary">Revenir à la liste</a>

<?php
// ===================== TRAITEMENT POST =====================
} elseif (isset($_POST['numel'], $_POST['note'], $_POST['id'])) {
    $id = intval($_POST['id']);
    $numel = intval($_POST['numel']);
    $note = str_replace(",", ".", $_POST['note']);

    if (trim($note) !== "") {
        $stmt = $conn->prepare("UPDATE evaluation SET numel=?, note=? WHERE numeval=?");
        $stmt->bind_param("idi", $numel, $note, $id);
        if ($stmt->execute()) {
            echo '<script>alert("Modifié avec succès !");</script>';
        } else {
            echo '<script>alert("Erreur : '.$stmt->error.'");</script>';
        }
        $stmt->close();
    } else {
        echo '<script>alert("Erreur ! Vous devez remplir tous les champs.");</script>';
    }

    echo '<br/><br/><a href="modif_eval.php?modif_eval='.$id.'" class="btn btn-secondary">Revenir à la page précédente</a>';

// ===================== SUPPRESSION =====================
} elseif (isset($_GET['supp_eval'])) {
    $id = intval($_GET['supp_eval']);
    $stmt = $conn->prepare("DELETE FROM evaluation WHERE numeval=?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo '<script>alert("Supprimé avec succès !");</script>';
    } else {
        echo '<script>alert("Erreur : '.$stmt->error.'");</script>';
    }
    $stmt->close();
    echo '<br/><br/><a href="afficher_evaluation.php" class="btn btn-secondary">Revenir à la liste des évaluations</a>';
}

$conn->close();
echo '</div>';
?>
