<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion de Scolarité</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Header stylé "wow" */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f7fc;
            margin: 0;
            padding: 0;
        }

        .navbar-custom {
            background: linear-gradient(90deg, #1e3c72, #2a5298);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }

        .navbar-custom .navbar-brand {
            font-weight: bold;
            color: #fff;
            font-size: 1.6rem;
            text-shadow: 1px 1px 5px rgba(0,0,0,0.3);
            transition: transform 0.3s;
        }
        .navbar-custom .navbar-brand:hover {
            transform: scale(1.1);
        }

        .navbar-custom .nav-link {
            color: #fff;
            transition: all 0.3s;
            position: relative;
        }

        .navbar-custom .nav-link::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -3px;
            height: 3px;
            width: 0;
            background-color: #ffdd57;
            transition: 0.3s;
        }

        .navbar-custom .nav-link:hover::after {
            width: 100%;
        }

        .navbar-custom .nav-link:hover,
        .navbar-custom .nav-item.dropdown:hover > .nav-link {
            color: #ffdd57;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.3);
        }

        .navbar-custom .dropdown-menu {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            transition: all 0.3s;
        }

        .navbar-custom .dropdown-item {
            color: #1e3c72;
            font-weight: 500;
            transition: all 0.3s;
        }

        .navbar-custom .dropdown-item:hover {
            background-color: #2a5298;
            color: #ffdd57;
            transform: translateX(5px);
        }

        .navbar-toggler {
            border-color: #fff;
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml;charset=utf8,%3Csvg viewBox='0 0 30 30' 
            xmlns='http://www.w3.org/2000/svg'%3E%3Cpath stroke='rgba%28255, 221, 87, 1%29' 
            stroke-width='2' stroke-linecap='round' stroke-miterlimit='10' d='M4 7h22M4 15h22M4 23h22'/ %3E%3C/svg%3E");
        }
    </style>

<link rel="stylesheet" href="theme.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-custom mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">BOB SCHOOL</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarNavDropdown"
            aria-controls="navbarNavDropdown" aria-expanded="false"
            aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNavDropdown">
      <ul class="navbar-nav">
        <!-- Étudiants -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button"
             data-bs-toggle="dropdown" aria-expanded="false">
            Étudiants
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="listeEtudiant.php">Liste des étudiants</a></li>
            <li><a class="dropdown-item" href="Ajout_etudiant.php">Ajouter un étudiant</a></li>
            <li><a class="dropdown-item" href="note_etudiant.php">Consulter notes</a></li>
            <li><a class="dropdown-item" href="chercher_eleve.php">Chercher un étudiant</a></li>
          </ul>
        </li>

        <!-- Professeurs -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button"
             data-bs-toggle="dropdown" aria-expanded="false">
            Professeurs
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="listeprof.php">Liste des profs</a></li>
            <li><a class="dropdown-item" href="ajout_prof.php">Ajouter un prof</a></li>
            <li><a class="dropdown-item" href="chercher_prof.php">Chercher un prof</a></li>
          </ul>
        </li>

        <!-- Matières -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button"
             data-bs-toggle="dropdown" aria-expanded="false">
            Matières
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="listematière.php">Liste des matières</a></li>
            <li><a class="dropdown-item" href="ajout_matiere.php">Ajouter une matière</a></li>
          </ul>
        </li>

        <!-- Stage -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button"
             data-bs-toggle="dropdown" aria-expanded="false">
            Stage
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="afficher_stage.php">Liste des stages</a></li>
            <li><a class="dropdown-item" href="ajout_stage.php">Ajouter un stage</a></li>
            <li><a class="dropdown-item" href="chercher_stage.php">Chercher un stage</a></li>
          </ul>
        </li>

        <!-- Classes -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button"
             data-bs-toggle="dropdown" aria-expanded="false">
            Classes
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="affiche_classe.php">Liste des classes</a></li>
            <li><a class="dropdown-item" href="ajout_classe.php">Ajouter une classe</a></li>
          </ul>
        </li>

        <!-- Conseil -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button"
             data-bs-toggle="dropdown" aria-expanded="false">
            Conseil
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="affiche_conseil.php">Voir les conseils</a></li>
            <li><a class="dropdown-item" href="ajout_conseil.php">Ajouter un conseil</a></li>
          </ul>
        </li>

       <!-- Bulletin -->
<li class="nav-item dropdown">
  <a class="nav-link dropdown-toggle" href="#" role="button"
     data-bs-toggle="dropdown" aria-expanded="false">
    Bulletin
  </a>
  <ul class="dropdown-menu">
    <li><a class="dropdown-item" href="afficher_bullettin.php">Notes finales</a></li>
  </ul>
</li>


        <!-- Diplômes -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button"
             data-bs-toggle="dropdown" aria-expanded="false">
            Diplômes
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="type_diplome.php">Type de diplôme</a></li>
            <li><a class="dropdown-item" href="diplome_obt.php">Diplômes obtenus</a></li>
            <li><a class="dropdown-item" href="ajout_diplome.php">Ajouter un diplôme</a></li>
          </ul>
        </li>

        <!-- Evaluations -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button"
             data-bs-toggle="dropdown" aria-expanded="false">
            Evaluations
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="afficher_evaluation.php">Voir les évaluations</a></li>
            <li><a class="dropdown-item" href="ajout_eval.php">Ajouter une évaluation</a></li>
          </ul>
        </li>

        <!-- Devoir -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button"
             data-bs-toggle="dropdown" aria-expanded="false">
            Devoir
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="ajout_devoir.php">Ajouter un devoir</a></li>
            <li><a class="dropdown-item" href="afficher_devoir.php">Voir les devoirs</a></li>
          </ul>
        </li>

        <!-- Enseignement -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button"
             data-bs-toggle="dropdown" aria-expanded="false">
            Enseignement
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="afficher_enseign.php">Liste des enseignements</a></li>
            <li><a class="dropdown-item" href="ajout_enseignement.php">Ajouter un enseignement</a></li>
          </ul>
        </li>

         <!-- Paramètres -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button"
             data-bs-toggle="dropdown" aria-expanded="false">
            Paramètres
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="profil.php">Profil</a></li>
              <li><a class="dropdown-item" href="changermdp.php">Changer mot de passe</a></li>
                <li><a class="dropdown-item" href="inscription.php">Inscription</a></li>
            <li><a class="dropdown-item" href="liste_utilisateur.php">Voir tout les utilisateur</a></li>
            <li><a class="dropdown-item" href="logout.php">Se déconnecter</a></li>
          </ul>
        </li>

      </ul>
    </div>
  </div>
</nav>
