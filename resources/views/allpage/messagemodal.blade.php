@extends('layouts.app')

@section('content')

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>লাইভ মেসেজ ইনবক্স</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <style>
        /* Base Font Family */
        body {
            font-family: 'Inter', sans-serif;
        }

        /* Container for larger screens */
        @media (min-width: 992px) { /* Bootstrap's 'lg' breakpoint for medium devices */
            .container-wrapper {
                max-width: 960px;
                margin-left: auto;
                margin-right: auto;
            }
        }

        /* Hide scrollbar for chat messages */
        .chat-messages::-webkit-scrollbar {
            display: none; /* For Chrome, Safari, Opera */
        }
        .chat-messages {
            -ms-overflow-style: none; /* For IE and Edge */
            scrollbar-width: none; /* For Firefox */
            scroll-behavior: smooth; /* Smooth scrolling for new messages */
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            height: calc(100vh - 180px); /* Adjust height based on header and input */
            padding-bottom: 1rem;
        }

        /* Styling for sent messages */
        .message-bubble.sent {
            background-color: #e2e8f0; /* Light gray background */
            align-self: flex-end; /* Align to the right */
            margin-left: auto; /* Push to the right */
        }

        /* Styling for received messages */
        .message-bubble.received {
            background-color: #d1fae5; /* Light green background */
            align-self: flex-start; /* Align to the left */
            margin-right: auto; /* Push to the left */
        }

        /* Custom border-radius for message bubbles */
        .rounded-3xl {
            border-radius: 1.5rem !important; /* 24px */
        }

        /* Custom border-radius for user list items */
        .rounded-xl {
            border-radius: 0.75rem !important; /* 12px */
        }

        /* User list item active state */
        .user-list-item.active {
            background-color: #4f46e5; /* indigo-600 */
            color: #ffffff;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); /* shadow-lg */
        }
        .user-list-item.active:hover {
            background-color: #4338ca; /* Darker indigo on hover */
        }
        .user-list-item {
            background-color: #f3f4f6; /* gray-100 */
            color: #4b5563; /* gray-700 */
        }
        .user-list-item:hover {
            background-color: #e5e7eb; /* gray-200 */
        }
        .user-list-item .user-avatar {
            background-color: #a5b4fc; /* indigo-200 */
            color: #3730a3; /* indigo-800 */
        }
         .chat-header .user-avatar {
            background-color: #93c5fd; /* indigo-300 */
            color: #312e81; /* indigo-900 */
        }

        /* Responsive adjustments for smaller screens */
        @media (max-width: 991.98px) { /* Below Bootstrap's 'lg' breakpoint */
            .container-wrapper {
                flex-direction: column;
                height: auto; /* Allow content to dictate height */
            }

            .sidebar {
                width: 100%;
                border-right: none;
                border-bottom: 1px solid #e2e8f0; /* gray-200 */
                max-height: 40vh; /* Limit sidebar height on small screens */
            }

            .chat-window {
                height: 100vh; /* Full viewport height for chat area */
            }

            .chat-messages {
                height: calc(100vh - 250px); /* Adjust height for header and input on small screens */
            }
        }
    </style>
</head>
<body class="bg-light antialiased">
    <div class="container-wrapper d-flex vh-100 overflow-hidden">
        <div class="sidebar w-25 bg-white border-end border-gray-200 p-4 shadow-sm overflow-auto">
            <h2 class="fs-4 fw-semibold text-gray-800 mb-4">চ্যাট</h2>
            <ul id="user-list" class="list-unstyled space-y-3">
                @forelse($users as $user)
                    <li class="position-relative">
                        <a href="#"
                           data-receiver-id="{{ $user->id }}"
                           class="user-list-item d-flex align-items-center p-3 rounded-xl transition-all duration-200
                           {{ (isset($receiverId) && $receiverId == $user->id) ? 'active' : '' }}">
                            <div class="user-avatar width-40 height-40 rounded-circle d-flex align-items-center justify-content-center fw-bold fs-6">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <div class="ms-3">
                                <p class="mb-0 fw-medium fs-5">{{ $user->name }}</p>
                            </div>
                        </a>
                    </li>
                @empty
                    <li class="text-secondary fst-italic">কোনো চ্যাট নেই।</li>
                @endforelse
            </ul>
        </div>

        <div class="chat-window flex-grow-1 d-flex flex-column bg-gray-100">
            <div id="chat-header" class="chat-header bg-white border-bottom border-gray-200 p-4 shadow-sm d-flex align-items-center">
                @if(isset($receiverId) && $receiverId)
                    @php
                        // Find the current receiver
                        $currentReceiver = $users->firstWhere('id', $receiverId);
                    @endphp
                    @if($currentReceiver)
                        <div class="user-avatar width-48 height-48 rounded-circle d-flex align-items-center justify-content-center fw-bold fs-5">
                            {{ substr($currentReceiver->name, 0, 1) }}
                        </div>
                        <h2 class="fs-4 fw-semibold text-gray-800 ms-3">{{ $currentReceiver->name }}</h2>
                    @else
                        <div class="user-avatar width-48 height-48 rounded-circle d-flex align-items-center justify-content-center fw-bold fs-5">
                            ?
                        </div>
                        <h2 class="fs-4 fw-semibold text-gray-800 ms-3">ব্যবহারকারী পাওয়া যায়নি</h2>
                    @endif
                @else
                    <div class="user-avatar width-48 height-48 rounded-circle d-flex align-items-center justify-content-center fw-bold fs-5">
                        ?
                    </div>
                    <h2 class="fs-4 fw-semibold text-gray-800 ms-3">একটি ব্যবহারকারী নির্বাচন করুন</h2>
                @endif
            </div>

            <div id="chat-messages" class="chat-messages p-4 flex-grow-1 space-y-3">
                @if(isset($receiverId) && $receiverId)
                    @forelse($messages as $message)
                        <div class="d-flex {{ $message->sender_id == Auth::id() ? 'justify-content-end' : 'justify-content-start' }}" data-message-id="{{ $message->id }}">
                            <div class="message-bubble p-3 rounded-3xl max-w-75 shadow-sm
                                        {{ $message->sender_id == Auth::id() ? 'sent' : 'received' }}">
                                <p class="fs-7 text-secondary mb-1">
                                    {{ $message->sender_id == Auth::id() ? 'আপনি' : $message->sender->name }} - {{ $message->created_at->diffForHumans() }}
                                </p>
                                <p class="fs-6 fw-medium text-dark mb-0">{{ $message->message }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-secondary fst-italic mt-5">এই ব্যবহারকারীর সাথে এখনো কোনো মেসেজ নেই। একটি কথোপকথন শুরু করুন!</p>
                    @endforelse
                @else
                    <p class="text-center text-secondary fs-5">চ্যাট শুরু করতে বাম পাশ থেকে একজন ব্যবহারকারী নির্বাচন করুন।</p>
                @endif
            </div>

            <div id="message-input-area" class="bg-white border-top border-gray-200 p-4 {{ (isset($receiverId) && $receiverId) ? '' : 'd-none' }}">
                <form id="message-form" class="d-flex align-items-center gap-3">
                    @csrf <input type="hidden" id="receiver-id-input" name="receiver_id" value="{{ $receiverId ?? '' }}">
                    <textarea id="message-textarea" name="message" rows="3" placeholder="আপনার মেসেজ এখানে টাইপ করুন..."
                        class="form-control flex-grow-1 p-3 border border-gray-300 rounded-xl focus-ring focus-ring-indigo resize-none"
                        required></textarea>
                    <button type="submit"
                        class="btn btn-primary btn-lg fw-bold py-3 px-4 rounded-xl shadow-lg transition-transform hover-scale-105">
                        পাঠান
                    </button>
                </form>
                <p id="message-error" class="text-danger small mt-2 d-none"></p>
            </div>
        </div>
    </div>


   
</body>
</html>

@endsection

@push('scripts')

    <script>
        // --- DOM Elements ---
        const chatMessagesElement = document.getElementById('chat-messages');
        const messageForm = document.getElementById('message-form');
        const messageTextarea = document.getElementById('message-textarea');
        const receiverIdInput = document.getElementById('receiver-id-input');
        const messageErrorElement = document.getElementById('message-error');
        const chatHeaderElement = document.getElementById('chat-header');
        const messageInputArea = document.getElementById('message-input-area');
        const userListElement = document.getElementById('user-list');

        // Get current user ID and receiver ID from Laravel Blade
        const currentUserId = "{{ Auth::id() }}";
        let currentReceiverId = "{{ $receiverId ?? '' }}"; // Current selected user ID, empty string if null

        // Get $users data from Laravel into JavaScript, used for updating the user list
        const usersData = @json($users->keyBy('id'));

        // --- Utility Functions ---

        /**
         * Scrolls the chat messages container to the bottom.
         */
        const scrollToBottom = () => {
            chatMessagesElement.scrollTop = chatMessagesElement.scrollHeight;
        };

        /**
         * Formats a date object into a human-readable "time ago" string.
         * @param {Date|string} date - The date object or date string to format.
         * @returns {string} Human-readable time difference.
         */
        const timeAgo = (date) => {
            const messageDate = new Date(date);
            const seconds = Math.floor((new Date() - messageDate) / 1000);

            let interval = seconds / 31536000; // Years
            if (interval > 1) return `${Math.floor(interval)} বছর আগে`;
            interval = seconds / 2592000; // Months
            if (interval > 1) return `${Math.floor(interval)} মাস আগে`;
            interval = seconds / 86400; // Days
            if (interval > 1) return `${Math.floor(interval)} দিন আগে`;
            interval = seconds / 3600; // Hours
            if (interval > 1) return `${Math.floor(interval)} ঘন্টা আগে`;
            interval = seconds / 60; // Minutes
            if (interval > 1) return `${Math.floor(interval)} মিনিট আগে`;
            return `${Math.floor(seconds)} সেকেন্ড আগে`;
        };

        /**
         * Renders a single message bubble in the chat.
         * @param {object} message - The message object to render.
         * @param {string} senderName - The name of the message sender.
         */
        const addMessageToChat = (message, senderName) => {
            const isSent = message.sender_id == currentUserId; // Using == because IDs can be strings or numbers
            const messageDate = new Date(message.created_at);

            const messageBubble = document.createElement('div');
            messageBubble.className = `d-flex ${isSent ? 'justify-content-end' : 'justify-content-start'}`;
            messageBubble.dataset.messageId = message.id; // Set message ID for new message loading
            messageBubble.innerHTML = `
                <div class="message-bubble p-3 rounded-3xl max-w-75 shadow-sm ${isSent ? 'sent' : 'received'}">
                    <p class="fs-7 text-secondary mb-1">
                        ${isSent ? 'আপনি' : senderName} - ${timeAgo(messageDate)}
                    </p>
                    <p class="fs-6 fw-medium text-dark mb-0">${message.message}</p>
                </div>
            `;
            chatMessagesElement.appendChild(messageBubble);
        };

        /**
         * Renders all messages for the currently selected chat.
         * This is only for initial load; new messages will be added by fetchNewMessages.
         */
        const renderInitialMessages = () => {
            if (currentReceiverId) {
                // Render initial messages loaded from Laravel
                @if(isset($receiverId) && $receiverId)
                    @forelse($messages as $message)
                        addMessageToChat({
                            id: "{{ $message->id }}",
                            sender_id: "{{ $message->sender_id }}",
                            receiver_id: "{{ $message->receiver_id }}",
                            message: `{!! addslashes($message->message) !!}`, // Escape message for JavaScript
                            created_at: "{{ $message->created_at }}"
                        }, "{{ $message->sender_id == Auth::id() ? 'আপনি' : $message->sender->name }}");
                    @empty
                        // If no messages, show a placeholder message
                        chatMessagesElement.innerHTML = `<p class="text-center text-secondary fst-italic mt-5">এই ব্যবহারকারীর সাথে এখনো কোনো মেসেজ নেই। একটি কথোপকথন শুরু করুন!</p>`;
                    @endforelse
                @endif
                scrollToBottom();
            }
        };

        // --- Event Handlers ---

        /**
         * Handles form submission for sending messages.
         * @param {Event} event - The form submission event.
         */
        const handleSendMessage = async (event) => {
            event.preventDefault(); // Prevent default form submission (page reload)

            const messageContent = messageTextarea.value.trim();
            if (!messageContent) {
                messageErrorElement.textContent = 'মেসেজ খালি হতে পারে না।';
                messageErrorElement.classList.remove('d-none');
                return;
            } else {
                messageErrorElement.classList.add('d-none');
            }

            if (!currentReceiverId) {
                messageErrorElement.textContent = 'মেসেজ পাঠানোর জন্য একজন ব্যবহারকারী নির্বাচন করুন।';
                messageErrorElement.classList.remove('d-none');
                return;
            }

            // Send message via AJAX POST request
            try {
                const response = await fetch("{{ route('messages.send') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content // Use CSRF token
                    },
                    body: JSON.stringify({
                        receiver_id: currentReceiverId,
                        message: messageContent
                    })
                });

                const data = await response.json();

                if (data.status === 'success') {
                    // Add new message to local state and render
                    addMessageToChat(data.message, data.sender_name); // `sender_name` comes from Laravel
                    scrollToBottom();
                    messageTextarea.value = ''; // Clear input
                } else {
                    messageErrorElement.textContent = data.message || 'মেসেজ পাঠাতে ব্যর্থ হয়েছে।';
                    messageErrorElement.classList.remove('d-none');
                }
            } catch (error) {
                console.error('মেসেজ পাঠাতে ত্রুটি:', error);
                messageErrorElement.textContent = 'মেসেজ পাঠাতে ত্রুটি হয়েছে। অনুগ্রহ করে আবার চেষ্টা করুন।';
                messageErrorElement.classList.remove('d-none');
            }
        };

        /**
         * Fetches new messages from the backend.
         */
        const fetchNewMessages = async () => {
            if (!currentReceiverId) {
                return; // Don't fetch messages if no user is selected
            }

            // Get the ID of the last message, so we only fetch newer ones
            const lastMessageElement = chatMessagesElement.querySelector('.flex:last-child');
            const lastMessageId = lastMessageElement ? lastMessageElement.dataset.messageId : null;

            try {
                const response = await fetch(`{{ route('messages.fetch') }}?receiver_id=${currentReceiverId}${lastMessageId ? `&last_message_id=${lastMessageId}` : ''}`);
                const data = await response.json();

                if (data.messages && data.messages.length > 0) {
                    // If chatMessagesElement has "no messages" placeholder, remove it
                    const noMessagesPlaceholder = chatMessagesElement.querySelector('.text-center.fst-italic');
                    if (noMessagesPlaceholder && noMessagesPlaceholder.textContent.includes('কোনো মেসেজ নেই')) {
                        chatMessagesElement.innerHTML = '';
                    }

                    data.messages.forEach(message => {
                        // Ensure the message is part of the current conversation and not already added
                        const isMessageExists = chatMessagesElement.querySelector(`[data-message-id="${message.id}"]`);
                        if (!isMessageExists) {
                            addMessageToChat(message, message.sender.name);
                        }
                    });
                    scrollToBottom();
                }
            } catch (error) {
                console.error('মেসেজ আনতে ত্রুটি:', error);
            }
        };

        /**
         * Handles user selection from the sidebar.
         * Updates the chat window dynamically without a full page reload.
         * @param {string} newReceiverId - The ID of the selected user.
         */
        const selectUser = async (newReceiverId) => {
            if (currentReceiverId === newReceiverId) {
                return; // Already selected
            }
            currentReceiverId = newReceiverId;
            receiverIdInput.value = newReceiverId; // Update hidden input for message form

            // Update URL in browser history
            const newUrl = `{{ route('messages.index') }}?receiver_id=${newReceiverId}`;
            window.history.pushState({ receiver_id: newReceiverId }, '', newUrl);

            // Dynamically update chat header
            const selectedUser = usersData[newReceiverId];

            if (selectedUser) {
                chatHeaderElement.innerHTML = `
                    <div class="user-avatar width-48 height-48 rounded-circle d-flex align-items-center justify-content-center fw-bold fs-5">
                        ${selectedUser.name.charAt(0)}
                    </div>
                    <h2 class="fs-4 fw-semibold text-gray-800 ms-3">${selectedUser.name}</h2>
                `;
                messageInputArea.classList.remove('d-none'); // Show message input
            } else {
                chatHeaderElement.innerHTML = `
                    <div class="user-avatar width-48 height-48 rounded-circle d-flex align-items-center justify-content-center fw-bold fs-5">
                        ?
                    </div>
                    <h2 class="fs-4 fw-semibold text-gray-800 ms-3">ব্যবহারকারী পাওয়া যায়নি</h2>
                `;
                messageInputArea.classList.add('d-none'); // Hide message input
            }

            // Clear previous messages and fetch new ones
            chatMessagesElement.innerHTML = '';
            chatMessagesElement.innerHTML = `<p class="text-center text-secondary fst-italic mt-5">মেসেজ লোড হচ্ছে...</p>`; // Loading indicator

            // Re-render user list to highlight active user
            document.querySelectorAll('#user-list a').forEach(link => {
                link.classList.remove('active');
            });
            const activeLink = document.querySelector(`#user-list a[data-receiver-id="${newReceiverId}"]`);
            if (activeLink) {
                activeLink.classList.add('active');
            }

            // Fetch all messages for the newly selected user
            try {
                const response = await fetch(`{{ route('messages.fetch') }}?receiver_id=${newReceiverId}`);
                const data = await response.json();

                chatMessagesElement.innerHTML = ''; // Clear loading message

                if (data.messages && data.messages.length > 0) {
                    data.messages.forEach(message => {
                        addMessageToChat(message, message.sender.name);
                    });
                    scrollToBottom();
                } else {
                    chatMessagesElement.innerHTML = `<p class="text-center text-secondary fst-italic mt-5">এই ব্যবহারকারীর সাথে এখনো কোনো মেসেজ নেই। একটি কথোপকথন শুরু করুন!</p>`;
                }
            } catch (error) {
                console.error('মেসেজ আনতে ত্রুটি:', error);
                chatMessagesElement.innerHTML = `<p class="text-center text-danger fst-italic mt-5">মেসেজ লোড করতে সমস্যা হয়েছে।</p>`;
            }
        };

        // --- Initialization ---
        document.addEventListener('DOMContentLoaded', () => {
            renderInitialMessages(); // Render initial messages

            // Add event listener for message form submission
            messageForm.addEventListener('submit', handleSendMessage);

            // Add click event listener to the user list
            userListElement.addEventListener('click', (event) => {
                const link = event.target.closest('a[data-receiver-id]');
                if (link) {
                    event.preventDefault(); // Prevent default link behavior
                    selectUser(link.dataset.receiverId);
                }
            });

            // Set an interval to fetch new messages every 3 seconds
            setInterval(fetchNewMessages, 3000); // 3 seconds
        });
    </script>
@endpush
