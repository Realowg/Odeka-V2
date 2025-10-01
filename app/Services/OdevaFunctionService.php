<?php

namespace App\Services;

use App\Models\User;
use App\Models\Subscriptions;
use App\Models\Messages;
use App\Models\Updates;
use App\Models\Transactions;
use App\Models\Tips;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OdevaFunctionService
{
    /**
     * Get all available functions for a creator
     *
     * @param int $creatorId
     * @return array
     */
    public function getFunctions($creatorId)
    {
        $functions = config('odeva-functions');
        
        // Return in Anthropic tool format
        return array_values($functions);
    }

    /**
     * Execute a function
     *
     * @param string $name
     * @param array $params
     * @param int $creatorId
     * @return mixed
     */
    public function execute($name, $params, $creatorId)
    {
        $method = 'execute' . str_replace('_', '', ucwords($name, '_'));
        
        if (!method_exists($this, $method)) {
            throw new \Exception("Function {$name} not implemented");
        }

        return $this->$method($params, $creatorId);
    }

    /**
     * Get creator earnings
     */
    protected function executeGetCreatorEarnings($params, $creatorId)
    {
        $period = $params['period'] ?? 'all';
        $creator = User::find($creatorId);

        $query = Transactions::where('user_id', $creatorId)
            ->where('type', 'credit');

        $query = $this->applyPeriodFilter($query, $period);

        $earnings = $query->sum('amount');

        return [
            'period' => $period,
            'total_earnings' => (float) $earnings,
            'currency' => config('settings.currency_code', 'USD'),
            'balance' => (float) $creator->balance,
        ];
    }

    /**
     * Get subscriber count
     */
    protected function executeGetSubscriberCount($params, $creatorId)
    {
        $status = $params['status'] ?? 'active';

        $query = Subscriptions::where('stripe_id', $creatorId);

        if ($status !== 'all') {
            $query->where('stripe_status', $status);
        }

        $count = $query->count();
        $activeCount = Subscriptions::where('stripe_id', $creatorId)
            ->where('stripe_status', 'active')
            ->count();

        return [
            'status_filter' => $status,
            'count' => $count,
            'active_count' => $activeCount,
        ];
    }

    /**
     * Get subscriber list
     */
    protected function executeGetSubscriberList($params, $creatorId)
    {
        $limit = min($params['limit'] ?? 20, 100);
        $offset = $params['offset'] ?? 0;

        $subscribers = Subscriptions::where('stripe_id', $creatorId)
            ->where('stripe_status', 'active')
            ->with('user:id,username,name,avatar')
            ->skip($offset)
            ->take($limit)
            ->get();

        return [
            'subscribers' => $subscribers->map(function($sub) {
                return [
                    'id' => $sub->user->id,
                    'username' => $sub->user->username,
                    'name' => $sub->user->name,
                    'subscribed_since' => $sub->created_at->toDateString(),
                ];
            }),
            'count' => $subscribers->count(),
        ];
    }

    /**
     * Search messages
     */
    protected function executeSearchMessages($params, $creatorId)
    {
        $query = $params['query'];
        $userId = $params['user_id'] ?? null;
        $limit = min($params['limit'] ?? 20, 50);

        $messagesQuery = Messages::where(function($q) use ($creatorId) {
            $q->where('from_user_id', $creatorId)
              ->orWhere('to_user_id', $creatorId);
        })
        ->where('message', 'LIKE', "%{$query}%")
        ->where('mode', 'active');

        if ($userId) {
            $messagesQuery->where(function($q) use ($userId, $creatorId) {
                $q->where(function($subQ) use ($userId, $creatorId) {
                    $subQ->where('from_user_id', $creatorId)
                         ->where('to_user_id', $userId);
                })->orWhere(function($subQ) use ($userId, $creatorId) {
                    $subQ->where('from_user_id', $userId)
                         ->where('to_user_id', $creatorId);
                });
            });
        }

        $messages = $messagesQuery->take($limit)->get();

        return [
            'query' => $query,
            'results_count' => $messages->count(),
            'messages' => $messages->map(function($msg) use ($creatorId) {
                return [
                    'id' => $msg->id,
                    'from_me' => $msg->from_user_id == $creatorId,
                    'message' => $msg->message,
                    'date' => $msg->created_at->toDateString(),
                ];
            }),
        ];
    }

    /**
     * Get analytics
     */
    protected function executeGetAnalytics($params, $creatorId)
    {
        $metric = $params['metric'];
        $period = $params['period'] ?? 'month';
        $creator = User::find($creatorId);

        switch ($metric) {
            case 'earnings':
                return $this->executeGetCreatorEarnings(['period' => $period], $creatorId);
            
            case 'subscribers':
                $query = Subscriptions::where('stripe_id', $creatorId);
                $query = $this->applyPeriodFilter($query, $period, 'created_at');
                return [
                    'metric' => 'subscribers',
                    'period' => $period,
                    'new_subscribers' => $query->count(),
                ];
            
            case 'posts':
                $query = Updates::where('user_id', $creatorId);
                $query = $this->applyPeriodFilter($query, $period, 'created_at');
                return [
                    'metric' => 'posts',
                    'period' => $period,
                    'posts_count' => $query->count(),
                ];
            
            case 'likes':
                $postIds = Updates::where('user_id', $creatorId)->pluck('id');
                $query = DB::table('likes')->whereIn('updates_id', $postIds);
                $query = $this->applyPeriodFilter($query, $period, 'created_at');
                return [
                    'metric' => 'likes',
                    'period' => $period,
                    'likes_count' => $query->count(),
                ];
            
            case 'messages':
                $query = Messages::where('from_user_id', $creatorId);
                $query = $this->applyPeriodFilter($query, $period, 'created_at');
                return [
                    'metric' => 'messages',
                    'period' => $period,
                    'messages_sent' => $query->count(),
                ];
            
            case 'overview':
                return [
                    'metric' => 'overview',
                    'period' => $period,
                    'balance' => (float) $creator->balance,
                    'total_subscribers' => Subscriptions::where('stripe_id', $creatorId)->where('stripe_status', 'active')->count(),
                    'total_posts' => Updates::where('user_id', $creatorId)->count(),
                    'total_earnings' => (float) Transactions::where('user_id', $creatorId)->where('type', 'credit')->sum('amount'),
                ];
            
            default:
                throw new \Exception("Unknown metric: {$metric}");
        }
    }

    /**
     * Send message (on behalf of creator)
     */
    protected function executeSendMessage($params, $creatorId)
    {
        $userId = $params['user_id'];
        $message = $params['message'];

        // Verify user exists and is a subscriber
        $user = User::find($userId);
        if (!$user) {
            throw new \Exception('User not found');
        }

        // Create conversation if needed
        $conversation = DB::table('conversations')
            ->where(function($q) use ($creatorId, $userId) {
                $q->where('user_1', $creatorId)->where('user_2', $userId);
            })
            ->orWhere(function($q) use ($creatorId, $userId) {
                $q->where('user_1', $userId)->where('user_2', $creatorId);
            })
            ->first();

        if (!$conversation) {
            $conversationId = DB::table('conversations')->insertGetId([
                'user_1' => $creatorId,
                'user_2' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $conversationId = $conversation->id;
        }

        // Send message
        $messageId = Messages::insertGetId([
            'conversations_id' => $conversationId,
            'from_user_id' => $creatorId,
            'to_user_id' => $userId,
            'message' => $message,
            'mode' => 'active',
            'status' => 'new',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [
            'success' => true,
            'message_id' => $messageId,
            'sent_to' => $user->username,
        ];
    }

    /**
     * Get recent posts
     */
    protected function executeGetRecentPosts($params, $creatorId)
    {
        $limit = min($params['limit'] ?? 10, 20);

        $posts = Updates::where('user_id', $creatorId)
            ->orderBy('id', 'desc')
            ->take($limit)
            ->get();

        return [
            'posts' => $posts->map(function($post) {
                return [
                    'id' => $post->id,
                    'description' => substr($post->description, 0, 200),
                    'likes' => $post->likes()->count(),
                    'comments' => $post->comments()->count(),
                    'created_at' => $post->created_at->toDateString(),
                ];
            }),
        ];
    }

    /**
     * Get post stats
     */
    protected function executeGetPostStats($params, $creatorId)
    {
        $postId = $params['post_id'];
        $post = Updates::find($postId);

        if (!$post || $post->user_id != $creatorId) {
            throw new \Exception('Post not found or not owned by creator');
        }

        return [
            'post_id' => $postId,
            'likes' => $post->likes()->count(),
            'comments' => $post->comments()->count(),
            'created_at' => $post->created_at->toIso8601String(),
        ];
    }

    /**
     * Get wallet balance
     */
    protected function executeGetWalletBalance($params, $creatorId)
    {
        $creator = User::find($creatorId);
        $includeHistory = $params['include_history'] ?? false;

        $result = [
            'balance' => (float) $creator->balance,
            'currency' => config('settings.currency_code', 'USD'),
        ];

        if ($includeHistory) {
            $recentTransactions = Transactions::where('user_id', $creatorId)
                ->orderBy('id', 'desc')
                ->take(10)
                ->get();

            $result['recent_transactions'] = $recentTransactions->map(function($tx) {
                return [
                    'id' => $tx->id,
                    'type' => $tx->type,
                    'amount' => (float) $tx->amount,
                    'description' => $tx->description,
                    'date' => $tx->created_at->toDateString(),
                ];
            });
        }

        return $result;
    }

    /**
     * Get unread messages
     */
    protected function executeGetUnreadMessages($params, $creatorId)
    {
        $includePreview = $params['include_preview'] ?? false;

        $unreadCount = Messages::where('to_user_id', $creatorId)
            ->where('status', 'new')
            ->where('mode', 'active')
            ->count();

        $result = [
            'unread_count' => $unreadCount,
        ];

        if ($includePreview && $unreadCount > 0) {
            $previews = Messages::where('to_user_id', $creatorId)
                ->where('status', 'new')
                ->where('mode', 'active')
                ->with('sender:id,username,name')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            $result['previews'] = $previews->map(function($msg) {
                return [
                    'from' => $msg->sender->username,
                    'message' => substr($msg->message, 0, 100),
                    'time' => $msg->created_at->diffForHumans(),
                ];
            });
        }

        return $result;
    }

    /**
     * Apply period filter to query
     */
    protected function applyPeriodFilter($query, $period, $field = 'created_at')
    {
        switch ($period) {
            case 'today':
                return $query->whereDate($field, Carbon::today());
            case 'week':
                return $query->where($field, '>=', Carbon::now()->subWeek());
            case 'month':
                return $query->where($field, '>=', Carbon::now()->subMonth());
            case 'year':
                return $query->where($field, '>=', Carbon::now()->subYear());
            default:
                return $query;
        }
    }
}

