-- ============================================================
--  Contact Manager — MySQL Schema
--  Run this on Digital Ocean droplet:
--  mysql -u root -p < schema.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS contact_manager
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE contact_manager;

-- ── USERS ────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
  user_id       INT          NOT NULL AUTO_INCREMENT,
  username      VARCHAR(50)  NOT NULL UNIQUE,
  email         VARCHAR(100) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,          -- bcrypt via password_hash()
  created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id)
) ENGINE=InnoDB;

-- ── CONTACTS ─────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS contacts (
  contact_id  INT          NOT NULL AUTO_INCREMENT,
  user_id     INT          NOT NULL,            -- FK → users (private per user)
  first_name  VARCHAR(50)  NOT NULL,
  last_name   VARCHAR(50)  NOT NULL,
  email       VARCHAR(100) DEFAULT NULL,
  phone       VARCHAR(20)  DEFAULT NULL,
  notes       TEXT         DEFAULT NULL,
  created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
                           ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (contact_id),
  CONSTRAINT fk_contacts_user
    FOREIGN KEY (user_id) REFERENCES users(user_id)
    ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── INDEXES for fast search ───────────────────────────────────
CREATE INDEX idx_contacts_user   ON contacts(user_id);
CREATE INDEX idx_contacts_name   ON contacts(first_name, last_name);
CREATE INDEX idx_contacts_email  ON contacts(email);
