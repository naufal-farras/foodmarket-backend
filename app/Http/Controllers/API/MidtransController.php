<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Notification;

class MidtransController extends Controller
{
    public function callback(Request $request)
    {
        //Set konfigurasi 
        Config::$serverKey = config('services.midtrans.serverKey');
        Config::$isProduction = config('services.midtrans.isProduction');
        Config::$isSanitized = config('services.midtrans.isSanitized');
        Config::$is3ds = config('services.midtrans.is3ds');


        //Buat instance midtrans notification

        $notification = new Notification();
        //Assign ke variable untuk memudahkan coding
        $status= $notification->transaction_status;
        $type= $notification->payment_type;
        $fraud = $notification->fraud->status;
        $order_id = $notification->order_id;


        //Cari transaski berdasarkan ID

        $transaction = Transaction::findorFail($order_id);
        //Handle notifikasi status midtrans

        if ($status == 'capture') 
        {
            if ($type == 'credit_card') 
            {
                if ($fraud == 'challenge') {
                  $transaction->status= 'PENDING';
                }
                else
                {
                    $transaction->status= 'SUCCESS';

                }
            }
        }
        else if($status == 'settlement') 
        {
            $transaction->status= 'SUCCESS';

        }
        else if($status == 'pending') 
        {
            $transaction->status= 'PENDING';

        }
        else if($status == 'deny') 
        {
            $transaction->status= 'CANCCELED';

        }
        else if($status == 'expire') 
        {
            $transaction->status= 'CANCCELED';

        }
        else if($status == 'cancel') 
        {
            $transaction->status= 'CANCCELED';

        }
        //Simpan transaksi
        $transaction->save();
        
    }

    public function success()
    {
        return view('midtrans.success');
    }
    public function unfinish()
    {
        return view('midtrans.unfinish');
    }  
    public function error()
    {
        return view('midtrans.error');
    }
}
