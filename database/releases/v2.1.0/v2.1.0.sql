create table `status` (`id` int unsigned not null auto_increment primary key, `status` int not null, `name` varchar(255) not null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci';

alter table `status` add unique `status_status_unique`(`status`);

alter table `status` add unique `status_name_unique`(`name`);

alter table `clients` add `user_id` int null;

alter table `users` add `owner_id` int null, add `owner_type` varchar(255) null;

insert into `permissions` (`name`, `display_name`, `description`, `guard_name`, `updated_at`, `created_at`) values ('manage_status', 'Manage Status', '<p>Able to access Setting tab.</p>', 'web', '2020-12-19 00:00:00', '2020-12-19 00:00:00');

UPDATE users SET email_verified_at = '2020-12-19 13:11:23' WHERE is_email_verified = 1 AND email_verified_at is null;

-- Seeder
insert into `status` (`status`, `name`, `updated_at`, `created_at`) values ('0', 'Pending', '2020-12-19 00:00:00', '2020-12-19 00:00:00');
insert into `status` (`status`, `name`, `updated_at`, `created_at`) values ('1', 'Completed', '2020-12-19 00:00:00', '2020-12-19 00:00:00');

-- Create a client role
insert into `roles` (`name`, `display_name`, `description`, `guard_name`, `updated_at`, `created_at`) values ('Client', 'Client', '<p>Client</p>', 'web', '2020-12-19 00:00:00', '2020-12-19 00:00:00')


