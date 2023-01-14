<form id="formData" action="{{route('popup-content.update')}}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <input type="hidden" name="id" value="{{ $popup->id }}">
        <div class="col-xl-6">
            <div class="primary_input mb-25">
                <label class="primary_input_label" for="">{{ __('common.Title') }}  </label>
                <input name="title" class="primary_input_field" placeholder="-" type="text"
                       value="{{ old('title') ? old('title') : $popup->title }}">
            </div>
            <span class="text-danger" id="title_error"></span>
        </div>
        <div class="col-xl-6 ">
            <div class="primary_input mb-25">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="primary_input_label"
                               for="    "> {{__('frontendmanage.Show In Frontend')}}</label>
                    </div>

                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-4 mb-25">
                                <label class="primary_checkbox d-flex mr-12"
                                       for="yes">
                                    <input type="radio"
                                           class="common-radio "
                                           id="yes"
                                           name="status"
                                           {{$popup->status==1?'checked':''}}
                                           value="1">
                                    <span class="checkmark mr-2"></span> {{__('common.Yes')}}</label>
                            </div>
                            <div class="col-md-4 mb-25">
                                <label class="primary_checkbox d-flex mr-12"
                                       for="no">
                                    <input type="radio"
                                           class="common-radio "
                                           id="no"
                                           name="status"
                                           value="0" {{$popup->status==0?'checked':''}}>
                                    <span class="checkmark mr-2"></span> {{__('common.No')}}</label>
                            </div>
                        </div>


                    </div>

                </div>
            </div>
        </div>

        <div class="col-xl-12">
            <div class="primary_input mb-35">
                <label class="primary_input_label"
                       for="">{{ __('common.Details') }} </label>
                <textarea name="message" id="message"
                          class="lms_summernote">{{ $popup->message }}</textarea>
            </div>
            <span class="text-danger" id="message_error"></span>
        </div>

        <div class="col-xl-6">
            <div class="primary_input mb-25">
                <label class="primary_input_label" for="">{{ __('common.Button Text') }} </label>
                <input name="btn_txt" class="primary_input_field" placeholder="-" type="text"
                       value="{{ old('btn_txt') ? old('btn_txt') : $popup->btn_txt }}">
            </div>
            <span class="text-danger" id="btn_txt_error"></span>
        </div>

        <div class="col-xl-6">
            <div class="primary_input mb-25">
                <label class="primary_input_label" for="">{{ __('common.Link') }} </label>
                <input name="link" class="primary_input_field" placeholder="-" type="url"
                       value="{{ old('link') ? old('link') : $popup->link }}">
            </div>
            <span class="text-danger" id="btn_txt_error"></span>
        </div>

        <div class="col-xl-6">
            <div class="primary_input mb-25">
                <label class="mb-2 mr-30">{{ __('common.Image') }}<small>(327x446)px</small></label>
                <div class="primary_file_uploader">
                    <input class="primary-input" type="text" id="placeholderFileOneName"
                           placeholder="{{ __('common.Browse') }}" readonly="">
                    <button class="" type="button">
                        <label class="primary-btn small fix-gr-bg" for="document_file_1">{{__("common.Image")}} </label>
                        <input type="file" class="d-none" name="file" id="document_file_1">
                    </button>
                </div>
                @if ($popup->image)
                    <div class="img_div mt-20">
                        <img id="blogImgShow"
                             src="{{asset($popup->image)}}" alt="">
                    </div>

                @endif
            </div>
        </div>


        @if (permissionCheck('popup-content.index'))
            <div class="col-lg-12 text-center">
                <div class="d-flex justify-content-center">
                    <button class="primary-btn semi_large2  fix-gr-bg mr-1" id="save_button_parent"
                            type="submit"><i
                                class="ti-check"></i>{{ __('common.Update') }}</button>
                </div>
            </div>
        @endif
    </div>
</form>
