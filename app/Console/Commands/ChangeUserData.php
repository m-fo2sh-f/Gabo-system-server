<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

#[Signature('app:change-user-data {id} {email} {password}')]
#[Description('Change user email and password by ID')]
class ChangeUserData extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = User::find($this->argument('id'));

        if (!$user) {
            $this->error('User not found!');
            return;
        }

        $user->email = $this->argument('email');
        $user->password = Hash::make($this->argument('password'));
        $user->save();

        $this->info('User data updated successfully!');
    }
}
