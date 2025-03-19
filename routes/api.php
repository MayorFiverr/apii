<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\WorkExperienceController;
use App\Http\Controllers\EducationController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\CompanyOverviewController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;


// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes (only accessible with authentication)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    // Edit different sections of the profile
    Route::post('/profile/update-bio', [ProfileController::class, 'updateBio']);
    Route::post('/profile/add-work-experience', [ProfileController::class, 'updateWorkExperience']);
    Route::post('/profile/add-education', [ProfileController::class, 'updateEducation']);
    Route::post('/profile/update-skills', [ProfileController::class, 'updateSkills']);
    Route::post('/profile/update-company-overview', [ProfileController::class, 'updateCompanyOverview']);
    Route::post('/profile/upload-profile-picture', [ProfileController::class, 'uploadProfilePicture']);
    Route::post('/profile/upload-banner-picture', [ProfileController::class, 'uploadBannerPicture']);
    Route::get('/profile', [ProfileController::class, 'getProfile']);

    // User Profile
    Route::get('/user/profile', [UserProfileController::class, 'show']);
    Route::post('/user/profile/update', [UserProfileController::class, 'update']);
    Route::post('/user/profile/upload-picture', [UserProfileController::class, 'uploadProfilePicture']);
    Route::post('/user/profile/upload-banner', [UserProfileController::class, 'uploadBannerPicture']);

    // Work Experience
    Route::post('/user/work-experience', [WorkExperienceController::class, 'store']);
    Route::delete('/user/work-experience/{id}', [WorkExperienceController::class, 'destroy']);

    // Education
    Route::post('/user/education', [EducationController::class, 'store']);
    Route::delete('/user/education/{id}', [EducationController::class, 'destroy']);

    // Skills
    Route::put('/user/skills', [SkillController::class, 'store']);
    Route::delete('/user/skills/{id}', [SkillController::class, 'destroy']);

    // Company Overview
    Route::post('/user/company-overview', [CompanyOverviewController::class, 'store']);
    Route::delete('/user/company-overview/{id}', [CompanyOverviewController::class, 'destroy']);

    
    Route::post('/posts', [PostController::class, 'store']); // Create a post
    Route::get('/posts', [PostController::class, 'index']); // Get all posts
    Route::get('/posts/{id}', [PostController::class, 'show']); // Get a single post
    Route::put('/posts/{id}', [PostController::class, 'update']); // Update a post
    Route::delete('/posts/{id}', [PostController::class, 'destroy']); // Delete a post

    // Likes
    Route::post('/post/{postId}/like', [LikeController::class, 'likePost']);
    Route::delete('/post/{postId}/unlike', [LikeController::class, 'unlikePost']);

    // Comments
    Route::post('/post/{postId}/comment', [CommentController::class, 'store']);
    Route::get('/post/{postId}/comments', [CommentController::class, 'getComments']);
    Route::put('/comment/{commentId}', [CommentController::class, 'updateComment']);
    Route::delete('/comment/{commentId}', [CommentController::class, 'deleteComment']);

    //Group Feature
    Route::post('/groups', [GroupController::class, 'store']); // Create group
    Route::post('/groups/{groupId}/join', [GroupController::class, 'join']); // Join or request to join
    Route::post('/groups/{groupId}/approve/{userId}', [GroupController::class, 'approveMember']); // Approve member
    Route::post('/groups/{groupId}/leave', [GroupController::class, 'leave']); // Leave group
    Route::post('/groups/{groupId}/remove/{userId}', [GroupController::class, 'removeMember']); // Remove member
    Route::post('/groups/{groupId}/make-admin/{userId}', [GroupController::class, 'makeAdmin']); // Make admin
    Route::post('/groups/{groupId}/posts', [PostInGroupController::class, 'store']);
    Route::get('/groups/{groupId}/posts', [PostInGroupController::class, 'index']);
    Route::put('/groups/posts/{postId}', [PostInGroupController::class, 'update']); // Edit Post
    Route::delete('/groups/posts/{postId}', [PostInGroupController::class, 'destroy']); // Delete Post
    Route::post('/groups/{group}/pin/{post}', [PostInGroupController::class, 'pinPost']);
    Route::post('/groups/{group}/unpin', [PostInGroupController::class, 'unpinPost']);
});


