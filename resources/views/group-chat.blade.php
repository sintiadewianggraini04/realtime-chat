<!DOCTYPE html>
<html>
<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>{{ $group->name }}</title>

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

</head>

<body style="
    margin:0;
    font-family:Arial;
    background:#0f172a;
">

<div style="
    display:flex;
    height:100vh;
">

    <!-- SIDEBAR -->

    <div style="
        width:260px;
        background:#111827;
        padding:20px;
        overflow-y:auto;
    ">

        <a
            href="{{ route('chat') }}"
            style="
                display:block;
                background:#2563eb;
                padding:12px;
                text-align:center;
                border-radius:10px;
                color:white;
                text-decoration:none;
                margin-bottom:20px;
            "
        >
            ← Back
        </a>

        <h2 style="
            color:white;
            margin-bottom:15px;
        ">
            Members
        </h2>

        <!-- MEMBERS -->

        <div id="member-list">

            @foreach($group->users as $member)

                <div
                    id="member-{{ $member->id }}"
                    style="
                        background:#1f2937;
                        color:white;
                        padding:12px;
                        border-radius:10px;
                        margin-bottom:10px;
                    "
                >

                    <div style="
                        display:flex;
                        justify-content:space-between;
                        align-items:center;
                    ">

                        <span>
                            {{ $member->name }}
                        </span>

                        <form
                            action="{{ route('group.remove.member', [$group->id, $member->id]) }}"
                            method="POST"
                        >

                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                style="
                                    background:red;
                                    color:white;
                                    border:none;
                                    padding:5px 10px;
                                    border-radius:6px;
                                    cursor:pointer;
                                    font-size:12px;
                                "
                            >
                                Remove
                            </button>

                        </form>

                    </div>

                </div>

            @endforeach

        </div>

        <div style="margin-top:20px;">

            <h2 style="
                color:white;
                margin-bottom:10px;
            ">
                Add Member
            </h2>

            <form
                action="{{ route('group.add.member', $group->id) }}"
                method="POST"
            >

                @csrf

                <select
                    name="user_id"
                    style="
                        width:100%;
                        padding:10px;
                        border:none;
                        border-radius:10px;
                        margin-bottom:10px;
                    "
                >

                    @foreach($users as $user)

                        <option value="{{ $user->id }}">
                            {{ $user->name }}
                        </option>

                    @endforeach

                </select>

                <button
                    type="submit"
                    style="
                        width:100%;
                        background:#2563eb;
                        color:white;
                        border:none;
                        padding:10px;
                        border-radius:10px;
                        cursor:pointer;
                    "
                >
                    Add Member
                </button>

            </form>

        </div>

    </div>

    <!-- CHAT -->

    <div style="
        flex:1;
        display:flex;
        flex-direction:column;
        background:#e5e7eb;
    ">

        <div style="
            background:#1e293b;
            color:white;
            padding:20px;
            font-size:24px;
            font-weight:bold;
        ">
            {{ $group->name }}
        </div>

        <!-- MESSAGES -->

        <div
            id="messages"
            style="
                flex:1;
                overflow-y:auto;
                padding:20px;
                display:flex;
                flex-direction:column;
            "
        >

            @foreach($messages as $message)

                <div style="
                    width:100%;
                    margin-bottom:15px;
                    display:flex;
                    justify-content:
                    {{ $message->sender_id == Auth::id()
                        ? 'flex-end'
                        : 'flex-start'
                    }};
                ">

                    <div style="
                        max-width:60%;
                        padding:14px;
                        border-radius:20px;
                        background:
                        {{ $message->sender_id == Auth::id()
                            ? '#2563eb'
                            : 'white'
                        }};
                        color:
                        {{ $message->sender_id == Auth::id()
                            ? 'white'
                            : 'black'
                        }};
                        box-shadow:0 2px 8px rgba(0,0,0,0.1);
                        word-break:break-word;
                    ">

                        <div style="
                            font-size:12px;
                            font-weight:bold;
                            margin-bottom:6px;
                            opacity:0.9;
                        ">
                            {{ $message->sender->name }}
                        </div>

                        <div style="
                            font-size:16px;
                            line-height:1.4;
                        ">
                            {{ $message->message }}
                        </div>

                        <div style="
                            font-size:11px;
                            margin-top:6px;
                            opacity:0.7;
                            text-align:right;
                        ">
                            {{ $message->created_at->format('H:i') }}
                        </div>

                    </div>

                </div>

            @endforeach

        </div>

        <!-- FORM -->

        <form
            id="chatForm"
            style="
                display:flex;
                gap:10px;
                padding:20px;
                background:white;
                border-top:1px solid #ddd;
            "
        >

            @csrf

            <input
                type="hidden"
                id="group_id"
                value="{{ $group->id }}"
            >

            <input
                type="text"
                id="messageInput"
                placeholder="Type message..."
                style="
                    flex:1;
                    padding:15px;
                    border-radius:12px;
                    border:1px solid #ccc;
                    font-size:15px;
                    outline:none;
                "
                required
            >

            <button
                type="submit"
                style="
                    background:#2563eb;
                    color:white;
                    border:none;
                    padding:15px 25px;
                    border-radius:12px;
                    cursor:pointer;
                    font-weight:bold;
                "
            >
                Send
            </button>

        </form>

    </div>

</div>

<script>
    window.currentUserId = {{ Auth::id() }};
    window.groupChannel = 'group.{{ $group->id }}';

    const messagesBox = document.getElementById('messages');
    const chatForm = document.getElementById('chatForm');
    const messageInput = document.getElementById('messageInput');
    const groupId = document.getElementById('group_id').value;

    function scrollBottom() {
        messagesBox.scrollTop = messagesBox.scrollHeight;
    }

    function formatTime(dateValue) {
        const date = dateValue
            ? new Date(dateValue)
            : new Date();

        return date.toLocaleTimeString([], {
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    window.appendGroupMessage = function(message, isMine) {
        const senderName = message.sender?.name || '{{ Auth::user()->name }}';
        const messageTime = formatTime(message.created_at);

        messagesBox.innerHTML += `
            <div style="
                width:100%;
                margin-bottom:15px;
                display:flex;
                justify-content:${isMine ? 'flex-end' : 'flex-start'};
            ">
                <div style="
                    max-width:60%;
                    padding:14px;
                    border-radius:20px;
                    background:${isMine ? '#2563eb' : 'white'};
                    color:${isMine ? 'white' : 'black'};
                    box-shadow:0 2px 8px rgba(0,0,0,0.1);
                    word-break:break-word;
                ">
                    <div style="
                        font-size:12px;
                        font-weight:bold;
                        margin-bottom:6px;
                        opacity:0.9;
                    ">
                        ${senderName}
                    </div>

                    <div style="
                        font-size:16px;
                        line-height:1.4;
                    ">
                        ${message.message}
                    </div>

                    <div style="
                        font-size:11px;
                        margin-top:6px;
                        opacity:0.7;
                        text-align:right;
                    ">
                        ${messageTime}
                    </div>
                </div>
            </div>
        `;

        scrollBottom();
    }

    chatForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        const text = messageInput.value.trim();

        if (text === '') {
            return;
        }

        const response = await fetch('/send-message', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                message: text,
                group_id: groupId
            })
        });

        const data = await response.json();

        if (data.success === true) {
            window.appendGroupMessage(data.message, true);
            messageInput.value = '';
        } else {
            alert(data.error);
        }
    });

    scrollBottom();
</script>

</body>
</html>