<?php
require_once '../config.php';
if(!isAdmin()||$_SERVER['REQUEST_METHOD']!=='POST') redirect('../admin.php');
$id=(int)($_POST['id']??0);$action=$_POST['action']??'';
$statut=$action==='activer'?'actif':'inactif';
$pdo->prepare("UPDATE utilisateurs SET statut=? WHERE id=?")->execute([$statut,$id]);
$_SESSION['msg']='Compte '.($statut==='actif'?'activé':'désactivé').'.';
redirect('../admin.php?p=users');
