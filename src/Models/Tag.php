<?php

namespace DigitalLeapAgency\ActiveCampaign\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model {
    use HasFactory;

    protected $table = 'active_tags';

    protected $fillable = ['activeTagID', 'tag', 'tagType', 'description'];
}
