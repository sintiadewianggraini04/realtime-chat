<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Realtime Chat</title>

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

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
            width:260px;
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
            transition:0.2s;
        }

        .user:hover{
            background:#374151;
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
            font-size:24px;
            font-weight:bold;
        }

        .welcome-box{
            flex:1;
            display:flex;
            justify-content:center;
            align-items:center;
            flex-direction:column;
            text-align:center;
            padding:30px;
        }

        .welcome-title{
            font-size:40px;
            font-weight:bold;
            color:#1e293b;
            margin-bottom:15px;
        }

        .welcome-text{
            font-size:18px;
            color:#64748b;
        }

        .group-link{
            display:block;
            background:#1f2937;
            padding:15px;
            border-radius:12px;
            margin-bottom:10px;
            color:white;
            text-decoration:none;
            transition:0.2s;
        }

        .group-link:hover{
            background:#374151;
        }

        .logout-btn{
            width:100%;
            background:red;
            color:white;
            border:none;
            padding:12px;
            border-radius:10px;
            cursor:pointer;
            margin-top:20px;
            font-size:15px;
            font-weight:bold;
        }

        .logout-btn:hover{
            opacity:0.9;
        }

    </style>

</head>

<body>

<div class="container">

    <!-- SIDEBAR -->

    <div class="sidebar">

        <!-- USERS -->

        <h2>
            Users
        </h2>

        @foreach($users as $user)

            <a
                href="{{ route('private.chat', $user->id) }}"
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
                        {{ $user->name }}
                    </span>

                    <span
                        data-user-id="{{ $user->id }}"
                        id="user-status-{{ $user->id }}"
                        style="
                            color:{{ $user->is_online ? 'lime' : 'red' }};
                            font-size:12px;
                        "
                    >
                        {{ $user->is_online ? 'Online' : 'Offline' }}
                    </span>

                </div>

            </a>

        @endforeach

        <hr style="
            margin:20px 0;
            border-color:#374151;
        ">

        <!-- GROUP -->

        <h2>
            Groups
        </h2>

        <!-- CREATE GROUP -->

        <form
            action="{{ route('group.create') }}"
            method="POST"
            style="
                margin-bottom:20px;
            "
        >

            @csrf

            <input
                type="text"
                name="name"
                placeholder="Group name"
                required
                style="
                    width:100%;
                    height:45px;
                    padding:0 15px;
                    border:none;
                    border-radius:10px;
                    margin-bottom:10px;
                    font-size:14px;
                    background:white;
                    color:black;
                    outline:none;
                "
            >

            <button
                type="submit"
                style="
                    width:100%;
                    height:45px;
                    border:none;
                    border-radius:10px;
                    background:#2563eb;
                    color:white;
                    font-size:14px;
                    font-weight:bold;
                    cursor:pointer;
                "
            >
                Create Group
            </button>

        </form>

        <!-- GROUP LIST -->

        <div id="group-list">

            @foreach($groups as $group)

                <a
                    id="group-link-{{ $group->id }}"
                    href="{{ route('group.chat', $group->id) }}"
                    class="group-link"
                >
                    👥 {{ $group->name }}
                </a>

            @endforeach

        </div>

        <!-- LOGOUT -->

        <form
            method="POST"
            action="{{ route('logout') }}"
        >

            @csrf

            <button
                type="submit"
                class="logout-btn"
            >
                Logout
            </button>

        </form>

    </div>

    <!-- CHAT AREA -->

    <div class="chat-area">

        <div class="chat-header">
            Realtime Chat App
        </div>

        <div class="welcome-box">

            <div class="welcome-title">
                Welcome 
            </div>

            <div class="welcome-text">
                Select a user or group to start chatting
            </div>

        </div>

    </div>

</div>

<script>

    window.currentUserId = {{ auth()->id() }};

    window.chatChannel = 'chat-room';

</script>

</body>
</html>