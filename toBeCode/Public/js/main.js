$(function() {
    $('#table').bootstrapTable()
    //$("#includedNav").load("nav.html"); 
    //$("#includedContainer").load("includedContainer.html");
    $("#includedViewModal").load("viewModal.html"); 
            
})

function ajaxRequest(params) {        
    $.ajax({
        url: "/toBeCode/App/Controllers/Products.php",   
        type:"POST",
        dataType: 'JSON',
        data:params.data,
        success:function(response){
            if(response.success == 1) {
                params.success(response.data);
            }
            else{
                alert(response.error);
            }
        },
        error:function(response){
            //console.log('error',response);
            if(response) {
            }
        },
    });
} 
        
function openViewForm(products_id) {
    postData = {
        products_id: products_id,
    } 
    $.ajax({
        url: "/toBeCode/App/Controllers/OpenViewForm.php",
        type:"POST",
	dataType: 'JSON',
        data:postData,
        success:function(response){
            //console.log(response.requestData);Price_EndUser_LV_wo_VAT
            if(response.success == 1) {
                $("#viewModal").modal();
                data = response.data
                $( "#viewModal input[name='prod_name']" ).val(data.prod_name);
                $( "#viewModal input[name='prod_name']" ).attr('readonly', true);
                $( "#viewModal input[name='prod_name_english']" ).val(data.prod_name_english);
                $( "#viewModal input[name='prod_name_english']" ).attr('readonly', true);
                $( "#viewModal input[name='prod_model']" ).val(data.prod_model);
                $( "#viewModal input[name='prod_model']" ).attr('readonly', true);
                $( "#viewModal input[name='Price_EndUser_LV_wo_VAT']" ).val(data.prod_Price_EndUser_LV_wo_VAT);
                $( "#viewModal input[name='Price_EndUser_LV_wo_VAT']" ).attr('readonly', true);
                $( "#viewModal input[name='prod_avail']" ).val(data.prod_avail);
                $( "#viewModal input[name='prod_avail']" ).attr('readonly', true);
                
                $( "#viewModal input[name='prod_weight']" ).val(data.prod_weight);
                $( "#viewModal input[name='prod_weight']" ).attr('readonly', true);
                $( "#viewModal input[name='prod_warra']" ).val(data.prod_warra);
                $( "#viewModal input[name='prod_warra']" ).attr('readonly', true);
                $( "#viewModal input[name='prod_full_cat_path']" ).val(data.prod_full_cat_path);
                $( "#viewModal input[name='prod_full_cat_path']" ).attr('readonly', true);
                $( "#viewModal input[name='prod_full_cat']" ).val(data.prod_full_cat);
                $( "#viewModal input[name='prod_full_cat']" ).attr('readonly', true);
                $( "#viewModal input[name='EAN']" ).val(data.EAN);
                $( "#viewModal input[name='EAN']" ).attr('readonly', true);
                
                $( "#viewModal input[name='In_Promotion']" ).val(data.In_Promotion);
                $( "#viewModal input[name='In_Promotion']" ).attr('readonly', true);
                $( "#viewModal input[name='man_name']" ).val(data.man_name);
                $( "#viewModal input[name='man_name']" ).attr('readonly', true);
                $( "#viewModal input[name='video_reviews']" ).val(data.video_reviews);
                $( "#viewModal input[name='video_reviews']" ).attr('readonly', true);
                $( "#viewModal input[name='prod_descr_text']" ).val(data.prod_descr_text);
                $( "#viewModal input[name='prod_descr_text']" ).attr('readonly', true);
                $( "#viewModal input[name='prod_descr_full']" ).val(data.prod_descr_full);
                $( "#viewModal input[name='prod_descr_full']" ).attr('readonly', true);
                
                $( "#viewModal input[name='prod_descr']" ).val(data.prod_descr);
                $( "#viewModal input[name='prod_descr']" ).attr('readonly', true);
                $( "#viewModal input[name='cat_name']" ).val(data.cat_name);
                $( "#viewModal input[name='cat_name']" ).attr('readonly', true);
                
            }
            else{
                //alert(response.error);
            }
        },
	error:function(response){
            console.log('error',response);
            if(response) {
            }
        },
    });
}
