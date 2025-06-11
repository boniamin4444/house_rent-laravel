<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Ensure DB facade is imported

class MessageController extends Controller
{
    /**
     * Show the message inbox.
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $currentUserId = Auth::id();
        $receiverId = $request->input('receiver_id'); // GET parameter for selected user

        // বর্তমান ব্যবহারকারীর সাথে চ্যাট করা অনন্য ব্যবহারকারী আইডিগুলো পান
        $chattedUserIds = Message::select('sender_id')
            ->where('receiver_id', $currentUserId)
            ->union( // দুটি ক্যোয়ারি একত্রিত করুন
                Message::select('receiver_id')
                    ->where('sender_id', $currentUserId)
            )
            ->distinct() // শুধু অনন্য আইডিগুলো রাখুন
            ->pluck('sender_id') // একটি কলাম থেকে আইডিগুলো বের করুন (sender_id বা receiver_id যে কোনো একটি হতে পারে)
            ->filter(fn ($id) => $id != $currentUserId); // বর্তমান ব্যবহারকারীকে তালিকা থেকে বাদ দিন

        // চ্যাট করা ব্যবহারকারীদের User মডেলগুলো আইডি অনুসারে সাজিয়ে আনুন
        $users = User::whereIn('id', $chattedUserIds)
                     ->orderBy('name')
                     ->get();

        $messages = collect(); // মেসেজগুলোর জন্য একটি খালি কালেকশন শুরু করুন

        if ($receiverId) {
            // বর্তমান ব্যবহারকারী এবং নির্বাচিত রিসিভারের মধ্যে মেসেজগুলো আনুন
            $messages = Message::with(['sender', 'receiver']) // প্রেরক এবং প্রাপকের সম্পর্ক লোড করুন
                                ->where(function ($query) use ($currentUserId, $receiverId) {
                                    $query->where('sender_id', $currentUserId)->where('receiver_id', $receiverId);
                                })->orWhere(function ($query) use ($currentUserId, $receiverId) {
                                    $query->where('sender_id', $receiverId)->where('receiver_id', $currentUserId);
                                })->orderBy('created_at', 'asc') // চ্যাট ক্রনোলজিক্যাল অর্ডারে সাজান
                                ->get();
        }

        return view('allpage.messagemodal', compact('messages', 'users', 'receiverId'));
    }

    /**
     * Send a new message via AJAX.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000', // সর্বোচ্চ দৈর্ঘ্য যোগ করা হয়েছে
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        // তৈরি করা মেসেজ এবং এর সম্পর্কগুলো (sender, receiver) JSON প্রতিক্রিয়া হিসেবে ফেরত দিন
        return response()->json([
            'status' => 'success',
            'message' => $message->load(['sender', 'receiver']), // sender এবং receiver সম্পর্ক লোড করুন
            'sender_name' => Auth::user()->name, // ফ্রন্টএন্ডে প্রেরকের নাম ব্যবহারের জন্য
        ]);
    }

    /**
     * Fetch messages for a specific conversation via AJAX.
     * This endpoint is called periodically by JavaScript to get new messages.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchMessages(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'last_message_id' => 'nullable|exists:messages,id', // কেবল নতুন মেসেজ আনার জন্য
        ]);

        $currentUserId = Auth::id();
        $receiverId = $request->receiver_id;
        $lastMessageId = $request->last_message_id;

        $query = Message::with(['sender', 'receiver']) // প্রেরক এবং প্রাপকের সম্পর্ক লোড করুন
                        ->where(function ($q) use ($currentUserId, $receiverId) {
                            $q->where('sender_id', $currentUserId)->where('receiver_id', $receiverId);
                        })->orWhere(function ($q) use ($currentUserId, $receiverId) {
                            $q->where('sender_id', $receiverId)->where('receiver_id', $currentUserId);
                        });

        if ($lastMessageId) {
            // ক্লায়েন্ট দ্বারা প্রাপ্ত শেষ মেসেজের চেয়ে নতুন মেসেজ আনুন
            $lastMessageCreatedAt = Message::find($lastMessageId)->created_at;
            $query->where('created_at', '>', $lastMessageCreatedAt)
                  ->where('id', '!=', $lastMessageId); // একই টাইমস্ট্যাম্পের ক্ষেত্রে শেষ মেসেজ বাদ দিন
        }

        $messages = $query->orderBy('created_at', 'asc') // কালানুক্রমিক অর্ডারে সাজান
                          ->get();

        return response()->json(['messages' => $messages]);
    }
}