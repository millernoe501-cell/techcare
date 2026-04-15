-- ================================================================
--  techcare.sql  —  Base de données complète TechCare
--  Import : phpMyAdmin > Importer > ce fichier
-- ================================================================
CREATE DATABASE IF NOT EXISTS techcare CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE techcare;

-- ----------------------------------------------------------------
CREATE TABLE utilisateurs (
  id               INT AUTO_INCREMENT PRIMARY KEY,
  nom              VARCHAR(100)  NOT NULL,
  email            VARCHAR(150)  UNIQUE NOT NULL,
  password_hash    VARCHAR(255)  NOT NULL,
  role             ENUM('etudiant','employe') DEFAULT 'etudiant',
  telephone        VARCHAR(30)   DEFAULT NULL,
  departement      VARCHAR(100)  DEFAULT NULL,
  statut           ENUM('actif','inactif') DEFAULT 'actif',
  date_inscription DATETIME      DEFAULT CURRENT_TIMESTAMP
);

-- ----------------------------------------------------------------
CREATE TABLE admins (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  nom           VARCHAR(100) NOT NULL,
  email         VARCHAR(150) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role          ENUM('Super Admin','Admin','Technicien') DEFAULT 'Technicien',
  statut        ENUM('actif','inactif') DEFAULT 'actif',
  date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ----------------------------------------------------------------
CREATE TABLE appareils (
  id               INT AUTO_INCREMENT PRIMARY KEY,
  nom              VARCHAR(120) NOT NULL,
  serial_number    VARCHAR(80)  UNIQUE DEFAULT NULL,
  type             ENUM('laptop','desktop','imprimante','tablette','reseau','autre') DEFAULT 'autre',
  statut           ENUM('operationnel','en_panne','maintenance','hors_service') DEFAULT 'operationnel',
  sante            TINYINT UNSIGNED DEFAULT 100,
  localisation     VARCHAR(150) DEFAULT NULL,
  dernier_controle DATE         DEFAULT NULL,
  date_ajout       DATETIME     DEFAULT CURRENT_TIMESTAMP
);

-- ----------------------------------------------------------------
CREATE TABLE tickets (
  id               INT AUTO_INCREMENT PRIMARY KEY,
  titre            VARCHAR(200) NOT NULL,
  description      TEXT         DEFAULT NULL,
  appareil_id      INT          DEFAULT NULL,
  demandeur_id     INT          DEFAULT NULL,
  nom_public       VARCHAR(100) DEFAULT NULL,
  email_public     VARCHAR(150) DEFAULT NULL,
  service_public   VARCHAR(100) DEFAULT NULL,
  priorite         ENUM('basse','normale','haute','critique') DEFAULT 'normale',
  statut           ENUM('ouvert','en_cours','resolu') DEFAULT 'ouvert',
  date_creation    DATETIME     DEFAULT CURRENT_TIMESTAMP,
  date_resolution  DATETIME     DEFAULT NULL,
  FOREIGN KEY (appareil_id)  REFERENCES appareils(id) ON DELETE SET NULL,
  FOREIGN KEY (demandeur_id) REFERENCES utilisateurs(id) ON DELETE SET NULL
);

-- ----------------------------------------------------------------
CREATE TABLE interventions (
  id                INT AUTO_INCREMENT PRIMARY KEY,
  ticket_id         INT          DEFAULT NULL,
  technicien_id     INT          DEFAULT NULL,
  type_intervention VARCHAR(120) DEFAULT NULL,
  date_debut        DATETIME     DEFAULT NULL,
  duree_estimee     VARCHAR(30)  DEFAULT NULL,
  statut            ENUM('planifiee','en_cours','terminee') DEFAULT 'planifiee',
  notes             TEXT         DEFAULT NULL,
  date_creation     DATETIME     DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (ticket_id)     REFERENCES tickets(id) ON DELETE SET NULL,
  FOREIGN KEY (technicien_id) REFERENCES admins(id) ON DELETE SET NULL
);

-- ----------------------------------------------------------------
CREATE TABLE tutoriels (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  titre       VARCHAR(200) NOT NULL,
  description TEXT         DEFAULT NULL,
  niveau      ENUM('debutant','intermediaire','avance') DEFAULT 'debutant',
  duree       VARCHAR(20)  DEFAULT NULL,
  emoji       VARCHAR(10)  DEFAULT '🎬',
  url_video   VARCHAR(500) DEFAULT NULL,
  date_ajout  DATETIME     DEFAULT CURRENT_TIMESTAMP
);

-- ================================================================
--  DONNÉES DE DÉMONSTRATION
--  Tous les mots de passe = "password"
-- ================================================================

-- Super Admin  (mdp: password)
INSERT INTO admins (nom,email,password_hash,role) VALUES
('Super Admin','superadmin@techcare.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Super Admin'),
('Karim Bah',  'karim@techcare.com',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Technicien'),
('Fatou Diallo','fdiallo@techcare.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Admin');

-- Utilisateurs  (mdp: password)
INSERT INTO utilisateurs (nom,email,password_hash,role,telephone,departement) VALUES
('Kader Diallo',   'kader@email.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','etudiant','+226 70 11 22 33','Informatique L3'),
('Fatou Ouedraogo','fatou@email.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','employe', '+226 70 44 55 66','Direction RH');

-- Appareils
INSERT INTO appareils (nom,serial_number,type,statut,sante,localisation,dernier_controle) VALUES
('HP ProBook 450 G9',      'CND3420XKP', 'laptop',    'en_panne',    20,'Salle 204 Bat A','2026-03-10'),
('Dell OptiPlex 7090',     'DXYZ8820',   'desktop',   'operationnel',91,'Direction RH',   '2026-03-20'),
('HP LaserJet Pro M404',   'VNBZ2201',   'imprimante','maintenance',  55,'Secretariat',    '2026-03-15'),
('Samsung Galaxy Tab A8',  'R58T9102',   'tablette',  'hors_service',  5,'Commercial',     '2026-02-28'),
('Cisco Switch 2960',      'FOC1234XYZ', 'reseau',    'operationnel', 78,'Salle serveurs', '2026-03-22'),
('Lenovo ThinkCentre M80q','LNV004512',  'desktop',   'operationnel', 85,'Bureau Compta',  '2026-03-18');

-- Tickets
INSERT INTO tickets (titre,description,appareil_id,demandeur_id,priorite,statut) VALUES
('Ecran noir HP ProBook 450',      'Ecran reste noir au demarrage.',          1,1,'critique','en_cours'),
('Imprimante reseau hors ligne',   'L imprimante ne repond plus sur le reseau.',3,2,'haute',  'ouvert'),
('Tablette Samsung vitre brisee',  'Vitre fissuree suite a une chute.',        4,1,'normale', 'ouvert'),
('PC ne demarre plus',             'Le bureau de la DG ne s allume plus.',     2,2,'critique','en_cours'),
('Souris bluetooth non detectee',  'Souris non reconnue par le PC.',           NULL,1,'basse','resolu');

-- Interventions
INSERT INTO interventions (ticket_id,technicien_id,type_intervention,date_debut,duree_estimee,statut) VALUES
(1,2,'Reparation ecran',     '2026-03-25 09:00:00','2h',    'en_cours'),
(2,3,'Config reseau',        '2026-03-25 11:30:00','1h30',  'planifiee'),
(4,2,'Recuperation donnees', '2026-03-25 14:00:00','3h',    'planifiee'),
(3,3,'Changement toner',     '2026-03-24 10:00:00','45min', 'terminee');

-- Tutoriels
INSERT INTO tutoriels (titre,description,niveau,duree,emoji) VALUES
('Demontage & nettoyage laptop',    'Apprendre a demonter un ordinateur portable.',   'debutant',      '12:30','💻'),
('Depannage imprimantes reseau',    'Diagnostiquer les imprimantes reseau.',           'intermediaire', '18:10','🖨️'),
('Recuperation donnees SSD/HDD',    'Methodes avancees de recuperation de donnees.',  'avance',        '25:00','💾'),
('Configuration switches Cisco',    'Configurer les equipements reseau Cisco.',        'avance',        '32:45','📡'),
('Remplacement batterie laptop',    'Changer la batterie d un portable facilement.',  'debutant',      '08:20','🔋'),
('Securite & antivirus entreprise', 'Politique de securite informatique en entreprise.','intermediaire','20:55','🛡️');
