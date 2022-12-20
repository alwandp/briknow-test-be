<?php

namespace App\Console\Commands;

use App\Project;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ESReload extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ES:reload';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perintah Membersihkan Index dan Supply data terbaru elastic search';

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
      ini_set('memory_limit', '-1');
      ini_set('max_execution', 0);
        $cek    =   Project::where('flag_es', NULL)->count();
        // dd($cek);

        if ($cek > 0) {
            try{
                $projectCurrent = Project::where('flag_es', null);
              // Project::where('flag_es', null)->update(['flag_es' => 1]);
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

                $ch = curl_init();
                $headers  = [
                            'Content-Type: application/json',
                            'Accept: application/json',
                        ];
                $postData = [
                    'transient' => ['cluster.routing.allocation.disk.threshold_enabled' => false]
                ];
                curl_setopt($ch, CURLOPT_URL,config('app.ES_url').'/_cluster/settings');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));           
                $result     = curl_exec ($ch);
                $hasil = json_decode($result);

                $this->info('Clearing data ES Berhasil');
            }catch (\Throwable $th) {
                $this->info('Clearing data ES Gagal');
            }

            try {
		          Artisan::call('scout:import', ['model' => 'App\ProjectES']);
                //Artisan::call('scout:import', ['model' => 'App\ConsultantES']);
                //Artisan::call('scout:import', ['model' => 'App\DivisiES']);
                //Artisan::call('scout:import', ['model' => 'App\KeywordES']);
                //Artisan::call('scout:import', ['model' => 'App\ProjectManagerES']);

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

                // update project yang udah
		            $projectCurrent->update(['flag_es' => 1]);
                //Project::where('flag_es', null)->update(['flag_es' =>  1]);
            }catch (\Throwable $th) {
                $this->info('Refresh data ES Gagal');
            }
        }
    }
}
