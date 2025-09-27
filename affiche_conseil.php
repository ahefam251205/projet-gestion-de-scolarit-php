<?php
session_start();
include('cadre.php');

// Connexion
$conn = mysqli_connect("localhost", "root", "", "test");
if (!$conn) {
    die("Erreur de connexion : " . mysqli_connect_error());
}

// Chargement des promotions et classes
$promotions = mysqli_query($conn, "SELECT DISTINCT promotion FROM classe ORDER BY promotion DESC");
$classes = mysqli_query($conn, "SELECT DISTINCT nom FROM classe");

// Suppression d’un conseil
if (isset($_GET['supp_conseil'])) {
    $id = intval($_GET['supp_conseil']);
    mysqli_query($conn, "DELETE FROM conseil WHERE id = $id");
    echo '<script>alert("Supprimé avec succès !"); window.location.href="affiche_conseil.php";</script>';
    exit();
}
?>

<link rel="stylesheet" href="theme.css">

<div class="container my-5">
    <div class="card p-4 shadow-lg">
        <h1 class="text-center mb-4" style="color: #1e3c72; text-shadow: 2px 2px 8px rgba(0,0,0,0.2);">
            Affichage des Conseils
        </h1>

<?php
// Affichage des conseils selon critères
if (isset($_POST['nomcl'], $_POST['numsem'], $_POST['promotion'])) {
    $nomcl = mysqli_real_escape_string($conn, $_POST['nomcl']);
    $promo = intval($_POST['promotion']);
    $numsem = intval($_POST['numsem']);

    $sql = "
        SELECT c.id, c.numsem, cl.nom 
        FROM conseil c
        INNER JOIN classe cl ON cl.codecl = c.codecl
        WHERE cl.nom = '$nomcl'
          AND cl.promotion = $promo
          AND c.numsem = $numsem
    ";
    $donnee = mysqli_query($conn, $sql);

    if (mysqli_num_rows($donnee) === 0) {
        echo '<div class="alert alert-warning text-center">Aucun conseil trouvé pour cette classe et ce semestre.</div>';
    } else {
        echo '<div class="table-responsive">
              <table class="table table-striped styled-table mb-0">
              <thead style="background: linear-gradient(45deg, #ffdd57, #1e3c72); color:white;">
                <tr>';
        if (isset($_SESSION['admin'])) echo '<th>Supprimer</th>';
        echo '<th>Semestre</th><th>Classe</th>
              </tr></thead><tbody>';

        while ($a = mysqli_fetch_assoc($donnee)) {
            echo '<tr>';
            if (isset($_SESSION['admin'])) {
                echo '<td><a href="affiche_conseil.php?supp_conseil=' . $a['id'] . '" class="btn btn-grad btn-sm" onclick="return confirm(\'Êtes-vous sûr de vouloir supprimer cette entrée ?\')">Supprimer</a></td>';
            }
            echo '<td>S' . htmlspecialchars($a['numsem']) . '</td>
                  <td>' . htmlspecialchars($a['nom']) . '</td>
                  </tr>';
        }

        echo '</tbody></table></div>';
    }

    echo '<div class="text-center mt-3"><a href="affiche_conseil.php" class="btn btn-grad">⬅️ Revenir à la recherche</a></div>';

} else {
    // Formulaire par défaut
    ?>
    <form method="post" action="affiche_conseil.php" class="formulaire p-3">
        <h4 class="mb-3 text-center">Rechercher un Conseil</h4>

        <div class="mb-3">
            <label>Classe :</label>
            <select name="nomcl" class="form-select" required>
                <?php 
                mysqli_data_seek($classes, 0); // remettre le curseur au début
                while ($a = mysqli_fetch_assoc($classes)) {
                    echo '<option value="' . htmlspecialchars($a['nom']) . '">' . htmlspecialchars($a['nom']) . '</option>';
                } 
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Promotion :</label>
            <select name="promotion" class="form-select" required>
                <?php 
                mysqli_data_seek($promotions, 0); // remettre le curseur au début
                while ($a = mysqli_fetch_assoc($promotions)) {
                    echo '<option value="' . intval($a['promotion']) . '">' . intval($a['promotion']) . '</option>';
                } 
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Semestre :</label>
            <select name="numsem" class="form-select" required>
                <?php for ($i = 1; $i <= 4; $i++) {
                    echo '<option value="' . $i . '">Semestre ' . $i . '</option>';
                } ?>
            </select>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-grad w-100">Afficher les conseils</button>
        </div>
    </form>
<?php } ?>
</div>
