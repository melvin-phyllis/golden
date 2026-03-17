# Golden – Système de gestion hôtelière

Application PHP (réception, réservations, chambres, salles, finances, stock, maintenance, restaurant & bar). Compatible déploiement sur **cPanel** ou hébergement mutualisé.

---

## Prérequis

- **PHP 8.1** ou supérieur (extensions : PDO MySQL, mbstring, dom, json)
- **MySQL 5.7** / MariaDB 10.x
- **Composer** (pour les dépendances)

---

## Déploiement sur cPanel (étape par étape)

### 1. Envoyer les fichiers sur le serveur

- **Option A – Git** (si le dépôt est sur GitHub/GitLab) :

```bash
cd ~/public_html
git clone https://github.com/TON_USER/TON_REPO.git golden
cd golden
```

- **Option B – FTP / Gestionnaire de fichiers cPanel**  
  Envoyer tout le projet dans un dossier, par exemple `public_html/golden/` (ou à la racine du domaine si le site est dédié).

---

### 2. Créer la base de données MySQL

1. Dans cPanel → **Bases de données MySQL** (MySQL® Databases).
2. Créer une **nouvelle base** (ex. `cpanel_user_management`).
3. Créer un **utilisateur MySQL** avec un mot de passe fort.
4. **Associer** l’utilisateur à la base en lui accordant **tous les privilèges** (ALL PRIVILEGES).
5. Noter : **nom de la base**, **utilisateur**, **mot de passe** (souvent préfixés par cPanel, ex. `cpanel_user_management`).

---

### 3. Configurer la connexion à la base

Le projet lit les identifiants dans `config/db.local.php` (fichier non versionné).

```bash
cd ~/public_html/golden
cp config/db.local.php.example config/db.local.php
```

Éditer `config/db.local.php` avec les identifiants fournis par cPanel :

```php
<?php
$host     = 'localhost';           // Souvent "localhost" sur cPanel
$dbname   = 'cpanel_user_management';  // Nom exact de la base
$username = 'cpanel_user_db';       // Utilisateur MySQL
$password = 'TonMotDePasseIci';
```

---

### 4. Définir l’URL de base (si le site est dans un sous-dossier)

Si l’application est dans `public_html/golden/`, l’URL du site sera `https://tondomaine.com/golden/`.

**Option A – Variable d’environnement (recommandé)**  
Dans cPanel → **Variables d’environnement** (ou via `.htaccess` si disponible), ajouter :

- Nom : `BASE_URL`  
- Valeur : `/golden/`

**Option B – Fichier de config**  
Éditer `config/paths.php` et, avant la ligne `define('BASE_URL', ...)`, forcer la valeur :

```php
$base = '/golden/';  // Remplacer par ton sous-dossier
```

(Si le site est à la racine du domaine, ne rien changer : `BASE_URL` reste `/`.)

---

### 5. Choisir la version de PHP

1. cPanel → **Sélecteur de version PHP** (Select PHP Version) ou **MultiPHP Manager**.
2. Sélectionner le répertoire du site (ex. `public_html/golden`).
3. Choisir **PHP 8.1** (ou 8.2 / 8.3).

---

### 6. Installer les dépendances Composer

En SSH, à la racine du projet :

```bash
cd ~/public_html/golden
composer install --no-dev
```

Si SSH n’est pas disponible : uploader un dossier `vendor/` déjà généré en local avec `composer install --no-dev`, ou demander à l’hébergeur d’exécuter cette commande.

---

### 7. Exécuter les migrations (créer les tables)

**Option A – Script PHP (recommandé, utilise `config/db.local.php`)**  

Aucun mot de passe à taper : le script lit les identifiants dans `config/db.local.php`.

```bash
cd ~/public_html/golden
php migrate.php
```

**Option B – Ligne de commande MySQL** :

```bash
cd ~/public_html/golden
mysql -h localhost -u Cpanel_User_Db -p Cpanel_User_Management < install_tables.sql
```

(Remplacer par les noms réels. Le mot de passe sera demandé.)

**Option C – phpMyAdmin** (sans SSH) :

1. cPanel → **phpMyAdmin**.
2. Sélectionner la base créée à l’étape 2.
3. Onglet **Importer** (Import).
4. Choisir le fichier `install_tables.sql` du projet, puis **Exécuter**.

---

### 8. Créer le premier compte administrateur

Les tables sont vides : il faut créer un utilisateur admin pour se connecter.

**Option A – Script PHP (recommandé, utilise `config/db.local.php`)**  

Aucun mot de passe à taper. Le script crée un admin avec **admin@ya-consulting.com** / **Admin123!**.

```bash
cd ~/public_html/golden
php create_admin.php
```

Puis supprimer le fichier : `rm create_admin.php`

**Option B – Ligne de commande MySQL**  

Générer le hash (guillemets simples pour éviter les soucis avec `!` en bash) :

```bash
php -r 'echo password_hash("Admin123!", PASSWORD_DEFAULT);'
```

Puis insérer l’admin (remplacer USER, BASE et HASH) :

```bash
mysql -h localhost -u USER -p BASE -e "INSERT INTO utilisateurs (nom, email, mot_de_passe, role_id, statut) VALUES ('Administrateur', 'admin@tondomaine.com', 'HASH', 1, 'Actif');"
```

**Option C – phpMyAdmin**  

1. Générer le hash en SSH avec la commande `php -r "..."` ci-dessus (ou sur une machine locale en PHP 8.1+).
2. Dans phpMyAdmin, ouvrir la base → table **`utilisateurs`** → onglet **Insérer**.
3. Renseigner :  
   - `nom` : Administrateur  
   - `email` : ton@email.com  
   - `mot_de_passe` : le hash généré  
   - `role_id` : 1  
   - `statut` : Actif  
4. Exécuter.

Ensuite, connexion sur **https://tondomaine.com/golden/login.php** avec cet email et le mot de passe en clair (ex. `Admin123!`).

---

### 9. Droits sur les dossiers

Certains dossiers doivent être écrivables par le serveur web (uploads, backups, assets).

En SSH :

```bash
cd ~/public_html/golden
mkdir -p backups uploads uploads/profiles uploads/factures uploads/maintenance
chmod -R 755 backups uploads
chmod -R 755 assets
```

Sur cPanel, si les uploads échouent, passer les dossiers concernés en **755** (ou **775** selon l’hébergeur) via le Gestionnaire de fichiers → Propriétés du dossier.

---

### 10. Vérifications finales

| Élément | À vérifier |
|--------|------------|
| **Connexion** | Ouvrir `https://tondomaine.com/golden/login.php` et se connecter avec le compte admin. |
| **Pas de 404** | Les liens du menu (Réception, Admin, etc.) doivent rester sous `/golden/...`. |
| **PHP** | En cas d’erreur 500, consulter les logs (cPanel → Erreurs) et confirmer PHP 8.1+. |
| **Sécurité** | Ne jamais committer `config/db.local.php` ; il est déjà dans `.gitignore`. |

---

## Récapitulatif des commandes (déploiement cPanel)

À adapter avec tes noms de base et d’utilisateur.

```bash
# 1. Aller dans le projet
cd ~/public_html/golden

# 2. Config BDD
cp config/db.local.php.example config/db.local.php
# Éditer config/db.local.php avec les identifiants cPanel

# 3. Composer
composer install --no-dev

# 4. Migrations (utilise config/db.local.php)
php migrate.php

# 5. Premier admin (remplacer HASH par le résultat de la commande ci-dessous)
# php -r "echo password_hash('Admin123!', PASSWORD_DEFAULT);"
mysql -h localhost -u TON_USER -p TON_NOM_BASE -e "INSERT INTO utilisateurs (nom, email, mot_de_passe, role_id, statut) VALUES ('Admin', 'admin@email.com', 'HASH', 1, 'Actif');"

# 6. Dossiers éditables
mkdir -p backups uploads uploads/profiles uploads/factures uploads/maintenance
chmod -R 755 backups uploads assets
```

---

## Structure du projet (principaux dossiers)

```
golden/
├── auth/              # Connexion, réinitialisation mot de passe
├── config/            # db.php, paths.php, session (ne pas exposer db.local.php)
├── modules/
│   ├── admin/         # Utilisateurs, logs, sauvegardes
│   ├── reception/     # Dashboard, chambres, réservations
│   ├── maintenance/   # Salles, personnel, pannes
│   ├── finances/      # Dépenses, rapports
│   ├── conciergerie/  # Restaurant & bar
│   ├── manager/       # Demandes d’accès
│   ├── stock/         # Mouvements de stock
│   └── layout/        # En-tête / pied de page
├── assets/            # Images (chambres, salles)
├── backups/           # Sauvegardes SQL (protégé par .htaccess)
├── uploads/           # Fichiers uploadés (profiles, factures, maintenance)
├── install_tables.sql # Script de création des tables
├── index.php          # Redirection vers login
├── login.php
├── logout.php
└── composer.json
```

---

## Test en local (XAMPP)

1. Démarrer **Apache** et **MySQL** dans XAMPP.
2. Créer une base `management` dans phpMyAdmin (ou en ligne de commande).
3. Exécuter les migrations :  
   `mysql -u root -D management < install_tables.sql`
4. Créer un admin (voir section 8 ci-dessus, ou utiliser un script local équivalent).
5. Ouvrir **http://localhost/golden/login.php** et se connecter.

En local, `config/db.local.php` est optionnel : en son absence, le projet utilise `root` sans mot de passe et la base `management`.

---

## Sécurité

- **config/** et **backups/** sont protégés par `.htaccess` (accès HTTP refusé).
- Ne jamais versionner `config/db.local.php` ni un fichier `.env` contenant des mots de passe.
- En production, garder PHP à jour et limiter les accès (IP, HTTPS, etc.) selon les moyens de l’hébergeur.
