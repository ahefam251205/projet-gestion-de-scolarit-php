<?php
session_start();
include('cadre.php');

// Connexion
$conn = new mysqli("localhost","root","","test");
if ($conn->connect_error) die("Échec : " . $conn->connect_error);
$conn->set_charset("utf8");

// Récupération des promotions et classes pour le formulaire
$promoResult = $conn->query("SELECT DISTINCT promotion FROM classe ORDER BY promotion DESC");
$classResult = $conn->query("SELECT DISTINCT nom FROM classe");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Afficher Stage</title>
<link rel="stylesheet" href="theme.css">
</head>
<body>

<div class="container mt-5">
  <div class="card p-4">
    
    <h2 class="mb-4 text-primary">Affichage des stages</h2>

    <?php 
    // Vérifie si le formulaire a été soumis
    if (isset($_POST['nomcl']) && isset($_POST['promotion'])): 

        // Récupération sécurisée des valeurs
        $nomcl = $conn->real_escape_string($_POST['nomcl']);
        $promo = (int)$_POST['promotion']; // promotion = int

        // Requête corrigée avec préfixe table pour promotion
        $sql = "SELECT stage.numstage, eleve.nomel, eleve.prenomel, classe.nom AS classe_nom, 
                       classe.promotion, stage.date_debut, stage.date_fin, stage.lieu_stage
                FROM eleve 
                JOIN stage ON eleve.numel = stage.numel 
                JOIN classe ON classe.codecl = eleve.codecl 
                WHERE classe.nom='$nomcl' AND classe.promotion=$promo";

        $donnee = $conn->query($sql);
    ?>
      <?php if ($donnee->num_rows > 0): ?>
        <div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead>
              <tr>
                <?php if(isset($_SESSION['admin'])): ?>
                  <th>Modifier</th><th>Supprimer</th>
                <?php endif; ?>
                <th>Nom</th><th>Prénom</th><th>Classe</th><th>Promotion</th>
                <th>Date début</th><th>Date fin</th><th>Lieu</th>
              </tr>
            </thead>
            <tbody>
              <?php while($a = $donnee->fetch_assoc()): ?>
                <tr>
                  <?php if(isset($_SESSION['admin'])): ?>
                    <td><a href="ajout_stage.php?modif_stage=<?= $a['numstage'] ?>">Modifier</a></td>
                    <td><a href="supp_stage.php?supp_stage=<?= $a['numstage'] ?>" onclick="return confirm('Voulez-vous supprimer ?');">Supprimer</a></td>
                  <?php endif; ?>
                  <td><?= htmlspecialchars($a['nomel']) ?></td>
                  <td><?= htmlspecialchars($a['prenomel']) ?></td>
                  <td><?= htmlspecialchars($a['classe_nom']) ?></td>
                  <td><?= htmlspecialchars($a['promotion']) ?></td>
                  <td><?= htmlspecialchars($a['date_debut']) ?></td>
                  <td><?= htmlspecialchars($a['date_fin']) ?></td>
                  <td><?= htmlspecialchars($a['lieu_stage']) ?></td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <p class="text-center text-danger">Aucun stage trouvé pour cette classe et cette promotion.</p>
      <?php endif; ?>
    <?php else: ?>
      <!-- Formulaire de recherche -->
      <form method="post" action="afficher_stage.php" class="mb-3">
        <div class="mb-3">
          <label>Promotion :</label>
          <select name="promotion" class="form-control">
            <?php while($p = $promoResult->fetch_assoc()): ?>
              <option value="<?= htmlspecialchars($p['promotion']) ?>"><?= htmlspecialchars($p['promotion']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="mb-3">
          <label>Classe :</label>
          <select name="nomcl" class="form-control">
            <?php while($c = $classResult->fetch_assoc()): ?>
              <option value="<?= htmlspecialchars($c['nom']) ?>"><?= htmlspecialchars($c['nom']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>
        <button type="submit" class="btn btn-primary">Afficher les stages</button>
      </form>
    <?php endif; ?>

    <a href="afficher_stage.php" class="btn btn-outline-primary mt-3">Revenir à la page précédente</a>
  </div>
</div>

</body>
</html>

<?php $conn->close(); ?>
