<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\FileRequest;
use Illuminate\Support\Facades\Auth;

class ViewServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::composer('*', function ($view) {
            $user = session('user'); // Get user data from session

            if ($user) {
                $userId = $user->id; // Extract user ID from session
                $pendingRequestCount = FileRequest::where('requested_by', $userId)
                                                  ->where('request_status', 'pending')
                                                  ->count();
            } else {
                $pendingRequestCount = 0;
            }
            
            // Pass the count to all views
            $view->with('pendingRequestCount', $pendingRequestCount);
        });
    }
}
