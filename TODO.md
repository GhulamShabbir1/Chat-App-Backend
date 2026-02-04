# TODO: Move Validations to Middlewares

## Step 1: Create Middlewares
- [ ] Update ValidateFileUpload middleware for file upload validation
- [ ] Create ValidateWorkspaceUpdate middleware
- [ ] Create ValidateTeamUpdate middleware
- [ ] Create ValidateTeamAddMember middleware
- [ ] Create ValidateTeamRemoveMember middleware
- [ ] Create ValidateDirectMessagesIndex middleware
- [ ] Create ValidateChannelMessageStore middleware
- [ ] Create ValidateDirectMessageStore middleware
- [ ] Create ValidateMessageUpdate middleware

## Step 2: Register Middlewares
- [ ] Register new middlewares in bootstrap/app.php

## Step 3: Apply Middlewares to Routes
- [ ] Update routes/file.php for file upload middleware
- [ ] Update routes/workspace.php for workspace update middleware
- [ ] Update routes/team.php for team middlewares
- [ ] Update routes/message.php for message middlewares

## Step 4: Remove Validations from Controllers
- [ ] Remove validations from FileAttachmentController
- [ ] Remove validations from WorkspaceController
- [ ] Remove validations from TeamController
- [ ] Remove validations from MessageController

## Step 5: Testing
- [ ] Test all endpoints to ensure validations work through middlewares
