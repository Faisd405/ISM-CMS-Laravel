<script src="{{ asset('vendor/file-manager/js/file-manager.js') }}"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {

        document.getElementById('button-image').addEventListener('click', (event) => {
            event.preventDefault();

            inputId = 'image1';

            window.open('/file-manager/fm-button', 'fm', 'width=1400,height=800');
        });

        document.getElementById('button-image2').addEventListener('click', (event) => {
            event.preventDefault();

            inputId = 'image2';

            window.open('/file-manager/fm-button', 'fm', 'width=1400,height=800');
        });

    });

    // input
    let inputId = '';

    // set file link
    function fmSetLink($url) {
        document.getElementById(inputId).value = $url;
    }
</script>
