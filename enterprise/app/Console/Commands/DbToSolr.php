<?php

namespace App\Console\Commands;

use App\Models\SolrModel;
use Illuminate\Console\Command;

class DbToSolr extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravel:dbtosolr';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return mixed
     */
    public function handle()
    {

        $solrModel = new SolrModel;
        $effect    = $solrModel->updateGame();
        $this->info('game ' . $effect);
        $effect = $solrModel->updateVideo();
        $this->info('video ' . $effect);
        $effect = $solrModel->updateTop();
        $this->info('top ' . $effect);
        //02:01:00 updateMerchantGame when product online
        if (getenv('LARAVEL_APP_ENV') || (date("H") == "02" && date("i") == "01")) {
            $effect = $solrModel->updateMerchantGame();
            $this->info('merchant ' . $effect);
        }
        $effect = $solrModel->updateVronlineArticle();
        $this->info('vronline article ' . $effect);
        $effect = $solrModel->updateVronlineGame();
        $this->info('vronline game ' . $effect);
        $this->info('complete' . date("Y:m:d H:i:s"));

    }
}
