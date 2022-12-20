<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ESFlush extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ES:flush';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perintah Membersihkan Index data elastic search';

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
        try{
            $ch = curl_init();
            $headers  = [
                        'Content-Type: application/json',
                        'Accept: application/json',
                    ];
            curl_setopt($ch, CURLOPT_URL,config('app.ES_url').'/*');
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $result     = curl_exec ($ch);
            $hasil = json_decode($result);
            $this->info('Clearing data ES Berhasil');
        }catch (\Throwable $th) {
            $this->info('Clearing data ES Gagal');
        }
    }
}
