<?php

namespace DigitalLeapAgency\ActiveCampaign\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactTag extends Model {
    use HasFactory;

    protected $table = 'active_contact_tags';

    protected $fillable = ['activeContactTagID', 'ContactID', 'TagID'];
}
