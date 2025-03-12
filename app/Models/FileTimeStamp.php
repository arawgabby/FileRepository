<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileTimeStamp extends Model
{
    use HasFactory;

    protected $table = 'file_time_stamps';
    protected $primaryKey = 'timestamp_id';
    public $timestamps = false;

    protected $fillable = [
        'file_id',
        'version_id',
        'event_type',
        'timestamp',
        'recorded_at',
    ];

    public function file()
    {
        return $this->belongsTo(File::class, 'file_id');
    }

    public function fileVersion()
    {
        return $this->belongsTo(FileVersion::class, 'version_id');
    }
}
