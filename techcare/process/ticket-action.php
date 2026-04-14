<?php
require_once '../config.php';
if(!isAdmin()||$_SERVER['REQUEST_METHOD']!=='POST') redirect('../admin.php');
$id=(int)($_POST['id']??0); $action=$_POST['action']??'';
if(!$id){redirect('../admin.php?p=tickets');}
match($action){
  'en_cours'  => (function()use($pdo,$id){$pdo->prepare("UPDATE tickets SET statut='en_cours' WHERE id=?")->execute([$id]);$_SESSION['msg']='Ticket passé en cours.';}  )(),
  'resolu'    => (function()use($pdo,$id){$pdo->prepare("UPDATE tickets SET statut='resolu',date_resolution=NOW() WHERE id=?")->execute([$id]);$_SESSION['msg']='Ticket résolu !';}  )(),
  'supprimer' => (function()use($pdo,$id){$pdo->prepare("DELETE FROM tickets WHERE id=?")->execute([$id]);$_SESSION['msg']='Ticket supprimé.';}  )(),
  default     => null
};
redirect('../admin.php?p=tickets'.( isset($_GET['s'])?'&s='.$_GET['s']:'' ));
