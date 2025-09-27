<?php
session_start();
include('cadre.php');
include('connect.php');

if (!isset($_SESSION['admin']) && !isset($_SESSION['etudiant']) && !isset($_SESSION['prof'])) {
    echo "<p>Accès non autorisé.</p>";
    exit;
}

echo '<div class="container my-5">';

$nomel = $_POST['nomel'] ?? '';
$prenomel = $_POST['prenomel'] ?? '';
$nomcl = $_POST['nomcl'] ?? '';
$promo = $_POST['promotion'] ?? '';

if (!isset($_POST['submit'])) {
    $classes = mysqli_query($conn, "SELECT DISTINCT nom FROM classe");
    $promos = mysqli_query($conn, "SELECT DISTINCT promotion FROM classe ORDER BY promotion DESC");
    ?>

<link rel="stylesheet" href="theme.css">

    <form action="chercher_eleve.php" method="post" class="form-card shadow-lg p-4" style="border-radius:15px; max-width:600px; margin:0 auto; background:white;">
        <h2 class="text-center mb-4" style="color: #1e3c72; text-shadow: 2px 2px 8px rgba(0,0,0,0.2);">🔍 Chercher un élève</h2>
        
        <label>Nom :</label>
        <input type="text" name="nomel" value="<?= htmlspecialchars($nomel) ?>">

        <label>Prénom :</label>
        <input type="text" name="prenomel" value="<?= htmlspecialchars($prenomel) ?>">

        <label>Promotion :</label>
        <select name="promotion">
            <option value="">-- Choisir la promotion --</option>
            <?php while ($p = mysqli_fetch_assoc($promos)): ?>
                <option value="<?= htmlspecialchars($p['promotion']) ?>" <?= ($promo === $p['promotion']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($p['promotion']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Classe :</label>
        <select name="nomcl">
            <option value="">-- Choisir la classe --</option>
            <?php while ($c = mysqli_fetch_assoc($classes)): ?>
                <option value="<?= htmlspecialchars($c['nom']) ?>" <?= ($nomcl === $c['nom']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['nom']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <button type="submit" name="submit" class="btn-grad mt-3 w-100">Rechercher</button>
    </form>

    <a href="index.php" style="display:block; text-align:center; margin-top:20px; color:#1e3c72;">Revenir à la page principale !</a>
    <?php
} else {
    $nomel = trim($nomel);
    $prenomel = trim($prenomel);
    $nomcl = trim($nomcl);
    $promo = trim($promo);

    $query = "
        SELECT eleve.*, classe.nom AS classe_nom, classe.promotion
        FROM eleve 
        INNER JOIN classe ON classe.codecl = eleve.codecl
        WHERE eleve.nomel LIKE ? AND eleve.prenomel LIKE ?
    ";

    $params = ['%' . $nomel . '%', '%' . $prenomel . '%'];
    $types = "ss";

    if ($nomcl !== '') {
        $query .= " AND classe.nom = ?";
        $types .= "s";
        $params[] = $nomcl;
    }

    if ($promo !== '') {
        $query .= " AND classe.promotion = ?";
        $types .= "s";
        $params[] = $promo;
    }

    $stmt = mysqli_prepare($conn, $query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            echo '<div class="table-container mt-4"><table class="styled-table">';
            echo '<thead><tr>
                    <th>Nom</th><th>Prénom</th><th>Adresse</th><th>Date de naissance</th>
                    <th>Téléphone</th><th>Classe</th><th>Promotion</th>
                  </tr></thead><tbody>';
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<tr>
                        <td>' . htmlspecialchars($row['nomel']) . '</td>
                        <td>' . htmlspecialchars($row['prenomel']) . '</td>
                        <td>' . htmlspecialchars($row['adresse']) . '</td>
                        <td>' . htmlspecialchars($row['date_naissance']) . '</td>
                        <td>' . htmlspecialchars($row['telephone']) . '</td>
                        <td>' . htmlspecialchars($row['classe_nom']) . '</td>
                        <td>' . htmlspecialchars($row['promotion']) . '</td>
                      </tr>';
            }
            echo '</tbody></table></div>';
        } else {
            echo "<p class='alert error text-center mt-4'>Aucun élève trouvé avec ces critères.</p>";
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "<p class='alert error text-center mt-4'>Erreur lors de la requête : " . mysqli_error($conn) . "</p>";
    }

    echo '<a href="chercher_eleve.php" class="btn btn-grad mt-3 d-block mx-auto" style="width:200px; text-align:center;">Nouvelle recherche</a>';
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
.form-card input, .form-card select {
    width:100%;
    padding:10px 15px;
    margin-top:5px;
    border-radius:10px;
    border:1px solid #ccc;
    transition: all 0.3s;
}
.form-card input:focus, .form-card select:focus {
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
    min-width: 600px;
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
