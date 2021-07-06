<?php

namespace DigitalLeapAgency\ActiveCampaign\Facades;

use Illuminate\Support\Facades\Facade;

class Contact extends Facade {
    protected static function getFacadeAccessor() {
        return 'contact';
    }
}