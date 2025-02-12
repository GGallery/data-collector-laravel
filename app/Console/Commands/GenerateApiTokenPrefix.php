<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ApiTokenPrefix;
use Illuminate\Support\Str;


class GenerateApiTokenPrefix extends Command
{
    protected $signature = 'generate:api-token-prefix {platform_name}';
    protected $description = 'Generate an API token for a platform';

    public function handle()
    {
        $platformName = $this->argument('platform_name');
        $prefixToken = Str::random(10);

        ApiTokenPrefix::create([
            'platform_name' => $platformName,
            'prefix_token' => $prefixToken,
        ]);

        $this->info("API token for {$platformName}: {$prefixToken}");
    }
}
