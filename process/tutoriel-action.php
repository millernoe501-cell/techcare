<?php
require_once '../config.php';
if(!isAdmin()||$_SERVER['REQUEST_METHOD']!=='POST') redirect('../admin.php');
$action=$_POST['action']??'';
if($action==='ajouter'){
  $titre=clean($_POST['titre']??'');
  $niveau=in_array($_POST['niveau']??'',['debutant','intermediaire','avance'])?$_POST['niveau']:'debutant';
  $duree=clean($_POST['duree']??'');
  $emoji=clean($_POST['emoji']??'🎬');
  $desc=clean($_POST['description']??'');
  $url=filter_var($_POST['url_video']??'',FILTER_VALIDATE_URL)?$_POST['url_video']:'';
  if(empty($titre)){$_SESSION['err']='Titre obligatoire.';redirect('../admin.php?p=tutoriels');}
  $pdo->prepare("INSERT INTO tutoriels(titre,description,niveau,duree,emoji,url_video)VALUES(?,?,?,?,?,?)")->execute([$titre,$desc,$niveau,$duree,$emoji,$url]);
  $_SESSION['msg']='Tutoriel ajouté avec succès.';
}elseif($action==='supprimer'){
  $pdo->prepare("DELETE FROM tutoriels WHERE id=?")->execute([(int)$_POST['id']]);
  $_SESSION['msg']='Tutoriel supprimé.';
}
redirect('../admin.php?p=tutoriels');
