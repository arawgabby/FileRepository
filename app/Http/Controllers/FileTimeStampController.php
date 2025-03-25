<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FileTimeStamp;
use App\Models\FileVersions;

class FileTimeStampController extends Controller
{
    public function ViewIndex()
    {
        $timestamps = FileTimeStamp::with(['file', 'fileVersion'])->paginate(10); // Paginate with 10 records per page
        return view('staff.pages.ViewTimeStamps', compact('timestamps'));
    }    

    public function show($file_id)
    {
        // Fetch all timestamps related to the given file_id
        $timestamps = FileTimeStamp::where('file_id', $file_id)->get();

        // Pass data to the ViewTimeStampsDetails.blade.php
        return view('staff.pages.ViewTimeStampsDetails', compact('timestamps', 'file_id'));
    }

}
