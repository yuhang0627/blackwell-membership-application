<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('rewards:process-daily')->dailyAt('00:00');
