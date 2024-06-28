<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\ChatGroup;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    // public function index(){
    //     return view('message.index');
    // }
    public function index() {
        $chatGroups = ChatGroup::with('messages.user')->get();
        return view('message.index', compact('chatGroups'));
    }

    public function sendMessage(Request $request) {
        $message = new Message();
        $message->user_id = auth()->id();
        $message->chat_group_id = $request->chat_group_id;
        $message->message = $request->message;
        $message->save();

        broadcast(new MessageSent($message))->toOthers();
        
        return redirect()->back();
    }
}
