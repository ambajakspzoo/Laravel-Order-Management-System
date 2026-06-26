<?php

use Database\Seeders\OmsSeeder;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('app:seed', function () {
    $this->call('db:seed', ['--class' => OmsSeeder::class, '--force' => true]);
})->purpose('Seed the database with sample OMS data');
