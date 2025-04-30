<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Requests\StoreGroupRequest;
use Illuminate\Contracts\Cache\Store;

class GroupController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                throw new \Exception('User not authenticated');
            }
            
            $groups = $user->groups()->get();
            return ApiResponse::success($groups, "Groups fetched successfully");
        } catch (\Exception $e) {
            // \Log::error('Error fetching groups: ' . $e->getMessage());
            return ApiResponse::error("Failed to fetch groups");
        }
    }

    public function create(): View
    {
        return view('groups.create');
    }

    public function store(StoreGroupRequest $request)
    {
        try {
            $validated = $request->validated();
                
            
            $user = Auth::user();
            if (!$user) {
                throw new \Exception('User not authenticated');
            }
            
            $validated['user_id'] = $user->id;
            $group = Group::create($validated);

            return ApiResponse::success($group, "Group created successfully");
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::error('Validation error', $e->errors(), 422);
        } catch (\Exception $e) {
            // \Log::error('Error creating group: ' . $e->getMessage());
            return ApiResponse::error('Failed to create group', [], 500);
        }
        // return redirect()->route('groups.index')
        //     ->with('success', 'Group created successfully.');
    }

    public function show(Group $group): View
    {
        $user = Auth::user();
        if ($group->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        };
        $group->load('expenses');
        return view('groups.show', compact('group'));
    }

    public function edit(Group $group): View
    {
        $user = Auth::user();
        if ($group->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        };
        return view('groups.edit', compact('group'));
    }

    public function update(StoreGroupRequest $request, Group $group)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                throw new \Exception('User not authenticated');
            }
            
            if ($group->user_id !== $user->id) {
                throw new \Exception('Unauthorized action');
            }
            
            $validated = $request->validated();
            $group->update($validated);

            return ApiResponse::success($group, "Group updated successfully");
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::error('Validation error', $e->errors(), 422);
        } catch (\Exception $e) {
            // \Log::error('Error updating group: ' . $e->getMessage());
            return ApiResponse::error('Failed to update group', [], 500);
        }
        // return redirect()->route('groups.index')
        //     ->with('success', 'Group updated successfully.');
    }

    public function destroy(Group $group)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                throw new \Exception('User not authenticated');
            }
            
            if ($group->user_id !== $user->id) {
                throw new \Exception('Unauthorized action');
            }
            
            $group->delete();
            return ApiResponse::success([], "Group deleted successfully");
        } catch (\Exception $e) {
            // \Log::error('Error deleting group: ' . $e->getMessage());
            return ApiResponse::error('Failed to delete group', [], 500);
        }
    }
}
