<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Detail_Transaksi;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    public function show()
    {
        return Transaksi::all();
    }

    public function detail($id)
    {
    if(Transaksi::where('id_transaksi', $id)->exists()) {
    $data_transaksi = DB::table('transaksi')
           ->join('pelanggan', 'transaksi.id_pelanggan', '=', 'pelanggan.id_pelanggan')
           ->join('petugas', 'transaksi.id_petugas', '=', 'petugas.id_petugas')
           ->select('transaksi.id_transaksi', 'pelanggan.id_pelanggan', 'petugas.id_petugas', 'transaksi.tgl_transaksi')
           ->where('transaksi.id_transaksi', '=', $id)
           ->get();
    return Response()->json($data_transaksi);
   }
    else {
     return Response()->json(['message' => 'Tidak ditemukan' ]);
    }
    }

    public function store(Request $request)
    {
        $data=array(
            'tgl_transaksi' => date('Y-m-d'),
            'subtotal' => 0
        );
        $proses=Transaksi::create($data);

        if($proses){
            $id_transaksi=$proses->id_transaksi;
            $subtotal=0;
            foreach ($request->get('datapost') as $gdata) {
                $insert_detail=Detail_Transaksi::create([
                    'id_transaksi'=>$id_transaksi,
                    'id_produk'=>$gdata['id_produk'],
                    'qty'=>$gdata['quantity'],
                ]);
                $subtotal+=$gdata['harga']*$gdata['quantity'];
            }
            $updatetransaksi=Transaksi::where('id_transaksi', $id_transaksi)->update([
                'subtotal'=>$subtotal
            ]);
            return Response()->json(['status' => 1, 'message' => 'Sukses menambahkan transaksi']);
        } else {
            return Response()->json(['status' => 0, 'message' => 'Gagal menambahkan transaksi']);
        }
    }

    public function update($id, Request $request)  {
        $validator=Validator::make($request->all(),
        [
            'id_pelanggan' => 'required',
            'id_petugas' => 'required',
        ]);
       
        if($validator->fails()) {
        return Response()->json($validator->errors());
        }
       
        $ubah = Transaksi::where('id_transaksi', $id)->update([
            'id_pelanggan' => $request->id_pelanggan,
            'id_petugas' => $request->id_petugas,
            'tgl_transaksi' => date("Y-m-d")
        ]);
        if($ubah) {
        return Response()->json(['status' => 1]);
        }
        else {
        return Response()->json(['status' => 0]);
        }
    }

    public function destroy($id)
    {
       $hapus = Transaksi::where('id_transaksi', $id)->delete();
       if($hapus) {
       return Response()->json(['status' => 1]);
    }
       else {
       return Response()->json(['status' => 0]);
}
}
}