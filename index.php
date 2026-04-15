<?php
require_once 'config.php';
if(isAdmin())    redirect('admin.php');
if(isLoggedIn()) redirect('dashboard.php');
$loginErr    = flash('login_error');
$registerErr = flash('register_error');
$success     = flash('success');
$autoModal   = $_GET['modal'] ?? '';
// Stats dynamiques pour le hero
$st = $pdo->query("SELECT
  (SELECT COUNT(*) FROM appareils) nb_app,
  (SELECT COUNT(*) FROM admins WHERE statut='actif') nb_tech,
  (SELECT IFNULL(ROUND(SUM(statut='resolu')*100/GREATEST(COUNT(*),1)),0) FROM tickets) taux
")->fetch();
?><!DOCTYPE html>
<html lang="fr"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>TechCare — Gestion de Maintenance</title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">
</head><body>

<div id="landing">
  <nav class="nav-land">
    <div class="brand"><div class="brand-dot"></div>TechCare</div>
    <div class="nav-links">
      <a href="#features">Fonctionnalités</a>
      <a href="#report">Signalement</a>
      <a href="#contact">Contact</a>
    </div>
    <div class="nav-btns">
      <button class="btn btn-ghost" onclick="openModal('login')">Connexion</button>
      <button class="btn btn-primary" onclick="openModal('register')">S'inscrire</button>
    </div>
  </nav>

  <section class="hero">
    <div>
      <div class="hero-badge">⚙️ Plateforme de maintenance informatique</div>
      <h1>Vos pannes signalées,<br><span>prises en charge</span> rapidement</h1>
      <p>TechCare permet aux étudiants et employés de signaler leurs problèmes matériels et d'en suivre la résolution en temps réel.</p>
      <div class="hero-btns">
        <button class="btn btn-primary" onclick="openModal('panne-public')">🚨 Signaler une panne</button>
        <button class="btn btn-ghost" onclick="openModal('login')">Accéder à mon espace</button>
      </div>
      <div class="hero-stats">
        <div class="hero-stat"><div class="val"><?php echo $st['nb_app']; ?></div><div class="lbl">Appareils suivis</div></div>
        <div class="hero-stat"><div class="val"><?php echo $st['taux']; ?>%</div><div class="lbl">Taux de résolution</div></div>
        <div class="hero-stat"><div class="val">&lt;2h</div><div class="lbl">Temps de réponse</div></div>
        <div class="hero-stat"><div class="val"><?php echo $st['nb_tech']; ?></div><div class="lbl">Techniciens actifs</div></div>
      </div>
    </div>
  </section>

  <section class="features" id="features">
    <div class="sec-tag">Fonctionnalités</div>
    <div class="sec-title">Tout ce dont vous avez besoin</div>
    <div class="feat-grid">
      <div class="feat-card"><div class="feat-icon">🎫</div><div class="feat-title">Gestion des tickets</div><div class="feat-desc">Créez et suivez vos demandes avec priorisation automatique et assignation aux techniciens.</div></div>
      <div class="feat-card"><div class="feat-icon">💻</div><div class="feat-title">Inventaire des appareils</div><div class="feat-desc">Centralisez tous les équipements avec suivi de santé, numéros de série et historique.</div></div>
      <div class="feat-card"><div class="feat-icon">🔧</div><div class="feat-title">Suivi des interventions</div><div class="feat-desc">Planifiez et suivez chaque intervention avec délais et technicien assigné.</div></div>
      <div class="feat-card"><div class="feat-icon">📊</div><div class="feat-title">Rapports & statistiques</div><div class="feat-desc">Visualisez les performances, taux de résolution et KPIs clés en temps réel.</div></div>
      <div class="feat-card"><div class="feat-icon">🎓</div><div class="feat-title">Formation des techniciens</div><div class="feat-desc">Espace vidéo dédié aux stagiaires et techniciens juniors, organisé par niveau.</div></div>
      <div class="feat-card"><div class="feat-icon">🔔</div><div class="feat-title">Notifications en temps réel</div><div class="feat-desc">Soyez alerté à chaque changement de statut de votre ticket ou intervention.</div></div>
    </div>
  </section>

  <section class="report-sec" id="report">
    <div class="report-text">
      <div class="sec-tag">Simple & rapide</div>
      <h2>Signalez une panne<br>en 3 étapes</h2>
      <p>Pas besoin de compte. Remplissez le formulaire et notre équipe prend en charge votre demande rapidement.</p>
      <button class="btn btn-primary" onclick="openModal('panne-public')">🚨 Signaler maintenant</button>
    </div>
    <div class="report-mock">
      <div class="mock-step"><div class="mock-num">1</div><div class="mock-txt">Décrivez votre appareil et le problème rencontré</div></div>
      <div class="mock-step"><div class="mock-num">2</div><div class="mock-txt">Recevez un numéro de ticket par email</div></div>
      <div class="mock-step"><div class="mock-num">3</div><div class="mock-txt">Suivez l'avancement en temps réel depuis votre espace</div></div>
    </div>
  </section>

  <section class="cta-sec" id="contact">
    <h2>Rejoignez TechCare aujourd'hui</h2>
    <p>Étudiants et employés — créez votre compte gratuitement et suivez vos demandes.</p>
    <button class="btn btn-light" onclick="openModal('register')">Créer un compte gratuitement</button>
  </section>

  <footer class="footer">
    <div class="footer-brand">⚙️ TechCare © 2026</div>
    <div class="footer-links"><a href="#">Confidentialité</a><a href="#">CGU</a><a href="#">Support</a></div>
  </footer>
</div>

<!-- MODAL LOGIN -->
<div class="modal-overlay" id="modal-login">
  <div class="modal">
    <button class="close-btn" onclick="closeModal('login')">✕</button>
    <div class="modal-title">Connexion</div>
    <div class="modal-sub">Accédez à votre espace TechCare</div>
    <?php if($loginErr): ?><div class="alert alert-error">⚠️ <?php echo $loginErr; ?></div><?php endif; ?>
    <?php if($success): ?><div class="alert alert-success">✅ <?php echo $success; ?></div><?php endif; ?>
    <form method="POST" action="process/login.php">
      <div class="fg"><label class="lbl">Email</label><input class="inp" type="email" name="email" placeholder="votre@email.com" required></div>
      <div class="fg"><label class="lbl">Mot de passe</label><input class="inp" type="password" name="password" placeholder="••••••••" required></div>
      <div class="sec-note">🔒 <span>Les comptes administrateurs sont gérés en interne. Si vous êtes technicien ou admin, vos identifiants vous ont été transmis par votre responsable.</span></div>
      <div class="modal-actions"><button type="submit" class="btn btn-primary btn-block">Se connecter</button></div>
      <div class="divider">ou</div>
      <div class="modal-switch">Pas encore de compte ? <a onclick="switchModal('login','register')">S'inscrire</a></div>
    </form>
  </div>
</div>

<!-- MODAL REGISTER -->
<div class="modal-overlay" id="modal-register">
  <div class="modal">
    <button class="close-btn" onclick="closeModal('register')">✕</button>
    <div class="modal-title">Créer un compte</div>
    <div class="modal-sub">Rejoignez TechCare en tant qu'étudiant ou employé</div>
    <?php if($registerErr): ?><div class="alert alert-error">⚠️ <?php echo $registerErr; ?></div><?php endif; ?>
    <form method="POST" action="process/register.php">
      <div class="fg"><label class="lbl">Je suis</label>
        <select class="inp" name="role"><option value="etudiant">🎓 Étudiant(e)</option><option value="employe">💼 Employé(e)</option></select>
      </div>
      <div class="fg"><label class="lbl">Nom complet</label><input class="inp" type="text" name="nom" placeholder="Prénom Nom" required></div>
      <div class="fg"><label class="lbl">Email</label><input class="inp" type="email" name="email" placeholder="votre@email.com" required></div>
      <div class="fg"><label class="lbl">Mot de passe</label><input class="inp" type="password" name="password" placeholder="Minimum 8 caractères" required minlength="8"></div>
      <div class="modal-actions"><button type="submit" class="btn btn-primary btn-block">Créer mon compte</button></div>
      <div class="divider">ou</div>
      <div class="modal-switch">Déjà un compte ? <a onclick="switchModal('register','login')">Se connecter</a></div>
    </form>
  </div>
</div>

<!-- MODAL PANNE PUBLIC -->
<div class="modal-overlay" id="modal-panne-public" style="overflow-y:auto;align-items:flex-start;padding:60px 20px 40px">
  <div class="modal modal-lg">
    <button class="close-btn" onclick="closeModal('panne-public')">✕</button>
    <div class="modal-title">🚨 Signaler une panne</div>
    <div class="modal-sub">Accessible sans compte. Vous recevrez un numéro de ticket par email.</div>
    <form method="POST" action="process/panne-public.php">
      <div class="fg"><label class="lbl">Nom complet</label><input class="inp" type="text" name="nom_public" placeholder="Votre nom" required></div>
      <div class="fg"><label class="lbl">Email de contact</label><input class="inp" type="email" name="email_public" placeholder="email@exemple.com" required></div>
      <div class="fg"><label class="lbl">Service / Département</label><input class="inp" type="text" name="service_public" placeholder="Ex: Direction RH, L2 Informatique…"></div>
      <div class="fg"><label class="lbl">Type d'appareil</label>
        <select class="inp" name="type_appareil">
          <option value="">Sélectionner…</option>
          <option value="laptop">Ordinateur portable</option>
          <option value="desktop">Ordinateur de bureau</option>
          <option value="imprimante">Imprimante</option>
          <option value="tablette">Tablette / Smartphone</option>
          <option value="reseau">Équipement réseau</option>
          <option value="autre">Autre</option>
        </select>
      </div>
      <div class="fg"><label class="lbl">Priorité</label>
        <select class="inp" name="priorite">
          <option value="normale">Normale</option>
          <option value="haute">Haute</option>
          <option value="critique">Critique — urgence</option>
        </select>
      </div>
      <div class="fg"><label class="lbl">Description du problème</label><textarea class="inp" name="description" placeholder="Décrivez le problème…" required></textarea></div>
      <div class="modal-actions"><button type="submit" class="btn btn-primary btn-block">📨 Envoyer le signalement</button></div>
    </form>
  </div>
</div>

<script>
function openModal(n){document.getElementById('modal-'+n).classList.add('open');}
function closeModal(n){document.getElementById('modal-'+n).classList.remove('open');}
function switchModal(a,b){closeModal(a);openModal(b);}
document.querySelectorAll('.modal-overlay').forEach(o=>{o.addEventListener('click',e=>{if(e.target===o)o.classList.remove('open');});});
<?php if($autoModal): ?>document.addEventListener('DOMContentLoaded',()=>openModal('<?php echo htmlspecialchars($autoModal); ?>'));<?php endif; ?>
</script>
</body></html>
