<?php
require_once '../config.php';
if($_SERVER['REQUEST_METHOD']!=='POST') redirect('../index.php');
$nom=clean($_POST['nom']??'');
$email=trim($_POST['email']??'');
$role=in_array($_POST['role']??'',['etudiant','employe'])?$_POST['role']:'etudiant';
$pwd=$_POST['password']??'';
if(empty($nom)||!filter_var($email,FILTER_VALIDATE_EMAIL)||strlen($pwd)<8){
  $_SESSION['register_error']='Tous les champs sont requis (mot de passe : 8 car. minimum).';
  redirect('../index.php?modal=register');
}
$s=$pdo->prepare("SELECT id FROM utilisateurs WHERE email=?");$s->execute([$email]);
if($s->fetch()){$_SESSION['register_error']='Cette adresse email est déjà utilisée.';redirect('../index.php?modal=register');}
$hash=password_hash($pwd,PASSWORD_BCRYPT);
$s=$pdo->prepare("INSERT INTO utilisateurs(nom,email,password_hash,role)VALUES(?,?,?,?)");
$s->execute([$nom,$email,$hash,$role]);
$id=$pdo->lastInsertId();
session_regenerate_id(true);
$_SESSION['user_id']=$id;$_SESSION['user_nom']=$nom;$_SESSION['user_email']=$email;$_SESSION['user_role']=$role;
redirect('../dashboard.php');
