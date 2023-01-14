<div class="main-title mb-25">
    <div class="main-title mb-25">
        <h3 class="mb-0">{{ __('setting.General') }}</h3>
    </div>
    @if (permissionCheck('settings.general_setting_update'))
        <form action="" id="form_data_id" method="POST" enctype="multipart/form-data">
            @endif
            @csrf
            <div class="General_system_wrap_area">
                <div class="single_system_wrap">
                    <div class="single_system_wrap_inner text-center">
                        <div class="logo">
                            <span>{{ __('setting.Header Logo') }} <small>(SVG is not support for PWA) </small></span>
                        </div>
                        <div class="logo_img">
                            <img class="imagePreview1" src="{{getCourseImage(Settings('logo'))}}"
                                 style="width: 200px;max-width: 100%; height: auto;" alt="">

                        </div>
                        <div class="update_logo_btn">
                            <button class="primary-btn small fix-gr-bg" type="button">
                                <input class="imgInput1" placeholder="Upload Header Logo" type="file" name="site_logo"
                                       id="site_logo">
                                {{ __('setting.Upload Header Logo') }}
                            </button>
                        </div>
                        <a href="#" class="remove_logo">{{ __('setting.Remove') }}</a>
                    </div>
                    <div class="single_system_wrap_inner text-center">
                        <div class="logo">
                            <span>{{ __('setting.Footer Logo') }}</span>
                        </div>
                        <div class="logo_img">
                            <img class="imagePreview2" src="{{getCourseImage(Settings('logo2'))}}"
                                 style="width: 200px;max-width: 100% ;height: auto;" alt="">

                            <br>
                        </div>
                        <div class="update_logo_btn">
                            <button class="primary-btn small fix-gr-bg" type="button">
                                <input class="imgInput2" placeholder="Upload Footer Logo" type="file" name="site_logo2"
                                       id="site_logo2">
                                {{ __('setting.Upload Footer Logo') }}
                            </button>
                        </div>
                        <a href="#" class="remove_logo">{{ __('setting.Remove') }}</a>
                    </div>
                    <div class="single_system_wrap_inner text-center">
                        <div class="logo">
                            <span>{{ __('setting.Fav Icon') }}</span>
                        </div>
                        <div class="logo_img">
                            <img class="imagePreview3" src="{{getCourseImage(Settings('favicon'))}}" alt=""
                                 style="max-width: 100%">
                        </div>
                        <div class="update_logo_btn">
                            <button class="primary-btn small fix-gr-bg" type="button">
                                <input class="imgInput3" placeholder="Upload Header Logo" type="file"
                                       name="favicon_logo"
                                       id="favicon_logo">
                                {{ __('setting.Upload Fav Icon') }}
                            </button>
                        </div>
                        <a href="#" class="remove_logo">{{ __('setting.Remove') }}</a>
                    </div>
                </div>

                <div class="single_system_wrap">
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="primary_input mb-25">
                                <label class="primary_input_label" for="">{{ __('setting.Site Title') }}</label>
                                <input class="primary_input_field" placeholder="Infix CRM" type="text" id="site_title"
                                       name="site_title" value="{{ Settings('site_title') }}">
                            </div>
                        </div>

                        <div class="col-xl-6">
                            <div class="primary_input mb-25">
                                <label class="primary_input_label" for="">{{__('common.Email')}}</label>
                                <input class="primary_input_field" placeholder="demo@infix.com" type="email" id="email"
                                       name="email" value="{{ Settings('email') }}">
                            </div>
                        </div>


                        <div class="col-xl-6">
                            <div class="primary_input mb-25">
                                <label class="primary_input_label" for="">{{__('common.Phone')}}</label>
                                <input class="primary_input_field" placeholder="-" type="text" id="phone" name="phone"
                                       value="{{ Settings('phone') }}">
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="primary_input mb-25">
                                <label class="primary_input_label" for="">{{__('common.Country')}}</label>
                                <select class="primary_select mb-25" name="country_id" id="country_id">
                                    @foreach ($countries as $key => $country)
                                        <option value="{{ $country->id }}"
                                                @if (Settings('country_id')   == $country->id) selected @endif>{{ $country->name }}</option>
                                    @endforeach
                                </select>

                            </div>
                        </div>

                        <div class="col-xl-6">
                            <div class="primary_input mb-25">
                                <label class="primary_input_label" for="">{{__('common.Zip Code')}}</label>
                                <input class="primary_input_field" placeholder="-" type="text" id="zip_code"
                                       name="zip_code" value="{{ Settings('zip_code') }}">
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="primary_input mb-25">
                                <label class="primary_input_label"
                                       for="">{{ __('setting.System Default Language') }}</label>
                                <select class="primary_select mb-25" name="language_id" id="language_id">
                                    @foreach ($languages as $key => $language)
                                        <option value="{{ $language->id }}"
                                                @if (Settings('language_code')  == $language->code) selected @endif>{{ $language->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="primary_input mb-25">
                                <label class="primary_input_label" for="">{{ __('setting.Date Format') }}</label>
                                <select class="primary_select mb-25" name="date_format_id" id="date_format_id">
                                    @foreach ($date_formats as $key => $dateFormat)
                                        <option value="{{ $dateFormat->id }}"
                                                @if (Settings('date_format_id')  == $dateFormat->id) selected @endif>{{ $dateFormat->normal_view }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="primary_input mb-25">
                                <label class="primary_input_label"
                                       for="">{{ __('setting.System Default Currency') }}</label>
                                <select class="primary_select mb-25" name="currency_id" id="currency_id">
                                    @foreach ($currencies as $key => $currency)
                                        <option value="{{ $currency->id }}"
                                                @if (Settings('currency_id')  == $currency->id) selected @endif>{{ $currency->name }}
                                            - {{ $currency->code }}
                                            ({{ $currency->symbol }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="primary_input mb-25">
                                <label class="primary_input_label"
                                       for="currency_show">{{ __('setting.Currency Show At') }}</label>
                                <select class="primary_select mb-25" name="currency_show" id="currency_show">

                                    <option value="1"
                                            @if (Settings('currency_show')  == 1) selected @endif> {{__('setting.Left')}}
                                    </option>

                                    <option value="2"
                                            @if (Settings('currency_show')  == 2) selected @endif> {{__('setting.Left With Space')}}
                                    </option>

                                    <option value="3"
                                            @if (Settings('currency_show')  == 3) selected @endif> {{__('setting.Right')}}
                                    </option>

                                    <option value="4"
                                            @if (Settings('currency_show')  == 4) selected @endif> {{__('setting.Right With Space')}}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="primary_input mb-25">
                                <label class="primary_input_label" for="">{{ __('setting.Time Zone') }}</label>
                                <select class="primary_select mb-25" name="time_zone_id" id="time_zone_id">
                                    @foreach ($timeZones as $key => $timeZone)
                                        <option value="{{ $timeZone->id }}"
                                                @if (Settings('time_zone_id') == $timeZone->id) selected @endif>{{ $timeZone->time_zone }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="primary_input mb-25">
                                <label class="primary_input_label"
                                       for="show_drip">{{ __('common.Drip Content') }}</label>
                                <select class="primary_select mb-25" name="show_drip" id="show_drip">
                                    <option value="0"
                                            @if (Settings('show_drip') == 0) selected @endif>{{ __('common.Show All') }}</option>
                                    <option value="1"
                                            @if (Settings('show_drip') == 1) selected @endif>{{ __('common.Show After Unlock') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-xl-6">
                            <div class="primary_input mb-25">
                                <label class="primary_input_label"
                                       for="">{{ __('setting.Public Student Registration') }}</label>
                                <select class="primary_select mb-25" name="student_reg" id="student_reg">
                                    <option value="1"
                                            @if (Settings('student_reg') == 1) selected @endif>{{ __('common.Enable') }}</option>
                                    <option value="0"
                                            @if (Settings('student_reg') == 0) selected @endif>{{ __('common.Disable') }}</option>

                                </select>
                            </div>
                        </div>


                        <div class="col-xl-6">
                            <div class="primary_input mb-25">
                                <label class="primary_input_label"
                                       for="">{{ __('setting.Public Instructor Registration') }}</label>
                                <select class="primary_select mb-25" name="instructor_reg" id="instructor_reg">
                                    <option value="1"
                                            @if (Settings('instructor_reg') == 1) selected @endif>{{ __('common.Enable') }}</option>
                                    <option value="0"
                                            @if (Settings('instructor_reg') == 0) selected @endif>{{ __('common.Disable') }}</option>

                                </select>
                            </div>
                        </div>


                        <div class="col-xl-6">
                            <div class="primary_input mb-25">
                                <label class="primary_input_label"
                                       for="currency_conversion">{{ __('setting.Currency Conversion') }}</label>
                                <select class="primary_select mb-25" name="currency_conversion"
                                        id="currency_conversion">
                                    <option value="Fixer"
                                            @if (Settings('currency_conversion') == 'Fixer') selected @endif>Fixer
                                    </option>


                                </select>
                            </div>
                        </div>


                        <div class="col-xl-6">
                            <div class="primary_input mb-25">
                                <label class="primary_input_label" for="address">{{__('common.Address')}}</label>
                                <input class="primary_input_field" placeholder="-" type="text" id="address"
                                       name="address" value="{{Settings('address') }}">
                            </div>
                        </div>

                        <div class="col-xl-6">
                            <div class="primary_input mb-25">
                                <label class="primary_input_label"
                                       for="currency_conversion">{{ __('setting.Students Active Device Limit') }}</label>
                                <select class="primary_select mb-25" name="device_limit"
                                        id="currency_conversion">
                                    <option value="0"
                                            @if (Settings('device_limit') == 0) selected @endif>Unlimited
                                    </option>
                                    @for($i=1;$i<=20;$i++)
                                        <option value="{{$i}}"
                                                @if (Settings('device_limit') == $i) selected @endif>{{$i}}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <div class="col-xl-6">
                            <div class="primary_input mb-25">
                                <label class="primary_input_label"
                                       for="device_limit_time">{{ __('setting.Inactive Logout Time') }}
                                    <small>({{ __('setting.In Minute') }})</small>

                                </label>
                                <input class="primary_input_field" placeholder="0 means Unlimited" type="number" min="0"
                                       id="device_limit_time"
                                       name="device_limit_time" value="{{ Settings('device_limit_time') }}">
                            </div>
                        </div>

                        <div class="col-xl-6">
                            <div class="primary_input mb-25">
                                <label class="primary_input_label"
                                       for="google_analytics">{{__('setting.Google Analytics')}} <small>(example:G-5S6YDGEDS3/UA-198163311-1)</small></label>
                                <input class="primary_input_field" placeholder="-" type="text" id="google_analytics"
                                       name="google_analytics" value="{{ Settings('google_analytics') }}">
                            </div>
                        </div>

                        <div class="col-xl-6">
                            <div class="primary_input mb-25">
                                <label class="primary_input_label"
                                       for="category_show">{{__('setting.Category Show In Frontend')}} </label>
                                <select class="primary_select mb-25" name="category_show"
                                        id="category_show">
                                    <option value="1"
                                            @if (Settings('category_show') == 1) selected @endif>Show
                                    </option>

                                    <option value="0"
                                            @if (Settings('category_show') == 0) selected @endif>Hide
                                    </option>
                                </select>
                            </div>
                        </div>



                        <div class="col-xl-6">
                            <div class="primary_input mb-25">
                                <label class="primary_input_label"
                                       for="google_analytics">{{__('setting.Hide Footer From Mobile')}}  </label>
                                <select class="primary_select mb-25" name="footer_show"
                                        id="currency_conversion">
                                    <option value="1"
                                            @if (Settings('footer_show') == 1) selected @endif>Show
                                    </option>

                                    <option value="0"
                                            @if (Settings('footer_show') == 0) selected @endif>Hide
                                    </option>
                                </select>
                            </div>
                        </div>


                        <div class="col-xl-6">
                            <div class="primary_input mb-25">
                                <label class="primary_input_label"
                                       for="show_cart">{{__('setting.Show Cart  In Frontend')}}  </label>
                                <select class="primary_select mb-25" name="show_cart"
                                        id="show_cart">
                                    <option value="1"
                                            @if (Settings('show_cart') == 1) selected @endif>Show
                                    </option>

                                    <option value="0"
                                            @if (Settings('show_cart') == 0) selected @endif>Hide
                                    </option>
                                </select>
                            </div>
                        </div>


                        <div class="col-xl-6">
                            <div class="primary_input mb-25">
                                <label class="primary_input_label"
                                       for="course_approve">{{__('setting.Course Approval By Admin')}}  </label>
                                <select class="primary_select mb-25" name="course_approval"
                                        id="course_approval">
                                    <option value="1"
                                            @if (Settings('course_approval') == 1) selected @endif> Yes
                                    </option>

                                    <option value="0"
                                            @if (Settings('course_approval') == 0) selected @endif> No
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="col-xl-6">
                            <div class="primary_input mb-25">
                                <label class="primary_input_label"
                                       for="facebook_pixel">{{__('setting.Facebook Pixel ID')}}  </label>
                                <input class="primary_input_field" placeholder="-" type="text" id="facebook_pixel"
                                       name="facebook_pixel" value="{{ Settings('facebook_pixel') }}">
                            </div>
                        </div>


                        <div class="col-xl-6">
                            <div class="primary_input mb-25">
                                <label class="primary_input_label"
                                       for="student_dashboard_card_view">{{__('setting.Student Dashboard Info In Card View')}}  </label>
                                <select class="primary_select mb-25" name="student_dashboard_card_view"
                                        id="student_dashboard_card_view">
                                    <option value="1"
                                            @if (Settings('student_dashboard_card_view') == 1) selected @endif> Yes
                                    </option>

                                    <option value="0"
                                            @if (Settings('student_dashboard_card_view') == 0) selected @endif> No
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="col-xl-6">
                            <div class="primary_input mb-25">
                                <label class="primary_input_label"
                                       for="show_seek_bar">{{__('setting.Show SeekBar In Player')}}  </label>
                                <select class="primary_select mb-25" name="show_seek_bar"
                                        id="show_seek_bar">

                                    <option value="0"
                                            @if (Settings('show_seek_bar') == 0) selected @endif> No
                                    </option>
                                    <option value="1"
                                            @if (Settings('show_seek_bar') == 1) selected @endif> Yes
                                    </option>

                                </select>
                            </div>
                        </div>

                        <div class="col-xl-12">
                            <div class="primary_input mb-25">
                                <label class="primary_input_label"
                                       for="company_info">{{__('setting.Company Information')}}   </label>
                                <textarea class="primary_textarea" placeholder="Company Info" id="company_info"
                                          cols="30" rows="10"
                                          name="company_info">{{ Settings('company_info') }}</textarea>
                            </div>
                        </div>
                        <div class="col-xl-12">
                            <div class="primary_input mb-25">
                                <label class="primary_input_label"
                                       for="copyright_text">{{ __('setting.Backend') }} {{ __('setting.Copyright Text') }}</label>
                                <input class="primary_input_field" placeholder="-" type="text" id="copyright_text"
                                       name="copyright_text" value="{{Settings('copyright_text') }}">
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            @php
                $tooltip = "";
                if(permissionCheck('settings.general_setting_update')){
                    $tooltip = "";
                }else{
                    $tooltip = "You have no permission to add";
                }
            @endphp
            <div class="submit_btn text-center mt-4">
                <button class="primary_btn_large" type="submit" data-toggle="tooltip" title="{{$tooltip}}"
                        id="general_info_sbmt_btn"><i class="ti-check"></i> {{ __('common.Save') }}</button>
            </div>
        </form>
</div>
</div>
