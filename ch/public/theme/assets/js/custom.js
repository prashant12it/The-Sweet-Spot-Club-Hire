'use strict';

function delProductAttribute(delAttributeId){
   $('#delAttributeId').val(delAttributeId);
   $('#deleteAttributeConfirm').modal('show');
}
 
function delAttributeOption(attributeId, OptionId){
   $('#attributeId').val(attributeId);
   $('#OptionId').val(OptionId);
   $('#deleteOptionConfirm').modal('show');
}

function addProAttribute(iAttributeCount){
    
    jQuery.get("/addProductAttr",{iAttributeCount:iAttributeCount},function(result){
        var ar_result = result.split("||||");            
        
        $("#attributeDiv_"+iAttributeCount).html('');
        $("#attributeDiv_"+iAttributeCount).html(ar_result[0]);
        $("#attributeDiv_"+iAttributeCount).after(ar_result[1]);

        // Init Select2 - Basic Single
        $(".select2-single").select2();

        // Init Select2 - Basic Multiple
        $(".select2-multiple").select2({
            placeholder: "Select attribute option",
            allowClear: true
        });

    });
}
function getAttributeOptions(iAttributeCount){
    
    var idAttribute = $("#attribName_"+iAttributeCount).val();
    
    jQuery.get("/getAttrOptions",{iAttributeCount:iAttributeCount,idAttribute:idAttribute},function(result){
        
        $("#attributeOptionsDiv_"+iAttributeCount).html('');
        $("#attributeOptionsDiv_"+iAttributeCount).html(result);
        
        // Init Select2 - Basic Multiple
        $(".select2-multiple").select2({
            placeholder: "Select attribute option",
            allowClear: true
        });

    });
}

function showUpsellProductsList(){
    var is_upsell_selected = $("#is_upsell_product").prop('checked');
    
    if(is_upsell_selected == true){
        $("#upsell_products_parent_div").attr('style','display:block;');
        $(".select2-container").attr('style','width:100%');
        $(".upsellProOpt").select2();
        // Init Select2 - Basic Multiple
        $(".upsellProOpt").select2({
            placeholder: "Select upsell products",
            allowClear: true
        });
    }else{
        $("#upsell_products_parent_div").attr('style','display:none;');
    }
}

function updateOrderStatus(idOrder, idStatus){
    $('#idOrder').val(idOrder);
    $('#idStatus').val(idStatus);
    if(parseInt(idStatus) == 3){
        $('#canceledDescriptionDiv').attr('style','display:block;');
        $("#cancelDescription").attr("required","required");
    }else{
        $('#canceledDescriptionDiv').attr('style','display:none;');
        $("#cancelDescription").removeAttr("required");
    }
   $('#updateStatusModal').modal('show');
}
function updateDisputedOrderStatus(idOrder, idStatus){
    $('#idOrder').val(idOrder);
    $('#idStatus').val(idStatus);
    $('#updateStatusModal').modal('show');
}
function checkOfferType(){
    var iOfferType = $("#offer_type").val();
    $("#amount_offer").attr('style','display:none;');
    $("#percentage_offer").attr('style','display:none;');
    $("#offer_amnt").removeAttr("required");
    $("#offer_percntg").removeAttr("required");
    
    if($.trim(iOfferType) == "0"){
        $("#amount_offer").attr("style","display:block");
        $("#offer_amnt").attr("required","required");
    }
    else if($.trim(iOfferType) == "1"){
        $("#percentage_offer").attr("style","display:block");
        $("#offer_percntg").attr("required","required");
    }
}

function deleteOffer(delOfferId){
   $('#idOffer').val(delOfferId);
   $('#deleteOfferModal').modal('show');
}

function showHideChildProducts(idProduct,value){
    if($.trim(value) == 'show'){
        $(".parent_pro_"+idProduct).removeAttr('style');
        $("#show-pro-"+idProduct).removeClass('show');
        $("#show-pro-"+idProduct).addClass('hide');
        $("#hide-pro-"+idProduct).removeClass('hide');
        $("#hide-pro-"+idProduct).addClass('show');
    }
    else if($.trim(value) == 'hide'){
        $(".parent_pro_"+idProduct).attr('style','display:none;');
        $("#hide-pro-"+idProduct).removeClass('show');
        $("#hide-pro-"+idProduct).addClass('hide');
        $("#show-pro-"+idProduct).removeClass('hide');
        $("#show-pro-"+idProduct).addClass('show');
        
    }
}

function showBookingDetails(divId,show){
    if($.trim(show) == 'show'){
        $("#"+divId).attr('style','display:block;');
        $("#info_"+divId).addClass('info-booking-fill');
    }
    else{
        $("#"+divId).attr('style','display:none;');
        $("#info_"+divId).removeClass('info-booking-fill');
    }
}
function updatePartnerStatus(idPartner, iActive,text){
    $('#idPartner').val(idPartner);
    $('#iActive').val(iActive);
    $('#message').html('');
    $('#message').html("Are you sure you want "+text+" partner account?");
    
    $('#partnerStatusModal').modal('show');
}


function samePickupAddress(setcount){
    
    var is_same_pickup_addrs = $("#is_same_pickup_addrs").prop('checked');

    var delvr_hotel_name = $("#delvr_hotel_name").val();
    var delvr_address = $("#delvr_address").val();
    var delvr_state_id = $("#delvr_state_id").val();
    var delvr_postal_code = $("#delvr_postal_code").val();
    var delvr_suburb = $("#suburb").val();
    var PlaceVal = $("input[name='dropoff_place']:checked").val();
    $("input[name='pickup_place'][value='"+PlaceVal+"']").prop('checked',true);
    // var Placeid = $("input[name='dropoff_place']:checked").id;
    if(is_same_pickup_addrs == true){
        $('.pickup').addClass('hide');
        $("#pickup_hotel_name").val(delvr_hotel_name);
        $("#pickup_address").val(delvr_address);
        $("#pickup_state_id").val(delvr_state_id);
        $("#pickup_postal_code").val(delvr_postal_code);
        $("#suburbpickup").val(delvr_suburb);
    calculateShipping(setcount);
    }else{
        $('.place-pickup').prop('checked',false);
        $("#pickup_hotel_name").val('');
        $("#pickup_address").val('');
        $("#pickup_state_id").val('');
        $("#pickup_postal_code").val('');
        $("#suburbpickup").val('');
        $('.pickup').removeClass('hide');
        calculateShipping(setcount);
    }
}

function updateBannerStatus(idBanner, iActive,text){
    $('#idBanner').val(idBanner);
    $('#iActive').val(iActive);
    $('#message_banner').html('');
    $('#message_banner').html("Are you sure you want "+text+" this banner?");
    
    $('#bannerStatusModal').modal('show');
}

function DeleteBanner(idBanner){
    $('#bannerid').val(idBanner);
    $('#message_banner1').html('');
    $('#message_banner1').html("Are you sure you want delete this banner?");

    $('#bannerDeleteModal').modal('show');
}

function removeBannerImage(){
    $("#old_banner_image").attr("style","display:none;");
    $("#file_name").attr("required","required");
    $("#file_name").attr("style","display:block;");
}

function viewOrignalBanner(bannerName,width,height,title){
    if(bannerName && width>0){
        $('#viewOrignalBanner .modal-body h4').remove();
        $('#viewOrignalBanner .modal-body').html('<img id="banner_view" />');
        var imageUrl = window.location.protocol + "//" + window.location.host + "/shop/public/banners_img/"+bannerName;
        $("#banner_view").attr("src","");
        $("#banner_view").attr("src",imageUrl);
        $("#banner_view").attr("width",width);
        $("#banner_view").attr("height",height);
    }else{
        $('#viewOrignalBanner .modal-body').html('<h4>'+title+'</h4>');
    }
    $('#viewOrignalBanner').modal('show');
}

function copyBannerCode(idBanner){
    
    $("#banner_"+idBanner).focus();
    $("#banner_"+idBanner).select();
    var successful = document.execCommand('copy');

    if(successful){
        $("#banner_copied_"+idBanner).html('');
        $("#banner_copied_"+idBanner).html('Banner Code Copied!');
        $("#banner_copied_"+idBanner).addClass('alert alert-success');
        setTimeout(function(){
            $("#banner_copied_"+idBanner).html('');
            $("#banner_copied_"+idBanner).removeClass('alert alert-success');
        }, 3000);
    }
}