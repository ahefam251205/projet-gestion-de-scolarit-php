<?php
session_start();
include('cadre.php');

// Connexion MySQLi
$conn = new mysqli("localhost", "root", "", "test");
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}
$conn->set_charset("utf8");
?>

<link rel="stylesheet" href="theme.css">

<div class="container my-5">
    <div class="card p-4 shadow-lg">
        <h1 class="text-center mb-4" style="color: #1e3c72; text-shadow: 2px 2px 8px rgba(0,0,0,0.2);">
            Suppression du stage
        </h1>

        <div class="text-center">
            <?php
            if(isset($_GET['supp_stage'])) {
                $id = (int)$_GET['supp_stage']; // sécurité : cast en entier
                $stmt = $conn->prepare("DELETE FROM stage WHERE numstage = ?");
                $stmt->bind_param("i", $id);
                if($stmt->execute()) {
                    echo '<h3 style="color: green;">Suppression avec succès !</h3>';
                } else {
                    echo '<h3 style="color: red;">Erreur lors de la suppression.</h3>';
                }
                $stmt->close();
                echo '<br/><a href="index.php" class="btn btn-grad mt-3">Revenir à la page d\'accueil</a>';
            } else {
                echo '<p>Aucun stage sélectionné pour la suppression.</p>';
            }
            ?>
        </div>
    </div>
</div>

<style>
.btn-grad {
    background: linear-gradient(45deg, #ffdd57, #1e3c72);
    color: white;
    border: none;
    border-radius: 30px;
    padding: 10px 25px;
    font-weight: bold;
    transition: all 0.3s;
    text-decoration: none;
    display: inline-block;
}
.btn-grad:hover {
    background: linear-gradient(45deg, #1e3c72, #ffdd57);
    transform: scale(1.05);
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    color:white;
}
</style>

<?php
$conn->close();
?>
