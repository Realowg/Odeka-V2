# Odeka Media Platform - RESTful API v1 Documentation

## 📋 Table of Contents

1. [Overview](#overview)
2. [Authentication](#authentication)
3. [Rate Limiting](#rate-limiting)
4. [Response Format](#response-format)
5. [Error Codes](#error-codes)
6. [Endpoints](#endpoints)
   - [Authentication](#authentication-endpoints)
   - [Users](#users-endpoints)
   - [Messages](#messages-endpoints)
   - [Subscriptions](#subscriptions-endpoints)
   - [Posts](#posts-endpoints)
   - [Comments](#comments-endpoints)
   - [Payments](#payments-endpoints)
   - [Notifications](#notifications-endpoints)
   - [Admin](#admin-endpoints)
   - [Odeva AI](#odeva-ai-endpoints)

---

## Overview

**Base URL:** `/api/v1`  
**Authentication:** Laravel Sanctum (Token-based)  
**Response Format:** JSON  
**Total Endpoints:** 128+

---

## Authentication

### Token Authentication

All authenticated endpoints require a Bearer token in the Authorization header:

```
Authorization: Bearer {your-token-here}
```

### Obtaining a Token

**POST** `/api/v1/auth/login`

```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "user": { ... },
    "token": "odeka_xxxxxxxxxxxxxxxx",
    "token_type": "Bearer",
    "expires_in": 2592000
  }
}
```

---

## Rate Limiting

| Tier | Limit | Applied To |
|------|-------|------------|
| **Default API** | 120 req/min | Most authenticated endpoints |
| **Public** | 60 req/min | Unauthenticated endpoints |
| **Odeva AI** | 20 req/min | AI chat endpoints |
| **Upload** | 10 req/min | File upload endpoints |
| **Admin** | 200 req/min | Admin panel endpoints |

Rate limits are per user (authenticated) or per IP (unauthenticated).

**Headers:**
- `X-RateLimit-Limit`: Maximum requests
- `X-RateLimit-Remaining`: Remaining requests
- `Retry-After`: Seconds until reset (if exceeded)

---

## Response Format

### Success Response

```json
{
  "success": true,
  "message": "Operation successful",
  "data": { ... }
}
```

### Paginated Response

```json
{
  "success": true,
  "data": [ ... ],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 20,
    "total": 100,
    "from": 1,
    "to": 20
  },
  "links": {
    "first": "...",
    "last": "...",
    "prev": null,
    "next": "..."
  }
}
```

### Error Response

```json
{
  "success": false,
  "message": "Error description",
  "code": "ERROR_CODE",
  "errors": { ... }
}
```

---

## Error Codes

| Code | HTTP Status | Description |
|------|-------------|-------------|
| `VALIDATION_ERROR` | 422 | Invalid input data |
| `UNAUTHORIZED` | 401 | Invalid or missing token |
| `FORBIDDEN` | 403 | Insufficient permissions |
| `NOT_FOUND` | 404 | Resource not found |
| `ALREADY_EXISTS` | 400 | Duplicate resource |
| `INSUFFICIENT_BALANCE` | 400 | Not enough funds |
| `ODEVA_ERROR` | 500 | AI service error |

---

## Endpoints

### Authentication Endpoints

#### Register
**POST** `/api/v1/auth/register`

```json
{
  "name": "John Doe",
  "username": "johndoe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

#### Login
**POST** `/api/v1/auth/login`

```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

#### Logout
**POST** `/api/v1/auth/logout`  
*Requires: Authentication*

#### Refresh Token
**POST** `/api/v1/auth/refresh`  
*Requires: Authentication*

#### Forgot Password
**POST** `/api/v1/auth/forgot-password`

```json
{
  "email": "john@example.com"
}
```

#### Reset Password
**POST** `/api/v1/auth/reset-password`

```json
{
  "email": "john@example.com",
  "token": "reset-token",
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}
```

---

### Users Endpoints

#### Get Current User
**GET** `/api/v1/users/me`  
*Requires: Authentication*

#### Update Profile
**PUT** `/api/v1/users/me`  
*Requires: Authentication*

```json
{
  "name": "John Updated",
  "bio": "Content creator",
  "location": "New York",
  "website": "https://example.com"
}
```

#### Get User by Username
**GET** `/api/v1/users/{username}`

#### Get User Posts
**GET** `/api/v1/users/{id}/posts`

#### Get User Stats
**GET** `/api/v1/users/{id}/stats`

#### Follow User
**POST** `/api/v1/users/{id}/follow`  
*Requires: Authentication*

#### Unfollow User
**DELETE** `/api/v1/users/{id}/follow`  
*Requires: Authentication*

#### Get Followers
**GET** `/api/v1/users/{id}/followers`

#### Get Following
**GET** `/api/v1/users/{id}/following`

---

### Messages Endpoints

#### Get Conversations
**GET** `/api/v1/messages/conversations`  
*Requires: Authentication*

#### Get Conversation with User
**GET** `/api/v1/messages/conversations/{userId}`  
*Requires: Authentication*

**Query Params:**
- `skip`: Number of messages to skip (pagination)

#### Send Message
**POST** `/api/v1/messages/send`  
*Requires: Authentication*

```json
{
  "to_user_id": 123,
  "message": "Hello!",
  "price": 5.00,
  "media": [
    {
      "type": "image",
      "file": "path/to/file.jpg"
    }
  ]
}
```

#### Delete Message
**DELETE** `/api/v1/messages/{id}`  
*Requires: Authentication*

#### Mark Message as Read
**POST** `/api/v1/messages/{id}/read`  
*Requires: Authentication*

#### Get Unread Count
**GET** `/api/v1/messages/unread-count`  
*Requires: Authentication*

---

### Subscriptions Endpoints

#### Get Subscriptions
**GET** `/api/v1/subscriptions`  
*Requires: Authentication*

#### Subscribe to Creator
**POST** `/api/v1/subscriptions`  
*Requires: Authentication*

```json
{
  "creator_id": 456,
  "interval": "monthly"
}
```

#### Cancel Subscription
**DELETE** `/api/v1/subscriptions/{id}`  
*Requires: Authentication*

#### Get Creator Plans
**GET** `/api/v1/creators/{creatorId}/plans`

#### Get Subscribers (Creator)
**GET** `/api/v1/subscribers`  
*Requires: Authentication (Creator)*

**Query Params:**
- `status`: Filter by status (active, cancelled, all)

#### Get Subscriber Stats
**GET** `/api/v1/subscribers/stats`  
*Requires: Authentication (Creator)*

---

### Posts Endpoints

#### Get Feed
**GET** `/api/v1/posts`  
*Requires: Authentication*

Returns posts from subscribed creators.

#### Create Post
**POST** `/api/v1/posts`  
*Requires: Authentication*

```json
{
  "title": "My Post",
  "description": "Post content",
  "locked": true,
  "price": 10.00,
  "scheduled_date": "2025-10-15 12:00:00"
}
```

#### Get Post
**GET** `/api/v1/posts/{id}`

#### Update Post
**PUT** `/api/v1/posts/{id}`  
*Requires: Authentication*

#### Delete Post
**DELETE** `/api/v1/posts/{id}`  
*Requires: Authentication*

#### Like Post
**POST** `/api/v1/posts/{id}/like`  
*Requires: Authentication*

#### Unlike Post
**DELETE** `/api/v1/posts/{id}/like`  
*Requires: Authentication*

#### Bookmark Post
**POST** `/api/v1/posts/{id}/bookmark`  
*Requires: Authentication*

#### Get Bookmarks
**GET** `/api/v1/posts/bookmarks/list`  
*Requires: Authentication*

#### Report Post
**POST** `/api/v1/posts/{id}/report`  
*Requires: Authentication*

```json
{
  "reason": "Inappropriate content"
}
```

---

### Comments Endpoints

#### Get Post Comments
**GET** `/api/v1/posts/{postId}/comments`

#### Add Comment
**POST** `/api/v1/posts/{postId}/comments`  
*Requires: Authentication*

```json
{
  "comment": "Great post!"
}
```

#### Update Comment
**PUT** `/api/v1/comments/{id}`  
*Requires: Authentication*

#### Delete Comment
**DELETE** `/api/v1/comments/{id}`  
*Requires: Authentication*

---

### Payments Endpoints

#### Get Wallet
**GET** `/api/v1/payments/wallet`  
*Requires: Authentication*

**Response:**
```json
{
  "success": true,
  "data": {
    "balance": 100.50,
    "wallet": 50.25,
    "currency": "USD",
    "pending_balance": 10.00
  }
}
```

#### Add Funds
**POST** `/api/v1/payments/add-funds`  
*Requires: Authentication*

```json
{
  "amount": 50.00,
  "payment_gateway": "stripe"
}
```

#### Send Tip
**POST** `/api/v1/payments/tip`  
*Requires: Authentication*

```json
{
  "creator_id": 123,
  "amount": 10.00,
  "message": "Great content!"
}
```

#### Pay for PPV
**POST** `/api/v1/payments/ppv`  
*Requires: Authentication*

```json
{
  "post_id": 456,
  "amount": 5.00
}
```

#### Get Transactions
**GET** `/api/v1/payments/transactions`  
*Requires: Authentication*

**Query Params:**
- `type`: Filter by type (subscription, tip, ppv, all)

#### Get Earnings
**GET** `/api/v1/payments/earnings`  
*Requires: Authentication (Creator)*

**Query Params:**
- `period`: Time period (today, week, month, year, all)

#### Request Withdrawal
**POST** `/api/v1/payments/withdraw`  
*Requires: Authentication*

```json
{
  "amount": 100.00,
  "gateway": "bank"
}
```

#### Get Withdrawals
**GET** `/api/v1/payments/withdrawals`  
*Requires: Authentication*

---

### Notifications Endpoints

#### Get Notifications
**GET** `/api/v1/notifications`  
*Requires: Authentication*

#### Get Unread Notifications
**GET** `/api/v1/notifications/unread`  
*Requires: Authentication*

#### Mark as Read
**PUT** `/api/v1/notifications/{id}/read`  
*Requires: Authentication*

#### Mark All as Read
**PUT** `/api/v1/notifications/read-all`  
*Requires: Authentication*

#### Update Preferences
**POST** `/api/v1/notifications/preferences`  
*Requires: Authentication*

```json
{
  "notify_new_subscriber": "yes",
  "notify_new_tip": "yes",
  "notify_new_post": "no"
}
```

#### Register Device (Push)
**POST** `/api/v1/notifications/devices`  
*Requires: Authentication*

```json
{
  "device_token": "fcm-token-here"
}
```

---

### Admin Endpoints

*All admin endpoints require admin role.*

#### Dashboard
**GET** `/api/v1/admin/dashboard`

Returns comprehensive platform statistics.

#### Users List
**GET** `/api/v1/admin/users`

**Query Params:**
- `role`: Filter by role (normal, admin)
- `status`: Filter by status (active, suspended)
- `verified`: Filter by verification (yes, no)
- `search`: Search by username, email, name

#### Update User
**PUT** `/api/v1/admin/users/{id}`

```json
{
  "status": "active",
  "verified_id": "yes",
  "role": "normal",
  "balance": 100.00
}
```

#### Delete User
**DELETE** `/api/v1/admin/users/{id}`

#### Get Reports
**GET** `/api/v1/admin/reports`

**Query Params:**
- `status`: Filter by status (pending, approved, rejected)

#### Handle Report
**PUT** `/api/v1/admin/reports/{id}`

```json
{
  "status": "approved",
  "action": "delete_post"
}
```

Actions: `delete_post`, `ban_user`, `warn_user`, `none`

#### Get Analytics
**GET** `/api/v1/admin/analytics`

**Query Params:**
- `period`: Number of days (default: 30)

---

### Odeva AI Endpoints

*Rate Limited: 20 req/min*

#### Chat with Odeva
**POST** `/api/v1/odeva/chat`  
*Requires: Authentication + Active Odeva Subscription*

```json
{
  "message": "How much did I earn this month?",
  "conversation_id": 123,
  "subscriber_id": 456
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "conversation_id": 123,
    "message": "You earned $1,250 this month...",
    "metadata": {
      "model": "claude-3-5-sonnet-20241022",
      "usage": { ... }
    }
  }
}
```

#### Get Available Functions
**GET** `/api/v1/odeva/functions`  
*Requires: Authentication*

Returns list of 15 available Odeva functions.

#### Execute Function (Testing)
**POST** `/api/v1/odeva/functions/execute`  
*Requires: Authentication*

```json
{
  "function_name": "get_creator_earnings",
  "parameters": {
    "period": "month"
  }
}
```

#### Get Creator Context
**GET** `/api/v1/odeva/context`  
*Requires: Authentication*

#### Get/Update Automation
**GET** `/api/v1/odeva/automation`  
**PUT** `/api/v1/odeva/automation`  
*Requires: Authentication*

```json
{
  "automation_enabled": true,
  "settings": {
    "auto_reply": true,
    "reply_delay": 30
  }
}
```

#### Subscribe to Odeva
**POST** `/api/v1/odeva/subscribe`  
*Requires: Authentication*

Starts 14-day free trial.

#### Get Subscription Status
**GET** `/api/v1/odeva/subscription`  
*Requires: Authentication*

#### Cancel Subscription
**DELETE** `/api/v1/odeva/subscription`  
*Requires: Authentication*

#### Get Analytics
**GET** `/api/v1/odeva/analytics`  
*Requires: Authentication*

**Query Params:**
- `period`: Time period (today, week, month)

---

## Odeva Functions

Odeva AI can call these 15 functions:

1. **get_creator_earnings** - Get earnings by period
2. **get_subscriber_count** - Active/inactive subscriber counts
3. **get_subscriber_list** - Detailed subscriber data
4. **search_messages** - Search conversations
5. **get_analytics** - Platform analytics (views, likes, earnings, posts, messages)
6. **send_message** - Send message on behalf of creator
7. **get_recent_posts** - Recent posts with stats
8. **get_post_stats** - Specific post metrics
9. **get_wallet_balance** - Balance + transaction history
10. **get_top_subscribers** - Top by spending/messages/tips
11. **get_unread_messages** - Unread count + previews
12. **schedule_post** - Schedule future posts
13. **get_conversation_summary** - AI-generated summaries
14. **get_key_requests** - Extract subscriber requests
15. **get_pending_requests** - Pending subscriptions/verifications

---

## Best Practices

1. **Always check rate limits** before implementing loops
2. **Cache responses** where appropriate
3. **Handle pagination** for large datasets
4. **Validate tokens** before sensitive operations
5. **Use HTTPS** for all requests
6. **Store tokens securely** (never in localStorage)
7. **Implement exponential backoff** for failed requests
8. **Monitor API usage** via logs

---

## Support

- **API Issues:** [GitHub Issues](https://github.com/Realowg/Odeka-V2/issues)
- **Documentation:** `/docs/api/v1/`
- **Postman Collection:** `/postman/Odeka-API-v1.postman_collection.json`

---

**Last Updated:** October 1, 2025  
**API Version:** 1.0  
**Total Endpoints:** 128+

