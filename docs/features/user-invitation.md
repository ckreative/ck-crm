# User Invitation Feature Specification

## Overview

The User Invitation feature allows administrators to invite other administrators to the platform via email. This is the only method for creating new user accounts - direct registration is disabled, and users can only join through valid invitations.

## User Stories

1. **As an admin**, I want to invite other admins via email so that I can grant access to trusted users.
2. **As an admin**, I want to see all users and pending invitations so that I can manage system access.
3. **As an invited user**, I want to accept an invitation and create my account so that I can access the system.
4. **As an admin**, I want invitations to expire after 7 days so that unused invitations don't pose a security risk.

## Technical Specification

### Database Schema

#### User Invitations Table Migration
```php
Schema::create('user_invitations', function (Blueprint $table) {
    $table->id();
    $table->string('email')->unique();
    $table->string('token', 64)->unique();
    $table->foreignId('invited_by')->constrained('users')->onDelete('cascade');
    $table->timestamp('expires_at');
    $table->timestamp('accepted_at')->nullable();
    $table->timestamps();
    
    $table->index('token');
    $table->index('email');
});
```

### Models

#### UserInvitation Model
```php
class UserInvitation extends Model
{
    protected $fillable = [
        'email',
        'token',
        'invited_by',
        'expires_at',
        'accepted_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    public function invitedBy()
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isAccepted(): bool
    {
        return $this->accepted_at !== null;
    }

    public function isPending(): bool
    {
        return !$this->isAccepted() && !$this->isExpired();
    }
}
```

### Routes

```php
// Admin routes (protected by auth and admin middleware)
Route::middleware(['auth', 'admin'])->group(function () {
    // App Settings
    Route::prefix('app-settings')->name('app-settings.')->group(function () {
        Route::get('/', [AppSettingsController::class, 'index'])->name('index');
        
        // User Management
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserManagementController::class, 'index'])->name('index');
            Route::post('/invite', [UserManagementController::class, 'invite'])->name('invite');
            Route::post('/{invitation}/resend', [UserManagementController::class, 'resend'])->name('resend');
            Route::delete('/{invitation}', [UserManagementController::class, 'cancel'])->name('cancel');
        });
    });
});

// Public invitation routes
Route::prefix('invitations')->name('invitations.')->group(function () {
    Route::get('/{token}', [InvitationController::class, 'show'])->name('show');
    Route::post('/{token}', [InvitationController::class, 'accept'])->name('accept');
});
```

### Controllers

#### AppSettingsController
- `index()` - Display app settings main page with menu

#### UserManagementController
- `index()` - Display users list with active users and pending invitations
- `invite(Request $request)` - Send new invitation
- `resend(UserInvitation $invitation)` - Resend invitation email
- `cancel(UserInvitation $invitation)` - Cancel pending invitation

#### InvitationController
- `show($token)` - Display invitation acceptance form
- `accept(Request $request, $token)` - Process invitation acceptance

### Email Template

#### InvitationMail
```php
class InvitationMail extends Mailable
{
    public function build()
    {
        return $this->subject('You have been invited to ' . config('app.name'))
                    ->markdown('emails.invitation', [
                        'invitedBy' => $this->invitation->invitedBy->name,
                        'acceptUrl' => route('invitations.show', $this->invitation->token),
                        'expiresAt' => $this->invitation->expires_at,
                    ]);
    }
}
```

## User Interface

### Navigation Structure

```
Main Navigation:
├── Dashboard
├── App Settings (admin only)
└── User Dropdown
    ├── Profile (personal settings)
    └── Logout

App Settings Sidebar:
├── General
├── Users (user management)
└── [Future sections]
```

### User Management Page (`/app-settings/users`)

#### Layout
- Uses app settings layout with sidebar
- Main content area shows:
  1. Page header with "Users" title and "Invite User" button
  2. Tabs or sections for "Active Users" and "Pending Invitations"
  3. Data table with search/filter capabilities

#### User Table Columns
- Name
- Email
- Role (currently always "admin")
- Status (Active/Pending)
- Invited By
- Date (Joined/Invited)
- Actions

#### Invite User Modal
- Email input field (required, validated)
- Personal message textarea (optional)
- Send button
- Cancel button

### Invitation Acceptance Page (`/invitations/{token}`)

#### Layout
- Guest layout (user not logged in)
- Centered card design similar to login page

#### Form Fields
- Email (pre-filled, read-only)
- Name (required)
- Password (required, min 8 characters)
- Password Confirmation (required, must match)

#### Validation States
- Expired invitation message
- Already accepted message
- Invalid token message

## Security Considerations

1. **Token Generation**
   ```php
   $token = Str::random(64);
   ```

2. **Expiration Logic**
   - Invitations expire after 7 days
   - Check expiration before displaying form
   - Clean up expired invitations via scheduled job

3. **Rate Limiting**
   - Limit invitation sending to 10 per hour per admin
   - Limit invitation acceptance attempts

4. **Middleware Protection**
   ```php
   class AdminMiddleware
   {
       public function handle($request, Closure $next)
       {
           if (!auth()->user()?->isAdmin()) {
               abort(403);
           }
           return $next($request);
       }
   }
   ```

## Implementation Checklist

### Phase 1: Database & Models
- [ ] Create user_invitations migration
- [ ] Create UserInvitation model
- [ ] Add relationships to User model
- [ ] Create AdminMiddleware

### Phase 2: Backend Logic
- [ ] Create controllers
- [ ] Implement invitation creation logic
- [ ] Create invitation email template
- [ ] Implement acceptance logic
- [ ] Add validation rules

### Phase 3: Frontend - Admin Side
- [ ] Create app settings layout
- [ ] Build user management page
- [ ] Create invite user modal
- [ ] Implement data tables
- [ ] Add action buttons

### Phase 4: Frontend - Invitation Acceptance
- [ ] Create invitation acceptance page
- [ ] Handle expired/invalid states
- [ ] Implement form submission
- [ ] Add success redirect

### Phase 5: Testing & Cleanup
- [ ] Write feature tests
- [ ] Test invitation flow end-to-end
- [ ] Add invitation cleanup job
- [ ] Security testing

## Future Enhancements

1. **Role Management**
   - Add different user roles beyond admin
   - Role-based permissions

2. **Bulk Invitations**
   - CSV import for multiple emails
   - Invitation templates

3. **Invitation Analytics**
   - Track invitation acceptance rates
   - Show invitation history

4. **Team Features**
   - Organize users into teams
   - Team-based invitations

## Testing Scenarios

### Functional Tests
1. Admin can access user management page
2. Non-admin cannot access user management
3. Valid invitation can be sent
4. Duplicate email invitation fails
5. Invitation email is received
6. Valid token shows acceptance form
7. Invalid token shows error
8. Expired invitation cannot be accepted
9. Accepted invitation creates user
10. User can login after acceptance

### Edge Cases
1. Inviting already registered user
2. Re-inviting after cancellation
3. Multiple invitations to same email
4. Invitation acceptance with existing session
5. Database constraints on deletion