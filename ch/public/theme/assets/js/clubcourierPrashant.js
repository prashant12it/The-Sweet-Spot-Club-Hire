/**
 * Created by HP on 25-09-2018.
 */
function addAnotherBag(){
    var bagcount = parseInt($('#bagcount').val());
    var newBagCount = bagcount+1;
    var addhtml = '<div class="row" id="bag'+newBagCount+'"><div class="form-group col-sm-5"><label>Bag title</label>'+
    '<input type="text" name="bagTitle'+newBagCount+'" value="" placeholder="Bag title"/></div>'+
    '<div class="form-group col-sm-5"><label>Bag type/size</label><select name="bagType'+newBagCount+'">'+
        '<option value="1">Standard bag (123cmx30cmx30cm)</option>'+
    '<option value="2">Large bag (132cmx38cmx30cm)</option>'+
    '</select></div><div class="form-group col-sm-2">'+
        '<button class="btn btn-danger frontend-primary-btn col-md-8 btn-with-label" type="button" onclick="removeBag('+newBagCount+')">Remove bag</button>'+
    '</div></div>';
    $(addhtml).insertBefore('#add-bag');
    $('#bagcount').val(newBagCount);
}

function removeBag(bagCount) {
    $('#bag'+bagCount).remove();
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
    if($('#is_same_pickup_addrs').is(':checked')){
        $('#retccp_pickup_region').val($('#ccd_dropoff_region').val());
        $('#retccp_company_name').val($('#ccd_company_name').val());
        $('#retccp_contact_name').val($('#ccd_contact_name').val());
        $('#retccp_conatct_phone').val($('#ccd_conatct_phone').val());
        $('#retccp_address').val($('#ccd_address').val());
        $('#retccp_suburb').val($('#ccd_suburb').val());
        $('#retccp_postcode').val($('#ccd_postcode').val());
    }else{
        $('#retccp_pickup_region').val('');
        $('#retccp_company_name').val('');
        $('#retccp_contact_name').val('');
        $('#retccp_conatct_phone').val('');
        $('#retccp_address').val('');
        $('#retccp_suburb').val('');
        $('#retccp_postcode').val('');
    }
}

function sameDestinationAddress() {
    if($('#is_same_destination_addrs').is(':checked')){
        $('#retccd_dropoff_region').val($('#ccp_pickup_region').val());
        $('#retccd_company_name').val($('#ccp_company_name').val());
        $('#retccd_contact_name').val($('#ccp_contact_name').val());
        $('#retccd_conatct_phone').val($('#ccp_conatct_phone').val());
        $('#retccd_address').val($('#ccp_address').val());
        $('#retccd_suburb').val($('#ccp_suburb').val());
        $('#retccd_postcode').val($('#ccp_postcode').val());
    }else{
        $('#retccd_dropoff_region').val('');
        $('#retccd_company_name').val('');
        $('#retccd_contact_name').val('');
        $('#retccd_conatct_phone').val('');
        $('#retccd_address').val('');
        $('#retccd_suburb').val('');
        $('#retccd_postcode').val('');
    }
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
                            $('#voucher-message').html('Hooray! You will receive a discount of '+html[0]['offer_percntg']+'% on total amount.');
                        }else{
                            $('#voucher-message').html('Hooray! You will receive a flat discount of $'+html[0]['offer_amnt']+' on total amount.');
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