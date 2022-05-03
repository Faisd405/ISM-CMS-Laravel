@if (Auth::guard()->check() == true)
<script>
    function totalNotif() {
        $.ajax({
            url : "/admin/notification/latest",
            type : "GET",
            dataType : "json",
            data : {},
            success:function(data) {
                const totalNotif = data.data.total;
                
                if (totalNotif > 0) {
                    var html = totalNotif + ' @lang('feature/notification.label.new_notif')';
                } else {
                    var html = '@lang('feature/notification.caption')';
                }

                $('#count-new-notif').html(html);
                    if (totalNotif > 0) {
                        $('#notif-dot').css({
                            display : 'inline block'
                        });
                        $('.count-dot').html(totalNotif);
                    } else {
                        $('#notif-dot').css({
                            display : 'none'
                        });
                    }
                    // setTimeout(totalNotif, 60 * 1000);
                }
        })
    };

    $(document).ready(function() {
        totalNotif();
    });

    $('#click-notif').click(function () {
        $.ajax({
            url : "/admin/notification/latest",
            type : "GET",
            dataType : "json",
            data : {},
            success:function(data) {
                $('#list-notification').html(' ');
                const totalNotif = data.data.total;
                const latestNotif = data.data.latest;

                if (totalNotif > 0) {
                    $.each(latestNotif ,function(index, value) {
                        var titik = '';
                        if (value.title.length > 50) {
                            titik = '...';
                        }
                        if (value.content.length > 50) {
                            titik = '...';
                        }
                        $('#list-notification').append(`
                        <a href="/admin/notification/`+value.id+`/read" class="list-group-item list-group-item-action media d-flex align-items-center">
                            <div class="ui-icon ui-icon-sm `+value.icon+` bg-`+value.color+` border-0 text-white"></div>
                                <div class="media-body line-height-condenced ml-3">
                                <div class="text-body">`+value.title.substring(0, 50)+titik+`</div>
                                    <div class="text-light small mt-1">
                                        `+value.content.substring(0, 50)+titik+`
                                    </div>
                                <div class="text-light small mt-1">`+value.date+`</div>
                            </div>
                        </a>
                        `);
                    });
                } else {
                    $('#list-notification').html(`
                        <a href="javascript:void(0)" class="list-group-item list-group-item-action media text-center">
                            <i><strong style="color:red;">! @lang('global.data_attr_empty', ['attribute' => __('feature/notification.caption')]) !</strong></i>
                        </a>
                    `);
                }
            },
        });
    });
</script>
@endif