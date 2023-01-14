<?php

namespace Modules\OfflinePayment\Http\Controllers;

use App\User;
use App\DepositRecord;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Support\Renderable;
use Modules\OfflinePayment\Entities\OfflinePayment;

class OfflinePaymentController extends Controller
{

    public function offlinePaymentView()
    {
        $instructor = User::where('role_id', 2)->get();
        $student = User::where('role_id', 3)->get();
        return view('offlinepayment::fund.add_fund', compact('student', 'instructor'));
    }

    public function FundHistory($id)
    {

        try {
            $user = User::with('currency')->where('id', $id)->first();
            $payments = OfflinePayment::where('user_id', $id)->with('user.role')->get();
            return view('offlinepayment::fund.funding_history', compact('payments', 'user'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function addBalance(Request $request)
    {

        $request->validate([
            'user_id' => 'required',
            'amount' => 'required',
        ]);

        try {

            $user = User::where('id', $request->user_id)->first();
            $tran = new OfflinePayment();
            $new = $user->balance + $request->amount;
            $tran->user_id = $user->id;
            $tran->role_id = $user->role_id;
            $tran->amount = $request->amount;
            $tran->status = 1;
            $tran->after_bal = $new;
            $tran->save();
            $user->balance = $new;
            $user->save();

            $depositRecord = new DepositRecord();
            $depositRecord->user_id = $user->id;
            $depositRecord->method = 'Offline Payment';
            $depositRecord->amount = $request->amount;
            $depositRecord->save();
            if ($user->role_id == 3) {
                $isStudent = true;
            } else {
                $isStudent = false;
            }

            if (UserEmailNotificationSetup('OffLine_Payment', $user)) {
                send_email($user, $type = 'OffLine_Payment', $shortcodes = [
                    'amount' => $request->amount,
                    'currency' => Settings('currency_code'),
                    'time' => now()->format(Settings('active_date_format') . ' H:i:s A'),
                ]);
            }
            if (UserBrowserNotificationSetup('OffLine_Payment', $user)) {

                send_browser_notification($user, $type = 'OffLine_Payment', $shortcodes = [
                    'amount' => $request->amount,
                    'currency' => Settings('currency_code'),
                    'time' => now()->format(Settings('active_date_format') . ' H:i:s A'),
                ],
                    '',//actionText
                    ''//actionUrl
                );
            }
            Toastr::success(trans('common.Fund Added'), trans('common.Success'));
            return back()->with('isStudent', $isStudent);
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }

    }

    public function deductBalance(Request $request)
    {

        $request->validate([
            'user_id' => 'required',
            'amount' => 'required',
        ]);

        try {

            $user = User::where('id', $request->user_id)->first();
            if ($user->role_id == 3) {
                $isStudent = true;
            } else {
                $isStudent = false;
            }

            if ($user->balance < $request->amount) {
                Toastr::error(trans('common.Insufficient balance'), trans('common.Error'));
                return redirect()->back();
            }

            $tran = new OfflinePayment();
            $new = $user->balance - $request->amount;
            $tran->user_id = $user->id;
            $tran->role_id = $user->role_id;
            $tran->amount = $request->amount;
            $tran->status = 1;
            $tran->after_bal = $new;
            $tran->type = 'Deduct';
            $tran->save();
            $user->balance = $new;
            $user->save();

            $depositRecord = new DepositRecord();
            $depositRecord->user_id = $user->id;
            $depositRecord->method = 'Offline Payment';
            $depositRecord->amount = -abs($request->amount);
            $depositRecord->save();

            if (UserEmailNotificationSetup('Deduct_Payment', $user)) {
                send_email($user, $type = 'Deduct_Payment', $shortcodes = [
                    'amount' => getPriceFormat($request->amount),
                    'time' => now()->format(Settings('active_date_format') . ' H:i:s A'),
                ]);
            }
            if (UserBrowserNotificationSetup('Deduct_Payment', $user)) {

                send_browser_notification($user, $type = 'Deduct_Payment', $shortcodes = [
                    'amount' => getPriceFormat($request->amount),
                    'time' => now()->format(Settings('active_date_format') . ' H:i:s A'),
                ],
                    '',//actionText
                    ''//actionUrl
                );
            }
            Toastr::success(trans('payment.Deduct') . ' ' . trans('payment.Fund'), trans('common.Success'));
            return back()->with('isStudent', $isStudent);
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }

    }


}
