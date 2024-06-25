<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResultsTestsLatency extends Model
{
    use HasFactory;
    protected $table = 'result_latency_test';
    protected $fillable = ['id_team', 'date', 'response', 'description'];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
