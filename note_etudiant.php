<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once('connect.php');
include('cadre.php');

if (!isset($_SESSION['etudiant'])) {
    echo "<div class='container mt-5'><div class='alert alert-danger text-center'>Erreur : Aucun étudiant connecté. Veuillez vous connecter.</div></div>";
    exit;
}

$id = intval($_SESSION['etudiant']);

// Récupération des bulletins triés par semestre
$query = "
    SELECT b.numel, e.nomel, e.prenomel, m.nommat, b.numsem, c.promotion, b.notefinal, c.nom
    FROM bulletin b
    JOIN eleve e ON b.numel = e.numel
    JOIN matiere m ON b.codemat = m.codemat
    JOIN classe c ON e.codecl = c.codecl
    WHERE b.numel = ?
    ORDER BY b.numsem, m.nommat
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

// Organiser les bulletins par semestre
$semestres = [];
while ($row = $result->fetch_assoc()) {
    $sem = $row['numsem'];
    $semestres[$sem][] = $row;
}

?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

<div class="container my-5">
    <h2 class="text-center mb-4" style="color:#1e3c72; text-shadow: 1px 1px 5px rgba(0,0,0,0.2);">
        Mes Bulletins
    </h2>

    <?php if (empty($semestres)): ?>
        <div class="alert alert-warning text-center">
            Aucun bulletin trouvé.
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($semestres as $numsem => $notes): ?>
                <div class="col-12 col-md-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-gradient text-white" style="background: linear-gradient(45deg,#ffdd57,#1e3c72);">
                            <h5 class="mb-0">Semestre <?= $numsem ?></h5>
                        </div>
                        <div class="card-body p-3">
                            <p><strong>Nom :</strong> <?= htmlspecialchars($notes[0]['nomel']) ?></p>
                            <p><strong>Prénom :</strong> <?= htmlspecialchars($notes[0]['prenomel']) ?></p>
                            <p><strong>Classe :</strong> <?= htmlspecialchars($notes[0]['nom']) ?></p>
                            <p><strong>Promotion :</strong> <?= htmlspecialchars($notes[0]['promotion']) ?></p>
                            <table class="table table-striped table-hover text-center mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Matière</th>
                                        <th>Note finale</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($notes as $row): 
                                        $note = $row['notefinal'];
                                        if($note >= 15) $badge = 'success';
                                        elseif($note >= 10) $badge = 'warning';
                                        else $badge = 'danger';
                                    ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['nommat']) ?></td>
                                            <td><span class="badge bg-<?= $badge ?>"><?= $note ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="text-center mt-4">
        <a href="index.php" class="btn btn-grad">Revenir à l'accueil</a>
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
}
.btn-grad:hover {
    background: linear-gradient(45deg, #1e3c72, #ffdd57);
    transform: scale(1.05);
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    color:white;
}
.table-hover tbody tr:hover {
    background: rgba(30,60,114,0.1);
}
.card-header {
    font-weight: bold;
    font-size: 1.1rem;
}
.badge {
    font-size: 1rem;
    padding: 0.5em 0.8em;
}
</style>

<?php
$stmt->close();
$conn->close();
?>
