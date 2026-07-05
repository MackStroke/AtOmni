<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user (author/editor/admin) via interactive prompts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Create New User Account ===');

        $name = $this->ask('Full Name');
        
        $email = '';
        while (empty($email)) {
            $inputEmail = $this->ask('Email Address');
            $validator = Validator::make(['email' => $inputEmail], ['email' => 'required|email|unique:users,email']);
            
            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    $this->error($error);
                }
            } else {
                $email = $inputEmail;
            }
        }

        $password = '';
        while (empty($password)) {
            $inputPassword = $this->secret('Password (min 8 characters)');
            $inputPasswordConfirm = $this->secret('Confirm Password');

            if (strlen($inputPassword) < 8) {
                $this->error('Password must be at least 8 characters long!');
                continue;
            }

            if ($inputPassword !== $inputPasswordConfirm) {
                $this->error('Passwords do not match. Try again.');
            } else {
                $password = $inputPassword;
            }
        }

        // Available roles from the User model (super_admin, editor, author)
        $role = $this->choice(
            'Select System Role',
            ['author', 'editor', 'super_admin'],
            0 // default to author
        );

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => $role,
        ]);

        $this->info("✅ Success! User {$user->name} ({$user->email}) has been created with role '{$user->role}'.");
        $this->line("They can now log in at " . url('/login') . " to access the system.");

        return Command::SUCCESS;
    }
}
