<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ApiTokenPrefix;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Crypt;

require_once app_path('Helpers/EncryptionHelper.php');

class GenerateApiToken extends Command
{
    protected $signature = 'generate:api-token {platform_name}';
    protected $description = 'Generate an API token for a platform';

    public function handle()
    {
        $platform_name = $this->argument('platform_name');
        $prefix_token = Str::random(10);

        // Ottiene l'orario corrente dal database mysql in formato Unix
        $current_time = DB::selectOne('SELECT UNIX_TIMESTAMP(NOW())')->{'UNIX_TIMESTAMP(NOW())'};


        ApiTokenPrefix::create([
            'platform_name' => $platform_name,
            'prefix_token' => $prefix_token,
            'created_at' => DB::raw('FROM_UNIXTIME(' . $current_time . ')'),
            'updated_at' => DB::raw('FROM_UNIXTIME(' . $current_time . ')'),
        ]);

        // Viene usato il timestamp Unix come parte dinamica del token
        $dynamic_part = $current_time;
        $combined_token = $prefix_token . $dynamic_part;

        // Cripta il token combinato
        $secret_key = env('SECRET_KEY');
        $secret_iv = env('SECRET_IV');
        $encrypted_token = \App\Helpers\EncryptionHelper::encryptDecrypt($combined_token, $secret_key, $secret_iv, 'encrypt');

        $this->info("API token for {$platform_name}: {$encrypted_token}");
    }
}