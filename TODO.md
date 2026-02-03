# Custom Token Authentication Implementation

## Tasks
- [ ] Create CustomAccessToken model for MongoDB
- [ ] Update User model: remove HasApiTokens, add createCustomToken method using mt_rand
- [ ] Create CustomAuthMiddleware for authentication
- [ ] Update AuthController to use custom tokens
- [ ] Update routes/api.php to use 'custom.auth' middleware
- [ ] Remove Sanctum from bootstrap/providers.php
- [ ] Remove Sanctum from composer.json
- [ ] Update config/auth.php to remove api guard
- [ ] Test the implementation
