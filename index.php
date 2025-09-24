<?php include 'header.php'; ?>

<link rel="stylesheet" href="theme.css">


<div class="container my-5">
    <div class="text-center mb-5">
        <h1 style="color: #1e3c72; text-shadow: 2px 2px 8px rgba(0,0,0,0.2);">Bienvenue sur BOB SCHOOL</h1>
        <p style="color: #2a5298; font-size: 1.2rem;">Gérez vos étudiants, professeurs, matières et bien plus avec style et fluidité.</p>
    </div>

    <div class="row text-center">
        <div class="col-md-4 mb-4">
            <div class="card shadow-lg hover-card">
                <div class="card-body">
                    <h5 class="card-title text-primary">Étudiants</h5>
                    <p class="card-text">Ajoutez, consultez ou recherchez vos étudiants facilement.</p>
                    <a href="listeEtudiant.php" class="btn btn-grad">Voir les étudiants</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card shadow-lg hover-card">
                <div class="card-body">
                    <h5 class="card-title text-success">Professeurs</h5>
                    <p class="card-text">Gérez vos professeurs et suivez leurs matières.</p>
                    <a href="listeprof.php" class="btn btn-grad">Voir les professeurs</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card shadow-lg hover-card">
                <div class="card-body">
                    <h5 class="card-title text-warning">Matières</h5>
                    <p class="card-text">Créez et consultez les matières enseignées.</p>
                    <a href="listematière.php" class="btn btn-grad">Voir les matières</a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-card {
        border-radius: 15px;
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .hover-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 25px rgba(0,0,0,0.3);
    }

    .btn-grad {
        background: linear-gradient(45deg, #ffdd57, #1e3c72);
        color: white;
        border: none;
        border-radius: 30px;
        padding: 10px 25px;
        font-weight: bold;
        transition: all 0.3s;
    }

    .btn-grad:hover {
        background: linear-gradient(45deg, #1e3c72, #ffdd57);
        transform: scale(1.05);
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    }
</style>

<?php include 'footer.php'; ?>
