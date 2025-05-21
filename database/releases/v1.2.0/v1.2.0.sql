
alter table `reports` add `meta` text null after `invoice_generate`

insert into `permissions` (`name`, `display_name`, `description`, `guard_name`, `updated_at`, `created_at`) values ('manage_calendar_view', 'Manage Calendar View', '<p>Able to access Setting tab.</p>', 'web', '2020-11-24 00:00:00', '2020-11-24 00:00:00');
