/**
 * Created by whiz16 on 01-Jun-17.
 */
function checkfn(prodid) {
    $('#delProdid').val(prodid);
    $('#deleteConfirm').modal('show');
}

function enableDisableFields(formid) {
    if ($('#product_category').val() > 0) {
        $('#' + formid + ' input, #' + formid + ' select, .button').prop('disabled', false);
        var prodtypeid = $('#product_type').val();
        if (prodtypeid == 3) {
            var prodid = $('#prodid').val();
            if (!prodid) {
                prodid = 0;
            }
            $("#group_products").select2("val", "");
            getCategoryProdsByCatId($('#product_category').val(), prodid);
        }
    } else {
        $('#' + formid + ' input, #' + formid + ' select, .button').prop('disabled', true);
        $('#' + formid + ' #product_category').prop('disabled', false);
    }
}

function autoConfigureProducts() {
    $('#loader').show();
    /*var prodid = $('#prodid').val();
     if (!prodid) {
     prodid = 0;
     }*/
    var prodtypeid = $('#product_type').val();
    if (prodtypeid == 4) {
        $('.parentattribs').show();
        $('.simpleattribs').hide();
    } else {
        $('.simpleattribs').show();
        $('.parentattribs').hide();
    }
    $('#loader').hide();
    /*if (prodtypeid == 3) {
     $('.group-prod-sec').show();
     $('.select2-container').css('width', '100%');
     getCategoryProdsByCatId($('#product_category').val(), prodid);
     } else {
     $('.group-prod-sec').hide();
     $('#loader').hide();
     }*/
}

function getCategoryProdsByCatId(Catid, prodid) {
    var jdata = {
        categoryId: Catid,
        prodid: prodid
    }
    $.ajax({
        datatype: "json",
        url: "/getCategoryProds",
        type: "POST",
        cache: false,
        data: jdata,
        success: function (html) {
            var htmldata = '';
            if (html[0]) {
                $('#group_products')
                    .find('option')
                    .remove();
                $.each(html, function (key, value) {
                    $('#group_products').append($('<option>', {
                        value: value.id,
                        text: value.name
                    }));
                });
                $('#loader').hide();
            } else {
                $('#loader').hide();
                alert('No products found.');

            }
        }
    });
}

function addParentAttribute(selectedVal, totalattribs) {
    if (!totalattribs) {
        totalattribs = $('#totalattribs').val();
        totalattribs = parseInt(totalattribs) + 1;
        $('#totalattribs').val(totalattribs);
    }

    $.ajax({
        datatype: "json",
        url: siteRelPath+"getAttributes",
        type: "GET",
        cache: false,
        success: function (html) {
            var htmldata = '';
            if (html[0]) {
                htmldata = '<select class="form-control attribNo' + totalattribs + '" name="attribNo' + totalattribs + '">' +
                    '<option value="">Select attributes for sets</option> ';
                $.each(html, function (key, value) {
                    htmldata += '<option value="' + value.id + '" ' + (selectedVal && selectedVal == value.id ? "selected" : "") + '>' + value.attrib_name + '</option>';
                });
                htmldata += '</select><div class="clmr"></div>';
                $(htmldata).insertBefore('#addAttribs');
                $('#loader').hide();
            } else {
                $('#loader').hide();
                alert('No attributes found.');

            }
        }
    });
}

function checkOptionChecked(ele, inpId, othInp) {
    if ($(ele).is(':checked')) {
        $(ele).val(1);
        $('#' + inpId).attr('readonly', false);
        $('.' + othInp).hide();
        $('.' + inpId).show();
    } else {
        $('#' + inpId).attr('readonly', true);
        $(ele).val(0);
        $('.' + inpId).hide();
        $('.' + othInp).show();
    }
}

function setMaxLimit(ele) {
    if ($(ele).val() > 0) {
        $('#sale_price').attr('max', $(ele).val());
        $('#rent_price').attr('max', $(ele).val());
    }
}

function setAttribVal(attribOptId, FieldId, ele) {
    $('#loader').show();
    $('#' + FieldId).val(attribOptId);
    var values = [];
    $("input[name='attr[]']").each(function () {
        if(attribOptId == 59 && $(this).val() == 27){
            values.push(28);
        }else if(attribOptId == 60 && $(this).val() == 28){
            values.push(27);
        }else {
            values.push($(this).val());
        }
    });
    $('.' + FieldId).removeClass('active');
    $(ele).addClass('active');
    if(attribOptId == 59){
        $('.attrib-8:first').removeClass('active');
        $('.attrib-8:last').addClass('active');
    }else if(attribOptId == 60){
        $('.attrib-8:last').removeClass('active');
        $('.attrib-8:first').addClass('active');
    }
    var jdata = {
        filterArr: values,
        fromDate: $('#fromDate').val(),
        toDate: $('#toDate').val(),
        attribOptId: attribOptId
    }
    $.ajax({
        datatype: "json",
        url: siteRelPath+"filterProducts",
        type: "POST",
        cache: false,
        data: jdata,
        success: function (html) {
            var htmldata = '';
            $('.product-listing').empty();
            if (html[0]) {
                $.each(html, function (objkey, objvalue) {
                    var ArrCount = objvalue.length;
                    htmldata += '<div class="col-xs-12 col-sm-6 col-md-4">' +
                        '<div class="product-panel clearfix">' +
                        '<h3>' + objvalue[parseInt(ArrCount) - 1]["parent-prod-name"] + '</h3>' +
                        '<ul>' +
                        '<span id="hid-desc-'+objvalue[parseInt(ArrCount) - 1]["parent-prod-id"]+'" class="hidden-desc">'+objvalue[parseInt(ArrCount) - 1]["parent-prod-description"]+'</span>' +
                        '</ul>' +
                        '<div class="row">' +
                       '<div class="col-xs-12">' +
                       '<img src="/product_img/' + objvalue[parseInt(ArrCount) - 1]["parent-prod-feat_img"] + '" alt=""/>' +
                       '</div>' +
                        /*'<div class="col-xs-8">' +
                        '<p>' +
                        (objvalue[parseInt(ArrCount) - 1]["parent-prod-description"].length > 200 ? objvalue[parseInt(ArrCount) - 1]["parent-prod-description"].substring(0, 200) + '...' : objvalue[parseInt(ArrCount) - 1]["parent-prod-description"]) + '</p>' +
                        '</div>' +*/
                        '</div>' +
                        '<span>&nbsp;</span>' +
                            '<div class="clearfix">'+
                        '<a href="javascript:void(0);"><h3>Handicap: '+objvalue[parseInt(ArrCount) - 1]["parent-prod-attrib-handicap"]+
                        '</h3><br />'+
                        '<a href="javascript:void(0);" onclick="showProdDets(\''+objvalue[parseInt(ArrCount) - 1]["parent-prod-name"]+'\',\''+objvalue[parseInt(ArrCount) - 1]["parent-prod-id"]+'\',false,\''+(objvalue[parseInt(ArrCount) - 1]["parent-prod-feat_img"]?objvalue[parseInt(ArrCount) - 1]["parent-prod-feat_img"]:'commingsoon.png')+'\');"><h3>View More'+
                    '</h3>'+
                    '</a>'+
                    '</div>'+
                        '<div class="bottom-btn clearfix">' +
                        '<div class="btn-group number-spinner">' +
                        '<span class="input-prepend data-dwn">' +
                        '<button class="btn btn-default left-btn glyphicon qty-decrease glyphicon-play" data-dir="dwn" onclick="quantityCounter(\'qty-decrease\',\'qty-' + objvalue[parseInt(ArrCount) - 1]["parent-prod-id"] + '\');"></button>' +
                        '</span>';
                    for (var i = 0; i < ArrCount - 1; i++) {
                        htmldata += '<input type="hidden" name="childprodid-' + objvalue[parseInt(ArrCount) - 1]["parent-prod-id"] + '[]" value="' + objvalue[i]["id"] + '" />';
                    }
                    htmldata += '<input type="text" id="quantity-' + objvalue[parseInt(ArrCount) - 1]["parent-prod-id"] + '" name="quantity-' + objvalue[parseInt(ArrCount) - 1]["parent-prod-id"] + '" class="form-control qty-' + objvalue[parseInt(ArrCount) - 1]["parent-prod-id"] + '  input-box text-center" value="1" min="1" max="' + objvalue[parseInt(ArrCount) - 1]["parent-prod-quantity"] + '" style="max-width:100px;">' +
                        '<span class="input-append data-up">' +
                        '<button class="btn btn-default glyphicon qty-increase glyphicon-play" data-dir="up" onclick="quantityCounter(\'qty-increase\',\'qty-' + objvalue[parseInt(ArrCount) - 1]["parent-prod-id"] + '\');"></button>' +
                        '</span>' +
                        '</div>' +
                        '<button class="add-cart" onclick="addToCart(\'' + objvalue[parseInt(ArrCount) - 1]["parent-prod-id"] + '\',\'childprodid-' + objvalue[parseInt(ArrCount) - 1]["parent-prod-id"] + '\',\'quantity-' + objvalue[parseInt(ArrCount) - 1]["parent-prod-id"] + '\')">Add to Cart</button>' +
                        '</div>' +
                        '</div></div>';
                });
                $('.product-listing').html(htmldata);
            } else {
                $('.product-listing').html('<h2>No Clubs Available</h2><p>There are no sets available to match your current search. Please try another shaft type or amend your collection and drop off dates for further availability.</p>');
            }

            $('#loader').hide();
        }
    });
}

function quantityCounter(counterClass, inputClass) {
    var inputVal = $('.' + inputClass).val();
    var maxQty = $('.' + inputClass).attr('max');
    var minQty = $('.' + inputClass).attr('min');
    if (counterClass == 'qty-increase') {
        if (inputVal >= maxQty) {
            $('.' + inputClass).val(inputVal);
        } else {
            $('.' + inputClass).val(parseInt(inputVal) + 1);
        }
    } else if (counterClass == 'qty-decrease') {
        if (inputVal <= minQty) {
            $('.' + inputClass).val(inputVal);
        } else {
            $('.' + inputClass).val(parseInt(inputVal) - 1);
        }
    }
}

function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

function addToCart(parentProdId, childProdArr, quantityid) {

    $('#loader').show();
    var childProdsArr = [];
    $("input[name='" + childProdArr + "[]']").each(function () {
        childProdsArr.push($(this).val());
    });

    var jdata = {
        product_idArr: childProdsArr,
        quantity: $('#' + quantityid).val(),
        dt_book_from: $('#fromDate').val(),
        dt_book_upto: $('#toDate').val(),
        parent_prod_id: parentProdId,
        order_reference_id: $('#order_reference_id').val(),
        state_id: $('#state_id').val()
    }

    $.ajax({
        datatype: "json",
        url: siteRelPath+"addtocart",
        type: "POST",
        cache: false,
        data: jdata,
        success: function (html) {
            $('#infomodal .modal-title').html('Info');
            $('#info-msg').html('Product successfully added to your cart.');
            var h = ($('.modal-dialog').height()) / 2;
            $('.modal-dialog').css({'top': '50%', 'margin-top': '-' + h + 'px'});
            $('#infomodal').modal('show');
            $('#loader').hide();
        }
    });

}

function increaseQuantity(AvailProdArr, productType, parentProdId) {
    $('#loader').show();
    var availableProdsArr = [];
    $("input[name='Avail-prods-" + AvailProdArr + "[]']").each(function () {
        availableProdsArr.push($(this).val());
    });
    if (productType == '5') {
        if (availableProdsArr.length > 0) {
            var jdata = {
                product_idArr: availableProdsArr,
                quantity: 1,
                dt_book_from: $('#fromDate').val(),
                dt_book_upto: $('#toDate').val(),
                parent_prod_id: parentProdId,
                order_reference_id: $('#order_reference_id').val(),
                state_id: $('#state_id').val()
            }

            $.ajax({
                datatype: "json",
                url: siteRelPath+"addtocart",
                type: "POST",
                cache: false,
                data: jdata,
                success: function (html) {
                    var Pagename = window.location.pathname;
                    $('#infomodal .modal-title').html('Info');
                    $('#info-msg').html('Cart updated successfully.');
                    var h = ($('.modal-dialog').height()) / 2;
                    $('.modal-dialog').css({'top': '50%', 'margin-top': '-' + h + 'px'});
                    $('#infomodal').modal('show');
                    $('#infomodal button').attr("onclick","addremoveInsuranceToOrder('"+$('#order_reference_id').val()+"', '1', '.."+Pagename+"', '1')");
                    $('#loader').hide();
                }
            });
        } else {
            $('#actionInfo .modal-title').html('Info');
            $('#actionInfo .modal-body p').html('No clubs are available to add in your basket.');
            var h = ($('#actionInfo .modal-dialog').height()) / 2;
            $('#actionInfo .modal-dialog').css({'top': '50%', 'margin-top': '-' + h + 'px'});
            $('#info-container').modal('show');
            $('#loader').hide();
        }
    }else{
        var availableProdsArr = [];
        $("input[name='prods-" + AvailProdArr + "[]']").each(function () {
            availableProdsArr.push($(this).val());
        });
        var jdata = {
            product_idArr: availableProdsArr,
            quantity: $('#qtyval').val(),
            order_reference_id: $('#order_reference_id').val()
        }

        $.ajax({
            datatype: "json",
            url: siteRelPath+"increase-prod-qty",
            type: "POST",
            cache: false,
            data: jdata,
            success: function (html) {
                if(html['status'] == 'SUCCESS'){
                    $('#infomodal .modal-title').html('Info');
                    $('#info-msg').html('Cart updated successfully.');
                    var h = ($('.modal-dialog').height()) / 2;
                    $('.modal-dialog').css({'top': '50%', 'margin-top': '-' + h + 'px'});
                    $('#infomodal').modal('show');
                    $('#loader').hide();
                }else{
                    $('#infomodal .modal-title').html('Attention');
                    $('#info-msg').html('No more product is available. Please try after some time.');
                    var h = ($('.modal-dialog').height()) / 2;
                    $('.modal-dialog').css({'top': '50%', 'margin-top': '-' + h + 'px'});
                    $('#infomodal').modal('show');
                    $('#loader').hide();
                }
            }
        });
    }
}

function removeSet(orderRefId,Prodid) {
    $('#loader').show();
        var addedProdsArr = [];
        addedProdsArr.push(Prodid);

        if (addedProdsArr.length > 0) {
            var jdata = {
                product_idArr: addedProdsArr,
                order_reference_id: orderRefId,
                setcont: $('#setcount').val()
            }
        } else {
            $('#actionInfo .modal-title').html('Info');
            $('#actionInfo .modal-body p').html('Club set cannot be removed from your basket. ');
            var h = ($('#actionInfo .modal-dialog').height()) / 2;
            $('#actionInfo .modal-dialog').css({'top': '50%', 'margin-top': '-' + h + 'px'});
            $('#info-container').modal('show');
            $('#loader').hide();
        }
    $.ajax({
        datatype: "json",
        url: siteRelPath + "remove-from-cart",
        type: "POST",
        cache: false,
        data: jdata,
        success: function (html) {
            var Pagename = window.location.pathname;
            $('#infomodal .modal-title').html('Info');
            $('#info-msg').html('Cart updated successfully.');
            var h = ($('.modal-dialog').height()) / 2;
            $('.modal-dialog').css({'top': '50%', 'margin-top': '-' + h + 'px'});
            $('#infomodal button').attr("onclick", "addremoveInsuranceToOrder('" + $('#order_reference_id').val() + "', '1', '.." + Pagename + "', '1')");
            $('#infomodal').modal('show');
            $('#loader').hide();
        }
    });
}

function decreaseQuantity(BasketProdArr, productType, quantity) {
    $('#loader').show();
    if(quantity>1){
        var addedProdsArr = [];
        var i = 0;
        $("input[name='" + BasketProdArr + "[]']").each(function () {
            if (i == 0) {
                addedProdsArr.push($(this).val());
            }
            i++;
        });
        if (addedProdsArr.length > 0) {
            var jdata = {
                product_idArr: addedProdsArr,
                order_reference_id: $('#order_reference_id').val(),
                setcont: $('#setcount').val()
            }
        } else {
            $('#actionInfo .modal-title').html('Info');
            $('#actionInfo .modal-body p').html('Club set cannot be removed from your basket. ');
            var h = ($('#actionInfo .modal-dialog').height()) / 2;
            $('#actionInfo .modal-dialog').css({'top': '50%', 'margin-top': '-' + h + 'px'});
            $('#info-container').modal('show');
            $('#loader').hide();
        }
        if (productType == '5') {
                $.ajax({
                    datatype: "json",
                    url: siteRelPath+"remove-from-cart",
                    type: "POST",
                    cache: false,
                    data: jdata,
                    success: function (html) {
                        var Pagename = window.location.pathname;
                        $('#infomodal .modal-title').html('Info');
                        $('#info-msg').html('Cart updated successfully.');
                        var h = ($('.modal-dialog').height()) / 2;
                        $('.modal-dialog').css({'top': '50%', 'margin-top': '-' + h + 'px'});
                        $('#infomodal button').attr("onclick","addremoveInsuranceToOrder('"+$('#order_reference_id').val()+"', '1', '.."+Pagename+"', '1')");
                        $('#infomodal').modal('show');
                        $('#loader').hide();
                    }
                });
        }else{
            $.ajax({
                datatype: "json",
                url: siteRelPath+"decrease-prod-qty",
                type: "POST",
                cache: false,
                data: jdata,
                success: function (html) {
                    if(html['status'] == 'SUCCESS'){
                        $('#infomodal .modal-title').html('Info');
                        $('#info-msg').html('Cart updated successfully.');
                        var h = ($('.modal-dialog').height()) / 2;
                        $('.modal-dialog').css({'top': '50%', 'margin-top': '-' + h + 'px'});
                        $('#infomodal').modal('show');
                        $('#loader').hide();
                    }else{
                        $('#infomodal .modal-title').html('Error');
                        $('#info-msg').html('Something goes wrong. Please try again later.');
                        var h = ($('.modal-dialog').height()) / 2;
                        $('.modal-dialog').css({'top': '50%', 'margin-top': '-' + h + 'px'});
                        $('#infomodal').modal('show');
                        $('#loader').hide();
                    }

                }
            });
        }
    }else{
        $('#infomodal .modal-title').html('Attention');
        $('#info-msg').html('You can not decrease product quantity. Minimum product quantity allowed for ordering is 1 per product added to the basket.');
        var h = ($('.modal-dialog').height()) / 2;
        $('.modal-dialog').css({'top': '50%', 'margin-top': '-' + h + 'px'});
        $('#infomodal').modal('show');
        $('#loader').hide();
    }

}

function redirectToUrl(redirectUrl) {
    if(redirectUrl == 'clubsearch'){
        localStorage.setItem('scrollToDiv','1');
    }else {
        localStorage.removeItem('scrollToDiv');
    }
    window.location.href = siteRelPath + redirectUrl;
}

function scrollToDivOnHirePage() {
    if(localStorage.getItem('scrollToDiv') == '1'){
        localStorage.removeItem('scrollToDiv');
        var headerHeight = $("#header").height();
        $('html, body').animate({
            scrollTop: $("#first-hire-block").offset().top - headerHeight
        }, 500);
    }else{
        localStorage.removeItem('scrollToDiv');
    }
}

function removeProd() {
    $('#loader').show();
    var order_reference_id = $('#order-ref-id').val();
    var prodArr = $('#Prod-Arr').val();
    var ProdsArr = [];
    $("input[name='" + prodArr + "[]']").each(function () {
        ProdsArr.push($(this).val());
    });
    var jdata = {
        order_reference_id: order_reference_id,
        product_idArr: ProdsArr
    }
    $.ajax({
        datatype: "json",
        url: siteRelPath+"remove-from-cart",
        type: "POST",
        cache: false,
        data: jdata,
        success: function (html) {
            var Pagename = window.location.pathname;
            Pagename = Pagename.substring(1, Pagename.length);
            $('#infomodal .modal-title').html('Info');
            $('#info-msg').html('Product successfully removed from your cart.');
            var h = ($('.modal-dialog').height()) / 2;
            $('.modal-dialog').css({'top': '50%', 'margin-top': '-' + h + 'px'});
            if(html['dataarr']){
                if(html['dataarr'][0]['insurance_amnt']>0){
                    $('#infomodal button').attr("onclick","addremoveInsuranceToOrder('"+order_reference_id+"', '1', '../"+Pagename+"', '1')");
                }
            }
            $('#infomodal').modal('show');
            $('#loader').hide();
        }
    });
}

function checkandproceed(redirectPage, insuranceFlag) {
    $('#loader').show();
    if (!insuranceFlag) {
        insuranceFlag = 1;
    }
    var jdata = {
        orderRefId: $('#order_reference_id').val()
    }
    $.ajax({
        datatype: "json",
        url: siteRelPath+"getcartprodids",
        type: "POST",
        cache: false,
        data: jdata,
        success: function (html) {
            if (html[0]) {
                var totalProds = html.length;
                var prodcount = 0;
                var i = 1;
                var timeval = 300;
                totaltimeVal = 0;
                checkForAvailProduct(html, $('#fromDate').val(), $('#toDate').val(), 0, redirectPage, insuranceFlag);
            } else {
                alert('No data');
            }
        }
    });
}

function checkForAvailProduct(productidArr, fromdate, todate, extendedDays, redirectPage, insuranceFlag) {
    var jdata = {
        childProdIdArr: productidArr,
        fromDate: fromdate,
        toDate: todate,
        extendedDays: extendedDays,
        functionname: 'checkAvailProdsForBooking'
    }
    var returndata = false;
    $.ajax({
        datatype: "json",
        url: siteRelPath+"callmethodbyrequest",
        type: "POST",
        cache: false,
        data: jdata,
        success: function (html) {
            if (html[0]) {
                getParentProdByChildId(html);
                // alert(0);
            } else {
                addremoveInsuranceToOrder($('#order_reference_id').val(), insuranceFlag, redirectPage);
                // returndata = true;
            }
        }
    });
}

function getGift(giftProdId) {
    var jdata = {
        productids: giftProdId,
        functionname: 'getGiftProdDetails'
    }
    var gifthtml = '';
    $.ajax({
        datatype: "json",
        url: siteRelPath+"callmethodbyrequest",
        type: "POST",
        cache: false,
        data: jdata,
        success: function (html) {
            if(html){
                $.each(html,function (key,val) {
                    gifthtml +='<div class="col-md-12 clearfix">'+
                            '<h3 class="gift-prodname">'+val['name']+'</h3>'+
                        '<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12" id="gift-img">'+
                            '<img src="/product_img/'+val['feat_img']+'" />'+
                        '</div>'+
                        '<div class="col-lg-8 col-md-8 col-sm-6 col-xs-12" id="gift-desc">'+
                            '<p>'+val['description']+'</p>'+
                        '</div>'+
                        '</div>';

                });
            }
            $('#allgifts').html(gifthtml);
            $('#giftinfomodal').modal('show');
        }
    });
}

function getParentProdByChildId(ChildProdIdArr) {
    var jdata = {
        childProdIdArr: ChildProdIdArr,
        functionname: 'getParentProductByChildId'
    }
    $.ajax({
        datatype: "json",
        url: siteRelPath+"callmethodbyrequest",
        type: "POST",
        cache: false,
        data: jdata,
        success: function (html) {
            console.log(html);
            if (html[0]) {
                $('#infomodal .modal-title').html('Info');
                $('#info-msg').html('<b>' + html[0] + '</b> is out of stock. Please remove this set from your cart to proceed for ordering or wait for the availability of the set.');
                var h = ($('.modal-dialog').height()) / 2;
                $('.modal-dialog').css({'top': '50%', 'margin-top': '-' + h + 'px'});
                $('#infomodal').modal('show');
                $('#loader').hide();
                return false;
            } else {
                return false;
            }
        }
    });
}

function addremoveInsuranceToOrder(orderRefId, flag, redirectPage, loaderflag) {
    /*flag == 1 for add
     flag == 2 for remove*/
    if (loaderflag) {
        $('#loader').show();
    }
    if (redirectPage == 'shipping') {
        redirectToUrl(redirectPage);
    } else {
        var jdata = {
            orderRefId: orderRefId,
            flag: flag
        }
        $.ajax({
            datatype: "json",
            url: siteRelPath+"addremoveinsurance",
            type: "POST",
            cache: false,
            data: jdata,
            success: function (html) {
                if (html) {
                    if (loaderflag) {
                        if (flag == 2) {
                            redirectToUrl(redirectPage);
                        } else if (flag == 1) {
                            redirectToUrl(redirectPage);
                        }
                    } else {
                        redirectToUrl(redirectPage);
                    }
                    $('#loader').hide();
                } else {
                    $('#loader').hide();
                    return false;
                }
            }
        });
    }
}

function removeCartProductConfirmation(order_reference_id, prodArr) {
    $('#order-ref-id').val(order_reference_id);
    $('#Prod-Arr').val(prodArr);
    var h = ($('.modal-dialog').height()) / 2;
    $('.modal-dialog').css({'top': '50%', 'margin-top': '-' + h + 'px'});
    $('#deleteConfirm').modal('show');
}

function getOfferCodeDiscount() {
    var offercode = $('#offer-code').val();
    $('#offer').val(offercode);
    var jdata = {
        offercode: offercode,
        functionname: 'getOfferDetails'
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
                var subtotal = $('#totalprice').val();
                var insurance = $('#insurance').val();
                var handling = $('#handling').val();
                subtotal = (parseFloat(subtotal) - parseFloat(insurance)) - parseFloat(handling);
                var discount = html[0]['offer_percntg'];
                var TotalDiscountAmount = parseFloat(subtotal) * parseFloat(discount) * 0.01
                subtotal = parseFloat(subtotal) - parseFloat(TotalDiscountAmount) + parseFloat(insurance) + parseFloat(handling);
                $('#off-amount').html('Offer code applied successfully. You have received $' + parseFloat(TotalDiscountAmount).toFixed(2) + ' discount on your order.');
                // subtotal = parseFloat(subtotal).toFixed(2) + parseFloat(insurance).toFixed(2) + parseFloat(handling).toFixed(2);
                $('#view-totalprice').html('$' + parseFloat(subtotal).toFixed(2));
                $('#loader').hide();
            } else {
                $('#loader').hide();
                alert('No discount found.');
            }
        }
    });
}

function getDateVal(dateval) {
    var validDate = true;
    var formatedDate = new Date(dateval);
    if(formatedDate == 'Invalid Date'){
        validDate = false;
    }
    if(validDate){
        var month = formatedDate.getMonth()+1;
        var day = formatedDate.getDate();

        var output = ((''+month).length<2 ? '0' : '') + month + '/' +
            ((''+day).length<2 ? '0' : '') + day+'/'+formatedDate.getFullYear();
        return output;
    }else{
        return false;
    }
}

function showDP() {
    var DateTo = new Date($('#fromDate').val());
    alert(DateTo);
    DateTo.setDate(DateTo.getDate());
    alert(DateTo);
    $('#toDate').datepicker({
        startDate: DateTo+1
    });
}

function removeOrder() {
    $('#loader').show();
    var jdata = {
        order_reference_id: $('#orderrefid').val(),
        dt_book_from: $('#fromDate').val(),
        dt_book_upto: $('#toDate').val(),
        states: $('#states').val()
    }
    $.ajax({
        datatype: "json",
        url: siteRelPath+"remove-order",
        type: "POST",
        cache: false,
        data: jdata,
        success: function (html) {
                redirectToUrl('clubsearch');
        }
    });
}

function setDpInitVals(ele) {
    if($(ele).val()>0){
        $('#fromDate, #toDate').attr('disabled',false);
        var noDays = 3;
        if($(ele).val() == 2 || $(ele).val() == 3){
            noDays = 4;
        }else if($(ele).val() == 4){
            noDays = 7;
        }else if($(ele).val() == 5){
            noDays = 5;
        }
        var date = new Date();
        var today = date.getDay();
        if(noDays < 4 && today == 5){
            date.setDate(date.getDate()+4);
        }else{
            date.setDate(date.getDate()+noDays);
        }

        $('#fromDate').datepicker('setStartDate', date);
        $("#fromDate").datepicker({
            todayBtn:  1,
            autoclose: true,
        }).on('changeDate', function (selected) {
            var minDate = new Date(selected.date.valueOf());
            minDate.setDate(minDate.getDate()+2)
            $('#toDate').datepicker('setStartDate', minDate);
        });

        $("#toDate").datepicker({

            autoclose: true,
        })
            .on('changeDate', function (selected) {
                var minDate = new Date(selected.date.valueOf());
                $('#fromDate').datepicker('setStartDate', date);
            });
    }else{
        $('#fromDate, #toDate').attr('disabled',true);
    }
}

function showProdDets(prodname,prodid,hide,img) {
    $('#loader').show();
    var jdata = {
        functionname: 'getProductGalleryImgs',
        product_id: prodid
    }
    $.ajax({
        datatype: "json",
        url: siteRelPath+"callmethodbyrequest",
        type: "POST",
        cache: false,
        data: jdata,
        success: function (html) {
            if(hide == '1'){
                $('.hideupsell').hide();
            }
            var imagesArr = '';
            $.each(html,function (key,val) {
                imagesArr += '<div class="grid-item">'+
                    '<img src="storage'+StorageImageRelPath+val[1]+'" />'+
                    (val[2]?'<div class="btn-success" style="padding: 4px">'+val[2]+'</div>':'')+
                '</div>';

            });
            $('#prodinfomodal .modal-title').html(prodname+' - Info');
            $('#imgsec').html('<img src="/product_img/'+(img?img:'commingsoon.png')+'" />');
            $('#prod-desc-popup').html($('#hid-desc-'+prodid).html());
            $('#prodinfomodal .modal-body .masonry').html(imagesArr);
            var h = ($('.modal-dialog').height()) / 2;
            $('.modal-dialog').css({'top': '30%', 'margin-top': '-' + h + 'px'});
            $('#prodinfomodal').modal('show');
            $('#loader').hide();
        }
    });
}

function  calculateShipping(setcount) {
    var pickup = $('#pickup_postal_code').val();
    var dropoff = $('#delvr_postal_code').val();
    var jdata = {
        pickup: pickup,
        dropoff: dropoff
    }
    $.ajax({
        datatype: "json",
        url: siteRelPath+"calculateshipping",
        type: "POST",
        cache: false,
        data: jdata,
        success: function (html) {
            var shippingPrice = parseFloat(html)*setcount;
            if(shippingPrice > 0){
                shippingPrice = shippingPrice.toFixed(2);
                var SubTotal = $('#subtotal').val();
                var multisetDis = $('#msd').val();
                var partnerDis = $('#pdis').val();
                var Insurance = $('#insu').val();
                $('#handling-price').html('$'+shippingPrice);
                $('#handling').val(shippingPrice);
                var HandlingVal = $('#handling').val();
                var TotalPrice = $('#totalprice').val();
                TotalPrice = parseFloat(SubTotal)+parseFloat(Insurance)+parseFloat(HandlingVal)-parseFloat(multisetDis)-parseFloat(partnerDis);
                $('#view-totalprice').html('$'+TotalPrice.toFixed(2));
                $('#totalprice').val(TotalPrice.toFixed(2));
                $('#info-msg').html('Based upon the number of sets and your location(s) $'+shippingPrice+' will be charged as a handling and delivery fee.');
            }else{
                $('#info-msg').html('Delivery is free to this location.');
            }
            if(pickup && dropoff){
                $('#infomodal').modal('show');
            }
        }
    });
}
function checkPlace(placeType,ele) {
    $('.place-'+placeType).prop('checked',false);
    var selectedOptIndex = $('.place-'+placeType).index(ele);
    $(ele).prop('checked',true);
    if(placeType == 'dropoff' && $('#is_same_pickup_addrs').is(':checked')){
        $('.place-pickup').prop('checked',false);
        $('.place-pickup').eq(selectedOptIndex).prop('checked',true);
    }
    if($(ele).val() == '3'){
        $('#infomodal .modal-title').html('Acknowledgement');
        $('#info-msg').html('I acknowledge that not all golf courses will hold the clubs before they are picked up, and I am happy for the sweet spot to contact me to arrange an alternative delivery location if required.');
        $('#infomodal').modal('show');
    }
}

function pickupDetails(dropoff,pickup) {
    if($('#is_same_pickup_addrs').is(':checked')){
        $('#'+pickup).val($('#'+dropoff).val());
    }
}

function  removeBanner() {
    var bannerid = $('#bannerid').val();
    var jdata = {
        bannerid: bannerid
    }
    $.ajax({
        datatype: "json",
        url: siteRelPath+"deleteBanner",
        type: "POST",
        cache: false,
        data: jdata,
        success: function (html) {
            if(html == 'SUCCESS'){
                $('#banner-rec-id-'+bannerid).remove();
                $('#message_banner2').html('');
                $('#message_banner2').html("Selected banner has been deleted successfully.");
                $('#bannerDeleteStatus').modal('show');
            }else{
                $('#message_banner2').html('');
                $('#message_banner2').html("Something goes wrong while deleting banner. Please try again.");
                $('#bannerDeleteStatus').modal('show');
            }
        }
    });
}

function clearFilterCart() {
    var jdata = {
        clearfilter: 1
    }
    $.ajax({
        datatype: "json",
        url: siteRelPath+"clearfilter",
        type: "POST",
        cache: false,
        data: jdata,
        success: function (html) {
            redirectToUrl('clubsearch');
        }
    });
}

function updateCaption(id){
    $('#loader').show();
    var jdata = {
        imgid: id,
        imgcaption: $('#gal-caption-'+id).val()
    }
    $.ajax({
        datatype: "json",
        url: siteRelPath+"savecaption",
        type: "POST",
        cache: false,
        data: jdata,
        success: function (html) {
            $('#loader').hide();
        }
    });
}

function getsiteFooter(siteUrl) {
    $('#loader').show();
    var jdata = {
        siteUrl: siteUrl
    }
    $.ajax({
        datatype: "json",
        url: siteRelPath+"getfooter",
        type: "POST",
        cache: false,
        data: jdata,
        success: function (html) {
            $('#loader').hide();
            if (html) {
                $('.custom-header').html($(html).find('#header').html());
                $('#custom-footer').html($(html).find('#footer').html());
            } else {
                alert('No data');
            }
        }
    });
}

function openTable() {
    if($('.excess-cost').is(":visible")){
        $('.excess-cost').hide();
    }else {
        $('.excess-cost').show();
    }
}

function showhidefilter() {
    if($('.filter-opt').is(":visible")){
        $('.filter-opt').hide();
    }else {
        $('.filter-opt').show();
    }
}

function parseDate(str) {
    var mdy = str.split('/');
    return new Date(mdy[2], mdy[1]-1, mdy[0]);
}

function daydiff(first, second) {
    return Math.round((second-first)/(1000*60*60*24));
}

function getRegions(ele,selectedval) {

    var eleVal = $(ele).val();
    if(eleVal>0){
        // $('#loader').show();
        var jdata = {
            stateid: eleVal
        }
        $.ajax({
            datatype: "json",
            url: siteRelPath+"getregions",
            type: "POST",
            cache: false,
            data: jdata,
            success: function (html) {
                $('#region_id')
                    .find('option')
                    .remove();
                $('#region_id').append($('<option>', {
                    value: 0,
                    text: 'Select Region'
                }))
                $.each(html, function (key, value) {
                    $('#region_id').append($('<option>', {
                        value: value.id,
                        text: value.region
                    }));
                });
                setTimeout(function () {
                    if(selectedval){
                        alert(selectedval);
                        $('#region_id').val(selectedval);
                    }
                },500);
            }
        });
    }
}

function showPayOpt(choice) {
    $('#infomodal .modal-header').html('<button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Payment option</h4>');
    if(choice == 'ON'){
        var htmldata = '<div class="row"><div class="col-md-12"><div class="col-md-6 col-xs-12 col-sm-6 optpay opt1">' +
            '<div class="payopt paydollarimg"><img src="/shop/frontend/images/paydollar_logo.jpg" class="payoptimg" onclick="paymentGateway(\'paydollarform\',\'1\');" /> </div>' +
            '</div>' +
            '<div class="col-md-6 col-xs-12 col-sm-6 optpay opt2">' +
            '<div class="payopt nabimg"><img src="/shop/frontend/images/nab_logo.jpg" class="payoptimg" onclick="paymentGateway(\'nabform\',\'2\');" /> </div>' +
            '</div></div> </div> ';
    }else{
        var htmldata = '<div class="row"><div class="col-md-12"><h2>Coming Soon...</h2></div></div>';
    }
    $('#infomodal .modal-body').html(htmldata);
    $('#infomodal .modal-footer').html('<button type="button" class="btn btn-info frontend-primary-btn" data-dismiss="modal">Close</button>');
    var h = ($('.modal-dialog').height()) / 2;
    $('.modal-dialog').css({'top': '25%', 'margin-top': '-' + h + 'px'});
    $('#infomodal').modal('show');

}

function paymentGateway(formid,payopt) {
    updatePaymentOpt(payopt);
    setTimeout(function () {
        $('#'+formid).submit();
    },400);
}

function updatePaymentOpt(optval) {
    var jdata = {
        optval: optval,
        orderRefId:$('#order_reference_id').val()
    }
    $.ajax({
        datatype: "json",
        url: siteRelPath+"updatepaymentopt",
        type: "POST",
        cache: false,
        data: jdata,
        success: function (html) {

        }
    });
}

function changeProd(prodid) {
    var jdata = {
        prodid: prodid
    }
    $.ajax({
        datatype: "json",
        url: siteRelPath+"getunorderdprods",
        type: "POST",
        cache: false,
        data: jdata,
        success: function (html) {
            $('#change_prod').empty();
            $.each(html, function (key, value) {
                $('#change_prod').append($('<option>', {
                    value: value.id,
                    text: value.name
                }));
            });
            $('#oldprodid').val(prodid);
            $('#infomodal').modal('show');
        }
    });
}

function changeSelectedProduct(orderid) {
    var changeprodId = $('#change_prod').val();
    var oldprodid = $('#oldprodid').val();
    if(changeprodId > 0 && oldprodid > 0){
        var jdata = {
            orderid: orderid,
            productid: changeprodId,
            oldprodid: oldprodid
        }
        $.ajax({
            datatype: "json",
            url: siteRelPath+"changeOrderItem",
            type: "POST",
            cache: false,
            data: jdata,
            success: function (html) {
                $('#infomodal').modal('hide');
                if(html == 'SUCCESS'){
                    $('#info-msg').html('Product Changed successfully.');
                    $('#msg-mod-btn-fail').hide();
                    $('#msg-mod-btn-scs').show();
                }else{
                    $('#info-msg').html('Something goes wrong. Please try again.');
                    $('#msg-mod-btn-fail').show();
                    $('#msg-mod-btn-scs').hide();
                }
                $('#msgmodal').modal('show');
            }
        });
    }else{
        $('#info-msg').html('Something goes wrong. Please try again.');
        $('#msg-mod-btn-fail').show();
        $('#msg-mod-btn-scs').hide();
        $('#msgmodal').modal('show');
    }
}

function redirectToUrl(url) {
    if(url){
        window.location = url;
    }else {
        location.reload();
    }
}

function enableDisableProd(prodid,prodTypeFlag) {
    $('#loader').show();
    var jdata = {
        flag: $('#prod_'+prodid+'_flag').val(),
        prodid: prodid,
        prodtype: prodTypeFlag
    }
    $.ajax({
        datatype: "json",
        url: siteRelPath+"/enableDisableProd",
        type: "POST",
        cache: false,
        data: jdata,
        success: function (html) {
            var htmldata = '';
            if (html == '1') {
                if(jdata.flag == '1'){
                    $('#act-btn-'+prodid).removeClass('btn-success');
                    $('#act-btn-'+prodid).addClass('btn-warning');
                    $('#prod_'+prodid+'_flag').val(0);
                }else{
                    $('#act-btn-'+prodid).removeClass('btn-warning');
                    $('#act-btn-'+prodid).addClass('btn-success');
                    $('#prod_'+prodid+'_flag').val(1);
                }
                $('#loader').hide();
            } else {
                $('#loader').hide();
                alert('Some goes wrong. please try again.');

            }
        }
    });
}