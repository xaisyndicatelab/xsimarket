<?php

namespace Xsimarket\Console;

use Illuminate\Console\Command;
use Xsimarket\Database\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Xsimarket\Enums\Permission as UserPermission;
use Illuminate\Support\Facades\Validator;


class InstallCommand extends Command
{
    protected $signature = 'xsimarket:install';

    protected $description = 'Installing Xsimarket Dependencies';

    public function handle()
    {

        $this->info('Installing Xsimarket Dependencies...');
        if ($this->confirm('Do you want to migrate Tables? If you have already run this command or migrated tables then be aware, it will erase all of your data.')) {
            $this->info('Migrating Tables Now....');

            $this->call('migrate:fresh');

            $this->info('Tables Migration completed.');

            if ($this->confirm('Do you want to seed dummy data?')) {

                $this->call('xsimarket:seed');
            }
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::firstOrCreate(['name' => UserPermission::SUPER_ADMIN]);
        Permission::firstOrCreate(['name' => UserPermission::CUSTOMER]);
        Permission::firstOrCreate(['name' => UserPermission::STORE_OWNER]);
        Permission::firstOrCreate(['name' => UserPermission::STAFF]);

        try {
            if ($this->confirm('Do you want to create an admin?')) {

                $this->info('Provide admin credentials info to create an admin user for you.');
                $name = $this->ask('Enter admin name');
                $email = $this->ask('Enter admin email');
                $password = $this->secret('Enter your admin password');
                $confirmPassword = $this->secret('Enter your password again');

                $this->info('Please wait, Creating an admin profile for you...');
                $validator = Validator::make(
                    [
                        'name' =>  $name,
                        'email' =>  $email,
                        'password' =>  $password,
                        'confirmPassword' =>  $confirmPassword,
                    ],
                    [
                        'name'     => 'required|string',
                        'email'    => 'required|email|unique:users,email',
                        'password' => 'required',
                        'confirmPassword' => 'required|same:password',
                    ]
                );
                if ($validator->fails()) {
                    $this->info('User not created. See error messages below:');
                    foreach ($validator->errors()->all() as $error) {
                        $this->error($error);
                    }
                    return;
                }
                $user = User::create([
                    'name' =>  $name,
                    'email' =>  $email,
                    'password' =>  Hash::make($password),
                ]);
                $user->givePermissionTo(
                    [
                        UserPermission::SUPER_ADMIN,
                        UserPermission::STORE_OWNER,
                        UserPermission::CUSTOMER,
                    ]
                );
                $this->info('User Creation Successful!');
            }
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        $this->call('xsimarket:copy-files');
    }
}
