<?php
include("header.php");
include("connect.php");

// Récupérer toutes les classes pour le select
$classes = [];
$res = $conn->query("SELECT codecl, nom, promotion FROM classe ORDER BY promotion DESC, nom ASC");
if($res){
    while($row = $res->fetch_assoc()){
        $classes[] = $row;
    }
}

// Gestion du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nomel = trim($_POST['nomel'] ?? '');
    $prenomel = trim($_POST['prenomel'] ?? '');
    $date_naissance = trim($_POST['date_naissance'] ?? '');
    $codecl = intval($_POST['codecl'] ?? 0);

    if (!empty($nomel) && !empty($prenomel) && !empty($date_naissance) && $codecl > 0) {
        $stmt = $conn->prepare("INSERT INTO eleve (nomel, prenomel, date_naissance, codecl) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $nomel, $prenomel, $date_naissance, $codecl);

        if ($stmt->execute()) {
            echo '<div class="alert success">✅ Étudiant ajouté avec succès !</div>';
        } else {
            echo '<div class="alert error">❌ Erreur lors de l\'ajout : ' . htmlspecialchars($stmt->error) . '</div>';
        }

        $stmt->close();
    } else {
        echo '<div class="alert error">❌ Tous les champs sont obligatoires.</div>';
    }
}
?>

<link rel="stylesheet" href="theme.css">

<div class="container my-5">
    <h2 class="text-center mb-4 fancy-title">Ajouter un étudiant</h2>

    <form method="POST" action="" class="form-card shadow-lg p-4">
        <div class="input-group">
            <span class="input-icon">👤</span>
            <input type="text" name="nomel" placeholder="Nom de l'étudiant" required>
        </div>

        <div class="input-group">
            <span class="input-icon">📝</span>
            <input type="text" name="prenomel" placeholder="Prénom de l'étudiant" required>
        </div>

        <div class="input-group">
            <span class="input-icon">🎂</span>
            <input type="date" name="date_naissance" required>
        </div>

        <div class="input-group">
            <span class="input-icon">🏫</span>
            <select name="codecl" required>
                <option value="">-- Choisir une classe --</option>
                <?php foreach($classes as $c): ?>
                    <option value="<?= $c['codecl'] ?>">
                        <?= htmlspecialchars($c['nom']) ?> (<?= $c['promotion'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn-grad mt-3">Ajouter</button>
    </form>
</div>

<style>
/* Container responsive */
.container { max-width: 500px; margin: auto; }
/* Fancy title */
.fancy-title { color: #1e3c72; text-shadow: 2px 2px 10px rgba(0,0,0,0.2); font-family: 'Segoe UI', sans-serif; }
/* Form card */
.form-card { border-radius: 20px; background: #fff; transition: all 0.3s ease; }
.form-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.2); }
/* Input groups */
.input-group { position: relative; margin-bottom: 20px; }
.input-icon { position: absolute; top: 50%; left: 15px; transform: translateY(-50%); font-size: 18px; }
.input-group input, .input-group select { width: 100%; padding: 12px 15px 12px 45px; border-radius: 12px; border: 1px solid #ccc; transition: all 0.3s; }
.input-group input:focus, .input-group select:focus { border-color: #ffdd57; box-shadow: 0 0 12px rgba(255,221,87,0.5); outline: none; }
/* Button gradient */
.btn-grad { background: linear-gradient(45deg, #ffdd57, #1e3c72); color: white; border: none; border-radius: 30px; padding: 12px 30px; font-weight: bold; cursor: pointer; transition: all 0.3s; }
.btn-grad:hover { background: linear-gradient(45deg, #1e3c72, #ffdd57); transform: scale(1.05); box-shadow: 0 8px 20px rgba(0,0,0,0.3); }
/* Alerts */
.alert { padding: 15px; margin-bottom: 20px; border-radius: 10px; text-align: center; font-weight: bold; animation: fadeIn 0.5s ease-in-out; }
.alert.success { background-color: #d4edda; color: #155724; }
.alert.error { background-color: #f8d7da; color: #721c24; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
/* Responsive */
@media (max-width: 576px) { .input-group input, .input-group select { padding-left: 40px; } }
</style>

<?php include("footer.php"); ?>
