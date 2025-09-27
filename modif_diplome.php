<?php
session_start();
require_once('connect.php'); // $conn = mysqli_connect(...)
include('cadre.php');

// Vérification du paramètre GET
if (!isset($_GET['modif_dip'])) {
    echo '<div class="alert alert-danger text-center mt-3">Aucun diplôme sélectionné.</div>';
    exit;
}

$id = intval($_GET['modif_dip']);

// Récupération des infos de l'étudiant et du diplôme
$stmt = $conn->prepare("
    SELECT e.numel, e.nomel, e.prenomel, c.nom AS classe, c.promotion,
           d.numdip, d.titre_dip, ed.note, ed.commentaire, ed.etablissement, ed.lieu, ed.annee_obtention
    FROM eleve_diplome ed
    JOIN eleve e ON e.numel = ed.numel
    JOIN classe c ON c.codecl = e.codecl
    JOIN diplome d ON d.numdip = ed.numdip
    WHERE ed.id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$ligne = $result->fetch_assoc();

if (!$ligne) {
    echo '<div class="alert alert-danger text-center mt-3">Diplôme introuvable.</div>';
    exit;
}

// Récupération des titres de diplômes pour le select
$titre = $conn->query("SELECT numdip, titre_dip FROM diplome ORDER BY titre_dip ASC");

?>

<link rel="stylesheet" href="theme.css">
<div class="container my-5">
    <h2 class="text-center mb-4">Modifier le diplôme de <?= htmlspecialchars($ligne['nomel'].' '.$ligne['prenomel']) ?></h2>

    <form action="modif_diplome.php?modif_dip=<?= $id ?>" method="POST" class="needs-validation" novalidate>
        <div class="mb-3">
            <label class="form-label">Nom :</label>
            <input type="text" name="nomel" class="form-control" value="<?= htmlspecialchars($ligne['nomel']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Prénom :</label>
            <input type="text" name="prenomel" class="form-control" value="<?= htmlspecialchars($ligne['prenomel']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Classe :</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($ligne['classe']) ?>" disabled>
        </div>

        <div class="mb-3">
            <label class="form-label">Promotion :</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($ligne['promotion']) ?>" disabled>
        </div>

        <div class="mb-3">
            <label class="form-label">Titre du diplôme :</label>
            <select name="titre" class="form-select" required>
                <?php while ($var = $titre->fetch_assoc()): ?>
                    <option value="<?= $var['numdip'] ?>" <?= ($var['numdip'] == $ligne['numdip']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($var['titre_dip']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Note :</label>
            <input type="text" name="note" class="form-control" value="<?= htmlspecialchars($ligne['note']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Commentaire :</label>
            <input type="text" name="comment" class="form-control" value="<?= htmlspecialchars($ligne['commentaire']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Établissement :</label>
            <input type="text" name="etabli" class="form-control" value="<?= htmlspecialchars($ligne['etablissement']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Lieu :</label>
            <input type="text" name="lieu" class="form-control" value="<?= htmlspecialchars($ligne['lieu']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Année d'obtention :</label>
            <input type="text" name="ann_obt" class="form-control" value="<?= htmlspecialchars($ligne['annee_obtention']) ?>" required>
        </div>

        <input type="hidden" name="id" value="<?= $id ?>">
        <div class="text-center">
            <button type="submit" class="btn btn-grad">Modifier</button>
            <a href="listeEtudiant.php?nomcl=<?= urlencode($ligne['classe']) ?>" class="btn btn-secondary ms-2">⬅ Retour</a>
            <a href="modif_diplome.php?supp_dip=<?= $id ?>" class="btn btn-danger ms-2" onclick="return confirm('Voulez-vous vraiment supprimer ce diplôme ?');">Supprimer</a>
        </div>
    </form>
</div>

<?php
// Traitement POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $nomel = trim($_POST['nomel']);
    $prenomel = trim($_POST['prenomel']);
    $numdip = intval($_POST['titre']);
    $note = trim($_POST['note']);
    $comment = trim($_POST['comment']);
    $etabli = trim($_POST['etabli']);
    $lieu = trim($_POST['lieu']);
    $ann_obt = trim($_POST['ann_obt']);

    if ($nomel && $prenomel && $numdip && $note && $etabli && $lieu && $ann_obt) {
        // Update eleve
        $stmt = $conn->prepare("UPDATE eleve SET nomel=?, prenomel=? WHERE numel=?");
        $stmt->bind_param("ssi", $nomel, $prenomel, $ligne['numel']);
        $stmt->execute();
        $stmt->close();

        // Update eleve_diplome
        $stmt = $conn->prepare("
            UPDATE eleve_diplome
            SET numdip=?, note=?, commentaire=?, etablissement=?, lieu=?, annee_obtention=?
            WHERE id=?
        ");
        $stmt->bind_param("isssssi", $numdip, $note, $comment, $etabli, $lieu, $ann_obt, $id);
        $stmt->execute();
        $stmt->close();

        echo '<div class="alert alert-success text-center mt-3">Modifié avec succès !</div>';
        echo '<a href="modif_diplome.php?modif_dip='.$id.'" class="btn btn-secondary mt-2">Revenir</a>';
    } else {
        echo '<div class="alert alert-danger text-center mt-3">Erreur ! Tous les champs requis doivent être remplis.</div>';
    }
}

// Suppression
if (isset($_GET['supp_dip'])) {
    $id = intval($_GET['supp_dip']);
    $stmt = $conn->prepare("DELETE FROM eleve_diplome WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    echo '<div class="alert alert-success text-center mt-3">Supprimé avec succès !</div>';
    echo '<a href="listeEtudiant.php" class="btn btn-secondary mt-2">Revenir à la liste</a>';
}
?>
