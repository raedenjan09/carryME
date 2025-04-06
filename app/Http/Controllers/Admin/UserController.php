<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $users = User::select(['id', 'name', 'email', 'role', 'is_active', 'created_at']);
            
            return DataTables::of($users)
                ->addColumn('status', function($user) {
                    $status = $user->is_active ? 'active' : 'inactive';
                    $class = $user->is_active ? 'success' : 'danger';
                    return "<span class='badge bg-{$class}'>" . ucfirst($status) . "</span>";
                })
                ->addColumn('role', function($user) {
                    return ucfirst($user->role);
                })
                ->addColumn('action', function($user) {
                    $statusBtn = $user->is_active 
                        ? '<button class="btn btn-sm btn-danger" onclick="updateUserStatus('.$user->id.')">Deactivate</button>'
                        : '<button class="btn btn-sm btn-success" onclick="updateUserStatus('.$user->id.')">Activate</button>';
                    
                    $roleBtn = '<div class="btn-group">
                        <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                            Change Role
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="updateUserRole('.$user->id.', \'user\')">User</a></li>
                            <li><a class="dropdown-item" href="#" onclick="updateUserRole('.$user->id.', \'admin\')">Admin</a></li>
                        </ul>
                    </div>';
                    
                    return '<div class="btn-group">' . $statusBtn . $roleBtn . '</div>';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('admin.users.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Update the status of the specified user.
     */
    public function updateStatus(Request $request, User $user)
    {
        try {
            $was_active = $user->is_active;
            $user->update(['is_active' => !$user->is_active]);

            if ($was_active && !$user->is_active) {
                // Force logout all sessions for this user
                \DB::table('sessions')
                    ->where('user_id', $user->id)
                    ->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'User status updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating user status'
            ], 500);
        }
    }

    /**
     * Update the role of the specified user.
     */
    public function updateRole(Request $request, User $user)
    {
        try {
            $request->validate(['role' => 'required|in:admin,user']);
            $user->update(['role' => $request->role]);
            return response()->json([
                'success' => true,
                'message' => 'User role updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating user role'
            ], 500);
        }
    }
}
