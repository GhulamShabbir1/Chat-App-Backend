# Chat App Backend - Laravel MongoDB

<p align="center">
<img src="https://img.shields.io/badge/Laravel-12.49.0-red" alt="Laravel Version">
<img src="https://img.shields.io/badge/MongoDB-Latest-green" alt="MongoDB">
<img src="https://img.shields.io/badge/Status-Production Ready-brightgreen" alt="Status">
</p>

## 🚀 Quick Start

**New to the project?** → Read [QUICK_START.md](QUICK_START.md)

**Want to test immediately?** → Run `.\test_gridfs.ps1`

**Need API docs?** → See [API_ENDPOINTS_REFERENCE.md](API_ENDPOINTS_REFERENCE.md)

**File upload issues?** → Check [GRIDFS_QUICK_REFERENCE.md](GRIDFS_QUICK_REFERENCE.md)

**All documentation?** → See [DOCUMENTATION_INDEX.md](DOCUMENTATION_INDEX.md)

---

## 📋 Project Overview

Complete backend for a real-time chat application built with:
- **Framework:** Laravel 12.49.0
- **Database:** MongoDB with GridFS
- **Authentication:** Sanctum + Custom MongoDBGuard
- **Features:** Teams, Channels, Messaging, File Sharing

### Status: ✅ PRODUCTION READY

All features implemented, tested, and documented.

---

## 🎯 Features Implemented

### Authentication ✅
- User registration with validation
- Token-based authentication (Sanctum)
- Profile management
- Secure token storage

### Workspace & Team Management ✅
- Create workspaces
- Organize teams within workspaces
- Manage team members
- Team invitations

### Communication ✅
- Public channels with messaging
- Direct messaging between users
- Message history with pagination
- Message deletion

### File Management ✅
- File upload to GridFS
- File download with streaming
- File deletion with cleanup
- File listing with pagination
- File metadata storage

### Error Handling ✅
- Comprehensive error responses
- Clear error messages
- Proper HTTP status codes
- Logging and monitoring

---

## 📦 Project Structure

```
app/
├── Models/
│   ├── User.php
│   ├── Workspace.php
│   ├── Team.php
│   ├── Channel.php
│   ├── Message.php
│   └── FileAttachment.php
├── Http/Controllers/
│   ├── AuthController.php
│   ├── WorkspaceController.php
│   ├── TeamController.php
│   ├── ChannelController.php
│   ├── MessageController.php
│   └── FileAttachmentController.php
└── Guards/
    └── MongoDBGuard.php

routes/
├── api.php
└── web.php

config/
├── auth.php
├── database.php
└── sanctum.php
```

---

## 🔗 API Endpoints

### Authentication
- `POST /api/auth/register` - Register user
- `POST /api/auth/login` - Login user

### Workspaces
- `POST /api/workspaces` - Create workspace
- `GET /api/workspaces` - List workspaces
- `GET /api/workspaces/{id}` - Get workspace
- `PUT /api/workspaces/{id}` - Update workspace
- `DELETE /api/workspaces/{id}` - Delete workspace

### Teams
- `POST /api/teams` - Create team
- `GET /api/teams` - List teams
- `GET /api/teams/{id}` - Get team
- `PUT /api/teams/{id}` - Update team
- `DELETE /api/teams/{id}` - Delete team

### Channels
- `POST /api/channels` - Create channel
- `GET /api/channels` - List channels
- `GET /api/channels/{id}` - Get channel
- `DELETE /api/channels/{id}` - Delete channel

### Messages
- `POST /api/channels/{id}/messages` - Send channel message
- `GET /api/channels/{id}/messages` - Get channel messages
- `POST /api/direct-messages` - Send direct message
- `GET /api/direct-messages` - Get direct messages

### Files
- `POST /api/files/upload` - Upload file to GridFS
- `GET /api/files` - List user's files
- `GET /api/files/{id}` - Get file details
- `GET /api/files/{id}/download` - Download file from GridFS
- `DELETE /api/files/{id}` - Delete file

Full documentation: [API_ENDPOINTS_REFERENCE.md](API_ENDPOINTS_REFERENCE.md)

---

## 🧪 Testing

### Run All Tests
```powershell
.\test_gridfs.ps1
```

### Import to Postman
Import `Chat_App_API.postman_collection.json` for full API testing

### Test Commands
See [TEST_COMMANDS.md](TEST_COMMANDS.md) for detailed testing procedures

---

## 📚 Documentation

| Document | Purpose |
|----------|---------|
| [QUICK_START.md](QUICK_START.md) | 5-minute setup guide |
| [SETUP_GUIDE.md](SETUP_GUIDE.md) | Detailed installation |
| [API_ENDPOINTS_REFERENCE.md](API_ENDPOINTS_REFERENCE.md) | All API endpoints |
| [API_README.md](API_README.md) | API overview |
| [GRIDFS_GUIDE.md](GRIDFS_GUIDE.md) | File storage guide |
| [GRIDFS_QUICK_REFERENCE.md](GRIDFS_QUICK_REFERENCE.md) | File API reference |
| [POSTMAN_API_DOCUMENTATION.md](POSTMAN_API_DOCUMENTATION.md) | Postman guide |
| [TROUBLESHOOTING.md](TROUBLESHOOTING.md) | Problem solving |
| [DOCUMENTATION_INDEX.md](DOCUMENTATION_INDEX.md) | All documentation |

**All project documentation:** [DOCUMENTATION_INDEX.md](DOCUMENTATION_INDEX.md)

---

## 🔧 Configuration

### Environment Setup
```bash
# Copy environment file
cp .env.example .env

# Set MongoDB connection
MONGO_URI=mongodb://user:password@localhost:27017
MONGO_DATABASE=chat_app

# Generate app key
php artisan key:generate
```

### MongoDB Setup
```javascript
// Create database and collections
db.createDatabase('chat_app')
db.createCollection('users')
db.createCollection('workspaces')
db.createCollection('teams')
db.createCollection('channels')
db.createCollection('messages')
db.createCollection('file_attachments')

// GridFS collections auto-created on first file upload
// fs.files and fs.chunks
```

---

## 📁 GridFS File Storage

### Features
- ✅ Upload files to GridFS
- ✅ Download files with streaming
- ✅ Delete files with cleanup
- ✅ File metadata storage
- ✅ Ownership verification

### Quick Reference
- Upload: `POST /api/files/upload`
- Download: `GET /api/files/{id}/download`
- Delete: `DELETE /api/files/{id}`
- List: `GET /api/files`

See [GRIDFS_QUICK_REFERENCE.md](GRIDFS_QUICK_REFERENCE.md) for examples

---

## 🔒 Security

- ✅ Bearer token authentication
- ✅ Ownership verification
- ✅ Input validation
- ✅ Error message safety
- ✅ File size limits (10MB)
- ✅ CORS configuration
- ✅ Rate limiting ready

---

## 📊 Project Completion

### Status: ✅ PRODUCTION READY

- ✅ All endpoints implemented
- ✅ Error handling complete
- ✅ Security verified
- ✅ Tests passing
- ✅ Documentation complete
- ✅ Performance optimized

See [PROJECT_COMPLETION_SUMMARY.md](PROJECT_COMPLETION_SUMMARY.md) for details

---

## 🚀 Deployment

### Prerequisites
- PHP 8.3+
- MongoDB 5.0+
- Composer
- NodeJS (optional, for frontend)

### Steps
1. Clone repository
2. Install dependencies: `composer install`
3. Configure `.env` file
4. Run migrations: `php artisan migrate:fresh --seed`
5. Test: `.\test_gridfs.ps1`
6. Deploy

See [SETUP_GUIDE.md](SETUP_GUIDE.md) for detailed steps

---

## 🐛 Troubleshooting

### Common Issues
1. **MongoDB Connection Error** → Check MONGO_URI in .env
2. **GridFS Upload Fails** → Check file size < 10MB
3. **Authorization Error** → Verify Bearer token is current
4. **File Not Found** → Check file hasn't been deleted

Full troubleshooting: [TROUBLESHOOTING.md](TROUBLESHOOTING.md)

---

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
"# Chat-App-Backend" 
