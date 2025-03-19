<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Like;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller {
    public function likePost($postId) {
        $post = Post::findOrFail($postId);

        // Check if the user has already liked the post
        $existingLike = Like::where('user_id', Auth::id())->where('post_id', $postId)->first();

        if ($existingLike) {
            return response()->json([
                'message' => 'You already liked this post',
                'likes_count' => Like::where('post_id', $postId)->count(),
            ], 400);
        }

        Like::create([
            'user_id' => Auth::id(),
            'post_id' => $postId,
        ]);

        return response()->json([
            'message' => 'Post liked',
            'likes_count' => Like::where('post_id', $postId)->count(),
        ]);
    }

    public function unlikePost($postId) {
        $like = Like::where('user_id', Auth::id())->where('post_id', $postId)->first();

        if (!$like) {
            return response()->json([
                'message' => 'You have not liked this post',
                'likes_count' => Like::where('post_id', $postId)->count(),
            ], 400);
        }

        $like->delete();

        return response()->json([
            'message' => 'Post unliked',
            'likes_count' => Like::where('post_id', $postId)->count(),
        ]);
    }
}
