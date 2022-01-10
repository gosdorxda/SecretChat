<?php

use App\Message;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Message::class, 3)->create();
        factory(Message::class, 3)->create(['parent_id' => 1]);

        factory(Message::class, 3)->create(['user_id' => 2]);
        factory(Message::class, 3)->create(['user_id' => 2, 'parent_id' => 7]);
    }
}
