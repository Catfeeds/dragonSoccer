<script type="text/javascript">
var parameter = '';
function getparameter(str){
	//alert('---1-----');
    parameter = str;
    //alert(str);
}

$(document).ready(function (){
    $(".ajaxurl").on("click",function(){ 
    	//alert(parameter);  
        if(parameter !=''){
            window.location.href= $(this).attr('dataurl')+parameter;
        }else{
            sendError('重新登陆'); 
        }          
    })    
})  
</script>