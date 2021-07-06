<?php

namespace DigitalLeapAgency\ActiveCampaign\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallActiveCampaign extends Command {
    protected $signature = 'activecampaign:install';

    protected $description = 'Install the ActiveCampaign';

    public function handle() {
        $this->info('Installing ActiveCampaign...');

        $this->info('Publishing configuration...');

        if (!$this->configExists('activecampaign.php')) {
            $this->publishConfiguration();
            $this->info('Published configuration');
        } else {
            if ($this->shouldOverwriteConfig()) {
                $this->info('Overwriting configuration file...');
                $this->publishConfiguration($force = true);
            } else {
                $this->info('Existing configuration was not overwritten');
            }
        }

        $this->info('Installed ActiveCampaign');
    }

    private function configExists($fileName) {
        return File::exists(config_path($fileName));
    }

    private function shouldOverwriteConfig() {
        return $this->confirm(
            'Config file already exists. Do you want to overwrite it?',
            false
        );
    }

    private function publishConfiguration($forcePublish = false) {
        $params = [
            '--provider' => "DigitalLeapAgency\ActiveCampaign\ActiveCampaignServiceProvider",
            '--tag' => "config",
        ];

        if ($forcePublish === true) {
            $params['--force'] = '';
        }

        $this->call('vendor:publish', $params);
    }
}