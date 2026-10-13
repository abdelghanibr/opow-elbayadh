-- =====================================================================
--  Ajout de la colonne clubs.activity_id  (MySQL / MariaDB)
--  Correspond a database/migrations/2026_09_29_010000_add_activity_id_to_clubs_table.php
--  Executer manuellement sur la base de PRODUCTION (phpMyAdmin / cPanel).
--  La migration Laravel existe deja en prod et est probablement marquee
--  comme executee dans la table `migrations` : `php artisan migrate` ne
--  lancera donc rien. C'est pourquoi on applique l'ALTER directement.
-- =====================================================================

-- ---------------------------------------------------------------------
-- ETAPE 1 - Verification (lecture seule, sans risque)
-- Doit renvoyer 0 lignes si la colonne est deja presente.
-- ---------------------------------------------------------------------
SELECT COUNT(*) AS activity_id_column_exists
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME   = 'clubs'
  AND COLUMN_NAME  = 'activity_id';

-- ---------------------------------------------------------------------
-- ETAPE 2 - Verification de la table ciblee de la cle etrangere
-- `ClubDossierController` valide `exists:activities,id`, donc la table
-- `activities` doit exister en prod.
-- ---------------------------------------------------------------------
SELECT COUNT(*) AS activities_table_exists
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME   = 'activities';

-- ---------------------------------------------------------------------
-- ETAPE 3 - L'ALTER
-- Ne lancer que si ETAPE 1 a renvoye 0 ET ETAPE 2 a renvoye 1.
-- ---------------------------------------------------------------------
ALTER TABLE `clubs`
    ADD COLUMN `activity_id` BIGINT UNSIGNED NULL DEFAULT NULL
    COMMENT 'النشاط الرياضي الوحيد للنادي'
    AFTER `user_id`;

-- ---------------------------------------------------------------------
-- ETAPE 4 - Index (recommande : le modele utilise belongsTo + requetes)
-- A lancer seulement si l'index n'existe pas encore (verifier avec SHOW
-- INDEX FROM clubs LIKE 'activity_id'  ->  sinon ignorer cette section).
-- ---------------------------------------------------------------------
-- ALTER TABLE `clubs` ADD INDEX `clubs_activity_id_index` (`activity_id`);

-- ---------------------------------------------------------------------
-- ETAPE 5 - Verification finale
-- ---------------------------------------------------------------------
DESCRIBE `clubs`;

-- ---------------------------------------------------------------------
-- ANNULATION (au cas ou)
-- ---------------------------------------------------------------------
-- ALTER TABLE `clubs` DROP INDEX `clubs_activity_id_index`;
-- ALTER TABLE `clubs` DROP COLUMN `activity_id`;