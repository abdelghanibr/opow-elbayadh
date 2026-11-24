<?php

namespace App\Http\Controllers;

use App\Models\UserMessage;
use Illuminate\Support\Facades\Auth;

class UserMessageController extends Controller
{
    /** Marque un message comme lu (uniquement les siens). */
    public function read(int $message)
    {
        UserMessage::where('id', $message)->where('user_id', Auth::id())->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back()->with('open_messages', true);
    }

    public function readAll()
    {
        UserMessage::where('user_id', Auth::id())->whereNull('read_at')->update(['read_at' => now()]);

        return back()->with('open_messages', true);
    }
}
