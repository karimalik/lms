@extends('backend.master')

@section('mainContent')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="box_header common_table_header">
                <div class="main-title d-md-flex">
                    <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{__('setting.Color Theme')}}</h3>

                    <ul class="d-flex">
                        <li><a class="primary-btn radius_30px mr-10 fix-gr-bg"
                               href="{{ route('appearance.themes-customize.index') }}"><i
                                    class="ti-list"></i>{{__('setting.Color Theme List')}}</a>
                        </li>
                    </ul>

                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="white_box_50px box_shadow_white">
                {!! Form::open(['route' => 'appearance.themes-customize.store', 'files' => true]) !!}
                <div class="row form">
                    <div class="col-lg-3">
                        <div class="primary_input mb-15">
                            <label class="primary_input_label" for="title">{{__('setting.Theme Title')}} *</label>
                            <input type="text" name="title" class="primary_input_field" id="title" required
                                   maxlength="191" autofocus value="{{ old('title') }}">
                            <span class="text-danger">{{$errors->first('title')}}</span>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="primary_input">
                            <label class="primary_input_label" for="">{{__('setting.Themes')}} *</label>
                            <select class="primary_select mb-15 theme" name="theme" id="theme">
                                @foreach($themes as $theme)
                                    <option
                                        value="{{$theme->id}}">{{$theme->name}}</option>
                                @endforeach
                            </select>
                            <span class="text-danger">{{$errors->first('theme')}}</span>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="primary_input mb-15">
                            <label class="primary_input_label"
                                   for=" ">{{__('setting.Primary Color')}}</label>
                            <input type="color" name="p_color"
                                   class="primary_input_field color_field" value="{{$default->primary_color}}"
                                   required
                            >
                            <span class="text-danger">{{$errors->first('p_color')}}</span>
                        </div>
                    </div>


                    <div class="col-lg-3">
                        <div class="primary_input mb-15">
                            <label class="primary_input_label"
                                   for=" ">{{__('setting.Secondary Color')}}</label>
                            <input type="color" name="s_color"
                                   class="primary_input_field color_field" value="{{$default->secondary_color}}"
                                   required>
                            <span class="text-danger">{{$errors->first('s_color')}}</span>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <hr>
                    </div>
                    <div class="col-lg-3">
                        <div class="primary_input mb-15">
                            <label class="primary_input_label"
                                   for=" ">{{__('setting.Footer Background Color')}}</label>
                            <input type="color" name="footer_background_color" value="{{ $default->footer_background_color }}"
                                   class="primary_input_field color_field"
                                   required>
                            <span class="text-danger">{{$errors->first('footer_background_color')}}</span>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="primary_input mb-15">
                            <label class="primary_input_label"
                                   for=" ">{{__('setting.Footer Headline Color')}}</label>
                            <input type="color" name="footer_headline_color" value="{{ $default->footer_headline_color }}"
                                   class="primary_input_field color_field"
                                   required>
                            <span class="text-danger">{{$errors->first('footer_headline_color')}}</span>
                        </div>
                    </div>


                    <div class="col-lg-3">
                        <div class="primary_input mb-15">
                            <label class="primary_input_label"
                                   for=" ">{{__('setting.Footer Text Color')}}</label>
                            <input type="color" name="footer_text_color" value="{{ $default->footer_text_color }}"
                                   class="primary_input_field color_field"
                                   required>
                            <span class="text-danger">{{$errors->first('footer_text_color')}}</span>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="primary_input mb-15">
                            <label class="primary_input_label"
                                   for=" ">{{__('setting.Footer Text Hover Color')}}</label>
                            <input type="color" name="footer_text_hover_color"
                                   class="primary_input_field color_field"
                                   required>
                            <span class="text-danger">{{$errors->first('footer_text_hover_color')}}</span>
                        </div>
                    </div>
                </div>


                <div class="row form">
                    <div class="col-12">
                        <div class="primary_input mb-25">
                            <label class="primary_checkbox d-flex mr-12 w-100">
                                <input name="is_default" value="1"
                                       type="checkbox" {{ old('is_default') ? 'checked' : '' }} >
                                <span class="checkmark"></span>
                                <p class="ml-2">{{ __('setting.Make Default Theme') }}  </p>
                            </label>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="submit_btn text-center ">
                            <button class="primary-btn semi_large2 fix-gr-bg" id="reset_to_default" type="button"><i
                                    class="ti-check"></i>{{__('setting.Reset To Default')}}
                            </button>
                            <button class="primary-btn semi_large2 fix-gr-bg" type="submit"><i
                                    class="ti-check"></i>{{__('common.Save')}}
                            </button>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).on('click', '#reset_to_default', function(){
            document.location.reload(true);
        });
    </script>

@endpush
