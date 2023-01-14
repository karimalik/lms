@extends(theme('layouts.master'))
@section('title'){{Settings('site_title')  ? Settings('site_title')  : 'Infix LMS'}} | {{__('certificate.Certificate')}} @endsection
@section('css') @endsection
@section('js')
    <script src="{{ asset('public/frontend/infixlmstheme/js/classes.js') }}"></script>
@endsection
@section('mainContent')

   
<style>
    .primary_input4 {
        height: 60px;
    }
    .theme_mailChimp {
    margin-top: 40px;
}
.address_lines{
    margin-top: 20px
}
.theme_mailChimp {
    margin-bottom: 65px !important;
}
</style>
{{-- {!! $certificate !!} --}}

    <div class="contact_section ">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-10">
                    <div class="contact_address">
                        <div class="row justify-content-center">
                            <div class="col-xl-12">
                                <div class="row justify-content-between">
                                    <div class="col-lg-12 p-5">
                                        <div class="contact_title">
                                            <h4 class="mb-0">{{__('certificate.Verify Certificate')}}</h4>
                                            <div class="subcribe-form theme_mailChimp mt-40">
                                                <form action="{{route('showCertificate')}}"
                                                      method="POST" class="subscription relative">@csrf
                                                    <input name="certificate_number" class="primary_input4"
                                                           placeholder="{{__('certificate.Enter Certificate Number')}}"
                                                           onfocus="this.placeholder = ''"
                                                           onblur="this.placeholder = '{{__('certificate.Enter Certificate Number')}}'"
                                                           required="" type="text" value="{{old('certificate_number')}}">
            
                                                    <button type="submit">{{__('chat.search')}}</button>
                                                    <div class="info">
                                                        <span class="text-danger" role="alert">{{$errors->first('certificate_number')}}</span>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        {{-- <img class="img-rounded" src="{{$certificate}}" alt=""> --}}
                                        <div class="message text-center">
                                            @if($errors->any())
                                                <h4 class="text-danger">{{__('certificate.Invalid Certificate Number')}}</h4>
                                            @endif
                                        </div>
                                        <div class="address_lines py-3">
                                          
                                            <img class="" style="width: 100%; height:auto" src="{{@$certificate}}" alt="">
                                           
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

