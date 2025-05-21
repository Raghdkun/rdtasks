 create table `notifications` (`id` bigint unsigned not null auto_increment primary key, `user_id` int unsigned not null, `first_notification_hour` varchar(255) null, `second_notification_hour` varchar(255) null, `third_notification_hour` varchar(255) null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci';

 alter table `notifications` add constraint `notifications_user_id_foreign` foreign key (`user_id`) references `users` (`id`) on delete cascade on update cascade;

 create table `activity_log` (`id` bigint unsigned not null auto_increment primary key, `log_name` varchar(255) null, `description` text not null, `subject_type` varchar(255) null, `subject_id` bigint unsigned null, `causer_type` varchar(255) null, `causer_id` bigint unsigned null, `properties` json null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci';

alter table `activity_log` add index `subject`(`subject_type`, `subject_id`);

alter table `activity_log` add index `causer`(`causer_type`, `causer_id`);

alter table `activity_log` add index `activity_log_log_name_index`(`log_name`);

create table `events` (`id` bigint unsigned not null auto_increment primary key, `title` varchar(255) null, `description` text null, `start_date` datetime null, `end_date` datetime null, `type` bigint unsigned null, `added_by` int unsigned null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci';

alter table `events` add constraint `events_added_by_foreign` foreign key (`added_by`) references `users` (`id`);

alter table `tasks` add `estimate_time` varchar(255) null, add `estimate_time_type` bigint unsigned null;
