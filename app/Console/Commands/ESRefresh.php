<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ESRefresh extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ES:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perintah Memperbarui data elastic search';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            Artisan::call('scout:import', ['model' => 'App\ProjectES']);
            Artisan::call('scout:import', ['model' => 'App\ConsultantES']);
            Artisan::call('scout:import', ['model' => 'App\DivisiES']);
            Artisan::call('scout:import', ['model' => 'App\KeywordES']);
            Artisan::call('scout:import', ['model' => 'App\ProjectManagerES']);

            $ch = curl_init();
            $headers  = [
                        'Content-Type: application/json',
                        'Accept: application/json',
                    ];
            $postData = [
                'properties' => [
                                    'nama' => [
                                        'type' => 'text',
                                        "fielddata" => true
                                    ]
                                ]
            ];
            curl_setopt($ch, CURLOPT_URL,config('app.ES_url').'/project/_mapping');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));           
            $result     = curl_exec ($ch);
            $hasil = json_decode($result);
            
            $this->info('Data ES Sudah Diperbarui');
        }catch (\Throwable $th) {
            $this->info('Refresh data ES Gagal');
        }
    }
}
