@extends('backend.master')

@push('styles')
    <link rel="stylesheet" href="{{asset('public/backend/css/theme.css')}}"/>
@endpush
@section('mainContent')
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="box_header">
                        <div class="main-title d-flex justify-content-between">
                            <h3 class="mb-0 mr-30">{{ __('setting.Themes') }}</h3>


                            <ul class="d-flex">
                                <li><a class="primary-btn radius_30px mr-10 fix-gr-bg text-white"
                                       href="{{url('/appearance/themes/create')}}"><i
                                            class="ti-plus"></i>{{__('common.Add New/Update')}}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="row">

                        <div style="position: relative;" class="col-md-4 item_section">
                            <div style="border:2px solid;" class="card">

                                <div style="padding: 0;" class="card-body screenshot">
                                    <div class="single_item_img_div">
                                        <img style="width: 100%" src="{{ asset($activeTheme->image) }}" alt="">
                                    </div>

                                </div>
                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col-5">
                                            <h4>{{$activeTheme->name}}</h4>
                                        </div>
                                        @if($activeTheme->is_active !=1 )
                                            <div class="col-7 footer_div">
                                                <div class="row btn_div">
                                                    <div class="col-md-5 col-sm-12">
                                                        <form action="{{route('appearance.themes.active')}}"
                                                              method="POST">
                                                            @csrf
                                                            <input type="hidden" name="id" value="{{$activeTheme->id}}">
                                                            <button type="submit"
                                                                    class="primary-btn radius_30px mr-10   fix-gr-bg text-white pl-3 pr-3">{{__('common.active')}}</button>
                                                        </form>

                                                    </div>
                                                    <div style="padding-left: 0;" class="col-md-7 col-sm-12">
                                                        <a class="primary-btn radius_30px mr-10   fix-gr-bg text-white pl-3 pr-3"
                                                           target="_blank"
                                                           href="{{$activeTheme->live_link}}">{{__('setting.live preview')}}</a>
                                                    </div>
                                                </div>

                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="text-center detail_btn">
                                    <h4>
                                        <a href="{{route('appearance.themes.show',$activeTheme->id)}}">{{__('setting.Theme Details')}}</a>
                                    </h4>
                                </div>

                            </div>


                        </div>

                        @foreach($ThemeList as $key => $item)
                            <div style="position: relative;" class="col-md-4 item_section">
                                <div style="" class="card">

                                    <div style="padding: 0;" class="card-body screenshot">
                                        <div class="single_item_img_div">
                                            <img style="width: 100%" src="{{ asset($item->image) }}" alt="">
                                        </div>

                                    </div>
                                    <div class="card-footer">
                                        <div class="row">
                                            <div class="col-5">
                                                <h4>{{$item->name}}</h4>
                                            </div>
                                            {{-- @if($item->is_active !=1 ) --}}
                                            <div class="col-7 footer_div">
                                                <div class="row ">
                                                    <div class="col-md-5 col-sm-12 text-center">
                                                        @if(!empty($item->purchase_code) || $item->name=='infixlmstheme' || empty($item->item_code))
                                                            <form action="{{route('appearance.themes.active')}}"
                                                                  method="POST">
                                                                @csrf
                                                                <input type="hidden" name="id" value="{{$item->id}}">
                                                                <button type="submit"
                                                                        class="primary-btn radius_30px mr-10   fix-gr-bg text-white pl-3 pr-3">
                                                                    {{__('common.Active')}}
                                                                </button>
                                                            </form>
                                                            @if(!empty($item->item_code))
                                                                @includeIf('service::license.revoke-theme', ['name' =>$item->name])
                                                            @endif
                                                        @else
                                                            <a class=" verifyBtn primary-btn radius_30px mr-10   fix-gr-bg text-white pl-3 pr-3"
                                                               data-toggle="modal" data-id="{{@$item->name}}"
                                                               data-target="#Verify"
                                                               href="#">   {{__('setting.Verify')}}</a>
                                                        @endif

                                                    </div>
                                                    <div style="padding-left: 0;" class="col-md-7 col-sm-12">
                                                        <a class="primary-btn radius_30px mr-10   fix-gr-bg text-white pl-3 pr-3"
                                                           target="_blank" href="{{$item->live_link}}">{{__('setting.Live Preview')}}</a>
                                                    </div>
                                                </div>

                                            </div>

                                        </div>
                                    </div>

                                    <div class="text-center detail_btn">
                                        <h4>
                                            <a href="{{route('appearance.themes.show',$item->id)}}">{{__('setting.Theme Details')}}</a>
                                        </h4>
                                    </div>

                                </div>

                            </div>
                        @endforeach


                        <div class="col-md-4">
                            <a href="{{url('/appearance/themes/create')}}">
                                <div id="add_new">
                                    <span id="plus"><i class="fas fa-plus"></i></span>
                                </div>
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade admin-query" id="Verify">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Module Verification</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;
                    </button>
                </div>

                <div class="modal-body">
                    {{ Form::open(['id'=>"content_form",'class' => 'form-horizontal', 'files' => true, 'route' => 'service.theme.install', 'method' => 'POST']) }}
                    <input type="hidden" name="name" value="" id="moduleName">
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
