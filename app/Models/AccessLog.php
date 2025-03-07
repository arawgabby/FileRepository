<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessLog extends Model
{
    use HasFactory;

    protected $table = 'access_logs'; // Define the table name

    protected $fillable = [
        'file_id',
        'accessed_by',
        'action',
        'access_time'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'accessed_by');
    }

    public function file()
    {
        return $this->belongsTo(File::class, 'file_id');
    }
}
