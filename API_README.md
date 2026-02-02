# Chat Application Backend API

A Slack-like chat application backend built with Laravel and MongoDB. This is an API-only project with NO frontend and NO real-time messaging features.

## 🚀 Features

- **Authentication & Authorization** - Token-based authentication using Laravel Sanctum
- **Workspaces** - Create and manage workspaces with members
- **Teams** - Organize teams within workspaces
- **Channels** - Public and private channels for team communication
- **Messages** - Channel messages and direct messages (1-to-1)
- **File Attachments** - Upload and manage files using MongoDB GridFS
- **Email Notifications** - Workspace and team invitation emails
- **Caching** - Laravel Cache for improved performance
- **Throttling** - Rate limiting on sensitive endpoints
- **Middleware** - Access control for workspaces, teams, and channels

## 📋 Requirements

- PHP 8.2 or higher
- Composer
- MongoDB 4.4 or higher
- Laravel 12.x

## 🛠️ Installation

### 1. Clone the repository
```bash
git clone <repository-url>
cd chat-backend
```

### 2. Install dependencies
```bash
composer install
```

### 3. Configure environment
```bash
copy .env.example .env
```

Update `.env` with your MongoDB credentials:
```env
DB_CONNECTION=mongodb
DB_HOST=127.0.0.1
DB_PORT=27017
DB_DATABASE=chat_app
DB_USERNAME=
DB_PASSWORD=
DB_AUTHENTICATION_DATABASE=admin

CACHE_STORE=database
CACHE_PREFIX=chat_app_cache

MAIL_MAILER=log
```

### 4. Generate application key
```bash
php artisan key:generate
```

### 5. Start the development server
```bash
php artisan serve
```

The API will be available at `http://localhost:8000/api`

## 📁 Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── WorkspaceController.php
│   │   ├── TeamController.php
│   │   ├── ChannelController.php
│   │   ├── MessageController.php
│   │   └── FileAttachmentController.php
│   └── Middleware/
│       ├── CheckWorkspaceAccess.php
│       ├── CheckTeamAccess.php
│       └── CheckChannelAccess.php
├── Mail/
│   ├── WorkspaceInvitation.php
│   └── TeamInvitation.php
└── Models/
    ├── User.php
    ├── Workspace.php
    ├── Team.php
    ├── Channel.php
    ├── Message.php
    └── FileAttachment.php

resources/
└── views/
    └── emails/
        ├── workspace-invitation.blade.php
        └── team-invitation.blade.php

routes/
└── api.php
```

## 🔐 Authentication

The API uses Laravel Sanctum for token-based authentication.

### Register
```http
POST /api/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

### Login
```http
POST /api/login
Content-Type: application/json

{
    "email": "john@example.com",
    "password": "password123"
}
```

Response includes `access_token` - use this for authenticated requests:
```http
Authorization: Bearer {access_token}
```

## 📚 API Modules

### 1. Authentication
- `POST /api/register` - Register new user
- `POST /api/login` - Login (throttled: 5 attempts/minute)
- `POST /api/logout` - Logout
- `GET /api/profile` - Get user profile
- `PUT /api/profile` - Update user profile

### 2. Workspaces
- `GET /api/workspaces` - List all workspaces
- `POST /api/workspaces` - Create workspace
- `GET /api/workspaces/{id}` - Get single workspace
- `PUT /api/workspaces/{id}` - Update workspace
- `DELETE /api/workspaces/{id}` - Delete workspace
- `POST /api/workspaces/{id}/members` - Add member
- `DELETE /api/workspaces/{id}/members` - Remove member

### 3. Teams
- `GET /api/workspaces/{workspace}/teams` - List teams
- `POST /api/workspaces/{workspace}/teams` - Create team
- `GET /api/workspaces/{workspace}/teams/{team}` - Get team
- `PUT /api/workspaces/{workspace}/teams/{team}` - Update team
- `DELETE /api/workspaces/{workspace}/teams/{team}` - Delete team
- `POST /api/workspaces/{workspace}/teams/{team}/members` - Add member
- `DELETE /api/workspaces/{workspace}/teams/{team}/members` - Remove member

### 4. Channels
- `GET /api/workspaces/{workspace}/teams/{team}/channels` - List channels
- `POST /api/workspaces/{workspace}/teams/{team}/channels` - Create channel
- `GET /api/workspaces/{workspace}/teams/{team}/channels/{channel}` - Get channel
- `PUT /api/workspaces/{workspace}/teams/{team}/channels/{channel}` - Update channel
- `DELETE /api/workspaces/{workspace}/teams/{team}/channels/{channel}` - Delete channel
- `POST /api/workspaces/{workspace}/teams/{team}/channels/{channel}/members` - Add member (private channels)
- `DELETE /api/workspaces/{workspace}/teams/{team}/channels/{channel}/members` - Remove member

### 5. Messages
- `GET /api/workspaces/{workspace}/teams/{team}/channels/{channel}/messages` - List channel messages
- `POST /api/workspaces/{workspace}/teams/{team}/channels/{channel}/messages` - Send channel message
- `GET /api/messages/direct?user_id={id}` - List direct messages
- `POST /api/messages/direct` - Send direct message
- `PUT /api/messages/{message}` - Update message
- `DELETE /api/messages/{message}` - Delete message (soft delete)

### 6. File Attachments (GridFS)
- `GET /api/files` - List my uploaded files
- `POST /api/files/upload` - Upload file to GridFS
- `GET /api/files/{file}/download` - Download file from GridFS
- `DELETE /api/files/{file}` - Delete file from GridFS

## 🗄️ Database Schema

### Collections

#### users
```javascript
{
    _id: ObjectId,
    name: String,
    email: String (unique),
    password: String (hashed),
    profile_picture: String,
    status: String (active|away|busy|offline),
    last_seen_at: DateTime,
    created_at: DateTime,
    updated_at: DateTime
}
```

#### workspaces
```javascript
{
    _id: ObjectId,
    name: String,
    description: String,
    owner_id: ObjectId (ref: users),
    member_ids: [ObjectId],
    settings: Object,
    created_at: DateTime,
    updated_at: DateTime
}
```

#### teams
```javascript
{
    _id: ObjectId,
    name: String,
    description: String,
    workspace_id: ObjectId (ref: workspaces),
    member_ids: [ObjectId],
    settings: Object,
    created_at: DateTime,
    updated_at: DateTime
}
```

#### channels
```javascript
{
    _id: ObjectId,
    name: String,
    description: String,
    team_id: ObjectId (ref: teams),
    type: String (public|private),
    member_ids: [ObjectId],
    settings: Object,
    created_at: DateTime,
    updated_at: DateTime
}
```

#### messages
```javascript
{
    _id: ObjectId,
    content: String,
    sender_id: ObjectId (ref: users),
    channel_id: ObjectId (ref: channels),
    receiver_id: ObjectId (ref: users),
    type: String (channel|direct),
    attachment_id: ObjectId (ref: file_attachments),
    edited_at: DateTime,
    deleted_at: DateTime,
    created_at: DateTime,
    updated_at: DateTime
}
```

#### file_attachments
```javascript
{
    _id: ObjectId,
    filename: String,
    original_filename: String,
    mime_type: String,
    size: Integer,
    gridfs_id: String,
    uploader_id: ObjectId (ref: users),
    created_at: DateTime,
    updated_at: DateTime
}
```

## 🔒 Middleware

### Authentication Middleware
All protected routes require `auth:sanctum` middleware.

### Custom Middleware

1. **CheckWorkspaceAccess** - Verifies user has access to workspace
2. **CheckTeamAccess** - Verifies user is a team member
3. **CheckChannelAccess** - Verifies user can access channel (especially private channels)

## 💾 Caching

Caching is implemented using Laravel Cache facade:

- **Workspace Lists** - Cached for 1 hour (3600 seconds)
- **Channel Messages** - Cached for 10 minutes (600 seconds)

Cache keys:
- `user_workspaces_{user_id}`
- `channel_messages_{channel_id}_page_{page}_per_{per_page}`

Cache is automatically cleared when:
- Workspaces are created/updated/deleted
- Members are added/removed
- Messages are sent/updated/deleted

## 📧 Email Templates

Email templates are located in `resources/views/emails/`:

1. **workspace-invitation.blade.php** - Sent when user is added to workspace
2. **team-invitation.blade.php** - Sent when user is added to team

Emails are sent using Laravel Mail facade. Configure your mail driver in `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
```

## 🚦 Rate Limiting

Login endpoint is throttled to prevent brute force attacks:
- **5 attempts per minute** per IP address

## 📦 MongoDB GridFS

Files are stored in MongoDB GridFS instead of filesystem or cloud storage.

### Upload Process
1. File is uploaded via multipart/form-data
2. File is streamed to GridFS bucket
3. GridFS returns a unique file ID
4. File metadata is stored in `file_attachments` collection

### Download Process
1. Retrieve file metadata from `file_attachments`
2. Open download stream from GridFS using `gridfs_id`
3. Stream file content to response

### File Size Limit
Maximum file size: **10MB** (configurable in validation rules)

## 🧪 Testing with Postman

See `POSTMAN_API_DOCUMENTATION.md` for complete API documentation with request/response examples.

### Quick Start Testing Flow

1. **Register** a new user
2. **Login** and save the access token
3. **Create** a workspace
4. **Create** a team in the workspace
5. **Create** a channel in the team
6. **Send** messages to the channel
7. **Upload** files and attach to messages
8. **Test** direct messages between users

## 🔍 Query Examples

All queries are written directly in controllers for transparency:

### List workspaces for user
```php
Workspace::where('owner_id', $user->_id)
    ->orWhereIn('_id', function ($query) use ($user) {
        $query->select('_id')
            ->from('workspaces')
            ->where('member_ids', $user->_id);
    })
    ->orderBy('created_at', 'desc')
    ->get();
```

### List channel messages with pagination
```php
Message::where('channel_id', $channelId)
    ->where('type', 'channel')
    ->whereNull('deleted_at')
    ->orderBy('created_at', 'desc')
    ->paginate(50);
```

### List direct messages between two users
```php
Message::where('type', 'direct')
    ->where(function ($query) use ($currentUserId, $otherUserId) {
        $query->where(function ($q) use ($currentUserId, $otherUserId) {
            $q->where('sender_id', $currentUserId)
              ->where('receiver_id', $otherUserId);
        })->orWhere(function ($q) use ($currentUserId, $otherUserId) {
            $q->where('sender_id', $otherUserId)
              ->where('receiver_id', $currentUserId);
        });
    })
    ->whereNull('deleted_at')
    ->orderBy('created_at', 'desc')
    ->paginate(50);
```

## ⚠️ Important Notes

### What This Project DOES NOT Include

❌ No WebSockets or real-time messaging
❌ No frontend (API only)
❌ No Repository Pattern
❌ No complex architecture layers
❌ No multiple database connections

### What This Project INCLUDES

✅ Laravel default MVC structure
✅ MongoDB with single connection
✅ MongoDB GridFS for file storage
✅ Token-based authentication (Sanctum)
✅ Comprehensive middleware
✅ Caching implementation
✅ Email notifications
✅ Throttling on sensitive endpoints
✅ Clear, readable queries in controllers
✅ Proper validation
✅ RESTful API design

## 🐛 Troubleshooting

### MongoDB Connection Issues
```bash
# Check MongoDB is running
mongosh

# Verify connection in Laravel
php artisan tinker
>>> DB::connection('mongodb')->getMongoDB()->listCollections();
```

### GridFS Issues
```bash
# Test GridFS bucket
php artisan tinker
>>> $bucket = app('db')->connection('mongodb')->getMongoDB()->selectGridFSBucket();
>>> $bucket->find();
```

### Cache Issues
```bash
# Clear cache
php artisan cache:clear

# Clear config cache
php artisan config:clear
```

## 📝 License

This project is open-sourced software licensed under the MIT license.

## 👨‍💻 Author

Built with Laravel 12 and MongoDB for educational and assignment purposes.

