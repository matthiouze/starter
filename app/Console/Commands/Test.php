<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

#[Signature('cmd:test')]
#[Description('Commande pour tester des choses')]
class Test extends Command
{
    public function handle(): void
    {
        dd(Hash::make('azeaze'));
    }
}
