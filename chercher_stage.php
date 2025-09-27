<?php
session_start();
// $_SESSION['admin'] = 'admin'; // à enlever en prod
include('cadre.php');

if (isset($_SESSION['admin']) || isset($_SESSION['etudiant']) || isset($_SESSION['prof'])) {
    // Connexion
    $conn = new mysqli("localhost", "root", "", "test");
    if ($conn->connect_error) {
        die("Connexion échouée : " . $conn->connect_error);
    }
    $conn->set_charset("utf8");
?>

<link rel="stylesheet" href="theme.css">

<div class="container my-5">
    <div class="card p-4 shadow-lg">
        <h1 class="text-center mb-4" style="color: #1e3c72; text-shadow: 2px 2px 8px rgba(0,0,0,0.2);">
            Recherche de stage
        </h1>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'GET' || !isset($_POST['submit'])) {
    // Récupérer promotions et classes pour les selects
    $promosRes = $conn->query("SELECT DISTINCT promotion FROM classe ORDER BY promotion DESC");
    $classesRes = $conn->query("SELECT DISTINCT nom FROM classe");
    $promotions = $promosRes->fetch_all(MYSQLI_ASSOC);
    $classes = $classesRes->fetch_all(MYSQLI_ASSOC);
?>
        <form action="chercher_stage.php" method="post" class="formulaire">
            <div class="mb-3">
                <label>Nom :</label>
                <input type="text" name="nomel" class="form-control" value="">
            </div>
            <div class="mb-3">
                <label>Prénom :</label>
                <input type="text" name="prenomel" class="form-control" value="">
            </div>
            <div class="mb-3">
                <label>Promotion :</label>
                <select name="promotion" class="form-select">
                    <option value="">Choisir la promotion</option>
                    <?php foreach ($promotions as $row) {
                        echo '<option value="' . htmlspecialchars($row['promotion']) . '">' . htmlspecialchars($row['promotion']) . '</option>';
                    } ?>
                </select>
            </div>
            <div class="mb-3">
                <label>Classe :</label>
                <select name="nomcl" class="form-select">
                    <option value="">Choisir la classe</option>
                    <?php foreach ($classes as $row) {
                        echo '<option value="' . htmlspecialchars($row['nom']) . '">' . htmlspecialchars($row['nom']) . '</option>';
                    } ?>
                </select>
            </div>
            <button type="submit" name="submit" class="btn btn-grad">Chercher</button>
        </form>
        <div class="text-center mt-3">
            <a href="index.php" class="btn btn-grad">Revenir à la page principale</a>
        </div>
<?php
} else {
    // Traitement POST
    $nomel = $_POST['nomel'] ?? '';
    $prenomel = $_POST['prenomel'] ?? '';
    $nomcl = $_POST['nomcl'] ?? '';
    $promo = $_POST['promotion'] ?? '';

    $conditions = [];
    $params = [];
    $types = "";

    if ($nomel !== "") {
        $conditions[] = "eleve.nomel LIKE ?";
        $params[] = "%$nomel%";
        $types .= "s";
    }
    if ($prenomel !== "") {
        $conditions[] = "eleve.prenomel LIKE ?";
        $params[] = "%$prenomel%";
        $types .= "s";
    }
    if ($nomcl !== "") {
        $conditions[] = "eleve.codecl IN (SELECT codecl FROM classe WHERE nom = ?)";
        $params[] = $nomcl;
        $types .= "s";
    }
    if ($promo !== "") {
        $conditions[] = "eleve.codecl IN (SELECT codecl FROM classe WHERE promotion = ?)";
        $params[] = $promo;
        $types .= "s";
    }

    $where = count($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

    $sql = "SELECT eleve.nomel, eleve.prenomel, stage.lieu_stage, stage.date_debut, stage.date_fin,
                   classe.nom, classe.promotion
            FROM eleve
            JOIN stage ON stage.numel = eleve.numel
            JOIN classe ON classe.codecl = eleve.codecl
            $where";

    $stmt = $conn->prepare($sql);
    if ($params) $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
?>
        <div class="table-responsive shadow-lg mt-4" style="border-radius:15px; overflow:hidden;">
            <table class="table table-striped table-hover styled-table mb-0">
                <thead style="background: linear-gradient(45deg, #ffdd57, #1e3c72); color:white;">
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Lieu du stage</th>
                        <th>Date de début</th>
                        <th>Date de fin</th>
                        <th>Classe</th>
                        <th>Promotion</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($a = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . htmlspecialchars($a['nomel']) . "</td>
                                <td>" . htmlspecialchars($a['prenomel']) . "</td>
                                <td>" . htmlspecialchars($a['lieu_stage']) . "</td>
                                <td>" . htmlspecialchars($a['date_debut']) . "</td>
                                <td>" . htmlspecialchars($a['date_fin']) . "</td>
                                <td>" . htmlspecialchars($a['nom']) . "</td>
                                <td>" . htmlspecialchars($a['promotion']) . "</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>Aucun résultat trouvé.</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
        <div class="text-center mt-3">
            <a href="chercher_stage.php" class="btn btn-grad">Revenir à la recherche</a>
        </div>
<?php
}
$conn->close();
?>
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
} else {
    echo "<p>Accès non autorisé.</p>";
}
?>
