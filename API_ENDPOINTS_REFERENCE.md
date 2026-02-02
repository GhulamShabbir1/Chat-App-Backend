# Complete API Endpoints Reference

Base URL: `http://localhost:8000/api`

---

## 1. AUTHENTICATION

### 1.1 Register User
**POST** `/register`

**Headers:**
```
Content-Type: application/json
```

**Request Body:**
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
        "name": "John Doe",
        "email": "john@example.com",
        "status": "active",
        "updated_at": "2026-02-02T10:00:00.000000Z",
        "created_at": "2026-02-02T10:00:00.000000Z",
        "id": "69806e04c095166fbd0925d2"
    },
    "access_token": "6980724ad5fbd0bbc3081622|9KvUSOcIXOTTRBrd8rpXyr5EUSZXH2YMPh3H3GGS",
    "token_type": "Bearer"
}
```

---

### 1.2 Login
**POST** `/login`

**Headers:**
```
Content-Type: application/json
```

**Request Body:**
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
        "name": "John Doe",
        "email": "john@example.com",
        "status": "active",
        "updated_at": "2026-02-02T10:00:00.000000Z",
        "created_at": "2026-02-02T10:00:00.000000Z",
        "id": "69806e04c095166fbd0925d2"
    },
    "access_token": "6980724ad5fbd0bbc3081622|9KvUSOcIXOTTRBrd8rpXyr5EUSZXH2YMPh3H3GGS",
    "token_type": "Bearer"
}
```

---

### 1.3 Get Profile
**GET** `/profile`

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Response (200):**
```json
{
    "user": {
        "name": "John Doe",
        "email": "john@example.com",
        "status": "active",
        "profile_picture": null,
        "updated_at": "2026-02-02T10:00:00.000000Z",
        "created_at": "2026-02-02T10:00:00.000000Z",
        "id": "69806e04c095166fbd0925d2"
    }
}
```

---

### 1.4 Update Profile
**PUT** `/profile`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
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
        "name": "John Updated",
        "email": "john@example.com",
        "status": "away",
        "profile_picture": "https://example.com/avatar.jpg",
        "id": "69806e04c095166fbd0925d2"
    }
}
```

---

### 1.5 Logout
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

## 2. WORKSPACES

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
            "id": "69807a1cab1a7d6e090c2072",
            "name": "My Workspace",
            "description": "Main workspace",
            "owner_id": "69806e04c095166fbd0925d2",
            "member_ids": [],
            "settings": {},
            "created_at": "2026-02-02T10:15:00.000000Z",
            "updated_at": "2026-02-02T10:15:00.000000Z"
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
Content-Type: application/json
```

**Request Body:**
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
        "name": "Development Team",
        "description": "Workspace for dev team",
        "owner_id": "69806e04c095166fbd0925d2",
        "member_ids": [],
        "settings": {},
        "updated_at": "2026-02-02T10:20:00.000000Z",
        "created_at": "2026-02-02T10:20:00.000000Z",
        "id": "69807a1cab1a7d6e090c2072"
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
        "id": "69807a1cab1a7d6e090c2072",
        "name": "Development Team",
        "description": "Workspace for dev team",
        "owner_id": "69806e04c095166fbd0925d2",
        "member_ids": [],
        "settings": {}
    }
}
```

---

### 2.4 Update Workspace
**PUT** `/workspaces/{workspace_id}`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
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
        "id": "69807a1cab1a7d6e090c2072",
        "name": "Updated Workspace Name",
        "description": "Updated description"
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
Content-Type: application/json
```

**Request Body:**
```json
{
    "user_id": "69806xxx..."
}
```

**Response (200):**
```json
{
    "message": "Member added successfully",
    "workspace": {
        "id": "69807a1cab1a7d6e090c2072",
        "member_ids": ["69806xxx..."]
    }
}
```

---

### 2.7 Remove Member from Workspace
**DELETE** `/workspaces/{workspace_id}/members`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
    "user_id": "69806xxx..."
}
```

**Response (200):**
```json
{
    "message": "Member removed successfully"
}
```

---

## 3. TEAMS

### 3.1 List Teams
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
            "id": "69807b2dab1a7d6e090c2074",
            "name": "Backend Team",
            "description": "Backend developers",
            "workspace_id": "69807a1cab1a7d6e090c2072",
            "member_ids": ["69806e04c095166fbd0925d2"],
            "settings": {},
            "created_at": "2026-02-02T10:22:00.000000Z"
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
Content-Type: application/json
```

**Request Body:**
```json
{
    "name": "Backend Team",
    "description": "Backend developers"
}
```

**Response (201):**
```json
{
    "message": "Team created successfully",
    "team": {
        "name": "Backend Team",
        "description": "Backend developers",
        "workspace_id": "69807a1cab1a7d6e090c2072",
        "member_ids": ["69806e04c095166fbd0925d2"],
        "settings": {},
        "updated_at": "2026-02-02T10:22:00.000000Z",
        "created_at": "2026-02-02T10:22:00.000000Z",
        "id": "69807b2dab1a7d6e090c2074"
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
        "id": "69807b2dab1a7d6e090c2074",
        "name": "Backend Team",
        "description": "Backend developers",
        "workspace_id": "69807a1cab1a7d6e090c2072"
    }
}
```

---

### 3.4 Update Team
**PUT** `/workspaces/{workspace_id}/teams/{team_id}`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
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
        "id": "69807b2dab1a7d6e090c2074",
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
Content-Type: application/json
```

**Request Body:**
```json
{
    "user_id": "69806xxx..."
}
```

**Response (200):**
```json
{
    "message": "Member added successfully",
    "team": {
        "id": "69807b2dab1a7d6e090c2074",
        "member_ids": ["69806e04c095166fbd0925d2", "69806xxx..."]
    }
}
```

---

### 3.7 Remove Member from Team
**DELETE** `/workspaces/{workspace_id}/teams/{team_id}/members`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
    "user_id": "69806xxx..."
}
```

**Response (200):**
```json
{
    "message": "Member removed successfully"
}
```

---

## 4. CHANNELS

### 4.1 List Channels
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
            "id": "69807c31ab1a7d6e090c2076",
            "name": "general",
            "description": "General discussion",
            "team_id": "69807b2dab1a7d6e090c2074",
            "type": "public",
            "member_ids": [],
            "settings": {},
            "created_at": "2026-02-02T10:25:00.000000Z"
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
Content-Type: application/json
```

**Request Body:**
```json
{
    "name": "general",
    "description": "General discussion",
    "type": "public"
}
```

**Type:** `public` or `private`

**Response (201):**
```json
{
    "message": "Channel created successfully",
    "channel": {
        "name": "general",
        "description": "General discussion",
        "team_id": "69807b2dab1a7d6e090c2074",
        "type": "public",
        "member_ids": [],
        "settings": {},
        "updated_at": "2026-02-02T10:25:00.000000Z",
        "created_at": "2026-02-02T10:25:00.000000Z",
        "id": "69807c31ab1a7d6e090c2076"
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
        "id": "69807c31ab1a7d6e090c2076",
        "name": "general",
        "description": "General discussion",
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
Content-Type: application/json
```

**Request Body:**
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
        "id": "69807c31ab1a7d6e090c2076",
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
Content-Type: application/json
```

**Request Body:**
```json
{
    "user_id": "69806xxx..."
}
```

**Response (200):**
```json
{
    "message": "Member added successfully",
    "channel": {
        "id": "69807c31ab1a7d6e090c2076",
        "member_ids": ["69806xxx..."]
    }
}
```

---

### 4.7 Remove Member from Private Channel
**DELETE** `/workspaces/{workspace_id}/teams/{team_id}/channels/{channel_id}/members`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
    "user_id": "69806xxx..."
}
```

**Response (200):**
```json
{
    "message": "Member removed successfully"
}
```

---

## 5. MESSAGES

### 5.1 List Channel Messages
**GET** `/workspaces/{workspace_id}/teams/{team_id}/channels/{channel_id}/messages`

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `page` (optional): Page number (default: 1)
- `per_page` (optional): Items per page (default: 50)

**Example:** `/workspaces/{workspace_id}/teams/{team_id}/channels/{channel_id}/messages?page=1&per_page=50`

**Response (200):**
```json
{
    "messages": {
        "current_page": 1,
        "data": [
            {
                "id": "69807c56ab1a7d6e090c2077",
                "content": "Hello everyone!",
                "sender_id": "69806e04c095166fbd0925d2",
                "channel_id": "69807c31ab1a7d6e090c2076",
                "type": "channel",
                "attachment_id": null,
                "edited_at": null,
                "deleted_at": null,
                "created_at": "2026-02-02T10:28:38.158000Z",
                "updated_at": "2026-02-02T10:28:38.158000Z"
            }
        ],
        "per_page": 50,
        "total": 1,
        "last_page": 1
    }
}
```

---

### 5.2 Send Channel Message
**POST** `/workspaces/{workspace_id}/teams/{team_id}/channels/{channel_id}/messages`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
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
        "content": "Hello everyone!",
        "sender_id": "69806e04c095166fbd0925d2",
        "channel_id": "69807c31ab1a7d6e090c2076",
        "type": "channel",
        "attachment_id": null,
        "updated_at": "2026-02-02T10:28:38.158000Z",
        "created_at": "2026-02-02T10:28:38.158000Z",
        "id": "69807c56ab1a7d6e090c2077"
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

**Example:** `/messages/direct?user_id=69806xxx...&page=1&per_page=50`

**Response (200):**
```json
{
    "messages": {
        "current_page": 1,
        "data": [
            {
                "id": "69807xxx...",
                "content": "Hey there!",
                "sender_id": "69806e04c095166fbd0925d2",
                "receiver_id": "69806xxx...",
                "type": "direct",
                "attachment_id": null,
                "created_at": "2026-02-02T10:30:00.000000Z"
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
Content-Type: application/json
```

**Request Body:**
```json
{
    "content": "Hey, how are you?",
    "receiver_id": "69806xxx...",
    "attachment_id": null
}
```

**Response (201):**
```json
{
    "message": "Direct message sent successfully",
    "data": {
        "content": "Hey, how are you?",
        "sender_id": "69806e04c095166fbd0925d2",
        "receiver_id": "69806xxx...",
        "type": "direct",
        "attachment_id": null,
        "updated_at": "2026-02-02T10:30:00.000000Z",
        "created_at": "2026-02-02T10:30:00.000000Z",
        "id": "69807xxx..."
    }
}
```

---

### 5.5 Update Message
**PUT** `/messages/{message_id}`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
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
        "id": "69807c56ab1a7d6e090c2077",
        "content": "Updated message content",
        "edited_at": "2026-02-02T10:35:00.000000Z"
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

## 6. FILE ATTACHMENTS (GridFS)

### 6.1 Upload File
**POST** `/files/upload`

**Headers:**
```
Authorization: Bearer {token}
```

**Body Type:** `form-data` (NOT JSON!)

**Form Data:**
- Key: `file` (type: File)
- Value: Select a file

**Response (201):**
```json
{
    "message": "File uploaded successfully",
    "file": {
        "filename": "document.pdf",
        "original_filename": "document.pdf",
        "mime_type": "application/pdf",
        "size": 1024000,
        "gridfs_id": "69807xxx...",
        "uploader_id": "69806e04c095166fbd0925d2",
        "updated_at": "2026-02-02T10:40:00.000000Z",
        "created_at": "2026-02-02T10:40:00.000000Z",
        "id": "69807xxx..."
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
                "id": "69807xxx...",
                "filename": "document.pdf",
                "original_filename": "document.pdf",
                "mime_type": "application/pdf",
                "size": 1024000,
                "created_at": "2026-02-02T10:40:00.000000Z"
            }
        ],
        "per_page": 20,
        "total": 1
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

**Response:** File download (binary data)

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

## ERROR RESPONSES

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

## NOTES

1. All endpoints except `/register` and `/login` require authentication
2. Use the complete token format: `{id}|{token}` in Authorization header
3. MongoDB ObjectIDs are used as primary keys (`id` or `_id`)
4. Pagination is available on list endpoints
5. File uploads use `form-data`, not JSON
6. Maximum file size: 10MB
7. All timestamps are in UTC format

---

## TESTING ORDER

1. Register → Login → Get Profile
2. Create Workspace → Create Team → Create Channel
3. Send Message → List Messages
4. Upload File → Send Message with Attachment
5. Update/Delete Messages
6. Test Direct Messages
7. Test Member Management

---

**Total Endpoints: 40+**

All endpoints are fully functional and tested! 🚀
