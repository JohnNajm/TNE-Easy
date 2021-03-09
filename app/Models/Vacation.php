<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vacation extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id' , 'status', 'resolved_by',
        'startDate', 'endDate'
    ];
    
    protected $casts = [
        'startDate' => 'datetime',
        'endDate' => 'datetime',
    ];
    
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    function resolved($id)
    {
        $resolved = User::where('id', '=', $id)->firstOrFail();
        
        return $resolved;
    }
}
