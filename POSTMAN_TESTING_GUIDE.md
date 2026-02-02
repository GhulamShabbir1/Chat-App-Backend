# Chat Application Backend - Postman Testing Guide

## Setup Before Testing

1. **MongoDB Running**: Ensure MongoDB is running on `127.0.0.1:27017`
2. **Laravel Server**: Run `php artisan serve` (runs on `http://localhost:8000`)
3. **Postman Collection**: Import `Chat_App_API.postman_collection.json`
4. **Base URL Variable**: Set in Postman: `http://localhost:8000/api`
5. **Token Variable**: Will be auto-populated after login

## Test Sequence

### 1. Authentication Tests

#### Register User
```
POST {{base_url}}/register
Body (JSON):
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```
**Expected**: 201 Created, returns user and access_token
**Token Variable**: Copy `access_token` to `{{token}}` variable

#### Register Second User
```
POST {{base_url}}/register
Body (JSON):
{
  "name": "Jane Smith",
  "email": "jane@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```
**Expected**: 201 Created

#### Login
```
POST {{base_url}}/login
Body (JSON):
{
  "email": "john@example.com",
  "password": "password123"
}
```
**Expected**: 200 OK, returns access_token

#### Test Login Throttling (5 attempts per minute)
```
POST {{base_url}}/login (run 6 times quickly)
Body (JSON):
{
  "email": "john@example.com",
  "password": "wrongpassword"
}
```
**Expected**: 5th request succeeds, 6th request returns 429 Too Many Requests

#### Get Profile
```
GET {{base_url}}/profile
Headers: Authorization: Bearer {{token}}
```
**Expected**: 200 OK, returns user object

#### Update Profile
```
PUT {{base_url}}/profile
Headers: Authorization: Bearer {{token}}
Body (JSON):
{
  "name": "John Updated",
  "status": "busy"
}
```
**Expected**: 200 OK, user updated

#### Update Profile Password
```
PUT {{base_url}}/profile
Headers: Authorization: Bearer {{token}}
Body (JSON):
{
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}
```
**Expected**: 200 OK, password hashed and updated

#### Logout
```
POST {{base_url}}/logout
Headers: Authorization: Bearer {{token}}
```
**Expected**: 200 OK, token deleted

---

### 2. Workspace Tests

#### Create Workspace
```
POST {{base_url}}/workspaces
Headers: Authorization: Bearer {{token}}
Body (JSON):
{
  "name": "Development Workspace",
  "description": "Main development workspace"
}
```
**Expected**: 201 Created
**Save**: workspace._id for next tests

#### List Workspaces (Cached)
```
GET {{base_url}}/workspaces
Headers: Authorization: Bearer {{token}}
```
**Expected**: 200 OK, array of workspaces (first request cached)
**Note**: Run twice to see cache in action

#### Get Single Workspace
```
GET {{base_url}}/workspaces/{workspace_id}
Headers: Authorization: Bearer {{token}}
```
**Expected**: 200 OK

#### Update Workspace (Owner Only)
```
PUT {{base_url}}/workspaces/{workspace_id}
Headers: Authorization: Bearer {{token}}
Body (JSON):
{
  "name": "Updated Workspace",
  "description": "Updated description"
}
```
**Expected**: 200 OK

#### Add Member to Workspace
```
POST {{base_url}}/workspaces/{workspace_id}/members
Headers: Authorization: Bearer {{token}}
Body (JSON):
{
  "user_id": "jane_user_id"
}
```
**Expected**: 200 OK

#### Delete Workspace (Owner Only)
```
DELETE {{base_url}}/workspaces/{workspace_id}
Headers: Authorization: Bearer {{token}}
```
**Expected**: 200 OK

---

### 3. Team Tests

#### Create Team
```
POST {{base_url}}/workspaces/{workspace_id}/teams
Headers: Authorization: Bearer {{token}}
Body (JSON):
{
  "name": "Backend Team",
  "description": "Backend developers"
}
```
**Expected**: 201 Created
**Save**: team._id for next tests

#### List Teams
```
GET {{base_url}}/workspaces/{workspace_id}/teams
Headers: Authorization: Bearer {{token}}
```
**Expected**: 200 OK

#### Get Single Team
```
GET {{base_url}}/workspaces/{workspace_id}/teams/{team_id}
Headers: Authorization: Bearer {{token}}
```
**Expected**: 200 OK

#### Update Team (Owner Only)
```
PUT {{base_url}}/workspaces/{workspace_id}/teams/{team_id}
Headers: Authorization: Bearer {{token}}
Body (JSON):
{
  "name": "Updated Backend Team"
}
```
**Expected**: 200 OK

#### Add Member to Team (Sends Email)
```
POST {{base_url}}/workspaces/{workspace_id}/teams/{team_id}/members
Headers: Authorization: Bearer {{token}}
Body (JSON):
{
  "user_id": "jane_user_id"
}
```
**Expected**: 200 OK (check mail log for invitation)

#### Delete Team (Owner Only)
```
DELETE {{base_url}}/workspaces/{workspace_id}/teams/{team_id}
Headers: Authorization: Bearer {{token}}
```
**Expected**: 200 OK

---

### 4. Channel Tests

#### Create Public Channel
```
POST {{base_url}}/workspaces/{workspace_id}/teams/{team_id}/channels
Headers: Authorization: Bearer {{token}}
Body (JSON):
{
  "name": "general",
  "description": "General discussion",
  "type": "public"
}
```
**Expected**: 201 Created
**Save**: channel._id for next tests

#### Create Private Channel
```
POST {{base_url}}/workspaces/{workspace_id}/teams/{team_id}/channels
Headers: Authorization: Bearer {{token}}
Body (JSON):
{
  "name": "private-discussion",
  "description": "Private discussion",
  "type": "private"
}
```
**Expected**: 201 Created

#### List Channels
```
GET {{base_url}}/workspaces/{workspace_id}/teams/{team_id}/channels
Headers: Authorization: Bearer {{token}}
```
**Expected**: 200 OK

#### Get Single Channel
```
GET {{base_url}}/workspaces/{workspace_id}/teams/{team_id}/channels/{channel_id}
Headers: Authorization: Bearer {{token}}
```
**Expected**: 200 OK

#### Update Channel (Owner Only)
```
PUT {{base_url}}/workspaces/{workspace_id}/teams/{team_id}/channels/{channel_id}
Headers: Authorization: Bearer {{token}}
Body (JSON):
{
  "name": "updated-general",
  "type": "public"
}
```
**Expected**: 200 OK

#### Add Member to Private Channel
```
POST {{base_url}}/workspaces/{workspace_id}/teams/{team_id}/channels/{channel_id}/members
Headers: Authorization: Bearer {{token}}
Body (JSON):
{
  "user_id": "jane_user_id"
}
```
**Expected**: 200 OK

#### Delete Channel (Owner Only)
```
DELETE {{base_url}}/workspaces/{workspace_id}/teams/{team_id}/channels/{channel_id}
Headers: Authorization: Bearer {{token}}
```
**Expected**: 200 OK

---

### 5. Message Tests

#### Send Channel Message
```
POST {{base_url}}/workspaces/{workspace_id}/teams/{team_id}/channels/{channel_id}/messages
Headers: Authorization: Bearer {{token}}
Body (JSON):
{
  "content": "Hello everyone!"
}
```
**Expected**: 201 Created
**Save**: message._id for next tests

#### List Channel Messages (Cached)
```
GET {{base_url}}/workspaces/{workspace_id}/teams/{team_id}/channels/{channel_id}/messages
Headers: Authorization: Bearer {{token}}
Query Params:
  page: 1
  per_page: 50
```
**Expected**: 200 OK, paginated messages
**Note**: Run twice to see cache in action

#### Send Direct Message
```
POST {{base_url}}/messages/direct
Headers: Authorization: Bearer {{token}}
Body (JSON):
{
  "content": "Hey, how are you?",
  "receiver_id": "jane_user_id"
}
```
**Expected**: 201 Created
**Save**: message._id for next tests

#### List Direct Messages
```
GET {{base_url}}/messages/direct
Headers: Authorization: Bearer {{token}}
Query Params:
  user_id: jane_user_id
  page: 1
  per_page: 50
```
**Expected**: 200 OK, paginated direct messages

#### Update Message (Sender Only)
```
PUT {{base_url}}/messages/{message_id}
Headers: Authorization: Bearer {{token}}
Body (JSON):
{
  "content": "Updated message content"
}
```
**Expected**: 200 OK

#### Delete Message
```
DELETE {{base_url}}/messages/{message_id}
Headers: Authorization: Bearer {{token}}
```
**Expected**: 200 OK

---

### 6. File Attachment Tests

#### Upload File
```
POST {{base_url}}/files/upload
Headers: Authorization: Bearer {{token}}
Body (form-data):
  Key: file (type: file)
  Value: (select any file, max 10MB)
```
**Expected**: 201 Created
**Save**: file._id and gridfs_id for next tests

#### List User Files
```
GET {{base_url}}/files
Headers: Authorization: Bearer {{token}}
Query Params:
  page: 1
```
**Expected**: 200 OK, paginated files

#### Download File
```
GET {{base_url}}/files/{file_id}/download
Headers: Authorization: Bearer {{token}}
```
**Expected**: 200 OK, file binary content

#### Delete File (Uploader Only)
```
DELETE {{base_url}}/files/{file_id}
Headers: Authorization: Bearer {{token}}
```
**Expected**: 200 OK

#### Send Message with File Attachment
```
POST {{base_url}}/workspaces/{workspace_id}/teams/{team_id}/channels/{channel_id}/messages
Headers: Authorization: Bearer {{token}}
Body (JSON):
{
  "content": "Check out this file",
  "attachment_id": "{file_id}"
}
```
**Expected**: 201 Created

---

### 7. Authorization & Permission Tests

#### Test Non-Owner Cannot Update Workspace
```
POST {{base_url}}/register (as jane@example.com)
POST {{base_url}}/login (get jane's token)
PUT {{base_url}}/workspaces/{john_workspace_id}
Headers: Authorization: Bearer {{jane_token}}
```
**Expected**: 403 Forbidden

#### Test Non-Owner Cannot Delete Team
```
Same as above but for team delete
DELETE {{base_url}}/workspaces/{workspace_id}/teams/{john_team_id}
Headers: Authorization: Bearer {{jane_token}}
```
**Expected**: 403 Forbidden

#### Test Non-Owner Cannot Update Channel
```
PUT {{base_url}}/workspaces/{workspace_id}/teams/{team_id}/channels/{john_channel_id}
Headers: Authorization: Bearer {{jane_token}}
```
**Expected**: 403 Forbidden

#### Test Cannot Delete Others' Files
```
DELETE {{base_url}}/files/{john_file_id}
Headers: Authorization: Bearer {{jane_token}}
```
**Expected**: 403 Forbidden

#### Test Cannot Edit Others' Messages
```
PUT {{base_url}}/messages/{john_message_id}
Headers: Authorization: Bearer {{jane_token}}
Body: {"content": "hacked"}
```
**Expected**: 403 Forbidden

---

### 8. Validation Tests

#### Invalid Registration (Missing Confirmation)
```
POST {{base_url}}/register
Body (JSON):
{
  "name": "Test User",
  "email": "test@example.com",
  "password": "password123",
  "password_confirmation": "different123"
}
```
**Expected**: 422 Unprocessable Entity

#### Invalid Channel Type
```
POST {{base_url}}/workspaces/{workspace_id}/teams/{team_id}/channels
Headers: Authorization: Bearer {{token}}
Body (JSON):
{
  "name": "test",
  "type": "invalid"
}
```
**Expected**: 422 Unprocessable Entity

#### File Size Exceeds Limit
```
POST {{base_url}}/files/upload
Headers: Authorization: Bearer {{token}}
Body (form-data):
  file: (file > 10MB)
```
**Expected**: 422 Unprocessable Entity

#### Message Content Required
```
POST {{base_url}}/workspaces/{workspace_id}/teams/{team_id}/channels/{channel_id}/messages
Headers: Authorization: Bearer {{token}}
Body (JSON):
{
  "content": ""
}
```
**Expected**: 422 Unprocessable Entity

---

### 9. Middleware & Access Tests

#### Test Channel Access Denied for Non-Member
```
Create private channel as John
Add Jane to workspace/team but NOT to channel
GET {{base_url}}/workspaces/{workspace_id}/teams/{team_id}/channels/{private_channel_id}
Headers: Authorization: Bearer {{jane_token}}
```
**Expected**: 403 Forbidden

#### Test Workspace Access Denied
```
Create workspace as John
GET {{base_url}}/workspaces/{john_workspace_id}
Headers: Authorization: Bearer {{third_party_token}}
```
**Expected**: 403 Forbidden

---

### 10. Response Status Code Tests

| Operation | Status | Notes |
|-----------|--------|-------|
| Successful Create | 201 | POST create operations |
| Successful GET/Update | 200 | GET, PUT operations |
| Successful Delete | 200 | DELETE operations |
| Validation Error | 422 | Missing/invalid fields |
| Authentication Error | 401 | Missing/invalid token |
| Authorization Error | 403 | User lacks permission |
| Not Found | 404 | Resource doesn't exist |
| Rate Limited | 429 | Too many login attempts |
| Server Error | 500 | GridFS/Database errors |

---

## MongoDB Verification

Check MongoDB collections after testing:

```javascript
// MongoDB CLI
use chat_app

// Check collections created
show collections

// View documents
db.users.find().pretty()
db.workspaces.find().pretty()
db.teams.find().pretty()
db.channels.find().pretty()
db.messages.find().pretty()
db.file_attachments.find().pretty()
db.personal_access_tokens.find().pretty()

// Check GridFS
db.fs.files.find().pretty()
db.fs.chunks.find().pretty()
```

---

## Cache Verification

Clear cache between tests if needed:

```bash
# In Laravel tinker
php artisan tinker
> Cache::flush()
> exit()
```

---

## Email Testing

Emails are sent to the configured MAIL_MAILER (default: log)
Check log file: `storage/logs/laravel.log`

Look for "workspace-invitation" and "team-invitation" in logs.

---

## Notes

- All tests assume MongoDB is accessible
- Tokens expire based on Sanctum configuration (null = no expiry)
- Timestamps are stored in ISO 8601 format
- Soft deletes use `deleted_at` timestamp
- Cache keys follow pattern: `{resource}_{id}_{filter}`
- GridFS stores files with metadata
- All passwords are hashed using bcrypt

---

## Common Issues & Solutions

### Issue: "SQLSTATE[HY000]: General error: 1030 Got error"
**Solution**: Check MongoDB is running: `mongod`

### Issue: Token not working
**Solution**: Ensure token is copied correctly from login response, prefixed with "Bearer "

### Issue: Files not found after upload
**Solution**: Verify GridFS is enabled and MongoDB has sufficient space

### Issue: Cache not working
**Solution**: Check CACHE_STORE in .env, run `php artisan cache:clear`

### Issue: Emails not sending
**Solution**: Check MAIL_MAILER in .env (set to 'log' for testing), check storage/logs/laravel.log

---

## Success Criteria

✅ All endpoints respond with correct status codes
✅ Validation errors return 422 with error messages
✅ Permissions are enforced (403 when unauthorized)
✅ Caching works (same response on repeated requests)
✅ GridFS files are stored and retrievable
✅ Passwords are hashed (not plain text)
✅ Tokens are valid and expire appropriately
✅ Middleware prevents unauthorized access
✅ Messages track sender and edit timestamps
✅ Channels distinguish public/private access
