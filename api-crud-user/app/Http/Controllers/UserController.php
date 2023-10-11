<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $users=User::all();
        } catch (\Exception $e) {
            return response()->json(["error" => "Users not get"]);
        }
        $users = User::all();
        if(count($users) <= 0){
            return response(["message" => "Aucun utilisateur disponible pour le moment"]);
        }
        return response()->json($users, 201);
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
        // La validation des données
        $validator = Validator::make($request->all(), [
            "name" => "required|unique:users|regex:/^[\w\s-]+$/|max:100",
            "email" => "required|email|unique:users|regex:/^.+@.+\..+$/i",
            "password" => "required|min:8"
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed', 'messages' => $validator->errors ()], 400);
        }
        try {
            $user = User::create([
                "name" => $request->name,
                "email" => $request->email,
                "password" => bcrypt($request->password)
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => "user not created"], 500);
        }
        return response()->json($user,201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $user = User::find($id);
        } catch (\Throwable $th) {
            return response()->json(["Error" => "user not catch"]);
        }
        return response()->json($user, 201);
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
    public function update(Request $request, $id)
    {
        try {
             $user = User::find($id);
        } catch (\Exception $e) {
            return response()->json(["Error" => "user not catch"]);
        }

        // La validation des données
        $validator = Validator::make($request->all(), [
            "name" => "required|unique:users|regex:/^[\w\s-]+$/|max:100",
            "email" => "required|email|unique:users|regex:/^.+@.+\..+$/i",
            "password" => "required|min:8"
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed', 'messages' => $validator->errors ()], 400);
        };
// echo($request);
// echo($user);
         // Vérification du mot de passe
        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Mot de passe incorrect'], 401);
        };
        try {
            $user->update([
                "name"=> $request->name,
                "email" =>$request->email
            ]);

        } catch (\Exception $e) {
            return response()->json(["Error" => "Update not successfully"], 500);
        }
        return response()->json($user,201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            User::where("id", $id)->delete();
        } catch (\Exception $e) {
            return response()->json(["Error" => "Delete not successfully"], 500);
        }
        return response()->json(['message' => 'User deleted successfully'], 200);
    }
}
