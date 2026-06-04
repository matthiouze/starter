<?php

use App\Console\Commands\DeleteLog;
use Illuminate\Support\Facades\Schedule;

Schedule::command(DeleteLog::class)->daily();
