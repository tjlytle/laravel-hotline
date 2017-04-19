<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Nexmo\Client;
use Nexmo\Application\Application;
use Nexmo\Numbers\Number;

class LinkApp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nexmo:link:app {number} {app}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Link a Number to an Application';

    /**
     * @var Client
     */
    protected $client;

    /**
     * Create a new command instance.
     *
     * @param Client $client
     * @return void
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $application = new Application($this->argument('app'));
        $number = new Number($this->argument('number'));

        $number->setVoiceDestination($application);

        try{
            $this->info('Making API Request');
            $this->client->numbers()->update($number);
            $this->info('Linked Number to Application');
        } catch (\Exception $e) {
            $this->error('Request Failed: ' . $e->getMessage());
            $this->error($e->getTraceAsString(), \Symfony\Component\Console\Output\OutputInterface::VERBOSITY_DEBUG);
        }
    }
}
