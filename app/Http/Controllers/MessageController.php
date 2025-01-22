<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    // মেসেজ পাঠানোর ফাংশন
    public function sendMessage(Request $request)
    {
        // ইনপুট ভ্যালিডেশন
        $request->validate([
            'receiver_id' => 'required|exists:users,id', // রিসিভারের ইউজার আইডি চেক
            'message' => 'required|string', // মেসেজের কন্টেন্ট চেক
            'parent_id' => 'nullable|exists:messages,id', // parent_id চেক (যদি রেপ্লাই হয়)
        ]);
    
        // মেসেজ ইনসার্ট করা
        Message::create([
            'sender_id' => auth()->id(), // লগইন করা ইউজারের ID
            'receiver_id' => $request->receiver_id, // রিসিভারের ID
            'message' => $request->message, // মেসেজ কন্টেন্ট
            'parent_id' => $request->parent_id, // রেপ্লাইয়ের জন্য parent_id
        ]);
    
        // সফলভাবে মেসেজ পাঠানোর পর মেসেজ পেজে রিডিরেক্ট করা
        return redirect()->route('messages.index', ['receiver_id' => $request->receiver_id]); // রিসিভারের ID সাথে রেখে রিডিরেক্ট
    }
    

    // মেসেজের লিস্ট এবং মেসেজ পাঠানোর পেজের জন্য
    // Controller method (MessageController.php)
public function index(Request $request)
{
    // receiver_id URL প্যারামিটার থেকে নেওয়া
    $receiver_id = $request->receiver_id;

    // আগের মেসেজের ইতিহাস দেখানো
    $messages = Message::where(function ($query) {
            $query->where('sender_id', auth()->id())
                  ->orWhere('receiver_id', auth()->id());
        })
        ->orderBy('created_at', 'desc') // মেসেজ সঠিক অর্ডারে দেখানোর জন্য
        ->get();

    // মেসেজ পাঠানো ও প্রাপ্ত ইউজারদের লিস্ট দেখানো
    $users = Message::where('sender_id', auth()->id())
                    ->orWhere('receiver_id', auth()->id())
                    ->get()
                    ->pluck('sender_id')
                    ->merge(Message::where('receiver_id', auth()->id())
                    ->pluck('receiver_id'))
                    ->unique()
                    ->filter(function($id) {
                        return $id != auth()->id(); // নিজের ID বাদ দেওয়া
                    })
                    ->map(function($id) {
                        return User::find($id); // ইউজার ইনফরমেশন নিয়ে আসা
                    });

    // মেসেজ পেজে রেন্ডার করা
    return view('allpage.messagemodal', compact('messages', 'users', 'receiver_id'));
}

}
