<?php
require_once '../config.php';
if(!isSuperAdmin()||$_SERVER['REQUEST_METHOD']!=='POST') redirect('../admin.php');
$action=$_POST['action']??'';
if($action==='ajouter'){
  $nom=clean($_POST['nom']??'');
  $email=trim($_POST['email']??'');
  $role=in_array($_POST['role']??'',['Admin','Technicien'])?$_POST['role']:'Technicien';
  if(empty($nom)||!filter_var($email,FILTER_VALIDATE_EMAIL)){$_SESSION['err']='Nom et email valides requis.';redirect('../admin.php?p=admins');}
  $s=$pdo->prepare("SELECT id FROM admins WHERE email=?");$s->execute([$email]);
  if($s->fetch()){$_SESSION['err']='Email déjà utilisé.';redirect('../admin.php?p=admins');}
  $hash=password_hash('TechCare@2026',PASSWORD_BCRYPT);
  $pdo->prepare("INSERT INTO admins(nom,email,password_hash,role)VALUES(?,?,?,?)")->execute([$nom,$email,$hash,$role]);
  $_SESSION['msg']="Compte créé pour $nom. Mot de passe par défaut : TechCare@2026";
}elseif($action==='supprimer'){
  $id=(int)($_POST['id']??0);
  $s=$pdo->prepare("SELECT role FROM admins WHERE id=?");$s->execute([$id]);$a=$s->fetch();
  if($a&&$a['role']==='Super Admin'){$_SESSION['err']='Impossible de supprimer le Super Admin.';redirect('../admin.php?p=admins');}
  $pdo->prepare("DELETE FROM admins WHERE id=?")->execute([$id]);
  $_SESSION['msg']='Compte admin supprimé.';
}
redirect('../admin.php?p=admins');
