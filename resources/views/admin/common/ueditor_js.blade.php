<script type="text/javascript" src="/js/ueditor/ueditor.config.js"></script>
<script type="text/javascript" src="/js/ueditor/ueditor.all.min.js"></script>
<script type="text/javascript">
$(document).ready(function (){
    //<!-- 实例化编辑器 -->
    var ue = UE.getEditor(
        'container',
        {
            toolbars: [
                ['fullscreen', 'source', 'undo', 'redo'],
                ['bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'superscript', 'subscript', 'removeformat', 'formatmatch', 'autotypeset', 'blockquote', 'pasteplain', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', 'selectall', 'cleardoc']
            ],
            serverUrl:'', 
            initialFrameHeight:500, 
            //allHtmlEnabled:true,
            maximumWords :9999999999,
            autoHeightEnabled :false,
            //enableAutoSave: false,
        }
    );
});
</script>