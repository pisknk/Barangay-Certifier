<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\TenantUserRequest;
use App\Models\Tenant\TenantUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TenantUserController extends Controller
{
    /**
     * Display a listing of tenant users.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $users = TenantUser::all();
            
            return response()->json([
                'status' => 'success',
                'data' => $users,
                'tenant_id' => tenant('id')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve users: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created tenant user.
     *
     * @param  \App\Http\Requests\Tenant\TenantUserRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(TenantUserRequest $request)
    {
        try {
            // Request is already validated by the form request
            $user = new TenantUser();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->role = $request->role ?? 'user';
            $user->position = $request->position;
            $user->phone = $request->phone;
            $user->save();
            
            return response()->json([
                'status' => 'success',
                'message' => 'User created successfully',
                'data' => $user,
                'tenant_id' => tenant('id')
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified tenant user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $user = TenantUser::find($id);
            
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found'
                ], 404);
            }
            
            return response()->json([
                'status' => 'success',
                'data' => $user,
                'tenant_id' => tenant('id')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified tenant user.
     *
     * @param  \App\Http\Requests\Tenant\TenantUserRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(TenantUserRequest $request, $id)
    {
        try {
            $user = TenantUser::find($id);
            
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found'
                ], 404);
            }
            
            // Update user fields when specified
            if ($request->has('name')) {
                $user->name = $request->name;
            }
            
            if ($request->has('email')) {
                $user->email = $request->email;
            }
            
            if ($request->has('password')) {
                $user->password = Hash::make($request->password);
            }
            
            if ($request->has('role')) {
                $user->role = $request->role;
            }
            
            if ($request->has('position')) {
                $user->position = $request->position;
            }
            
            if ($request->has('phone')) {
                $user->phone = $request->phone;
            }
            
            $user->save();
            
            return response()->json([
                'status' => 'success',
                'message' => 'User updated successfully',
                'data' => $user,
                'tenant_id' => tenant('id')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified tenant user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $user = TenantUser::find($id);
            
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found'
                ], 404);
            }
            
            $user->delete();
            
            return response()->json([
                'status' => 'success',
                'message' => 'User deleted successfully',
                'tenant_id' => tenant('id')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete user: ' . $e->getMessage()
            ], 500);
        }
    }
} 