<?php
require_once '../config.php';
if(!isAdmin()||$_SERVER['REQUEST_METHOD']!=='POST') redirect('../admin.php');
$action=$_POST['action']??'';
if($action==='ajouter'){
  $nom=clean($_POST['nom']??'');
  $serial=clean($_POST['serial_number']??'')?:null;
  $type=$_POST['type']??'autre';
  $statut=$_POST['statut']??'operationnel';
  $sante=min(100,max(0,(int)($_POST['sante']??100)));
  $loc=clean($_POST['localisation']??'');
  if(empty($nom)){$_SESSION['err']='Nom obligatoire.';redirect('../admin.php?p=inventaire');}
  try{
    $pdo->prepare("INSERT INTO appareils(nom,serial_number,type,statut,sante,localisation)VALUES(?,?,?,?,?,?)")->execute([$nom,$serial,$type,$statut,$sante,$loc]);
    $_SESSION['msg']='Appareil ajouté.';
  }catch(PDOException $e){$_SESSION['err']='Numéro de série déjà utilisé.';}
}elseif($action==='modifier'){
  $id=(int)($_POST['id']??0);
  $nom=clean($_POST['nom']??'');
  $serial=clean($_POST['serial_number']??'')?:null;
  $statut=$_POST['statut']??'operationnel';
  $sante=min(100,max(0,(int)($_POST['sante']??100)));
  $loc=clean($_POST['localisation']??'');
  $pdo->prepare("UPDATE appareils SET nom=?,serial_number=?,statut=?,sante=?,localisation=? WHERE id=?")->execute([$nom,$serial,$statut,$sante,$loc,$id]);
  $_SESSION['msg']='Appareil mis à jour.';
}elseif($action==='supprimer'){
  $id=(int)($_POST['id']??0);
  $pdo->prepare("DELETE FROM appareils WHERE id=?")->execute([$id]);
  $_SESSION['msg']='Appareil supprimé.';
}
redirect('../admin.php?p=inventaire');
