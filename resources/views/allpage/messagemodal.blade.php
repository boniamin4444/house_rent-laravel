@extends('layouts.app')



<style>
    body {
        font-family: 'Arial', sans-serif;
    }

    .message-box {
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 15px;
        position: relative;
    }

    /* Separate sender and receiver styles */
    .sender-message {
        background-color: #f1f1f1;
        text-align: left;
        margin-left: 50px;
    }

    .receiver-message {
        background-color: #d1e7ff;
        text-align: right;
        margin-right: 50px;
    }

    /* Style the message container with maximum height and scroll */
    .user-messages {
        max-height: 300px;
        overflow-y: auto;
        margin-bottom: 15px;
        padding-right: 10px;
    }

    /* Messages for specific users hidden initially */
    .messages-for-user {
        display: none;
    }

    /* Layout for message container */
    .message-container {
        display: flex;
        justify-content: space-between;
        gap: 30px;
    }

    /* Left side for user list */
    .message-list {
        width: 30%;
        padding-right: 20px;
    }

    /* Right side for message details */
    .message-detail {
        width: 65%;
    }

    /* Hover effect for user list */
    .list-group-item {
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .list-group-item:hover {
        background-color: #f0f0f0;
    }

    /* Last message style */
    .last-message {
        font-size: 0.9rem;
        color: #888;
        margin-top: 10px;
    }

    /* Input styling */
    .form-group label {
        font-weight: bold;
    }

    .form-control {
        border-radius: 8px;
    }

    .btn-primary {
        border-radius: 20px;
        padding: 8px 20px;
    }

    .card-body {
        padding: 20px;
    }

    .card-title {
        font-size: 1.25rem;
        font-weight: bold;
    }

    /* Responsive design for mobile */
    @media (max-width: 768px) {
        .message-container {
            flex-direction: column;
        }

        .message-list,
        .message-detail {
            width: 100%;
        }
    }
</style>
@section('content')
<div class="container mt-4">
    <div class="message-container">
        <!-- List of users you have messaged -->
        <div class="message-list">
            <h3 class="text-center mb-4">Users you've messaged:</h3>
            <div class="list-group">
                @foreach($users as $user)
                    <a href="javascript:void(0)" class="list-group-item list-group-item-action user-message-toggle" data-user-id="{{ $user->id }}">
                        {{ $user->name }}
                        <div class="last-message">
                            <!-- Display the last message for each user -->
                            @php
                                $lastMessage = \App\Models\Message::where('sender_id', $user->id)
                                    ->orWhere('receiver_id', $user->id)
                                    ->latest()
                                    ->first();
                            @endphp
                            @if($lastMessage)
                                {{ $lastMessage->message }}
                            @else
                                No messages yet
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Message history, initially hidden, shown after user click -->
        <div class="message-detail" id="messages-container">
            @foreach($users as $user)
                <div class="messages-for-user" id="messages-{{$user->id}}" style="{{ $receiver_id == $user->id ? 'display: block;' : 'display: none;' }}">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Messages with {{ $user->name }}</h5>
                            <!-- Show messages for the selected user -->
                            <div class="user-messages">
                                @foreach($messages as $message)
                                    @if($message->sender_id == $user->id || $message->receiver_id == $user->id)
                                        <div class="message-box {{ $message->sender_id == auth()->id() ? 'sender-message' : 'receiver-message' }}">
                                            <p class="mb-0">{{ $message->message }}</p>
                                        </div>
                                    @endif
                                @endforeach
                            </div>

                            <!-- Message sending form -->
                            <form action="{{ route('messages.send') }}" method="POST" class="mb-4">
                                @csrf
                                <input type="hidden" name="receiver_id" value="{{ $user->id }}">

                                <div class="form-group">
                                    <textarea name="message" id="message" class="form-control" required placeholder="Type your message..."></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary mt-2">Send Message</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Bootstrap JS and dependencies CDN -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<!-- JavaScript to toggle the message container -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get all the user toggle links
        const userMessageToggles = document.querySelectorAll('.user-message-toggle');

        // Show the selected user's message container
        userMessageToggles.forEach(function(toggle) {
            toggle.addEventListener('click', function() {
                const userId = toggle.getAttribute('data-user-id');
                const messageContainer = document.getElementById('messages-' + userId);

                // Hide all message containers
                const allMessageContainers = document.querySelectorAll('.messages-for-user');
                allMessageContainers.forEach(function(container) {
                    container.style.display = 'none';
                });

                // Show the selected user's message container
                if (messageContainer) {
                    messageContainer.style.display = messageContainer.style.display === 'none' ? 'block' : 'block';
                }
            });
        });
    });
</script>

@endsection
