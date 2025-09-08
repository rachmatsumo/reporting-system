<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CustomScript;

class CustomScriptController extends Controller
{
    public function index()
    {
        $scripts = CustomScript::all();
        return view('admin.custom_scripts.index', compact('scripts'));
    }

    public function create()
    {
        return view('admin.custom_scripts.create');
    }   

    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'script' => 'required|string',
            'is_active' => 'required|in:0,1',
        ]);

        CustomScript::create([
            'name'    => $request->name,
            'script'  => $request->script,
            'is_active'  => $request->is_active,
            'user_id' => auth()->id(), // otomatis ambil user login
        ]);

        return redirect()
            ->route('custom-scripts.index')
            ->with('success', 'Custom script created successfully.');
    }

    public function edit($id)
    {
        $script = CustomScript::findOrFail($id);
        return view('admin.custom_scripts.edit', compact('script'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'script'    => 'required|string',
            'is_active' => 'required|boolean',
        ]);

        $script = CustomScript::findOrFail($id);

        $script->update([
            'name'      => $request->name,
            'script'    => $request->script,
            'is_active' => $request->is_active,
            'user_id'   => auth()->id(), // optional, kalau mau override pemilik
        ]);

        return redirect()
            ->route('custom-scripts.index') // pastikan konsisten dengan store()
            ->with('success', 'Custom script updated successfully.');
    }


    public function destroy($id)
    {
        $script = CustomScript::findOrFail($id);
        $script->delete();

        return redirect()->route('custom-scripts.index')->with('success', 'Custom script deleted successfully.');
    }

    public function run(Request $request)
    {
        $request->validate([
            'script_id' => 'required|exists:custom_scripts,id',
        ]);

        $script = CustomScript::findOrFail($request->script_id);

        if (!$script->is_active) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Script tidak aktif',
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'script' => $script->script,
        ]);
    }


}
