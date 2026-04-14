<?php
// ================================================================
//  config.php  —  Connexion BDD + helpers
//  Modifier DB_USER / DB_PASS selon votre environnement WAMP
// ================================================================
define('DB_HOST','localhost');
define('DB_NAME','techcare');
define('DB_USER','root');   // <-- modifiez si besoin
define('DB_PASS','');       // <-- modifiez si besoin

if(session_status()===PHP_SESSION_NONE) session_start();

try {
  $pdo = new PDO(
    "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4",
    DB_USER, DB_PASS,
    [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
     PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
     PDO::ATTR_EMULATE_PREPARES=>false]
  );
} catch(PDOException $e){
  die("<div style='font-family:sans-serif;padding:40px;color:red'><h2>Erreur base de données</h2><p>".$e->getMessage()."</p><p>Vérifiez DB_USER et DB_PASS dans config.php</p></div>");
}

function isLoggedIn():bool { return isset($_SESSION['user_id']); }
function isAdmin():bool    { return isset($_SESSION['admin_id']); }
function isSuperAdmin():bool { return isset($_SESSION['admin_role']) && $_SESSION['admin_role']==='Super Admin'; }
function redirect(string $u):void { header("Location: $u"); exit; }
function clean(string $v):string  { return htmlspecialchars(strip_tags(trim($v)),ENT_QUOTES,'UTF-8'); }
function flash(string $k):string  { $m=$_SESSION[$k]??''; unset($_SESSION[$k]); return (string)$m; }

// Helpers badges (réutilisés dans index, dashboard, admin)
function badgeStatut(string $s):string {
  return match($s){
    'ouvert'   => '<span class="badge b-cyan"><span class="dot"></span>Ouvert</span>',
    'en_cours' => '<span class="badge b-blue"><span class="dot"></span>En cours</span>',
    'resolu'   => '<span class="badge b-green"><span class="dot"></span>Résolu</span>',
    default    => '<span class="badge b-gray">'.htmlspecialchars($s).'</span>'
  };
}
function badgePriorite(string $p):string {
  return match($p){
    'critique' => '<span class="badge b-red">Critique</span>',
    'haute'    => '<span class="badge b-orange">Haute</span>',
    'normale'  => '<span class="badge b-blue">Normale</span>',
    'basse'    => '<span class="badge b-gray">Basse</span>',
    default    => '<span class="badge b-gray">'.htmlspecialchars($p).'</span>'
  };
}
function badgeIntervStatut(string $s):string {
  return match($s){
    'planifiee' => '<span class="badge b-cyan">Planifiée</span>',
    'en_cours'  => '<span class="badge b-blue">En cours</span>',
    'terminee'  => '<span class="badge b-green">Terminée</span>',
    default     => '<span class="badge b-gray">'.htmlspecialchars($s).'</span>'
  };
}
function initiales(string $nom):string {
  $parts = explode(' ', trim($nom));
  $i = strtoupper(substr($parts[0],0,1));
  if(isset($parts[1])) $i .= strtoupper(substr($parts[1],0,1));
  return $i;
}
function santeColor(int $v):string {
  if($v>=70) return 'var(--ok)';
  if($v>=40) return 'var(--warn)';
  return 'var(--danger)';
}
function typeEmoji(string $t):string {
  return match($t){'laptop'=>'💻','desktop'=>'🖥️','imprimante'=>'🖨️','tablette'=>'📱','reseau'=>'📡',default=>'🔧'};
}
