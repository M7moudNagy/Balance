<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Massage;
use Illuminate\Http\Request;

class MassageController extends Controller
{
    public function sendMessage(Request $request)
    {
        $data = $request->validate([
            'sender_id' => 'required|int',
            'sender_type' => 'required|in:doctor,patient',
            'receiver_id' => 'required|int',
            'receiver_type' => 'required|in:doctor,patient',
            'message' => 'required|string',
        ]);

        $message = Massage::create($data);

        broadcast(new MessageSent($message))->toOthers(); // البث هنا

        return response()->json(['message' => 'Message sent', 'data' => $message]);
    }

}
