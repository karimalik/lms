@extends('backend.master')
@push('styles')

@endpush
@section('mainContent')
    <section class="sms-breadcrumb mb-40 white-box">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1> {{__('certificate.Certificate Fonts')}}</h1>
                <div class="bc-pages">
                    <a href="{{route('dashboard')}}">{{__('dashboard.Dashboard')}}</a>
                    <a href="#"> {{__('certificate.Certificate Fonts')}}</a>
                </div>
            </div>
        </div>
    </section>


    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">

            <div class="row justify-content-center mt-50">

                <div class="col-12">
                    <div class="box_header common_table_header">
                        <div class="main-title d-md-flex">
                            <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px"> {{__('certificate.Font List')}}</h3>
                            @if (permissionCheck('certificate.fonts.save'))
                                <ul class="d-flex">
                                    <li><a class="primary-btn radius_30px mr-10 fix-gr-bg" data-toggle="modal"
                                           data-target="#add_blog" href="#"><i
                                                    class="ti-plus"></i>{{__('common.Add')}} {{__('certificate.Font')}}</a>
                                    </li>
                                </ul>
                            @endif
                        </div>

                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="QA_section QA_section_heading_custom check_box_table">
                        <div class="QA_table ">
                            <!-- table-responsive -->
                            <div class="">
                                <table id="lms_table" class="table Crm_table_active3">
                                    <thead>
                                    <tr>

                                        <th scope="col"> {{__('certificate.SL')}}</th>
                                        <th scope="col"> {{__('certificate.Title')}}</th>
                                        <th scope="col"> {{__('certificate.Name')}}</th>
                                        <th scope="col"> {{__('common.Type')}}</th>
                                        <th scope="col">{{__('common.Action')}}</th>

                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($fonts as $key => $font)
                                        <tr>
                                            <td class=""><span class="m-2">{{++$key}}</span></td>
                                            <td>{{@$font->title}}</td>
                                            <td>{{@$font->name}}</td>
                                            <td>{{@$font->rlt==1?'RTL':'LTL'}}</td>


                                            <td>
                                                <div class="dropdown CRM_dropdown">
                                                    <button class="btn btn-secondary dropdown-toggle" type="button"
                                                            id="dropdownMenu2" data-toggle="dropdown"
                                                            aria-haspopup="true"
                                                            aria-expanded="false">
                                                        {{__('common.Action')}}
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right"
                                                         aria-labelledby="dropdownMenu2">

                                                        @if (permissionCheck('certificate.fonts.delete'))
                                                            <button data-id="{{$font->id}}"
                                                                    class="deleteFont dropdown-item"
                                                                    type="button">{{__('common.Delete')}}</button>

                                                        @endif
                                                    </div>
                                                </div>

                                            </td>


                                        </tr>


                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade admin-query" id="add_blog">
                    <div class="modal-dialog modal_1000px modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">{{__('common.Add New')}} {{__('certificate.Font')}}</h4>
                                <button type="button" class="close " data-dismiss="modal">
                                    <i class="ti-close "></i>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="{{route('certificate.fonts.save')}}" method="POST"
                                      enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">

                                        <div class="col-xl-12">
                                            <div class="primary_input mb-25">
                                                <label class="primary_input_label"
                                                       for="">  {{__('certificate.Title')}}
                                                    <strong
                                                            class="text-danger">*</strong>
                                                </label>
                                                <input class="primary_input_field addTitle" name="title" placeholder="-"
                                                       type="text"
                                                       value="{{old('title')}}" required>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="row">

                                        <div class="col-xl-12">
                                            <div class="primary_input mb-25">
                                                <label class="primary_input_label"
                                                       for="">  {{__('certificate.Name')}}
                                                    <strong
                                                            class="text-danger">*</strong>
                                                </label>
                                                <input class="primary_input_field addTitle" name="name" placeholder="-"
                                                       type="text"
                                                       value="{{old('name')}}" required>
                                            </div>
                                        </div>

                                        <div class="col-xl-12 ">
                                            <div class="primary_input  ">
                                                <div class="row">

                                                    <div class="col-md-1 mb-25">
                                                        <label class="primary_checkbox d-flex mr-12"
                                                               for="type1">
                                                            <input type="radio"
                                                                   class="common-radio type1"
                                                                   id="type1"
                                                                   name="rtl" checked
                                                                   value="0">
                                                            <span class="checkmark mr-2"></span>  LTL</label>
                                                    </div>
                                                    <div class="col-md-1  mb-25">
                                                        <label class="primary_checkbox d-flex mr-12"
                                                               for="type2">
                                                            <input type="radio"
                                                                   class="common-radio type2"
                                                                   id="type2"
                                                                   name="rtl"
                                                                   value="1">
                                                            <span class="checkmark mr-2"></span> RTL</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row ">
                                        <div class="col-xl-12">
                                            <div class="primary_input mb-35">
                                                <label class="primary_input_label"
                                                       for="">{{__('certificate.Font') }}
                                                    <strong
                                                            class="text-danger">*</strong>
                                                </label>
                                                <div class="primary_file_uploader">
                                                    <input class="primary-input filePlaceholder" type="text"
                                                           id=""
                                                           placeholder="{{__('certificate.Browse TTF File')}}"
                                                           readonly="">
                                                    <button class="" type="button">
                                                        <label class="primary-btn small fix-gr-bg"
                                                               for="document_file_2">{{__('common.Browse') }}</label>
                                                        <input type="file" class="d-none fileUpload" name="font"
                                                               id="document_file_2">
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-lg-12 text-center pt_15">
                                        <div class="d-flex justify-content-center">
                                            <button class="primary-btn semi_large2  fix-gr-bg" id="save_button_parent"
                                                    type="submit"><i
                                                        class="ti-check"></i> {{__('common.Add') }} {{__('certificate.Font') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>



                <div class="modal fade admin-query" id="deleteFont">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <form action="{{route('certificate.fonts.delete')}}"
                                  method="post">
                                @csrf

                                <div class="modal-header">
                                    <h4 class="modal-title">{{__('common.Delete')}} {{__('certificate.Font')}} </h4>
                                    <button type="button" class="close" data-dismiss="modal"><i
                                                class="ti-close "></i></button>
                                </div>

                                <div class="modal-body">
                                    <div class="text-center">

                                        <h4>{{__('common.Are you sure to delete ?')}} </h4>
                                    </div>

                                    <div class="mt-40 d-flex justify-content-between">

                                        <input type="hidden" name="id" value="" id="fontDeleteId">
                                        <button type="button" class="primary-btn tr-bg"
                                                data-dismiss="modal">{{__('common.Cancel')}}</button>
                                        <button class="primary-btn fix-gr-bg"
                                                type="submit">{{__('common.Delete')}}</button>


                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>

        $(document).on('click', '.deleteFont', function () {
            let id = $(this).data('id');
            $('#fontDeleteId').val(id);
            $("#deleteFont").modal('show');
        })
    </script>
@endpush
