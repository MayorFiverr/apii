<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PostInGroup;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;

class PostInGroupController extends Controller
{
    public function store(Request $request, $groupId)
    {
        $group = Group::findOrFail($groupId);

        // Check if user is in the group (for private groups)
        if ($group->type === 'private' && !$group->members()->where('user_id', Auth::id())->exists()) {
            return response()->json(['message' => 'You are not a member of this group'], 403);
        }

        $request->validate([
            'content' => 'required|string|max:1000',
            'media' => 'nullable|mimes:jpeg,png,jpg,gif,mp4,mov,avi|max:20480', // 20MB max
        ]);

        $mediaPath = null;
        if ($request->hasFile('media')) {
            $mediaPath = $request->file('media')->store('group_posts', 'public');
        }

        $post = PostInGroup::create([
            'group_id' => $groupId,
            'user_id' => Auth::id(),
            'content' => $request->content,
            'media' => $mediaPath,
        ]);

        return response()->json(['message' => 'Post created successfully', 'post' => $post], 201);
    }

    public function index($groupId)
    {
        $group = Group::findOrFail($groupId);

        $posts = $group->posts()->with('user')->latest()->paginate(10);

        return response()->json(['posts' => $posts]);
    }

    public function update(Request $request, $postId)
    {
        $post = PostInGroup::findOrFail($postId);

        // Only post owner or group admin can edit
        if ($post->user_id !== auth()->id() && !$post->group->admins()->where('user_id', auth()->id())->exists()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'content' => 'required|string|max:1000',
            'media' => 'nullable|mimes:jpeg,png,jpg,gif,mp4,mov,avi|max:20480',
        ]);

        // Handle new media upload
        if ($request->hasFile('media')) {
            // Delete old media if exists
            if ($post->media) {
                \Storage::disk('public')->delete($post->media);
            }
            $post->media = $request->file('media')->store('group_posts', 'public');
        }

        $post->content = $request->content;
        $post->save();

        return response()->json(['message' => 'Post updated successfully', 'post' => $post]);
    }

    public function destroy($postId)
    {
        $post = PostInGroup::findOrFail($postId);

        // Only post owner or group admin can delete
        if ($post->user_id !== auth()->id() && !$post->group->admins()->where('user_id', auth()->id())->exists()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }   

        // Delete media file if exists
        if ($post->media) {
            \Storage::disk('public')->delete($post->media);
        }

        $post->delete();

        return response()->json(['message' => 'Post deleted successfully']);
    }
    

    private function isGroupAdmin($user, $groupId)
    {
        return GroupAdmin::where('group_id', $groupId)
                        ->where('user_id', $user->id)
                        ->exists();
    }

    public function pinPost(Request $request, $groupId, $postId)
{
    $group = Group::findOrFail($groupId);
    $post = GroupPost::where('group_id', $groupId)->findOrFail($postId);

    // Check if the user is an admin
    if (!GroupAdmin::where('group_id', $groupId)->where('user_id', Auth::id())->exists()) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    // Pin the post
    $group->pinned_post_id = $post->id;
    $group->save();

    return response()->json(['message' => 'Post pinned successfully', 'pinned_post' => $post]);
}

public function getGroupPosts($groupId)
{
    $group = Group::findOrFail($groupId);
    
    $posts = GroupPost::where('group_id', $groupId)
        ->orderByRaw("id = ? DESC", [$group->pinned_post_id]) // Pinned post first
        ->latest()
        ->paginate(10);

    return response()->json($posts);
}
    
}
