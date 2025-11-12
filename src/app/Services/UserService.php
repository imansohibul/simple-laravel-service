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
     * @param array $data
     * @return User
     * @throws Exception
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
     * Check if email already exists.
     *
     * @param string $email
     * @return bool
     */
    public function emailExists(string $email): bool
    {
        return User::where('email', $email)->exists();
    }

   /**
     * Get paginated list of users with filters and sorting.
     *
     * @param array $params
     * @param User|null $currentUser
     * @return array
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

    /**
     * Determine if current user can edit the target user.
     *
     * @param User|null $currentUser
     * @param User $targetUser
     * @return bool
     */
    protected function canEditUser(?User $currentUser, User $targetUser): bool
    {
        if (!$currentUser) {
            return false;
        }

        // Administrator can edit any user
        if ($currentUser->role === 'administrator') {
            return true;
        }

        // Manager can only edit users with role 'user'
        if ($currentUser->role === 'manager') {
            return $targetUser->role === 'user';
        }

        // User can only edit themselves
        if ($currentUser->role === 'user') {
            return $currentUser->id === $targetUser->id;
        }

        return false;
    }
}
