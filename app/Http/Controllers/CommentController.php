<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    // Add a comment to a post
    public function store(Request $request, $postId)
    {
        $request->validate([
            'content' => 'required|string|max:500',
        ]);

        $post = Post::findOrFail($postId);

        $comment = $post->comments()->create([
            'user_id' => Auth::id(),
            'comment' => $request->content, 
        ]);

        

        // Handle user tagging in the comment
        $this->handleTags($request->content, $comment, $post);

        return response()->json(['message' => 'Comment added successfully', 'comment' => $comment], 201);
    }

    // Handle and notify tagged users in comments
    private function handleTags($content, $comment, $post) {
        preg_match_all('/@([A-Za-z0-9_]+)/', $content, $matches);
        $usernames = $matches[1] ?? [];

        if (empty($usernames)) return;

        $taggedUsers = User::whereIn('first_name', $usernames)
                            ->where('is_fully_registered', true)
                            ->get();

        foreach ($taggedUsers as $user) {
            Notification::create([
                'user_id' => $user->id,
                'message' => Auth::user()->first_name . ' tagged you in a comment on a post.',
                'post_id' => $post->id,
            ]);
        }
    }

    // Edit a comment
    public function update(Request $request, $id)
    {
        $comment = \App\Models\PostComment::where('id', $id)->where('user_id', Auth::id())->first();

        $request->validate([
            'content' => 'required|string|max:500',
        ]);

        $comment->update([
            'content' => $request->content,
        ]);

        // Update tagged users in edited comment
        $this->handleTags($request->content, $comment, $comment->post);

        return response()->json(['message' => 'Comment updated successfully', 'comment' => $comment]);
    }

    // Delete a comment
    public function destroy($id)
    {
        $comment = Comment::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully']);
    }
}
