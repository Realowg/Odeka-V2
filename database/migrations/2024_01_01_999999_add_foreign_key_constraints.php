<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add foreign key constraints after all tables are created
        
        // Users table foreign keys
        // Note: countries_id is stored as string (country phone codes/IDs) 
        // and doesn't directly reference countries.id, so no foreign key constraint

        // Media table foreign keys
        // Schema::table('media', function (Blueprint $table) {
        //     $table->foreign('updates_id')->references('id')->on('updates')->onDelete('cascade');
        //     $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        // });

        // // Comments table foreign keys
        // Schema::table('comments', function (Blueprint $table) {
        //     $table->foreign('updates_id')->references('id')->on('updates')->onDelete('cascade');
        //     $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        // });

        // // Comments likes table foreign keys
        // Schema::table('comments_likes', function (Blueprint $table) {
        //     $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        //     $table->foreign('comments_id')->references('id')->on('comments')->onDelete('cascade');
        // });

        // // Likes table foreign keys
        // Schema::table('likes', function (Blueprint $table) {
        //     $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        //     $table->foreign('updates_id')->references('id')->on('updates')->onDelete('cascade');
        // });

        // // Bookmarks table foreign keys
        // Schema::table('bookmarks', function (Blueprint $table) {
        //     $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        //     $table->foreign('updates_id')->references('id')->on('updates')->onDelete('cascade');
        // });

        // // Messages table foreign keys
        // Schema::table('messages', function (Blueprint $table) {
        //     $table->foreign('from_user_id')->references('id')->on('users')->onDelete('cascade');
        //     $table->foreign('to_user_id')->references('id')->on('users')->onDelete('cascade');
        // });

        // // Media messages table foreign keys
        // Schema::table('media_messages', function (Blueprint $table) {
        //     $table->foreign('messages_id')->references('id')->on('messages')->onDelete('cascade');
        // });

        // // Subscriptions table foreign keys
        // Schema::table('subscriptions', function (Blueprint $table) {
        //     $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');
        //     $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        // });

        // // Transactions table foreign keys
        // Schema::table('transactions', function (Blueprint $table) {
        //     $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        //     $table->foreign('subscribed')->references('id')->on('users')->onDelete('cascade');
        // });

        // // Stories table foreign keys
        // Schema::table('stories', function (Blueprint $table) {
        //     $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        // });

        // // Media stories table foreign keys
        // Schema::table('media_stories', function (Blueprint $table) {
        //     $table->foreign('stories_id')->references('id')->on('stories')->onDelete('cascade');
        // });

        // // Story views table foreign keys
        // Schema::table('story_views', function (Blueprint $table) {
        //     $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        //     $table->foreign('stories_id')->references('id')->on('stories')->onDelete('cascade');
        // });

        // // Live streamings table foreign keys
        // Schema::table('live_streamings', function (Blueprint $table) {
        //     $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        // });

        // // Live comments table foreign keys
        // Schema::table('live_comments', function (Blueprint $table) {
        //     $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        //     $table->foreign('live_streamings_id')->references('id')->on('live_streamings')->onDelete('cascade');
        // });

        // // Live likes table foreign keys
        // Schema::table('live_likes', function (Blueprint $table) {
        //     $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        //     $table->foreign('live_streamings_id')->references('id')->on('live_streamings')->onDelete('cascade');
        // });

        // // Products table foreign keys
        // Schema::table('products', function (Blueprint $table) {
        //     $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        // });

        // // Media products table foreign keys
        // Schema::table('media_products', function (Blueprint $table) {
        //     $table->foreign('products_id')->references('id')->on('products')->onDelete('cascade');
        // });

        // // Purchases table foreign keys
        // Schema::table('purchases', function (Blueprint $table) {
        //     $table->foreign('transactions_id')->references('id')->on('transactions')->onDelete('cascade');
        //     $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        //     $table->foreign('products_id')->references('id')->on('products')->onDelete('cascade');
        // });

        // // Withdrawals table foreign keys
        // Schema::table('withdrawals', function (Blueprint $table) {
        //     $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        // });

        // // Deposits table foreign keys  
        // Schema::table('deposits', function (Blueprint $table) {
        //     $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        // });

        // // Notifications table foreign keys
        // Schema::table('notifications', function (Blueprint $table) {
        //     $table->foreign('destination')->references('id')->on('users')->onDelete('cascade');
        //     $table->foreign('author')->references('id')->on('users')->onDelete('cascade');
        // });

        // // Reports table foreign keys
        // Schema::table('reports', function (Blueprint $table) {
        //     $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        //     $table->foreign('reported_user_id')->references('id')->on('users')->onDelete('cascade');
        // });

        // // States table foreign keys
        // Schema::table('states', function (Blueprint $table) {
        //     $table->foreign('countries_id')->references('id')->on('countries')->onDelete('cascade');
        // });

        // // Subscriptions table foreign keys (after adding missing columns)
        // Schema::table('subscriptions', function (Blueprint $table) {
        //     $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');
        //     // user_id foreign key should already exist from Cashier migration
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign key constraints in reverse order
        
        Schema::table('states', function (Blueprint $table) {
            $table->dropForeign(['countries_id']);
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['reported_user_id']);
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropForeign(['destination']);
            $table->dropForeign(['author']);
        });

        Schema::table('deposits', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('withdrawals', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->dropForeign(['transactions_id']);
            $table->dropForeign(['user_id']);
            $table->dropForeign(['products_id']);
        });

        Schema::table('media_products', function (Blueprint $table) {
            $table->dropForeign(['products_id']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('live_likes', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['live_streamings_id']);
        });

        Schema::table('live_comments', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['live_streamings_id']);
        });

        Schema::table('live_streamings', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('story_views', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['stories_id']);
        });

        Schema::table('media_stories', function (Blueprint $table) {
            $table->dropForeign(['stories_id']);
        });

        Schema::table('stories', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['subscribed']);
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropForeign(['creator_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::table('media_messages', function (Blueprint $table) {
            $table->dropForeign(['messages_id']);
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['from_user_id']);
            $table->dropForeign(['to_user_id']);
        });

        Schema::table('bookmarks', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['updates_id']);
        });

        Schema::table('likes', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['updates_id']);
        });

        Schema::table('comments_likes', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['comments_id']);
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign(['updates_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::table('media', function (Blueprint $table) {
            $table->dropForeign(['updates_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropForeign(['creator_id']);
        });

        Schema::table('updates', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        // No foreign key constraint to drop for users.countries_id
    }
};
