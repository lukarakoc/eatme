<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getUsers(): JsonResponse
    {
        $users = User::with('role')->orderBy('id', 'desc')->paginate(10);
        return response()->json(['success', $users]);
    }

    public function store(UserRequest $request): JsonResponse
    {
        $data = $request->validated();

        if (auth()->user()->role_id != Role::ADMIN) return response()->json(['Admin jedino može dodavati zaposlene'], 403);

        try {
            $data['temporary_password'] = $data['password'];
            DB::beginTransaction();
            User::insert([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role_id' => $data['role_id'],
                'email_verified_at' => now()
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            abort(500, $e->getTraceAsString());
        }
        return response()->json(['success']);
    }

    public function update(UserRequest $request): JsonResponse
    {
        $user = User::findOrFail($request->id);

        if (auth()->user()->role_id != Role::ADMIN) return response()->json(['Admin jedino može mijenjati podatke o zaposlenom'], 403);

        try {
            DB::beginTransaction();
            $user->name = $request->name;
            $user->email = $request->email;
            if ($request->password) {
                $user->password = Hash::make($request->password);
            }
            if ($request->role_id != $user->role_id) {
                $user->role_id = Role::where('id', $request->role_id)->first()->id;
            }
            $user->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            abort(500, $e->getTraceAsString());
        }
        return response()->json(['success']);
    }

    public function destroy($id): JsonResponse
    {
        $user = User::findOrFail($id);
        if ($user->id == auth()->id()) {
            return response()->json(['error' => 'Nije dozvoljeno brisanje sopstvenog profila.'], 422);
        }
        if (auth()->user()->role_id != Role::ADMIN) return response()->json(['error' =>'Admin jedino može izbrisati zaposlenog'], 403);

        $user->delete();
        return response()->json(['success']);
    }

    public function searchUsers($keyword): JsonResponse
    {
        return response()->json(['success', User::searchUsers($keyword)]);
    }

    public function getAllRoles(): JsonResponse
    {
        return response()->json(['success', Role::all()]);
    }

}
