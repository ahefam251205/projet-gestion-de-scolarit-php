<?php
session_start();
include('cadre.php'); // Header
include('connect.php'); // Connexion MySQLi sécurisée

echo '<div class="corp">';

// Message d'alerte
$message = '';

// Récupérer l'élève si demandé
if(isset($_GET['modif_el'])){
    $id = intval($_GET['modif_el']);
    
    // Supprimer l'élève si demandé
    if(isset($_GET['delete']) && $_GET['delete'] == 1){
        $stmt = $conn->prepare("DELETE FROM eleve WHERE numel=?");
        $stmt->bind_param("i", $id);
        if($stmt->execute()){
            echo '<div class="alert-success" style="text-align:center; padding:10px; border-radius:5px;">✅ Élève supprimé avec succès !</div>';
            echo '<br/><a href="listeEtudiant.php" class="btn-grad">Revenir à la liste</a>';
            $stmt->close();
            $conn->close();
            exit;
        } else {
            echo '<div class="alert-danger" style="text-align:center; padding:10px; border-radius:5px;">❌ Erreur lors de la suppression.</div>';
        }
        $stmt->close();
    }

    // Récupérer les infos de l'élève
    $stmt = $conn->prepare("
        SELECT e.*, c.nom AS nom_classe, c.promotion 
        FROM eleve e 
        JOIN classe c ON e.codecl = c.codecl 
        WHERE e.numel = ?
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $eleve = $result->fetch_assoc();
    $stmt->close();

    if(!$eleve){
        echo "<p>Élève introuvable.</p>";
        echo '<br/><a href="listeEtudiant.php" class="btn-grad">Revenir à la liste</a>';
        exit;
    }
}

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $date_naissance = htmlspecialchars($_POST['date_naissance']);
    $adresse = htmlspecialchars($_POST['adresse']);
    $telephone = htmlspecialchars($_POST['telephone']);

    if ($date_naissance && $adresse && $telephone) {
        $stmt = $conn->prepare("UPDATE eleve SET date_naissance=?, adresse=?, telephone=? WHERE numel=?");
        $stmt->bind_param("sssi", $date_naissance, $adresse, $telephone, $id);

        if ($stmt->execute()) {
            $message = '<div class="alert-success">✅ Modifié avec succès !</div>';
        } else {
            $message = '<div class="alert-danger">❌ Erreur lors de la modification.</div>';
        }
        $stmt->close();
    } else {
        $message = '<div class="alert-danger">❌ Tous les champs sont obligatoires.</div>';
    }

    // Recharger les infos après modification
    $stmt = $conn->prepare("
        SELECT e.*, c.nom AS nom_classe, c.promotion 
        FROM eleve e 
        JOIN classe c ON e.codecl = c.codecl 
        WHERE e.numel = ?
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $eleve = $result->fetch_assoc();
    $stmt->close();
}
?>

<link rel="stylesheet" href="theme.css">

<form action="modif_eleve.php?modif_el=<?= $eleve['numel'] ?>" method="POST" class="formulaire" style="max-width:600px; margin:auto;">
    <h2 style="text-align:center; margin-bottom:20px;">Modifier l'élève</h2>
    <?= $message ?>

    <input type="hidden" name="id" value="<?= $eleve['numel'] ?>">

    <label>Nom :</label>
    <input type="text" value="<?= htmlspecialchars($eleve['nomel']) ?>" disabled>

    <label>Prénom :</label>
    <input type="text" value="<?= htmlspecialchars($eleve['prenomel']) ?>" disabled>

    <label>Date de naissance :</label>
    <input type="date" name="date_naissance" value="<?= htmlspecialchars($eleve['date_naissance']) ?>" required>

    <label>Adresse :</label>
    <textarea name="adresse" required><?= htmlspecialchars($eleve['adresse']) ?></textarea>

    <label>Téléphone :</label>
    <input type="text" name="telephone" value="<?= htmlspecialchars($eleve['telephone']) ?>" required>

    <label>Classe :</label>
    <input type="text" value="<?= htmlspecialchars($eleve['nom_classe']) ?>" disabled>

    <label>Promotion :</label>
    <input type="text" value="<?= htmlspecialchars($eleve['promotion']) ?>" disabled>

    <div style="text-align:center; margin-top:20px;">
        <button type="submit" class="btn-grad">Modifier</button>
        <a href="listeEtudiant.php" class="btn-grad">⬅ Retour</a>
        <a href="modif_eleve.php?modif_el=<?= $eleve['numel'] ?>&delete=1" 
           class="btn-supprimer" 
           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet élève ?');">
           Supprimer
        </a>
    </div>
</form>

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
        margin: 5px;
    }
    .btn-grad:hover {
        background: linear-gradient(45deg, #1e3c72, #ffdd57);
        transform: scale(1.05);
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        color: white;
    }
    .btn-supprimer {
        background: linear-gradient(45deg, #ff4b4b, #c70000);
        color: white;
        border: none;
        border-radius: 30px;
        padding: 10px 25px;
        font-weight: bold;
        transition: all 0.3s;
        text-decoration: none;
        display: inline-block;
        margin: 5px;
    }
    .btn-supprimer:hover {
        background: linear-gradient(45deg, #c70000, #ff4b4b);
        transform: scale(1.05);
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        color: white;
    }
</style>

</div>
<?php $conn->close(); ?>
