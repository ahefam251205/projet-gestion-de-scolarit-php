<?php
include("header.php");
include("connect.php"); // $conn = new mysqli(...)
$result = $conn->query("SELECT m.codemat, m.nommat, m.codecl, c.nom AS classe, c.promotion 
                        FROM matiere m
                        JOIN classe c ON m.codecl = c.codecl");
?>

<link rel="stylesheet" href="theme.css">

<h2 class="mt-4 mb-3 text-center">Liste des Matières</h2>

<table class="table table-bordered table-striped styled-table">
  <thead class="styled-thead">
    <tr>
      <th>Nom</th>
      <th>Classe</th>
      <th>Promotion</th>
      <th>Modifier</th>
      <th>Supprimer</th>
    </tr>
  </thead>
  <tbody>
    <?php while ($row = $result->fetch_assoc()) : ?>
      <tr>
        <td><?= htmlspecialchars($row['nommat']) ?></td>
        <td><?= htmlspecialchars($row['classe']) ?></td>
        <td><?= htmlspecialchars($row['promotion']) ?></td>
        <td>
          <a href="modif_matiere.php?modif_matiere=<?= $row['codemat'] ?>" class="btn btn-grad">Modifier</a>
        </td>
        <td>
          <a href="modif_matiere.php?supp_matiere=<?= $row['codemat'] ?>" 
             onclick="return confirm('Voulez-vous vraiment supprimer cette matière ?');" 
             class="btn btn-grad">Supprimer</a>
        </td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>

<style>
/* Style du tableau */
.styled-table {
    width: 80%;
    margin: 20px auto;
    border-collapse: collapse;
    font-family: Arial, sans-serif;
    transition: all 0.3s ease;
}
.styled-thead {
    background: linear-gradient(90deg, #ffd700, #1e90ff);
    color: white;
    font-weight: bold;
}
.styled-table tbody tr:hover {
    background-color: rgba(30, 60, 114, 0.2);
    transform: scale(1.01);
    transition: all 0.3s ease;
}
.styled-table td, .styled-table th {
    padding: 12px;
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
    padding: 6px 15px;
    font-weight: bold;
    text-decoration: none;
    transition: all 0.3s;
}
.btn-grad:hover {
    background: linear-gradient(45deg, #1e3c72, #ffdd57);
    transform: scale(1.05);
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    color:white;
}
</style>

<?php include("footer.php"); ?>
