<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileVersions extends Model
{
    use HasFactory;

    protected $table = 'file_versions'; 
    protected $primaryKey = 'version_id'; 

    public $timestamps = true; // Ensure timestamps are enabled

    protected $fillable = [
        'file_id',
        'version_number',
        'filename',
        'file_path',
        'file_size',
        'file_type',
        'uploaded_by',
        'status',
    ];

    // Relationship to the main File model
    public function file()
    {
        return $this->belongsTo(Files::class, 'file_id');
    }

    // Relationship to the User who uploaded the version
    public function user()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
    
}
