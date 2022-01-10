<?php

use App\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(User::class)->create(['username' => 'user', 'email' => 'saarizu@gmail.com']);

        factory(User::class)->create(['email_notification' => true]);
    }
}
