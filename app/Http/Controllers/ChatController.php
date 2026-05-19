<?php

namespace App\Http\Controllers;

use App\Events\GroupMemberUpdated;
use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;
use App\Events\MessageSent;
use App\Events\GroupCreated;

class ChatController extends Controller
{
    // =========================
    // GLOBAL CHAT
    // =========================
    public function index()
    {
        $messages = Message::with('sender')
            ->whereNull('group_id')
            ->whereNull('receiver_id')
            ->latest()
            ->get()
            ->reverse();

        $users = User::where(
            'id',
            '!=',
            Auth::id()
        )->get();

        $groups = Auth::user()->groups;

        return view('chat', compact(
            'messages',
            'users',
            'groups'
        ));
    }

    // =========================
    // SEND MESSAGE
    // =========================
public function sendMessage(Request $request)
{
    $request->validate([
        'message' => 'required',
    ]);

    $message = Message::create([
        'sender_id' => Auth::id(),
        'receiver_id' => $request->receiver_id ?? Auth::id(),
        'group_id' => $request->group_id,
        'message' => $request->message,
    ]);

    $message->load('sender');

    broadcast(new MessageSent($message))->toOthers();

    return response()->json([
        'success' => true,
        'message' => $message,
    ]);
} 
    

    // =========================
    // EDIT MESSAGE
    // =========================
    public function update(Request $request, $id)
    {
        $message = Message::findOrFail($id);

        if (
            $message->sender_id !=
            Auth::id()
        ) {
            return back();
        }

        $message->update([
            'message' => $request->message
        ]);

        return back();
    }

    // =========================
    // DELETE MESSAGE
    // =========================
    public function deleteMessage($id)
    {
        $message = Message::findOrFail($id);

        if (
            $message->sender_id !=
            Auth::id()
        ) {
            return back();
        }

        $message->delete();

        return back();
    }

    // =========================
    // CREATE GROUP
    // =========================
    public function createGroup(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        $group = Group::create([
            'name' => $request->name
        ]);

        // AUTO JOIN
        $group->users()->attach(
            Auth::id()
        );

        // REALTIME GROUP
        broadcast(
            new GroupCreated($group)
        );

        return back();
    }

    // =========================
    // GROUP CHAT PAGE
    // =========================
    public function groupChat($id)
    {
        $group = Group::findOrFail($id);

        $messages = Message::with('sender')
            ->where(
                'group_id',
                $id
            )
            ->latest()
            ->get()
            ->reverse();

        $groups = Auth::user()->groups;

        $users = User::where(
            'id',
            '!=',
            Auth::id()
        )->get();

        return view('group-chat', compact(
            'group',
            'messages',
            'groups',
            'users'
        ));
    }

    // =========================
    // PRIVATE CHAT PAGE
    // =========================
    public function privateChat($id)
    {
        $user = User::findOrFail($id);

        $messages = Message::with('sender')

            ->where(function ($query) use ($id) {

                $query->where(
                    'sender_id',
                    Auth::id()
                )
                ->where(
                    'receiver_id',
                    $id
                );

            })

            ->orWhere(function ($query) use ($id) {

                $query->where(
                    'sender_id',
                    $id
                )
                ->where(
                    'receiver_id',
                    Auth::id()
                );

            })

            ->latest()
            ->get()
            ->reverse();

        $users = User::where(
            'id',
            '!=',
            Auth::id()
        )->get();

        $groups = Auth::user()->groups;

        return view('private-chat', compact(
            'user',
            'messages',
            'users',
            'groups'
        ));
    }

    // =========================
    // ADD MEMBER
    // =========================
    public function addMember(Request $request, $id)
{
    $group = Group::findOrFail($id);

    $user = User::findOrFail($request->user_id);

    $group->users()->syncWithoutDetaching([
        $user->id
    ]);

    broadcast(new GroupMemberUpdated($group, $user, 'added'));

    return back();
}

    // =========================
    // REMOVE MEMBER
    // =========================
    public function removeMember($groupId, $userId)
{
    $group = Group::findOrFail($groupId);

    $user = User::findOrFail($userId);

    $group->users()->detach($user->id);

    broadcast(new GroupMemberUpdated($group, $user, 'removed'));

    return back();
}

}