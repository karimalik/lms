@extends('backend.master')
@php
    $table_name='categories';
@endphp
@section('table'){{$table_name}}@endsection

    <style>
    .unread_notification{
        font-weight: bold;
    }
    .notifi_par{
        font-weight: bold;
    }
    .notify_normal{
        color: var(--system_secendory_color);
    }
</style>
@section('mainContent')
    @include("backend.partials.alertMessage")

    <section class="sms-breadcrumb mb-40 white-box">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>{{ __('setting.Notification') }}</h1>
                <div class="bc-pages">
                    <a href="{{route('dashboard')}}">{{__('common.Dashboard')}}</a>
                    <a class="active" href="{{route('notification_setup_list')}}">{{ __('setting.Notification') }}</a>
                </div>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-lg-12">
                    <div class="box_header common_table_header">
                        <div class="main-title d-md-flex mb-0">
                            <h3 class="mb-0">{{ __('setting.Notification') }} {{ __('common.List') }}</h3>
                        </div>
                    </div>
                    <div class="  QA_section QA_section_heading_custom check_box_table">
                        <div class="QA_table ">
                            <!-- table-responsive -->
                        
                            
                            <div class="">
                                <table  class="table Crm_table_active3">
                                    <thead>
                                    <tr>
                                        <th>{{__('frontendmanage.Notification')}}</th>
                                        <th>{{__('common.Date')}}</th>
                                        <th>{{__('common.Action')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                       @foreach(Auth::user()->notifications as $notification)
                                            <tr>
                                                <td>
                                                    @if ($notification->read_at==null)
                                                    <a href="#" class="unread_notification" id="{{$notification->id}}" title="Mark As Read" data-notification_id="{{$notification->id}}">
                                                         <h4 class="notifi_par notify_{{$notification->id}}">
                                                                 {{@$notification->data['title']}}
                                                         </h4> 
                                                         <p class="notifi_par notify_{{$notification->id}}">
                                                                {!! @$notification->data['body']!!}
                                                         </p>
                                                    </a>
                                                    @else
                                                    <b >{{@$notification->data['title']}}</b> 
                                                   <p>{!! @$notification->data['body']!!}</p>
                                                    @endif
                                                  

                                                </td>
                                                <td>
                                                   {{showDate($notification->created_at)}}
                                                </td>
                                                <td>
                                                   <a target="_blank" href="{{@$notification->data['actionURL']}}">{{@$notification->data['actionText']}}</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <input type="hidden" name="status_route" class="status_route" value="{{ route('course.category.status_update') }}">
    @include('backend.partials.delete_modal')
@endsection
@push('scripts')
    <script src="{{asset('public/backend/js/category.js')}}"></script>
@endpush
