<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ApiToken;
use Illuminate\Support\Str;


class GenerateApiToken extends Command
{
    protected $signature = 'generate:api-token {platform_name}';
    protected $description = 'Generate an API token for a platform';

    public function handle()
    {
        $platformName = $this->argument('platform_name');
        $token = Str::random(10);

        ApiToken::create([
            'platform_name' => $platformName,
            'token' => $token,
        ]);

        $this->info("API token for {$platformName}: {$token}");
    }
}
