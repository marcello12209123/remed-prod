<?php

namespace App\Http\Controllers;

use App\Helpers\ApiFormatter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
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

    public function create(Request $request)
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

    public function store (Request $request) {
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
    
    public function show($id) {
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

    public function edit(User $user)
    {
        if (!$user) {
            return response()->json(['error' => 'User tidak ditemukan'], 404);
        }
        return response()->json(['data' => $user], 200);
    
    }

    public function update(Request $request, $id)
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

    public function destroy($id)
    {
        try {
            $checkProses = User::where('id', $id)->delete();

                return ApiFormatter::sendResponse(200, 'success', 'Data berhasil dihapus!');
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse (400, 'bad request', $err->getMessage());
        }
    }

    public function trash()
    {
        try {
            $data = User::onlyTrashed()->get();

                return ApiFormatter::sendResponse(200, 'success', $data);
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse (400, 'bad request', $err->getMessage());
        }
    }

    public function restore($id)
    {
        try {
            $checkProses = User::onlyTrashed()->where('id', $id)->restore();

            if($checkProses) {
                $data = User::find($id);
                return ApiFormatter::sendResponse(200, 'success', $data);
            } else {
                return ApiFormatter::sendResponse(400, 'bad request', 'Gagal mengembalikan data! ');
            }
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse (400, 'bad request', $err->getMessage());
            }
    }

    public function deletePermanent($id)
    {
        try {
            $checkProses = User::onlyTrashed()->where('id', $id)->forceDelete();

                return ApiFormatter::sendResponse(200, 'success', 'Berhasil menghapus data secara permanen!');
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse (400, 'bad request', $err->getMessage());
        }
    }

    public function login(Request $request){
        try {
            $this->validate($request, [
                'email' => 'required',
                'password' => 'required',
            ]);

            $user = User::where('email', $request->email)->first(); // mencari dan mendapatkan data user berdasarkan email yang digunakan untuk login

            if(!$user) {
                //Jika email tidak terdaftar maka akan dikembalikan response error
                return ApiFormatter::sendResponse(400, false, 'Login Failed! User Does Not Exists');
            } else {
                // Mencocokkan Password yang diinput dengan Password di database
                $isValid = Hash::check($request->password, $user->password);

                if (!$isValid) {
                    // Jika password tidak cocok m aka akan dikembalikan dengan response error
                    return ApiFormatter::sendResponse(400, false, 'Login Failed! Password Does Not Matches');
                } else {
                    // Jika password sesuai selanjutnya akan membuat token
                    // bin2hex digunakan untuk dapat mengonversi string karakter ASCII menjadi nilai heksadesimal
                    // random_bytes menghasilkan byye pseuido-acak yang aman secara kriptografis dengan panjang 40 karakter
                    $generateToken = bin2hex(random_bytes(40));
                    
                    // Token inilah nanti yang digunakan pada proses authentication user yang login
                    $user->update([
                        'token' => $generateToken
                     ]);
    
                    return ApiFormatter::sendResponse(200, 'Login Successfully', $user);
                    }
                } 
            } catch (\Exception $e) {
            return ApiFormatter::sendResponse(400, false, $e->getMessage());
        }
    } 

    public function logout(Request $request)
    {
        try {
            $this->validate($request, ['email' => 'required', 
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return APIFormatter::sendResponse(400, 'login failed! user Doesnt Exist');
        } else {
            if (!$user->token) {
                return APIFormatter::sendResponse(400, 'logout failed! user doesnt login Scine');
            } else {
                $logout = $user->update(['token' => null]);

                if ($logout) {
                    return APIFormatter::sendResponse(200, 'logout Successfully');
                }
            }
        }
    }catch (\Exception $e) {
        return ApiFormatter::sendResponse(400, false, $e->getMessage());
    }
}

}