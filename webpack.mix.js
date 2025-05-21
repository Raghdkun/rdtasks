const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

/* Copy */

mix.copyDirectory('resources/assets/img', 'public/assets/img');

mix.copyDirectory('node_modules/@fortawesome/fontawesome-free/css',
    'public/assets/css/@fortawesome/fontawesome-free/css');
mix.copyDirectory('node_modules/@fortawesome/fontawesome-free/webfonts',
    'public/assets/css/@fortawesome/fontawesome-free/webfonts');
mix.copyDirectory('node_modules/simple-line-icons/css',
    'public/assets/css/simple-line-icons/css');
mix.copyDirectory('node_modules/simple-line-icons/fonts',
    'public/assets/css/simple-line-icons/fonts');
mix.copyDirectory('node_modules/summernote/dist/font',
    'public/assets/css/font');
mix.copyDirectory('node_modules/@simonwep/pickr/types',
    'public/color-pickr');

mix.copy('node_modules/quill/dist/quill.snow.css',
    'public/assets/style/css/quill.snow.css');
mix.copy('node_modules/izitoast/dist/css/iziToast.min.css',
    'public/assets/css/iziToast.min.css');
mix.copy('node_modules/select2/dist/css/select2.min.css',
    'public/assets/css/select2.min.css');
mix.copy('node_modules/sweetalert/dist/sweetalert.css',
    'public/assets/css/sweetalert.css');
mix.copy('node_modules/bootstrap/dist/css/bootstrap.min.css',
    'public/assets/css/bootstrap.min.css');
mix.copy('node_modules/summernote/dist/summernote.min.css',
    'public/assets/css/summernote.min.css');
mix.copy('node_modules/datatables.net-dt/css/jquery.dataTables.min.css',
    'public/assets/css/jquery.dataTables.min.css');
mix.copy('node_modules/datatables.net-dt/images', 'public/assets/images');
mix.copy('node_modules/daterangepicker/daterangepicker.css',
    'public/assets/css/daterangepicker.css');
mix.babel('node_modules/bootstrap-toggle/css/bootstrap-toggle.min.css',
    'public/assets/css/bootstrap-toggle.min.css');
mix.babel('node_modules/dropzone/dist/min/dropzone.min.css',
    'public/assets/css/dropzone.min.css');
mix.babel('node_modules/@simonwep/pickr/dist/themes/nano.min.css',
    'public/assets/css/nano.min.css');
mix.copy('node_modules/fullcalendar/dist/fullcalendar.min.css',
    'public/assets/css/fullcalendar.min.css');
mix.copy('node_modules/dragula/dist/dragula.css',
    'public/assets/css/dragula.css');

/* CSS */
mix.sass('resources/assets/style/sass/style.scss',
    'public/assets/style/css/style.css').
    sass('resources/assets/style/sass/dashboard.scss',
        'public/assets/style/css/dashboard.css').
    sass('resources/assets/style/sass/task-detail.scss',
        'public/assets/style/css/task-detail.css').
    sass('resources/assets/style/sass/report.scss',
        'public/assets/style/css/report.css').
    sass('resources/assets/style/sass/invoice-pdf.scss',
        'public/assets/style/css/invoice-pdf.css').
    sass('resources/assets/style/sass/infy-loader.scss',
        'public/assets/style/css/infy-loader.css').
    sass('resources/assets/style/sass/tasks.scss',
        'public/assets/style/css/tasks.css').
    sass('resources/assets/style/sass/kanban.scss',
        'public/assets/style/css/kanban.css').
    sass('resources/assets/style/sass/invoice-template.scss',
        'public/assets/style/css/invoice-template.css').
    sass('resources/assets/style/sass/login-page.scss',
        'public/assets/style/css/login-page.css').
    sass('resources/assets/style/sass/task-details-kanban.scss',
        'public/assets/style/css/task-details-kanban.css').
    sass('resources/assets/style/sass/project-details.scss',
        'public/assets/style/css/project-details.css').
    sass('resources/assets/style/sass/activity-logs.scss',
        'public/assets/style/css/activity-logs.css').
    sass('resources/assets/style/sass/events.scss',
        'public/assets/style/css/events.css').
    version();

/* Copy JS */
mix.babel('node_modules/jquery.nicescroll/dist/jquery.nicescroll.js',
    'public/assets/js/jquery.nicescroll.js');
mix.babel('node_modules/quill/dist/quill.min.js',
    'public/assets/js/quill.min.js');
mix.babel('node_modules/izitoast/dist/js/iziToast.min.js',
    'public/assets/js/iziToast.min.js');
mix.babel('node_modules/moment/min/moment.min.js',
    'public/assets/js/moment.min.js');
mix.babel('node_modules/select2/dist/js/select2.min.js',
    'public/assets/js/select2.min.js');
mix.babel('node_modules/sweetalert/dist/sweetalert.min.js',
    'public/assets/js/sweetalert.min.js');
mix.babel('node_modules/jquery/dist/jquery.min.js',
    'public/assets/js/jquery.min.js');
mix.babel('node_modules/popper.js/dist/umd/popper.min.js',
    'public/assets/js/popper.min.js');
mix.babel('node_modules/bootstrap/dist/js/bootstrap.min.js',
    'public/assets/js/bootstrap.min.js');
mix.babel('node_modules/summernote/dist/summernote.min.js',
    'public/assets/js/summernote.min.js');
mix.babel('node_modules/datatables.net/js/jquery.dataTables.min.js',
    'public/assets/js/jquery.dataTables.min.js');
mix.babel('node_modules/daterangepicker/daterangepicker.js',
    'public/assets/js/daterangepicker.js');
mix.babel('node_modules/moment/min/moment.min.js',
    'public/assets/js/moment.min.js');
mix.babel('node_modules/chart.js/dist/Chart.min.js',
    'public/assets/js/chart.min.js');
mix.babel('node_modules/dropzone/dist/min/dropzone.min.js',
    'public/assets/js/dropzone.min.js');
mix.babel('node_modules/ekko-lightbox/dist/ekko-lightbox.js',
    'public/assets/js/ekko-lightbox.js');
mix.babel('node_modules/@simonwep/pickr/dist/pickr.min.js',
    'public/assets/js/pickr.min.js');
mix.babel('node_modules/fullcalendar/dist/fullcalendar.min.js',
    'public/assets/js/fullcalendar.min.js');
mix.babel('node_modules/dragula/dist/dragula.js',
    'public/assets/js/dragula.js');
mix.babel('node_modules/dom-autoscroller/dist/dom-autoscroller.js',
    'public/assets/js/dom-autoscroller.js');
mix.babel('node_modules/push.js/bin/push.js',
    'public/assets/js/push.js');
mix.babel('node_modules/handlebars/dist/handlebars.js',
    'public/assets/js/handlebars.js');
mix.babel('node_modules/jsrender/jsrender.js',
    'public/assets/js/jsrender.js');
mix.copy('node_modules/moment/min/moment-with-locales.min.js',
    'public/assets/js/moment-with-locales.min.js')

/* JS */
mix.js('resources/assets/js/custom.js', 'public/assets/js/custom.js').
    js('resources/assets/js/time_tracker/time_tracker.js',
        'public/assets/js/time_tracker/time_tracker.js').
    js('resources/assets/js/users/user.js', 'public/assets/js/users/user.js').
    js('resources/assets/js/time_entries/time_entry.js',
        'public/assets/js/time_entries/time_entry.js').
    js('resources/assets/js/clients/client.js',
        'public/assets/js/clients/client.js').
    js('resources/assets/js/projects/project.js',
        'public/assets/js/projects/project.js').
    js('resources/assets/js/my_projects/my_project.js',
        'public/assets/js/my_projects/my_project.js').
    js('resources/assets/js/task/task.js', 'public/assets/js/task/task.js').
    js('resources/assets/js/projects/task/create-task.js', 'public/assets/js/projects/task/create-task.js').
    js('resources/assets/js/activity_types/activity.js',
        'public/assets/js/activity_types/activity.js').
    js('resources/assets/js/tags/tag.js', 'public/assets/js/tags/tag.js').
    js('resources/assets/js/report/report.js',
        'public/assets/js/report/report.js').
    js('resources/assets/js/report/report-show.js',
        'public/assets/js/report/report-show.js').
    js('resources/assets/js/dashboard/dashboard.js',
        'public/assets/js/dashboard/dashboard.js').
    js('resources/assets/js/dashboard/developers-daily-report.js',
        'public/assets/js/dashboard/developers-daily-report.js').
    js('resources/assets/js/task/task_detail.js',
        'public/assets/js/task/task_detail.js').
    js('resources/assets/js/profile/profile.js',
        'public/assets/js/profile/profile.js').
    js('resources/assets/js/roles/role.js', 'public/assets/js/roles/role.js').
    js('resources/assets/js/task/task_time_entry.js',
        'public/assets/js/task/task_time_entry.js').
    js('resources/assets/js/department/department.js',
        'public/assets/js/department/department.js').
    js('resources/assets/js/dashboard/users-open-tasks.js',
        'public/assets/js/dashboard/users-open-tasks.js').
    js('resources/assets/js/dashboard/users-project-status.js',
        'public/assets/js/dashboard/users-project-status.js').
    js('resources/assets/js/dashboard/clients-invoice-status.js',
        'public/assets/js/dashboard/clients-invoice-status.js').
    js('resources/assets/js/tax/tax.js',
        'public/assets/js/tax/tax.js').
    js('resources/assets/js/invoices/invoices.js',
        'public/assets/js/invoices/invoices.js').
    js('resources/assets/js/invoices/invoices-show.js',
        'public/assets/js/invoices/invoices-show.js').
    js('resources/assets/js/settings/setting.js',
        'public/assets/js/settings/setting.js').
    js('resources/assets/js/input_price_format.js',
        'public/assets/js/input_price_format.js').
    js('resources/assets/js/sidebar_menu_search/sidebar_menu_search.js',
        'public/assets/js/sidebar_menu_search/sidebar_menu_search.js').
    js('resources/assets/js/roles/create-edit.js',
        'public/assets/js/roles/create-edit.js').
    js('resources/assets/js/custom-datatable.js',
        'public/assets/js/custom-datatable.js').
    js('resources/assets/js/projects/show-edit.js',
        'public/assets/js/projects/show-edit.js').
    js('resources/assets/js/time_entries_calender/time_entries_calender.js',
        'public/assets/js/time_entries_calender/time_entries_calender.js').
    js('resources/assets/js/status/status.js',
        'public/assets/js/status/status.js').
    js('resources/assets/js/projects/kanban.js',
        'public/assets/js/projects/kanban.js').
    js('resources/assets/js/settings/invoice-template.js',
        'public/assets/js/settings/invoice-template.js').
    js('resources/assets/js/clients/profile/profile.js',
        'public/assets/js/clients/profile/profile.js').
    js('resources/assets/js/clients/dashboard/dashboard.js',
        'public/assets/js/clients/dashboard/dashboard.js').
    js('resources/assets/js/clients/dashboard/project-status.js',
        'public/assets/js/clients/dashboard/project-status.js').
    js('resources/assets/js/clients/dashboard/invoice-status.js',
        'public/assets/js/clients/dashboard/invoice-status.js').
    js('resources/assets/js/clients/invoice/invoices.js',
        'public/assets/js/clients/invoice/invoices.js').
    js('resources/assets/js/clients/projects/projects.js',
        'public/assets/js/clients/projects/projects.js').
    js('resources/assets/js/expense/expense.js',
        'public/assets/js/expense/expense.js').
    js('resources/assets/js/activity_logs/activity_logs.js',
        'public/assets/js/activity_logs/activity_logs.js').
    js('resources/assets/js/events/events.js',
        'public/assets/js/events/events.js').
    js('resources/assets/js/soft_delete/soft-delete.js',
        'public/assets/js/soft_delete/soft-delete.js').
    version();
