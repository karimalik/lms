<div>
    <style>
        .pb_50 {
            padding-bottom: 50px;
        }
    </style>
    <div class="main_content_iner">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="purchase_history_wrapper pb_50">
                        <div class="row">
                            <div class="col-12">
                                <div class="section__title3 mb_40">
                                    <h3 class="mb-0">{{__('certificate.My Certificates')}}</h3>
                                    <h4></h4>
                                </div>
                            </div>
                        </div>
                        @if(count($certificate_records)==0)
                            <div class="col-12">
                                <div class="section__title3 margin_50">
                                    <p class="text-center">{{__('certificate.Certificate Not Found!')}}</p>
                                </div>
                            </div>
                        @else
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="table-responsive">
                                        <table class="table custom_table3 mb-0">
                                            <thead>
                                            <tr>
                                                <th scope="col">{{__('common.SL')}}</th>
                                                <th scope="col">{{__('common.Date')}}</th>
                                                <th scope="col">{{__('common.Course')}}</th>
                                                <th scope="col">{{__('certificate.Certificate No')}}</th>
                                                <th scope="col" style="text-align: center">{{__('common.Action')}}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if(isset($certificate_records))
                                                @foreach ($certificate_records as $key=>$certificate)
                                                {{-- @dd($certificate) --}}
                                                    <tr>
                                                        <td scope="row">{{@$key+1}}</td>

                                                        <td>{{ date(Settings('active_date_format'), strtotime($certificate->created_at)) }}</td>

                                                        <td>
                                                            {{@$certificate->course->title}}

                                                        </td>
                                                        <td>
                                                            {{@$certificate->certificate_id}}

                                                        </td>
                                                        <td>
                                                            <a href="{{route('certificateDownload',$certificate->certificate_id)}}"
                                                               class="link_value theme_btn small_btn4">{{__('common.Download')}}</a>
                                                            <a href="{{route('certificateCheck',$certificate->certificate_id)}}"
                                                               class="link_value theme_btn small_btn4">{{__('common.View')}}</a>
                                                    </tr>
                                                @endforeach
                                            @endif
                                            </tbody>
                                        </table>
                                        {{ $certificate_records->links() }}
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>