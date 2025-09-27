<?php
session_start();
$_SESSION['admin'] = 'admin'; // À supprimer en production
include('cadre.php');
require_once('connect.php'); // Connexion à la base avec mysqli
?>

<link rel="stylesheet" href="theme.css">

<div class="corp">
    <!-- Titre en lettres à la place de l'image -->
    <h1 class="page-title">Types de Diplômes</h1>

    <?php
    // Suppression d'un diplôme si demandé
    if (isset($_GET['supp_type'])) {
        $id = intval($_GET['supp_type']); // Sécurisation
        $stmt = $conn->prepare("DELETE FROM diplome WHERE numdip = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        echo "<div class='alert alert-success'>Le diplôme a bien été supprimé.</div>";
    }

    // Récupération des diplômes
    $result = $conn->query("SELECT * FROM diplome");

    if ($result && $result->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <?php if (isset($_SESSION['admin'])): ?>
                            <th>Supprimer</th>
                        <?php endif; ?>
                        <th>Titre du diplôme</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($a = $result->fetch_assoc()): ?>
                        <tr>
                            <?php if (isset($_SESSION['admin'])): ?>
                                <td>
                                    <a class="btn btn-gold-blue btn-sm" href="type_diplome.php?supp_type=<?= $a['numdip'] ?>"
                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette entrée ?');">
                                        🗑 Supprimer
                                    </a>
                                </td>
                            <?php endif; ?>
                            <td><?= htmlspecialchars($a['titre_dip']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">Aucun diplôme trouvé.</div>
    <?php endif; ?>

    <a class="btn btn-gold-blue mt-3" href="index.php">Revenir à la page principale</a>
</div>
