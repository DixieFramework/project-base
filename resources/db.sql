-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Generation Time: Sep 23, 2023 at 02:14 AM
-- Server version: 5.7.41-log
-- PHP Version: 8.1.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `talav_demo_dev`
--

-- --------------------------------------------------------

--
-- Table structure for table `asset`
--

CREATE TABLE `asset` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:uuid)',
  `sponsor_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:uuid)',
  `asset_type_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:uuid)',
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:uuid)',
  `name` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `privacy` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `discriminator` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data` longtext COLLATE utf8mb4_unicode_ci COMMENT '(DC2Type:object)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `asset_type`
--

CREATE TABLE `asset_type` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:uuid)',
  `parent_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:uuid)',
  `name` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` int(11) NOT NULL,
  `is_asset` tinyint(1) NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `slug` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `image_id` int(11) NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nick` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `text` varchar(1024) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `galleries`
--

CREATE TABLE `galleries` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:uuid)',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `galleries`
--

INSERT INTO `galleries` (`id`, `user_id`, `name`) VALUES
(1, 'bf543636-3eff-4c91-a530-4b75f2cecae4', 'rgrgrgr'),
(2, 'bf543636-3eff-4c91-a530-4b75f2cecae4', 'rgrgrgr');

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE `images` (
  `id` int(11) NOT NULL,
  `gallery_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `institution`
--

CREATE TABLE `institution` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:uuid)',
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:uuid)',
  `name` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `website` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messenger_messages`
--

CREATE TABLE `messenger_messages` (
  `id` bigint(20) NOT NULL,
  `body` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `headers` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue_name` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `available_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `delivered_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `messenger_messages`
--

INSERT INTO `messenger_messages` (`id`, `body`, `headers`, `queue_name`, `created_at`, `available_at`, `delivered_at`) VALUES
(1, 'O:36:\\\"Symfony\\\\Component\\\\Messenger\\\\Envelope\\\":2:{s:44:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0stamps\\\";a:1:{s:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\";a:1:{i:0;O:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\":1:{s:55:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\0busName\\\";s:9:\\\"event.bus\\\";}}}s:45:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0message\\\";O:51:\\\"Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\\":2:{s:60:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0message\\\";O:39:\\\"Talav\\\\CoreBundle\\\\Mime\\\\RegistrationEmail\\\":4:{i:0;a:8:{s:10:\\\"importance\\\";s:6:\\\"medium\\\";s:7:\\\"content\\\";s:0:\\\"\\\";s:9:\\\"exception\\\";b:0;s:11:\\\"action_text\\\";s:19:\\\"registration.action\\\";s:10:\\\"action_url\\\";s:210:\\\"http://demoapp.dvl.to/register/verify?expires=1695411032&id=05ceb243-11de-43db-ae21-f686f960333b&signature=bJIeuD3zHvRblmyZpmxu6BkBgrIqYnKYh4QWl88cILs%3D&token=kk2w4%2F4qXI9iTkj6e2m4frzEn%2BErpEMulatiqbCAidk%3D\\\";s:8:\\\"markdown\\\";b:0;s:3:\\\"raw\\\";b:0;s:11:\\\"footer_text\\\";s:34:\\\"Notification email sent by Symfony\\\";}i:1;s:7:\\\"default\\\";i:2;b:0;i:3;a:4:{i:0;s:46:\\\"@TalavUser/notification/registration.html.twig\\\";i:1;N;i:2;a:12:{s:10:\\\"importance\\\";s:6:\\\"medium\\\";s:7:\\\"content\\\";s:0:\\\"\\\";s:9:\\\"exception\\\";b:0;s:11:\\\"action_text\\\";s:19:\\\"registration.action\\\";s:10:\\\"action_url\\\";s:210:\\\"http://demoapp.dvl.to/register/verify?expires=1695411032&id=05ceb243-11de-43db-ae21-f686f960333b&signature=bJIeuD3zHvRblmyZpmxu6BkBgrIqYnKYh4QWl88cILs%3D&token=kk2w4%2F4qXI9iTkj6e2m4frzEn%2BErpEMulatiqbCAidk%3D\\\";s:8:\\\"markdown\\\";b:0;s:3:\\\"raw\\\";b:0;s:11:\\\"footer_text\\\";s:34:\\\"Notification email sent by Symfony\\\";s:15:\\\"importance_text\\\";s:22:\\\"importance.medium_full\\\";s:8:\\\"username\\\";s:4:\\\"root\\\";s:12:\\\"expires_date\\\";O:17:\\\"DateTimeImmutable\\\":3:{s:4:\\\"date\\\";s:26:\\\"2023-09-22 19:30:32.000000\\\";s:13:\\\"timezone_type\\\";i:1;s:8:\\\"timezone\\\";s:6:\\\"+00:00\\\";}s:17:\\\"expires_life_time\\\";s:6:\\\"1 hour\\\";}i:3;a:6:{i:0;N;i:1;N;i:2;N;i:3;N;i:4;a:0:{}i:5;a:2:{i:0;O:37:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\\":2:{s:46:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0headers\\\";a:3:{s:7:\\\"subject\\\";a:1:{i:0;O:48:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:7:\\\"Subject\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:55:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\0value\\\";s:20:\\\"registration.subject\\\";}}s:4:\\\"from\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:4:\\\"From\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:19:\\\"calculation@bibi.nu\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:11:\\\"Calculation\\\";}}}}s:2:\\\"to\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:2:\\\"To\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:14:\\\"root@local.dev\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:0:\\\"\\\";}}}}}s:49:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0lineLength\\\";i:76;}i:1;N;}}}}s:61:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0envelope\\\";N;}}', '[]', 'default', '2023-09-22 18:30:32', '2023-09-22 18:30:32', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `permission`
--

CREATE TABLE `permission` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:uuid)',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permission`
--

INSERT INTO `permission` (`id`, `name`) VALUES
('8597e63c-5976-11ee-b3e8-0242ac10ee0c', 'GALLERY_CREATE'),
('8597eddf-5976-11ee-b3e8-0242ac10ee0c', 'GALLERY_READ'),
('8597f4fc-5976-11ee-b3e8-0242ac10ee0c', 'GALLERY_UPDATE'),
('8597fba7-5976-11ee-b3e8-0242ac10ee0c', 'GALLERY_DELETE'),
('8598028a-5976-11ee-b3e8-0242ac10ee0c', 'GALLERY_LIST');

-- --------------------------------------------------------

--
-- Table structure for table `position`
--

CREATE TABLE `position` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:uuid)',
  `asset_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:uuid)',
  `last_value_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:uuid)',
  `institution_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:uuid)',
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:uuid)',
  `notes` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `complete_date` date DEFAULT NULL,
  `generated_income` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `discriminator` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data` longtext COLLATE utf8mb4_unicode_ci COMMENT '(DC2Type:object)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `position_tag`
--

CREATE TABLE `position_tag` (
  `position_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:uuid)',
  `tag_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:uuid)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `position_value`
--

CREATE TABLE `position_value` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:uuid)',
  `position_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:uuid)',
  `value` int(11) NOT NULL,
  `value_date` date NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:uuid)',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`id`, `name`) VALUES
('a6da8488-5976-11ee-b3e8-0242ac10ee0c', 'ROLE_USER'),
('a6da8bde-5976-11ee-b3e8-0242ac10ee0c', 'ROLE_MODERATOR'),
('a6da926c-5976-11ee-b3e8-0242ac10ee0c', 'ROLE_SUPER_MODERATOR'),
('a6da98d2-5976-11ee-b3e8-0242ac10ee0c', 'ROLE_ADMIN'),
('a6da9f5e-5976-11ee-b3e8-0242ac10ee0c', 'ROLE_SUPER_ADMIN');

-- --------------------------------------------------------

--
-- Table structure for table `role_permission`
--

CREATE TABLE `role_permission` (
  `role_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:uuid)',
  `permission_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:uuid)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_permission`
--

INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES
('a6da8488-5976-11ee-b3e8-0242ac10ee0c', '8597e63c-5976-11ee-b3e8-0242ac10ee0c'),
('a6da8488-5976-11ee-b3e8-0242ac10ee0c', '8597eddf-5976-11ee-b3e8-0242ac10ee0c'),
('a6da8488-5976-11ee-b3e8-0242ac10ee0c', '8597f4fc-5976-11ee-b3e8-0242ac10ee0c'),
('a6da8488-5976-11ee-b3e8-0242ac10ee0c', '8597fba7-5976-11ee-b3e8-0242ac10ee0c'),
('a6da8488-5976-11ee-b3e8-0242ac10ee0c', '8598028a-5976-11ee-b3e8-0242ac10ee0c');

-- --------------------------------------------------------

--
-- Table structure for table `sponsor`
--

CREATE TABLE `sponsor` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:uuid)',
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:uuid)',
  `name` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `website` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `privacy` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tag`
--

CREATE TABLE `tag` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:uuid)',
  `tag_group_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:uuid)',
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:uuid)',
  `name` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` int(11) NOT NULL,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tag`
--

INSERT INTO `tag` (`id`, `tag_group_id`, `created_by`, `name`, `position`, `color`, `created_at`, `updated_at`) VALUES
('04da8f39-f584-4775-bbe4-40867b858dda', 'b7fd689e-6ab8-4160-868c-7a6466ad4e81', '6326965d-06bf-434d-b72a-f9ddb329be8e', 'Short-term', 0, '#ff94c2', '2023-09-20 04:51:52', '2023-09-20 04:51:52'),
('053e3346-3683-4831-a224-8bb106415d62', 'f68a817a-f946-4319-85d2-5a77a3473b9e', '001c2169-8245-4b9f-8a9d-f24f8967c033', 'Conservative', 0, '#ff94c2', '2023-09-20 04:51:54', '2023-09-20 04:51:54'),
('094e86c2-6f55-4a46-8fd1-92b6720ee437', 'b7fd689e-6ab8-4160-868c-7a6466ad4e81', '6326965d-06bf-434d-b72a-f9ddb329be8e', 'Long-term', 1, '#fbc02d', '2023-09-20 04:51:52', '2023-09-20 04:51:52'),
('1000aee4-89ba-4078-a6e3-e666664fdc1a', '6dc9758a-fa54-4351-9e82-03faf4cef6be', '590d86f8-cc57-4ab7-9160-9682b2c977f1', 'Short-term', 0, '#ff94c2', '2023-09-20 04:51:55', '2023-09-20 04:51:55'),
('1a2afb7b-7e5e-4f51-b45b-66fb69ae2d4d', 'ed0b09b0-9c4e-4360-9730-40555a2bbf1a', 'dad3b14f-1c84-4991-ad0f-91e9e802474e', 'Long-term', 1, '#fbc02d', '2023-09-20 04:51:53', '2023-09-20 04:51:53'),
('1b5cac81-9eb0-4809-8165-48da12b80a93', '9cd8bd75-c82c-481d-8ef0-a4f81740872f', 'bf543636-3eff-4c91-a530-4b75f2cecae4', 'Moderate', 1, '#ff94c2', '2023-09-20 04:51:52', '2023-09-20 04:51:52'),
('1e467497-ce88-489a-bf1f-5bc6ff5e1bee', '547cd91b-882d-40b7-a1ff-19866711b952', '2243df71-71a5-4ba4-9f70-728453fb8bc4', 'Short-term', 0, '#ff94c2', '2023-09-20 04:51:55', '2023-09-20 04:51:55'),
('23700008-2b15-4f37-b57f-d86119244364', '2bb61007-645b-4b05-9320-c47902762914', '2243df71-71a5-4ba4-9f70-728453fb8bc4', 'Moderate', 1, '#ff94c2', '2023-09-20 04:51:55', '2023-09-20 04:51:55'),
('28fc9e91-1198-47df-9635-6a16851f405c', 'cbe07ed1-309d-480a-b917-cf886dff60ba', 'a892a004-a931-4192-a633-45a113061195', 'Short-term', 0, '#ff94c2', '2023-09-20 04:51:54', '2023-09-20 04:51:54'),
('30e8744c-1cd3-4aaf-895c-e0ac96de4e87', 'f6cb0be4-4dd4-4c60-8fa2-81f44d292f67', 'a892a004-a931-4192-a633-45a113061195', 'Moderate', 1, '#ff94c2', '2023-09-20 04:51:54', '2023-09-20 04:51:54'),
('34dbdd91-c14d-4904-835b-75ff8d2129de', '7f14efee-b130-482c-8f6e-9ea2f004dee4', 'd198ede1-c91b-4085-857d-7e5e950b8baf', 'Long-term', 1, '#fbc02d', '2023-09-20 04:51:54', '2023-09-20 04:51:54'),
('3d110039-368b-4949-b8b1-50c228f1ab3f', 'c45cacf9-3012-4217-8171-dbe816e9ed7e', '6326965d-06bf-434d-b72a-f9ddb329be8e', 'Moderate', 1, '#ff94c2', '2023-09-20 04:51:52', '2023-09-20 04:51:52'),
('41d6f083-8d6a-4cad-b9bc-75dc81cc1343', 'f68a817a-f946-4319-85d2-5a77a3473b9e', '001c2169-8245-4b9f-8a9d-f24f8967c033', 'Moderate', 1, '#ff94c2', '2023-09-20 04:51:54', '2023-09-20 04:51:54'),
('467b1776-cd53-4f4c-b3e4-e42d7905f756', '54564767-880e-446d-acd3-6a858d7edb65', '6279f4f2-6484-4844-b708-0bf8c7f3062d', 'Moderate', 1, '#ff94c2', '2023-09-20 04:51:53', '2023-09-20 04:51:53'),
('479f115d-6369-4e00-969d-45f8d8feb56b', 'cc38bc76-cc30-4eb3-a15d-8913f32fbb57', '6279f4f2-6484-4844-b708-0bf8c7f3062d', 'Short-term', 0, '#ff94c2', '2023-09-20 04:51:53', '2023-09-20 04:51:53'),
('482d9051-8835-477a-b4f3-6c625530a683', '42072d4f-5a82-49be-9f1d-ad5450e7a00b', '3641f178-a494-4e0f-9144-ab00616c2a3d', 'Long-term', 1, '#fbc02d', '2023-09-20 04:51:52', '2023-09-20 04:51:52'),
('4ad01aa9-edac-4fe0-b665-be8741fc43e1', 'a9b74bb9-34af-4417-a5a3-14c76cfd7532', '590d86f8-cc57-4ab7-9160-9682b2c977f1', 'Aggressive', 2, '#fbc02d', '2023-09-20 04:51:55', '2023-09-20 04:51:55'),
('4b46d7ce-c56c-4d6a-9bf3-f966158f1794', 'cc38bc76-cc30-4eb3-a15d-8913f32fbb57', '6279f4f2-6484-4844-b708-0bf8c7f3062d', 'Long-term', 1, '#fbc02d', '2023-09-20 04:51:53', '2023-09-20 04:51:53'),
('4e89db3d-46fc-4688-bd7e-6bebe7a2e009', '6dc9758a-fa54-4351-9e82-03faf4cef6be', '590d86f8-cc57-4ab7-9160-9682b2c977f1', 'Long-term', 1, '#fbc02d', '2023-09-20 04:51:55', '2023-09-20 04:51:55'),
('4fd0ded0-0b70-4e1f-aa65-9cf99726ace2', 'f6cb0be4-4dd4-4c60-8fa2-81f44d292f67', 'a892a004-a931-4192-a633-45a113061195', 'Conservative', 0, '#ff94c2', '2023-09-20 04:51:54', '2023-09-20 04:51:54'),
('565577a5-1847-4a65-a893-8da4ac7d8a1d', 'a9b74bb9-34af-4417-a5a3-14c76cfd7532', '590d86f8-cc57-4ab7-9160-9682b2c977f1', 'Conservative', 0, '#ff94c2', '2023-09-20 04:51:55', '2023-09-20 04:51:55'),
('5b51b600-007d-4dca-bb90-fc655b199455', '1415a305-c901-4566-bbfe-99af6b7cade8', '3641f178-a494-4e0f-9144-ab00616c2a3d', 'Moderate', 1, '#ff94c2', '2023-09-20 04:51:52', '2023-09-20 04:51:52'),
('6302189a-f726-452e-a1ef-36d0b10363c3', '2bb61007-645b-4b05-9320-c47902762914', '2243df71-71a5-4ba4-9f70-728453fb8bc4', 'Conservative', 0, '#ff94c2', '2023-09-20 04:51:55', '2023-09-20 04:51:55'),
('6a76391d-515a-42fb-9b0b-9e34201775cc', 'cbe07ed1-309d-480a-b917-cf886dff60ba', 'a892a004-a931-4192-a633-45a113061195', 'Long-term', 1, '#fbc02d', '2023-09-20 04:51:54', '2023-09-20 04:51:54'),
('6cdba37e-7ca1-47a3-a823-149ec7feff29', '7f14efee-b130-482c-8f6e-9ea2f004dee4', 'd198ede1-c91b-4085-857d-7e5e950b8baf', 'Short-term', 0, '#ff94c2', '2023-09-20 04:51:54', '2023-09-20 04:51:54'),
('70350a9b-0df8-4de3-8c00-eaa472ee0cc8', '547cd91b-882d-40b7-a1ff-19866711b952', '2243df71-71a5-4ba4-9f70-728453fb8bc4', 'Long-term', 1, '#fbc02d', '2023-09-20 04:51:55', '2023-09-20 04:51:55'),
('755af081-7910-41e8-8b9f-a72f780ee8c5', 'f6cb0be4-4dd4-4c60-8fa2-81f44d292f67', 'a892a004-a931-4192-a633-45a113061195', 'Aggressive', 2, '#fbc02d', '2023-09-20 04:51:54', '2023-09-20 04:51:54'),
('764100a8-8f95-4201-8ab5-f7ec846888e5', '9cd8bd75-c82c-481d-8ef0-a4f81740872f', 'bf543636-3eff-4c91-a530-4b75f2cecae4', 'Aggressive', 2, '#fbc02d', '2023-09-20 04:51:52', '2023-09-20 04:51:52'),
('7fb96157-64ef-436e-acc9-f27d7c54b3bc', '2bb61007-645b-4b05-9320-c47902762914', '2243df71-71a5-4ba4-9f70-728453fb8bc4', 'Aggressive', 2, '#fbc02d', '2023-09-20 04:51:55', '2023-09-20 04:51:55'),
('8aef952d-2e3a-41b7-8e33-471fa1895231', '2a03ea88-2be1-44de-a219-1be043239b92', '001c2169-8245-4b9f-8a9d-f24f8967c033', 'Long-term', 1, '#fbc02d', '2023-09-20 04:51:54', '2023-09-20 04:51:54'),
('8dad4652-d0a1-4306-98e0-0340ad7787b7', 'c45cacf9-3012-4217-8171-dbe816e9ed7e', '6326965d-06bf-434d-b72a-f9ddb329be8e', 'Aggressive', 2, '#fbc02d', '2023-09-20 04:51:52', '2023-09-20 04:51:52'),
('8defa1c5-8928-4b52-b47f-77ca14fdf0cb', '53773c3e-4a76-471a-875d-cac291f40d24', 'dad3b14f-1c84-4991-ad0f-91e9e802474e', 'Moderate', 1, '#ff94c2', '2023-09-20 04:51:53', '2023-09-20 04:51:53'),
('928181c0-979a-49b5-9804-bf55cbbdc9f6', 'f68a817a-f946-4319-85d2-5a77a3473b9e', '001c2169-8245-4b9f-8a9d-f24f8967c033', 'Aggressive', 2, '#fbc02d', '2023-09-20 04:51:54', '2023-09-20 04:51:54'),
('93a5c8d7-19be-4f06-9480-fed5d8a4c102', 'ed0b09b0-9c4e-4360-9730-40555a2bbf1a', 'dad3b14f-1c84-4991-ad0f-91e9e802474e', 'Short-term', 0, '#ff94c2', '2023-09-20 04:51:53', '2023-09-20 04:51:53'),
('9712c364-92c7-421f-93da-33adbe34e4b1', 'a9b74bb9-34af-4417-a5a3-14c76cfd7532', '590d86f8-cc57-4ab7-9160-9682b2c977f1', 'Moderate', 1, '#ff94c2', '2023-09-20 04:51:55', '2023-09-20 04:51:55'),
('9b3e4c74-c832-4eb6-9b4a-c3a2cb3bff80', '733d3462-d1c2-4d47-8e01-f3462211b610', 'd198ede1-c91b-4085-857d-7e5e950b8baf', 'Moderate', 1, '#ff94c2', '2023-09-20 04:51:54', '2023-09-20 04:51:54'),
('9d266c3e-a249-4802-975a-9037202956ae', '1415a305-c901-4566-bbfe-99af6b7cade8', '3641f178-a494-4e0f-9144-ab00616c2a3d', 'Aggressive', 2, '#fbc02d', '2023-09-20 04:51:52', '2023-09-20 04:51:52'),
('a6ac25f3-d0e2-4d8f-9cf6-4d9b2c119c9c', '1c007b7e-5867-450d-a75b-bb88f55b062e', 'bf543636-3eff-4c91-a530-4b75f2cecae4', 'Short-term', 0, '#ff94c2', '2023-09-20 04:51:52', '2023-09-20 04:51:52'),
('aa8958aa-e58a-4b55-8706-25373914e459', '733d3462-d1c2-4d47-8e01-f3462211b610', 'd198ede1-c91b-4085-857d-7e5e950b8baf', 'Aggressive', 2, '#fbc02d', '2023-09-20 04:51:54', '2023-09-20 04:51:54'),
('aae56707-4561-4d35-b7c2-1b35ad47fc86', '53773c3e-4a76-471a-875d-cac291f40d24', 'dad3b14f-1c84-4991-ad0f-91e9e802474e', 'Conservative', 0, '#ff94c2', '2023-09-20 04:51:53', '2023-09-20 04:51:53'),
('b9b6e7a0-829f-455a-b901-c8da49969023', '42072d4f-5a82-49be-9f1d-ad5450e7a00b', '3641f178-a494-4e0f-9144-ab00616c2a3d', 'Short-term', 0, '#ff94c2', '2023-09-20 04:51:52', '2023-09-20 04:51:52'),
('bcf5e385-5dec-43e9-b14a-9d81e9c4a856', '2a03ea88-2be1-44de-a219-1be043239b92', '001c2169-8245-4b9f-8a9d-f24f8967c033', 'Short-term', 0, '#ff94c2', '2023-09-20 04:51:54', '2023-09-20 04:51:54'),
('c76d57d4-e395-4064-be09-ffaf5423e19a', '733d3462-d1c2-4d47-8e01-f3462211b610', 'd198ede1-c91b-4085-857d-7e5e950b8baf', 'Conservative', 0, '#ff94c2', '2023-09-20 04:51:54', '2023-09-20 04:51:54'),
('c9f83b1c-5387-4b7a-84c3-807c58256533', '54564767-880e-446d-acd3-6a858d7edb65', '6279f4f2-6484-4844-b708-0bf8c7f3062d', 'Conservative', 0, '#ff94c2', '2023-09-20 04:51:53', '2023-09-20 04:51:53'),
('d8bbf918-dba3-4d66-aece-b72613dafbad', 'c45cacf9-3012-4217-8171-dbe816e9ed7e', '6326965d-06bf-434d-b72a-f9ddb329be8e', 'Conservative', 0, '#ff94c2', '2023-09-20 04:51:52', '2023-09-20 04:51:52'),
('dd822054-6d0a-4b4d-9b44-8b7261e4cef2', '53773c3e-4a76-471a-875d-cac291f40d24', 'dad3b14f-1c84-4991-ad0f-91e9e802474e', 'Aggressive', 2, '#fbc02d', '2023-09-20 04:51:53', '2023-09-20 04:51:53'),
('e5ca4d80-f34a-43d0-a925-9a41c4147b80', '9cd8bd75-c82c-481d-8ef0-a4f81740872f', 'bf543636-3eff-4c91-a530-4b75f2cecae4', 'Conservative', 0, '#ff94c2', '2023-09-20 04:51:52', '2023-09-20 04:51:52'),
('eb48e710-524c-47a7-9b7a-4e4cde855c30', '54564767-880e-446d-acd3-6a858d7edb65', '6279f4f2-6484-4844-b708-0bf8c7f3062d', 'Aggressive', 2, '#fbc02d', '2023-09-20 04:51:53', '2023-09-20 04:51:53'),
('f1e4b9e0-ce53-4918-a6c4-77129b19a549', '1c007b7e-5867-450d-a75b-bb88f55b062e', 'bf543636-3eff-4c91-a530-4b75f2cecae4', 'Long-term', 1, '#fbc02d', '2023-09-20 04:51:52', '2023-09-20 04:51:52'),
('fd8cb7db-e93c-4e33-bd7e-36b8fbc90130', '1415a305-c901-4566-bbfe-99af6b7cade8', '3641f178-a494-4e0f-9144-ab00616c2a3d', 'Conservative', 0, '#ff94c2', '2023-09-20 04:51:52', '2023-09-20 04:51:52');

-- --------------------------------------------------------

--
-- Table structure for table `tag_group`
--

CREATE TABLE `tag_group` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:uuid)',
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:uuid)',
  `name` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tag_group`
--

INSERT INTO `tag_group` (`id`, `created_by`, `name`, `position`, `created_at`, `updated_at`) VALUES
('1415a305-c901-4566-bbfe-99af6b7cade8', '3641f178-a494-4e0f-9144-ab00616c2a3d', 'Risk Tolerance', 0, '2023-09-20 04:51:52', '2023-09-20 04:51:52'),
('1c007b7e-5867-450d-a75b-bb88f55b062e', 'bf543636-3eff-4c91-a530-4b75f2cecae4', 'Investment Horizon', 1, '2023-09-20 04:51:52', '2023-09-20 04:51:52'),
('2a03ea88-2be1-44de-a219-1be043239b92', '001c2169-8245-4b9f-8a9d-f24f8967c033', 'Investment Horizon', 1, '2023-09-20 04:51:54', '2023-09-20 04:51:54'),
('2bb61007-645b-4b05-9320-c47902762914', '2243df71-71a5-4ba4-9f70-728453fb8bc4', 'Risk Tolerance', 0, '2023-09-20 04:51:55', '2023-09-20 04:51:55'),
('42072d4f-5a82-49be-9f1d-ad5450e7a00b', '3641f178-a494-4e0f-9144-ab00616c2a3d', 'Investment Horizon', 1, '2023-09-20 04:51:52', '2023-09-20 04:51:52'),
('53773c3e-4a76-471a-875d-cac291f40d24', 'dad3b14f-1c84-4991-ad0f-91e9e802474e', 'Risk Tolerance', 0, '2023-09-20 04:51:53', '2023-09-20 04:51:53'),
('54564767-880e-446d-acd3-6a858d7edb65', '6279f4f2-6484-4844-b708-0bf8c7f3062d', 'Risk Tolerance', 0, '2023-09-20 04:51:53', '2023-09-20 04:51:53'),
('547cd91b-882d-40b7-a1ff-19866711b952', '2243df71-71a5-4ba4-9f70-728453fb8bc4', 'Investment Horizon', 1, '2023-09-20 04:51:55', '2023-09-20 04:51:55'),
('6dc9758a-fa54-4351-9e82-03faf4cef6be', '590d86f8-cc57-4ab7-9160-9682b2c977f1', 'Investment Horizon', 1, '2023-09-20 04:51:55', '2023-09-20 04:51:55'),
('733d3462-d1c2-4d47-8e01-f3462211b610', 'd198ede1-c91b-4085-857d-7e5e950b8baf', 'Risk Tolerance', 0, '2023-09-20 04:51:54', '2023-09-20 04:51:54'),
('7f14efee-b130-482c-8f6e-9ea2f004dee4', 'd198ede1-c91b-4085-857d-7e5e950b8baf', 'Investment Horizon', 1, '2023-09-20 04:51:54', '2023-09-20 04:51:54'),
('9cd8bd75-c82c-481d-8ef0-a4f81740872f', 'bf543636-3eff-4c91-a530-4b75f2cecae4', 'Risk Tolerance', 0, '2023-09-20 04:51:52', '2023-09-20 04:51:52'),
('a9b74bb9-34af-4417-a5a3-14c76cfd7532', '590d86f8-cc57-4ab7-9160-9682b2c977f1', 'Risk Tolerance', 0, '2023-09-20 04:51:55', '2023-09-20 04:51:55'),
('b7fd689e-6ab8-4160-868c-7a6466ad4e81', '6326965d-06bf-434d-b72a-f9ddb329be8e', 'Investment Horizon', 1, '2023-09-20 04:51:52', '2023-09-20 04:51:52'),
('c45cacf9-3012-4217-8171-dbe816e9ed7e', '6326965d-06bf-434d-b72a-f9ddb329be8e', 'Risk Tolerance', 0, '2023-09-20 04:51:52', '2023-09-20 04:51:52'),
('cbe07ed1-309d-480a-b917-cf886dff60ba', 'a892a004-a931-4192-a633-45a113061195', 'Investment Horizon', 1, '2023-09-20 04:51:54', '2023-09-20 04:51:54'),
('cc38bc76-cc30-4eb3-a15d-8913f32fbb57', '6279f4f2-6484-4844-b708-0bf8c7f3062d', 'Investment Horizon', 1, '2023-09-20 04:51:53', '2023-09-20 04:51:53'),
('ed0b09b0-9c4e-4360-9730-40555a2bbf1a', 'dad3b14f-1c84-4991-ad0f-91e9e802474e', 'Investment Horizon', 1, '2023-09-20 04:51:53', '2023-09-20 04:51:53'),
('f68a817a-f946-4319-85d2-5a77a3473b9e', '001c2169-8245-4b9f-8a9d-f24f8967c033', 'Risk Tolerance', 0, '2023-09-20 04:51:54', '2023-09-20 04:51:54'),
('f6cb0be4-4dd4-4c60-8fa2-81f44d292f67', 'a892a004-a931-4192-a633-45a113061195', 'Risk Tolerance', 0, '2023-09-20 04:51:54', '2023-09-20 04:51:54');

-- --------------------------------------------------------

--
-- Table structure for table `talav_settings`
--

CREATE TABLE `talav_settings` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `scope` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaction`
--

CREATE TABLE `transaction` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:uuid)',
  `position_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:uuid)',
  `value` int(11) NOT NULL,
  `value_date` date NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:uuid)',
  `profile_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:uuid)',
  `username` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username_canonical` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_canonical` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `salt` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `password_reset_token` varchar(180) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password_requested_at` datetime DEFAULT NULL,
  `roles_array` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `flags` json DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `profile_id`, `username`, `username_canonical`, `email`, `email_canonical`, `enabled`, `salt`, `password`, `last_login`, `password_reset_token`, `password_requested_at`, `roles_array`, `first_name`, `last_name`, `created_at`, `updated_at`, `flags`) VALUES
('001c2169-8245-4b9f-8a9d-f24f8967c033', NULL, 'user5', 'user5', 'user5@test.com', 'user5@test.com', 0, 'qc96y9gmzeo4s848sc8s8cskocc8wc4', '$2y$13$kuDAhc7KvzRdmaax00AoxulcP7BMgq9hJmY9uqqEaARUhnGN3XtzS', NULL, NULL, NULL, 'a:0:{}', 'Hailee', 'Runolfsson', '2023-09-20 04:51:54', '2023-09-20 04:51:54', 'null'),
('05ceb243-11de-43db-ae21-f686f960333b', NULL, 'root', 'root', 'root@local.dev', 'root@local.dev', 1, 'ecp5wdindcg8gs0wsgskcckggk8co8w', '$2y$13$t/c16AaFMMwC9aZhHXsZZOjISM3VCetHNgJ/oRfNN5fDn3dQwygei', '2023-09-22 20:21:40', NULL, NULL, 'a:0:{}', NULL, NULL, '2023-09-22 18:30:31', '2023-09-22 20:21:40', 'null'),
('2243df71-71a5-4ba4-9f70-728453fb8bc4', NULL, 'user8', 'user8', 'user8@test.com', 'user8@test.com', 1, 'i5ogj9brec8csgc0s4sk48sgco48wc0', '$2y$13$v35B9pG/yn7Z21ATt6oaaes.QgaHTJoPC.HemW24h5Gdt7JVdZ/lu', NULL, NULL, NULL, 'a:0:{}', 'Euna', 'Ernser', '2023-09-20 04:51:55', '2023-09-20 04:51:55', 'null'),
('3641f178-a494-4e0f-9144-ab00616c2a3d', NULL, 'user2', 'user2', 'user2@test.com', 'user2@test.com', 1, 'g6vwrewf5f4sos4sw4ooc08oswcgkgw', '$2y$13$MPKpNQNaJQ46xLi4ipE.m.qDgpfgtNdDhDQFUmzmUQdYJVVUvDDVm', NULL, NULL, NULL, 'a:0:{}', 'Rosalyn', 'Schmitt', '2023-09-20 04:51:52', '2023-09-20 04:51:52', 'null'),
('590d86f8-cc57-4ab7-9160-9682b2c977f1', NULL, 'user9', 'user9', 'user9@test.com', 'user9@test.com', 0, '90u8p8opz9s8cwo800wcc440osw8k08', '$2y$13$1ycR.hbIhnilURuPfkeD9OAAV0ozPbKe6q8YVhdOfnMEZKTRvj.rm', NULL, NULL, NULL, 'a:0:{}', 'Macey', 'Kassulke', '2023-09-20 04:51:55', '2023-09-20 04:51:55', 'null'),
('6279f4f2-6484-4844-b708-0bf8c7f3062d', NULL, 'user3', 'user3', 'user3@test.com', 'user3@test.com', 1, 'e33wjqs9860ckk8cg0wcok0gk0wwo88', '$2y$13$mqTuHiWeSbiqWZfI0OPVPOJRsphI0LbllZipcqKPd3tRlA/uWHAqy', NULL, NULL, NULL, 'a:0:{}', 'Johnson', 'Zemlak', '2023-09-20 04:51:53', '2023-09-20 04:51:53', 'null'),
('6326965d-06bf-434d-b72a-f9ddb329be8e', NULL, 'user1', 'user1', 'user1@test.com', 'user1@test.com', 1, 'ni4s9w2hvesg0kokosw8go4gsokswk4', '$2y$13$q0SqWwuVdo20hIY6DmamyuJjUqc5cD2ZAfVTkpVbUXQGruEnuuWVm', NULL, NULL, NULL, 'a:0:{}', 'Gina', 'Ratke', '2023-09-20 04:51:52', '2023-09-20 04:51:52', 'null'),
('a892a004-a931-4192-a633-45a113061195', NULL, 'user6', 'user6', 'user6@test.com', 'user6@test.com', 1, 'msgua12hggg80ss8ggwggko04kocs00', '$2y$13$qqQon2wLOoMHBZaLcjIIIO4IUr142yvdcpB2XmuxL/RyKTnmqaLsK', NULL, NULL, NULL, 'a:0:{}', 'Stone', 'Shields', '2023-09-20 04:51:54', '2023-09-20 04:51:54', 'null'),
('bf543636-3eff-4c91-a530-4b75f2cecae4', NULL, 'user0', 'user0', 'user0@test.com', 'user0@test.com', 1, '1q19wc18t6kg8s0kws0gk0osc0c4w4c', '$2y$13$M7KFfIHKxFzAtaRp3wzSJeOdsvuP77f2pciLOhJNxdxW822PJjjRi', '2023-09-23 00:04:33', NULL, NULL, 'a:0:{}', 'Ofelia', 'Kulas', '2023-09-20 04:51:51', '2023-09-23 00:04:33', 'null'),
('d198ede1-c91b-4085-857d-7e5e950b8baf', NULL, 'user7', 'user7', 'user7@test.com', 'user7@test.com', 1, '6emk9bgaodc08g00gckowks4ogk0ooc', '$2y$13$QCaIu5WhTmchdGNoy2mXru7OR.wNU6Zwj3xVUgQNi96L.SL76.Gsa', NULL, NULL, NULL, 'a:0:{}', 'Gunnar', 'Daniel', '2023-09-20 04:51:54', '2023-09-20 04:51:54', 'null'),
('dad3b14f-1c84-4991-ad0f-91e9e802474e', NULL, 'user4', 'user4', 'user4@test.com', 'user4@test.com', 0, 'nseperv4yggsgc844os4wwkcw888okk', '$2y$13$BImCC5BhOL1fhGlNqJ2MuOcg6bSChTgrRJnHje1B8qv3wiVX7/Mhq', NULL, NULL, NULL, 'a:0:{}', 'Bruce', 'Dibbert', '2023-09-20 04:51:53', '2023-09-20 04:51:53', 'null');

-- --------------------------------------------------------

--
-- Table structure for table `user_metadata`
--

CREATE TABLE `user_metadata` (
  `id` int(11) NOT NULL,
  `user_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:uuid)',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_metadata`
--

INSERT INTO `user_metadata` (`id`, `user_id`, `name`, `value`) VALUES
(1, '05ceb243-11de-43db-ae21-f686f960333b', 'PROFILE_COMPLETED', 'false');

-- --------------------------------------------------------

--
-- Table structure for table `user_oauth`
--

CREATE TABLE `user_oauth` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:uuid)',
  `user_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:uuid)',
  `access_token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `identifier` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provider` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `refresh_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_permission`
--

CREATE TABLE `user_permission` (
  `user_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:uuid)',
  `permission_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:uuid)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_profile`
--

CREATE TABLE `user_profile` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:uuid)',
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_relation`
--

CREATE TABLE `user_relation` (
  `id` int(11) NOT NULL,
  `owner_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:uuid)',
  `relation_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:uuid)',
  `comment` int(11) NOT NULL,
  `confirmed` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_relation`
--

INSERT INTO `user_relation` (`id`, `owner_id`, `relation_id`, `comment`, `confirmed`, `created_at`, `updated_at`) VALUES
(2, '05ceb243-11de-43db-ae21-f686f960333b', 'bf543636-3eff-4c91-a530-4b75f2cecae4', 0, 'Yes', '2023-09-23 00:34:22', '2023-09-23 00:46:47');

-- --------------------------------------------------------

--
-- Table structure for table `user_role`
--

CREATE TABLE `user_role` (
  `user_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:uuid)',
  `role_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:uuid)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_role`
--

INSERT INTO `user_role` (`user_id`, `role_id`) VALUES
('05ceb243-11de-43db-ae21-f686f960333b', 'a6da9f5e-5976-11ee-b3e8-0242ac10ee0c'),
('bf543636-3eff-4c91-a530-4b75f2cecae4', 'a6da8488-5976-11ee-b3e8-0242ac10ee0c');

-- --------------------------------------------------------

--
-- Table structure for table `user_user`
--

CREATE TABLE `user_user` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:uuid)',
  `profile_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:uuid)',
  `username` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username_canonical` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_canonical` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `salt` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `password_reset_token` varchar(180) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password_requested_at` datetime DEFAULT NULL,
  `roles_array` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `flags` json DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `asset`
--
ALTER TABLE `asset`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_2AF5A5C12F7FB51` (`sponsor_id`),
  ADD KEY `IDX_2AF5A5CA6A2CDC5` (`asset_type_id`),
  ADD KEY `IDX_2AF5A5CDE12AB56` (`created_by`);

--
-- Indexes for table `asset_type`
--
ALTER TABLE `asset_type`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_68BA92E1989D9B62` (`slug`),
  ADD KEY `IDX_68BA92E1727ACA70` (`parent_id`),
  ADD KEY `IDX_68BA92E14E53B92C` (`is_asset`),
  ADD KEY `IDX_68BA92E1462CE4F5` (`position`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_5F9E962A3DA5256D` (`image_id`);

--
-- Indexes for table `galleries`
--
ALTER TABLE `galleries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_F70E6EB7A76ED395` (`user_id`);

--
-- Indexes for table `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_E01FBE6A4E7AF8F` (`gallery_id`);

--
-- Indexes for table `institution`
--
ALTER TABLE `institution`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_3A9F98E5DE12AB56` (`created_by`);

--
-- Indexes for table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  ADD KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  ADD KEY `IDX_75EA56E016BA31DB` (`delivered_at`);

--
-- Indexes for table `permission`
--
ALTER TABLE `permission`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `position`
--
ALTER TABLE `position`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_462CE4F55DA1941` (`asset_id`),
  ADD KEY `IDX_462CE4F58141FE3C` (`last_value_id`),
  ADD KEY `IDX_462CE4F510405986` (`institution_id`),
  ADD KEY `IDX_462CE4F5DE12AB56` (`created_by`);

--
-- Indexes for table `position_tag`
--
ALTER TABLE `position_tag`
  ADD PRIMARY KEY (`position_id`,`tag_id`),
  ADD KEY `IDX_F73FBF9BDD842E46` (`position_id`),
  ADD KEY `IDX_F73FBF9BBAD26311` (`tag_id`);

--
-- Indexes for table `position_value`
--
ALTER TABLE `position_value`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_91B7BCE93716CA3DD842E46` (`value_date`,`position_id`),
  ADD KEY `IDX_91B7BCE9DD842E46` (`position_id`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `role_permission`
--
ALTER TABLE `role_permission`
  ADD PRIMARY KEY (`role_id`,`permission_id`),
  ADD KEY `IDX_6F7DF886D60322AC` (`role_id`),
  ADD KEY `IDX_6F7DF886FED90CCA` (`permission_id`);

--
-- Indexes for table `sponsor`
--
ALTER TABLE `sponsor`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_818CC9D4DE12AB56` (`created_by`);

--
-- Indexes for table `tag`
--
ALTER TABLE `tag`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_389B783C865A29C` (`tag_group_id`),
  ADD KEY `IDX_389B783DE12AB56` (`created_by`);

--
-- Indexes for table `tag_group`
--
ALTER TABLE `tag_group`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_4F2C5DC3DE12AB56` (`created_by`);

--
-- Indexes for table `talav_settings`
--
ALTER TABLE `talav_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaction`
--
ALTER TABLE `transaction`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_723705D1DD842E46` (`position_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_8D93D64992FC23A8` (`username_canonical`),
  ADD UNIQUE KEY `UNIQ_8D93D649A0D96FBF` (`email_canonical`),
  ADD UNIQUE KEY `UNIQ_8D93D6496B7BA4B6` (`password_reset_token`),
  ADD UNIQUE KEY `UNIQ_8D93D649CCFA12B8` (`profile_id`);

--
-- Indexes for table `user_metadata`
--
ALTER TABLE `user_metadata`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_AF99D014A76ED395` (`user_id`);

--
-- Indexes for table `user_oauth`
--
ALTER TABLE `user_oauth`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_E618839CB6A2DD6892C4739C` (`access_token`,`provider`),
  ADD UNIQUE KEY `UNIQ_E618839CA76ED39592C4739C` (`user_id`,`provider`),
  ADD UNIQUE KEY `UNIQ_E618839C772E836A92C4739C` (`identifier`,`provider`),
  ADD KEY `IDX_E618839CA76ED395` (`user_id`);

--
-- Indexes for table `user_permission`
--
ALTER TABLE `user_permission`
  ADD PRIMARY KEY (`user_id`,`permission_id`),
  ADD KEY `IDX_472E5446A76ED395` (`user_id`),
  ADD KEY `IDX_472E5446FED90CCA` (`permission_id`);

--
-- Indexes for table `user_profile`
--
ALTER TABLE `user_profile`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_relation`
--
ALTER TABLE `user_relation`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_relation` (`owner_id`,`relation_id`),
  ADD KEY `owner_id` (`owner_id`),
  ADD KEY `IDX_8204A3493256915B` (`relation_id`);

--
-- Indexes for table `user_role`
--
ALTER TABLE `user_role`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `IDX_2DE8C6A3A76ED395` (`user_id`),
  ADD KEY `IDX_2DE8C6A3D60322AC` (`role_id`);

--
-- Indexes for table `user_user`
--
ALTER TABLE `user_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_F7129A8092FC23A8` (`username_canonical`),
  ADD UNIQUE KEY `UNIQ_F7129A80A0D96FBF` (`email_canonical`),
  ADD UNIQUE KEY `UNIQ_F7129A806B7BA4B6` (`password_reset_token`),
  ADD UNIQUE KEY `UNIQ_F7129A80CCFA12B8` (`profile_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `galleries`
--
ALTER TABLE `galleries`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `images`
--
ALTER TABLE `images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `talav_settings`
--
ALTER TABLE `talav_settings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_metadata`
--
ALTER TABLE `user_metadata`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_relation`
--
ALTER TABLE `user_relation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `asset`
--
ALTER TABLE `asset`
  ADD CONSTRAINT `FK_2AF5A5C12F7FB51` FOREIGN KEY (`sponsor_id`) REFERENCES `sponsor` (`id`),
  ADD CONSTRAINT `FK_2AF5A5CA6A2CDC5` FOREIGN KEY (`asset_type_id`) REFERENCES `asset_type` (`id`),
  ADD CONSTRAINT `FK_2AF5A5CDE12AB56` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`);

--
-- Constraints for table `asset_type`
--
ALTER TABLE `asset_type`
  ADD CONSTRAINT `FK_68BA92E1727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `asset_type` (`id`);

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `FK_5F9E962A3DA5256D` FOREIGN KEY (`image_id`) REFERENCES `images` (`id`);

--
-- Constraints for table `galleries`
--
ALTER TABLE `galleries`
  ADD CONSTRAINT `FK_F70E6EB7A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `images`
--
ALTER TABLE `images`
  ADD CONSTRAINT `FK_E01FBE6A4E7AF8F` FOREIGN KEY (`gallery_id`) REFERENCES `galleries` (`id`);

--
-- Constraints for table `institution`
--
ALTER TABLE `institution`
  ADD CONSTRAINT `FK_3A9F98E5DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`);

--
-- Constraints for table `position`
--
ALTER TABLE `position`
  ADD CONSTRAINT `FK_462CE4F510405986` FOREIGN KEY (`institution_id`) REFERENCES `institution` (`id`),
  ADD CONSTRAINT `FK_462CE4F55DA1941` FOREIGN KEY (`asset_id`) REFERENCES `asset` (`id`),
  ADD CONSTRAINT `FK_462CE4F58141FE3C` FOREIGN KEY (`last_value_id`) REFERENCES `position_value` (`id`),
  ADD CONSTRAINT `FK_462CE4F5DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`);

--
-- Constraints for table `position_tag`
--
ALTER TABLE `position_tag`
  ADD CONSTRAINT `FK_F73FBF9BBAD26311` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`id`),
  ADD CONSTRAINT `FK_F73FBF9BDD842E46` FOREIGN KEY (`position_id`) REFERENCES `position` (`id`);

--
-- Constraints for table `position_value`
--
ALTER TABLE `position_value`
  ADD CONSTRAINT `FK_91B7BCE9DD842E46` FOREIGN KEY (`position_id`) REFERENCES `position` (`id`);

--
-- Constraints for table `role_permission`
--
ALTER TABLE `role_permission`
  ADD CONSTRAINT `FK_6F7DF886D60322AC` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`),
  ADD CONSTRAINT `FK_6F7DF886FED90CCA` FOREIGN KEY (`permission_id`) REFERENCES `permission` (`id`);

--
-- Constraints for table `sponsor`
--
ALTER TABLE `sponsor`
  ADD CONSTRAINT `FK_818CC9D4DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`);

--
-- Constraints for table `tag`
--
ALTER TABLE `tag`
  ADD CONSTRAINT `FK_389B783C865A29C` FOREIGN KEY (`tag_group_id`) REFERENCES `tag_group` (`id`),
  ADD CONSTRAINT `FK_389B783DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`);

--
-- Constraints for table `tag_group`
--
ALTER TABLE `tag_group`
  ADD CONSTRAINT `FK_4F2C5DC3DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`);

--
-- Constraints for table `transaction`
--
ALTER TABLE `transaction`
  ADD CONSTRAINT `FK_723705D1DD842E46` FOREIGN KEY (`position_id`) REFERENCES `position` (`id`);

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `FK_8D93D649CCFA12B8` FOREIGN KEY (`profile_id`) REFERENCES `user_profile` (`id`);

--
-- Constraints for table `user_metadata`
--
ALTER TABLE `user_metadata`
  ADD CONSTRAINT `FK_AF99D014A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `user_oauth`
--
ALTER TABLE `user_oauth`
  ADD CONSTRAINT `FK_E618839CA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `user_permission`
--
ALTER TABLE `user_permission`
  ADD CONSTRAINT `FK_472E5446A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_472E5446FED90CCA` FOREIGN KEY (`permission_id`) REFERENCES `permission` (`id`);

--
-- Constraints for table `user_relation`
--
ALTER TABLE `user_relation`
  ADD CONSTRAINT `FK_8204A3493256915B` FOREIGN KEY (`relation_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_8204A3497E3C61F9` FOREIGN KEY (`owner_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `user_role`
--
ALTER TABLE `user_role`
  ADD CONSTRAINT `FK_2DE8C6A3A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_2DE8C6A3D60322AC` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`);

--
-- Constraints for table `user_user`
--
ALTER TABLE `user_user`
  ADD CONSTRAINT `FK_F7129A80CCFA12B8` FOREIGN KEY (`profile_id`) REFERENCES `user_profile` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
