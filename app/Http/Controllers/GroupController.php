<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\GroupMember;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    //  CREATE A GROUP
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:groups',
            'description' => 'nullable|string|max:500',
            'privacy' => 'required|in:public,private',
        ]);

        $group = Group::create([
            'name' => $request->name,
            'description' => $request->description,
            'privacy' => $request->privacy,
            'admin_id' => Auth::id(),
        ]);

        // Add creator as admin member
        GroupMember::create([
            'group_id' => $group->id,
            'user_id' => Auth::id(),
            'status' => 'approved',
            'is_admin' => true,
        ]);

        return response()->json(['message' => 'Group created successfully', 'group' => $group], 201);
    }

    //  JOIN A PUBLIC GROUP OR REQUEST TO JOIN A PRIVATE GROUP
    public function join($groupId)
    {
        $group = Group::findOrFail($groupId);

        // Check if user is already a member
        if (GroupMember::where('group_id', $groupId)->where('user_id', Auth::id())->exists()) {
            return response()->json(['message' => 'You are already a member'], 400);
        }

        $status = ($group->privacy === 'public') ? 'approved' : 'pending';

        $member = GroupMember::create([
            'group_id' => $groupId,
            'user_id' => Auth::id(),
            'status' => $status,
        ]);

        return response()->json([
            'message' => $status === 'approved' ? 'Joined group successfully' : 'Request sent to admin',
            'member' => $member,
        ]);
    }

    //  APPROVE A MEMBER REQUEST (PRIVATE GROUPS)
    public function approveMember($groupId, $userId)
    {
        $group = Group::findOrFail($groupId);

        // Check if the authenticated user is an admin of the group
        $admin = GroupMember::where('group_id', $groupId)
            ->where('user_id', Auth::id())
            ->where('is_admin', true)
            ->first();

        if (!$admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $member = GroupMember::where('group_id', $groupId)
            ->where('user_id', $userId)
            ->where('status', 'pending')
            ->firstOrFail();

        $member->update(['status' => 'approved']);

        return response()->json(['message' => 'Member approved successfully']);
    }

    //  LEAVE A GROUP
    public function leave($groupId)
    {
        $member = GroupMember::where('group_id', $groupId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$member) {
            return response()->json(['message' => 'You are not a member of this group'], 404);
        }

        $member->delete();

        return response()->json(['message' => 'Left group successfully']);
    }

    //  REMOVE A MEMBER (ADMIN ONLY)
    public function removeMember($groupId, $userId)
    {
        $group = Group::findOrFail($groupId);

        // Check if the authenticated user is an admin of the group
        $admin = GroupMember::where('group_id', $groupId)
            ->where('user_id', Auth::id())
            ->where('is_admin', true)
            ->first();

        if (!$admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $member = GroupMember::where('group_id', $groupId)
            ->where('user_id', $userId)
            ->firstOrFail();

        $member->delete();

        return response()->json(['message' => 'Member removed successfully']);
    }

    //  MAKE ANOTHER USER ADMIN
    public function makeAdmin($groupId, $userId)
    {
        $group = Group::findOrFail($groupId);

        $admin = GroupMember::where('group_id', $groupId)
            ->where('user_id', Auth::id())
            ->where('is_admin', true)
            ->first();

        if (!$admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $member = GroupMember::where('group_id', $groupId)
            ->where('user_id', $userId)
            ->where('status', 'approved')
            ->firstOrFail();

        $member->update(['is_admin' => true]);

        return response()->json(['message' => 'User promoted to admin']);
    }

    public function unpinPost($groupId)
    {
    $group = Group::findOrFail($groupId);

    // Check if the user is an admin
    if (!GroupAdmin::where('group_id', $groupId)->where('user_id', Auth::id())->exists()) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    // Unpin the post
    $group->pinned_post_id = null;
    $group->save();

    return response()->json(['message' => 'Post unpinned successfully']);
    }
}

