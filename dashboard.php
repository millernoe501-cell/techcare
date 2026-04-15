<?php
require_once 'config.php';
if(!isLoggedIn()) redirect('index.php');
$uid=$_SESSION['user_id']; $unom=$_SESSION['user_nom']; $urole=$_SESSION['user_role'];
$panel=$_GET['p']??'accueil';
$msg=flash('msg'); $err=flash('err');
// Mes tickets
$q=$pdo->prepare("SELECT t.*,a.nom AS anom,adm.nom AS technom FROM tickets t LEFT JOIN appareils a ON t.appareil_id=a.id LEFT JOIN interventions i ON i.ticket_id=t.id LEFT JOIN admins adm ON i.technicien_id=adm.id WHERE t.demandeur_id=? ORDER BY t.date_creation DESC");
$q->execute([$uid]); $mes=$q->fetchAll();
$nb_tot=count($mes);
$nb_ouv=count(array_filter($mes,fn($t)=>$t['statut']==='ouvert'));
$nb_enc=count(array_filter($mes,fn($t)=>$t['statut']==='en_cours'));
$nb_res=count(array_filter($mes,fn($t)=>$t['statut']==='resolu'));
$ini=initiales($unom);
// Status track helper
function stTrack(string $statut):string {
  $steps=['ouvert'=>'Reçu','en_cours'=>'En cours','resolu'=>'Résolu'];
  $order=['ouvert'=>1,'en_cours'=>2,'resolu'=>3];
  $cur=$order[$statut]??1; $html='<div class="st-track">';
  foreach($steps as $k=>$lbl){
    $n=$order[$k]; $cls=''; $sym='';
    if($cur>$n){$cls='done';$sym='✓';}
    elseif($cur===$n){$cls='cur';$sym='▶';}
    $html.='<div class="st-step '.($cls?$cls:'').'"><div class="st-dot '.($cls?$cls:''). '">'.$sym.'</div><div class="st-lbl '.($cls?$cls:'').'">'.$lbl.'</div></div>';
  }
  return $html.'</div>';
}
?><!DOCTYPE html><html lang="fr"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>TechCare — Mon espace</title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">
</head><body>
<div class="app-layout">
  <aside class="sidebar">
    <div class="sb-logo"><div class="sb-logo-name">⚙️ TechCare</div><div class="sb-logo-role">Espace Utilisateur</div></div>
    <nav class="sb-nav">
      <a href="?p=accueil" class="sb-item <?php echo $panel==='accueil'?'active':''; ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>Mon espace
      </a>
      <a href="?p=tickets" class="sb-item <?php echo $panel==='tickets'?'active':''; ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="2"/></svg>Mes tickets
        <?php if($nb_ouv+$nb_enc>0): ?><span class="sb-badge"><?php echo $nb_ouv+$nb_enc; ?></span><?php endif; ?>
      </a>
      <a href="?p=nouveau" class="sb-item <?php echo $panel==='nouveau'?'active':''; ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>Signaler une panne
      </a>
      <a href="?p=profil" class="sb-item <?php echo $panel==='profil'?'active':''; ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>Mon profil
      </a>
    </nav>
    <div class="sb-footer">
      <div class="sb-user">
        <div class="sb-av" style="background:linear-gradient(135deg,#10b981,#06b6d4)"><?php echo $ini; ?></div>
        <div><div class="sb-uname"><?php echo clean($unom); ?></div><div class="sb-urole"><?php echo ucfirst($urole); ?></div></div>
        <a href="process/logout.php" class="sb-logout" title="Déconnexion">✕</a>
      </div>
    </div>
  </aside>
  <main class="app-main">
    <div class="topbar">
      <div class="topbar-title" id="usr-title"><?php echo match($panel){'accueil'=>'Mon espace','tickets'=>'Mes tickets','nouveau'=>'Signaler une panne','profil'=>'Mon profil',default=>'Mon espace'}; ?></div>
      <div class="topbar-right">
        <div class="icon-btn"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg><div class="notif-dot"></div></div>
        <div class="sb-av" style="background:linear-gradient(135deg,#10b981,#06b6d4)"><?php echo $ini; ?></div>
      </div>
    </div>
    <div class="app-content">
      <?php if($msg): ?><div class="alert alert-success">✅ <?php echo $msg; ?></div><?php endif; ?>
      <?php if($err): ?><div class="alert alert-error">⚠️ <?php echo $err; ?></div><?php endif; ?>

<?php if($panel==='accueil'): ?>
      <div style="background:linear-gradient(135deg,#eff6ff,#ecfeff);border:1px solid #bfdbfe;border-radius:13px;padding:22px;margin-bottom:20px">
        <div style="font-size:1.25rem;font-weight:800;margin-bottom:5px">👋 Bonjour, <?php echo clean($unom); ?> !</div>
        <div style="color:var(--muted);font-size:.86rem">
          <?php if($nb_enc+$nb_ouv>0): ?>Vous avez <strong style="color:var(--accent)"><?php echo $nb_enc+$nb_ouv; ?> ticket(s) actif(s)</strong>. <?php if($nb_enc): ?>Votre dernier ticket est en cours de traitement.<?php endif; ?>
          <?php else: ?>Aucun ticket actif. Tout va bien ! 🎉<?php endif; ?>
        </div>
      </div>
      <div class="stats-row" style="grid-template-columns:repeat(3,1fr)">
        <div class="stat-card"><div class="stat-stripe"></div><div class="stat-lbl">Mes tickets</div><div class="stat-val"><?php echo $nb_tot; ?></div><div class="stat-sub"><?php echo $nb_enc; ?> en cours</div></div>
        <div class="stat-card"><div class="stat-stripe ok"></div><div class="stat-lbl">Résolus</div><div class="stat-val" style="color:var(--ok)"><?php echo $nb_res; ?></div></div>
        <div class="stat-card"><div class="stat-stripe warn"></div><div class="stat-lbl">En attente</div><div class="stat-val" style="color:var(--warn)"><?php echo $nb_ouv; ?></div></div>
      </div>
      <?php if($mes): $t=$mes[0]; ?>
      <div class="card" style="margin-top:16px">
        <div class="card-head"><div class="card-title">Dernier ticket — #<?php echo $t['id']; ?></div><?php echo badgeStatut($t['statut']); ?></div>
        <div style="font-weight:700;margin-bottom:3px"><?php echo clean($t['titre']); ?></div>
        <div style="font-size:.81rem;color:var(--muted);margin-bottom:14px">Signalé le <?php echo date('d/m/Y',strtotime($t['date_creation'])); ?><?php if($t['technom']): ?> • Technicien : <?php echo clean($t['technom']); ?><?php endif; ?></div>
        <?php echo stTrack($t['statut']); ?>
      </div>
      <?php endif; ?>

<?php elseif($panel==='tickets'): ?>
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
        <div style="font-size:.88rem;color:var(--muted)"><?php echo $nb_tot; ?> ticket(s) au total</div>
        <a href="?p=nouveau" class="btn btn-primary">+ Signaler une panne</a>
      </div>
      <?php if(!$mes): ?><div class="alert alert-info">Aucun ticket pour l'instant. <a href="?p=nouveau">Signaler une panne</a></div>
      <?php else: foreach($mes as $t): ?>
      <div class="my-ticket">
        <div class="ticket-icon" style="width:40px;height:40px;border-radius:11px;background:var(--accent-light);font-size:1.1rem">💻</div>
        <div style="flex:1">
          <div style="font-size:.71rem;color:var(--muted)">#<?php echo $t['id']; ?> • <?php echo date('d/m/Y',strtotime($t['date_creation'])); ?></div>
          <div style="font-weight:700;font-size:.9rem;margin-bottom:2px"><?php echo clean($t['titre']); ?></div>
          <div style="font-size:.73rem;color:var(--muted)"><?php echo $t['technom']?'Technicien : '.clean($t['technom']):"En attente d'assignation"; ?></div>
          <?php echo stTrack($t['statut']); ?>
        </div>
        <?php echo badgeStatut($t['statut']); ?>
      </div>
      <?php endforeach; endif; ?>

<?php elseif($panel==='nouveau'): ?>
      <div class="panne-hero"><div style="font-size:2.2rem;margin-bottom:9px">🚨</div><div class="panne-title">Signaler une panne</div><div style="color:var(--muted);font-size:.86rem">Notre équipe traite votre demande sous 2h ouvrées.</div></div>
      <div class="card">
        <form method="POST" action="process/ticket-user.php">
          <div class="fg2">
            <div class="fg"><label class="lbl">Type d'appareil</label>
              <select class="inp" name="type_appareil">
                <option value="">Sélectionner…</option>
                <option value="laptop">Ordinateur portable</option>
                <option value="desktop">Ordinateur de bureau</option>
                <option value="imprimante">Imprimante</option>
                <option value="tablette">Tablette / Smartphone</option>
                <option value="reseau">Réseau</option>
                <option value="autre">Autre</option>
              </select>
            </div>
            <div class="fg"><label class="lbl">Priorité estimée</label>
              <select class="inp" name="priorite">
                <option value="normale">Normale</option>
                <option value="haute">Haute</option>
                <option value="critique">Critique — urgence</option>
              </select>
            </div>
            <div class="fg"><label class="lbl">Numéro de série (si connu)</label><input class="inp" type="text" name="serial" placeholder="Ex: CND3420XKP"></div>
            <div class="fg"><label class="lbl">Localisation</label><input class="inp" type="text" name="localisation" placeholder="Ex: Salle 204, Bât. A"></div>
            <div class="fg full"><label class="lbl">Description du problème</label><textarea class="inp" name="description" placeholder="Décrivez le problème en détail…" required></textarea></div>
          </div>
          <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:16px">
            <a href="?p=tickets" class="btn btn-outline">Annuler</a>
            <button type="submit" class="btn btn-primary">📨 Soumettre</button>
          </div>
        </form>
      </div>

<?php elseif($panel==='profil'): ?>
      <?php $u=$pdo->prepare("SELECT * FROM utilisateurs WHERE id=?");$u->execute([$uid]);$uu=$u->fetch(); ?>
      <div class="grid2">
        <div class="card">
          <div class="card-head"><div class="card-title">Informations personnelles</div></div>
          <form method="POST" action="process/update-profil.php">
            <div style="display:flex;align-items:center;gap:14px;margin-bottom:20px">
              <div style="width:56px;height:56px;border-radius:50%;background:linear-gradient(135deg,#10b981,#06b6d4);display:flex;align-items:center;justify-content:center;font-size:1.4rem;font-weight:700;color:#fff"><?php echo $ini; ?></div>
              <div><div style="font-size:1.05rem;font-weight:800"><?php echo clean($uu['nom']); ?></div><div style="color:var(--muted);font-size:.8rem"><?php echo ucfirst($uu['role']); ?> • Inscrit le <?php echo date('d/m/Y',strtotime($uu['date_inscription'])); ?></div></div>
            </div>
            <div class="fg"><label class="lbl">Nom complet</label><input class="inp" name="nom" value="<?php echo clean($uu['nom']); ?>" required></div>
            <div class="fg"><label class="lbl">Email</label><input class="inp" type="email" name="email" value="<?php echo clean($uu['email']); ?>" required></div>
            <div class="fg"><label class="lbl">Téléphone</label><input class="inp" name="telephone" value="<?php echo clean($uu['telephone']??''); ?>" placeholder="+226 70 00 00 00"></div>
            <div class="fg"><label class="lbl">Département / Filière</label><input class="inp" name="departement" value="<?php echo clean($uu['departement']??''); ?>" placeholder="Ex: Informatique L3"></div>
            <div class="fg"><label class="lbl">Nouveau mot de passe (laisser vide pour ne pas changer)</label><input class="inp" type="password" name="new_password" minlength="8" placeholder="••••••••"></div>
            <button type="submit" class="btn btn-primary" style="margin-top:4px">💾 Enregistrer</button>
          </form>
        </div>
        <div class="card" style="align-self:start">
          <div class="card-head"><div class="card-title">Activité</div></div>
          <div style="display:flex;flex-direction:column;gap:9px">
            <div class="alert alert-info">🎫 <?php echo $nb_tot; ?> ticket(s) au total</div>
            <?php if($nb_res>0): ?><div class="alert alert-success">✅ <?php echo $nb_res; ?> ticket(s) résolu(s)</div><?php endif; ?>
            <?php if($nb_enc+$nb_ouv>0): ?><div class="alert alert-warn">⏳ <?php echo $nb_enc+$nb_ouv; ?> ticket(s) actif(s)</div><?php endif; ?>
            <div style="font-size:.82rem;color:var(--muted);margin-top:4px">Rôle : <?php echo ucfirst($uu['role']); ?></div>
          </div>
        </div>
      </div>
<?php endif; ?>
    </div>
  </main>
</div>
</body></html>
