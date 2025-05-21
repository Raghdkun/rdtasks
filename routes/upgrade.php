<?php

use App\Models\Permission;
use App\Models\ProjectActivity;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/upgrade-to-v1-2-0', function () {
    $calendarViewPermission = Permission::whereName('manage_calendar_view')->first();
    $roles = Role::all();
    foreach ($roles as $role) {
        $role->givePermissionTo($calendarViewPermission);
    }

    return 'Permission assigned successfully.';
});

Route::get('/upgrade-to-v2-1-0', function () {
    $manageStatusPermission = Permission::whereName('manage_status')->first();
    $role = Role::whereName('Admin')->first();
    $role->givePermissionTo($manageStatusPermission);

    return 'Permission assigned successfully.';
});

Route::get('/client-role/upgrade-to-v2-1-0', function () {
    $clientRole = Role::whereName('Client')->first();

    $clients = \App\Models\Client::all();
    foreach ($clients as $client) {
        $client->roles()->sync([$clientRole->id]);
    }

    return 'Client role assigned successfully.';
});

// upgrade v2-3-0
Route::get('/upgrade-to-v2-3-0', function () {
    $softDeletedTags = \App\Models\Tag::onlyTrashed()->get();
    if (! empty($softDeletedTags)) {
        foreach ($softDeletedTags as $tags) {
            $tags->taskTags()->detach();
            $tags->forceDelete();
        }
    }
});

Route::get('/upgrade-to-v3-0-0', function () {
    Artisan::call('db:seed', ['--class' => 'AddExpensePermissionSeeder']);
});

Route::get('/upgrade-to-v3-1-0', function () {
    Artisan::call('db:seed', ['--class' => 'RemoveTaskEntryFromTaskTagTableSeeder']);
});

Route::get('/upgrade-to-v5-0-0', function () {
    Artisan::call('db:seed', ['--class' => 'AddActivityLogPermission']);
    Artisan::call('db:seed', ['--class' => 'AddEventPermissionSeeder']);
});

Route::get('/upgrade-to-v5-1-0', function () {
    Artisan::call('db:seed', ['--class' => 'AddRoleClientPermissionSeeder']);
});

Route::get('/upgrade-to-v5-2-0', function () {
    $userActivityLogIds = ProjectActivity::where('causer_type', User::class)->pluck('causer_id')->toArray();
    $userIds = User::pluck('id')->toArray();
    foreach ($userActivityLogIds as $userActivityLogId) {
        if (! in_array($userActivityLogId, $userIds)) {
            ProjectActivity::whereCauserId($userActivityLogId)->where('causer_type', User::class)->delete();
        }
    }
});

Route::get('/upgrade-to-v6-0-0', function () {
    \Illuminate\Support\Facades\Artisan::call('migrate',
        [
            '--force' => true,
            '--path' => 'database/migrations/2021_07_12_000000_add_uuid_to_failed_jobs_table.php',
        ]);
    \Illuminate\Support\Facades\Artisan::call('migrate',
        [
            '--force' => true,
            '--path' => 'database/migrations/2021_07_1_103036_add_conversions_disk_column_in_media_table.php',
        ]);
});

Route::get('/upgrade/database', function () {
    if (config('app.upgrade_mode')) {
        Artisan::call('migrate', [
            '--force' => true,
        ]);
    }
});
