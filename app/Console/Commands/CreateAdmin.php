<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CreateAdmin extends Command
{
    protected $signature = 'app:create-admin
                            {email : Login email}
                            {--name=Admin : Display name}
                            {--password= : Password (omit to be prompted, which keeps it out of shell history)}';

    protected $description = 'Create an admin account, or promote an existing user to admin';

    public function handle(): int
    {
        $email = (string) $this->argument('email');
        $existing = User::where('email', $email)->first();

        $password = $this->option('password') ?: $this->secret('Password (min 8 characters)');

        $validator = Validator::make(
            ['email' => $email, 'password' => $password],
            ['email' => ['required', 'email'], 'password' => ['required', Password::defaults()]]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $message) {
                $this->error($message);
            }

            return self::FAILURE;
        }

        if ($existing) {
            $existing->forceFill(['role_id' => User::ROLE_ADMIN, 'password' => $password])->save();
            $this->info("Promoted {$email} to admin and set a new password.");

            return self::SUCCESS;
        }

        // username/phone are required by the website's forms but not by the database
        $user = new User([
            'name' => (string) $this->option('name'),
            'email' => $email,
            'password' => $password,
        ]);
        $user->forceFill(['role_id' => User::ROLE_ADMIN, 'email_verified_at' => now()])->save();

        $this->info("Created admin {$email}.");

        return self::SUCCESS;
    }
}
