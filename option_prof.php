<?php
session_start();
include("header.php");
include("connect.php"); // $conn = new mysqli(...)

if (!isset($_GET['numprof'])) {
    echo '<div class="alert alert-danger text-center">Aucun professeur sélectionné.</div>';
    exit;
}

$numprof = (int)$_GET['numprof'];

// Récupérer les infos du prof
$stmt = $conn->prepare("SELECT nom, prenom FROM prof WHERE numprof=?");
$stmt->bind_param("i", $numprof);
$stmt->execute();
$prof_result = $stmt->get_result();
$prof = $prof_result->fetch_assoc();

if (!$prof) {
    echo '<div class="alert alert-danger text-center">Professeur introuvable.</div>';
    exit;
}
?>

<link rel="stylesheet" href="theme.css">

<div class="container my-5">
    <h1 class="text-center mb-4" style="color:#1e3c72; text-shadow:2px 2px 8px rgba(0,0,0,0.2);">
        Professeur : <?= htmlspecialchars($prof['nom'] . ' ' . $prof['prenom']) ?>
    </h1>

    <!-- Matières enseignées -->
    <h3>Matières enseignées</h3>
    <div class="table-responsive">
        <table class="table table-striped table-hover styled-table">
            <thead style="background: linear-gradient(45deg,#ffdd57,#1e3c72); color:white;">
                <tr>
                    <th>Matière</th>
                    <th>Classe</th>
                    <th>Promotion</th>
                    <th>Semestre</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $conn->prepare("
                    SELECT m.nommat, c.nom AS classe, c.promotion, e.numsem
                    FROM enseignement e
                    JOIN matiere m ON e.codemat = m.codemat
                    JOIN classe c ON e.codecl = c.codecl
                    WHERE e.numprof = ?
                    ORDER BY c.promotion DESC
                ");
                $stmt->bind_param("i", $numprof);
                $stmt->execute();
                $matieres = $stmt->get_result();
                
                if ($matieres->num_rows > 0) {
                    while ($row = $matieres->fetch_assoc()) {
                        echo "<tr>
                                <td>".htmlspecialchars($row['nommat'])."</td>
                                <td>".htmlspecialchars($row['classe'])."</td>
                                <td>".htmlspecialchars($row['promotion'])."</td>
                                <td>".htmlspecialchars($row['numsem'])."</td>
                              </tr>";
                    }
                } else {
                    echo '<tr><td colspan="4" class="text-center">Aucune matière trouvée.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Classes coordonnées -->
    <h3 class="mt-4">Classes coordonnées</h3>
    <div class="table-responsive">
        <table class="table table-striped table-hover styled-table">
            <thead style="background: linear-gradient(45deg,#ffdd57,#1e3c72); color:white;">
                <tr>
                    <th>Classe</th>
                    <th>Promotion</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $conn->prepare("
                    SELECT nom, promotion
                    FROM classe
                    WHERE numprofcoord = ?
                    ORDER BY promotion DESC
                ");
                $stmt->bind_param("i", $numprof);
                $stmt->execute();
                $classes = $stmt->get_result();
                
                if ($classes->num_rows > 0) {
                    while ($row = $classes->fetch_assoc()) {
                        echo "<tr>
                                <td>".htmlspecialchars($row['nom'])."</td>
                                <td>".htmlspecialchars($row['promotion'])."</td>
                              </tr>";
                    }
                } else {
                    echo '<tr><td colspan="2" class="text-center">Aucune classe coordonnée.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="text-center mt-4">
        <a href="listeprof.php" class="btn btn-grad">⬅ Retour à la liste des profs</a>
    </div>
</div>

<style>
.styled-table tbody tr:hover {
    background: rgba(30, 60, 114, 0.2);
    transform: scale(1.01);
}
.styled-table td, .styled-table th { padding: 12px; text-align:center; }
.styled-table th { font-size:1.1rem; }
.btn-grad {
    background: linear-gradient(45deg, #ffdd57, #1e3c72);
    color:white; border:none; border-radius:30px;
    padding:10px 25px; font-weight:bold; text-decoration:none;
    transition:all 0.3s; display:inline-block;
}
.btn-grad:hover {
    background: linear-gradient(45deg, #1e3c72, #ffdd57);
    transform:scale(1.05); box-shadow:0 5px 15px rgba(0,0,0,0.3);
}
</style>

<?php include("footer.php"); ?>
