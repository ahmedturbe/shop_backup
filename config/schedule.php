<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Schedules
    |--------------------------------------------------------------------------
    |
    | Here you may list all of the classes that define your scheduled tasks.
    | These should be invokable classes or service providers that implement
    | a `schedule(Schedule $schedule)` method.
    |
    */

    'schedules' => [
        App\Schedule\BackupProductsSchedule::class,
    ],

];
