<?php

namespace App\Http\Controllers;

use App\helpers\ApiFormatter;
use App\Models\Stuff;
use Illuminate\Http\Request;

class StuffController extends Controller
{
    public function index()
    {
        try {
            $data = stuff::all()->toArray();

            return ApiFormatter::sendResponse(200, 'success', $data);
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {
            $this ->validate($request, [
                'name' => 'required', 
                'category' => 'required',
            ]);

            $data = stuff::create([
                'name' => $request->name,
                'category' => $request->category,
            ]);

            return ApiFormatter::sendResponse(200, 'successs', $data);
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }

    public function show(Stuff $stuff)
    {
        try {
            $data = stuff::where('id', $id)->first();

            if (is_null($data)) {
                return ApiFormatter::sendResponse(400, 'bad request', 'Data not found!');
            } else {
                return ApiFormatter::sendResponse(200, 'success', $data);
            }
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }

    public function edit(Stuff $stuff)
    {
        if (!$user) {
            return response()->json(['error' => 'User tidak ditemukan'], 404);
        }
        return response()->json(['data' => $user], 200);
    }

    public function update(Request $request, Stuff $stuff)
    {
        try {
            $this->validate($request, [
                'name' => 'required', 
                'category' => $request->category
            ]);

            $checkProses = stuff::where('id', $id)->update([
                'name' => $request->name, 
                'category' => $request->category
            ]);

            if ($checkProses) {
                $data = stuff::find($id);
                return ApiFormatter::sendResponse(200, 'success', $data);
            } else {
                return ApiFormatter::sendResponse(400, 'bad request', 'gagal mengubah data!');
            }
        } catch (\Expection $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }

    public function destroy(Stuff $stuff)
    {
        try{
            $checkProses= Stuff::where('id', $id)->delete();

            return ApiFormatter::sendResponse(200, 'success', 'Data stuff berhasil di hapus');
        }catch(\Exception $err){
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }
}
