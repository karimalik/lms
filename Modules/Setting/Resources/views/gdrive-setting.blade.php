@extends('backend.master')

@section('mainContent')

    <section class="sms-breadcrumb mb-40 white-box">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>{{__('setting.Google Drive Configuration')}}</h1>
                <div class="bc-pages">
                    <a href="{{url('dashboard')}}">{{__('common.Dashboard')}} </a>
                    <a href="#">{{__('setting.Setting')}}</a>
                    <a href="#">{{__('setting.Google Drive Configuration')}}</a>
                </div>
            </div>
        </div>
    </section>

    <section class="mb-40 student-details">
        <div class="container-fluid p-0">
            <div class="row">

                <div class="col-lg-12">

                    @if (permissionCheck('gdrive.setting.update'))
                        <form class="form-horizontal" action="{{route('gdrive.setting.update')}}" method="POST"
                              enctype="multipart/form-data">
                            @endif
                            @csrf
                            <div class="white-box">

                                <div class="row   mb-3 pb-3">
                                    <div class="col-lg-12 text-right d-flex justify-content-end">
                                        @if(auth()->user()->role_id==1)
                                            <a class="primary-btn radius_30px mr-10 fix-gr-bg"
                                               href="{{ url('public/backend/img/google_drive.pdf') }}"
                                               download="google_drive.pdf"><i
                                                    class="ti-file"></i>{{__('setting.Documentation')}}</a>
                                        @endif
                                        @if (auth()->user()->googleToken)
                                            <div class="dropdown CRM_dropdown ml-10">
                                                <button class="btn btn-secondary dropdown-toggle" type="button"
                                                        data-toggle="dropdown"
                                                        aria-haspopup="true"
                                                        aria-expanded="false">
                                                    {{ auth()->user()->googleToken->google_email }}
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right"
                                                     aria-labelledby="dropdownMenu2">
                                                    <a href="{{ route('setting.google.login') }}"
                                                       class="dropdown-item">{{__('setting.Switch Account')}}</a>
                                                    <a href="{{ route('setting.google.logout') }}"
                                                       class="dropdown-item">{{__('common.Logout')}}</a>
                                                </div>
                                            </div>
                                        @else
                                            <a href="{{ route('setting.google.login') }}"
                                               class="primary-btn radius_30px mr-10 fix-gr-bg"><i
                                                    class="ti-google"></i> {{ trans('common.Login') }}
                                            </a>
                                        @endif
                                    </div>
                                    @if(auth()->user()->role_id==1)
                                        <input type="hidden" name="google" value="1">
                                        <div class="col-xl-12">
                                            <div class="primary_input mb-25">
                                                <label class="primary_input_label"
                                                       for="">Client ID
                                                    *</label>
                                                <input class="primary_input_field" placeholder="-" type="text"
                                                       name="gdrive_client_id"
                                                       value="{{ config('filesystems.disks.google.clientId') }}">
                                            </div>
                                        </div>

                                        <div class="col-xl-12">
                                            <div class="primary_input mb-25">
                                                <label class="primary_input_label"
                                                       for="">Client Secret
                                                    *</label>
                                                <input class="primary_input_field" placeholder="-" type="text"
                                                       name="gdrive_client_secret"
                                                       value="{{ config('filesystems.disks.google.clientSecret') }}">
                                            </div>
                                        </div>


                                        <div class="col-lg-12">
                                            <span>Callback URL: </span>
                                            <code>
                                                {{route('setting.google.callback')}}
                                            </code>
                                        </div>


                                        <div class="row mt-40">
                                            <div class="col-lg-12 text-center">
                                                <button type="submit" class="primary-btn fix-gr-bg"
                                                        data-toggle="tooltip">
                                                    <span class="ti-check"></span>
                                                    {{__('common.Update')}}
                                                </button>
                                            </div>
                                        </div>
                                    @else
                                        <table class="">


                                            <tbody>
                                            <tr>
                                                <th>{{__('common.Status')}} :</th>
                                                <td>
                                                    @if (auth()->user()->googleToken)
                                                        {{trans('common.Connected')}}
                                                    @else
                                                        {{trans('setting.Google Drive login is required')}}
                                                    @endif
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    @endif
                                </div>

                            </div>
                        </form>
                </div>
            </div>
        </div>
    </section>

@endsection
