-- ============================================================
-- Campus Connect - University Events & Club Management System
-- Database schema (MySQL / XAMPP / phpMyAdmin)
-- ============================================================

CREATE DATABASE IF NOT EXISTS campus_connect
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE campus_connect;

-- ----------------------------------------------------------
-- Unified Users Table (Students & Admins combined)
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
  user_id        VARCHAR(50)   NOT NULL PRIMARY KEY,
  role           ENUM('student', 'admin') NOT NULL DEFAULT 'student',
  full_name      VARCHAR(100)  NOT NULL,
  email          VARCHAR(150)  NULL,
  password_hash  VARCHAR(255)  NOT NULL,
  created_at     TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at     TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ----------------------------------------------------------
-- Events
--   status: pending  -> waiting for admin approval (just added / just edited)
--           approved -> visible to everyone on the public events page
--           rejected -> admin declined it
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS events (
  event_id     INT AUTO_INCREMENT PRIMARY KEY,
  title        VARCHAR(150)  NOT NULL,
  description  TEXT          NULL,
  category     VARCHAR(50)   NOT NULL DEFAULT 'Workshop',
  event_date   DATE          NOT NULL,
  start_time   TIME          NOT NULL,
  end_time     TIME          NULL,
  location     VARCHAR(150)  NOT NULL,
  capacity     INT           NULL,
  organizer    VARCHAR(150)  NULL,
  status       ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  created_by   VARCHAR(50)   NOT NULL,
  approved_by  VARCHAR(50)   NULL,
  created_at   TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at   TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_events_creator FOREIGN KEY (created_by)  REFERENCES users(user_id) ON DELETE CASCADE,
  CONSTRAINT fk_events_approver FOREIGN KEY (approved_by) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ----------------------------------------------------------
-- Event registrations (a student reserving a seat at an event)
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS registrations (
  registration_id  INT AUTO_INCREMENT PRIMARY KEY,
  event_id         INT          NOT NULL,
  user_id          VARCHAR(50)  NOT NULL,
  registered_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_registration (event_id, user_id),
  CONSTRAINT fk_reg_event   FOREIGN KEY (event_id)   REFERENCES events(event_id)     ON DELETE CASCADE,
  CONSTRAINT fk_reg_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ----------------------------------------------------------
-- Announcements (basic, stretch goal)
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS announcements (
  announcement_id  INT AUTO_INCREMENT PRIMARY KEY,
  title            VARCHAR(150) NOT NULL,
  message          TEXT         NOT NULL,
  posted_by        VARCHAR(50)  NOT NULL,
  created_at       TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_announce_user FOREIGN KEY (posted_by) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ----------------------------------------------------------
-- Seed an admin account manually so you can log in immediately.
--   user_id: Admin/001
--   password: Admin@123   (CHANGE THIS after first login!)
-- The hash below was generated with PHP's password_hash() (bcrypt)
-- and works with PHP's password_verify() out of the box.
-- ----------------------------------------------------------
INSERT INTO users (user_id, role, full_name, email, password_hash)
VALUES ('Admin/001', 'admin', 'System Administrator', 'admin@campus.local', '$2b$10$kr1JosmQdpI9xw/4c0vHROqGBtSRJXLNnCglbxrI6lCTkSy.ub.Sm')
ON DUPLICATE KEY UPDATE user_id = user_id;
