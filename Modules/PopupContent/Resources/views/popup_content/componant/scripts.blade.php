@push('scripts')

    <script>

        (function ($) {
            "use strict";
            $(document).ready(function () {
                $(document).on('change', '#document_file_1', function () {
                    getFileName($(this).val(), '#placeholderFileOneName');
                    imageChangeWithFile($(this)[0], '#blogImgShow');
                });
            });
        })(jQuery);

    </script>

@endpush
