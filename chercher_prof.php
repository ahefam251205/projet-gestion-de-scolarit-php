<?php
session_start();
include('cadre.php');
include('connect.php');

// Vérification accès
if (!isset($_SESSION['admin']) && !isset($_SESSION['etudiant']) && !isset($_SESSION['prof'])) {
    echo "<p>Accès non autorisé.</p>";
    exit;
}

echo '<div class="container my-5">';

$nomprof = $_POST['nomprof'] ?? '';
$prenomprof = $_POST['prenomprof'] ?? '';

if (!isset($_POST['submit'])) {
    // Formulaire
    ?>
    <link rel="stylesheet" href="theme.css">
    <form action="chercher_prof.php" method="post" class="form-card shadow-lg p-4" style="border-radius:15px; max-width:600px; margin:0 auto;">
        <h2 class="text-center mb-4" style="color:#1e3c72; text-shadow:2px 2px 8px rgba(0,0,0,0.2);">Chercher un professeur</h2>
        
        <label>Nom :</label>
        <input type="text" name="nomprof" value="<?= htmlspecialchars($nomprof) ?>">

        <label>Prénom :</label>
        <input type="text" name="prenomprof" value="<?= htmlspecialchars($prenomprof) ?>">

        <button type="submit" name="submit" class="btn-grad mt-3">Rechercher</button>
    </form>
    <a href="index.php" style="display:block; text-align:center; margin-top:20px; color:#1e3c72;">Revenir à la page principale</a>
    <?php
} else {
    $nomprof = trim($nomprof);
    $prenomprof = trim($prenomprof);

    $query = "SELECT * FROM prof WHERE nom LIKE ? AND prenom LIKE ?";
    $stmt = mysqli_prepare($conn, $query);
    if ($stmt) {
        $likeNom = "%$nomprof%";
        $likePrenom = "%$prenomprof%";
        mysqli_stmt_bind_param($stmt, "ss", $likeNom, $likePrenom);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            echo '<div class="table-container"><table class="styled-table">';
            echo '<thead><tr>
                    <th>Nom</th><th>Prénom</th><th>Adresse</th><th>Téléphone</th>
                  </tr></thead><tbody>';
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<tr>
                        <td>' . htmlspecialchars($row['nom']) . '</td>
                        <td>' . htmlspecialchars($row['prenom']) . '</td>
                        <td>' . htmlspecialchars($row['adresse']) . '</td>
                        <td>' . htmlspecialchars($row['telephone']) . '</td>
                      </tr>';
            }
            echo '</tbody></table></div>';
        } else {
            echo "<p class='alert error' style='text-align:center;'>Aucun professeur trouvé.</p>";
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "<p class='alert error' style='text-align:center;'>Erreur lors de la préparation de la requête : " . mysqli_error($conn) . "</p>";
    }

    echo '<br><a href="chercher_prof.php" style="display:block; text-align:center; margin-top:20px; color:#1e3c72;">Revenir à la page de recherche</a>';
}

echo '</div>';
?>

<style>
/* Formulaire */
.form-card label {
    display:block;
    margin-top:15px;
    font-weight:bold;
    color:#1e3c72;
}
.form-card input {
    width:100%;
    padding:10px 15px;
    margin-top:5px;
    border-radius:10px;
    border:1px solid #ccc;
    transition: all 0.3s;
}
.form-card input:focus {
    border-color:#ffdd57;
    box-shadow:0 0 10px rgba(255,221,87,0.5);
    outline:none;
}

/* Bouton */
.btn-grad {
    background: linear-gradient(45deg, #ffdd57, #1e3c72);
    color: white;
    border: none;
    border-radius: 30px;
    padding: 10px 25px;
    font-weight: bold;
    transition: all 0.3s;
    cursor: pointer;
}
.btn-grad:hover {
    background: linear-gradient(45deg, #1e3c72, #ffdd57);
    transform: scale(1.05);
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
}

/* Tableau */
.table-container {
    overflow-x:auto;
    margin-top:20px;
}
.styled-table {
    border-collapse: collapse;
    margin: 0 auto;
    font-size: 16px;
    min-width: 500px;
    border-radius: 10px 10px 0 0;
    overflow: hidden;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
}
.styled-table thead tr {
    background: linear-gradient(45deg, #1e3c72, #ffdd57);
    color: #ffffff;
    text-align: left;
    font-weight: bold;
}
.styled-table th, .styled-table td {
    padding: 12px 15px;
}
.styled-table tbody tr {
    border-bottom: 1px solid #dddddd;
}
.styled-table tbody tr:nth-of-type(even) {
    background-color: #f3f3f3;
}
.styled-table tbody tr:last-of-type {
    border-bottom: 2px solid #1e3c72;
}

/* Alert */
.alert {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 10px;
    font-weight: bold;
    animation: fadeIn 0.5s ease-in-out;
}
.alert.error { background-color:#f8d7da; color:#721c24; }

@keyframes fadeIn {
    from {opacity:0; transform:translateY(-10px);}
    to {opacity:1; transform:translateY(0);}
}
</style>
