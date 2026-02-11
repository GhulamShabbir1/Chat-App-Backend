- [x] Create `app/Models/OtpToken.php` model for storing OTP tokens
- [x] Create `app/Mail/OtpMail.php` for sending OTP emails
- [x] Create `app/Http/Requests/Auth/SendOtpRequest.php` validation class
- [x] Create `app/Http/Requests/Auth/VerifyOtpRequest.php` validation class
- [x] Modify `app/Http/Controllers/AuthController.php`: rename `register` to `sendOtp`, add `verifyOtp` method
- [x] Update `routes/auth.php` to replace `/register` with `/send-otp` and add `/verify-otp`
- [ ] Test the OTP send and verify flow
=======
## Tasks
- [x] Create `app/Models/OtpToken.php` model for storing OTP tokens
- [x] Create `app/Mail/OtpMail.php` for sending OTP emails
- [x] Create `app/Http/Requests/Auth/SendOtpRequest.php` validation class
- [x] Create `app/Http/Requests/Auth/VerifyOtpRequest.php` validation class
- [x] Modify `app/Http/Controllers/AuthController.php`: rename `register` to `sendOtp`, add `verifyOtp` method
- [x] Update `routes/auth.php` to replace `/register` with `/send-otp` and add `/verify-otp`
- [x] Test the OTP send and verify flow
