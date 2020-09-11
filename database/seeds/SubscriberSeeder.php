<?php

use Illuminate\Database\Seeder;
use App\Subscriber;

class SubscriberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Subscriber::class, 1000)->create();
    }
}
