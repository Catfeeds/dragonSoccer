<script type="text/javascript" src="/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="/js/bootstrap-datetimepicker.zh-CN.js"></script>
<script type="text/javascript">
$(document).ready(function (){
    //引用日历控件
    $('.form_datetime').datetimepicker({
        language:  'zh-CN',
        weekStart: 1,
        todayBtn:  1,
        autoclose: 1,
        todayHighlight: 1,
        startView: 2,
        minView: 2,
        forceParse: 0,
        initialDate:new Date(),
        pickerPosition:'bottom-left'
    });
});
</script>