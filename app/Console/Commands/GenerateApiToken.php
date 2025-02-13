<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ApiTokenPrefix;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GenerateApiToken extends Command
{
    protected $signature = 'generate:api-token {platform_name}';
    protected $description = 'Generate an API token for a platform';

    public function handle()
    {
        $platform_name = $this->argument('platform_name');
        $prefix_token = Str::random(10);

        // Ottiene l'orario corrente dal database mysql
        $current_time = DB::selectOne('SELECT NOW() AS current_time')->current_time;


        $api_token_prefix = ApiTokenPrefix::create([
            'platform_name' => $platform_name,
            'prefix_token' => $prefix_token,
            'created_at' => $current_time,
            'updated_at' => $current_time,
        ]);

        // Viene usato created_at come parte dinamica del token
        $dynamic_part = Carbon::parse($current_time)->timestamp; // parse in formato Unix
        $combined_token = $prefix_token . $dynamic_part;
        
        $this->info("API token for {$platform_name}: {$combined_token}");
    }
}
