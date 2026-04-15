<?php
require_once '../config.php';
if($_SERVER['REQUEST_METHOD']!=='POST') redirect('../index.php');
$email=trim($_POST['email']??''); $pwd=$_POST['password']??'';
if(empty($email)||empty($pwd)){$_SESSION['login_error']='Champs obligatoires.';redirect('../index.php?modal=login');}
// Check admins first
$s=$pdo->prepare("SELECT * FROM admins WHERE email=? AND statut='actif' LIMIT 1");
$s->execute([$email]); $a=$s->fetch();
if($a && password_verify($pwd,$a['password_hash'])){
  session_regenerate_id(true);
  $_SESSION['admin_id']=$a['id'];$_SESSION['admin_nom']=$a['nom'];
  $_SESSION['admin_email']=$a['email'];$_SESSION['admin_role']=$a['role'];
  redirect('../admin.php');
}
// Check users
$s=$pdo->prepare("SELECT * FROM utilisateurs WHERE email=? LIMIT 1");
$s->execute([$email]); $u=$s->fetch();
if($u && password_verify($pwd,$u['password_hash'])){
  if($u['statut']==='inactif'){$_SESSION['login_error']='Compte désactivé. Contactez un administrateur.';redirect('../index.php?modal=login');}
  session_regenerate_id(true);
  $_SESSION['user_id']=$u['id'];$_SESSION['user_nom']=$u['nom'];
  $_SESSION['user_email']=$u['email'];$_SESSION['user_role']=$u['role'];
  redirect('../dashboard.php');
}
$_SESSION['login_error']='Email ou mot de passe incorrect.';
redirect('../index.php?modal=login');
