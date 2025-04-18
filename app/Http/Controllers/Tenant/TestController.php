<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\TenantUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class TestController extends Controller
{
    /**
     * Test endpoint to verify tenant users functionality
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function testTenantUsers()
    {
        try {
            // Get tenant information
            $tenant = tenant();
            $tenantId = $tenant ? $tenant->id : null;
            
            // Verify database connection
            $connection = DB::connection('tenant')->getDatabaseName();
            
            // Get table schema
            $schema = [];
            $columns = DB::connection('tenant')->select('SHOW COLUMNS FROM tenant_users');
            foreach ($columns as $column) {
                $schema[$column->Field] = $column->Type;
            }
            
            // Get user count
            $userCount = TenantUser::count();
            
            // Return test information
            return response()->json([
                'status' => 'success',
                'message' => 'Tenant users test completed successfully',
                'tenant_info' => [
                    'id' => $tenantId,
                    'database' => $connection,
                ],
                'schema' => $schema,
                'user_count' => $userCount,
                'sample_data' => TenantUser::take(3)->get()
            ]);
        } catch (\Exception $e) {
            Log::error('Tenant test error: ' . $e->getMessage(), [
                'exception' => $e,
                'tenant_id' => tenant('id') ?? 'undefined'
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Test failed: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }
    
    /**
     * Create a test user for development purposes
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function createTestUser()
    {
        try {
            // Check if test user already exists
            $existingUser = TenantUser::where('email', 'test@example.com')->first();
            
            if ($existingUser) {
                return response()->json([
                    'status' => 'info',
                    'message' => 'Test user already exists',
                    'user' => $existingUser
                ]);
            }
            
            // Create a test user
            $user = new TenantUser();
            $user->name = 'Test User';
            $user->email = 'test@example.com';
            $user->password = Hash::make('password123');
            $user->role = 'user';
            $user->position = 'Tester';
            $user->phone = '1234567890';
            $user->save();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Test user created successfully',
                'user' => $user,
                'tenant_id' => tenant('id')
            ], 201);
        } catch (\Exception $e) {
            Log::error('Create test user error: ' . $e->getMessage(), [
                'exception' => $e,
                'tenant_id' => tenant('id') ?? 'undefined'
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create test user: ' . $e->getMessage()
            ], 500);
        }
    }
} 