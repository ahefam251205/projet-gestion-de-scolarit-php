<?php
/**
 * make_zip.php
 * Applique le style moderne sur toutes les pages de ton projet.
 * Crée / met à jour theme.css et l'injecte automatiquement dans chaque page.
 * Améliore login.php si présent.
 */

ini_set('display_errors',1);
error_reporting(E_ALL);
set_time_limit(0);

$projectRoot = __DIR__;
$copyThemeToEachDir = true;   // true = dépose aussi theme.css dans chaque dossier contenant des .php/.html
$touchLoginLayout = true;      // true = améliore login.php si présent

// ---------- 1) Contenu du thème complet ----------
$themeCss = <<<CSS
/* === Unified Modern Theme (global) === */
:root{
  --bg:#f5f7ff; --surface:#ffffff; --text:#0f1e34; --sub:#667085;
  --primary:#5b72ff; --primary2:#7d8aff; --success:#42c28a; --danger:#ff5a7f;
  --shadow:0 12px 28px rgba(15,25,45,.10);
}
*{box-sizing:border-box}
html,body{height:100%}
body{
  margin:0;
  background:linear-gradient(180deg,#f7f9ff 0%,#eef3ff 100%);
  color:var(--text);
  font-family:"Segoe UI", Roboto, system-ui, -apple-system, "Helvetica Neue", Arial;
  -webkit-font-smoothing:antialiased;
  -moz-osx-font-smoothing:grayscale;
  animation:fade .5s both;
}
@keyframes fade{from{opacity:0}to{opacity:1}}

/* Navbar */
.navbar{
  background:linear-gradient(90deg,#0b1220,#142042)!important;
  box-shadow:0 10px 26px rgba(10,16,32,.28);
}
.navbar .navbar-brand{color:#fff!important;font-weight:800;letter-spacing:.5px;text-transform:uppercase}
.navbar .nav-link{color:rgba(255,255,255,.92)!important;transition:transform .16s,color .16s}
.navbar .nav-link:hover{color:var(--primary2)!important;transform:translateY(-2px)}

/* Containers & cards */
.container{max-width:1150px}
.card,.card-custom,.bg-white{
  background:linear-gradient(180deg,rgba(255,255,255,.96),rgba(250,252,255,.98));
  border:0!important;border-radius:14px!important;box-shadow:var(--shadow)
}
.shadow,.shadow-sm,.shadow-lg{box-shadow:var(--shadow)!important}

/* Buttons */
.btn{border-radius:10px;transition:transform .14s,box-shadow .14s}
.btn-primary{background:linear-gradient(90deg,var(--primary),var(--primary2));border:0;
  box-shadow:0 10px 24px rgba(91,114,255,.24)}
.btn-primary:hover{transform:translateY(-3px);box-shadow:0 16px 32px rgba(91,114,255,.28)}
.btn-outline-primary{border-color:var(--primary);color:var(--primary)}
.btn-outline-primary:hover{background:var(--primary);color:#fff}
.btn-success{background:linear-gradient(90deg,var(--success),#7fe0bb);border:0}
.btn-danger{background:linear-gradient(90deg,var(--danger),#ff7b97);border:0}

/* Forms */
.form-control{
  border-radius:10px;border:1px solid rgba(14,27,43,.10);
  background:linear-gradient(180deg,#fff,#fbfdff);
  transition:box-shadow .12s,transform .12s,border-color .12s
}
.form-control:focus{border-color:rgba(91,114,255,.6);box-shadow:0 10px 22px rgba(91,114,255,.12);transform:translateY(-1px);outline:none}
.form-label{font-weight:600;color:#0f1e34}

/* Tables */
.table{border-collapse:separate;border-spacing:0 6px}
.table thead th{
  background:linear-gradient(90deg,rgba(91,114,255,.08),rgba(108,227,180,.04));
  border:0!important;color:#0f1e34;font-weight:700
}
.table tbody tr{background:#fff;box-shadow:0 4px 12px rgba(15,25,45,.06)}
.table tbody tr:hover{background:rgba(91,114,255,.03);transform:translateY(-1px)}
.table td,.table th{border:0!important}

/* Alerts */
.alert{border-radius:12px;box-shadow:0 8px 20px rgba(15,25,45,.06)}

/* Login */
.login-wrapper{min-height:calc(100vh - 80px);display:flex;align-items:center;justify-content:center;padding:40px 16px;
  background:radial-gradient(1200px 600px at 10% -10%,rgba(91,114,255,.12),transparent 60%),
             radial-gradient(1000px 600px at 110% 10%,rgba(108,227,180,.12),transparent 60%)}
.login-card{width:430px;border-radius:16px;padding:28px;
  background:linear-gradient(180deg,rgba(255,255,255,.78),rgba(255,255,255,.92));
  box-shadow:0 24px 60px rgba(12,18,33,.16);backdrop-filter:blur(8px);position:relative;overflow:hidden}
.login-card::before{content:"";position:absolute;width:220px;height:220px;right:-60px;top:-60px;
  background:radial-gradient(circle at 30% 30%,rgba(91,114,255,.16),rgba(108,227,180,.08));transform:rotate(25deg)}
.login-card h4{margin-bottom:8px;font-weight:800;color:#0f1e34}
.login-sub{color:var(--sub);margin-bottom:16px}

/* Helpers */
.badge-soft{display:inline-block;padding:.35rem .6rem;border-radius:999px;background:rgba(91,114,255,.10);color:var(--primary);font-weight:700}
.fadeUp{animation:fadeUp .6s both}@keyframes fadeUp{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:none}}
CSS;

// ---------- 2) Fonctions utilitaires ----------
function listAllFiles($dir){
    $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir,FilesystemIterator::SKIP_DOTS));
    $files=[];
    foreach($rii as $file){ if($file->isFile()) $files[]=$file->getPathname(); }
    return $files;
}

function writeFileIfChanged($path,$content){
    if(!file_exists($path) || md5_file($path)!==md5($content)){
        file_put_contents($path,$content);
        return true;
    }
    return false;
}

function injectThemeLink($filepath){
    $content=file_get_contents($filepath);
    if($content===false) return false;
    if(preg_match('/href=[\'"][^\'"]*theme\.css[\'"]/i',$content)) return false;
    $link="\n<link rel=\"stylesheet\" href=\"theme.css\">\n";
    if(preg_match('/<\/head\s*>/i',$content)){
        $content=preg_replace('/<\/head\s*>/i',$link.'
<link rel="stylesheet" href="theme.css">
</head>',$content,1);
    }else{
        $content=$link.$content;
    }
    file_put_contents($filepath,$content);
    return true;
}

function enhanceLoginIfAny($root){
    $login=$root.DIRECTORY_SEPARATOR."login.php";
    if(!file_exists($login)) return false;
    $html=file_get_contents($login);
    if(strpos($html,'login-wrapper')===false){
        $html=preg_replace('/<body([^>]*)>/','<body$1><div class="login-wrapper">',$html,1);
        $html=preg_replace('/<\/body>/','</div></body>',$html,1);
    }
    file_put_contents($login,$html);
    return true;
}

// ---------- 3) Écrire theme.css ----------
$rootThemePath=$projectRoot.DIRECTORY_SEPARATOR."theme.css";
writeFileIfChanged($rootThemePath,$themeCss);

// ---------- 4) Injection dans toutes les pages ----------
$all=listAllFiles($projectRoot);
$modifiedPages=0;
$dirsWithPages=[];

foreach($all as $path){
    $ext=strtolower(pathinfo($path,PATHINFO_EXTENSION));
    if(in_array($ext,['php','html'])){
        if(injectThemeLink($path)) $modifiedPages++;
        $dirsWithPages[dirname($path)]=true;
    }
}

// ---------- 5) Copier theme.css dans chaque dossier ----------
if($copyThemeToEachDir){
    foreach(array_keys($dirsWithPages) as $dir){
        writeFileIfChanged($dir.DIRECTORY_SEPARATOR."theme.css",$themeCss);
    }
}

// ---------- 6) Améliorer login.php ----------
$loginTweaked=$touchLoginLayout ? enhanceLoginIfAny($projectRoot) : false;

// ---------- 7) Résultat ----------
echo "<h2>✅ Style appliqué à toutes les pages</h2>";
echo "<p>Pages modifiées: $modifiedPages</p>";
echo "<p>Login amélioré: ".($loginTweaked?'oui':'non / inchangé')."</p>";
echo "<p>theme.css est présent dans chaque dossier de pages.</p>";
?>
