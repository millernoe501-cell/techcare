<?php
require_once '../config.php';
if($_SERVER['REQUEST_METHOD']!=='POST') redirect('../index.php');
$nom=clean($_POST['nom_public']??'');
$email=trim($_POST['email_public']??'');
$service=clean($_POST['service_public']??'');
$titre="Panne signalée par $nom";
$desc=clean($_POST['description']??'');
$prio=in_array($_POST['priorite']??'',['basse','normale','haute','critique'])?$_POST['priorite']:'normale';
if(empty($nom)||empty($desc)||!filter_var($email,FILTER_VALIDATE_EMAIL)){
  $_SESSION['login_error']='Veuillez remplir tous les champs obligatoires.';
  redirect('../index.php?modal=panne-public');
}
$s=$pdo->prepare("INSERT INTO tickets(titre,description,nom_public,email_public,service_public,priorite,statut)VALUES(?,?,?,?,?,?,'ouvert')");
$s->execute([$titre,$desc,$nom,$email,$service,$prio]);
$id=$pdo->lastInsertId();
$_SESSION['success']="Signalement enregistré ! Numéro de ticket : #$id. Connectez-vous pour suivre l'avancement.";
redirect('../index.php?modal=login');
