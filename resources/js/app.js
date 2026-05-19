import './bootstrap';
import './echo';

console.log('Echo Connected');

// =====================================
// PRIVATE CHAT
// =====================================

if (window.chatChannel) {

    console.log(
        'JOIN:',
        window.chatChannel
    );

    let channel =
        window.Echo.private(
            window.chatChannel
        );

    channel.subscribed(() => {

        console.log(
            'SUBSCRIBED'
        );
    });

    channel.error((err) => {

        console.log(
            'CHANNEL ERROR:',
            err
        );
    });

    channel.listen(
        '.message.sent',
        (e) => {

            console.log(
                'MESSAGE RECEIVED:',
                e
            );

            if (
                parseInt(e.message.sender_id)
                === parseInt(window.currentUserId)
            ) {
                return;
            }

            appendMessage(
                e.message,
                false
            );
        }
    );
}

// =====================================
// GROUP CHAT
// =====================================

if (window.groupChannel) {

    console.log('JOIN GROUP:', window.groupChannel);

    let groupChannel = window.Echo.private(window.groupChannel);

    groupChannel.listen('.message.sent', (e) => {

        console.log('GROUP MESSAGE RECEIVED:', e);

        if (
            parseInt(e.message.sender_id)
            === parseInt(window.currentUserId)
        ) {
            return;
        }

        if (window.appendGroupMessage) {
            window.appendGroupMessage(e.message, false);
        }
    });
}

window.Echo
.channel('online-status')

.listen('.user.status', (e) => {

    console.log(e);

    document
    .querySelectorAll(
        `[data-user-id="${e.id}"]`
    )
    .forEach((el) => {

        el.innerHTML =
            e.is_online
                ? 'Online'
                : 'Offline';

        el.style.color =
            e.is_online
                ? 'lime'
                : 'red';
    });
});

// =====================================
// AUTO RECONNECT
// =====================================

setInterval(() => {

    if (
        window.Echo.connector
        .pusher.connection.state
        !== 'connected'
    ) {

        console.log(
            'RECONNECTING...'
        );

        window.Echo.connector
        .pusher.connection.connect();
    }

}, 1000);

// =====================================
// GROUP MEMBER REALTIME
// =====================================

if (window.currentUserId) {

    window.Echo
        .channel('user.' + window.currentUserId + '.groups')
        .listen('.group.member.updated', (e) => {

            console.log('GROUP MEMBER UPDATED:', e);

            const groupList = document.getElementById('group-list');

            if (!groupList) {
                return;
            }

            if (e.action === 'added') {

                const exists = document.getElementById(
                    'group-link-' + e.group.id
                );

                if (exists) {
                    return;
                }

                groupList.innerHTML += `
                    <a
                        id="group-link-${e.group.id}"
                        href="/group/${e.group.id}"
                        class="group-link"
                    >
                        👥 ${e.group.name}
                    </a>
                `;
            }

            if (e.action === 'removed') {

                const target = document.getElementById(
                    'group-link-' + e.group.id
                );

                if (target) {
                    target.remove();
                }
            }
        });
}

// =====================================
// GROUP MEMBER LIST REALTIME
// =====================================

if (window.groupChannel) {

    window.Echo
        .private(window.groupChannel)
        .listen('.group.member.updated', (e) => {

            console.log('GROUP MEMBER LIST UPDATED:', e);

            const memberList = document.getElementById('member-list');

            if (!memberList) {
                return;
            }

            if (e.action === 'added') {

                const exists = document.getElementById(
                    'member-' + e.user.id
                );

                if (exists) {
                    return;
                }

                memberList.innerHTML += `
    <div
        id="member-${e.user.id}"
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
                ${e.user.name}
            </span>

            <form
                action="/group/${e.group.id}/remove-member/${e.user.id}"
                method="POST"
            >
                <input
                    type="hidden"
                    name="_token"
                    value="${document.querySelector('meta[name="csrf-token"]').content}"
                >

                <input
                    type="hidden"
                    name="_method"
                    value="DELETE"
                >

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
`;
            }

            if (e.action === 'removed') {

                const target = document.getElementById(
                    'member-' + e.user.id
                );

                if (target) {
                    target.remove();
                }
            }
        });
}