-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 13, 2023 at 04:49 AM
-- Server version: 8.0.32
-- PHP Version: 8.0.28

SET
SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET
time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `infy-project`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log`
(
    `id`           bigint UNSIGNED NOT NULL,
    `log_name`     varchar(161) COLLATE utf8mb4_unicode_ci                       DEFAULT NULL,
    `description`  text COLLATE utf8mb4_unicode_ci NOT NULL,
    `subject_type` varchar(161) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `event`        varchar(255) COLLATE utf8mb4_unicode_ci                       DEFAULT NULL,
    `subject_id`   bigint UNSIGNED DEFAULT NULL,
    `causer_type`  varchar(161) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `causer_id`    bigint UNSIGNED DEFAULT NULL,
    `properties`   longtext COLLATE utf8mb4_unicode_ci,
    `batch_uuid`   char(36) COLLATE utf8mb4_unicode_ci                           DEFAULT NULL,
    `created_at`   timestamp NULL DEFAULT NULL,
    `updated_at`   timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `log_name`, `description`, `subject_type`, `event`, `subject_id`, `causer_type`,
                            `causer_id`, `properties`, `batch_uuid`, `created_at`, `updated_at`)
VALUES (1, 'New Client created.', 'New Client Mr created.', 'App\\Models\\Client', NULL, 1, 'App\\Models\\User', 1,
        '{\"modal\":\"App\\\\Models\\\\Client\",\"data\":\"\"}', NULL, '2023-06-12 23:14:48', '2023-06-12 23:14:48'),
       (2, 'New Client created.', 'New Client clinets created.', 'App\\Models\\Client', NULL, 2, 'App\\Models\\User', 1,
        '{\"modal\":\"App\\\\Models\\\\Client\",\"data\":\"\"}', NULL, '2023-06-12 23:16:26', '2023-06-12 23:16:26'),
       (3, 'Project Assign To User', 'Assigned restaurant management to InfyTracker Admin', 'App\\Models\\Project',
        NULL, 1, 'App\\Models\\User', 1, '{\"modal\":\"App\\\\Models\\\\Project\",\"data\":\"\"}', NULL,
        '2023-06-12 23:17:22', '2023-06-12 23:17:22'),
       (4, 'Project Created', 'Created project', 'App\\Models\\Project', NULL, 1, 'App\\Models\\User', 1,
        '{\"modal\":\"App\\\\Models\\\\Project\",\"data\":\" restaurant management\"}', NULL, '2023-06-12 23:17:22',
        '2023-06-12 23:17:22'),
       (5, 'Project Updated', 'Updated Project', 'App\\Models\\Project', NULL, 1, 'App\\Models\\User', 1,
        '{\"modal\":\"App\\\\Models\\\\Project\",\"data\":\" restaurant management\"}', NULL, '2023-06-12 23:18:16',
        '2023-06-12 23:18:16'),
       (6, 'Task Created', 'Created new task fdsdsfs', 'App\\Models\\Project', NULL, 1, 'App\\Models\\User', 2,
        '{\"modal\":\"App\\\\Models\\\\Task\",\"data\":\"of restaurant management\"}', NULL, '2023-06-12 23:18:36',
        '2023-06-12 23:18:36');

-- --------------------------------------------------------

--
-- Table structure for table `activity_types`
--

CREATE TABLE `activity_types`
(
    `id`         int UNSIGNED NOT NULL,
    `name`       varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `created_by` int UNSIGNED DEFAULT NULL,
    `deleted_by` int UNSIGNED DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_types`
--

INSERT INTO `activity_types` (`id`, `name`, `created_by`, `deleted_by`, `created_at`, `updated_at`, `deleted_at`)
VALUES (1, 'Development', NULL, NULL, '2023-06-12 23:11:23', '2023-06-12 23:11:23', NULL),
       (2, 'Management', NULL, NULL, '2023-06-12 23:11:23', '2023-06-12 23:11:23', NULL),
       (3, 'Code Review', NULL, NULL, '2023-06-12 23:11:23', '2023-06-12 23:11:23', NULL),
       (4, 'Testing', NULL, NULL, '2023-06-12 23:11:23', '2023-06-12 23:11:23', NULL),
       (5, 'Documentation', NULL, NULL, '2023-06-12 23:11:23', '2023-06-12 23:11:23', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients`
(
    `id`            int UNSIGNED NOT NULL,
    `name`          varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `email`         varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `website`       varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `created_by`    int UNSIGNED DEFAULT NULL,
    `deleted_by`    int UNSIGNED DEFAULT NULL,
    `created_at`    timestamp NULL DEFAULT NULL,
    `updated_at`    timestamp NULL DEFAULT NULL,
    `deleted_at`    timestamp NULL DEFAULT NULL,
    `department_id` bigint UNSIGNED DEFAULT NULL,
    `user_id`       int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `name`, `email`, `website`, `created_by`, `deleted_by`, `created_at`, `updated_at`,
                       `deleted_at`, `department_id`, `user_id`)
VALUES (1, 'Mr', 'client@infyprojects.com', '', 1, NULL, '2023-06-12 23:14:48', '2023-06-12 23:15:21', NULL, 3, 2),
       (2, 'clinets', 'clinet@gmail.com', '', 1, NULL, '2023-06-12 23:16:26', '2023-06-12 23:16:26', NULL, 4, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments`
(
    `id`         int UNSIGNED NOT NULL,
    `comment`    text COLLATE utf8mb4_unicode_ci NOT NULL,
    `task_id`    int UNSIGNED NOT NULL,
    `created_by` int UNSIGNED NOT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments`
(
    `id`          bigint UNSIGNED NOT NULL,
    `name`        varchar(170) COLLATE utf8mb4_unicode_ci NOT NULL,
    `description` text COLLATE utf8mb4_unicode_ci,
    `color`       varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `created_at`  timestamp NULL DEFAULT NULL,
    `updated_at`  timestamp NULL DEFAULT NULL,
    `deleted_at`  timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `description`, `color`, `created_at`, `updated_at`, `deleted_at`)
VALUES (1, 'Web Development', '<p>My Department is best department</p>', '#f2c852', '2023-06-12 23:11:23',
        '2023-06-12 23:11:23', NULL),
       (2, 'Web Designing', '<p>My Department is best department</p>', '#f26d52', '2023-06-12 23:11:23',
        '2023-06-12 23:11:23', NULL),
       (3, 'Android Development', '<p>My Department is best department</p>', '#52f28f', '2023-06-12 23:11:23',
        '2023-06-12 23:11:23', NULL),
       (4, 'IOS Development', '<p>My Department is best department</p>', '#52d7f2', '2023-06-12 23:11:23',
        '2023-06-12 23:11:23', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events`
(
    `id`          bigint UNSIGNED NOT NULL,
    `title`       varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `description` text COLLATE utf8mb4_unicode_ci,
    `start_date`  datetime                                DEFAULT NULL,
    `end_date`    datetime                                DEFAULT NULL,
    `type`        bigint UNSIGNED DEFAULT NULL,
    `added_by`    int UNSIGNED DEFAULT NULL,
    `created_at`  timestamp NULL DEFAULT NULL,
    `updated_at`  timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses`
(
    `id`          bigint UNSIGNED NOT NULL,
    `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
    `amount`      double                          NOT NULL,
    `date`        datetime                        NOT NULL,
    `project_id`  int UNSIGNED NOT NULL,
    `client_id`   int UNSIGNED NOT NULL,
    `category`    int                             NOT NULL DEFAULT '1',
    `billable`    tinyint(1) NOT NULL DEFAULT '0',
    `created_by`  int UNSIGNED DEFAULT NULL,
    `deleted_by`  int UNSIGNED DEFAULT NULL,
    `created_at`  timestamp NULL DEFAULT NULL,
    `updated_at`  timestamp NULL DEFAULT NULL,
    `deleted_at`  timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs`
(
    `id`         bigint UNSIGNED NOT NULL,
    `uuid`       varchar(161) COLLATE utf8mb4_unicode_ci      DEFAULT NULL,
    `connection` text COLLATE utf8mb4_unicode_ci     NOT NULL,
    `queue`      text COLLATE utf8mb4_unicode_ci     NOT NULL,
    `payload`    longtext COLLATE utf8mb4_unicode_ci NOT NULL,
    `exception`  longtext COLLATE utf8mb4_unicode_ci NOT NULL,
    `failed_at`  timestamp                           NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices`
(
    `id`             int UNSIGNED NOT NULL,
    `name`           varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `invoice_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `issue_date`     date                                    NOT NULL,
    `due_date`       date                                             DEFAULT NULL,
    `total_hour`     varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `discount`       double                                           DEFAULT NULL,
    `tax_id`         int UNSIGNED DEFAULT NULL,
    `status`         int                                     NOT NULL,
    `amount`         double                                  NOT NULL,
    `sub_total`      double                                  NOT NULL,
    `notes`          text COLLATE utf8mb4_unicode_ci,
    `created_by`     int UNSIGNED NOT NULL,
    `created_at`     timestamp NULL DEFAULT NULL,
    `updated_at`     timestamp NULL DEFAULT NULL,
    `deleted_at`     timestamp NULL DEFAULT NULL,
    `discount_type`  int                                     NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_clients`
--

CREATE TABLE `invoice_clients`
(
    `id`         int UNSIGNED NOT NULL,
    `invoice_id` int UNSIGNED NOT NULL,
    `client_id`  int UNSIGNED NOT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_items`
--

CREATE TABLE `invoice_items`
(
    `id`              int UNSIGNED NOT NULL,
    `owner_id`        int                                     NOT NULL,
    `owner_type`      varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `item_name`       varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `task_id`         int UNSIGNED DEFAULT NULL,
    `item_project_id` int UNSIGNED DEFAULT NULL,
    `hours`           varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `task_amount`     double DEFAULT NULL,
    `fix_rate`        double DEFAULT NULL,
    `description`     text COLLATE utf8mb4_unicode_ci,
    `created_at`      timestamp NULL DEFAULT NULL,
    `updated_at`      timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_projects`
--

CREATE TABLE `invoice_projects`
(
    `id`         int UNSIGNED NOT NULL,
    `invoice_id` int UNSIGNED NOT NULL,
    `project_id` int UNSIGNED NOT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE `media`
(
    `id`                    bigint UNSIGNED NOT NULL,
    `model_type`            varchar(161) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `model_id`              bigint UNSIGNED NOT NULL,
    `collection_name`       varchar(161) COLLATE utf8mb4_unicode_ci                       NOT NULL,
    `name`                  varchar(161) COLLATE utf8mb4_unicode_ci                       NOT NULL,
    `file_name`             varchar(161) COLLATE utf8mb4_unicode_ci                       NOT NULL,
    `mime_type`             varchar(161) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `disk`                  varchar(161) COLLATE utf8mb4_unicode_ci                       NOT NULL,
    `size`                  bigint UNSIGNED NOT NULL,
    `manipulations`         longtext COLLATE utf8mb4_unicode_ci                           NOT NULL,
    `custom_properties`     longtext COLLATE utf8mb4_unicode_ci                           NOT NULL,
    `responsive_images`     longtext COLLATE utf8mb4_unicode_ci                           NOT NULL,
    `order_column`          int UNSIGNED DEFAULT NULL,
    `created_at`            timestamp NULL DEFAULT NULL,
    `updated_at`            timestamp NULL DEFAULT NULL,
    `conversions_disk`      varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `uuid`                  char(36) COLLATE utf8mb4_unicode_ci     DEFAULT NULL,
    `generated_conversions` text COLLATE utf8mb4_unicode_ci                               NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `media`
--

INSERT INTO `media` (`id`, `model_type`, `model_id`, `collection_name`, `name`, `file_name`, `mime_type`, `disk`,
                     `size`, `manipulations`, `custom_properties`, `responsive_images`, `order_column`, `created_at`,
                     `updated_at`, `conversions_disk`, `uuid`, `generated_conversions`)
VALUES (1, 'App\\Models\\Project', 1, 'attachments', '1686631707124_krhcttdezmy2_1686142809221_w9jwyuuxqo64_download_5',
        '1686631707124_krhcttdezmy2_1686142809221_w9jwyuuxqo64_download_5.jpeg', 'image/jpeg', 'public', 14072, '[]',
        '[]', '[]', 1, '2023-06-12 23:18:27', '2023-06-12 23:18:27', 'public', '9f8e3913-217f-467b-a50e-3bc8803335ba',
        '[]'),
       (2, 'App\\Models\\Project', 1, 'attachments', '1686631707128_lg4qlw3kjmko_download_13',
        '1686631707128_lg4qlw3kjmko_download_13.jpeg', 'image/jpeg', 'public', 4623, '[]', '[]', '[]', 1,
        '2023-06-12 23:18:27', '2023-06-12 23:18:27', 'public', '69215c3a-3137-48ea-9f8c-40352b58d03b', '[]'),
       (3, 'App\\Models\\Project', 1, 'attachments', '1686631707129_oj8u859sr19m_jfif_279',
        '1686631707129_oj8u859sr19m_jfif_279.png', 'image/png', 'public', 183851, '[]', '[]', '[]', 2,
        '2023-06-12 23:18:27', '2023-06-12 23:18:27', 'public', 'd04ca22d-f4d2-4899-abcb-412921c2e836', '[]');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations`
(
    `id`        int UNSIGNED NOT NULL,
    `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `batch`     int                                     NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
                                                          (1, '2014_10_12_000000_create_users_table', 1),
                                                          (2, '2014_10_12_100000_create_password_resets_table', 1),
                                                          (3, '2019_05_02_101439_create_activity_types_table', 1),
                                                          (4, '2019_05_02_101619_create_clients_table', 1),
                                                          (5, '2019_05_03_043336_create_tags_table', 1),
                                                          (6, '2019_05_03_050601_create_projects_table', 1),
                                                          (7, '2019_05_03_060503_create_tasks_table', 1),
                                                          (8, '2019_05_03_072634_create_task_tags_table', 1),
                                                          (9, '2019_05_03_094616_create_time_entries_table', 1),
                                                          (10, '2019_05_22_115729_create_table_task_assignees', 1),
                                                          (11, '2019_06_11_062240_create_project_users_table', 1),
                                                          (12, '2019_06_18_093135_create_table_task_attachment', 1),
                                                          (13, '2019_06_19_045436_create_status_table', 1),
                                                          (14, '2019_06_21_124817_create_comments_table', 1),
                                                          (15, '2019_07_06_121218_create_reports_table', 1),
                                                          (16, '2019_07_08_114940_create_table_report_filters', 1),
                                                          (17, '2019_07_19_055226_populate_project_users_table', 1),
                                                          (18, '2019_08_19_000000_create_failed_jobs_table', 1),
                                                          (19, '2019_09_14_060733_create_permission_tables', 1),
                                                          (20, '2019_10_15_095114_add_salary_in_users', 1),
                                                          (21, '2020_02_19_134502_create_settings_table', 1),
                                                          (22, '2020_04_08_105133_create_departments_table', 1),
                                                          (23, '2020_04_08_115453_add_department_id_to_clients_table', 1),
                                                          (24, '2020_08_06_045725_create_taxes_table', 1),
                                                          (25, '2020_08_06_071644_create_invoices_table', 1),
                                                          (26, '2020_08_07_110611_create_invoice_items_table', 1),
                                                          (27, '2020_08_08_053928_create_media_table', 1),
                                                          (28, '2020_09_21_050626_create_report_invoices_table', 1),
                                                          (29, '2020_10_05_055541_add_discount_apply_field_to_invoice_table', 1),
                                                          (30, '2020_10_05_070445_create_invoice_projects_table', 1),
                                                          (31, '2020_10_05_070527_create_invoice_clients_table', 1),
                                                          (32, '2020_10_09_063902_add_budget_type_to_projects_table', 1),
                                                          (33, '2020_11_06_114032_add_status_feild_to_projects_table', 1),
                                                          (34, '2020_11_20_090538_add_meta_to_reports_table', 1),
                                                          (35, '2020_11_21_083148_add_calender_view_permission_to_permissions_table', 1),
                                                          (36, '2020_11_25_074504_add_group_to_setting_table', 1),
                                                          (37, '2020_12_12_092228_add_user_id_to_clients', 1),
                                                          (38, '2020_12_12_092404_add_owner_id_and_owner_type_to_users', 1),
                                                          (39, '2020_12_15_153246_add_manage_status_permission_to_permissions_table', 1),
                                                          (40, '2021_01_02_043241_remove_soft_deleted_tags_from_tags_table', 1),
                                                          (41, '2021_01_16_112850_create_expenses_table', 1),
                                                          (42, '2021_02_02_091125_change_budget_column_type_in_projects', 1),
                                                          (43, '2021_02_04_085602_remove_task_tag_entry_from_pivot_table', 1),
                                                          (44, '2021_02_12_050509_make_fields_nullable_of_projects_table', 1),
                                                          (45, '2021_02_23_051251_create_activity_log_table', 1),
                                                          (46, '2021_03_24_070000_create_notifications_table', 1),
                                                          (47, '2021_04_12_112648_create_events_table', 1),
                                                          (48, '2021_04_16_084448_add_estimate_field_to_tasks_table',
                                                           1),
                                                          (49, '2021_05_10_112220_create_user_notifications_table', 1),
                                                          (50, '2021_06_29_115255_remove_activity_log_of_users', 1),
                                                          (51, '2021_07_12_000000_add_uuid_to_failed_jobs_table', 1),
                                                          (52,
                                                           '2021_07_1_103036_add_conversions_disk_column_in_media_table',
                                                           1),
                                                          (53, '2021_12_31_041836_add_order_to_status_table', 1),
                                                          (54, '2022_05_19_085808_change_column_length_table', 1),
                                                          (55,
                                                           '2022_09_10_042604_add_google_recaptcha_field_in_settings',
                                                           1),
                                                          (56,
                                                           '2022_09_14_040402_change_amount_field_type_in_expenses_table',
                                                           1),
                                                          (57,
                                                           '2022_12_23_091932_add_event_column_to_activity_log_table',
                                                           1),
                                                          (58,
                                                           '2022_12_23_091933_add_batch_uuid_column_to_activity_log_table',
                                                           1),
                                                          (59,
                                                           '2023_04_01_035635_add_soft_delete_user_permission_to_permissions_table',
                                                           1),
                                                          (60,
                                                           '2023_05_30_104525_assign_task_management_to_client_role',
                                                           1);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions`
(
    `permission_id` int UNSIGNED NOT NULL,
    `model_type`    varchar(170) COLLATE utf8mb4_unicode_ci NOT NULL,
    `model_id`      bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles`
(
    `role_id`    int UNSIGNED NOT NULL,
    `model_type` varchar(170) COLLATE utf8mb4_unicode_ci NOT NULL,
    `model_id`   bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`)
VALUES (1, 'App\\Models\\User', 1),
       (4, 'App\\Models\\User', 2);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications`
(
    `id`                       bigint UNSIGNED NOT NULL,
    `user_id`                  int UNSIGNED NOT NULL,
    `first_notification_hour`  varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `second_notification_hour` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `third_notification_hour`  varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `created_at`               timestamp NULL DEFAULT NULL,
    `updated_at`               timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
                                   `email` varchar(170) COLLATE utf8mb4_unicode_ci NOT NULL,
                                   `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                                   `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions`
(
    `id`           int UNSIGNED NOT NULL,
    `name`         varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `guard_name`   varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `display_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `description`  varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `created_at`   timestamp NULL DEFAULT NULL,
    `updated_at`   timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `display_name`, `description`, `created_at`, `updated_at`)
VALUES (1, 'manage_calendar_view', 'web', 'Manage Calendar View', '<p>Able to access Setting tab.</p>',
        '2023-06-12 23:11:21', '2023-06-12 23:11:21'),
       (2, 'manage_status', 'web', 'Manage Status', '<p>Able to access Status tab.</p>', '2023-06-12 23:11:21',
        '2023-06-12 23:11:21'),
       (3, 'archived_users', 'web', 'Archived Users', NULL, '2023-06-12 23:11:22', '2023-06-12 23:11:22'),
       (4, 'manage_clients', 'web', 'Manage Clients', '<p>Visible clients tab and manage it.</p>',
        '2023-06-12 23:11:22', '2023-06-12 23:11:22'),
       (5, 'manage_projects', 'web', 'Manage Projects', '<p>Project tab visible and manage it.</p>',
        '2023-06-12 23:11:22', '2023-06-12 23:11:22'),
       (6, 'manage_all_tasks', 'web', 'Manage Tasks',
        '<p>All projects list comes into Project filter otherwise comes only related projects.Assignee Filter visible in task module otherwise own assigned and non-assigned.</p>',
        '2023-06-12 23:11:22', '2023-06-12 23:11:22'),
       (7, 'manage_time_entries', 'web', 'Manage Entry', '<p>User can manage own time entry.</p>',
        '2023-06-12 23:11:22', '2023-06-12 23:11:22'),
       (8, 'manage_users', 'web', 'Manage Users', '<p>User tab visible</p>', '2023-06-12 23:11:22',
        '2023-06-12 23:11:22'),
       (9, 'manage_tags', 'web', 'Manage Tags', '<p>Able to access tags tab.</p>', '2023-06-12 23:11:22',
        '2023-06-12 23:11:22'),
       (10, 'manage_activities', 'web', 'Manage Activities', '<p>Able to access Activity tab.</p>',
        '2023-06-12 23:11:22', '2023-06-12 23:11:22'),
       (11, 'manage_reports', 'web', 'Manage Reports', '<p></p>', '2023-06-12 23:11:22', '2023-06-12 23:11:22'),
       (12, 'manage_roles', 'web', 'Manage Roles', '<p></p>', '2023-06-12 23:11:22', '2023-06-12 23:11:22'),
       (13, 'manage_taxes', 'web', 'Manage Taxes', '<p>Able to access Taxes tab.</p>', '2023-06-12 23:11:22',
        '2023-06-12 23:11:22'),
       (14, 'manage_invoices', 'web', 'Manage Invoices', '<p>Able to access Invoices tab.</p>', '2023-06-12 23:11:22',
        '2023-06-12 23:11:22'),
       (15, 'manage_settings', 'web', 'Manage Settings', '<p>Able to access Setting tab.</p>', '2023-06-12 23:11:22',
        '2023-06-12 23:11:22'),
       (16, 'manage_department', 'web', 'Manage Department', NULL, '2023-06-12 23:11:23', '2023-06-12 23:11:23'),
       (17, 'manage_expenses', 'web', 'Manage Expenses', NULL, '2023-06-12 23:11:23', '2023-06-12 23:11:23'),
       (18, 'manage_activity_log', 'web', 'Manage Activity Log', NULL, '2023-06-12 23:11:23', '2023-06-12 23:11:23'),
       (19, 'manage_events', 'web', 'Manage Events', NULL, '2023-06-12 23:11:23', '2023-06-12 23:11:23'),
       (20, 'role_client', 'web', 'Role Client', '<p>Able to access Client Panel.</p>', '2023-06-12 23:11:23',
        '2023-06-12 23:11:23');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects`
(
    `id`          int UNSIGNED NOT NULL,
    `name`        varchar(170) COLLATE utf8mb4_unicode_ci NOT NULL,
    `client_id`   int UNSIGNED DEFAULT NULL,
    `description` text COLLATE utf8mb4_unicode_ci         NOT NULL,
    `price`       bigint                                           DEFAULT NULL,
    `currency`    int                                              DEFAULT NULL,
    `prefix`      varchar(170) COLLATE utf8mb4_unicode_ci NOT NULL,
    `color`       varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `created_by`  int UNSIGNED DEFAULT NULL,
    `deleted_by`  int UNSIGNED DEFAULT NULL,
    `created_at`  timestamp NULL DEFAULT NULL,
    `updated_at`  timestamp NULL DEFAULT NULL,
    `deleted_at`  timestamp NULL DEFAULT NULL,
    `budget_type` int                                              DEFAULT NULL,
    `status`      int                                     NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `name`, `client_id`, `description`, `price`, `currency`, `prefix`, `color`, `created_by`,
                        `deleted_by`, `created_at`, `updated_at`, `deleted_at`, `budget_type`, `status`)
VALUES (1, 'restaurant management', 1, '', 10000, 1, 'RESTAURA', '#3F51B5', 1, NULL, '2023-06-12 23:17:22',
        '2023-06-12 23:18:16', NULL, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `project_user`
--

CREATE TABLE `project_user`
(
    `id`         int UNSIGNED NOT NULL,
    `project_id` int UNSIGNED NOT NULL,
    `user_id`    int UNSIGNED NOT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `project_user`
--

INSERT INTO `project_user` (`id`, `project_id`, `user_id`, `created_at`, `updated_at`)
VALUES (1, 1, 1, '2023-06-12 23:17:22', '2023-06-12 23:17:22');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports`
(
    `id`               int UNSIGNED NOT NULL,
    `name`             varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `owner_id`         int UNSIGNED NOT NULL,
    `start_date`       date                                    NOT NULL,
    `end_date`         date                                    NOT NULL,
    `report_type`      int                                     NOT NULL DEFAULT '1',
    `report_data`      text COLLATE utf8mb4_unicode_ci,
    `invoice_generate` tinyint(1) NOT NULL DEFAULT '0',
    `meta`             text COLLATE utf8mb4_unicode_ci,
    `created_at`       timestamp NULL DEFAULT NULL,
    `updated_at`       timestamp NULL DEFAULT NULL,
    `deleted_at`       timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `report_filters`
--

CREATE TABLE `report_filters`
(
    `id`         bigint UNSIGNED NOT NULL,
    `report_id`  int UNSIGNED NOT NULL,
    `param_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `param_id`   int UNSIGNED NOT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `report_invoices`
--

CREATE TABLE `report_invoices`
(
    `id`         int UNSIGNED NOT NULL,
    `invoice_id` int UNSIGNED NOT NULL,
    `report_id`  int UNSIGNED NOT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles`
(
    `id`           int UNSIGNED NOT NULL,
    `name`         varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `display_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `description`  text COLLATE utf8mb4_unicode_ci,
    `guard_name`   varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `created_at`   timestamp NULL DEFAULT NULL,
    `updated_at`   timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `display_name`, `description`, `guard_name`, `created_at`, `updated_at`)
VALUES (1, 'Admin', 'Admin', '<p>Admin</p>', 'web', '2023-06-12 23:11:22', '2023-06-12 23:11:22'),
       (2, 'Team Member', 'Team Member', '<p>Team Member</p>', 'web', '2023-06-12 23:11:22', '2023-06-12 23:11:22'),
       (3, 'Developer', 'Developer', '<p>Developer</p>', 'web', '2023-06-12 23:11:22', '2023-06-12 23:11:22'),
       (4, 'Client', 'Client', '<p>Client</p>', 'web', '2023-06-12 23:11:23', '2023-06-12 23:11:23');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions`
(
    `permission_id` int UNSIGNED NOT NULL,
    `role_id`       int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
                                                                    (1, 1),
                                                                    (2, 1),
                                                                    (3, 1),
                                                                    (4, 1),
                                                                    (5, 1),
                                                                    (6, 1),
                                                                    (7, 1),
                                                                    (8, 1),
                                                                    (9, 1),
                                                                    (10, 1),
                                                                    (11, 1),
                                                                    (12, 1),
                                                                    (13, 1),
                                                                    (14, 1),
                                                                    (15, 1),
                                                                    (16, 1),
                                                                    (17, 1),
                                                                    (18, 1),
                                                                    (19, 1),
                                                                    (6, 2),
                                                                    (9, 2),
                                                                    (10, 2),
                                                                    (11, 2),
                                                                    (1, 3),
                                                                    (2, 3),
                                                                    (3, 3),
                                                                    (4, 3),
                                                                    (5, 3),
                                                                    (6, 3),
                                                                    (7, 3),
                                                                    (8, 3),
                                                                    (9, 3),
                                                                    (10, 3),
                                                                    (11, 3),
                                                                    (12, 3),
                                                                    (13, 3),
                                                                    (14, 3),
                                                                    (15, 3),
                                                                    (6, 4),
                                                                    (20, 4);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings`
(
    `id`         int UNSIGNED NOT NULL,
    `key`        varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `value`      varchar(255) COLLATE utf8mb4_unicode_ci          DEFAULT NULL,
    `group`      int                                     NOT NULL DEFAULT '1',
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`, `group`, `created_at`, `updated_at`)
VALUES (1, 'show_recaptcha', '0', 3, '2023-06-12 23:11:22', '2023-06-12 23:11:22'),
       (2, 'google_recaptcha_site_key', NULL, 3, '2023-06-12 23:11:22', '2023-06-12 23:11:22'),
       (3, 'google_recaptcha_secret_key', NULL, 3, '2023-06-12 23:11:22', '2023-06-12 23:11:22'),
       (4, 'default_task_status', '0', 1, '2023-06-12 23:11:22', '2023-06-12 23:11:22'),
       (5, 'app_name', 'InfyProject', 1, '2023-06-12 23:11:23', '2023-06-12 23:11:23'),
       (6, 'app_logo', 'assets/img/logo-red-black.png', 1, '2023-06-12 23:11:23', '2023-06-12 23:11:23'),
       (7, 'favicon', 'assets/img/favicon.png', 1, '2023-06-12 23:11:23', '2023-06-12 23:11:23'),
       (8, 'company_name', 'InfyOmLabs', 1, '2023-06-12 23:11:23', '2023-06-12 23:11:23'),
       (9, 'current_currency', 'inr', 1, '2023-06-12 23:11:23', '2023-06-12 23:11:23'),
       (10, 'company_address', '16/A saint Joseph Park', 1, '2023-06-12 23:11:23', '2023-06-12 23:11:23'),
       (11, 'company_email', 'infytracker@gmail.com', 1, '2023-06-12 23:11:23', '2023-06-12 23:11:23'),
       (12, 'company_phone', '1234567890', 1, '2023-06-12 23:11:23', '2023-06-12 23:11:23'),
       (13, 'working_days_of_month', '24', 1, '2023-06-12 23:11:23', '2023-06-12 23:11:23'),
       (14, 'working_hours_of_day', '8', 1, '2023-06-12 23:11:23', '2023-06-12 23:11:23'),
       (15, 'default_invoice_template', 'defaultTemplate', 2, '2023-06-12 23:11:23', '2023-06-12 23:11:23'),
       (16, 'default_invoice_color', '#040404', 2, '2023-06-12 23:11:23', '2023-06-12 23:11:23');

-- --------------------------------------------------------

--
-- Table structure for table `status`
--

CREATE TABLE `status`
(
    `id`         int UNSIGNED NOT NULL,
    `status`     int                                     NOT NULL,
    `name`       varchar(170) COLLATE utf8mb4_unicode_ci NOT NULL,
    `order`      double DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `status`
--

INSERT INTO `status` (`id`, `status`, `name`, `order`, `created_at`, `updated_at`)
VALUES (1, 0, 'Pending', NULL, '2023-06-12 23:11:23', '2023-06-12 23:11:23'),
       (2, 1, 'Completed', NULL, '2023-06-12 23:11:23', '2023-06-12 23:11:23');

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags`
(
    `id`         int UNSIGNED NOT NULL,
    `name`       varchar(170) COLLATE utf8mb4_unicode_ci NOT NULL,
    `created_by` int UNSIGNED DEFAULT NULL,
    `deleted_by` int UNSIGNED DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks`
(
    `id`                 int UNSIGNED NOT NULL,
    `priority`           varchar(255) COLLATE utf8mb4_unicode_ci          DEFAULT NULL,
    `title`              varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `description`        text COLLATE utf8mb4_unicode_ci         NOT NULL,
    `project_id`         int UNSIGNED NOT NULL,
    `status`             int                                     NOT NULL DEFAULT '0',
    `due_date`           date                                             DEFAULT NULL,
    `completed_on`       date                                             DEFAULT NULL,
    `task_number`        int UNSIGNED NOT NULL,
    `created_by`         int UNSIGNED DEFAULT NULL,
    `deleted_by`         int UNSIGNED DEFAULT NULL,
    `created_at`         timestamp NULL DEFAULT NULL,
    `updated_at`         timestamp NULL DEFAULT NULL,
    `deleted_at`         timestamp NULL DEFAULT NULL,
    `estimate_time`      varchar(255) COLLATE utf8mb4_unicode_ci          DEFAULT NULL,
    `estimate_time_type` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `priority`, `title`, `description`, `project_id`, `status`, `due_date`, `completed_on`,
                     `task_number`, `created_by`, `deleted_by`, `created_at`, `updated_at`, `deleted_at`,
                     `estimate_time`, `estimate_time_type`)
VALUES (1, NULL, 'fdsdsfs', '', 1, 1, NULL, NULL, 1, 2, NULL, '2023-06-12 23:18:36', '2023-06-12 23:18:38', NULL, NULL,
        NULL);

-- --------------------------------------------------------

--
-- Table structure for table `task_assignees`
--

CREATE TABLE `task_assignees`
(
    `id`      bigint UNSIGNED NOT NULL,
    `task_id` int UNSIGNED NOT NULL,
    `user_id` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `task_assignees`
--

INSERT INTO `task_assignees` (`id`, `task_id`, `user_id`)
VALUES (1, 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `task_attachments`
--

CREATE TABLE `task_attachments`
(
    `id`         int UNSIGNED NOT NULL,
    `task_id`    int UNSIGNED NOT NULL,
    `file`       varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `task_tags`
--

CREATE TABLE `task_tags`
(
    `id`      int UNSIGNED NOT NULL,
    `task_id` int UNSIGNED NOT NULL,
    `tag_id`  int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `taxes`
--

CREATE TABLE `taxes`
(
    `id`         int UNSIGNED NOT NULL,
    `name`       varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `tax`        double                                  NOT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `taxes`
--

INSERT INTO `taxes` (`id`, `name`, `tax`, `created_at`, `updated_at`)
VALUES (1, '5% of your taxable income', 5, '2023-06-12 23:11:23', '2023-06-12 23:11:23'),
       (2, '10% of your taxable salary', 10, '2023-06-12 23:11:23', '2023-06-12 23:11:23'),
       (3, '15% of your taxable expense', 15, '2023-06-12 23:11:23', '2023-06-12 23:11:23');

-- --------------------------------------------------------

--
-- Table structure for table `time_entries`
--

CREATE TABLE `time_entries`
(
    `id`               int UNSIGNED NOT NULL,
    `task_id`          int UNSIGNED NOT NULL,
    `activity_type_id` int UNSIGNED NOT NULL,
    `user_id`          int UNSIGNED NOT NULL,
    `start_time`       varchar(255) COLLATE utf8mb4_unicode_ci  DEFAULT NULL,
    `end_time`         varchar(255) COLLATE utf8mb4_unicode_ci  DEFAULT NULL,
    `duration`         int                             NOT NULL,
    `entry_type`       int                             NOT NULL DEFAULT '1',
    `note`             text COLLATE utf8mb4_unicode_ci NOT NULL,
    `deleted_by`       int UNSIGNED DEFAULT NULL,
    `created_at`       timestamp NULL DEFAULT NULL,
    `updated_at`       timestamp NULL DEFAULT NULL,
    `deleted_at`       timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users`
(
    `id`                int UNSIGNED NOT NULL,
    `name`              varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `email`             varchar(170) COLLATE utf8mb4_unicode_ci NOT NULL,
    `phone`             varchar(255) COLLATE utf8mb4_unicode_ci          DEFAULT NULL,
    `email_verified_at` timestamp NULL DEFAULT NULL,
    `password`          varchar(255) COLLATE utf8mb4_unicode_ci          DEFAULT NULL,
    `set_password`      tinyint(1) NOT NULL DEFAULT '0',
    `is_email_verified` tinyint(1) NOT NULL DEFAULT '0',
    `is_active`         tinyint(1) NOT NULL DEFAULT '0',
    `activation_code`   varchar(255) COLLATE utf8mb4_unicode_ci          DEFAULT NULL,
    `salary`            varchar(255) COLLATE utf8mb4_unicode_ci          DEFAULT NULL,
    `created_by`        int UNSIGNED DEFAULT NULL,
    `deleted_by`        int UNSIGNED DEFAULT NULL,
    `remember_token`    varchar(100) COLLATE utf8mb4_unicode_ci          DEFAULT NULL,
    `language`          varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
    `image_path`        varchar(255) COLLATE utf8mb4_unicode_ci          DEFAULT NULL,
    `created_at`        timestamp NULL DEFAULT NULL,
    `updated_at`        timestamp NULL DEFAULT NULL,
    `deleted_at`        timestamp NULL DEFAULT NULL,
    `owner_id`          int                                              DEFAULT NULL,
    `owner_type`        varchar(255) COLLATE utf8mb4_unicode_ci          DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `email_verified_at`, `password`, `set_password`,
                     `is_email_verified`, `is_active`, `activation_code`, `salary`, `created_by`, `deleted_by`,
                     `remember_token`, `language`, `image_path`, `created_at`, `updated_at`, `deleted_at`, `owner_id`,
                     `owner_type`)
VALUES (1, 'InfyTracker Admin', 'admin@infyprojects.com', NULL, '2023-06-12 23:11:23',
        '$2y$10$oFvjs16vgQrE40czzRrYP.TKfRvKlrtIZYgMvFfg24/LPq0Pzc096', 1, 1, 1, NULL, NULL, NULL, NULL, NULL, 'en',
        NULL, '2023-06-12 23:11:22', '2023-06-12 23:11:23', NULL, NULL, NULL),
       (2, 'Mr', 'client@infyprojects.com', NULL, '2023-06-12 23:15:21',
        '$2y$10$BzJdYML/ZIaHjNSlEw762eU9kvOmo.vWSZSSFx84bhe263UhjuIXq', 1, 1, 1, NULL, NULL, NULL, NULL, NULL, 'en',
        NULL, '2023-06-12 23:15:21', '2023-06-12 23:15:21', NULL, 1, 'App\\Models\\Client');

-- --------------------------------------------------------

--
-- Table structure for table `user_notifications`
--

CREATE TABLE `user_notifications`
(
    `id`          bigint UNSIGNED NOT NULL,
    `title`       varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `type`        varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `description` text COLLATE utf8mb4_unicode_ci,
    `read_at`     timestamp NULL DEFAULT NULL,
    `user_id`     int UNSIGNED DEFAULT NULL,
    `created_at`  timestamp NULL DEFAULT NULL,
    `updated_at`  timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_notifications`
--

INSERT INTO `user_notifications` (`id`, `title`, `type`, `description`, `read_at`, `user_id`, `created_at`,
                                  `updated_at`)
VALUES (1, 'New Project Assigned', 'App\\Models\\Project', 'You are assigned to restaurant management', NULL, 1,
        '2023-06-12 23:17:22', '2023-06-12 23:17:22');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
    ADD PRIMARY KEY (`id`),
  ADD KEY `subject` (`subject_type`,`subject_id`),
  ADD KEY `causer` (`causer_type`,`causer_id`),
  ADD KEY `activity_log_log_name_index` (`log_name`);

--
-- Indexes for table `activity_types`
--
ALTER TABLE `activity_types`
    ADD PRIMARY KEY (`id`),
  ADD KEY `activity_types_created_by_foreign` (`created_by`),
  ADD KEY `activity_types_deleted_by_foreign` (`deleted_by`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
    ADD PRIMARY KEY (`id`),
  ADD KEY `clients_created_by_foreign` (`created_by`),
  ADD KEY `clients_deleted_by_foreign` (`deleted_by`),
  ADD KEY `clients_department_id_foreign` (`department_id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
    ADD PRIMARY KEY (`id`),
  ADD KEY `comments_created_by_foreign` (`created_by`),
  ADD KEY `comments_task_id_foreign` (`task_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
    ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `departments_name_unique` (`name`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
    ADD PRIMARY KEY (`id`),
  ADD KEY `events_added_by_foreign` (`added_by`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
    ADD PRIMARY KEY (`id`),
  ADD KEY `expenses_client_id_foreign` (`client_id`),
  ADD KEY `expenses_project_id_foreign` (`project_id`),
  ADD KEY `expenses_created_by_foreign` (`created_by`),
  ADD KEY `expenses_deleted_by_foreign` (`deleted_by`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
    ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
    ADD PRIMARY KEY (`id`),
  ADD KEY `invoices_tax_id_foreign` (`tax_id`),
  ADD KEY `invoices_created_by_foreign` (`created_by`);

--
-- Indexes for table `invoice_clients`
--
ALTER TABLE `invoice_clients`
    ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_clients_invoice_id_foreign` (`invoice_id`),
  ADD KEY `invoice_clients_client_id_foreign` (`client_id`);

--
-- Indexes for table `invoice_items`
--
ALTER TABLE `invoice_items`
    ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_items_task_id_foreign` (`task_id`),
  ADD KEY `invoice_items_item_project_id_foreign` (`item_project_id`);

--
-- Indexes for table `invoice_projects`
--
ALTER TABLE `invoice_projects`
    ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_projects_invoice_id_foreign` (`invoice_id`),
  ADD KEY `invoice_projects_project_id_foreign` (`project_id`);

--
-- Indexes for table `media`
--
ALTER TABLE `media`
    ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `media_uuid_unique` (`uuid`),
  ADD KEY `media_model_type_model_id_index` (`model_type`,`model_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
    ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
    ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
    ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_user_id_foreign` (`user_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
    ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
    ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `projects_prefix_unique` (`prefix`),
  ADD UNIQUE KEY `projects_name_unique` (`name`),
  ADD KEY `projects_client_id_foreign` (`client_id`),
  ADD KEY `projects_created_by_foreign` (`created_by`),
  ADD KEY `projects_deleted_by_foreign` (`deleted_by`);

--
-- Indexes for table `project_user`
--
ALTER TABLE `project_user`
    ADD PRIMARY KEY (`id`),
  ADD KEY `project_user_project_id_foreign` (`project_id`),
  ADD KEY `project_user_user_id_foreign` (`user_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
    ADD PRIMARY KEY (`id`),
  ADD KEY `reports_owner_id_foreign` (`owner_id`);

--
-- Indexes for table `report_filters`
--
ALTER TABLE `report_filters`
    ADD PRIMARY KEY (`id`),
  ADD KEY `report_filters_report_id_foreign` (`report_id`);

--
-- Indexes for table `report_invoices`
--
ALTER TABLE `report_invoices`
    ADD PRIMARY KEY (`id`),
  ADD KEY `report_invoices_invoice_id_foreign` (`invoice_id`),
  ADD KEY `report_invoices_report_id_foreign` (`report_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
    ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `status`
--
ALTER TABLE `status`
    ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `status_status_unique` (`status`),
  ADD UNIQUE KEY `status_name_unique` (`name`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
    ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tags_name_unique` (`name`),
  ADD KEY `tags_created_by_foreign` (`created_by`),
  ADD KEY `tags_deleted_by_foreign` (`deleted_by`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
    ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tasks_task_number_project_id_unique` (`task_number`,`project_id`),
  ADD KEY `tasks_deleted_by_foreign` (`deleted_by`),
  ADD KEY `tasks_project_id_foreign` (`project_id`),
  ADD KEY `tasks_created_by_foreign` (`created_by`);

--
-- Indexes for table `task_assignees`
--
ALTER TABLE `task_assignees`
    ADD PRIMARY KEY (`id`),
  ADD KEY `task_assignees_task_id_foreign` (`task_id`),
  ADD KEY `task_assignees_user_id_foreign` (`user_id`);

--
-- Indexes for table `task_attachments`
--
ALTER TABLE `task_attachments`
    ADD PRIMARY KEY (`id`),
  ADD KEY `task_attachments_task_id_foreign` (`task_id`);

--
-- Indexes for table `task_tags`
--
ALTER TABLE `task_tags`
    ADD PRIMARY KEY (`id`),
  ADD KEY `task_tags_task_id_foreign` (`task_id`),
  ADD KEY `task_tags_tag_id_foreign` (`tag_id`);

--
-- Indexes for table `taxes`
--
ALTER TABLE `taxes`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `time_entries`
--
ALTER TABLE `time_entries`
    ADD PRIMARY KEY (`id`),
  ADD KEY `time_entries_deleted_by_foreign` (`deleted_by`),
  ADD KEY `time_entries_task_id_foreign` (`task_id`),
  ADD KEY `time_entries_activity_type_id_foreign` (`activity_type_id`),
  ADD KEY `time_entries_user_id_foreign` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
    ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_created_by_foreign` (`created_by`),
  ADD KEY `users_deleted_by_foreign` (`deleted_by`);

--
-- Indexes for table `user_notifications`
--
ALTER TABLE `user_notifications`
    ADD PRIMARY KEY (`id`),
  ADD KEY `user_notifications_user_id_foreign` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
    MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `activity_types`
--
ALTER TABLE `activity_types`
    MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
    MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
    MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
    MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
    MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
    MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
    MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
    MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoice_clients`
--
ALTER TABLE `invoice_clients`
    MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoice_items`
--
ALTER TABLE `invoice_items`
    MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoice_projects`
--
ALTER TABLE `invoice_projects`
    MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `media`
--
ALTER TABLE `media`
    MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
    MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
    MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
    MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
    MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `project_user`
--
ALTER TABLE `project_user`
    MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
    MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `report_filters`
--
ALTER TABLE `report_filters`
    MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `report_invoices`
--
ALTER TABLE `report_invoices`
    MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
    MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
    MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `status`
--
ALTER TABLE `status`
    MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
    MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
    MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `task_assignees`
--
ALTER TABLE `task_assignees`
    MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `task_attachments`
--
ALTER TABLE `task_attachments`
    MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `task_tags`
--
ALTER TABLE `task_tags`
    MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `taxes`
--
ALTER TABLE `taxes`
    MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `time_entries`
--
ALTER TABLE `time_entries`
    MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
    MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_notifications`
--
ALTER TABLE `user_notifications`
    MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_types`
--
ALTER TABLE `activity_types`
    ADD CONSTRAINT `activity_types_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `activity_types_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `clients`
--
ALTER TABLE `clients`
    ADD CONSTRAINT `clients_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `clients_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `clients_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
    ADD CONSTRAINT `comments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `comments_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`);

--
-- Constraints for table `events`
--
ALTER TABLE `events`
    ADD CONSTRAINT `events_added_by_foreign` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
    ADD CONSTRAINT `expenses_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`),
  ADD CONSTRAINT `expenses_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `expenses_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `expenses_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`);

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
    ADD CONSTRAINT `invoices_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `invoices_tax_id_foreign` FOREIGN KEY (`tax_id`) REFERENCES `taxes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `invoice_clients`
--
ALTER TABLE `invoice_clients`
    ADD CONSTRAINT `invoice_clients_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `invoice_clients_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `invoice_items`
--
ALTER TABLE `invoice_items`
    ADD CONSTRAINT `invoice_items_item_project_id_foreign` FOREIGN KEY (`item_project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `invoice_items_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `invoice_projects`
--
ALTER TABLE `invoice_projects`
    ADD CONSTRAINT `invoice_projects_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `invoice_projects_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
    ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`);

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
    ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
    ADD CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
    ADD CONSTRAINT `projects_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`),
  ADD CONSTRAINT `projects_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `projects_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `project_user`
--
ALTER TABLE `project_user`
    ADD CONSTRAINT `project_user_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`),
  ADD CONSTRAINT `project_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
    ADD CONSTRAINT `reports_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `report_filters`
--
ALTER TABLE `report_filters`
    ADD CONSTRAINT `report_filters_report_id_foreign` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `report_invoices`
--
ALTER TABLE `report_invoices`
    ADD CONSTRAINT `report_invoices_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `report_invoices_report_id_foreign` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
    ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`),
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

--
-- Constraints for table `tags`
--
ALTER TABLE `tags`
    ADD CONSTRAINT `tags_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `tags_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
    ADD CONSTRAINT `tasks_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `tasks_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `tasks_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`);

--
-- Constraints for table `task_assignees`
--
ALTER TABLE `task_assignees`
    ADD CONSTRAINT `task_assignees_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`),
  ADD CONSTRAINT `task_assignees_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `task_attachments`
--
ALTER TABLE `task_attachments`
    ADD CONSTRAINT `task_attachments_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`);

--
-- Constraints for table `task_tags`
--
ALTER TABLE `task_tags`
    ADD CONSTRAINT `task_tags_tag_id_foreign` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`),
  ADD CONSTRAINT `task_tags_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`);

--
-- Constraints for table `time_entries`
--
ALTER TABLE `time_entries`
    ADD CONSTRAINT `time_entries_activity_type_id_foreign` FOREIGN KEY (`activity_type_id`) REFERENCES `activity_types` (`id`),
  ADD CONSTRAINT `time_entries_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `time_entries_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`),
  ADD CONSTRAINT `time_entries_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
    ADD CONSTRAINT `users_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `users_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `user_notifications`
--
ALTER TABLE `user_notifications`
    ADD CONSTRAINT `user_notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
