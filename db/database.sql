
DROP TABLE IF EXISTS `{{prefix}}ovoform_extensions`;
CREATE TABLE `{{prefix}}ovoform_extensions` (
  `id` bigint(20) UNSIGNED NOT NULL  AUTO_INCREMENT,
  `act` varchar(40)  DEFAULT NULL,
  `name` varchar(40)  DEFAULT NULL,
  `description` text  DEFAULT NULL,
  `image` varchar(255)  DEFAULT NULL,
  `script` text  DEFAULT NULL,
  `shortcode` text  DEFAULT NULL COMMENT 'object',
  `support` text  DEFAULT NULL COMMENT 'help section',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=>enable, 2=>disable',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) {{collate}};

DROP TABLE IF EXISTS `{{prefix}}ovoform_forms`;
CREATE TABLE `{{prefix}}ovoform_forms` (
  `id` bigint(20) UNSIGNED NOT NULL  AUTO_INCREMENT,
  `act` varchar(40)  DEFAULT NULL,
  `form_data` text  DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) {{collate}};

DROP TABLE IF EXISTS `{{prefix}}ovoform_form_infos`;
CREATE TABLE `{{prefix}}ovoform_form_infos` (
  `id` bigint(20) UNSIGNED NOT NULL  AUTO_INCREMENT,
  `form_id` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `captcha_required` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `name` varchar(40)  DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
){{collate}};

DROP TABLE IF EXISTS `{{prefix}}ovoform_submit_forms`;
CREATE TABLE `{{prefix}}ovoform_submit_forms` (
  `id` bigint(20) UNSIGNED NOT NULL  AUTO_INCREMENT,
  `form_info_id` int(11) DEFAULT 0,
  `is_viewed` int(11) DEFAULT 0,
  `form_data` text  DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) {{collate}};



INSERT INTO `{{prefix}}ovoform_extensions` (`id`, `act`, `name`, `description`, `image`, `script`, `shortcode`, `support`, `status`, `created_at`, `updated_at`) VALUES
(1, 'google-recaptcha2', 'Google Recaptcha 2', 'Key location is shown bellow', 'recaptcha3.png', '\n<script src=\"https://www.google.com/recaptcha/api.js\"></script>\n<div class=\"g-recaptcha\" data-sitekey=\"{{site_key}}\" data-callback=\"verifyCaptcha\"></div>\n<div id=\"g-recaptcha-error\"></div>', '{\"site_key\":{\"title\":\"Site Key\",\"value\":\"-----------\"},\"secret_key\":{\"title\":\"Secret Key\",\"value\":\"--------\"}}', 'recaptcha.png', 1, '2019-10-18 11:16:05', '2023-01-14 22:28:49')