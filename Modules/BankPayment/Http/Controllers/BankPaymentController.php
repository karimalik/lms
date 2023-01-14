<?php

namespace Modules\BankPayment\Http\Controllers;

use App\User;
use App\DepositRecord;
use App\Traits\ImageStore;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Support\Renderable;
use Modules\BankPayment\Entities\BankPaymentRequest;

class BankPaymentController extends Controller
{
    use ImageStore;
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $payments = BankPaymentRequest::latest()->paginate(10);
        return view('bankpayment::index', compact('payments'));

    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('bankpayment::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return bool
     */
    public function store(Request $request)
    {

        try {

            $payment = new BankPaymentRequest();
            $payment->user_id = Auth::user()->id ?? 0;
            $payment->bank_name = $request->bank_name;
            $payment->branch_name = $request->branch_name;
            $payment->account_number = $request->account_number;
            $payment->account_holder = $request->account_holder;
            $payment->account_type = $request->type;
            $payment->amount = $request->deposit_amount;


            if ($request->hasFile('image')) {

                $image = $request->file('image');

                $name = md5($request->account_number . rand(0, 10000)) . '.' . 'png';
                $upload_path = 'public/uploads/bankpayment/';
                $image->move($upload_path, $name);
                $payment->image = 'public/uploads/bankpayment/' . $name;

            }
            $payment->save();
            Toastr::success('Your request has padding. Please wait for approved', 'Success');

            return true;
        } catch (\Exception $e) {
            Toastr::error($e->getMessage(), trans('common.Failed'));
            return false;

        }
    }


    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        if (demoCheck()) {
            return redirect()->back();
        }

        try {
            $request = BankPaymentRequest::findOrFail($id);
            $request->status = 1;
            $request->save();


            $result = $this->depositWithGateWay($request->amount, $request->user_id);
            if ($result) {
                return redirect()->back();
            } else {
                return redirect()->back();
            }


        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {

        if (demoCheck()) {
            return redirect()->back();
        }
        try {
            $request = BankPaymentRequest::findOrFail($id);
            $request->delete();

            Toastr::success("Operation Success", 'Success');
            return redirect()->back();

        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }


    public static function depositWithGateWay($amount, $user_id)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        try {


            if (Auth::check()) {
                DB::beginTransaction();
                $user = User::find($user_id);
                $user->balance += $amount;
                $user->save();
                $depositRecord = new DepositRecord();
                $depositRecord->user_id = $user->id;
                $depositRecord->method = "Bank Payment";
                $depositRecord->amount = $amount;
                $depositRecord->save();


            if (UserEmailNotificationSetup('Bank_Payment',$user)) {
                 send_email($user, $type = 'Bank_Payment', $shortcodes = [
                    'amount' => $amount,
                    'currency' => Settings('currency_code'),
                    'time' => now()->format(Settings('active_date_format').' H:i:s A')
                ]);
            }
            if (UserBrowserNotificationSetup('Bank_Payment',$user)) {

                 send_browser_notification($user, $type = 'Bank_Payment', $shortcodes = [
                    'amount' => $amount,
                    'currency' => Settings('currency_code'),
                    'time' => now()->format(Settings('active_date_format').' H:i:s A')
                ],
                '',//actionText
                ''//actionUrl
                );
            }


                Toastr::success(trans('common.Operation successful'), trans('common.Success'));

                DB::commit();
                return true;

            } else {
                Toastr::error('Something Went Wrong', 'Error');
                return false;
            }


        } catch (\Exception $e) {
dd($e);
            Toastr::error('Something Went Wrong', 'Error');
            return false;

        }
    }

}
