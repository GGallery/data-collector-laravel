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
        $platform_name = $this->argument('platform_name');
        $prefix_token = Str::random(10);

        ApiTokenPrefix::create([
            'platform_name' => $platform_name,
            'prefix_token' => $prefix_token,
        ]);

        $this->info("API token for {$platform_name}: {$prefix_token}");
    }
}
