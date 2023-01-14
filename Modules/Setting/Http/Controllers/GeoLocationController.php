<?php

namespace Modules\Setting\Http\Controllers;

use App\UserLogin;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;

class GeoLocationController extends Controller
{

    public function index()
    {
        return view('setting::geoLocation');
    }


    public function destroy(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        $request->validate([
            'id' => 'required'
        ]);

        try {

            $block = UserLogin::findOrFail($request->id);
            $block->delete();

            Toastr::success(trans('common.Operation successful'), trans('common.Success'));


            return redirect()->back();

        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function data(Request $request)
    {


        $query = UserLogin::latest()->with('user');

        return Datatables::of($query)
            ->addIndexColumn()
            ->editColumn('created_at', function ($query) {
                return $query->created_at->diffForHumans();
            })
            ->addColumn('action', function ($query) {


                return '       <div class="dropdown CRM_dropdown">
                                                <button class="btn btn-secondary dropdown-toggle" type="button"
                                                        id="dropdownMenu2" data-toggle="dropdown"
                                                        aria-haspopup="true"
                                                        aria-expanded="false">
                                                  ' . trans('common.Action') . '
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right"
                                                     aria-labelledby="dropdownMenu2">
                                                    <a class="dropdown-item geoLocation"
                                                       data-id="'.$query->id.'"
                                                       type="button"
                                                       type="button">  ' . trans('common.Delete') . ' </a>
                                                </div>
                                            </div>';


            })->rawColumns(['action'])
            ->make(true);
    }

    public function EmptyLog(){
        try {
            $success = trans('lang.Deleted') . ' ' . trans('lang.Successfully');

            UserLogin::truncate();

            Toastr::success($success, 'Success');
            return redirect()->back();

        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());

        }
    }
}
