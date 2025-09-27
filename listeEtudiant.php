<?php
session_start();
include("header.php");
include("connect.php");

// Récupère le nom de la classe si filtré
$classeNom = isset($_GET['classe']) ? $_GET['classe'] : null;

// Préparer la requête
if ($classeNom) {
    $sql = "SELECT e.numel, e.nomel, e.prenomel, e.date_naissance, c.nom AS classe, c.promotion
            FROM eleve e
            JOIN classe c ON e.codecl = c.codecl
            WHERE c.nom = ?
            ORDER BY c.promotion DESC, e.nomel ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $classeNom);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT e.numel, e.nomel, e.prenomel, e.date_naissance, c.nom AS classe, c.promotion
            FROM eleve e
            JOIN classe c ON e.codecl = c.codecl
            ORDER BY c.nom ASC, c.promotion DESC, e.nomel ASC";
    $result = $conn->query($sql);
}
?>

<link rel="stylesheet" href="theme.css">

<div class="container my-5">
    <h1 class="text-center mb-4" style="color: #1e3c72; text-shadow: 2px 2px 8px rgba(0,0,0,0.2);">
        Liste des étudiants<?= $classeNom ? " - $classeNom" : "" ?>
    </h1>

    <div class="text-center mb-4">
        <a href="listeEtudiant.php" class="btn btn-grad me-2 mb-2">Tous</a>
        <a href="listeEtudiant.php?classe=GI" class="btn btn-grad me-2 mb-2">GI</a>
        <a href="listeEtudiant.php?classe=TM" class="btn btn-grad me-2 mb-2">TM</a>
        <a href="listeEtudiant.php?classe=GRH" class="btn btn-grad mb-2">GRH</a>
    </div>

    <div class="table-responsive shadow-lg" style="border-radius:15px; overflow:hidden;">
        <table class="table table-striped table-hover styled-table mb-0">
            <thead style="background: linear-gradient(45deg, #ffdd57, #1e3c72); color:white;">
                <tr>
                    <th>Modifier</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Date de naissance</th>
                    <th>Classe</th>
                    <th>Promotion</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr style="transition: background 0.3s;">
                            <td>
                                <a href="modif_eleve.php?modif_el=<?= $row['numel'] ?>" class="btn btn-sm btn-grad">
                                    Modifier
                                </a>
                            </td>
                            <td><?= htmlspecialchars($row['nomel']) ?></td>
                            <td><?= htmlspecialchars($row['prenomel']) ?></td>
                            <td><?= htmlspecialchars($row['date_naissance']) ?></td>
                            <td><?= htmlspecialchars($row['classe']) ?></td>
                            <td><?= htmlspecialchars($row['promotion']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">Aucun étudiant trouvé.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="text-center mt-4">
        <a href="index.php" class="btn btn-grad">Revenir à la page précédente</a>
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
    .styled-table tbody tr:hover {
        background: rgba(30, 60, 114, 0.2);
        transform: scale(1.01);
    }
    .styled-table td, .styled-table th {
        padding: 15px;
        text-align: center;
    }
    .styled-table th {
        font-size: 1.1rem;
    }
</style>

<?php
$conn->close();
include("footer.php");
?>
