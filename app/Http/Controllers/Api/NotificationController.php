<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function indexNegotiateStatus()
    {
        $notifications = Auth::user()->notifications()->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'payload' => $notifications
        ]);
    }
}
