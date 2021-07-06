<?php

namespace DigitalLeapAgency\ActiveCampaign\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactEvent extends Model {
    use HasFactory;

    protected $table = "active_contact_events";

    protected $fillable = ['ContactID', 'event', 'eventdata'];
}
