<div>
    <table id="" class="table  branchList ">
        <tbody>
        @if(!empty($branches))
            @foreach($branches->where('parent_id',0) as $key=>$branch)
                @include('org::students._single_branch',['branch'=>$branch,'level'=>1])
            @endforeach
        @endif
        </tbody>
    </table>

    @push('js')
        <script>
            /*
            $(document).on("click", ".activeBranchCode", function () {


                var parent = $(this).data('parent');

                console.log($('.collapseBranch' + parent))
                if ($('.collapseBranch' + parent).length && parent != '') {
                    if ($('.collapseBranch' + parent).find('.activeBranchCode').hasClass('active')) {
                        return;
                    }
                }

                console.log('ok')

                $(this).closest('td').find(".btn-header-link").trigger('click');
                var active;
                if ($(this).hasClass('active')) {
                    $(this).removeClass('active');
                    active = false;
                } else {
                    $(this).addClass('active');
                    active = true;
                }

                var id = $(this).closest('tr').data('id');
                var element = $('.collapseBranch' + id);

                if (element.length) {
                    checkChildActive(element, active)
                }

            });


            function checkChildActive(parentCode, active) {

                parentCode.each(function (i, obj) {
                    var parent = $(this);

                    parent.closest('tr').find('.btn-header-link').trigger('click');


                    var child = parent.closest('tr').find('.activeBranchCode');
                    if (!active) {
                        child.removeClass('active');
                    } else {
                        child.addClass('active');
                    }


                    var id = parent.closest('tr').data('id');
                    var element = $('.collapseBranch' + id);

                    if (element.length) {
                        checkChildActive(element, active)
                    }
                });

            }*/

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
