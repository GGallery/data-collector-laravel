<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ApiTokenPrefix;
use Illuminate\Support\Str;


class GenerateApiTokenPrefix extends Command
{
    protected $signature = 'generate:api-token {platform_name}';
    protected $description = 'Generate an API token for a platform';

    public function handle()
    {
        $platform_name = $this->argument('platform_name');
        $prefix_token = Str::random(10);

        $api_token_prefix = ApiTokenPrefix::create([
            'platform_name' => $platform_name,
            'prefix_token' => $prefix_token,
        ]);
        
        // Viene usato created_at come parte dinamica del token
        $dynamic_part = $api_token_prefix->created_at->format('YmdHis');
        $combined_token = $prefix_token . $dynamic_part;
        
        $this->info("API token for {$platform_name}: {$combined_token}");
    }
}
