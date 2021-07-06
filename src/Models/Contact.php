<?php

namespace DigitalLeapAgency\ActiveCampaign\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model {
    use HasFactory;

    protected $table = "active_contacts";

    protected $fillable = ['activeContactID', 'firstName', 'lastName', 'email', 'phone_number'];
}
