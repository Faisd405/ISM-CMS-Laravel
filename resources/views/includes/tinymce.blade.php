<script>
    tinymce.init({
        selector: '.tiny-mce',
        height: 400,
        min_height: 300,
        max_height: 500,
        plugins: 'image, link, media, wordcount, lists, code, table, preview',
        toolbar: ['formatselect | bold italic strikethrough superscript subscript forecolor backcolor formatpainter | table link image media pageembed | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent | removeformat code'],

        path_absolute : "/",
        // file_picker_callback (callback, value, meta) {
        //     let x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth
        //     let y = window.innerHeight|| document.documentElement.clientHeight|| document.getElementsByTagName('body')[0].clientHeight

        //     tinymce.activeEditor.windowManager.openUrl({
        //     url : '/file-manager/tinymce5',
        //     title : 'File manager',
        //     width : x * 0.8,
        //     height : y * 0.8,
        //     onMessage: (api, message) => {
        //         callback(message.content, { text: message.text })
        //     }
        //     })
        // },
        relative_urls : false,
        remove_script_host : false,
        convert_urls : true,
      });

</script>
