<?php
session_start();
include('cadre.php');
include('connexion.php');

if (isset($_SESSION['admin']) || isset($_SESSION['prof'])) {
    echo '<div class="container">';
    echo '<h3>Ajouter un élève</h3>';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nomel = trim($_POST['nomel']);
        $prenomel = trim($_POST['prenomel']);
        $adresse = trim($_POST['adresse']);
        $date_naissance = trim($_POST['date_naissance']);
        $telephone = trim($_POST['telephone']);
        $codecl = trim($_POST['codecl']);

        if ($nomel && $prenomel && $adresse && $date_naissance && $telephone && $codecl) {
            $stmt = $link->prepare("INSERT INTO eleve (nomel, prenomel, adresse, date_naissance, telephone, codecl) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssi", $nomel, $prenomel, $adresse, $date_naissance, $telephone, $codecl);

            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>Élève ajouté avec succès !</div>";
            } else {
                echo "<div class='alert alert-danger'>Erreur : " . htmlspecialchars($stmt->error) . "</div>";
            }
            $stmt->close();
        } else {
            echo "<div class='alert alert-warning'>Veuillez remplir tous les champs.</div>";
        }
    }

    // Liste des classes pour le formulaire
    $res = $link->query("SELECT codecl, nom, promotion FROM classe");
    ?>

<link rel="stylesheet" href="theme.css">


    <form method="post" class="mt-4">
        <div class="mb-3">
            <label class="form-label">Nom :</label>
            <input type="text" name="nomel" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Prénom :</label>
            <input type="text" name="prenomel" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Adresse :</label>
            <input type="text" name="adresse" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Date de naissance :</label>
            <input type="date" name="date_naissance" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Téléphone :</label>
            <input type="text" name="telephone" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Classe :</label>
            <select name="codecl" class="form-select" required>
                <option value="">Choisir la classe</option>
                <?php while ($row = $res->fetch_assoc()) {
                    echo '<option value="' . $row['codecl'] . '">' . $row['nom'] . ' (' . $row['promotion'] . ')</option>';
                } ?>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Ajouter l'élève</button>
    </form>

    <a href="index.php" class="btn btn-secondary mt-3">Retour à l'accueil</a>
    </div>
<?php
} else {
    echo "<div class='container'><p class='alert alert-danger'>Accès refusé.</p></div>";
}
?>