CREATE DATABASE IF NOT EXISTS cavibe_waitlist CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cavibe_waitlist;
CREATE TABLE IF NOT EXISTS student_survey_responses(
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, public_id CHAR(36) NOT NULL UNIQUE,
 full_name VARCHAR(120) NOT NULL,email VARCHAR(190) NOT NULL UNIQUE,phone VARCHAR(30) NOT NULL,
 school VARCHAR(190) NOT NULL,campus_location VARCHAR(190),student_level VARCHAR(60) NOT NULL,
 primary_goal VARCHAR(200) NOT NULL,biggest_problem TEXT NOT NULL,willing_to_test VARCHAR(10) NOT NULL,
 preferred_contact VARCHAR(30),referral_code VARCHAR(50),consent TINYINT(1) NOT NULL DEFAULT 0,
 marketing_consent TINYINT(1) NOT NULL DEFAULT 0,source VARCHAR(80),created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 KEY idx_school(school),KEY idx_created(created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS student_survey_answers(
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,response_public_id CHAR(36) NOT NULL,
 section_code VARCHAR(10) NOT NULL,question_key VARCHAR(100) NOT NULL,answer_json JSON,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,UNIQUE KEY uq_answer(response_public_id,question_key),
 KEY idx_response(response_public_id),KEY idx_question(question_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
