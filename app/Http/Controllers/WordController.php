<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpWord\IOFactory;

class WordController extends Controller
{
    public function showOrEdit($file_id)
    {
        $file = \App\Models\Files::findOrFail($file_id);

        $ext = strtolower(pathinfo($file->file_path, PATHINFO_EXTENSION));
        if (!in_array($ext, ['docx', 'doc'])) {
            return back()->with('error', 'Not a Word document.');
        }

        $filePath = storage_path('app/public/' . $file->file_path);
        $phpWord = \PhpOffice\PhpWord\IOFactory::load($filePath);

        // Convert to HTML to retain formatting
        $htmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'HTML');
        ob_start();
        $htmlWriter->save('php://output');
        $html = ob_get_clean();

        return view('admin.pages.WordShowEdit', compact('file', 'html'));
    }

    public function showOrEdit2($file_id)
    {
        $file = \App\Models\Files::findOrFail($file_id);

        $ext = strtolower(pathinfo($file->file_path, PATHINFO_EXTENSION));
        if (!in_array($ext, ['docx', 'doc'])) {
            return back()->with('error', 'Not a Word document.');
        }

        $filePath = storage_path('app/public/' . $file->file_path);
        $phpWord = \PhpOffice\PhpWord\IOFactory::load($filePath);

        // Convert to HTML to retain formatting
        $htmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'HTML');
        ob_start();
        $htmlWriter->save('php://output');
        $html = ob_get_clean();

        return view('staff.pages.WordShowEdit', compact('file', 'html'));
    }
}
