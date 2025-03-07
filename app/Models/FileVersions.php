<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileVersions extends Model
{
    use HasFactory;

    protected $table = 'file_versions'; // Explicitly defining the table name

    protected $fillable = [
        'file_id', // References the original file
        'version_number', // Versioning (e.g., 1.0, 1.1)
        'filename', // Name of the versioned file
        'file_path', // Storage path
        'file_size', // File size in bytes
        'file_type', // File type (e.g., pdf, docx)
        'uploaded_by', // User who uploaded the version
    ];

    // Relationship to the main File model
    public function file()
    {
        return $this->belongsTo(File::class, 'file_id');
    }

    // Relationship to the User who uploaded the version
    public function user()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
