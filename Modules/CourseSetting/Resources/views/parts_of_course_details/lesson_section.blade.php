@if(isset($editChapter) || isset($editLesson))
    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'updateChapter', 'method' => 'PUT', 'enctype' => 'multipart/form-data']) }}
@else
    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'saveChapter',
    'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
@endif

<input type="hidden" id="url" value="{{url('/')}}">
<input type="hidden" name="course_id" value="{{@$course->id}}">
<div class="section-white-box">
    <div class="add-visitor">

        <div class="row">
            <div class="col-lg-12">
                <input type="hidden" name="input_type" value="0" id="">
                <div class="lesson_div">
                    <div class="row">
                        <div class="col-lg-12">
                            <label class="primary_input_label mt-1"
                                   for=""> {{__('courses.Chapter')}}
                                <span>*</span></label>
                            <select class="primary_select" name="chapter_id">
                                <option
                                    data-display="{{__('common.Select')}} {{__('courses.Chapter')}}"
                                    value="">{{__('common.Select')}} {{__('courses.Chapter')}} </option>
                                @foreach ($chapters as $chapter)
                                    <option
                                        value="{{@$chapter->id}}" {{isset($editLesson)? ($editLesson->chapter_id == $chapter->id? 'selected':''):''}} >{{@$chapter->name}}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('category'))
                                <span class="invalid-feedback invalid-select"
                                      role="alert">
                                                    <strong>{{ $errors->first('category') }}</strong>
                                                </span>
                            @endif
                        </div>
                        <div class="col-lg-12">
                            <div class="input-effect mt-2 pt-1">
                                <label>{{__('courses.Lesson')}} {{__('common.Name')}}
                                    <span>*</span></label>
                                <input
                                    class="primary_input_field name{{ $errors->has('chapter_name') ? ' is-invalid' : '' }}"
                                    type="text" name="name"
                                    placeholder="{{__('courses.Lesson')}} {{__('common.Name')}}"
                                    autocomplete="off"
                                    value="{{isset($editLesson)? $editLesson->name:''}}">
                                <input type="hidden" name="lesson_id"
                                       value="{{isset($editLesson)? $editLesson->id: ''}}">
                                <span class="focus-border"></span>
                                @if ($errors->has('chapter_name'))
                                    <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('chapter_name') }}</strong>
                                            </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-12">

                            <div class="input-effect mt-2 pt-1">
                                <label>{{__('common.Duration')}} ({{__('common.In Minute')}}) </label>
                                <input
                                    class="primary_input_field name{{ $errors->has('chapter_name') ? ' is-invalid' : '' }}"
                                    min="0" step="any" type="number" name="duration"
                                    placeholder="{{__('courses.Duration')}}"
                                    autocomplete="off"
                                    value="{{isset($editLesson)? $editLesson->duration:''}}">

                                <span class="focus-border"></span>
                                @if ($errors->has('chapter_name'))
                                    <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('chapter_name') }}</strong>
                                            </span>
                                @endif
                            </div>
                            @if(isModuleActive('Org'))

                                @include('coursesetting::parts_of_course_details._org_host_select')

                            @endif

                            <div class="defaultHost {{isModuleActive('Org')?'d-none':''}}">

                                <div class="input-effect mt-2 pt-1">
                                    <label class="primary_input_label mt-1"
                                           for=""> {{__('courses.Host')}}
                                        <span>*</span></label>


                                    <select class="primary_select host_select" name="host"
                                            id="category_id">
                                        <option
                                            data-display="{{__('common.Select')}} {{__('courses.Host')}}"
                                            value="">{{__('common.Select')}} {{__('courses.Host')}} </option>
                                        <option value="Youtube"
                                                @if (@$editLesson->host=='Youtube') Selected
                                                @endif
                                                @if(empty(@$editLesson) =="Youtube") selected @endif
                                        >
                                            Youtube
                                        </option>

                                        <option value="Vimeo"
                                                @if (@$editLesson->host=='Vimeo') Selected
                                                @endif
                                                @if(empty(@$editLesson) =="Vimeo") selected @endif
                                        >
                                            Vimeo
                                        </option>
                                        <option value="Self"
                                                @if (@$editLesson->host=='Self') Selected
                                                @endif
                                                @if(empty(@$editLesson) =="Self") selected @endif
                                        >
                                            Self
                                        </option>
                                        <option value="VdoCipher"
                                                @if (@$editLesson->host=='VdoCipher') Selected
                                                @endif
                                                @if(empty(@$editLesson) && @$editLesson->host=="VdoCipher") selected @endif>
                                            VdoCipher
                                        </option>
                                        <option value="URL"
                                                @if (@$editLesson->host=='URL') Selected
                                                @endif
                                                @if(empty(@$editLesson) && @$editLesson->host=="URL") selected @endif >
                                            Video URL
                                        </option>

                                        <option value="Iframe"
                                                @if (@$editLesson->host=='Iframe') Selected
                                                @endif
                                                @if(empty(@$editLesson) && @$editLesson->host=="Iframe") selected @endif >
                                            Iframe embed
                                        </option>

                                        <option value="Image"
                                                @if (@$editLesson->host=='Image') Selected
                                                @endif
                                                @if(empty(@$editLesson) && @$editLesson->host=="Image") selected @endif >
                                            Image
                                        </option>

                                        <option value="PDF"
                                                @if (@$editLesson->host=='PDF') Selected
                                                @endif
                                                @if(empty(@$editLesson) && @$editLesson->host=="PDF") selected @endif >
                                            PDF File
                                        </option>

                                        <option value="Word"
                                                @if (@$editLesson->host=='Word') Selected
                                                @endif
                                                @if(empty(@$editLesson) && @$editLesson->host=="Word") selected @endif >
                                            Word File
                                        </option>


                                        <option value="Excel"
                                                @if (@$editLesson->host=='Excel') Selected
                                                @endif
                                                @if(empty(@$editLesson) && @$editLesson->host=="Excel") selected @endif >
                                            Excel File
                                        </option>

                                        <option value="PowerPoint"
                                                @if (@$editLesson->host=='PowerPoint') Selected
                                                @endif
                                                @if(empty(@$editLesson) && @$editLesson->host=="PowerPoint") selected @endif >
                                            Power Point File
                                        </option>


                                        <option value="Text"
                                                @if (@$editLesson->host=='Text') Selected
                                                @endif
                                                @if(empty(@$editLesson) && @$editLesson->host=="Text") selected @endif >
                                            Text File
                                        </option>


                                        <option value="Zip"
                                                @if (@$editLesson->host=='Zip') Selected
                                                @endif
                                                @if(empty(@$editLesson) && @$editLesson->host=="Zip") selected @endif >
                                            Zip File
                                        </option>

                                        <option value="GoogleDrive"
                                                @if (@$editLesson->host=='GoogleDrive') Selected
                                                @endif
                                                @if(empty(@$editLesson) && @$editLesson->host=="GoogleDrive") selected @endif >
                                            Google Drive
                                        </option>

                                        @if(isModuleActive("AmazonS3"))
                                            <option value="AmazonS3"
                                                    @if (@$editLesson->host=='AmazonS3') Selected
                                                    @endif
                                                    @if(empty(@$editLesson) =="AmazonS3") selected @endif
                                            >
                                                Amazon S3
                                            </option>
                                        @endif

                                        @if(isModuleActive("SCORM"))
                                            <option value="SCORM"

                                                    @if(empty(@$editLesson) =="SCORM") selected @endif
                                            >
                                                SCORM Self
                                            </option>
                                        @endif

                                        @if(isModuleActive("AmazonS3") && isModuleActive("SCORM"))
                                            <option value="SCORM-AwsS3"
                                                    @if(empty(@$editLesson) =="SCORM-AwsS3") selected @endif
                                            >
                                                SCORM AWS S3
                                            </option>
                                        @endif
                                    </select>
                                    @if ($errors->has('category'))
                                        <span class="invalid-feedback invalid-select"
                                              role="alert">
                                                                        <strong>{{ $errors->first('category') }}</strong>
                                                                    </span>
                                    @endif
                                </div>
                                @if(isModuleActive('SCORM'))
                                    <div
                                        class="input-effect mt-2 pt-1 {{ isset($editLesson) && $editLesson->host != 'SCORM' ? 'd-none' : '' }}"
                                        id="scorm_vendor_type">
                                        <div class="" id="">
                                            <label class="primary_input_label mt-1"
                                                   for="">{{__('courses.scorm_vendor_type')}}
                                                <span>*</span> </label>
                                            <select class="primary_select" name="scorm_vendor_type">
                                                @if(!isset($editLesson))
                                                    <option
                                                        data-display="{{__('common.Select')}} Vendor Type "
                                                        value="">{{__('common.Select')}} {{__('courses.SCORM Vendor')}}
                                                    </option>
                                                @endif
                                                <option
                                                    {{isset($editLesson) ? $editLesson->scorm_vendor_type=='ispring'? 'selected':'':'' }}
                                                    value="ispring">{{__('courses.Ispring')}} </option>

                                                <option
                                                    {{isset($editLesson) ? $editLesson->scorm_vendor_type=='storyline'? 'selected':'':'' }}
                                                    value="storyline">{{__('courses.Story Line')}} </option>

                                                <option
                                                    {{isset($editLesson) ? $editLesson->scorm_vendor_type=='other'? 'selected':'':'' }}
                                                    value="other">{{__('courses.Other')}} </option>

                                            </select>
                                            @if ($errors->has('scorm_vendor_type'))
                                                <span class="invalid-feedback invalid-select"
                                                      role="alert">
                                            <strong>{{ $errors->first('scorm_vendor_type') }}</strong>
                                        </span>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                <div class="input-effect mt-2 pt-1" id="videoUrl"
                                     style="display:@if((isset($editLesson) && ($editLesson->host!="Youtube"  && $editLesson->host!="URL")) || !isset($editLesson)) none  @endif">
                                    <label>{{__('courses.Video URL')}}
                                        <span>*</span></label>
                                    <input
                                        id="youtubeVideo"
                                        class="primary_input_field name{{ $errors->has('video_url') ? ' is-invalid' : '' }}"
                                        type="text" name="video_url"
                                        placeholder="{{__('courses.Video URL')}}"
                                        autocomplete="off"
                                        value="@if(isset($editLesson)) @if($editLesson->host=="Youtube" || $editLesson->host=="URL"){{$editLesson->video_url}} @endif @endif">
                                    <span class="focus-border"></span>
                                    @if ($errors->has('video_url'))
                                        <span class="invalid-feedback" role="alert">
                                                                        <strong>{{ $errors->first('video_url') }}</strong>
                                                                    </span>
                                    @endif
                                </div>

                                <div class="input-effect mt-2 pt-1" id="iframeBox"
                                     style="display: @if((isset($editLesson) && ($editLesson->host!="Iframe")) || !isset($editLesson)) none  @endif">
                                    <div class="" id="">

                                        <label>{{__('courses.Iframe URL')}}
                                            <span>*</span></label>
                                        <input
                                            class="primary_input_field name{{ $errors->has('iframe_url') ? ' is-invalid' : '' }}"
                                            type="text" name="iframe_url"
                                            placeholder="{{__('courses.Iframe (Provide the source only)')}}"
                                            autocomplete="off"
                                            value="@if(isset($editLesson)) @if($editLesson->host=="Iframe"){{$editLesson->video_url}} @endif @endif">
                                        <span class="focus-border"></span>
                                        @if ($errors->has('video_url'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('video_url') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="input-effect mt-2 pt-1" id="vimeoUrl"
                                     style="display: @if((isset($editLesson) && ($editLesson->host!="Vimeo")) || !isset($editLesson)) none  @endif">
                                    <div class="" id="">
                                        @if(config('vimeo.connections.main.upload_type')=="Direct")
                                            <div class="primary_file_uploader">
                                                <input
                                                    class="primary-input filePlaceholder"
                                                    type="text"
                                                    id=""
                                                    {{$errors->has('image') ? 'autofocus' : ''}}
                                                    placeholder="{{__('courses.Browse Video file')}}"
                                                    readonly="">
                                                <button class="" type="button">
                                                    <label
                                                        class="primary-btn small fix-gr-bg"
                                                        for="document_file_thumb_vimeo_lesson_section">{{__('common.Browse') }}</label>
                                                    <input type="file"
                                                           class="d-none fileUpload"
                                                           name="vimeo"
                                                           id="document_file_thumb_vimeo_lesson_section">
                                                </button>
                                            </div>
                                        @else
                                            <select class="primary_select" name="vimeo"
                                                    id="vimeoVideo">
                                                <option
                                                    data-display="{{__('common.Select')}} video "
                                                    value="">{{__('common.Select')}} video
                                                </option>
                                                @foreach ($video_list as $video)
                                                    @if(isset($editLesson))
                                                        <option
                                                            value="{{@$video['uri']}}" {{$video['uri']==$editLesson->video_url?'selected':''}}>{{@$video['name']}}</option>
                                                    @else
                                                        <option
                                                            value="{{@$video['uri']}}">{{@$video['name']}}</option>
                                                    @endif


                                                @endforeach
                                            </select>
                                        @endif
                                        @if ($errors->has('vimeo'))
                                            <span class="invalid-feedback invalid-select"
                                                  role="alert">
                                            <strong>{{ $errors->first('vimeo') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="input-effect mt-2 pt-1" id="VdoCipherUrl"
                                     style="display: @if((isset($editLesson) && ($editLesson->host!="VdoCipher")) || !isset($editLesson)) none  @endif">
                                    <div class="" id="">

                                        <select class="select2 lessonVdocipher VdoCipherVideoLesson" name="vdocipher"
                                                id="VdoCipherVideo">
                                            <option
                                                data-display="{{__('common.Select')}} video "
                                                value="">{{__('common.Select')}} video
                                            </option>
                                            {{--                                            @foreach ($vdocipher_list as $vdo)--}}
                                            {{--                                                @if(isset($editLesson))--}}
                                            {{--                                                    <option--}}
                                            {{--                                                        value="{{@$vdo->id}}" {{$vdo->id==$editLesson->video_url?'selected':''}}>{{@$vdo->title}}</option>--}}
                                            {{--                                                @else--}}
                                            {{--                                                    <option--}}
                                            {{--                                                        value="{{@$vdo->id}}">{{@$vdo->title}}</option>--}}
                                            {{--                                                @endif--}}


                                            {{--                                            @endforeach--}}
                                        </select>
                                        @if ($errors->has('vdocipher'))
                                            <span class="invalid-feedback invalid-select"
                                                  role="alert">
                                                                        <strong>{{ $errors->first('vdocipher') }}</strong>
                                                                    </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="input-effect mt-2 pt-1" id="fileupload"
                                     style="display: @if((isset($editLesson) && (($editLesson->host=="Vimeo") ||  ($editLesson->host=="Youtube")|| ($editLesson->host=="vdocipher")|| ($editLesson->host=="Iframe")||  ($editLesson->host=="URL")) ) || !isset($editLesson)) none  @endif">
                                    <input type="file" class="filepond"
                                           name="file"
                                           id="">


                                </div>
                            </div>
                            <div class="input-effect mt-2 pt-1">
                                <div class="" id="">
                                    <label class="primary_input_label mt-1"
                                           for="">{{__('courses.Privacy')}}
                                        <span>*</span> </label>
                                    <select class="primary_select" name="is_lock">
                                        <option
                                            data-display="{{__('common.Select')}} {{__('courses.Privacy')}} "
                                            value="">{{__('common.Select')}} {{__('courses.Privacy')}} </option>
                                        @if(isset($editLesson))
                                            <option value="0"
                                                    @if ( @$editLesson->is_lock==0) selected @endif >{{__('courses.Unlock')}}</option>
                                            <option value="1"
                                                    @if (@$editLesson->is_lock==1) selected @endif >{{__('courses.Locked')}}</option>
                                        @else
                                            <option
                                                value="0">{{__('courses.Unlock')}}</option>
                                            <option value="1"
                                                    selected>{{__('courses.Locked')}}</option>
                                        @endif


                                    </select>
                                    @if ($errors->has('is_lock'))
                                        <span class="invalid-feedback invalid-select"
                                              role="alert">
                                            <strong>{{ $errors->first('is_lock') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="input-effect mt-2 pt-1">
                                <label>{{__('common.Description')}}
                                </label>
                                <input
                                    class="primary_input_field name{{ $errors->has('description') ? ' is-invalid' : '' }}"
                                    type="text" name="description"
                                    placeholder="{{__('common.Description')}}"
                                    autocomplete="off"
                                    value="{{isset($editLesson)? $editLesson->description:''}}">
                                <span class="focus-border"></span>
                                @if ($errors->has('description'))
                                    <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('description') }}</strong>
                                            </span>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>


            </div>
        </div>

        <div class="row mt-40">
            <div class="col-lg-12 text-center">
                <button type="submit" class="primary-btn fix-gr-bg"
                        data-toggle="tooltip">
                    <span class="ti-check"></span>
                    {{__('common.Save')}}
                </button>
            </div>
        </div>
    </div>
</div>
{{ Form::close() }}

@push('js')
    <script>
        var scorm_vendor_type = $('#scorm_vendor_type');
        var host_e = $('.host_select').val();
        scorm_vendor_type.hide();
        if (host_e == 'SCORM') {
            scorm_vendor_type.show();
        }

        $('.host_select').on('change', function () {
            var host_e = $('.host_select').val();
            console.log(host_e)
            scorm_vendor_type.hide();
            if (host_e == 'SCORM') {
                scorm_vendor_type.show();
            }
        });

    </script>
@endpush
