<?php
session_start();
include('cadre.php');

// Connexion sécurisée en mysqli
$mysqli = new mysqli("localhost", "root", "", "test");
if ($mysqli->connect_error) {
    die("Erreur de connexion : " . $mysqli->connect_error);
}

// Requête sécurisée
$sql = "SELECT codecl, classe.nom AS nomcl, promotion, prof.nom AS nomprof 
        FROM classe 
        JOIN prof ON classe.numprofcoord = prof.numprof";
$result = $mysqli->query($sql);
?>

<link rel="stylesheet" href="theme.css">

<div class="container my-5">
    <div class="card p-4 shadow-lg">
        <h1 class="text-center mb-4" style="color: #1e3c72; text-shadow: 2px 2px 8px rgba(0,0,0,0.2);">
            Liste des classes
        </h1>

        <div class="table-responsive shadow-lg" style="border-radius:15px; overflow:hidden;">
            <table class="table table-striped table-hover styled-table mb-0">
                <thead style="background: linear-gradient(45deg, #ffdd57, #1e3c72); color:white;">
                    <tr>
                        <?php if (isset($_SESSION['admin'])): ?>
                            <th>Modifier</th>
                            <th>Supprimer</th>
                        <?php endif; ?>
                        <th>Nom de la classe</th>
                        <th>Promotion</th>
                        <th>Prof coordonnateur</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr style="transition: background 0.3s;">
                            <?php if (isset($_SESSION['admin'])): ?>
                                <td>
                                    <a href="modif_classe.php?modif_classe=<?= urlencode($row['codecl']) ?>" class="btn btn-sm btn-grad">Modifier</a>
                                </td>
                                <td>
                                    <a href="modif_classe.php?supp_classe=<?= urlencode($row['codecl']) ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette entrée ? Tous les enregistrements liés seront perdus.')" class="btn btn-sm btn-grad">Supprimer</a>
                                </td>
                            <?php endif; ?>
                            <td><?= htmlspecialchars($row['nomcl']) ?></td>
                            <td><?= htmlspecialchars($row['promotion']) ?></td>
                            <td><?= htmlspecialchars($row['nomprof']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="text-center mt-4">
            <a href="index.php" class="btn btn-grad">Revenir à la page précédente</a>
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
$mysqli->close();
?>
