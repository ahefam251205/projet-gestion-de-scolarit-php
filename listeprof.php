<?php
include("header.php");
include("connect.php");
?>

<link rel="stylesheet" href="theme.css">

<div class="container my-5">
    <h1 class="text-center mb-4" style="color: #1e3c72; text-shadow: 2px 2px 8px rgba(0,0,0,0.2);">
        Liste des enseignants
    </h1>

    <div class="table-responsive shadow-lg" style="border-radius:15px; overflow:hidden;">
        <table class="table table-striped table-hover styled-table mb-0">
            <thead style="background: linear-gradient(45deg, #ffdd57, #1e3c72); color:white;">
                <tr>
                    <th>Modifier</th>
                    <th>Supprimer</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Adresse</th>
                    <th>Téléphone</th>
                    <th>Matières</th>
                    <th>Classes coordonnées</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("SELECT * FROM prof ORDER BY nom ASC");
                while ($row = $result->fetch_assoc()) : ?>
                    <tr style="transition: background 0.3s;">
                        <td>
                            <a href="modif_prof.php?modif_prof=<?= $row['numprof'] ?>" class="btn btn-sm btn-grad">Modifier</a>
                        </td>
                        <td>
                            <a href="modif_prof.php?supp_prof=<?= $row['numprof'] ?>" onclick="return confirm('Voulez-vous vraiment supprimer ce professeur ?');" class="btn btn-sm btn-grad">Supprimer</a>
                        </td>
                        <td><?= htmlspecialchars($row['nom']) ?></td>
                        <td><?= htmlspecialchars($row['prenom']) ?></td>
                        <td><?= htmlspecialchars($row['adresse']) ?></td>
                        <td><?= htmlspecialchars($row['telephone']) ?></td>
                        <td>
                            <a href="option_prof.php?numprof=<?= $row['numprof'] ?>&view=matiere" class="btn btn-sm btn-grad">Voir matières</a>
                        </td>
                        <td>
                            <a href="option_prof.php?numprof=<?= $row['numprof'] ?>&view=classe" class="btn btn-sm btn-grad">Voir classes</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
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

.btn-grad {
    background: linear-gradient(45deg, #ffdd57, #1e3c72);
    color: white;
    border: none;
    border-radius: 30px;
    padding: 5px 15px;
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

<?php include("footer.php"); ?>
