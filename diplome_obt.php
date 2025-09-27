<?php
session_start();
$_SESSION['admin'] = 'admin'; // Pour forcer l'admin connecté
include('cadre.php');
require_once('connect.php'); // Connexion à la base

// Fonctions utilitaires
function Edition() {
    return '<th>Modifier</th><th>Supprimer</th>';
}
function rond() { return 'rounded-q1'; }
function colspan($start, $end) { return $end - $start; }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Diplômes obtenus</title>
    <link rel="stylesheet" href="theme.css">
</head>
<body>
<div class="corp">
    <!-- Titre texte au lieu de l'image -->
    <h1 class="page-title">Diplômes Obtenus</h1>

<?php
if (isset($_POST['nomcl']) && isset($_POST['promotion'])):
    $nomcl = mysqli_real_escape_string($conn, $_POST['nomcl']);
    $promo = mysqli_real_escape_string($conn, $_POST['promotion']);

    $query = "
    SELECT 
        eleve_diplome.id,
        diplome.titre_dip,
        eleve.nomel,
        eleve.prenomel,
        classe.nom,
        classe.promotion,
        eleve_diplome.note,
        eleve_diplome.commentaire,
        eleve_diplome.etablissement,
        eleve_diplome.lieu,
        eleve_diplome.annee_obtention
    FROM eleve
    INNER JOIN classe ON eleve.codecl = classe.codecl
    INNER JOIN eleve_diplome ON eleve.numel = eleve_diplome.numel
    INNER JOIN diplome ON diplome.numdip = eleve_diplome.numdip
    WHERE classe.nom='$nomcl' AND classe.promotion='$promo'";

    $donnee = mysqli_query($conn, $query);
    if (!$donnee) die("Erreur dans la requête : " . mysqli_error($conn));
?>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <?= Edition() ?>
                    <th class="<?= rond() ?>">Nom</th>
                    <th>Prénom</th>
                    <th>Classe</th>
                    <th>Promo</th>
                    <th>Titre du diplôme</th>
                    <th>Note</th>
                    <th>Commentaire</th>
                    <th>Établissement</th>
                    <th>Lieu</th>
                    <th>Année d'obtention</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($a = mysqli_fetch_assoc($donnee)): ?>
                    <tr>
                        <?php if(isset($_SESSION['admin'])): ?>
                            <td>
                                <a class="btn btn-gold-blue btn-sm" href="modif_diplome.php?modif_dip=<?= $a['id'] ?>">Modifier</a>
                            </td>
                            <td>
                                <a class="btn btn-gold-blue btn-sm" href="modif_diplome.php?supp_dip=<?= $a['id'] ?>"
                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette entrée ?');">Supprimer</a>
                            </td>
                        <?php endif; ?>
                        <td><?= htmlspecialchars($a['nomel']) ?></td>
                        <td><?= htmlspecialchars($a['prenomel']) ?></td>
                        <td><?= htmlspecialchars($a['nom']) ?></td>
                        <td><?= htmlspecialchars($a['promotion']) ?></td>
                        <td><?= htmlspecialchars($a['titre_dip']) ?></td>
                        <td><?= htmlspecialchars($a['note']) ?></td>
                        <td><?= htmlspecialchars($a['commentaire']) ?></td>
                        <td><?= htmlspecialchars($a['etablissement']) ?></td>
                        <td><?= htmlspecialchars($a['lieu']) ?></td>
                        <td><?= htmlspecialchars($a['annee_obtention']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <br>
    <a class="btn btn-gold-blue mt-3" href="diplome_obt.php">Revenir à la page précédente</a>

<?php else: ?>
    <form method="post" action="diplome_obt.php" class="formulaire">
        <p>Veuillez choisir la classe et la promotion :</p>
        <label>Promotion :</label>
        <select name="promotion">
            <?php
            $data = mysqli_query($conn, "SELECT DISTINCT promotion FROM classe ORDER BY promotion DESC");
            while($a = mysqli_fetch_assoc($data)) {
                echo '<option value="'.htmlspecialchars($a['promotion']).'">'.htmlspecialchars($a['promotion']).'</option>';
            }
            ?>
        </select>
        <br><br>
        <label>Classe :</label>
        <select name="nomcl">
            <?php
            $retour = mysqli_query($conn, "SELECT DISTINCT nom FROM classe");
            while($a = mysqli_fetch_assoc($retour)) {
                echo '<option value="'.htmlspecialchars($a['nom']).'">'.htmlspecialchars($a['nom']).'</option>';
            }
            ?>
        </select>
        <br><br>
        <input class="btn btn-gold-blue" type="submit" value="Afficher les diplômes obtenus">
    </form>
    <br>
    <a class="btn btn-gold-blue mt-3" href="index.php">Revenir à la page principale</a>
<?php endif; ?>

</div>
<?php mysqli_close($conn); ?>
</body>
</html>
