# Chat Application API - Postman Collection Guide

## Base URL
```
http://localhost:8000/api
```

## Authentication
All protected endpoints require Bearer Token authentication.
Add the token to the Authorization header:
```
Authorization: Bearer {your_token_here}
```

---

## 1. AUTHENTICATION MODULE

### 1.1 Register User
**POST** `/register`

**Body (JSON):**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Response (201):**
```json
{
    "message": "User registered successfully",
    "user": {
        "_id": "507f1f77bcf86cd799439011",
        "name": "John Doe",
        "email": "john@example.com",
        "status": "active"
    },
    "access_token": "1|abc123...",
    "token_type": "Bearer"
}
```

---

### 1.2 Login
**POST** `/login`

**Throttling:** 5 attempts per minute

**Body (JSON):**
```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

**Response (200):**
```json
{
    "message": "Login successful",
    "user": {
        "_id": "507f1f77bcf86cd799439011",
        "name": "John Doe",
        "email": "john@example.com"
    },
    "access_token": "2|xyz789...",
    "token_type": "Bearer"
}
```

---

### 1.3 Logout
**POST** `/logout`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "message": "Logged out successfully"
}
```

---

### 1.4 Get Profile
**GET** `/profile`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "user": {
        "_id": "507f1f77bcf86cd799439011",
        "name": "John Doe",
        "email": "john@example.com",
        "status": "active",
        "profile_picture": null
    }
}
```

---

### 1.5 Update Profile
**PUT** `/profile`

**Headers:**
```
Authorization: Bearer {token}
```

**Body (JSON):**
```json
{
    "name": "John Updated",
    "status": "away",
    "profile_picture": "https://example.com/avatar.jpg"
}
```

**Response (200):**
```json
{
    "message": "Profile updated successfully",
    "user": {
        "_id": "507f1f77bcf86cd799439011",
        "name": "John Updated",
        "status": "away"
    }
}
```

---

## 2. WORKSPACE MODULE

### 2.1 List Workspaces
**GET** `/workspaces`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "workspaces": [
        {
            "_id": "507f1f77bcf86cd799439012",
            "name": "My Workspace",
            "description": "Main workspace",
            "owner_id": "507f1f77bcf86cd799439011",
            "member_ids": [],
            "created_at": "2024-01-01T00:00:00.000000Z"
        }
    ]
}
```

---

### 2.2 Create Workspace
**POST** `/workspaces`

**Headers:**
```
Authorization: Bearer {token}
```

**Body (JSON):**
```json
{
    "name": "Development Team",
    "description": "Workspace for dev team"
}
```

**Response (201):**
```json
{
    "message": "Workspace created successfully",
    "workspace": {
        "_id": "507f1f77bcf86cd799439013",
        "name": "Development Team",
        "description": "Workspace for dev team",
        "owner_id": "507f1f77bcf86cd799439011",
        "member_ids": []
    }
}
```

---

### 2.3 Get Single Workspace
**GET** `/workspaces/{workspace_id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "workspace": {
        "_id": "507f1f77bcf86cd799439013",
        "name": "Development Team",
        "description": "Workspace for dev team",
        "owner_id": "507f1f77bcf86cd799439011"
    }
}
```

---

### 2.4 Update Workspace
**PUT** `/workspaces/{workspace_id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Body (JSON):**
```json
{
    "name": "Updated Workspace Name",
    "description": "Updated description"
}
```

**Response (200):**
```json
{
    "message": "Workspace updated successfully",
    "workspace": {
        "_id": "507f1f77bcf86cd799439013",
        "name": "Updated Workspace Name"
    }
}
```

---

### 2.5 Delete Workspace
**DELETE** `/workspaces/{workspace_id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "message": "Workspace deleted successfully"
}
```

---

### 2.6 Add Member to Workspace
**POST** `/workspaces/{workspace_id}/members`

**Headers:**
```
Authorization: Bearer {token}
```

**Body (JSON):**
```json
{
    "user_id": "507f1f77bcf86cd799439014"
}
```

**Response (200):**
```json
{
    "message": "Member added successfully",
    "workspace": {
        "_id": "507f1f77bcf86cd799439013",
        "member_ids": ["507f1f77bcf86cd799439014"]
    }
}
```

---

### 2.7 Remove Member from Workspace
**DELETE** `/workspaces/{workspace_id}/members`

**Headers:**
```
Authorization: Bearer {token}
```

**Body (JSON):**
```json
{
    "user_id": "507f1f77bcf86cd799439014"
}
```

**Response (200):**
```json
{
    "message": "Member removed successfully"
}
```

---

## 3. TEAM MODULE

### 3.1 List Teams in Workspace
**GET** `/workspaces/{workspace_id}/teams`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "teams": [
        {
            "_id": "507f1f77bcf86cd799439015",
            "name": "Backend Team",
            "description": "Backend developers",
            "workspace_id": "507f1f77bcf86cd799439013",
            "member_ids": ["507f1f77bcf86cd799439011"]
        }
    ]
}
```

---

### 3.2 Create Team
**POST** `/workspaces/{workspace_id}/teams`

**Headers:**
```
Authorization: Bearer {token}
```

**Body (JSON):**
```json
{
    "name": "Frontend Team",
    "description": "Frontend developers"
}
```

**Response (201):**
```json
{
    "message": "Team created successfully",
    "team": {
        "_id": "507f1f77bcf86cd799439016",
        "name": "Frontend Team",
        "workspace_id": "507f1f77bcf86cd799439013"
    }
}
```

---

### 3.3 Get Single Team
**GET** `/workspaces/{workspace_id}/teams/{team_id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "team": {
        "_id": "507f1f77bcf86cd799439016",
        "name": "Frontend Team",
        "description": "Frontend developers"
    }
}
```

---

### 3.4 Update Team
**PUT** `/workspaces/{workspace_id}/teams/{team_id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Body (JSON):**
```json
{
    "name": "Updated Team Name",
    "description": "Updated description"
}
```

**Response (200):**
```json
{
    "message": "Team updated successfully",
    "team": {
        "_id": "507f1f77bcf86cd799439016",
        "name": "Updated Team Name"
    }
}
```

---

### 3.5 Delete Team
**DELETE** `/workspaces/{workspace_id}/teams/{team_id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "message": "Team deleted successfully"
}
```

---

### 3.6 Add Member to Team
**POST** `/workspaces/{workspace_id}/teams/{team_id}/members`

**Headers:**
```
Authorization: Bearer {token}
```

**Body (JSON):**
```json
{
    "user_id": "507f1f77bcf86cd799439014"
}
```

**Response (200):**
```json
{
    "message": "Member added successfully",
    "team": {
        "_id": "507f1f77bcf86cd799439016",
        "member_ids": ["507f1f77bcf86cd799439011", "507f1f77bcf86cd799439014"]
    }
}
```

---

### 3.7 Remove Member from Team
**DELETE** `/workspaces/{workspace_id}/teams/{team_id}/members`

**Headers:**
```
Authorization: Bearer {token}
```

**Body (JSON):**
```json
{
    "user_id": "507f1f77bcf86cd799439014"
}
```

**Response (200):**
```json
{
    "message": "Member removed successfully"
}
```

---

## 4. CHANNEL MODULE

### 4.1 List Channels in Team
**GET** `/workspaces/{workspace_id}/teams/{team_id}/channels`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "channels": [
        {
            "_id": "507f1f77bcf86cd799439017",
            "name": "general",
            "description": "General discussion",
            "team_id": "507f1f77bcf86cd799439016",
            "type": "public",
            "member_ids": []
        }
    ]
}
```

---

### 4.2 Create Channel
**POST** `/workspaces/{workspace_id}/teams/{team_id}/channels`

**Headers:**
```
Authorization: Bearer {token}
```

**Body (JSON):**
```json
{
    "name": "random",
    "description": "Random discussions",
    "type": "public"
}
```

**Type options:** `public` or `private`

**Response (201):**
```json
{
    "message": "Channel created successfully",
    "channel": {
        "_id": "507f1f77bcf86cd799439018",
        "name": "random",
        "type": "public",
        "team_id": "507f1f77bcf86cd799439016"
    }
}
```

---

### 4.3 Get Single Channel
**GET** `/workspaces/{workspace_id}/teams/{team_id}/channels/{channel_id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "channel": {
        "_id": "507f1f77bcf86cd799439018",
        "name": "random",
        "description": "Random discussions",
        "type": "public"
    }
}
```

---

### 4.4 Update Channel
**PUT** `/workspaces/{workspace_id}/teams/{team_id}/channels/{channel_id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Body (JSON):**
```json
{
    "name": "updated-channel",
    "description": "Updated description"
}
```

**Response (200):**
```json
{
    "message": "Channel updated successfully",
    "channel": {
        "_id": "507f1f77bcf86cd799439018",
        "name": "updated-channel"
    }
}
```

---

### 4.5 Delete Channel
**DELETE** `/workspaces/{workspace_id}/teams/{team_id}/channels/{channel_id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "message": "Channel deleted successfully"
}
```

---

### 4.6 Add Member to Private Channel
**POST** `/workspaces/{workspace_id}/teams/{team_id}/channels/{channel_id}/members`

**Headers:**
```
Authorization: Bearer {token}
```

**Body (JSON):**
```json
{
    "user_id": "507f1f77bcf86cd799439014"
}
```

**Response (200):**
```json
{
    "message": "Member added successfully",
    "channel": {
        "_id": "507f1f77bcf86cd799439018",
        "member_ids": ["507f1f77bcf86cd799439011", "507f1f77bcf86cd799439014"]
    }
}
```

---

### 4.7 Remove Member from Private Channel
**DELETE** `/workspaces/{workspace_id}/teams/{team_id}/channels/{channel_id}/members`

**Headers:**
```
Authorization: Bearer {token}
```

**Body (JSON):**
```json
{
    "user_id": "507f1f77bcf86cd799439014"
}
```

**Response (200):**
```json
{
    "message": "Member removed successfully"
}
```

---

## 5. MESSAGE MODULE

### 5.1 List Channel Messages
**GET** `/workspaces/{workspace_id}/teams/{team_id}/channels/{channel_id}/messages`

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `page` (optional): Page number (default: 1)
- `per_page` (optional): Items per page (default: 50)

**Response (200):**
```json
{
    "messages": {
        "current_page": 1,
        "data": [
            {
                "_id": "507f1f77bcf86cd799439019",
                "content": "Hello team!",
                "sender_id": "507f1f77bcf86cd799439011",
                "channel_id": "507f1f77bcf86cd799439018",
                "type": "channel",
                "attachment_id": null,
                "created_at": "2024-01-01T10:00:00.000000Z"
            }
        ],
        "per_page": 50,
        "total": 1
    }
}
```

---

### 5.2 Send Channel Message
**POST** `/workspaces/{workspace_id}/teams/{team_id}/channels/{channel_id}/messages`

**Headers:**
```
Authorization: Bearer {token}
```

**Body (JSON):**
```json
{
    "content": "Hello everyone!",
    "attachment_id": null
}
```

**Response (201):**
```json
{
    "message": "Message sent successfully",
    "data": {
        "_id": "507f1f77bcf86cd79943901a",
        "content": "Hello everyone!",
        "sender_id": "507f1f77bcf86cd799439011",
        "channel_id": "507f1f77bcf86cd799439018",
        "type": "channel"
    }
}
```

---

### 5.3 List Direct Messages
**GET** `/messages/direct`

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `user_id` (required): The other user's ID
- `page` (optional): Page number
- `per_page` (optional): Items per page

**Example:**
```
GET /messages/direct?user_id=507f1f77bcf86cd799439014&page=1&per_page=50
```

**Response (200):**
```json
{
    "messages": {
        "current_page": 1,
        "data": [
            {
                "_id": "507f1f77bcf86cd79943901b",
                "content": "Hi there!",
                "sender_id": "507f1f77bcf86cd799439011",
                "receiver_id": "507f1f77bcf86cd799439014",
                "type": "direct",
                "created_at": "2024-01-01T11:00:00.000000Z"
            }
        ]
    }
}
```

---

### 5.4 Send Direct Message
**POST** `/messages/direct`

**Headers:**
```
Authorization: Bearer {token}
```

**Body (JSON):**
```json
{
    "content": "Hey, how are you?",
    "receiver_id": "507f1f77bcf86cd799439014",
    "attachment_id": null
}
```

**Response (201):**
```json
{
    "message": "Direct message sent successfully",
    "data": {
        "_id": "507f1f77bcf86cd79943901c",
        "content": "Hey, how are you?",
        "sender_id": "507f1f77bcf86cd799439011",
        "receiver_id": "507f1f77bcf86cd799439014",
        "type": "direct"
    }
}
```

---

### 5.5 Update Message
**PUT** `/messages/{message_id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Body (JSON):**
```json
{
    "content": "Updated message content"
}
```

**Response (200):**
```json
{
    "message": "Message updated successfully",
    "data": {
        "_id": "507f1f77bcf86cd79943901c",
        "content": "Updated message content",
        "edited_at": "2024-01-01T12:00:00.000000Z"
    }
}
```

---

### 5.6 Delete Message
**DELETE** `/messages/{message_id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "message": "Message deleted successfully"
}
```

---

## 6. FILE ATTACHMENT MODULE (GridFS)

### 6.1 Upload File
**POST** `/files/upload`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Body (Form Data):**
- `file`: File to upload (max 10MB)

**Response (201):**
```json
{
    "message": "File uploaded successfully",
    "file": {
        "_id": "507f1f77bcf86cd79943901d",
        "filename": "document.pdf",
        "original_filename": "document.pdf",
        "mime_type": "application/pdf",
        "size": 1024000,
        "gridfs_id": "507f1f77bcf86cd79943901e",
        "uploader_id": "507f1f77bcf86cd799439011"
    }
}
```

---

### 6.2 List My Files
**GET** `/files`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "files": {
        "current_page": 1,
        "data": [
            {
                "_id": "507f1f77bcf86cd79943901d",
                "filename": "document.pdf",
                "size": 1024000,
                "created_at": "2024-01-01T13:00:00.000000Z"
            }
        ]
    }
}
```

---

### 6.3 Download File
**GET** `/files/{file_id}/download`

**Headers:**
```
Authorization: Bearer {token}
```

**Response:** File download with appropriate headers

---

### 6.4 Delete File
**DELETE** `/files/{file_id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "message": "File deleted successfully"
}
```

---

## Testing Flow in Postman

### Step 1: Setup Environment Variables
Create a Postman environment with:
- `base_url`: `http://localhost:8000/api`
- `token`: (will be set after login)

### Step 2: Register and Login
1. Register a new user
2. Copy the `access_token` from response
3. Set it as `token` environment variable

### Step 3: Create Workspace
1. Create a workspace
2. Copy the workspace `_id`

### Step 4: Create Team
1. Create a team in the workspace
2. Copy the team `_id`

### Step 5: Create Channel
1. Create a channel in the team
2. Copy the channel `_id`

### Step 6: Send Messages
1. Send messages to the channel
2. Test direct messages

### Step 7: Upload Files
1. Upload a file
2. Copy the file `_id`
3. Use it as `attachment_id` in messages

---

## Error Responses

### 400 Bad Request
```json
{
    "error": "Workspace ID is required"
}
```

### 401 Unauthorized
```json
{
    "message": "Unauthenticated."
}
```

### 403 Forbidden
```json
{
    "error": "Access denied to this workspace"
}
```

### 404 Not Found
```json
{
    "error": "Workspace not found"
}
```

### 422 Validation Error
```json
{
    "errors": {
        "email": ["The email field is required."],
        "password": ["The password must be at least 8 characters."]
    }
}
```

### 500 Server Error
```json
{
    "error": "Failed to upload file to GridFS",
    "details": "Error message"
}
```

---

## Notes

1. All timestamps are in UTC format
2. MongoDB ObjectIDs are used as primary keys (`_id`)
3. Pagination is available on list endpoints
4. Caching is implemented for workspace lists and channel messages (10 minutes TTL)
5. Login endpoint has throttling: 5 attempts per minute
6. File uploads are limited to 10MB
7. Files are stored in MongoDB GridFS, not filesystem
8. Email notifications are sent for workspace and team invitations

