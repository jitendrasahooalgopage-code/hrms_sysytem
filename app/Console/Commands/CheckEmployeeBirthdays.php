<?php

namespace App\Console\Commands;

use App\Models\Employee;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class CheckEmployeeBirthdays extends Command
{
    protected $signature = 'employees:check-birthdays';
    protected $description = 'Find employees whose birthday is today and cache the list for dashboard display';

    public function handle()
    {
        $today = Carbon::now();

        $birthdays = Employee::whereMonth('dob', $today->month)
            ->whereDay('dob', $today->day)
            ->get()
            ->map(function ($employee) {
                return [
                    'name'  => trim($employee->firstname . ' ' . $employee->lastname),
                    'email' => $employee->email,
                    'dob'   => $employee->dob,
                ];
            });

        // Cache until the end of the day so the dashboard can read it without re-querying
        Cache::put('todays_birthdays', $birthdays, now()->endOfDay());

        $this->info("Found {$birthdays->count()} birthday(s) today and cached the result.");

        return Command::SUCCESS;
    }
}