<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $table = 'files';

    protected $primaryKey = 'file_id';

    protected $fillable = [
        'filename',
        'file_path',
        'file_size',
        'file_type',
        'uploaded_by',
        'category',
        'published_by',
        'year_published',
        'uploaded_by',
        'description',
        'status',
    ];

    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

}
