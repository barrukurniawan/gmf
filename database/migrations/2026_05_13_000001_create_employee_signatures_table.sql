-- Create employee_signatures table for storing employee signatures
CREATE TABLE IF NOT EXISTS `employee_signatures` (
  `signature_id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `signature_data` TEXT NULL,
  `file_path` VARCHAR(255) NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`signature_id`),
  INDEX `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;