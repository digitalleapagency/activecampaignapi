<?php

namespace DigitalLeapAgency\ActiveCampaign\Facades;

use Illuminate\Support\Facades\Facade;

class Event extends Facade {
    protected static function getFacadeAccessor() {
        return 'event';
    }
}