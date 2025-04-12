<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FolderAccess extends Model
{
    // Specify the table name if needed
    protected $table = 'folder_access';

    // Mass assignable fields
    protected $fillable = [
        'folder_id', 
        'user_id', 
        'assigned_by', 
        'status',
    ];

    public function folder()
    {
        return $this->belongsTo(Folder::class, 'folder_id');
    }



}
