<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Nexmo\Client;
use Nexmo\Application\Application;
use Nexmo\Application\VoiceConfig;

class AppCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nexmo:app:create {name} {answer_url} {event_url} {--type=voice} {--answer_method} {--event_method} {--keyfile=nexmo.key}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Nexmo Application';

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
        $application = new Application();
        $application->setName($this->argument('name'));

        $config = new VoiceConfig();
        $config->setWebhook(VoiceConfig::ANSWER, $this->argument('answer_url'), $this->option('answer_method'));
        $config->setWebhook(VoiceConfig::EVENT,  $this->argument('event_url'),  $this->option('event_method'));

        $application->setVoiceConfig($config);

        try {
            $this->info('Making API Request');
            $application = $this->client->applications()->post($application);
            file_put_contents($this->option('keyfile'), $application->getPrivateKey());
            $this->info('Key Saved To: ' . realpath($this->option('keyfile')));
            $this->info('Created Application: ' . $application->getId());
        } catch (\Exception $e) {
            $this->error('Request Failed: ' . $e->getMessage());
            $this->error($e->getTraceAsString(), \Symfony\Component\Console\Output\OutputInterface::VERBOSITY_DEBUG);
        }
    }
}
