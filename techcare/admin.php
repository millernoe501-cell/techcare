<?php
require_once 'config.php';
if(!isAdmin()) redirect('index.php');
$panel=$_GET['p']??'dashboard';
$anom=$_SESSION['admin_nom']; $arole=$_SESSION['admin_role'];
$ain=initiales($anom);
$isSA=isSuperAdmin();
$msg=flash('msg'); $err=flash('err');
// Global stats
$gs=$pdo->query("SELECT
  (SELECT COUNT(*) FROM tickets WHERE statut='ouvert') t_ouv,
  (SELECT COUNT(*) FROM tickets WHERE statut='en_cours') t_enc,
  (SELECT COUNT(*) FROM tickets WHERE statut='resolu') t_res,
  (SELECT COUNT(*) FROM appareils) nb_app,
  (SELECT COUNT(*) FROM utilisateurs WHERE statut='actif') nb_usr,
  (SELECT COUNT(*) FROM admins WHERE statut='actif') nb_adm,
  (SELECT COUNT(*) FROM interventions WHERE statut IN ('planifiee','en_cours')) nb_int
")->fetch();
?><!DOCTYPE html><html lang="fr"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>TechCare — Administration</title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">
</head><body>
<div class="app-layout">
  <aside class="sidebar">
    <div class="sb-logo"><div class="sb-logo-name">⚙️ TechCare</div><div class="sb-logo-role">Administration</div></div>
    <nav class="sb-nav">
      <div class="sb-sec">Principal</div>
      <a href="?p=dashboard" class="sb-item <?php echo $panel==='dashboard'?'active':''; ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>Tableau de bord
      </a>
      <a href="?p=tickets" class="sb-item <?php echo $panel==='tickets'?'active':''; ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="2"/></svg>Tickets
        <?php if($gs['t_ouv']>0): ?><span class="sb-badge"><?php echo $gs['t_ouv']; ?></span><?php endif; ?>
      </a>
      <a href="?p=inventaire" class="sb-item <?php echo $panel==='inventaire'?'active':''; ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>Inventaire
      </a>
      <a href="?p=interventions" class="sb-item <?php echo $panel==='interventions'?'active':''; ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/></svg>Interventions
      </a>
      <div class="sb-sec">Analyse</div>
      <a href="?p=rapports" class="sb-item <?php echo $panel==='rapports'?'active':''; ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>Statistiques
      </a>
      <div class="sb-sec">Comptes &amp; Formation</div>
      <a href="?p=users" class="sb-item <?php echo $panel==='users'?'active':''; ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>Utilisateurs
      </a>
      <?php if($isSA): ?>
      <a href="?p=admins" class="sb-item <?php echo $panel==='admins'?'active':''; ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>Comptes Admins<span class="sb-badge sb-badge-ok"><?php echo $gs['nb_adm']; ?></span>
      </a>
      <?php endif; ?>
      <a href="?p=tutoriels" class="sb-item <?php echo $panel==='tutoriels'?'active':''; ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2"/></svg>Tutoriels vidéo
      </a>
    </nav>
    <div class="sb-footer">
      <div class="sb-user">
        <div class="sb-av"><?php echo $ain; ?></div>
        <div><div class="sb-uname"><?php echo clean($anom); ?></div><div class="sb-urole"><?php echo $arole; ?></div></div>
        <a href="process/logout.php" class="sb-logout" title="Déconnexion">✕</a>
      </div>
    </div>
  </aside>
  <main class="app-main">
    <div class="topbar">
      <div class="topbar-title" id="adm-title"><?php echo match($panel){'dashboard'=>'Tableau de bord','tickets'=>'Gestion des tickets','inventaire'=>'Inventaire des appareils','interventions'=>'Suivi des interventions','rapports'=>'Rapports & Statistiques','users'=>'Utilisateurs','admins'=>'Comptes Administrateurs','tutoriels'=>'Tutoriels vidéo',default=>'Administration'}; ?></div>
      <div class="topbar-right">
        <input class="search" type="text" placeholder="🔍 Rechercher…" oninput="liveSearch(this.value)">
        <div class="icon-btn"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg><div class="notif-dot"></div></div>
        <div class="sb-av"><?php echo $ain; ?></div>
      </div>
    </div>
    <div class="app-content">
      <?php if($msg): ?><div class="alert alert-success">✅ <?php echo $msg; ?></div><?php endif; ?>
      <?php if($err): ?><div class="alert alert-error">⚠️ <?php echo $err; ?></div><?php endif; ?>

<?php /* ============ DASHBOARD ============ */ if($panel==='dashboard'): ?>
      <div class="stats-row">
        <div class="stat-card"><div class="stat-stripe warn"></div><div class="stat-lbl">Tickets ouverts</div><div class="stat-val" style="color:var(--warn)"><?php echo $gs['t_ouv']; ?></div><div class="stat-sub"><?php echo $gs['t_enc']; ?> en cours</div></div>
        <div class="stat-card"><div class="stat-stripe ok"></div><div class="stat-lbl">Résolus</div><div class="stat-val" style="color:var(--ok)"><?php echo $gs['t_res']; ?></div></div>
        <div class="stat-card"><div class="stat-stripe info"></div><div class="stat-lbl">Appareils</div><div class="stat-val" style="color:var(--accent2)"><?php echo $gs['nb_app']; ?></div></div>
        <div class="stat-card"><div class="stat-stripe"></div><div class="stat-lbl">Techniciens</div><div class="stat-val"><?php echo $gs['nb_adm']; ?></div></div>
      </div>
      <div class="grid3">
        <div class="card">
          <div class="card-head"><div class="card-title">Activité hebdomadaire</div><span class="badge b-green"><span class="dot"></span> En direct</span></div>
          <div class="chart-bars" id="admin-chart"></div>
          <div style="display:flex;gap:12px;margin-top:9px;font-size:.71rem;color:var(--muted)">
            <span style="display:flex;align-items:center;gap:5px"><span style="width:9px;height:9px;background:var(--accent);border-radius:2px;display:inline-block"></span>Ouverts</span>
            <span style="display:flex;align-items:center;gap:5px"><span style="width:9px;height:9px;background:var(--ok);border-radius:2px;display:inline-block"></span>Résolus</span>
          </div>
        </div>
        <div class="card">
          <div class="card-head"><div class="card-title">Priorités en attente</div></div>
          <?php $prios=$pdo->query("SELECT priorite,COUNT(*) c FROM tickets WHERE statut!='resolu' GROUP BY priorite ORDER BY FIELD(priorite,'critique','haute','normale','basse')")->fetchAll();
          $pcols=['critique'=>'var(--danger)','haute'=>'var(--warn)','normale'=>'var(--accent)','basse'=>'var(--muted)'];
          $tot_p=array_sum(array_column($prios,'c'))?:1;
          foreach($prios as $pp): ?>
          <div style="margin-bottom:10px">
            <div style="display:flex;justify-content:space-between;font-size:.82rem;margin-bottom:3px"><span><?php echo ucfirst($pp['priorite']); ?></span><span style="color:<?php echo $pcols[$pp['priorite']]??'var(--muted)'; ?>"><?php echo $pp['c']; ?></span></div>
            <div class="prog-bar"><div class="prog-fill" style="width:<?php echo round($pp['c']/$tot_p*100); ?>%;background:<?php echo $pcols[$pp['priorite']]??'var(--accent)'; ?>"></div></div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="grid2" style="margin-top:16px">
        <div class="card">
          <div class="card-head"><div class="card-title">Tickets récents</div><a href="?p=tickets" class="btn btn-outline btn-sm">Voir tout</a></div>
          <?php $rec=$pdo->query("SELECT t.*,u.nom unom FROM tickets t LEFT JOIN utilisateurs u ON t.demandeur_id=u.id ORDER BY t.date_creation DESC LIMIT 4")->fetchAll();
          foreach($rec as $t): ?>
          <div class="ticket-row">
            <div class="ticket-icon">💻</div>
            <div style="flex:1"><div style="font-weight:600;font-size:.87rem"><?php echo clean($t['titre']); ?></div><div style="font-size:.72rem;color:var(--muted)"><?php echo clean($t['unom']??$t['nom_public']??'Public'); ?> • <?php echo date('d/m H:i',strtotime($t['date_creation'])); ?></div></div>
            <?php echo badgePriorite($t['priorite']); ?>
            <?php echo badgeStatut($t['statut']); ?>
          </div>
          <?php endforeach; ?>
        </div>
        <div class="card">
          <div class="card-head"><div class="card-title">Interventions du jour</div></div>
          <?php $tday=$pdo->query("SELECT i.*,t.titre ttitre,adm.nom technom FROM interventions i LEFT JOIN tickets t ON i.ticket_id=t.id LEFT JOIN admins adm ON i.technicien_id=adm.id WHERE DATE(i.date_debut)=CURDATE() OR i.statut='en_cours' ORDER BY i.date_debut LIMIT 4")->fetchAll();
          if($tday): foreach($tday as $iv): ?>
          <div class="tl-item">
            <div class="tl-dot <?php echo $iv['statut']==='en_cours'?'active':''; ?>">🔧</div>
            <div><div style="font-weight:600;font-size:.86rem"><?php echo clean($iv['type_intervention']??$iv['ttitre']??'Intervention'); ?></div>
            <div style="font-size:.71rem;color:var(--muted)"><?php echo $iv['date_debut']?date('H:i',strtotime($iv['date_debut'])):'—'; ?> — <?php echo clean($iv['technom']??'Non assigné'); ?></div></div>
          </div>
          <?php endforeach; else: ?><div style="color:var(--muted);font-size:.84rem">Aucune intervention planifiée aujourd'hui.</div><?php endif; ?>
        </div>
      </div>

<?php /* ============ TICKETS ============ */ elseif($panel==='tickets'): ?>
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:10px">
        <div class="tabs" style="margin-bottom:0">
          <a href="?p=tickets" class="tab <?php echo !isset($_GET['s'])?'active':''; ?>">Tous (<?php echo $gs['t_ouv']+$gs['t_enc']+$gs['t_res']; ?>)</a>
          <a href="?p=tickets&s=ouvert" class="tab <?php echo ($_GET['s']??'')==='ouvert'?'active':''; ?>">Ouverts (<?php echo $gs['t_ouv']; ?>)</a>
          <a href="?p=tickets&s=en_cours" class="tab <?php echo ($_GET['s']??'')==='en_cours'?'active':''; ?>">En cours (<?php echo $gs['t_enc']; ?>)</a>
          <a href="?p=tickets&s=resolu" class="tab <?php echo ($_GET['s']??'')==='resolu'?'active':''; ?>">Résolus (<?php echo $gs['t_res']; ?>)</a>
        </div>
      </div>
      <?php
      $w=''; $pa=[];
      if(isset($_GET['s'])&&in_array($_GET['s'],['ouvert','en_cours','resolu'])){$w='WHERE t.statut=?';$pa[]=$_GET['s'];}
      $q=$pdo->prepare("SELECT t.*,u.nom unom,a.nom anom FROM tickets t LEFT JOIN utilisateurs u ON t.demandeur_id=u.id LEFT JOIN appareils a ON t.appareil_id=a.id $w ORDER BY FIELD(t.priorite,'critique','haute','normale','basse'),t.date_creation DESC");
      $q->execute($pa); $tks=$q->fetchAll();
      ?>
      <div class="card"><div class="tbl-wrap"><table id="searchable">
        <thead><tr><th>#</th><th>Titre</th><th>Demandeur</th><th>Priorité</th><th>Statut</th><th>Date</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach($tks as $t): ?>
        <tr>
          <td style="color:var(--muted)">#<?php echo $t['id']; ?></td>
          <td><strong><?php echo clean($t['titre']); ?></strong><?php if($t['anom']): ?><br><span style="font-size:.71rem;color:var(--muted)"><?php echo clean($t['anom']); ?></span><?php endif; ?></td>
          <td><?php echo clean($t['unom']??$t['nom_public']??'Public'); ?></td>
          <td><?php echo badgePriorite($t['priorite']); ?></td>
          <td><?php echo badgeStatut($t['statut']); ?></td>
          <td style="color:var(--muted)"><?php echo date('d/m/Y',strtotime($t['date_creation'])); ?></td>
          <td><div style="display:flex;gap:5px;flex-wrap:wrap">
            <?php if($t['statut']==='ouvert'): ?>
            <form method="POST" action="process/ticket-action.php"><input type="hidden" name="id" value="<?php echo $t['id']; ?>"><input type="hidden" name="action" value="en_cours"><button class="btn btn-outline btn-sm">▶ En cours</button></form>
            <?php elseif($t['statut']==='en_cours'): ?>
            <form method="POST" action="process/ticket-action.php"><input type="hidden" name="id" value="<?php echo $t['id']; ?>"><input type="hidden" name="action" value="resolu"><button class="btn btn-success-s btn-sm">✓ Résoudre</button></form>
            <?php endif; ?>
            <form method="POST" action="process/ticket-action.php" onsubmit="return confirm('Supprimer ce ticket ?')"><input type="hidden" name="id" value="<?php echo $t['id']; ?>"><input type="hidden" name="action" value="supprimer"><button class="btn btn-danger-s btn-sm">🗑</button></form>
          </div></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
      </table></div></div>

<?php /* ============ INVENTAIRE ============ */ elseif($panel==='inventaire'): ?>
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:10px">
        <div class="tabs" style="margin-bottom:0">
          <a href="?p=inventaire" class="tab <?php echo !isset($_GET['t'])?'active':''; ?>">Tous</a>
          <?php foreach(['laptop'=>'💻 Laptops','desktop'=>'🖥️ Desktop','imprimante'=>'🖨️ Imprimantes','reseau'=>'📡 Réseau','tablette'=>'📱 Tablettes'] as $v=>$l): ?>
          <a href="?p=inventaire&t=<?php echo $v; ?>" class="tab <?php echo ($_GET['t']??'')===$v?'active':''; ?>"><?php echo $l; ?></a>
          <?php endforeach; ?>
        </div>
        <button class="btn btn-primary" onclick="openModal('add-app')">+ Ajouter</button>
      </div>
      <?php
      $aw=''; $ap=[];
      if(isset($_GET['t'])&&in_array($_GET['t'],['laptop','desktop','imprimante','reseau','tablette','autre'])){$aw='WHERE type=?';$ap[]=$_GET['t'];}
      $apps=$pdo->prepare("SELECT * FROM appareils $aw ORDER BY statut,nom");$apps->execute($ap);$appareils=$apps->fetchAll();
      $smap=['operationnel'=>['b-green','Opérationnel'],'en_panne'=>['b-red','En panne'],'maintenance'=>['b-blue','Maintenance'],'hors_service'=>['b-red','Hors service']];
      ?>
      <div class="dev-grid" id="searchable">
      <?php foreach($appareils as $a): $sc=$smap[$a['statut']]??['b-gray',$a['statut']]; ?>
      <div class="dev-card">
        <div class="dev-emoji"><?php echo typeEmoji($a['type']); ?></div>
        <div class="dev-name"><?php echo clean($a['nom']); ?></div>
        <div class="dev-serial">S/N: <?php echo clean($a['serial_number']??'—'); ?></div>
        <span class="badge <?php echo $sc[0]; ?>"><?php echo $sc[1]; ?></span>
        <div class="prog-bar"><div class="prog-fill" style="width:<?php echo $a['sante']; ?>%;background:<?php echo santeColor($a['sante']); ?>"></div></div>
        <div style="font-size:.68rem;color:var(--muted);margin-top:3px">Santé <?php echo $a['sante']; ?>%</div>
        <div style="display:flex;gap:6px;margin-top:10px">
          <button class="btn btn-outline btn-sm" onclick='openEditApp(<?php echo json_encode($a); ?>)'>✏️</button>
          <form method="POST" action="process/appareil-action.php" onsubmit="return confirm('Supprimer ?')"><input type="hidden" name="id" value="<?php echo $a['id']; ?>"><input type="hidden" name="action" value="supprimer"><button class="btn btn-danger-s btn-sm">🗑</button></form>
        </div>
      </div>
      <?php endforeach; ?>
      </div>
      <!-- MODAL ADD APPAREIL -->
      <div class="modal-overlay" id="modal-add-app"><div class="modal modal-lg">
        <button class="close-btn" onclick="closeModal('add-app')">✕</button>
        <div class="modal-title">Ajouter un appareil</div>
        <form method="POST" action="process/appareil-action.php"><input type="hidden" name="action" value="ajouter">
          <div class="fg2">
            <div class="fg full"><label class="lbl">Nom de l'appareil</label><input class="inp" name="nom" required placeholder="Ex: HP ProBook 450 G9"></div>
            <div class="fg"><label class="lbl">Numéro de série</label><input class="inp" name="serial_number" placeholder="Ex: CND3420XKP"></div>
            <div class="fg"><label class="lbl">Type</label><select class="inp" name="type"><option value="laptop">💻 Laptop</option><option value="desktop">🖥️ Desktop</option><option value="imprimante">🖨️ Imprimante</option><option value="tablette">📱 Tablette</option><option value="reseau">📡 Réseau</option><option value="autre">🔧 Autre</option></select></div>
            <div class="fg"><label class="lbl">Statut</label><select class="inp" name="statut"><option value="operationnel">Opérationnel</option><option value="maintenance">Maintenance</option><option value="en_panne">En panne</option><option value="hors_service">Hors service</option></select></div>
            <div class="fg"><label class="lbl">Santé (%)</label><input class="inp" name="sante" type="number" min="0" max="100" value="100"></div>
            <div class="fg"><label class="lbl">Localisation</label><input class="inp" name="localisation" placeholder="Ex: Salle 204"></div>
          </div>
          <button type="submit" class="btn btn-primary btn-block" style="margin-top:12px">Ajouter l'appareil</button>
        </form>
      </div></div>
      <!-- MODAL EDIT APPAREIL -->
      <div class="modal-overlay" id="modal-edit-app"><div class="modal modal-lg">
        <button class="close-btn" onclick="closeModal('edit-app')">✕</button>
        <div class="modal-title">Modifier l'appareil</div>
        <form method="POST" action="process/appareil-action.php"><input type="hidden" name="action" value="modifier"><input type="hidden" name="id" id="ea-id">
          <div class="fg2">
            <div class="fg full"><label class="lbl">Nom</label><input class="inp" name="nom" id="ea-nom" required></div>
            <div class="fg"><label class="lbl">Numéro de série</label><input class="inp" name="serial_number" id="ea-serial"></div>
            <div class="fg"><label class="lbl">Statut</label><select class="inp" name="statut" id="ea-statut"><option value="operationnel">Opérationnel</option><option value="maintenance">Maintenance</option><option value="en_panne">En panne</option><option value="hors_service">Hors service</option></select></div>
            <div class="fg"><label class="lbl">Santé (%)</label><input class="inp" name="sante" id="ea-sante" type="number" min="0" max="100"></div>
            <div class="fg full"><label class="lbl">Localisation</label><input class="inp" name="localisation" id="ea-loc"></div>
          </div>
          <button type="submit" class="btn btn-primary btn-block" style="margin-top:12px">Enregistrer les modifications</button>
        </form>
      </div></div>

<?php /* ============ INTERVENTIONS ============ */ elseif($panel==='interventions'): ?>
      <div style="display:flex;justify-content:flex-end;margin-bottom:16px">
        <button class="btn btn-primary" onclick="openModal('add-interv')">+ Planifier une intervention</button>
      </div>
      <?php $ivs=$pdo->query("SELECT i.*,t.titre ttitre,a.nom anom,adm.nom technom FROM interventions i LEFT JOIN tickets t ON i.ticket_id=t.id LEFT JOIN appareils a ON t.appareil_id=a.id LEFT JOIN admins adm ON i.technicien_id=adm.id ORDER BY i.date_debut DESC")->fetchAll(); ?>
      <div class="card"><div class="tbl-wrap"><table id="searchable">
        <thead><tr><th>#</th><th>Ticket / Appareil</th><th>Type</th><th>Technicien</th><th>Date</th><th>Durée</th><th>Statut</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach($ivs as $iv): ?>
        <tr>
          <td style="color:var(--muted)">INT-<?php echo str_pad($iv['id'],3,'0',STR_PAD_LEFT); ?></td>
          <td><strong><?php echo clean($iv['ttitre']??'—'); ?></strong><?php if($iv['anom']): ?><br><span style="font-size:.71rem;color:var(--muted)"><?php echo clean($iv['anom']); ?></span><?php endif; ?></td>
          <td><?php echo clean($iv['type_intervention']??'—'); ?></td>
          <td><?php echo clean($iv['technom']??'Non assigné'); ?></td>
          <td style="color:var(--muted)"><?php echo $iv['date_debut']?date('d/m/Y H:i',strtotime($iv['date_debut'])):'—'; ?></td>
          <td><?php echo clean($iv['duree_estimee']??'—'); ?></td>
          <td><?php echo badgeIntervStatut($iv['statut']); ?></td>
          <td><div style="display:flex;gap:5px">
            <?php if($iv['statut']==='planifiee'): ?><form method="POST" action="process/interv-action.php"><input type="hidden" name="id" value="<?php echo $iv['id']; ?>"><input type="hidden" name="action" value="en_cours"><button class="btn btn-outline btn-sm">▶</button></form><?php endif; ?>
            <?php if($iv['statut']==='en_cours'): ?><form method="POST" action="process/interv-action.php"><input type="hidden" name="id" value="<?php echo $iv['id']; ?>"><input type="hidden" name="action" value="terminer"><button class="btn btn-success-s btn-sm">✓</button></form><?php endif; ?>
            <form method="POST" action="process/interv-action.php" onsubmit="return confirm('Supprimer ?')"><input type="hidden" name="id" value="<?php echo $iv['id']; ?>"><input type="hidden" name="action" value="supprimer"><button class="btn btn-danger-s btn-sm">🗑</button></form>
          </div></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
      </table></div></div>
      <?php $techs=$pdo->query("SELECT * FROM admins WHERE statut='actif' ORDER BY nom")->fetchAll();
      $tkouverts=$pdo->query("SELECT * FROM tickets WHERE statut IN ('ouvert','en_cours') ORDER BY priorite DESC LIMIT 50")->fetchAll(); ?>
      <div class="modal-overlay" id="modal-add-interv"><div class="modal modal-lg">
        <button class="close-btn" onclick="closeModal('add-interv')">✕</button>
        <div class="modal-title">Planifier une intervention</div>
        <form method="POST" action="process/interv-action.php"><input type="hidden" name="action" value="ajouter">
          <div class="fg2">
            <div class="fg"><label class="lbl">Ticket lié</label><select class="inp" name="ticket_id"><option value="">-- Aucun --</option><?php foreach($tkouverts as $t): ?><option value="<?php echo $t['id']; ?>">#<?php echo $t['id']; ?> — <?php echo clean(mb_substr($t['titre'],0,45)); ?></option><?php endforeach; ?></select></div>
            <div class="fg"><label class="lbl">Technicien</label><select class="inp" name="technicien_id"><option value="">-- Sélectionner --</option><?php foreach($techs as $t): ?><option value="<?php echo $t['id']; ?>"><?php echo clean($t['nom']); ?> (<?php echo $t['role']; ?>)</option><?php endforeach; ?></select></div>
            <div class="fg full"><label class="lbl">Type d'intervention</label><input class="inp" name="type_intervention" required placeholder="Ex: Réparation écran, Remplacement batterie…"></div>
            <div class="fg"><label class="lbl">Date de début</label><input class="inp" type="datetime-local" name="date_debut"></div>
            <div class="fg"><label class="lbl">Durée estimée</label><input class="inp" name="duree_estimee" placeholder="Ex: 2h, 45min…"></div>
            <div class="fg full"><label class="lbl">Notes</label><textarea class="inp" name="notes" placeholder="Notes…"></textarea></div>
          </div>
          <button type="submit" class="btn btn-primary btn-block" style="margin-top:12px">Planifier</button>
        </form>
      </div></div>

<?php /* ============ RAPPORTS ============ */ elseif($panel==='rapports'): ?>
      <?php $tot=$gs['t_ouv']+$gs['t_enc']+$gs['t_res'];
      $taux=$tot>0?round($gs['t_res']/$tot*100):0;
      $bycat=$pdo->query("SELECT a.type,COUNT(*) c FROM tickets t JOIN appareils a ON t.appareil_id=a.id GROUP BY a.type ORDER BY c DESC")->fetchAll();
      $bytech=$pdo->query("SELECT adm.nom,COUNT(*) c FROM interventions i JOIN admins adm ON i.technicien_id=adm.id GROUP BY i.technicien_id ORDER BY c DESC LIMIT 6")->fetchAll(); ?>
      <div class="kpi-grid">
        <div class="kpi-box"><div class="kpi-val"><?php echo $taux; ?>%</div><div class="kpi-lbl">Taux de résolution</div></div>
        <div class="kpi-box"><div class="kpi-val" style="color:var(--accent2)"><?php echo $tot; ?></div><div class="kpi-lbl">Tickets total</div></div>
        <div class="kpi-box"><div class="kpi-val" style="color:var(--ok)"><?php echo $gs['t_res']; ?></div><div class="kpi-lbl">Tickets résolus</div></div>
      </div>
      <div class="grid2">
        <div class="card">
          <div class="card-head"><div class="card-title">Tickets par catégorie d'appareil</div></div>
          <?php $tc=array_sum(array_column($bycat,'c'))?:1; foreach($bycat as $c): $pct=round($c['c']/$tc*100); ?>
          <div style="margin-bottom:11px">
            <div style="display:flex;justify-content:space-between;font-size:.82rem;margin-bottom:3px"><span><?php echo typeEmoji($c['type']); ?> <?php echo ucfirst($c['type']); ?></span><span><?php echo $pct; ?>%</span></div>
            <div class="prog-bar"><div class="prog-fill" style="width:<?php echo $pct; ?>%"></div></div>
          </div>
          <?php endforeach; ?>
        </div>
        <div class="card">
          <div class="card-head"><div class="card-title">Interventions par technicien</div></div>
          <div class="tbl-wrap"><table><thead><tr><th>Technicien</th><th>Interventions</th></tr></thead><tbody>
          <?php foreach($bytech as $t): ?><tr><td><?php echo clean($t['nom']); ?></td><td><span class="badge b-blue"><?php echo $t['c']; ?></span></td></tr><?php endforeach; ?>
          </tbody></table></div>
        </div>
      </div>

<?php /* ============ USERS ============ */ elseif($panel==='users'): ?>
      <?php $usrs=$pdo->query("SELECT u.*,(SELECT COUNT(*) FROM tickets WHERE demandeur_id=u.id) nb_t FROM utilisateurs u ORDER BY u.date_inscription DESC")->fetchAll(); ?>
      <div class="card"><div class="tbl-wrap"><table id="searchable">
        <thead><tr><th>Nom</th><th>Email</th><th>Rôle</th><th>Tickets</th><th>Statut</th><th>Inscrit le</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach($usrs as $u): $ui=initiales($u['nom']); ?>
        <tr>
          <td><div style="display:flex;align-items:center;gap:9px"><div class="sb-av" style="width:28px;height:28px;font-size:.68rem"><?php echo $ui; ?></div><strong><?php echo clean($u['nom']); ?></strong></div></td>
          <td style="color:var(--muted)"><?php echo clean($u['email']); ?></td>
          <td><?php echo $u['role']==='etudiant'?'<span class="badge b-blue">Étudiant</span>':'<span class="badge b-cyan">Employé</span>'; ?></td>
          <td><span class="badge b-gray"><?php echo $u['nb_t']; ?></span></td>
          <td><?php echo $u['statut']==='actif'?'<span class="badge b-green">Actif</span>':'<span class="badge b-red">Inactif</span>'; ?></td>
          <td style="color:var(--muted)"><?php echo date('d/m/Y',strtotime($u['date_inscription'])); ?></td>
          <td><form method="POST" action="process/user-action.php"><input type="hidden" name="id" value="<?php echo $u['id']; ?>"><input type="hidden" name="action" value="<?php echo $u['statut']==='actif'?'desactiver':'activer'; ?>"><button class="btn <?php echo $u['statut']==='actif'?'btn-danger-s':'btn-success-s'; ?> btn-sm"><?php echo $u['statut']==='actif'?'Désactiver':'Activer'; ?></button></form></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
      </table></div></div>

<?php /* ============ ADMINS ============ */ elseif($panel==='admins' && $isSA): ?>
      <div class="alert alert-warn">🔐 <span>Cette section est réservée au <strong>Super Administrateur</strong>. Les comptes créés ici ont accès à l'intégralité du dashboard.</span></div>
      <div class="grid2">
        <div>
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px">
            <div style="font-weight:700;font-size:.95rem">Comptes administrateurs</div>
            <button class="btn btn-primary" onclick="openModal('new-admin')">+ Créer un compte admin</button>
          </div>
          <?php $admins=$pdo->query("SELECT * FROM admins ORDER BY date_creation ASC")->fetchAll();
          $rcls=['Super Admin'=>'b-orange','Admin'=>'b-blue','Technicien'=>'b-purple'];
          $rcolors=['Super Admin'=>'linear-gradient(135deg,#dc2626,#f97316)','Admin'=>'linear-gradient(135deg,#10b981,#1a56db)','Technicien'=>'linear-gradient(135deg,#1a56db,#06b6d4)'];
          foreach($admins as $a): $ai2=initiales($a['nom']); ?>
          <div class="admin-card">
            <div class="admin-av" style="background:<?php echo $rcolors[$a['role']]??'var(--accent)'; ?>"><?php echo $ai2; ?></div>
            <div class="admin-info">
              <div class="admin-name"><?php echo clean($a['nom']); ?> <span class="badge <?php echo $rcls[$a['role']]??'b-gray'; ?>" style="margin-left:5px"><?php echo $a['role']; ?></span></div>
              <div class="admin-email"><?php echo clean($a['email']); ?></div>
            </div>
            <?php if($a['role']!=='Super Admin'): ?>
            <div class="admin-actions">
              <form method="POST" action="process/admin-action.php" onsubmit="return confirm('Supprimer ce compte ?')"><input type="hidden" name="id" value="<?php echo $a['id']; ?>"><input type="hidden" name="action" value="supprimer"><button class="btn btn-danger-s btn-sm">🗑 Supprimer</button></form>
            </div>
            <?php else: ?><div style="font-size:.72rem;color:var(--muted)">Compte système</div><?php endif; ?>
          </div>
          <?php endforeach; ?>
        </div>
        <div class="card" style="align-self:start">
          <div class="card-head"><div class="card-title">🔐 Comment ça fonctionne</div></div>
          <div style="display:flex;flex-direction:column;gap:13px;font-size:.84rem">
            <div style="display:flex;gap:10px"><div style="width:26px;height:26px;background:var(--accent-light);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;color:var(--accent);flex-shrink:0">1</div><div><strong>Seul le Super Admin</strong> peut créer des comptes admins et techniciens.</div></div>
            <div style="display:flex;gap:10px"><div style="width:26px;height:26px;background:var(--accent-light);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;color:var(--accent);flex-shrink:0">2</div><div>Le compte créé reçoit ses <strong>identifiants par email</strong>. Mot de passe par défaut : <strong>TechCare@2026</strong></div></div>
            <div style="display:flex;gap:10px"><div style="width:26px;height:26px;background:var(--accent-light);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;color:var(--accent);flex-shrink:0">3</div><div>La page d'accueil publique <strong>n'expose aucune option de rôle admin</strong>.</div></div>
            <div class="alert alert-info" style="font-size:.8rem;margin-bottom:0">💡 Le compte Super Admin est créé une seule fois lors de l'installation.</div>
          </div>
        </div>
      </div>
      <!-- MODAL NOUVEL ADMIN -->
      <div class="modal-overlay" id="modal-new-admin"><div class="modal">
        <button class="close-btn" onclick="closeModal('new-admin')">✕</button>
        <div class="modal-title">🔐 Créer un compte admin</div>
        <div class="modal-sub">Ce compte sera créé par le Super Administrateur. Mot de passe par défaut : TechCare@2026</div>
        <div class="alert alert-warn" style="font-size:.8rem">⚠️ Ce formulaire est accessible uniquement depuis le dashboard administrateur.</div>
        <form method="POST" action="process/admin-action.php"><input type="hidden" name="action" value="ajouter">
          <div class="fg"><label class="lbl">Nom complet</label><input class="inp" name="nom" required placeholder="Prénom Nom"></div>
          <div class="fg"><label class="lbl">Email professionnel</label><input class="inp" type="email" name="email" required placeholder="nom@techcare.com"></div>
          <div class="fg"><label class="lbl">Rôle</label><select class="inp" name="role"><option value="Technicien">Technicien</option><option value="Admin">Administrateur</option></select></div>
          <div class="modal-actions">
            <button type="submit" class="btn btn-primary btn-block">Créer le compte &amp; envoyer les identifiants</button>
            <button type="button" class="btn btn-outline btn-block" onclick="closeModal('new-admin')">Annuler</button>
          </div>
        </form>
      </div></div>

<?php /* ============ TUTORIELS ============ */ elseif($panel==='tutoriels'): ?>
      <div class="alert alert-info" style="margin-bottom:16px">🎓 Espace formation — Contenu réservé aux techniciens et stagiaires.</div>
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:10px">
        <div class="tabs" style="margin-bottom:0">
          <a href="?p=tutoriels" class="tab <?php echo !isset($_GET['n'])?'active':''; ?>">Tous</a>
          <a href="?p=tutoriels&n=debutant" class="tab <?php echo ($_GET['n']??'')==='debutant'?'active':''; ?>">Débutant</a>
          <a href="?p=tutoriels&n=intermediaire" class="tab <?php echo ($_GET['n']??'')==='intermediaire'?'active':''; ?>">Intermédiaire</a>
          <a href="?p=tutoriels&n=avance" class="tab <?php echo ($_GET['n']??'')==='avance'?'active':''; ?>">Avancé</a>
        </div>
        <button class="btn btn-primary" onclick="openModal('add-tuto')">+ Ajouter un tutoriel</button>
      </div>
      <?php $nw='';$np=[];
      if(isset($_GET['n'])&&in_array($_GET['n'],['debutant','intermediaire','avance'])){$nw='WHERE niveau=?';$np[]=$_GET['n'];}
      $tuts=$pdo->prepare("SELECT * FROM tutoriels $nw ORDER BY niveau,titre");$tuts->execute($np);$tutoriels=$tuts->fetchAll();
      $nlbl=['debutant'=>['b-green','Débutant'],'intermediaire'=>['b-blue','Intermédiaire'],'avance'=>['b-red','Avancé']]; ?>
      <div class="vid-grid" id="searchable">
      <?php foreach($tutoriels as $t): $nb=$nlbl[$t['niveau']]??['b-gray',$t['niveau']]; ?>
      <div class="vid-card">
        <div class="vid-thumb"><?php echo $t['emoji']??'🎬'; ?><div class="play-btn">▶</div></div>
        <div class="vid-info">
          <div class="vid-title"><?php echo clean($t['titre']); ?></div>
          <div class="vid-dur">⏱ <?php echo clean($t['duree']??'—'); ?></div>
          <div style="margin-top:5px"><span class="badge <?php echo $nb[0]; ?>"><?php echo $nb[1]; ?></span></div>
          <div style="display:flex;gap:6px;margin-top:8px">
            <form method="POST" action="process/tutoriel-action.php" onsubmit="return confirm('Supprimer ce tutoriel ?')"><input type="hidden" name="id" value="<?php echo $t['id']; ?>"><input type="hidden" name="action" value="supprimer"><button class="btn btn-danger-s btn-sm">🗑</button></form>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
      </div>
      <div class="modal-overlay" id="modal-add-tuto"><div class="modal">
        <button class="close-btn" onclick="closeModal('add-tuto')">✕</button>
        <div class="modal-title">Ajouter un tutoriel</div>
        <form method="POST" action="process/tutoriel-action.php"><input type="hidden" name="action" value="ajouter">
          <div class="fg"><label class="lbl">Titre</label><input class="inp" name="titre" required placeholder="Ex: Remplacement batterie laptop"></div>
          <div class="fg2">
            <div class="fg"><label class="lbl">Niveau</label><select class="inp" name="niveau"><option value="debutant">Débutant</option><option value="intermediaire">Intermédiaire</option><option value="avance">Avancé</option></select></div>
            <div class="fg"><label class="lbl">Durée</label><input class="inp" name="duree" placeholder="Ex: 12:30"></div>
            <div class="fg"><label class="lbl">Emoji</label><input class="inp" name="emoji" placeholder="💻" maxlength="5"></div>
            <div class="fg"><label class="lbl">URL vidéo</label><input class="inp" name="url_video" type="url" placeholder="https://youtube.com/…"></div>
          </div>
          <div class="fg"><label class="lbl">Description</label><textarea class="inp" name="description" placeholder="Décrivez le contenu…"></textarea></div>
          <button type="submit" class="btn btn-primary btn-block" style="margin-top:12px">Ajouter</button>
        </form>
      </div></div>
<?php endif; ?>

    </div>
  </main>
</div>
<script>
function openModal(n){document.getElementById('modal-'+n).classList.add('open');}
function closeModal(n){document.getElementById('modal-'+n).classList.remove('open');}
document.querySelectorAll('.modal-overlay').forEach(o=>{o.addEventListener('click',e=>{if(e.target===o)o.classList.remove('open');});});
function openEditApp(a){
  document.getElementById('ea-id').value=a.id;
  document.getElementById('ea-nom').value=a.nom;
  document.getElementById('ea-serial').value=a.serial_number||'';
  document.getElementById('ea-statut').value=a.statut;
  document.getElementById('ea-sante').value=a.sante;
  document.getElementById('ea-loc').value=a.localisation||'';
  openModal('edit-app');
}
function liveSearch(q){
  q=q.toLowerCase();
  const el=document.getElementById('searchable');
  if(!el)return;
  el.querySelectorAll('tr[data-searchable], .dev-card, .vid-card').forEach(r=>{r.style.display=r.textContent.toLowerCase().includes(q)?'':'none';});
  // For tables - tr
  if(el.tagName==='TABLE'){
    el.querySelectorAll('tbody tr').forEach(r=>{r.style.display=r.textContent.toLowerCase().includes(q)?'':'none';});
  }
}
// Chart
(function(){
  const c=document.getElementById('admin-chart');
  if(!c)return;
  const days=['Lun','Mar','Mer','Jeu','Ven','Sam','Dim'];
  const o=[5,8,6,12,9,3,4],r=[4,6,8,10,11,2,3],m=14;
  c.innerHTML=days.map((d,i)=>`<div class="bar-col"><div class="bar-pair"><div class="bar" style="width:13px;height:${o[i]/m*140}px;background:var(--accent)"></div><div class="bar" style="width:13px;height:${r[i]/m*140}px;background:var(--ok)"></div></div><div class="bar-lbl">${d}</div></div>`).join('');
})();
</script>
</body></html>
