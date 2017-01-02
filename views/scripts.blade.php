@section('scaffold.js')
    <script>
        $(function () {
            $.AdminArchitect.toggleCollection();
            $.AdminArchitect.handleBatchActions();
        });

        $(function () {
            $('.toggle-languages .btn').click(function() {
                $(this).addClass('active').siblings().removeClass('active');

                $('.translate-area[data-locale="' + $(this).data('locale') + '"]').show().siblings().hide();
            });

            $('#translates-save').click(function() {
                $('#batch_action').val('save_translations');
            });
        });
    </script>
@append
