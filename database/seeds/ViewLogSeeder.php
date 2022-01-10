<?php

use App\ViewLog;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ViewLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(ViewLog::class)->create([
            'ip' => '192.168.0.1',
            'view_at' => Carbon::now()->subDays(1)->toDate()
        ]);
        factory(ViewLog::class)->create(['view_at' => Carbon::now()->subDays(1)->toDate()]);
        factory(ViewLog::class)->create(['view_at' => Carbon::now()->subDays(2)->toDate()]);

        factory(ViewLog::class)->create([
            'user_id' => 2,
            'ip' => '192.168.0.1',
            'view_at' => Carbon::now()->subDays(1)->toDate()
        ]);
        factory(ViewLog::class)->create([
            'user_id' => 2,
            'view_at' => Carbon::now()->subDays(2)->toDate()
        ]);
    }
}
