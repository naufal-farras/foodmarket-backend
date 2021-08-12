<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function all(Request $request)
    {
        $id=$request->input('id');
        $limit=$request->input('limit',6);
        $food_id=$request->input('food_id');
        $status=$request->input('status');

        $price_from=$request->input('price_from');
        $price_to=$request->input('price_to');

        $rate_from=$request->input('rate_from');
        $rate_to=$request->input('rate_to');

        if ($id) {

            $transaction = Transaction::with(['food','user'])->find($id);

          if ($transaction) {
              return ResponseFormatter::success(
                  $transaction,'Data transaksi berhasil diambil'
              );
          }
          else 
          {
              return ResponseFormatter::error(
                  null,
                  'Data transaksi tidak ada',
                  404
              );
          }
        }

        $transaction=Transaction::with(['food','user'])
                     ->where('user_id', Auth::user()->id);

        if ($food_id) {
            
            $transaction->where('food_id',$food_id);
        }
        if ($status) {
            
            $transaction->where('status',$status);
        }
       
        return ResponseFormatter::success(
            $transaction->paginate($limit),
            'Data list transaction berhasil diambil'
        );
    }
}
