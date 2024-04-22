<?php

namespace App\Http\Controllers;

use App\Models\Restoration;
use Illuminate\Http\Request;

class RestorationController extends Controller
{
    public function index()
    {
        try {
            $data = User::all()->toArray();

            return ApiFormatter::sendResponse(200, 'success', $data);
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }

    public function create(request $request)
    {
        $this->validate($request, [
            'username'=>'required',
            'email'=>'required',
            'password'=> 'required',
        ]);

        $user = new User;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->password = app('hash')->make($request->password);

        $user->save();
    }

    public function store(Request $request)
    {
        try {
            $this->validate($request, [
            'username' => 'required',
            'email' => 'required',
            'password' => 'required',
            'role' => 'required',
            ]);

            $createUser = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            ]);

            return ApiFormatter::sendResponse(200, 'success', $createUser);
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse (400, 'bad request', $err->getMessage());
            }
    }

    public function show(Restoration $restoration)
    {
        try{
            $data = User::where('id', $id)->first();

            if (is_null($data)) {
                return ApiFormatter::sendResponse(400, 'bad request', 'Data not found!');
            } else {
                return ApiFormatter::sendResponse(200, 'success', $data);
            }
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse (400, 'bad request', $err->getMessage());
            }
    }

    public function edit(Restoration $restoration)
    {
        if (!$user) {
            return response()->json(['error' => 'User tidak ditemukan'], 404);
        }
        return response()->json(['data' => $user], 200);
    
    }

    public function update(Request $request, Restoration $restoration)
    {
        try {
            $this->validate($request, [
                'username' => 'required',
                'email' => 'required',
                'password' => 'required',
                'role' => 'required',
            ]);

            $checkProses = User::where('id', $id)->update([
                'username' => $request->username,
                'email' => $request->email,
                'password' => $request->password,
                'role' => $request->role,
            ]);

            if($checkProses) {
                $data = User::find($id);
                return ApiFormatter::sendResponse(200, 'success', $data);
            } else {
                return ApiFormatter::sendResponse(400, 'bad request', 'Gagal mengubah data! ');
            }
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse (400, 'bad request', $err->getMessage());
            }
    }

    public function destroy(Restoration $restoration)
    {
        try {
            $checkProses = User::where('id', $id)->delete();

                return ApiFormatter::sendResponse(200, 'success', 'Data berhasil dihapus!');
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse (400, 'bad request', $err->getMessage());
        }
    }
}
