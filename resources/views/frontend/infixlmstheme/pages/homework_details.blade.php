@extends(theme('layouts.dashboard_master'))
@section('title'){{Settings('site_title')  ? Settings('site_title')  : 'Infix LMS'}} | {{__('homework.Study Material')}} {{__('common.Details')}} @endsection
@section('css')
      <link href="{{asset('public/backend/css/summernote-bs4.min.css/')}}" rel="stylesheet">
    <link href="{{asset('public/frontend/compact/css/myProfile.css')}}" rel="stylesheet"/>
 @endsection
@section('js')
   <script src="{{asset('public/backend/js/summernote-bs4.min.js')}}"></script>
        <script>
            $(document).ready(function () {
                $('.lms_summernote').summernote({
                    placeholder: 'Answer',
                    tabsize: 2,
                    height: 188,
                    tooltip: true
                });
            });
    </script>

    <script>
        $('.assignment_file').change(function(){
            let file_name= $(this).val();
            file_name=file_name.replace(/C:\\fakepath\\/i, '');
           $('#show_file_name').html(file_name);
        });
    </script>
@endsection

@section('mainContent')
    <x-homework-details :id="$id" />
@endsection
