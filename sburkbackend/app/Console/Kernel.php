<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

use App\Http\Traits\SwitchPlans;

class Kernel extends ConsoleKernel
{
    use SwitchPlans;
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $this->checkSubscriptions();
            //Delete soft deleted parents after 3 days
            $this->deleteSoftDeletedParents();
            $this->deleteSoftDeletedDrivers();
        })->daily();
    }
    
    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    public function deleteSoftDeletedParents()
    {
        $parents = \App\Parent_::onlyTrashed()->where('deleted_at', '<=', \Carbon\Carbon::now()->subDays(3))->get();
        foreach ($parents as $parent) {
            $parent->forceDelete();
        }
    }

    public function deleteSoftDeletedDrivers()
    {
        $drivers = \App\Driver::onlyTrashed()->where('deleted_at', '<=', \Carbon\Carbon::now()->subDays(3))->get();
        foreach ($drivers as $driver) {
            $driver->forceDelete();
        }
    }
}
