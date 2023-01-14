@extends('backend.master')
@section('mainContent')




    <section class="sms-breadcrumb mb-40 white-box">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>{{__('quiz.Question')}} {{ __('quiz.Bulk Import') }}</h1>
                <div class="bc-pages">
                    <a href="{{url('dashboard')}}">{{__('common.Dashboard')}} </a>
                    <a href="#">{{__('quiz.Quiz')}}</a>
                    <a href="#">{{ __('quiz.Bulk Import') }}</a>
                </div>
            </div>
        </div>
    </section>
    <div class="row">
        <div class="col-md-12">
            <div class="box_header">
                <div class="main-title d-flex justify-content-between w-100">
                    <h3 class="mb-0 mr-30">  {{ __('quiz.Bulk Import') }}</h3>

                    <div class="">
                        <a href="{{route('download-sample')}}" class="primary-btn small fix-gr-bg float-right ml-2">
                            <span class="ti-plus pr-2"></span>
                            {{__('quiz.Sample Download')}}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <section class="mb-40 student-details">
        <div class="container-fluid p-0">
            <div class="row">

                <div class="col-lg-12">


                    <form class="form-horizontal" action="{{route('question-bank-bulk-submit')}}" method="POST"
                          enctype="multipart/form-data">

                        @csrf
                        <div class="white-box">

                            <div class="col-md-12 p-0">

                                <div class="row mb-30">
                                    <div class="col-md-12">

                                        <div class="row">
                                            <div class="col-xl-12">
                                                <p>
                                                    <pl>
                                                        <li>01. You need to import Excel File. For sample you can
                                                            download by clicking <b>Sample Download</b></li>
                                                        <li>02. Make sure input correct answer in right column.</li>
                                                        <li>03. Use Type <b>M</b>= Multiple Choice; <b>S</b>=Short
                                                            Answer; <b>L</b>=Long Answer
                                                        </li>
                                                    </pl>
                                                </p>
                                                <hr>
                                            </div>

                                            <div class="col-xl-3">

                                                <label class="primary_input_label"
                                                       for="groupInput">{{__('quiz.Group')}} *</label>

                                                <select {{ $errors->has('group') ? ' autofocus' : '' }}
                                                        class="primary_select{{ $errors->has('group') ? ' is-invalid' : '' }}"
                                                        name="group" id="groupInput">
                                                    <option
                                                        data-display="{{__('common.Select')}} {{__('quiz.Group')}} *"
                                                        value="">{{__('common.Select')}} {{__('quiz.Group')}}
                                                    </option>
                                                    @foreach($groups as $group)
                                                        @if(isset($bank))
                                                            <option
                                                                value="{{$group->id}}" {{$group->id == $bank->q_group_id? 'selected': ''}}>{{$group->title}}</option>
                                                        @else
                                                            <option
                                                                value="{{$group->id}}" {{old('group')!=''? (old('group') == $group->id? 'selected':''):''}} >{{$group->title}}</option>
                                                        @endif

                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-xl-3">
                                                <label class="primary_input_label"
                                                       for="category_id">{{__('quiz.Category')}} *</label>
                                                <select {{ $errors->has('category') ? ' autofocus' : '' }}
                                                        class="primary_select {{ $errors->has('category') ? ' is-invalid' : '' }}"
                                                        id="category_id" name="category">
                                                    <option data-display=" {{__('quiz.Category')}} *"
                                                            value=""> {{__('quiz.Category')}}
                                                    </option>
                                                    @foreach($categories as $category)
                                                        @if(isset($bank))
                                                            <option
                                                                value="{{$category->id}}" {{$bank->category_id == $category->id? 'selected': ''}}>{{$category->name}}</option>
                                                        @else
                                                            <option
                                                                value="{{$category->id}}" {{old('category')!=''? (old('category') == $category->id? 'selected':''):''}}>{{$category->name}}</option>
                                                        @endif

                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-xl-3">
                                                <div class="col-lg-12 mt-30-md" id="subCategoryDiv">
                                                    <label class="primary_input_label"
                                                           for="subcategory_id">{{__('quiz.Sub Category')}}</label>
                                                    <select {{ $errors->has('sub_category') ? ' autofocus' : '' }}
                                                            class="primary_select{{ $errors->has('sub_category') ? ' is-invalid' : '' }} select_section"
                                                            id="subcategory_id" name="sub_category">
                                                        <option
                                                            data-display=" {{__('common.Select')}} {{__('quiz.Sub Category')}}"
                                                            value="">{{__('common.Select')}} {{__('quiz.Sub Category')}}
                                                        </option>

                                                        @if(isset($bank))
                                                            <option value="{{@$bank->subcategory_id}}"
                                                                    selected>{{@$bank->subCategory->name}}</option>
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-xl-3">

                                                <label class="primary_input_label"
                                                       for="groupInput">{{__('quiz.Excel File')}} *</label>
                                                <div class="primary_input mb-35">
                                                    <div class="primary_file_uploader">

                                                        <label for="placeholderFileOneName" class="d-none"></label>
                                                        <input class="primary-input" type="text"
                                                               id="placeholderFileOneName"
                                                               placeholder="{{ __('quiz.Browse Excel File') }}"
                                                               readonly="">
                                                        <button class="" type="button">
                                                            <label class="primary-btn small fix-gr-bg"
                                                                   for="document_file_1">{{ __('common.Browse') }}</label>
                                                            <input type="file" class="d-none" name="excel_file"
                                                                   id="document_file_1"
                                                                   onchange="nameChange(this.value)">
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="col-md-7">
                                        <div class="row justify-content-center">

                                            @if(session()->has('message-success'))
                                                <p class=" text-success">
                                                    {{ session()->get('message-success') }}
                                                </p>
                                            @elseif(session()->has('message-danger'))
                                                <p class=" text-danger">
                                                    {{ session()->get('message-danger') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-40">
                                <div class="col-lg-12 text-center">
                                    <button id="submitBtn" type="submit" disabled
                                            class="btn primary_btn_2">{{ __('quiz.Bulk Import') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

@endsection
@push('scripts')
    <script src="{{asset('Modules/Appearance/Resources/assets/js/script.js')}}"></script>
    <script src="{{asset('Modules/Quiz/Resources/assets/js/quiz.js')}}"></script>
@endpush
