<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    // Create a new post
    public function store(Request $request) {
        $request->validate([
            'content' => 'nullable|string|max:500',
            'media' => 'nullable|array|max:4',
            'media.*' => 'file|mimes:jpeg,png,jpg,gif,mp4,mov,avi|max:51200',
            'privacy' => 'required|in:public,private',
            'tagged_users' => 'nullable|array',
        ]);

        $mediaPaths = [];
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $mediaFile) {
                $path = $mediaFile->store('posts', 'public');
                $mediaPaths[] = $path;
            }
        }

        $post = Auth::user()->posts()->create([
            'content' => $request->content,
            'media' => $mediaPaths,
            'privacy' => $request->privacy,
        ]);

        // Handle user tagging
        $this->handleTags($request->content, $post);

        return response()->json(['message' => 'Post created successfully', 'post' => $post], 201);
    }

    // Handle and notify tagged users
    private function handleTags($content, $post) {
        preg_match_all('/@([A-Za-z0-9_]+)/', $content, $matches);
        $usernames = $matches[1] ?? []; // Extracted usernames from the content

        if (empty($usernames)) {
            return; // No tags found, so no need to proceed
        }

        // Find users by username instead of first name
        $taggedUsers = User::whereIn('username', $usernames)
                            ->where('is_fully_registered', true)
                            ->get();

        foreach ($taggedUsers as $user) {
            Notification::create([
                'user_id' => $user->id,
                'message' => Auth::user()->username . ' tagged you in a post.',
                'post_id' => $post->id,
            ]);
        }
    }

    // Get all posts (Paginated)
    public function index()
    {
        $posts = Post::with('user')->latest()->paginate(10);
        return response()->json($posts);
    }

    // Get a single post
    public function show($id)
    {
        $post = Post::with('user', 'likes', 'comments')->findOrFail($id);
        return response()->json($post);
    }

    // Update a post
    public function update(Request $request, $id)
    {
        $post = Post::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        $request->validate([
            'content' => 'nullable|string|max:500',
            'privacy' => 'required|in:public,private',
        ]);

        $post->update([
            'content' => $request->content,
            'privacy' => $request->privacy,
        ]);

        // Update tagged users
        $this->handleTags($request->content, $post);

        return response()->json(['message' => 'Post updated successfully', 'post' => $post]);
    }

    // Delete a post
    public function destroy($id)
    {
        $post = Post::where('id', $id)->where('user_id', auth()->id())->first();

        if (!$post) {
            return response()->json(['error' => 'Post not found or unauthorized'], 403);
        }

        // Delete any media files associated with the post
        if ($post->media) {
            foreach ($post->media as $media) {
                Storage::delete($media);
            }
        }

        $post->delete();

        return response()->json(['message' => 'Post deleted successfully']);
    }
}
