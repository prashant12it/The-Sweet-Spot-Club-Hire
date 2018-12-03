/**
 * Created by HP on 25-09-2018.
 */
function addAnotherBag(){
    var bagcount = parseInt($('#bagcount').val());
    var exactBagCount = $('#exactbagcount').val();
    var newBagCount = bagcount+1;
    var addhtml = '<div class="row" id="bag'+newBagCount+'"><div class="form-group col-sm-5"><label>Bag title</label>'+
    '<input type="text" readonly name="bagTitle'+newBagCount+'" value="Bag '+newBagCount+'" placeholder="Bag title"/></div>'+
    '<div class="form-group col-sm-5"><label>Bag type/size</label><select name="bagType'+newBagCount+'">'+
        '<option value="3">Small Bag i.e. stand bag (30 x 30 x 123cm)</option>'+
        '<option value="1">Standard Bag i.e. cart bag (30 x 35 x 123cm)</option>'+
    '<option value="2">Large Bag i.e. staff bag (35 x 40 x 123cm)</option>'+
    '</select></div><div class="form-group col-sm-2">'+
        '<button class="btn btn-danger frontend-primary-btn col-md-8 btn-with-label" type="button" onclick="removeBag('+newBagCount+')">Remove bag</button>'+
    '</div></div>';
    $(addhtml).insertBefore('#add-bag');
    $('#bagcount').val(newBagCount);
    exactBagCount = parseInt(exactBagCount)+1;
    $('#exactbagcount').val(exactBagCount);
}

function removeBag(bagCount) {
    $('#bag'+bagCount).remove();
    var exactBagCount = $('#exactbagcount').val();
    exactBagCount = parseInt(exactBagCount)-1;
    $('#exactbagcount').val(exactBagCount);
}

function onewayship() {
    $('#oneway').removeClass('opt-inactive');
    $('#returnship').addClass('opt-inactive');
    $('#shipOpt').val(1);
    $('.retele').prop("disabled", true);
    $('.retShippment').addClass('hide');
    $('#return-form').hide();
}

function returnShip() {
    $('#returnship').removeClass('opt-inactive');
    $('#oneway').addClass('opt-inactive');
    $('#shipOpt').val(2);
    $('.retele').prop("disabled", false);
    $('.retShippment').removeClass('hide');
    $('#return-form').show();
}

function samePickupAddress() {
    var startDate = new Date();
    var PlaceVal = $("input[name='place-dropoff']:checked").val();
    $("input[name='place-ret-pickup'][value='"+PlaceVal+"']").prop('checked',true);
    if($('#is_same_pickup_addrs').is(':checked')){
        $('#retccp_pickup_region').val($('#ccd_dropoff_region').val());
        $('#retccp_company_name').val($('#ccd_company_name').val());
        $('#retccp_contact_name').val($('#ccd_contact_name').val());
        $('#retccp_conatct_phone').val($('#ccd_conatct_phone').val());
        $('#retccp_address').val($('#ccd_address').val());
        $('#retccp_suburb').val($('#ccd_suburb').val());
        $('#retccp_postcode').val($('#ccd_postcode').val());
        $('#retfromDate').data({date: startDate});
        $('#retfromDate').datepicker('update');
        $('#retfromDate').datepicker().children('input').val(startDate);
        calculateTransitDays(2);
    }else{
        $('.place-ret-pickup').prop('checked',false);
        $('#retccp_pickup_region').val('');
        $('#retccp_company_name').val('');
        $('#retccp_contact_name').val('');
        $('#retccp_conatct_phone').val('');
        $('#retccp_address').val('');
        $('#retccp_suburb').val('');
        $('#retccp_postcode').val('');
        calculateTransitDays(2);
    }
}

function checkPlace(placeType,ele) {
    $('.place-'+placeType).prop('checked',false);
    var selectedOptIndex = $('.place-'+placeType).index(ele);
    $(ele).prop('checked',true);
    if(placeType == 'pickup' && $('#is_same_destination_addrs').is(':checked')){
        $('.place-ret-dropoff').prop('checked',false);
        $('.place-ret-dropoff').eq(selectedOptIndex).prop('checked',true);
    }else if(placeType == 'dropoff' && $('#is_same_pickup_addrs').is(':checked')){
        $('.place-ret-pickup').prop('checked',false);
        $('.place-ret-pickup').eq(selectedOptIndex).prop('checked',true);
    }
    if($(ele).val() == '4'){
        $('#infomodal .modal-title').html('Info');
        $('#info-msg').html('All residential deliveries require signature on delivery to ensure insurance is not waived.');
        $('#infomodal').modal('show');
    }
}

function showPickupInfo() {
    $('#pickupInfo').modal('show');
}

function sameDestinationAddress() {
    var PlaceVal = $("input[name='place-pickup']:checked").val();
    $("input[name='place-ret-dropoff'][value='"+PlaceVal+"']").prop('checked',true);
    if($('#is_same_destination_addrs').is(':checked')){
        $('#retccd_dropoff_region').val($('#ccp_pickup_region').val());
        $('#retccd_company_name').val($('#ccp_company_name').val());
        $('#retccd_contact_name').val($('#ccp_contact_name').val());
        $('#retccd_conatct_phone').val($('#ccp_conatct_phone').val());
        $('#retccd_address').val($('#ccp_address').val());
        $('#retccd_suburb').val($('#ccp_suburb').val());
        $('#retccd_postcode').val($('#ccp_postcode').val());
        calculateTransitDays(2);
    }else{
        $('.place-ret-dropoff').prop('checked',false);
        $('#retccd_dropoff_region').val('');
        $('#retccd_company_name').val('');
        $('#retccd_contact_name').val('');
        $('#retccd_conatct_phone').val('');
        $('#retccd_address').val('');
        $('#retccd_suburb').val('');
        $('#retccd_postcode').val('');
        calculateTransitDays(2);
    }
}
function gotopage(url) {
    window.location.href=url;
}
function getVoucherCodeDiscount() {
        var offercode = $('#voucher_code').val();
        if(offercode && offercode != ""){
            var jdata = {
                offercode: offercode,
                functionname: 'getCCOfferDetails'
            }
            $.ajax({
                datatype: "json",
                url: siteRelPath+"callmethodbyrequest",
                type: "POST",
                cache: false,
                data: jdata,
                success: function (html) {
                    var htmldata = '';
                    if (html[0]) {
                        $('#voucher-message').removeClass('alert-danger');
                        $('#voucher-message').addClass('alert-success');
                        if(html[0]['offer_percntg']>0){
                            $('#voucher-message').html('Hooray! You will receive a discount of '+html[0]['offer_percntg']+'% off your orders.');
                        }else{
                            $('#voucher-message').html('Hooray! You will receive a flat discount of $'+html[0]['offer_amnt']+' off your orders.');
                        }
                    } else {
                        $('#voucher-message').removeClass('alert-success');
                        $('#voucher-message').addClass('alert-danger');
                        $('#voucher-message').html('Invalid voucher code.');
                    }
                }
            });
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

function  calculateTransitDays(opt) {
    var pickup = 0;
    var dropoff = 0;
    if(opt == 1){
        pickup = $('#ccp_pickup_region').val();
        dropoff = $('#ccd_dropoff_region').val();
    }else if(opt == 2){
        pickup = $('#retccp_pickup_region').val();
        dropoff = $('#retccd_dropoff_region').val();
    }
    if(pickup > 0 && dropoff > 0){
        var jdata = {
            pickup: pickup,
            drop: dropoff
        }
        $.ajax({
            datatype: "json",
            url: siteRelPath+"calculateccshipping",
            type: "POST",
            cache: false,
            data: jdata,
            success: function (html) {
                var res = JSON.parse(html);
                if(res.code == 200){
                    if(opt == 1){
                        $('#onewayshiptime').html('Estimated shipment time: '+res.days+' business days');
                    }else if(opt == 2){
                        $('#returnshiptime').html('Estimated shipment time: '+res.days+' business days');
                    }
                }
            }
        });
    }
}