create table `user_notifications` (`id` bigint unsigned not null auto_increment primary key, `title` varchar(255) null, `type` varchar(255) null, `description` text null, `read_at` timestamp null, `user_id` int unsigned null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci';

alter table `user_notifications` add constraint `user_notifications_user_id_foreign` foreign key (`user_id`) references `users` (`id`) on delete cascade on update cascade;
