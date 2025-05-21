alter table `settings` add `group` int not null default '1' after `value`;

insert into `settings` (`key`, `value`, `group`, `updated_at`, `created_at`) values ('default_invoice_template', 'defaultTemplate', 2, '2020-12-11 00:00:00', '2020-12-11 00:00:00');

insert into `settings` (`key`, `value`, `group`, `updated_at`, `created_at`) values ('default_invoice_color', '#040404', 2, '2020-12-11 00:00:00', '2020-12-11 00:00:00');
