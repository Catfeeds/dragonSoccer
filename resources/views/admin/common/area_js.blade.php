<script type="text/javascript">
$(document).ready(function (){
    //按钮提交 eg:删除
    $("select[name=province]").on("change",function(){ 
        $("select[name=city]").html('<option value="">请选择</option>'); 
        $("select[name=country]").html('<option value="">请选择</option>');  
        $.ajax({
            type: "get",
            url: '/area/ajaxgetlist/'+$(this).find("option:selected").attr('datacode'),
            dataType: "json",
            success: function (da) {
                if (da.error == 0) {
                    var html = '<option value="">请选择</option>';
                    $.each(da.data, function(index, ele) {
                        html += '<option value="'+ ele.name +'" datacode="'+ ele.code +'">'+ ele.name +'</option>';
                    })
                    $("select[name=city]").html(html);
                }
            },
        });
        return false;            
    })

    $("select[name=city]").on("change",function(){ 
        $("select[name=country]").html('<option value="">请选择</option>');   
        $.ajax({
            type: "get",
            url: '/area/ajaxgetlist/'+$(this).find("option:selected").attr('datacode'),
            dataType: "json",
            success: function (da) {
                if (da.error == 0) {
                    var html = '<option value="">请选择</option>';
                    $.each(da.data, function(index, ele) {
                        html += '<option value="'+ ele.name +'" datacode="'+ ele.code +'">'+ ele.name +'</option>';
                    })
                    $("select[name=country]").html(html);
                }
            },
        });
        return false;            
    })


});    
</script>