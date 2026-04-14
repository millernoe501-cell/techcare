<?php
require_once '../config.php';
if(!isLoggedIn()||$_SERVER['REQUEST_METHOD']!=='POST') redirect('../dashboard.php');
$desc=clean($_POST['description']??'');
$prio=in_array($_POST['priorite']??'',['basse','normale','haute','critique'])?$_POST['priorite']:'normale';
$serial=clean($_POST['serial']??'');
$loc=clean($_POST['localisation']??'');
$type=$_POST['type_appareil']??'';
if(empty($desc)){$_SESSION['err']='Description obligatoire.';redirect('../dashboard.php?p=nouveau');}
$titre='Panne '.($type?'('.$type.')':'').' signalée le '.date('d/m/Y');
// Try matching device by serial
$aid=null;
if(!empty($serial)){$s=$pdo->prepare("SELECT id FROM appareils WHERE serial_number=?");$s->execute([$serial]);$row=$s->fetch();if($row)$aid=$row['id'];}
$s=$pdo->prepare("INSERT INTO tickets(titre,description,appareil_id,demandeur_id,priorite,statut)VALUES(?,?,?,?,?,'ouvert')");
$s->execute([$titre,$desc,$aid,$_SESSION['user_id'],$prio]);
$id=$pdo->lastInsertId();
$_SESSION['msg']="Ticket #$id créé avec succès !";
redirect('../dashboard.php?p=tickets');
