CREATE DATABASE IF NOT EXISTS banza_site
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE banza_site;

CREATE TABLE IF NOT EXISTS admins (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS pages (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(120) NOT NULL UNIQUE,
  title VARCHAR(255) NOT NULL,
  excerpt TEXT NULL,
  body MEDIUMTEXT NOT NULL,
  hero_image VARCHAR(255) NULL,
  status ENUM('draft', 'published') NOT NULL DEFAULT 'draft',
  post_date CHAR(10) NULL,
  last_update CHAR(10) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS posts (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  type ENUM('news', 'project', 'team') NOT NULL,
  slug VARCHAR(160) NOT NULL UNIQUE,
  title VARCHAR(255) NOT NULL,
  excerpt TEXT NULL,
  body MEDIUMTEXT NOT NULL,
  main_image VARCHAR(255) NULL,
  published_at DATETIME NULL,
  is_featured TINYINT(1) NOT NULL DEFAULT 0,
  view_count INT UNSIGNED NOT NULL DEFAULT 0,
  status ENUM('draft', 'published') NOT NULL DEFAULT 'draft',
  post_date CHAR(10) NULL,
  last_update CHAR(10) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at TIMESTAMP NULL DEFAULT NULL,
  INDEX idx_posts_type_status_deleted (type, status, deleted_at),
  INDEX idx_posts_published_at (published_at)
);

CREATE TABLE IF NOT EXISTS media (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  source_key VARCHAR(190) NULL UNIQUE,
  post_id INT UNSIGNED NULL,
  file_path VARCHAR(255) NOT NULL,
  alt_text VARCHAR(255) NULL,
  media_type ENUM('image', 'youtube') NOT NULL DEFAULT 'image',
  youtube_url VARCHAR(255) NULL,
  sort_order INT UNSIGNED NOT NULL DEFAULT 0,
  post_date CHAR(10) NULL,
  last_update CHAR(10) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at TIMESTAMP NULL DEFAULT NULL,
  CONSTRAINT fk_media_post FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE SET NULL,
  INDEX idx_media_post_deleted (post_id, deleted_at)
);

CREATE TABLE IF NOT EXISTS settings (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  setting_key VARCHAR(120) NOT NULL UNIQUE,
  setting_value TEXT NULL,
  post_date CHAR(10) NULL,
  last_update CHAR(10) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS social_links (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  source_key VARCHAR(190) NULL UNIQUE,
  label VARCHAR(80) NOT NULL,
  url VARCHAR(255) NOT NULL,
  icon VARCHAR(80) NULL,
  sort_order INT UNSIGNED NOT NULL DEFAULT 0,
  post_date CHAR(10) NULL,
  last_update CHAR(10) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS donation_accounts (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  source_key VARCHAR(190) NULL UNIQUE,
  bank_name VARCHAR(120) NOT NULL,
  account_number VARCHAR(80) NOT NULL,
  account_holder VARCHAR(160) NULL,
  note VARCHAR(255) NULL,
  sort_order INT UNSIGNED NOT NULL DEFAULT 0,
  post_date CHAR(10) NULL,
  last_update CHAR(10) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS contact_messages (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(160) NOT NULL UNIQUE,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL,
  phone VARCHAR(60) NULL,
  subject VARCHAR(160) NOT NULL,
  message TEXT NOT NULL,
  read_at DATETIME NULL,
  post_date CHAR(10) NULL,
  last_update CHAR(10) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at TIMESTAMP NULL DEFAULT NULL,
  INDEX idx_contact_messages_deleted_created (deleted_at, created_at),
  INDEX idx_contact_messages_read (read_at)
);

DELIMITER //

DROP TRIGGER IF EXISTS pages_dates_bi//
CREATE TRIGGER pages_dates_bi BEFORE INSERT ON pages
FOR EACH ROW
BEGIN
  IF NEW.post_date IS NULL OR NEW.post_date = '' THEN SET NEW.post_date = DATE_FORMAT(CURRENT_DATE(), '%d/%m/%Y'); END IF;
  SET NEW.last_update = DATE_FORMAT(CURRENT_DATE(), '%d/%m/%Y');
END//

DROP TRIGGER IF EXISTS pages_dates_bu//
CREATE TRIGGER pages_dates_bu BEFORE UPDATE ON pages
FOR EACH ROW
BEGIN
  SET NEW.last_update = DATE_FORMAT(CURRENT_DATE(), '%d/%m/%Y');
END//

DROP TRIGGER IF EXISTS posts_dates_bi//
CREATE TRIGGER posts_dates_bi BEFORE INSERT ON posts
FOR EACH ROW
BEGIN
  IF NEW.post_date IS NULL OR NEW.post_date = '' THEN SET NEW.post_date = DATE_FORMAT(CURRENT_DATE(), '%d/%m/%Y'); END IF;
  SET NEW.last_update = DATE_FORMAT(CURRENT_DATE(), '%d/%m/%Y');
END//

DROP TRIGGER IF EXISTS posts_dates_bu//
CREATE TRIGGER posts_dates_bu BEFORE UPDATE ON posts
FOR EACH ROW
BEGIN
  SET NEW.last_update = DATE_FORMAT(CURRENT_DATE(), '%d/%m/%Y');
END//

DROP TRIGGER IF EXISTS media_dates_bi//
CREATE TRIGGER media_dates_bi BEFORE INSERT ON media
FOR EACH ROW
BEGIN
  IF NEW.post_date IS NULL OR NEW.post_date = '' THEN SET NEW.post_date = DATE_FORMAT(CURRENT_DATE(), '%d/%m/%Y'); END IF;
  SET NEW.last_update = DATE_FORMAT(CURRENT_DATE(), '%d/%m/%Y');
END//

DROP TRIGGER IF EXISTS media_dates_bu//
CREATE TRIGGER media_dates_bu BEFORE UPDATE ON media
FOR EACH ROW
BEGIN
  SET NEW.last_update = DATE_FORMAT(CURRENT_DATE(), '%d/%m/%Y');
END//

DROP TRIGGER IF EXISTS settings_dates_bi//
CREATE TRIGGER settings_dates_bi BEFORE INSERT ON settings
FOR EACH ROW
BEGIN
  IF NEW.post_date IS NULL OR NEW.post_date = '' THEN SET NEW.post_date = DATE_FORMAT(CURRENT_DATE(), '%d/%m/%Y'); END IF;
  SET NEW.last_update = DATE_FORMAT(CURRENT_DATE(), '%d/%m/%Y');
END//

DROP TRIGGER IF EXISTS settings_dates_bu//
CREATE TRIGGER settings_dates_bu BEFORE UPDATE ON settings
FOR EACH ROW
BEGIN
  SET NEW.last_update = DATE_FORMAT(CURRENT_DATE(), '%d/%m/%Y');
END//

DROP TRIGGER IF EXISTS social_links_dates_bi//
CREATE TRIGGER social_links_dates_bi BEFORE INSERT ON social_links
FOR EACH ROW
BEGIN
  IF NEW.post_date IS NULL OR NEW.post_date = '' THEN SET NEW.post_date = DATE_FORMAT(CURRENT_DATE(), '%d/%m/%Y'); END IF;
  SET NEW.last_update = DATE_FORMAT(CURRENT_DATE(), '%d/%m/%Y');
END//

DROP TRIGGER IF EXISTS social_links_dates_bu//
CREATE TRIGGER social_links_dates_bu BEFORE UPDATE ON social_links
FOR EACH ROW
BEGIN
  SET NEW.last_update = DATE_FORMAT(CURRENT_DATE(), '%d/%m/%Y');
END//

DROP TRIGGER IF EXISTS donation_accounts_dates_bi//
CREATE TRIGGER donation_accounts_dates_bi BEFORE INSERT ON donation_accounts
FOR EACH ROW
BEGIN
  IF NEW.post_date IS NULL OR NEW.post_date = '' THEN SET NEW.post_date = DATE_FORMAT(CURRENT_DATE(), '%d/%m/%Y'); END IF;
  SET NEW.last_update = DATE_FORMAT(CURRENT_DATE(), '%d/%m/%Y');
END//

DROP TRIGGER IF EXISTS donation_accounts_dates_bu//
CREATE TRIGGER donation_accounts_dates_bu BEFORE UPDATE ON donation_accounts
FOR EACH ROW
BEGIN
  SET NEW.last_update = DATE_FORMAT(CURRENT_DATE(), '%d/%m/%Y');
END//

DROP TRIGGER IF EXISTS contact_messages_dates_bi//
CREATE TRIGGER contact_messages_dates_bi BEFORE INSERT ON contact_messages
FOR EACH ROW
BEGIN
  IF NEW.post_date IS NULL OR NEW.post_date = '' THEN SET NEW.post_date = DATE_FORMAT(CURRENT_DATE(), '%d/%m/%Y'); END IF;
  SET NEW.last_update = DATE_FORMAT(CURRENT_DATE(), '%d/%m/%Y');
END//

DROP TRIGGER IF EXISTS contact_messages_dates_bu//
CREATE TRIGGER contact_messages_dates_bu BEFORE UPDATE ON contact_messages
FOR EACH ROW
BEGIN
  SET NEW.last_update = DATE_FORMAT(CURRENT_DATE(), '%d/%m/%Y');
END//

DELIMITER ;
