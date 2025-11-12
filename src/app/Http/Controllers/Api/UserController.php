<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\GetUsersRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserCollectionResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Exception;
use PHPUnit\Framework\MockObject\Stub\Stub;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected UserService $userService
    ) {}

    /**
     * Get paginated list of users.
     *
     * @param GetUsersRequest $request
     * @return JsonResponse
     */
    public function index(GetUsersRequest $request): JsonResponse
    {
        try {
            $result = $this->userService->getUsers(
                $request->validated(),
                $request->user()
            );

            return response()->json([
                'page' => $result['page'],
                'users' => UserCollectionResource::collection($result['users']),
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve users. Please try again later.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Create a new user.
     *
     * @param StoreUserRequest $request
     * @return JsonResponse
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        try {
            $user = $this->userService->createUser($request->validated());

            return response()->json(new UserResource($user), 201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create user. Please try again later.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
