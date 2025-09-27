<?php
session_start();
include('cadre.php');

// Connexion à la base de données avec PDO (plus sécurisé que mysql_* obsolète)
try {
    $pdo = new PDO('mysql:host=localhost;dbname=test;charset=utf8', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Afficher Matière</title>
    <link rel="stylesheet" href="votre_style.css"> <!-- Assurez-vous d'avoir le bon fichier CSS -->

<link rel="stylesheet" href="theme.css">
</head>
<body>
<div class="corp">
    <img src="titre_img/affich_matiere.png" class="position_titre">

<?php
// Sélection classe → choisir promotion & semestre
if (isset($_GET['nomcl'])) {
    $_SESSION['nomcl'] = $_GET['nomcl'];
    $nomcl = $_GET['nomcl'];

    $stmt = $pdo->prepare("SELECT promotion FROM classe WHERE nom = :nom ORDER BY promotion DESC");
    $stmt->execute(['nom' => $nomcl]);
    $promotions = $stmt->fetchAll();
?>

<form method="post" action="afficher_matiere.php" class="formulaire">
    <p>Veuillez choisir la promotion et le semestre pour <strong><?php echo htmlspecialchars($nomcl); ?></strong></p>
    <fieldset>
        <legend>Critères d'affichage</legend>
        <label for="promotion">Promotion :</label>
        <select name="promotion" id="promotion">
            <?php foreach ($promotions as $row): ?>
                <option value="<?= htmlspecialchars($row['promotion']) ?>"><?= htmlspecialchars($row['promotion']) ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="radiosem">Semestre :</label>
        <select name="radiosem" id="radiosem">
            <?php for ($i = 1; $i <= 4; $i++): ?>
                <option value="<?= $i ?>">Semestre <?= $i ?></option>
            <?php endfor; ?>
        </select><br><br>

        <input type="submit" value="Afficher les matières">
    </fieldset>
</form>
<br><br>
<a href="index.php">Revenir à la page principale</a>

<?php } ?>

<?php
// Affichage des matières selon critère sélectionné
if (isset($_POST['radiosem']) && isset($_POST['promotion'])) {
    $nomcl = $_SESSION['nomcl'];
    $semestre = $_POST['radiosem'];
    $promotion = $_POST['promotion'];

    $query = "
        SELECT m.codemat, m.nommat, c.nom AS nomclasse, e.numsem, p.nom AS nomprof
        FROM matiere m
        JOIN enseignement e ON m.codemat = e.codemat
        JOIN classe c ON e.codecl = c.codecl
        JOIN prof p ON e.numprof = p.numprof
        WHERE c.nom = :nomcl AND e.numsem = :semestre AND c.promotion = :promotion
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'nomcl' => $nomcl,
        'semestre' => $semestre,
        'promotion' => $promotion
    ]);

    $matieres = $stmt->fetchAll();
?>

    <center>
        <table id="rounded-corner">
            <thead>
                <tr>
                    <?php if (isset($_SESSION['admin'])): ?>
                        <th>Modifier</th>
                        <th>Supprimer</th>
                    <?php endif; ?>
                    <th>Matière</th>
                    <th>Classe</th>
                    <th>Nom Prof</th>
                    <th>Semestre</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($matieres as $row): ?>
                    <tr>
                        <?php if (isset($_SESSION['admin'])): ?>
                            <td><a href="modif_matiere.php?modif_matiere=<?= $row['codemat'] ?>">Modifier</a></td>
                            <td><a href="modif_matiere.php?supp_matiere=<?= $row['codemat'] ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette matière ?');">Supprimer</a></td>
                        <?php endif; ?>
                        <td><?= htmlspecialchars($row['nommat']) ?></td>
                        <td><?= htmlspecialchars($row['nomclasse']) ?></td>
                        <td><?= htmlspecialchars($row['nomprof']) ?></td>
                        <td>S<?= $row['numsem'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </center>
    <br><br>
    <a href="afficher_matiere.php?nomcl=<?= urlencode($nomcl) ?>">Revenir à la sélection</a>

<?php } ?>
</div>
</body>
</html>
