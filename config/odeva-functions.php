<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Odeva Function Definitions for Anthropic Claude
    |--------------------------------------------------------------------------
    |
    | These functions are available to Odeva AI for interacting with the platform.
    | Format follows Anthropic's Tool Use API specification.
    |
    */

    'get_creator_earnings' => [
        'name' => 'get_creator_earnings',
        'description' => 'Get creator earnings for a specific time period',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'period' => [
                    'type' => 'string',
                    'enum' => ['today', 'week', 'month', 'year', 'all'],
                    'description' => 'Time period for earnings'
                ],
            ],
            'required' => ['period']
        ]
    ],

    'get_subscriber_count' => [
        'name' => 'get_subscriber_count',
        'description' => 'Get the total number of active subscribers for the creator',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'status' => [
                    'type' => 'string',
                    'enum' => ['active', 'inactive', 'all'],
                    'description' => 'Filter by subscription status'
                ],
            ],
            'required' => []
        ]
    ],

    'get_subscriber_list' => [
        'name' => 'get_subscriber_list',
        'description' => 'Get a list of subscribers with details',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'limit' => [
                    'type' => 'integer',
                    'description' => 'Number of subscribers to return (max 100)',
                    'minimum' => 1,
                    'maximum' => 100
                ],
                'offset' => [
                    'type' => 'integer',
                    'description' => 'Offset for pagination',
                    'minimum' => 0
                ],
            ],
            'required' => []
        ]
    ],

    'search_messages' => [
        'name' => 'search_messages',
        'description' => 'Search through messages and conversations',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'query' => [
                    'type' => 'string',
                    'description' => 'Search query'
                ],
                'user_id' => [
                    'type' => 'integer',
                    'description' => 'Filter by specific user ID'
                ],
                'limit' => [
                    'type' => 'integer',
                    'description' => 'Number of results (max 50)',
                    'minimum' => 1,
                    'maximum' => 50
                ],
            ],
            'required' => ['query']
        ]
    ],

    'get_analytics' => [
        'name' => 'get_analytics',
        'description' => 'Get platform analytics and statistics',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'metric' => [
                    'type' => 'string',
                    'enum' => ['views', 'likes', 'earnings', 'subscribers', 'posts', 'messages', 'overview'],
                    'description' => 'Which metric to retrieve'
                ],
                'period' => [
                    'type' => 'string',
                    'enum' => ['today', 'week', 'month', 'year'],
                    'description' => 'Time period'
                ],
            ],
            'required' => ['metric']
        ]
    ],

    'send_message' => [
        'name' => 'send_message',
        'description' => 'Send a message to a subscriber on behalf of the creator',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'user_id' => [
                    'type' => 'integer',
                    'description' => 'Recipient user ID'
                ],
                'message' => [
                    'type' => 'string',
                    'description' => 'Message content'
                ],
            ],
            'required' => ['user_id', 'message']
        ]
    ],

    'get_recent_posts' => [
        'name' => 'get_recent_posts',
        'description' => 'Get recent posts from the creator',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'limit' => [
                    'type' => 'integer',
                    'description' => 'Number of posts (max 20)',
                    'minimum' => 1,
                    'maximum' => 20
                ],
            ],
            'required' => []
        ]
    ],

    'get_post_stats' => [
        'name' => 'get_post_stats',
        'description' => 'Get statistics for a specific post',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'post_id' => [
                    'type' => 'integer',
                    'description' => 'Post ID'
                ],
            ],
            'required' => ['post_id']
        ]
    ],

    'get_pending_requests' => [
        'name' => 'get_pending_requests',
        'description' => 'Get pending subscription or content requests',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'type' => [
                    'type' => 'string',
                    'enum' => ['subscriptions', 'verifications', 'withdrawals', 'all'],
                    'description' => 'Type of pending requests'
                ],
            ],
            'required' => []
        ]
    ],

    'get_wallet_balance' => [
        'name' => 'get_wallet_balance',
        'description' => 'Get current wallet balance and transaction history',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'include_history' => [
                    'type' => 'boolean',
                    'description' => 'Include recent transaction history'
                ],
            ],
            'required' => []
        ]
    ],

    'get_top_subscribers' => [
        'name' => 'get_top_subscribers',
        'description' => 'Get top subscribers by spending or engagement',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'metric' => [
                    'type' => 'string',
                    'enum' => ['spending', 'messages', 'tips'],
                    'description' => 'Ranking metric'
                ],
                'limit' => [
                    'type' => 'integer',
                    'description' => 'Number of subscribers (max 20)',
                    'minimum' => 1,
                    'maximum' => 20
                ],
            ],
            'required' => ['metric']
        ]
    ],

    'get_unread_messages' => [
        'name' => 'get_unread_messages',
        'description' => 'Get count and preview of unread messages',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'include_preview' => [
                    'type' => 'boolean',
                    'description' => 'Include message previews'
                ],
            ],
            'required' => []
        ]
    ],

    'schedule_post' => [
        'name' => 'schedule_post',
        'description' => 'Schedule a post for later publication (future feature)',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'content' => [
                    'type' => 'string',
                    'description' => 'Post content'
                ],
                'scheduled_at' => [
                    'type' => 'string',
                    'description' => 'ISO 8601 datetime for publication'
                ],
            ],
            'required' => ['content', 'scheduled_at']
        ]
    ],

    'get_conversation_summary' => [
        'name' => 'get_conversation_summary',
        'description' => 'Get AI-generated summary of a conversation with a subscriber',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'user_id' => [
                    'type' => 'integer',
                    'description' => 'Subscriber user ID'
                ],
                'messages_limit' => [
                    'type' => 'integer',
                    'description' => 'Number of recent messages to analyze (max 100)',
                    'minimum' => 10,
                    'maximum' => 100
                ],
            ],
            'required' => ['user_id']
        ]
    ],

    'get_key_requests' => [
        'name' => 'get_key_requests',
        'description' => 'Identify and extract key requests or questions from subscribers',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'period' => [
                    'type' => 'string',
                    'enum' => ['today', 'week', 'month'],
                    'description' => 'Time period to analyze'
                ],
            ],
            'required' => []
        ]
    ],
];

