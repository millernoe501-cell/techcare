<?php
require_once '../config.php';
if(!isAdmin()||$_SERVER['REQUEST_METHOD']!=='POST') redirect('../admin.php');
$action=$_POST['action']??'';
if($action==='ajouter'){
  $tid=$_POST['ticket_id']??null;if($tid===''||$tid==='0')$tid=null;
  $tech=$_POST['technicien_id']??null;if($tech===''||$tech==='0')$tech=null;
  $type=clean($_POST['type_intervention']??'');
  $date=!empty($_POST['date_debut'])?$_POST['date_debut']:null;
  $dur=clean($_POST['duree_estimee']??'');
  $notes=clean($_POST['notes']??'');
  if(empty($type)){$_SESSION['err']='Type intervention obligatoire.';redirect('../admin.php?p=interventions');}
  $pdo->prepare("INSERT INTO interventions(ticket_id,technicien_id,type_intervention,date_debut,duree_estimee,notes)VALUES(?,?,?,?,?,?)")->execute([$tid,$tech,$type,$date,$dur,$notes]);
  if($tid) $pdo->prepare("UPDATE tickets SET statut='en_cours' WHERE id=? AND statut='ouvert'")->execute([$tid]);
  $_SESSION['msg']='Intervention planifiée.';
}elseif($action==='en_cours'){
  $pdo->prepare("UPDATE interventions SET statut='en_cours' WHERE id=?")->execute([(int)$_POST['id']]);
  $_SESSION['msg']='Intervention démarrée.';
}elseif($action==='terminer'){
  $id=(int)$_POST['id'];
  $pdo->prepare("UPDATE interventions SET statut='terminee' WHERE id=?")->execute([$id]);
  // Optionally resolve linked ticket
  $r=$pdo->prepare("SELECT ticket_id FROM interventions WHERE id=?");$r->execute([$id]);$row=$r->fetch();
  if($row&&$row['ticket_id']) $pdo->prepare("UPDATE tickets SET statut='resolu',date_resolution=NOW() WHERE id=? AND statut='en_cours'")->execute([$row['ticket_id']]);
  $_SESSION['msg']='Intervention terminée.';
}elseif($action==='supprimer'){
  $pdo->prepare("DELETE FROM interventions WHERE id=?")->execute([(int)$_POST['id']]);
  $_SESSION['msg']='Intervention supprimée.';
}
redirect('../admin.php?p=interventions');
