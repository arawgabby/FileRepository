<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Files extends Model
{
    use HasFactory;

    protected $table = 'files';

    protected $primaryKey = 'file_id';

    protected $fillable = [
        'file_id',
        'filename',
        'file_path',
        'file_size',
        'file_type',
        'authors',
        'uploaded_by',
        'category',
        'published_by',
        'year_published',
        'description',
        'status',
    ];

    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function folder()
    {
        return $this->belongsTo(Folder::class, 'uploaded_by', 'id');
    }
}
