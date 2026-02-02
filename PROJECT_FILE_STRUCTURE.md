# Project File Structure & Documentation

**Chat App Backend - Laravel MongoDB**  
**Date:** 2026-02-02

---

## 📂 Core Application Files

### Models (app/Models/)

#### **User.php**
- **Function:** Represents user accounts
- **How it works:** Stores user login credentials, profile info, and relationships
- **Why needed:** Core entity for authentication and user identification
- **Key fields:** name, email, password, status, profile_picture

#### **Workspace.php**
- **Function:** Represents workspaces (organization containers)
- **How it works:** Groups teams and channels together for organization
- **Why needed:** Organize multiple teams within a workspace hierarchy
- **Key fields:** name, description, owner_id, created_at

#### **Team.php**
- **Function:** Represents teams within workspaces
- **How it works:** Contains channels and members, belongs to workspace
- **Why needed:** Organize users into teams for collaboration
- **Key fields:** name, workspace_id, owner_id, members

#### **Channel.php**
- **Function:** Represents communication channels
- **How it works:** Belongs to team, contains messages, can be public/private
- **Why needed:** Organize conversations by topic or purpose
- **Key fields:** name, team_id, description, is_public

#### **Message.php**
- **Function:** Represents chat messages
- **How it works:** Contains text, user info, file attachments, timestamps
- **Why needed:** Store conversation history and communication
- **Key fields:** body, user_id, channel_id, file_id, created_at

#### **FileAttachment.php**
- **Function:** Represents uploaded files
- **How it works:** Stores metadata; actual file in MongoDB GridFS
- **Why needed:** Track file uploads and manage file access
- **Key fields:** filename, gridfs_id, size, mime_type, uploader_id

---

### Controllers (app/Http/Controllers/)

#### **AuthController.php**
- **Function:** Handle user authentication
- **How it works:** Register new users, issue login tokens, manage sessions
- **Why needed:** Secure user access and token generation
- **Methods:** register(), login(), logout()

#### **WorkspaceController.php**
- **Function:** Manage workspaces (CRUD)
- **How it works:** Create, read, update, delete workspaces with authorization
- **Why needed:** Allow users to organize multiple workspaces
- **Methods:** store(), index(), show(), update(), destroy()

#### **TeamController.php**
- **Function:** Manage teams (CRUD)
- **How it works:** Create teams within workspaces, manage members
- **Why needed:** Organize users into teams
- **Methods:** store(), index(), show(), update(), destroy()

#### **ChannelController.php**
- **Function:** Manage channels (CRUD)
- **How it works:** Create channels in teams, set public/private
- **Why needed:** Create communication channels
- **Methods:** store(), index(), show(), update(), destroy()

#### **MessageController.php**
- **Function:** Handle messages (CRUD)
- **How it works:** Create channel messages, direct messages, retrieve history
- **Why needed:** Store and retrieve conversations
- **Methods:** storeChannelMessage(), storeDirectMessage(), index()

#### **FileAttachmentController.php**
- **Function:** Manage file uploads/downloads
- **How it works:** Upload to GridFS, download with streaming, delete with cleanup
- **Why needed:** Enable file sharing in chat
- **Methods:** upload(), download(), destroy(), index(), show()

---

### Middleware (app/Http/Middleware/)

#### **mongo.auth**
- **Function:** Verify Bearer token authentication
- **How it works:** Check token validity, inject user into request
- **Why needed:** Protect routes from unauthorized access

#### **workspace.access**
- **Function:** Verify workspace access permissions
- **How it works:** Check if user belongs to workspace
- **Why needed:** Prevent unauthorized workspace access

#### **team.access**
- **Function:** Verify team access permissions
- **How it works:** Check if user belongs to team
- **Why needed:** Prevent unauthorized team access

#### **channel.access**
- **Function:** Verify channel access permissions
- **How it works:** Check if user can access channel
- **Why needed:** Prevent unauthorized channel access

---

### Guards (app/Guards/)

#### **MongoDBGuard.php**
- **Function:** Custom authentication guard for MongoDB
- **How it works:** Implements Laravel Guard interface for Sanctum tokens
- **Why needed:** Enable Bearer token authentication with MongoDB
- **Uses:** Sanctum tokens, custom token model

---

## 🔧 Configuration Files (config/)

#### **auth.php**
- **Function:** Define authentication configuration
- **What it does:** Set default guard (mongo), specify authentication providers
- **Why needed:** Configure which authentication method to use

#### **database.php**
- **Function:** Configure database connections
- **What it does:** Define MongoDB connection string, database name
- **Why needed:** Connect to MongoDB

#### **sanctum.php**
- **Function:** Configure API token authentication
- **What it does:** Set token expiration, middleware
- **Why needed:** Manage API tokens for authentication

#### **app.php**
- **Function:** Global application configuration
- **What it does:** Set app name, timezone, providers
- **Why needed:** Control app behavior

#### **cache.php**
- **Function:** Configure caching
- **What it does:** Set cache driver and settings
- **Why needed:** Performance optimization

#### **mail.php**
- **Function:** Configure email settings
- **What it does:** Set mail driver, from address
- **Why needed:** Send emails (team invitations, etc.)

#### **filesystems.php**
- **Function:** Configure file storage
- **What it does:** Define storage drivers and paths
- **Why needed:** Store uploaded files

#### **logging.php**
- **Function:** Configure error logging
- **What it does:** Set log channel and output format
- **Why needed:** Track application errors

#### **session.php**
- **Function:** Configure sessions
- **What it does:** Set session driver, lifetime
- **Why needed:** Manage user sessions

#### **queue.php**
- **Function:** Configure background jobs
- **What it does:** Set queue driver and settings
- **Why needed:** Process jobs asynchronously

---

## 📍 Route Files (routes/)

#### **api.php**
- **Function:** Define all API endpoints
- **What it does:** Map HTTP requests to controller methods
- **Why needed:** Configure URL routing for API
- **Endpoints:** 
  - `/api/auth/*` - Authentication
  - `/api/workspaces/*` - Workspace CRUD
  - `/api/teams/*` - Team CRUD
  - `/api/channels/*` - Channel CRUD
  - `/api/messages/*` - Message operations
  - `/api/files/*` - File operations

#### **web.php**
- **Function:** Define web routes (rarely used)
- **What it does:** Map web page requests
- **Why needed:** Support web-based access if needed

#### **console.php**
- **Function:** Define console commands
- **What it does:** Register custom Artisan commands
- **Why needed:** Run maintenance tasks

---

## 🗄️ Database Files (database/)

#### **Migrations/** (0001_01_01_*)
- **Function:** Define database schema
- **How it works:** Create tables and set up structure
- **Why needed:** Initialize database for models
- **Tables created:**
  - users - User accounts
  - cache - Cache table
  - jobs - Queue jobs

#### **Seeders/DatabaseSeeder.php**
- **Function:** Populate database with sample data
- **How it works:** Insert test data for development
- **Why needed:** Quick testing without manual data entry

#### **Factories/UserFactory.php**
- **Function:** Generate fake user data
- **How it works:** Create realistic test users
- **Why needed:** Test with realistic data

---

## 📦 Project Files

#### **composer.json**
- **Function:** Define PHP dependencies
- **What it does:** List all required packages and versions
- **Why needed:** Manage project dependencies

#### **package.json**
- **Function:** Define Node.js dependencies
- **What it does:** List JavaScript packages needed
- **Why needed:** Frontend tooling (if using Vite)

#### **phpunit.xml**
- **Function:** Configure unit tests
- **What it does:** Set test database and paths
- **Why needed:** Run and configure tests

#### **vite.config.js**
- **Function:** Configure Vite build tool
- **What it does:** Set build process for assets
- **Why needed:** Build frontend assets (CSS, JS)

#### **.env**
- **Function:** Environment variables
- **What it does:** Store MongoDB URI, app key, credentials
- **Why needed:** Configuration without committing secrets

#### **.env.example**
- **Function:** Template for .env
- **What it does:** Show what .env variables are needed
- **Why needed:** Help developers set up .env correctly

#### **README.md**
- **Function:** Project overview
- **What it does:** Describe project and setup
- **Why needed:** Help new developers understand project

#### **.gitignore**
- **Function:** Exclude files from Git
- **What it does:** Prevent committing node_modules, .env, vendor
- **Why needed:** Keep repository clean

#### **.editorconfig**
- **Function:** Standardize code formatting
- **What it does:** Define indentation, line endings
- **Why needed:** Consistent code style

#### **artisan**
- **Function:** Laravel CLI tool
- **What it does:** Run commands (migrate, serve, etc.)
- **Why needed:** Interact with Laravel framework

---

## 📂 Project Directories

#### **app/**
- Contains all application logic (models, controllers, middleware, guards)

#### **bootstrap/**
- Contains application bootstrap files (cache, providers)

#### **config/**
- Contains all configuration files

#### **database/**
- Contains migrations, seeders, factories

#### **public/**
- Contains index.php and publicly accessible files

#### **resources/**
- Contains views and frontend files (CSS, JavaScript)

#### **routes/**
- Contains route definitions

#### **storage/**
- Contains logs, cache, temporary files

#### **tests/**
- Contains test files

#### **vendor/**
- Contains installed dependencies (generated by Composer)

---

## 🔑 Key Architecture Decisions

### MongoDB Instead of SQL
- **Why:** Better for flexible schema and document storage
- **How:** Uses MongoDB driver with Eloquent ORM

### GridFS for File Storage
- **Why:** Built-in MongoDB feature, handles large files
- **How:** Files stored in GridFS collections (fs.files, fs.chunks)

### Sanctum for Authentication
- **Why:** Built-in Laravel token auth
- **How:** Issues Bearer tokens, verified by MongoDBGuard

### Bearer Token Auth
- **Why:** Stateless, REST-compliant, suitable for APIs
- **How:** Client sends `Authorization: Bearer {token}` header

### Middleware for Access Control
- **Why:** Centralized permission checking
- **How:** Verifies user access before reaching controller

---

## 🔄 Data Flow Example: Upload File

```
1. User sends POST /api/files/upload with file
2. mongo.auth middleware verifies token
3. FileAttachmentController@upload() handles request
4. File validated (size, type)
5. File uploaded to MongoDB GridFS
6. Database record created in FileAttachment collection
7. Response returned with file_id and gridfs_id
```

---

## 🔄 Data Flow Example: Send Message

```
1. User sends POST /api/channels/{id}/messages with text
2. mongo.auth middleware verifies token
3. channel.access middleware verifies access
4. MessageController@storeChannelMessage() handles request
5. Message created in database
6. If file attached: link file_id to message
7. Response returned with message_id
```

---

## 📊 Database Collections (MongoDB)

| Collection | Purpose | Key Fields |
|-----------|---------|-----------|
| users | Store user accounts | _id, name, email, password |
| workspaces | Store workspaces | _id, name, owner_id |
| teams | Store teams | _id, name, workspace_id, owner_id |
| channels | Store channels | _id, name, team_id |
| messages | Store messages | _id, body, user_id, channel_id |
| file_attachments | Store file metadata | _id, filename, gridfs_id, uploader_id |
| fs.files | GridFS file metadata | _id, filename, length, metadata |
| fs.chunks | GridFS file chunks | _id, files_id, data |

---

## 🚀 API Endpoints Summary

| Method | Endpoint | Function |
|--------|----------|----------|
| POST | /api/auth/register | Register user |
| POST | /api/auth/login | Login user |
| POST | /api/workspaces | Create workspace |
| GET | /api/workspaces | List workspaces |
| POST | /api/teams | Create team |
| GET | /api/teams | List teams |
| POST | /api/channels | Create channel |
| GET | /api/channels | List channels |
| POST | /api/channels/{id}/messages | Send message |
| GET | /api/channels/{id}/messages | Get messages |
| POST | /api/files/upload | Upload file |
| GET | /api/files | List files |
| GET | /api/files/{id}/download | Download file |
| DELETE | /api/files/{id} | Delete file |

---

## 📝 Important Notes

### Security
- All routes require authentication (except /auth/register, /auth/login)
- Ownership verified for sensitive operations
- File size limited to 10MB
- CORS configured for frontend access

### Performance
- MongoDB indexes on frequently queried fields
- Pagination on list endpoints
- File streaming (not buffering)
- Connection pooling for database

### Error Handling
- All endpoints return JSON errors
- HTTP status codes indicate error type
- Clear error messages for debugging

---

## 🔗 Relationships

```
User
├── has many Workspaces (as owner)
├── has many Teams (as member)
├── has many Messages
└── has many FileAttachments (as uploader)

Workspace
├── has many Teams
└── belongs to User (owner)

Team
├── has many Channels
└── belongs to Workspace

Channel
├── has many Messages
└── belongs to Team

Message
├── has one FileAttachment (optional)
└── belongs to User

FileAttachment
└── belongs to User (uploader)
```

---

**This document provides a complete overview of the project structure and file functions.**

For API usage, see: [API_ENDPOINTS_REFERENCE.md](API_ENDPOINTS_REFERENCE.md)  
For Postman testing, see: [POSTMAN_TESTING_GUIDE.md](POSTMAN_TESTING_GUIDE.md)
