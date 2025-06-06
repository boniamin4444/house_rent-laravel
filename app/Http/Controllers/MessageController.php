<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function sendMessage(Request $request)
    {

        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string', 
            'parent_id' => 'nullable|exists:messages,id', 
        ]);
    

        Message::create([
            'sender_id' => auth()->id(), 
            'receiver_id' => $request->receiver_id,
            'message' => $request->message, 
            'parent_id' => $request->parent_id, 
        ]);
    
        return redirect()->route('messages.index', ['receiver_id' => $request->receiver_id]);
    }
    

 public function index(Request $request)
   {

    $receiver_id = $request->receiver_id;
    $messages = Message::where(function ($query) {
            $query->where('sender_id', auth()->id())
                  ->orWhere('receiver_id', auth()->id());
        })
        ->orderBy('created_at', 'desc') 
        ->get();

    $users = Message::where('sender_id', auth()->id())
                    ->orWhere('receiver_id', auth()->id())
                    ->get()
                    ->pluck('sender_id')
                    ->merge(Message::where('receiver_id', auth()->id())
                    ->pluck('receiver_id'))
                    ->unique()
                    ->filter(function($id) {
                        return $id != auth()->id();
                    })
                    ->map(function($id) {
                        return User::find($id);
                    });
    return view('allpage.messagemodal', compact('messages', 'users', 'receiver_id'));
}

}
