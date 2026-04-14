<?php
require_once '../config.php';
if(!isLoggedIn()||$_SERVER['REQUEST_METHOD']!=='POST') redirect('../dashboard.php');
$nom=clean($_POST['nom']??'');
$email=trim($_POST['email']??'');
$tel=clean($_POST['telephone']??'');
$dep=clean($_POST['departement']??'');
$pwd=$_POST['new_password']??'';
if(empty($nom)||!filter_var($email,FILTER_VALIDATE_EMAIL)){$_SESSION['err']='Nom et email valides requis.';redirect('../dashboard.php?p=profil');}
// Check email uniqueness
$s=$pdo->prepare("SELECT id FROM utilisateurs WHERE email=? AND id!=?");$s->execute([$email,$_SESSION['user_id']]);
if($s->fetch()){$_SESSION['err']='Email déjà utilisé.';redirect('../dashboard.php?p=profil');}
if(!empty($pwd)&&strlen($pwd)<8){$_SESSION['err']='Mot de passe trop court.';redirect('../dashboard.php?p=profil');}
if(!empty($pwd)){
  $h=password_hash($pwd,PASSWORD_BCRYPT);
  $pdo->prepare("UPDATE utilisateurs SET nom=?,email=?,telephone=?,departement=?,password_hash=? WHERE id=?")->execute([$nom,$email,$tel,$dep,$h,$_SESSION['user_id']]);
}else{
  $pdo->prepare("UPDATE utilisateurs SET nom=?,email=?,telephone=?,departement=? WHERE id=?")->execute([$nom,$email,$tel,$dep,$_SESSION['user_id']]);
}
$_SESSION['user_nom']=$nom;$_SESSION['user_email']=$email;
$_SESSION['msg']='Profil mis à jour avec succès !';
redirect('../dashboard.php?p=profil');
