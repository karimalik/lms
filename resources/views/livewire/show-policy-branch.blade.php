<div>
    <table id="" class="table  branchList ">
        <tbody>
        @if(!empty($branches))
            @foreach($branches->where('parent_id',0) as $key=>$branch)
                @include('orginstructorpolicy::_single_branch',['branch'=>$branch,'level'=>1])
            @endforeach
        @endif
        </tbody>
    </table>

    @push('js')
        <script>
            $(document).on("click", ".btn-header-link", function () {
                var id = $(this).data('branch_id');
                var parent_id = $(this).data('parent_id');
                var childs = $('.parentId' + id);
                var status = false;
                if ($(this).hasClass('collapsed')) {
                    status = true;
                }
                if (status) {
                    checkChildActive(childs);
                }


            });


            function checkChildActive(childs) {
                childs.each(function (i, obj) {
                    var parent = $(this);
                    parent.removeClass('show');


                    var id = parent.data('id');
                    var childs = $('.parentId' + id);
                    if (childs.length) {
                        checkChildActive(childs)
                    }
                });
            }
        </script>
    @endpush
</div>
