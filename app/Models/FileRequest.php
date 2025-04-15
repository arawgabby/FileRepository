<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileRequest extends Model
{
    use HasFactory;

    protected $table = 'file_requests'; // Define table name explicitly

    protected $primaryKey = 'request_id'; // Set the primary key

    public $timestamps = true; // Enable timestamps

    protected $fillable = [
        'file_id',
        'requested_by',
        'request_status',
        'note',
    ];

    public function file()
    {
        return $this->belongsTo(Files::class, 'file_id', 'file_id');
    }

    // Relationship to User Model
    public function user()
    {
        return $this->belongsTo(User::class, 'requested_by', 'id');
    }
    
}
