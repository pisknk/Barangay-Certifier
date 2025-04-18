<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestDashboardController extends Controller
{
    /**
     * Display a simple test dashboard
     */
    public function testDashboard()
    {
        // Hard-coded testing data - no database queries
        $data = [
            'activeTenants' => 5,
            'totalIncome' => 2000,
            'totalRevenue' => 5000,
            'debug' => true
        ];
        
        // Try to render the admin dashboard view
        return view('admin.admindash', $data);
    }

    /**
     * Simple test that doesn't even use a view
     */
    public function simpleTest()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Simple test controller is working'
        ]);
    }
}
