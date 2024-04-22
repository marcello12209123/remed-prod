<?php

namespace App\Http\Controllers;

use App\Models\StuffStock;
use Illuminate\Http\Request;

class StuffStockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $data = stuffStock::with('stuff')->get();
    
            return Apiformatter::sendResponse(200, 'succes', $data);
        }catch(\Exception $err){
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
           }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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

            $createStock = StuffStock::create([
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

    public function show(StuffStock $stuffStock)
    {
        try{
            $data = StuffStock::where('id', $id)->first();

            if (is_null($data)) {
                return ApiFormatter::sendResponse(400, 'bad request', 'Data not found!');
            } else {
                return ApiFormatter::sendResponse(200, 'success', $data);
            }
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse (400, 'bad request', $err->getMessage());
            }
    }

    public function edit(StuffStock $stuffStock)
    {
        //
    }

    public function update(Request $request, StuffStock $stuffStock)
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

    public function destroy(StuffStock $stuffStock)
    {
        try{
            $checkProses= StuffStock::where('id', $id)->delete();

            return ApiFormatter::sendResponse(200, 'success', 'Data stuff berhasil di hapus');
        }catch(\Exception $err){
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }

    public function addStock(Request $request, $id)
    {
    try {
        $getStuffStock = stuffStock::find($id);

        if (!$getStuffStock) {
            return APIformatter::sendResponse(404, false, 'data stuff stock not found');
        } else {
            $this->validate($request, [
                'total_available' => 'required',
                'total_defac' => 'required',
            ]);

            $addStock = $getStuffStock->update([
                'total_available' => $getStuffStock['total_available'] + $request->total_available,
                'total_defac' => $getStuffStock['total_defac'] + $request->total_defac,

            ]);

            if ($addStock) {
                $getStuffAdded = StuffStock::where('id', $id)->with('stuff')->first();

                return APIformatter::sendResponse(200, true, 'succesfully add a stock of stuff stock data', $getStuffStock);
            }
        }
    } catch(\Exception $err){
        return APIFormatter::sendResponse(400, false, $th->getMessage());
        }
    }

    public function subStock(Request $request, $id)
    {
        try {
             $getStuffStock = StuffStock::find($id);

             if (!$getStuffStock) {
                return ApiFormatter::sendResponse(400, false, 'Data Stuff Stock Not Found');
             } else {
                $this->validate($request, [
                    'total_available' => 'required',
                    'total_defac' => 'required',
                ]);

                $isStockAvailable = $getStuffStock->update['total_available'] - $request->total_available;
                $isStockDefac = $getStuffStock->update['total_defac'] - $request->total_defac;

                if ($isStockAvailable < 0 || $isStockDefac < 0) {
                    return ApiFormatter::sendResponse(400, true, 'Substraction Stock Cant Less Than A Stock Stored');
                } else {
                    $subStock = $getStuffStock->update([
                        'total_available' => $isStockAvailable,
                        'total_defac' => $isStockDefac,
                    ]);

                    if ($subStock) {
                        $getStockSub = StuffStock::where('id', $id)->with('stuff')->first();

                        return ApiFormatter::sendResponse(200, true, 'Succesfully Sub A Stock Of StuFf Stock Data', $getStockSub);
                    }
                }
             }
        }catch(\Exception $err){
            return ApiFormatter::sendResponse(400, $err->getMessage());
        }
    }
}
