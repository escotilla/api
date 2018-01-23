<?php
namespace App\Console\Commands;

use App\Factories\UserFactory;
use App\Role;
use App\User;
use Illuminate\Console\Command;

class BootstrapRolesCommand extends Command
{
    protected $signature = 'bootstrap:roles {password}';

    protected $description = 'Bootstraps database with roles';

    public function handle() {
        $admin = Role::where('name', 'admin')->first();

        if (is_null($admin)) {
            $admin = new Role(['name' => 'admin']);
            $admin->save();
        }

        if (is_null(Role::where('name', 'applicant')->first())) {
            $applicant = new Role(['name' => 'applicant']);
            $applicant->save();
        }

        $email = env('ADMIN_EMAIL');

        $password = $this->argument('password');

        if (!is_null(User::where('email', $email)->first())) {
            $this->output->writeln('Failed');
            return;
        }

        $adminUser = UserFactory::newUser($email, $password);

        $adminUser->role()->associate($admin);
        $adminUser->save();

        $this->output->writeln('Created admin user for ' . $email);
    }
}