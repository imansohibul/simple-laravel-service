<?php

namespace App\Services;

use App\Models\User;
use App\Mail\WelcomeNewUser;
use App\Mail\NewUserNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Exception;

class UserService
{
    /**
     * Create a new user and send notification emails.
     *
     * @param array $data {
     *     @type string $email    Valid email address (unique)
     *     @type string $password Plain text password (will be hashed)
     *     @type string $name     User's full name
     * }
     *
     * @return User The newly created user instance
     *
     * @throws Exception If user creation fails or database transaction error occurs
     */
    public function createUser(array $data): User
    {
        DB::beginTransaction();

        try {
            // Create the user
            $user = User::create([
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'name' => $data['name'],
                'role' => 'user',
                'active' => true,
            ]);

            // Send emails asynchronously
            $this->sendNotificationEmails($user);

            DB::commit();

            Log::info('New user created successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            return $user;

        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Failed to create user', [
                'email' => $data['email'] ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Send notification emails to user and admin.
     *
     * @param User $user
     * @return void
     */
    protected function sendNotificationEmails(User $user): void
    {
        try {
            // Send welcome email to the new user
            Mail::to($user->email)->queue(new WelcomeNewUser($user));

            // Send notification to admin
            $adminEmail = config('mail.admin_email');
            if ($adminEmail) {
                Mail::to($adminEmail)->queue(new NewUserNotification($user));
            }

        } catch (Exception $e) {
            // Log email sending errors but don't fail the user creation
            Log::error('Failed to send notification emails', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get paginated list of users with filters and sorting.
     *
     * @param array $params {
     *     @type string|null $search  Search term for name or email
     *     @type int         $page    Page number (default: 1)
     *     @type string      $sortBy  Sort column: 'name', 'email', 'created_at' (default: 'created_at')
     * }
     * @param User|null $currentUser Currently authenticated user for permission check
     *
     * @return array {
     *     @type int   $page  Current page number
     *     @type array $users Array of user objects with orders_count and can_edit
     * }
     */
    public function getUsers(array $params, ?User $currentUser): array
    {
        // Get filtered users using Model method
        $users = User::getFilteredUsers([
            'search' => $params['search'] ?? null,
            'page' => $params['page'] ?? 1,
            'sortBy' => $params['sortBy'] ?? 'created_at',
            'perPage' => 15,
        ]);

        // Add can_edit attribute to each user
        $users->getCollection()->transform(function ($user) use ($currentUser) {
            $user->can_edit = $currentUser ? $currentUser->canEdit($user) : false;
            return $user;
        });

        return [
            'page' => $users->currentPage(),
            'users' => $users->items(),
        ];
    }
}
