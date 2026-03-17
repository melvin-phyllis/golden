-- ============================================================
-- Installation des tables minimales pour le projet Golden
-- À exécuter dans phpMyAdmin (base "management")
-- ============================================================

-- 1. Table des rôles
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom_role` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Insérer le rôle Admin
INSERT IGNORE INTO `roles` (`id`, `nom_role`) VALUES (1, 'Admin');

-- 3. Table des utilisateurs
CREATE TABLE IF NOT EXISTS `utilisateurs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL DEFAULT 1,
  `salaire_base` decimal(12,2) DEFAULT 0,
  `planning_horaire` varchar(255) DEFAULT NULL,
  `statut` varchar(50) DEFAULT 'Actif',
  `photo` varchar(500) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Table des logs d'activité
CREATE TABLE IF NOT EXISTS `logs_activite` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int(11) NOT NULL,
  `action` text NOT NULL,
  `date_action` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `adresse_ip` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `utilisateur_id` (`utilisateur_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Types de chambre (Suite, Standard, etc.)
CREATE TABLE IF NOT EXISTS `types_chambre` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(100) NOT NULL,
  `tarif_nuit` decimal(12,2) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
INSERT IGNORE INTO `types_chambre` (`id`, `libelle`, `tarif_nuit`) VALUES (1, 'Suite', 150000), (2, 'Standard', 75000);

-- 6. Chambres
CREATE TABLE IF NOT EXISTS `chambres` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero_chambre` varchar(20) NOT NULL,
  `type_id` int(11) NOT NULL,
  `statut` varchar(50) DEFAULT 'Libre',
  PRIMARY KEY (`id`),
  UNIQUE KEY `numero_chambre` (`numero_chambre`),
  KEY `type_id` (`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Clients
CREATE TABLE IF NOT EXISTS `clients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom_complet` varchar(255) NOT NULL,
  `telephone` varchar(50) DEFAULT NULL,
  `type_piece` varchar(50) DEFAULT NULL,
  `num_piece` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. Réservations (hébergement)
CREATE TABLE IF NOT EXISTS `reservations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `chambre_id` int(11) NOT NULL,
  `date_arrivee` date NOT NULL,
  `date_depart` date NOT NULL,
  `statut` varchar(50) DEFAULT 'Réservée',
  `preferences` text DEFAULT NULL,
  `type_piece` varchar(50) DEFAULT NULL,
  `num_piece` varchar(100) DEFAULT NULL,
  `accueil_service` varchar(255) DEFAULT NULL,
  `confort_sommeil` varchar(255) DEFAULT NULL,
  `montant_total` decimal(12,2) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `chambre_id` (`chambre_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 9. Salles (événements)
CREATE TABLE IF NOT EXISTS `salles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom_salle` varchar(255) NOT NULL,
  `type_salle` varchar(100) DEFAULT NULL,
  `capacite` int(11) DEFAULT 0,
  `tarif_heure` decimal(12,2) DEFAULT 0,
  `tarif_jour` decimal(12,2) DEFAULT 0,
  `image_salle` varchar(255) DEFAULT 'default_salle.jpg',
  `statut` varchar(50) DEFAULT 'Disponible',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 10. Réservations de salles
CREATE TABLE IF NOT EXISTS `reservations_salles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `salle_id` int(11) NOT NULL,
  `nom_client` varchar(255) NOT NULL,
  `date_reservation` date NOT NULL,
  `heure_debut` time DEFAULT NULL,
  `heure_fin` time DEFAULT NULL,
  `option_restauration` tinyint(1) DEFAULT 0,
  `option_equipement` tinyint(1) DEFAULT 0,
  `montant_total` decimal(12,2) DEFAULT 0,
  `type_piece` varchar(50) DEFAULT NULL,
  `num_piece` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `salle_id` (`salle_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 11. Dépenses (finances)
CREATE TABLE IF NOT EXISTS `depenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) DEFAULT NULL,
  `categorie` varchar(100) DEFAULT NULL,
  `sous_categorie` varchar(100) DEFAULT NULL,
  `montant` decimal(12,2) NOT NULL DEFAULT 0,
  `date_depense` date NOT NULL,
  `justificatif` varchar(500) DEFAULT NULL,
  `cree_par` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cree_par` (`cree_par`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 12. Articles menu (restaurant / bar)
CREATE TABLE IF NOT EXISTS `menu_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom_article` varchar(255) NOT NULL,
  `categorie` varchar(100) DEFAULT NULL,
  `prix_unitaire` decimal(12,2) NOT NULL DEFAULT 0,
  `image_url` varchar(500) DEFAULT NULL,
  `stock_actuel` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 13. Historique des mouvements de stock
CREATE TABLE IF NOT EXISTS `stocks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `article_id` int(11) NOT NULL,
  `type_mouvement` varchar(50) NOT NULL,
  `quantite` int(11) NOT NULL DEFAULT 0,
  `motif` varchar(255) DEFAULT NULL,
  `date_mouvement` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `article_id` (`article_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 14. Équipe (personnel maintenance)
CREATE TABLE IF NOT EXISTS `equipe_prestige` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom_complet` varchar(255) NOT NULL,
  `fonction` varchar(100) DEFAULT NULL,
  `telephone` varchar(50) DEFAULT NULL,
  `statut_emploi` varchar(50) DEFAULT 'Actif',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 15. Présences (pointage)
CREATE TABLE IF NOT EXISTS `equipe_presences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employe_id` int(11) NOT NULL,
  `date_jour` date NOT NULL,
  `statut_presence` varchar(50) DEFAULT NULL,
  `heure_arrivee` time DEFAULT NULL,
  `heure_depart` time DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employe_date` (`employe_id`,`date_jour`),
  KEY `employe_id` (`employe_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 16. Maintenance (pannes / signalements)
CREATE TABLE IF NOT EXISTS `maintenance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `chambre_id` int(11) DEFAULT NULL,
  `description_probleme` text NOT NULL,
  `image_preuve` varchar(255) DEFAULT NULL,
  `priorite` varchar(50) DEFAULT 'Moyenne',
  `statut` varchar(50) DEFAULT 'A faire',
  `date_signalement` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_resolution` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chambre_id` (`chambre_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 17. Demandes d'accès (manager)
CREATE TABLE IF NOT EXISTS `demandes_acces` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `poste` varchar(100) DEFAULT NULL,
  `date_demande` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 18. Commandes (restaurant / bar)
CREATE TABLE IF NOT EXISTS `commandes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `table_numero` int(11) DEFAULT 0,
  `chambre_id` int(11) DEFAULT NULL,
  `utilisateur_id` int(11) DEFAULT NULL,
  `total` decimal(12,2) DEFAULT 0,
  `mode_paiement` varchar(50) DEFAULT NULL,
  `date_commande` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `chambre_id` (`chambre_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 19. Paiements (liés aux commandes)
CREATE TABLE IF NOT EXISTS `paiements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reservation_id` int(11) DEFAULT NULL,
  `montant` decimal(12,2) NOT NULL DEFAULT 0,
  `mode_paiement` varchar(50) DEFAULT NULL,
  `date_paiement` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `reservation_id` (`reservation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Rôles supplémentaires (optionnel)
INSERT IGNORE INTO `roles` (`id`, `nom_role`) VALUES (2, 'Réceptionniste'), (3, 'Manager'), (4, 'Comptable'), (5, 'Technicien'), (6, 'Restauration & Bar'), (7, 'Service évènement '), (8, 'Gouvernance '), (9, 'Gérant de stock');

-- ============================================================
-- Après avoir exécuté ce SQL, ouvre dans le navigateur :
-- http://localhost/golden/create_admin.php
-- pour créer le compte admin (email: admin@prestige.local / mot de passe: Admin123!)
-- ============================================================
