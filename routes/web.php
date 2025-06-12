<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\ActivityTypeController;
use App\Http\Controllers\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Client;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EventsController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SoftDeleteUser;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\TimeEntryCalenderController;
use App\Http\Controllers\TimeEntryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TaskRatingController;
use App\Http\Controllers\UserNotificationController;
use App\Http\Controllers\ClientRatingController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('login');
});

/*
|--------------------------------------------------------------------------
| Auth Login Route
|--------------------------------------------------------------------------
*/
//Auth::routes();
\Illuminate\Support\Facades\Auth::routes(['verify' => true, 'register' => false]);
Route::get('login', [Auth\LoginController::class, 'showLoginForm'])->name('login');

Route::post('login', [Auth\LoginController::class, 'login']);

Route::get('activate', [AuthController::class, 'verifyAccount']);

Route::post('set-password', [AuthController::class, 'setPassword']);

Route::get('tracker-notification', [HomeController::class, 'trackerNotification'])->name('tracker.notification.command');

// Add this in the authenticated middleware group
Route::middleware('auth', 'validate.user', 'xss', 'user.activated')->group(function () {
    Route::get('/dashboard', [HomeController::class, 'index'])->name('home');
    Route::get('/dashboard-total-hours', [HomeController::class, 'getTotalHours'])->name('dashboard-total-hours');
    Route::get('/users-work-report', [HomeController::class, 'workReport'])->name('users-work-report');
    Route::get('/developer-work-report', [HomeController::class, 'developerWorkReport'])->name('developers-work-report');
    Route::get('/users-open-tasks', [HomeController::class, 'userOpenTasks'])->name('users-open-tasks');
    Route::get('/users-project-status', [HomeController::class, 'userProjectStatus'])->name('users-project-status');
    Route::get('/client-invoices-status', [HomeController::class, 'clientInvoiceStatus'])->name('client-invoices-status');

    Route::post('logout', [Auth\LoginController::class, 'logout']);
    Route::middleware('permission:manage_activities')->group(function () {
        Route::resource('activity-types', ActivityTypeController::class);
    });

    Route::get('user-assign-projects', [ProjectController::class, 'userAssignProjects'])->name('user.projects');
    Route::get('user-assign-projects/{project}', [ProjectController::class, 'userAssignProjectsShow'])->name('user.projects.show');

    Route::middleware('permission:manage_clients')->group(function () {
        Route::resource('clients', ClientController::class);
        Route::post('clients/{client}/update', [ClientController::class, 'update'])->name('clients-update');
        Route::post('client/store', [ClientController::class, 'storeClients'])->name('client.store');
    });

    Route::middleware('permission:manage_users')->group(function () {
        Route::post('users/{user}/active-de-active', [UserController::class, 'activeDeActiveUser'])
            ->name('active-de-active-user');
        Route::resource('users', UserController::class)->parameters(['users' => 'user']);
        Route::post('users/{id}/restore', [UserController::class, 'restoreUser'])->name('user.restore');
        Route::delete('users/{user}/delete', [UserController::class, 'UserDelete'])->name('user.force-delete');
        Route::post('users/{user}/update', [UserController::class, 'update'])->name('users-update');
        Route::get('users/{user}/send-email', [UserController::class, 'resendEmailVerification'])->name('send-email');
    });

    Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::post('users/profile-update', [UserController::class, 'profileUpdate'])->name('user-update-profile');
    Route::post('users/change-password', [UserController::class, 'changePassword'])->name('user-change-password');
    Route::post('update-language', [UserController::class, 'updateLanguage'])->name('update-language');
    Route::resource('notifications', NotificationController::class);

    Route::middleware('permission:manage_tags')->group(function () {
        Route::resource('tags', TagController::class);
    });

//    Route::group(['middleware' => ['permission:manage_projects']], function () {
    Route::resource('projects', ProjectController::class);
    Route::post('projects/{project}/add-attachment',
        [ProjectController::class, 'addAttachment'])->name('projects.add-attachment');
    Route::post('projects/{media}/delete-attachment', [ProjectController::class, 'deleteAttachment'])
        ->name('project.delete-attachment');
    Route::get('projects/{project}/get-attachments',
        [ProjectController::class, 'getAttachment'])->name('projects.attachments');
    Route::get('projects/media/{id}', [ProjectController::class, 'downloadAttachment'])->name('download-attachment');


//    });
    // tasks routes
    Route::middleware('permission:manage_all_tasks')->group(function () {
        Route::resource('tasks', TaskController::class);
        Route::post('tasks/{task}/update-status', [TaskController::class, 'updateStatus'])->name('task.update-status');
        Route::post('tasks/{task}/add-attachment',
            [TaskController::class, 'addAttachment'])->name('task.add-attachment');
        Route::post('tasks/{media}/delete-attachment', [TaskController::class, 'deleteAttachment'])
            ->name('task.delete-attachment');
        Route::get('tasks/{task}/get-attachments', [TaskController::class, 'getAttachment'])->name('task.attachments');
        Route::post('tasks/{task}/comments', [CommentController::class, 'addComment'])->name('task.comments');
        Route::post(
            'tasks/{task}/comments/{comment}/update',
            [CommentController::class, 'editComment']
        )->name('task.update-comment');
        Route::delete(
            'tasks/{task}/comments/{comment}',
            [CommentController::class, 'deleteComment']
        )->name('task.delete-comment');
        Route::get('task-details/{task}', [TaskController::class, 'getTaskDetails'])->name('task.get-details');
        Route::get('tasks/{task}/comments-count', [TaskController::class, 'getCommentsCount'])->name('task.comments-count');
        Route::get('tasks/{task}/users', [TaskController::class, 'getTaskUsers'])->name('task.users');
        Route::get('tasks/{task}/edit-assignees', [TaskController::class, 'editAssignee'])->name('task.edit-assignee');
        Route::get('tasks/{task}/kanban-edit', [TaskController::class, 'editKanbanTask'])->name('task.kanban-edit');
        Route::post('tasks/{task}/kanban-edit', [TaskController::class, 'updateKanbanTask'])->name('task.kanban-update');
        Route::get('kanban-task-details/{task}', [TaskController::class, 'getKanbanTaskDetails'])->name('kanban-task-details');
        Route::post('kanban-task-details/{task}/task-edit', [TaskController::class, 'updateKanbanTaskDetails'])->name('kanban-task-details-update');
        Route::post('kanban-task-details/{task}/add-attachment',
            [TaskController::class, 'addAttachmentTaskDetails'])->name('kanban-task.add-attachment');
        Route::get('users-by-project/{project}', [TaskController::class, 'getUserByProject'])->name('users-by-project');
        Route::get('tasks/{user}/project-by-users', [TaskController::class, 'getProjectsByUser'])->name('project-by-users');
    });

    Route::middleware('permission:manage_status')->group(function () {
        Route::get('status', [StatusController::class, 'index'])->name('status.index');
        Route::get('order', [StatusController::class, 'orderNumber'])->name('status.orderNumber');
        Route::post('status', [StatusController::class, 'store'])->name('status.store');
        Route::get('status/{status}/edit', [StatusController::class, 'edit'])->name('status.edit');
        Route::put('status/{status}', [StatusController::class, 'update'])->name('status.update');
        Route::delete('status/{status}', [StatusController::class, 'destroy'])->name('status.destroy');
    });
    Route::resource('time-entries', TimeEntryController::class);
    Route::get('task-time-entry', [TimeEntryController::class, 'taskTimeEntryFilter'])->name('task-time-entry');
    Route::post('time-entries/{time_entry}/update', [TimeEntryController::class, 'update']);
    Route::get('time-entries/{time_entry}', [TimeEntryController::class, 'showTimeEntryNote']);
    Route::post('start-timer', [TimeEntryController::class, 'getStartTimer'])->name('start-timer');
    Route::get('copy-today-activity', [TimeEntryController::class, 'copyTodayActivity'])->name('copy-today-activity');

    Route::middleware('permission:manage_reports')->group(function () {
        Route::resource('reports', ReportController::class);
        Route::post('reports/preview', [ReportController::class, 'showPreview'])->name('reports.preview');
        Route::get('reports-users-of-projects', [ReportController::class, 'projectUsers'])->name('reports.users-of-projects');
    });
    Route::get('users-of-projects', [ProjectController::class, 'users'])->name('users-of-projects');
    Route::get('projects-of-client', [ClientController::class, 'projects'])->name('projects-of-client');
    Route::get('clients-of-department', [DepartmentController::class, 'clients'])->name('clients-of-department');

    Route::get('my-tasks', [TaskController::class, 'myTasks'])->name('my-tasks');
    Route::get('archived-users', [SoftDeleteUser::class, 'index'])->name('archived-users');
    Route::delete('archived-users/{user}', [SoftDeleteUser::class, 'destroy'])->name('destroy-user');
    Route::get('user-last-task-work', [TimeEntryController::class, 'getUserLastTask'])->name('user-last-task-work');
    Route::get('projects/{project}/tasks', [TimeEntryController::class, 'getTasks'])->name('project-tasks');
    Route::put('tasks/{task}/update-task-status', [TaskController::class, 'updateTaskStatus'])->name('tasks.update-status');
    Route::get('my-projects', [ProjectController::class, 'getMyProjects'])->name('my-projects');
    Route::get('get-user-lists', [UserController::class, 'getUserLists'])->name('get-user-lists');
    Route::get('tasks-kanban', [TaskController::class, 'getKanbanTasks'])->name('kanban.index');
    Route::get('login-user-projects', [ProjectController::class, 'getLoginUsersProjects'])->name('projects.login.user');
    Route::post('tracker/task', [TaskController::class, 'storeTrackerTask'])->name('tracker.task.store');

    Route::middleware('permission:manage_roles')->group(function () {
        Route::resource('roles', RoleController::class);
    });

    Route::middleware('permission:manage_department')->group(function () {
        Route::resource('departments', DepartmentController::class);
        Route::post('departments/{department}/update', [DepartmentController::class, 'update'])->name('departments-update');
    });

    Route::middleware('permission:manage_time_entries')->group(function () {
        Route::get('projects/{user}/users', [ProjectController::class, 'getProjectsByUser'])->name('users-project');
        Route::get('projects/{project}/project-users', [TimeEntryController::class, 'getUsers'])->name('project-users');
    });

    Route::middleware('permission:manage_calendar_view')->group(function () {
        Route::get('time-entries-calendar', [TimeEntryCalenderController::class, 'index'])->name('time-entries-calendar.index');
        Route::get('time-entries-calendar-list', [TimeEntryCalenderController::class, 'timeEntriesCalendarList'])
            ->name('time-entries-calendar-list');
    });

    Route::middleware('permission:manage_taxes')->group(function () {
        Route::get('taxes', [TaxController::class, 'index'])->name('taxes.index');
        Route::post('taxes', [TaxController::class, 'store'])->name('taxes.store');
        Route::get('taxes/{tax}/edit', [TaxController::class, 'edit'])->name('taxes.edit');
        Route::put('taxes/{tax}', [TaxController::class, 'update'])->name('taxes.update');
        Route::delete('taxes/{tax}', [TaxController::class, 'destroy'])->name('taxes.destroy');
    });

    Route::middleware('permission:manage_invoices')->group(function () {
        Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::post('invoices', [InvoiceController::class, 'store'])->name('invoices.store');
        Route::put('invoices/{invoice}', [InvoiceController::class, 'update'])->name('invoices.update');
        Route::get('invoices/{invoice}/edit', [InvoiceController::class, 'edit'])->name('invoices.edit');
        Route::delete('invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');
        Route::get('invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
        Route::get('invoices/{project}/tasks', [InvoiceController::class, 'getProjectTasks'])->name('get.project-tasks');
        Route::get('invoices/{id}/details', [InvoiceController::class, 'getTaskDetails'])->name('get.task-details');
        Route::get('invoices/{client}/projects', [InvoiceController::class, 'getClientProjects'])->name('get.client-projects');
        Route::get('invoice-download/{fkanban}', [InvoiceController::class, 'downloadInvoice']);
        Route::post('invoices/{invoice}/update-status', [InvoiceController::class, 'updateStatus'])->name('invoices-update-status');
        Route::get('generate-invoice/{report}', [InvoiceController::class, 'createInvoice'])->name('invoices.generate');
    });

    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'convertToPdf'])->name('invoices.pdf');

    Route::middleware('permission:manage_expenses')->group(function () {
        Route::resource('expenses', ExpenseController::class);
        Route::get('expenses-project/{client}', [ExpenseController::class, 'getProjects'])->name('expenses.project');
        Route::post('expenses/{media}/delete-attachment',
            [ExpenseController::class, 'deleteAttachment'])->name('expense.delete-attachment');
        Route::get('expenses/{media}/download',
            [ExpenseController::class, 'downloadAttachment'])->name('expenses.download.attachment');
    });

    Route::middleware('permission:manage_settings')->group(function () {
        Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
        Route::post('invoice-settings', [SettingController::class, 'invoiceSettingUpdate'])->name('invoice-settings.settings');
        Route::post('google-recaptcha', [SettingController::class, 'googleRecaptchaUpdate'])->name('google-recaptcha.settings');
    });

    Route::get('tasks/media/{id}', [TaskController::class, 'downloadAttachment'])->name('download-attachment');

    Route::middleware('permission:manage_activity_log')->group(function () {
        Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs');
        Route::delete('activity-logs/{id}', [ActivityLogController::class, 'destroy'])->name('activity-delete');
    });
    Route::get('events', [EventsController::class, 'index'])->name('events.index');
    Route::post('events', [EventsController::class, 'store'])->name('events.store');
    Route::get('events/{event}/edit', [EventsController::class, 'edit'])->name('events.edit');
    Route::post('events/{event}/update', [EventsController::class, 'update'])->name('events.update');
    Route::post('events/{event}/drop-update', [EventsController::class, 'dropUpdate'])->name('events.drop.update');
    Route::get('events-data', [EventsController::class, 'getEventsData'])->name('events.data');
    Route::delete('events/{event}', [EventsController::class, 'destroy'])->name('events.delete');

    //Header Notification
    Route::get('/get-notifications', [UserNotificationController::class, 'index']);
    Route::post('/notification/{notification}/read',
        [UserNotificationController::class, 'readNotification'])->name('notifications.read');
    Route::post('/read-all-notification',
        [UserNotificationController::class, 'readAllNotification'])->name('notifications.read.all');
});

Route::middleware('auth', 'xss', 'user.activated',
    'validate.user')->prefix('client')->group(function () {
    //dashboard
    Route::get('dashboard', [Client\DashboardController::class, 'index'])->name('dashboard');

    Route::get('{id}/edit-profile', [Client\ClientController::class, 'edit'])->name('client-edit-profile');
    Route::post('profile-update', [Client\ClientController::class, 'profileUpdate'])->name('update-profile');
    Route::post('change-password', [Client\ClientController::class, 'changePassword'])->name('change-password');
    Route::get('project-status', [Client\DashboardController::class, 'projectStatus'])->name('project-status');
    Route::get('client-invoices', [Client\DashboardController::class, 'getClientInvoices'])->name('client-invoices');

    Route::get('invoices', [Client\InvoiceController::class, 'index'])->name('client.invoices.index');
    Route::get('invoices/{invoice}', [Client\InvoiceController::class, 'show'])->name('client.invoices.show');
    Route::post('invoices/{invoice}/change-status', [Client\InvoiceController::class, 'changeStatus'])->name('invoices.change-status');
    Route::get('invoices/{invoice}/edit', [Client\InvoiceController::class, 'edit'])->name('client.invoices.edit');

    Route::get('projects', [Client\ProjectController::class, 'index'])->name('client.projects.index');
    Route::get('projects/{project}', [Client\ProjectController::class, 'show'])->name('client.projects.show');

    Route::get('get-notifications', [Client\DashboardController::class, 'getNotifications'])->name('client.notifications.index');
    Route::post('notification/{notification}/read',
        [Client\DashboardController::class, 'readNotification'])->name('client.notifications.read');
    Route::post('read-all-notification',
        [Client\DashboardController::class, 'readAllNotification'])->name('client.notifications.read.all');
});

Route::fallback(function () {
    abort(\Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND);
});

// Add these routes to your existing web.php file

// Team KPI Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/team-kpi', [App\Http\Controllers\TeamKpiController::class, 'index'])->name('team-kpi.index');
    Route::get('/team-kpi/export', [App\Http\Controllers\TeamKpiController::class, 'export'])->name('team-kpi.export');
});

Route::middleware(['auth', 'role:Admin'])->group(function () {
    Route::get('/task-ratings', [TaskRatingController::class, 'index'])->name('task-ratings.index');
    Route::get('/task-ratings/{task}/create', [TaskRatingController::class, 'create'])->name('task-ratings.create');
    Route::post('/task-ratings/{task}', [TaskRatingController::class, 'store'])->name('task-ratings.store');
    Route::get('/task-ratings/{task}/edit', [TaskRatingController::class, 'edit'])->name('task-ratings.edit');
    Route::put('/task-ratings/{task}', [TaskRatingController::class, 'update'])->name('task-ratings.update');
    Route::get('/task-ratings/{task}', [TaskRatingController::class, 'show'])->name('task-ratings.show');
});

Route::get('task-reports', [App\Http\Controllers\TaskReportController::class, 'index'])->name('task.reports');
Route::get('task-reports/export', [App\Http\Controllers\TaskReportController::class, 'exportCsv'])->name('task.report.export');

// Client Ratings Routes
Route::middleware(['auth'])->group(function () {
    Route::resource('client-ratings', ClientRatingController::class);
});

// Public client rating view (no auth required)
Route::get('client/{client}/ratings/public/{token?}', [ClientRatingController::class, 'publicView'])
    ->name('client-ratings.public');

// Profile routes - Add these
Route::get('/my-profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('profile.index');
Route::get('/profile/{user}', [App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
Route::get('/my-dashboard', [App\Http\Controllers\UserDashboardController::class, 'index'])->name('user-dashboard.index');

// Add these routes
Route::get('/ratings', [App\Http\Controllers\RatingsController::class, 'index'])->name('ratings.index');
Route::get('/ratings/task/{task}/details', [App\Http\Controllers\RatingsController::class, 'getTaskRatingDetails'])->name('ratings.task.details');
Route::get('/task-ratings/{task}/popup', [App\Http\Controllers\TaskRatingController::class, 'getPopupContent'])->name('task-ratings.popup');

// Client rating routes
Route::get('/client-ratings', [App\Http\Controllers\ClientRatingController::class, 'index'])->name('client-ratings.index');
Route::get('/client-ratings/create', [App\Http\Controllers\ClientRatingController::class, 'create'])->name('client-ratings.create');
Route::post('/client-ratings', [App\Http\Controllers\ClientRatingController::class, 'store'])->name('client-ratings.store');
Route::get('/client-ratings/{clientRating}/edit', [App\Http\Controllers\ClientRatingController::class, 'edit'])->name('client-ratings.edit');
Route::put('/client-ratings/{clientRating}', [App\Http\Controllers\ClientRatingController::class, 'update'])->name('client-ratings.update');
// Add this line after the existing user-dashboard route
Route::get('/my-dashboard/rating-rules', [App\Http\Controllers\UserDashboardController::class, 'ratingRules'])->name('user-dashboard.rating-rules');
