@extends('backend.master')
@section('mainContent')
    <link rel="stylesheet" href="{{asset('Modules/ModuleManager/Resources/assets/sass/manage_module.css')}}">
    <link rel="stylesheet" href="{{ asset('public/vendor/spondonit/css/parsley.css') }}">
    <section class="sms-breadcrumb mb-40 white-box">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>{{__('setting.Module')}} {{__('setting.Manage')}}</h1>
                <div class="bc-pages">
                    <a href="{{route('dashboard')}}">{{__('dashboard.Dashboard')}}</a>
                    <a href="#"> {{__('setting.System Setting')}}</a>
                    <a href="#">{{__('setting.Module')}} {{__('setting.Manage')}}</a>
                </div>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-9 col-xs-6 col-md-6 col-6 no-gutters ">
                            <div class="main-title sm_mb_20 sm2_mb_20 md_mb_20 mb-30 ">
                                <h3 class="mb-0"> {{__('setting.Module')}} {{__('setting.Manage')}}</h3>
                            </div>
                        </div>

                        <div class="col-lg-3 col-xs-6 col-md-6 col-6 no-gutters text-right ">
                            <a data-toggle="modal"
                               data-target="#add_module" href="#"
                               class="primary-btn fix-gr-bg small pull-right">  {{__('common.Add')}}
                                / {{__('common.Update')}} {{__('setting.Module')}}</a>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">

                            <table class="display school-table school-table-style" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th>{{__('setting.SL')}}</th>
                                    <th>{{__('setting.Module')}}{{__('setting.Name')}}</th>
                                    <th>{{__('setting.Description')}}</th>
                                    <th class="text-right"></th>
                                </tr>
                                </thead>

                                <tbody>
                                <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">

                                @foreach($modules as $key=>$module)

                                    @php
                                        $is_module_available = base_path().'/'.'Modules/' . $module->name. '/Providers/' .$module->name. 'ServiceProvider.php';
                                        $configFile = 'Modules/' . $module->name. '/' .$module->name. '.json';

                                    try {
                                        $config =file_get_contents(base_path().'/'.$configFile);

                                    }catch (\Exception $exception){
                                        $config =null;
                                    }


                                    @endphp
                                    <tr>
                                        <td>{{++$key}}</td>
                                        <td>
                                            @if ($module->name=='LmsSaas')
                                                LMS-SAAS
                                            @else
                                                {{@$module->name}}
                                            @endif

                                            @if(!empty($module->verify->purchase_code)) <p class="text-success">
                                                {{__('setting.Verified')}}
                                                on {{date("F jS, Y", strtotime(@$module->verify->activated_date))}}</p>
                                            <a href="#" class="module_switch" data-id="{{@$module->name}}"
                                               id="module_switch_label{{@$module->name}}"
                                               data-item="{{$module}}">
                                                {{isModuleActive($module->name )  == false? 'Activate':'Deactivate'}}


                                            </a>

                                            @includeIf('service::license.revoke-module', ['name' =>$module->name, 'row' => true, 'file' =>false])

                                            <div id="waiting_loader"
                                                 class="waiting_loader{{@$module->name}}">
                                                <img
                                                        src="{{asset('public/backend/img/demo_wait.gif')}}"
                                                        width="20" height="20"/><br>

                                            </div>

                                            @else<p class="text-danger">
                                                @if (! file_exists($is_module_available))
                                                @else

                                                    <a class=" verifyBtn"
                                                       data-toggle="modal" data-id="{{@$module->name}}"
                                                       data-target="#Verify"
                                                       href="#">   {{__('setting.Verify')}}</a>
                                                @endif
                                                @endif
                                            </p>
                                        </td>
                                        <td>
                                            @if(isset($config))
                                                @php

                                                    $name=$module->name;
                                                    $config= json_decode($config);
                                                    if (isset($config->$name->notes[0])){
                                                    echo $config->$name->notes[0];
                                                    echo '<br>';
                                                    echo 'Version: '.$config->$name->versions[0].' | Developed By <a href="https://spondonit.com/">SpondonIT</a>';

                                                    }
                                                @endphp
                                            @else
                                                @php
                                                    if (isset($module->details)){
    echo $module->details;
}
                                                @endphp
                                            @endif
                                        </td>

                                        <td class="text-right">
                                            @if (! file_exists($is_module_available))
                                                <div class="row">
                                                    <div class="col-lg-12 ">
                                                        <a class="primary-btn fix-gr-bg" style="white-space: nowrap;"
                                                           href="mailto:support@spondonit.com">   {{__('common.Buy Now')}}</a>

                                                    </div>
                                                </div>
                                            @endif
                                            @if (file_exists($is_module_available))
                                                @php
                                                    $is_moduleV= $module->verify;
                                                    $configName = $module->name;
                                                    $availableConfig=Settings($configName);


                                                @endphp

                                                @if(@$availableConfig==0 || @@$is_moduleV->purchase_code== null)
                                                    <input type="hidden" name="name" value="{{@$configName}}">


                                                @else
                                                    <div class="row">
                                                        <div class="col-lg-12 ">
                                                            @if('RolePermission' != $module->name && 'TemplateSettings' != $module->name )
                                                                <div id="waiting_loader"
                                                                     class="waiting_loader{{@$module->name}}">


                                                                </div>
                                                            @endif

                                                        </div>
                                                @endif
                                            @endif

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
    </section>

    <div class="modal fade admin-query" id="add_module">
        <div class="modal-dialog  modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add New / Update Module</h4>
                    <button type="button" class="close " data-dismiss="modal">
                        <i class="ti-close "></i>
                    </button>
                </div>

                <div class="modal-body">
                    <form action="{{route('modulemanager.uploadModule')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">

                            <div class="col-xl-12">
                                <div class="primary_input mb-35">

                                    <div class="primary_file_uploader">
                                        <input class="primary-input filePlaceholder" type="text"
                                               id=""
                                               placeholder="Select Module File"
                                               readonly="">
                                        <button class="" type="button">
                                            <label class="primary-btn small fix-gr-bg"
                                                   for="document_file">{{__('common.Browse')}}</label>
                                            <input type="file" class="d-none fileUpload" name="module"
                                                   id="document_file">
                                        </button>
                                    </div>
                                </div>
                            </div>


                        </div>

                        <div class="col-lg-12 text-center pt_15">
                            <div class="d-flex justify-content-center">
                                <button class="primary-btn semi_large2  fix-gr-bg" id="save_button_parent"
                                        type="submit"><i
                                            class="ti-check"></i> {{__('common.Save')}}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade admin-query" id="Verify">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Module Verification</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;
                    </button>
                </div>

                <div class="modal-body">
                    {{ Form::open(['id'=>"content_form",'class' => 'form-horizontal', 'files' => true, 'route' => 'ManageAddOnsValidation', 'method' => 'POST']) }}
                    <input type="hidden" name="name" value="" id="moduleName">
                    <input type="hidden" name="row" value="1">
                    @csrf
                    <div class="form-group">
                        <label for="user">Envato Email Address :</label>
                        <input type="text" class="form-control " name="envatouser"
                               required="required"
                               placeholder="Enter Your Envato Email Address"
                               value="{{old('envatouser')}}">
                    </div>
                    <div class="form-group">
                        <label for="purchasecode">Envato Purchase Code:</label>
                        <input type="text" class="form-control" name="purchase_code"
                               required="required"
                               placeholder="Enter Your Envato Purchase Code"
                               value="{{old('purchasecode')}}">
                    </div>
                    <div class="form-group">
                        <label for="domain">Installation Path:</label>
                        <input type="text" class="form-control"
                               name="installationdomain" required="required"
                               placeholder="Enter Your Installation Domain"
                               value="{{url('/')}}" readonly>
                    </div>
                    <div class="row mt-40">
                        <div class="col-lg-12 text-center">
                            <button class="primary-btn fix-gr-bg submit">
                                <span class="ti-check"></span>
                                {{__('setting.Verify')}}
                            </button>
                            <button type="button" class="primary-btn fix-gr-bg submitting" style="display: none">
                                <i class="fas fa-spinner fa-pulse"></i>
                                Verifying
                            </button>
                        </div>
                    </div>

                    {{ Form::close() }}
                </div>

            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{asset('public/backend/js/module.js')}}"></script>
    <script type="text/javascript" src="{{ asset('public/vendor/spondonit/js/parsley.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('public/vendor/spondonit/js/function.js') }}"></script>
    <script type="text/javascript" src="{{ asset('public/vendor/spondonit/js/common.js') }}"></script>
    <script type="text/javascript">
        _formValidation('content_form');
    </script>
@endpush
