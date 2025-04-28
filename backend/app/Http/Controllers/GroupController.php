<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\View\View;



class GroupController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $groups =$user->groups()->get();
        return response()->json($groups);
        // return view('groups.index', compact('groups'));
    }

    public function create(): View
    {
        return view('groups.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
        ]);
        $user = Auth::user();
        $validated['user_id'] = $user->id;

         // Add user_id to the validated data
        $group = Group::create($validated);

        return response()->json([
            'message' => 'Group created successfully.',
            'group' => $group,
            'validated' => $validated
            
        ]);
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

    public function update(Request $request, Group $group)
    {
        $user = Auth::user();
        if ($group->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        };
        $validated = $request->validate([
            'name' => 'required|max:255'
        ]);

        $group->update($validated);

        return response()->json([
            'message' => 'Group updated successfully.',
            'group' => $group
        ]);
        // return redirect()->route('groups.index')
        //     ->with('success', 'Group updated successfully.');
    }

    public function destroy(Group $group)
    {
       $user = Auth::user();
         if ($group->user_id !== $user->id) {
                abort(403, 'Unauthorized action.');
          };
        $group->delete();

       return response()->json([
            'message' => 'Group deleted successfully.'
       ]);
    }
}
