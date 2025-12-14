<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $query = User::with('wilayah');

        // Filter by role
        if ($request->has('role') && $request->role) {
            $query->where('role', $request->role);
        }

        // Filter by wilayah
        if ($request->has('wilayah_id') && $request->wilayah_id) {
            $query->where('wilayah_id', $request->wilayah_id);
        }

        // Filter by status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('name')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:admin,petugas_pos,keamanan',
            'wilayah_id' => 'nullable|exists:wilayah,id',
            'is_active' => 'boolean',
        ]);

        // Validate role-wilayah relationship
        if ($request->role === UserRole::KEAMANAN->value && !$request->wilayah_id) {
            throw ValidationException::withMessages([
                'wilayah_id' => ['Keamanan users must be assigned to a wilayah.']
            ]);
        }

        if ($request->role !== UserRole::KEAMANAN->value && $request->wilayah_id) {
            throw ValidationException::withMessages([
                'wilayah_id' => ['Only keamanan users can be assigned to a wilayah.']
            ]);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'wilayah_id' => $request->wilayah_id,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => [
                'user' => $user->load('wilayah')
            ]
        ], 201);
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $currentUser = auth()->user();

        // Check if current user can view this user
        if (!$currentUser->canManageUser($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user->load('wilayah')
            ]
        ]);
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        $currentUser = auth()->user();

        // Check if current user can update this user
        if (!$currentUser->canManageUser($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'username' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => 'sometimes|required|in:admin,petugas_pos,keamanan',
            'wilayah_id' => 'nullable|exists:wilayah,id',
            'is_active' => 'boolean',
        ]);

        // Validate role-wilayah relationship
        if ($request->has('role') && $request->role === UserRole::KEAMANAN->value && !$request->wilayah_id) {
            throw ValidationException::withMessages([
                'wilayah_id' => ['Keamanan users must be assigned to a wilayah.']
            ]);
        }

        if ($request->has('role') && $request->role !== UserRole::KEAMANAN->value && $request->has('wilayah_id')) {
            if ($request->wilayah_id) {
                throw ValidationException::withMessages([
                    'wilayah_id' => ['Only keamanan users can be assigned to a wilayah.']
                ]);
            }
        }

        $updateData = $request->only(['name', 'email', 'username', 'role', 'wilayah_id', 'is_active']);

        // Only hash password if provided
        if ($request->has('password') && $request->password) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => [
                'user' => $user->fresh()->load('wilayah')
            ]
        ]);
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        $currentUser = auth()->user();

        // Check if current user can delete this user
        if (!$currentUser->canManageUser($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        // Prevent self-deletion
        if ($currentUser->id === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account'
            ], 422);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }

    /**
     * Toggle user active status.
     */
    public function toggleStatus(User $user)
    {
        $currentUser = auth()->user();

        // Check if current user can manage this user
        if (!$currentUser->canManageUser($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        // Prevent deactivating self
        if ($currentUser->id === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot deactivate your own account'
            ], 422);
        }

        $user->update([
            'is_active' => !$user->is_active
        ]);

        $status = $user->is_active ? 'activated' : 'deactivated';

        return response()->json([
            'success' => true,
            'message' => "User {$status} successfully",
            'data' => [
                'user' => $user->fresh()
            ]
        ]);
    }

    /**
     * Assign wilayah to keamanan user.
     */
    public function assignWilayah(Request $request, User $user)
    {
        $currentUser = auth()->user();

        // Only admin can assign wilayah
        if (!$currentUser->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        $request->validate([
            'wilayah_id' => 'required|exists:wilayah,id'
        ]);

        if (!$user->isKeamanan()) {
            return response()->json([
                'success' => false,
                'message' => 'Only keamanan users can be assigned to wilayah'
            ], 422);
        }

        $user->update([
            'wilayah_id' => $request->wilayah_id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Wilayah assigned successfully',
            'data' => [
                'user' => $user->fresh()->load('wilayah')
            ]
        ]);
    }

    /**
     * Get keamanan users by wilayah.
     */
    public function getKeamananByWilayah($wilayahId)
    {
        $currentUser = auth()->user();

        // Check access to this wilayah
        if (!$currentUser->canAccessWilayah($wilayahId)) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        $keamananUsers = User::where('role', UserRole::KEAMANAN)
            ->where('wilayah_id', $wilayahId)
            ->where('is_active', true)
            ->select('id', 'name', 'email', 'username')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'users' => $keamananUsers
            ]
        ]);
    }
}