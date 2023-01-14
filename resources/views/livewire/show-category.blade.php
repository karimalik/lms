<div>
    <table id="" class="table  branchList">
        <tbody>
        @if(!empty($categories))
            @foreach($categories as $key=>$category)
                @include('org::category._single_category',['$category'=>$category,'level'=>1])
            @endforeach
        @endif
        </tbody>
    </table>

    @push('js')
{{--        <script>--}}
{{--            $(document).on("click", ".activeCategoryCode", function () {--}}
{{--                if ($(this).hasClass('active')) {--}}
{{--                    $(this).removeClass('active');--}}
{{--                } else {--}}
{{--                    $(this).addClass('active');--}}
{{--                }--}}
{{--            });--}}
{{--        </script>--}}
    @endpush
</div>
