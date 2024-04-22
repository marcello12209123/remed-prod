<?php

namespace App\Http\Controllers;

use App\Models\InboundStuff;
use Illuminate\Http\Request;
use App\Models\Stuff;
use App\Models\StuffStock;
use App\Helpers\ApiFormatter;

class InboundStuffController extends Controller
{
    public function index()
    {
        try{
            $data = InboundStuff::with('stuff')->get();
    
            return Apiformatter::sendResponse(200, 'succes', $data);
           }catch(\Exception $err){
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
            $this->validate($request, [
                'stuff_id' => 'required',
                'total' => 'required',
                'date' => 'required',
                'proff_file' => 'required|mimes:jpeg,png,pdf|max:2048',
            ]);

            if ($request->hasFile('proff_file')) {
                $proof = $request->file('proff_file');
                $destinationPath = 'proof/';
                $proofName = date('YmdHis') . "." . $proof->getClientOriginalExtension();
                $proof->move($destinationPath, $proofName);
            }

            $createStock = InboundStuff::create([
                'stuff_id' => $request->stuff_id,
                'total' => $request->stuff_id,
                'date' => $request->date,
                'proff_file' => $proofName,
            ]);

            if ($createStock) {
                $getStuff = Stuff::where('id', $request->stuff_id)->first();
                $getStuffStock = StuffStock::where('stuff_id', $request->stuff_id)->first();

                if (!$getStuffStock) {
                    $updateStock = StuffStock::create([
                        'stuff_id' => $request->stuff_id,
                        'total_available' => $request->total,
                        'total_defec' => 0,

                    ]);

                }else {
                    $updateStock = $getstuffStock->update([
                        'stuff_id' => $request->stuff_id,
                        'total_available' => $getStuffStock['total_available'] + $request->total,
                        'total_defec' => $getStuffStock['total_defec'],
                    ]);

                }

                if ($updateStock) {
                    $getStock = stuffStock::where('stuff_id', $request->stuff_id)->first();
                    $stuff = [
                        'stuff' => $getStuff,
                        'InboundStuff' => $createStock,
                        'stuffStock' => $getStock,

                    ];
                    return APIFormatter::sendResponse(200, 'successfully Createa a Inbound Stuff Data', $stuff);

                }else{
                    return APIFormatter::sendResponse(400, 'failed to update a stuff Stock data');
                }
            } else {
                return APIFormatter::sendResponse(400, 'failed to create a inbund stuff data');
            }

        }catch (\Throwable $th) {
            return APIFormatter::sendResponse(400, false, $th->getMessage());

        }
    }

    public function show(InboundStuff $inboundStuff)
    {
        try {
            $data = InboundStuff::with('stuff')->where('id', $id)->first();

            if (is_null($data)) {
                return ApiFormatter::sendResponse(400, 'bad request', 'Data not found!');
            } else {
                return ApiFormatter::sendResponse(200, 'success', $data);
            }
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }

    public function edit(InboundStuff $inboundStuff)
    {
        //
    }

    public function update(Request $request, InboundStuff $inboundStuff)
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

    public function destroy(InboundStuff $inboundStuff)
    {
        try{
            $checkProses= InboundStuff::where('id', $id)->delete();

            return ApiFormatter::sendResponse(200, 'success', 'Data stuff berhasil di hapus');
        }catch(\Exception $err){
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }
}
