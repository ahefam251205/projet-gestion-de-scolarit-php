<?php
session_start();
include('cadre.php');
include('calendrier.html');

// Connexion à MySQL
$conn = mysqli_connect("localhost", "root", "", "test");
if (!$conn) { die("Erreur de connexion : " . mysqli_connect_error()); }
?>

<link rel="stylesheet" href="theme.css">

<div class="container my-5">
    <h2 class="text-center mb-4 fancy-title">Ajouter / Modifier un stage</h2>
    <div class="form-card shadow-lg p-4">

<?php
// MODIFICATION
if (isset($_SESSION['modif_stage']) && isset($_POST['lieu'])) {
    if (!empty($_POST['lieu']) && !empty($_POST['date_debut']) && !empty($_POST['date_fin'])) {
        $id = $_SESSION['modif_stage'];
        $date_debut = $_POST['date_debut'];
        $date_fin   = $_POST['date_fin'];
        $lieu       = $_POST['lieu'];

        mysqli_query($conn, "UPDATE stage SET lieu_stage='$lieu', date_debut='$date_debut', date_fin='$date_fin' WHERE numstage='$id'");
        echo '<div class="alert alert-success">✅ Modification avec succès !</div>';
        unset($_SESSION['modif_stage']);
        echo '<a href="index.php" class="btn btn-grad mt-3 w-100">Revenir à l\'accueil</a>';
    } else {
        echo '<div class="alert alert-warning">⚠️ Veuillez remplir tous les champs !</div>';
    }
}
// AJOUT
else if (isset($_POST['lieu'])) {
    if (!empty($_POST['lieu']) && !empty($_POST['date_debut']) && !empty($_POST['date_fin'])) {
        $numel       = $_POST['numel'];
        $date_debut  = htmlspecialchars($_POST['date_debut']);
        $date_fin    = htmlspecialchars($_POST['date_fin']);
        $lieu        = htmlspecialchars($_POST['lieu']);

        $res_compte = mysqli_query($conn, "SELECT COUNT(*) AS nb FROM stage WHERE lieu_stage='$lieu' AND numel='$numel' AND date_debut='$date_debut' AND date_fin='$date_fin'");
        $compte = mysqli_fetch_assoc($res_compte);

        if ($compte['nb'] > 0) {
            echo '<div class="alert alert-danger">❌ Ce stage existe déjà !</div>';
        } else {
            mysqli_query($conn, "INSERT INTO stage(lieu_stage, date_debut, date_fin, numel) VALUES ('$lieu', '$date_debut', '$date_fin', '$numel')");
            echo '<div class="alert alert-success">✅ Stage ajouté avec succès !</div>';
        }
        echo '<a href="index.php" class="btn btn-grad mt-3 w-100">Revenir à l\'accueil</a>';
    } else {
        echo '<div class="alert alert-warning">⚠️ Tous les champs sont obligatoires !</div>';
        echo '<a href="index.php" class="btn btn-grad mt-3 w-100">Revenir à l\'accueil</a>';
    }
}
// CHOIX classe/promotion
else if (!isset($_POST['nomcl']) && !isset($_GET['modif_stage'])) {
    $data   = mysqli_query($conn, "SELECT DISTINCT promotion FROM classe ORDER BY promotion DESC");
    $retour = mysqli_query($conn, "SELECT DISTINCT nom FROM classe");
?>
    <form action="ajout_stage.php" method="POST" class="form-card">
        <label>Promotion :</label>
        <select name="promotion" class="form-control">
            <?php while($a = mysqli_fetch_assoc($data)) {
                echo '<option value="'.$a['promotion'].'">'.$a['promotion'].'</option>';
            } ?>
        </select>

        <label>Classe :</label>
        <select name="nomcl" class="form-control">
            <?php while($a = mysqli_fetch_assoc($retour)) {
                echo '<option value="'.$a['nom'].'">'.$a['nom'].'</option>';
            } ?>
        </select>

        <button type="submit" class="btn-grad w-100 mt-3">Suivant</button>
    </form>
<?php
}
// FORMULAIRE ajout/modif
if ((isset($_POST['nomcl']) && isset($_POST['promotion'])) || isset($_GET['modif_stage'])) {
    $id = $lieu = $date_debut = $date_fin = "";

    if (isset($_GET['modif_stage'])) { // modif
        $id = $_GET['modif_stage'];
        $_SESSION['modif_stage'] = $id;
        $donnee = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM stage WHERE numstage='$id'"));
        $lieu = $donnee['lieu_stage'];
        $date_debut = $donnee['date_debut'];
        $date_fin = $donnee['date_fin'];
        $data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT numel, nomel, prenomel FROM eleve WHERE numel=(SELECT numel FROM stage WHERE numstage='$id')"));
    } else { // ajout
        $_SESSION['promo'] = $_POST['promotion'];
        $promo = $_POST['promotion'];
        $nomcl = $_POST['nomcl'];
        $data = mysqli_query($conn, "SELECT numel, nomel, prenomel FROM eleve, classe WHERE classe.codecl = eleve.codecl AND nom='$nomcl' AND promotion='$promo'");
    }
?>
    <form action="ajout_stage.php" method="POST" class="form-card">
        <label>Élève :</label>
        <?php if (isset($_GET['modif_stage'])) {
            echo $data['nomel'].' '.$data['prenomel'];
        } else { ?>
            <select name="numel" class="form-control">
                <?php while($a = mysqli_fetch_assoc($data)) {
                    echo '<option value="'.$a['numel'].'">'.$a['nomel'].' '.$a['prenomel'].'</option>';
                } ?>
            </select>
        <?php } ?>

        <label>Lieu de stage :</label>
        <input type="text" name="lieu" class="form-control" value="<?php echo $lieu; ?>">

        <label>Date de début :</label>
        <input type="text" name="date_debut" class="form-control calendrier" value="<?php echo $date_debut; ?>">

        <label>Date de fin :</label>
        <input type="text" name="date_fin" class="form-control calendrier" value="<?php echo $date_fin; ?>">

        <button type="submit" class="btn-grad w-100 mt-3">Valider</button>
    </form>
<?php } ?>
</div>

<style>
.container { max-width: 600px; margin:auto; }
.fancy-title { color:#1e3c72; text-shadow:2px 2px 8px rgba(0,0,0,0.2); }
.form-card { background:#fff; border-radius:15px; padding:25px; box-shadow:0 5px 20px rgba(0,0,0,0.1); margin-bottom:30px; transition:0.3s; }
.form-card:hover { transform:translateY(-5px); box-shadow:0 10px 25px rgba(0,0,0,0.2); }
.form-card label { font-weight:bold; margin-top:15px; display:block; color:#1e3c72; }
.form-control { width:100%; padding:10px 15px; border-radius:10px; border:1px solid #ccc; margin-top:5px; transition:all 0.3s; }
.form-control:focus { border-color:#ffdd57; box-shadow:0 0 10px rgba(255,221,87,0.5); outline:none; }
.btn-grad { background:linear-gradient(45deg,#ffdd57,#1e3c72); color:white; border:none; border-radius:30px; padding:12px 25px; font-weight:bold; cursor:pointer; transition:0.3s; }
.btn-grad:hover { background:linear-gradient(45deg,#1e3c72,#ffdd57); transform:scale(1.05); box-shadow:0 8px 20px rgba(0,0,0,0.3); }
.alert { padding:15px; margin-bottom:20px; border-radius:10px; text-align:center; font-weight:bold; animation:fadeIn 0.5s; }
.alert-success { background:#d4edda; color:#155724; }
.alert-warning { background:#fff3cd; color:#856404; }
.alert-danger { background:#f8d7da; color:#721c24; }
@keyframes fadeIn { from { opacity:0; transform:translateY(-10px);} to {opacity:1; transform:translateY(0);} }
</style>
