<?php

namespace DigitalLeapAgency\ActiveCampaign\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model {
    use HasFactory;

    protected $table = "active_events";

    protected $fillable = ['event'];
}
