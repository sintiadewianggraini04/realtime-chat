<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Private Chat</title>

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    

    <style>

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        body{
            font-family:Arial, sans-serif;
            background:#0f172a;
            height:100vh;
            overflow:hidden;
        }

        .container{
            display:flex;
            height:100vh;
        }

        .sidebar{
            width:250px;
            background:#111827;
            color:white;
            padding:20px;
            overflow-y:auto;
        }

        .sidebar h2{
            margin-bottom:20px;
            font-size:22px;
        }

        .user{
            background:#1f2937;
            padding:15px;
            border-radius:12px;
            margin-bottom:10px;
        }

        .chat-area{
            flex:1;
            display:flex;
            flex-direction:column;
            background:#e5e7eb;
        }

        .chat-header{
            background:#1e293b;
            color:white;
            padding:20px;
            font-size:22px;
            font-weight:bold;
        }

        .messages{
            flex:1;
            overflow-y:auto;
            padding:20px;
            display:flex;
            flex-direction:column;
            gap:15px;
        }

        .message-wrapper{
            display:flex;
            flex-direction:column;
        }

        .my-wrapper{
            align-items:flex-end;
        }

        .other-wrapper{
            align-items:flex-start;
        }

        .message{
            max-width:60%;
            padding:12px 15px;
            border-radius:15px;
            word-wrap:break-word;
        }

        .my-message{
            background:#2563eb;
            color:white;
        }

        .other-message{
            background:white;
            color:black;
        }

        .message-name{
            font-size:12px;
            font-weight:bold;
            margin-bottom:5px;
        }

        .chat-form{
            display:flex;
            padding:20px;
            background:white;
            gap:10px;
        }

        .chat-input{
            flex:1;
            padding:15px;
            border:1px solid #ccc;
            border-radius:10px;
            font-size:16px;
            outline:none;
        }

        .send-btn{
            background:#2563eb;
            color:white;
            border:none;
            padding:15px 25px;
            border-radius:10px;
            cursor:pointer;
            font-size:16px;
            font-weight:bold;
        }

        .send-btn:hover{
            background:#1d4ed8;
        }

    </style>

</head>

<body>

<div class="container">

    <!-- SIDEBAR -->

    <div class="sidebar">

        <h2>Users</h2>

        @foreach($users as $userItem)

            <a
                href="{{ route('private.chat', $userItem->id) }}"
                class="user"
                style="
                    display:block;
                    text-decoration:none;
                    color:white;
                "
            >

                <div style="
                    display:flex;
                    justify-content:space-between;
                    align-items:center;
                ">

                    <span>
                        {{ $userItem->name }}
                    </span>

                    <span
                        data-user-id="{{ $userItem->id }}"
                        style="
                            color:{{ $userItem->is_online ? 'lime' : 'red' }};
                            font-size:12px;
                        "
                    >
                        {{ $userItem->is_online ? 'Online' : 'Offline' }}
                    </span>

                </div>

            </a>

        @endforeach

        <hr style="
            margin:20px 0;
            border-color:#374151;
        ">

        <h2>Groups</h2>

        @foreach($groups as $group)

            <a
                href="{{ route('group.chat', $group->id) }}"
                style="
                    display:block;
                    background:#1f2937;
                    padding:15px;
                    border-radius:12px;
                    margin-bottom:10px;
                    color:white;
                    text-decoration:none;
                "
            >
                👥 {{ $group->name }}
            </a>

        @endforeach

    </div>

    <!-- CHAT -->

    <div class="chat-area">

        <div class="chat-header">

            <div style="
                font-size:22px;
                font-weight:bold;
            ">
                {{ $user->name }}
            </div>

            <div
                id="header-online-status"
                data-user-id="{{ $user->id }}"
                style="
                    font-size:13px;
                    color:{{ $user->is_online ? 'lime' : '#f87171' }};
                "
            >
                {{ $user->is_online ? 'Online' : 'Offline' }}
            </div>

        </div>

        <!-- MESSAGES -->

        <div
            class="messages"
            id="messages"
        >

            @foreach($messages as $message)

                <div class="message-wrapper {{ $message->sender_id == Auth::id() ? 'my-wrapper' : 'other-wrapper' }}">

                    <div class="message {{ $message->sender_id == Auth::id() ? 'my-message' : 'other-message' }}">

                        <div class="message-name">
                            {{ $message->sender->name }}
                        </div>

                        <div>
                            {{ $message->message }}
                        </div>

                    </div>

                </div>

            @endforeach

        </div>

        <!-- FORM -->

        <form
            id="chatForm"
            class="chat-form"
        >

            @csrf

            <input
                type="hidden"
                id="receiver_id"
                value="{{ $user->id }}"
            >

            <input
                type="text"
                id="messageInput"
                class="chat-input"
                placeholder="Type message..."
                required
            >

            <button
                type="submit"
                class="send-btn"
            >
                Send
            </button>

        </form>

    </div>

</div>

<script>

window.currentUserId = {{ Auth::id() }};

let ids = [
    {{ Auth::id() }},
    {{ $user->id }}
];

ids.sort((a,b) => a - b);

window.chatChannel =
    'private.' + ids[0] + '.' + ids[1];

function scrollBottom()
{
    const messages =
        document.getElementById('messages');

    messages.scrollTop =
        messages.scrollHeight;
}

scrollBottom();


// =====================================
// APPEND MESSAGE
// =====================================

window.appendMessage = function(message, isMine = true)
{
    let messages =
        document.getElementById('messages');

    let senderName =
        message.sender?.name
        || message.sender_name
        || 'User';

    messages.innerHTML += `

        <div class="message-wrapper ${isMine ? 'my-wrapper' : 'other-wrapper'}">

            <div class="message ${isMine ? 'my-message' : 'other-message'}">

                <div class="message-name">
                    ${senderName}
                </div>

                <div>
                    ${message.message}
                </div>

            </div>

        </div>

    `;

    scrollBottom();
}


// =====================================
// SEND MESSAGE
// =====================================

document
.getElementById('chatForm')
.addEventListener('submit', async function(e){

    e.preventDefault();

    let messageInput =
        document.getElementById('messageInput');

    let receiverId =
        document.getElementById('receiver_id').value;

    let message =
        messageInput.value;

    if(message.trim() === '')
    {
        return;
    }

    // langsung tampil ke diri sendiri
    appendMessage({
        sender_name: '{{ Auth::user()->name }}',
        message: message
    }, true);

    await fetch("{{ route('send.message') }}", {

        method: "POST",

        headers: {

            "Content-Type": "application/json",

            "X-CSRF-TOKEN":
                document.querySelector(
                    'meta[name=\"csrf-token\"]'
                ).content,

            "Accept": "application/json"

        },

        body: JSON.stringify({

            message: message,
            receiver_id: receiverId

        })

    });

    messageInput.value = '';

});

</script>
@vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])
</body>
</html>