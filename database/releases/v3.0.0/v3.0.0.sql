   create table `expenses` (`id` bigint unsigned not null auto_increment primary key, `description` text not null, `amount` int not null, `date` datetime not null, `project_id` int unsigned not null, `client_id` int unsigned not null, `category` int not null default '1', `billable` tinyint(1) not null default '0', `created_by` int unsigned null, `deleted_by` int unsigned null, `created_at` timestamp null, `updated_at` timestamp null, `deleted_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci';
 
    alter table `expenses` add constraint `expenses_client_id_foreign` foreign key (`client_id`) references `clients` (`id`);

    alter table `expenses` add constraint `expenses_project_id_foreign` foreign key (`project_id`) references `projects` (`id`);

    alter table `expenses` add constraint `expenses_created_by_foreign` foreign key (`created_by`) references `users` (`id`);

    alter table `expenses` add constraint `expenses_deleted_by_foreign` foreign key (`deleted_by`) references `users` (`id`);
		   
