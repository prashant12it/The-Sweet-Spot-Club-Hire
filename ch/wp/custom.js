var siteRelPath = 'https://www.tssclubhire.com/shop/';

/*Search club*/
jQuery('.datepicker').datepicker({
    format: 'mm/dd/yyyy'
});
jQuery(document).ready(function ($) {
    $('.day').click(function(){
        $('.datepicker').hide();
    });

    var orderrefid = getCookie('order_reference_id');

    if(orderrefid){
        getPreDetOrder(orderrefid);
    }

    $('form').submit(function () {
        var sDate = parseDate($('#fromDate').val());
        var eDate = parseDate($('#toDate').val());
        var osDate = parseDate($('#oldfromdate').val());
        var oeDate = parseDate($('#oldtodate').val());
        var oldState = $('#oldstate').val();
        var state = $('#states').val();
        /*if(state == '4'){
            $('#infomodal .modal-title').html('Special Delivery Condition');
            $('#info-msg').html('Please <a href="mailto:fiachra.mccloskey@gmail.com?Subject=Golf%20club%20hire%20in%20Tasmania">email us</a> directly for golf club hire in <b>Tasmania</b>. ');
            $('#infomodal .modal-footer').html('<button type="button" class="btn btn-info frontend-primary-btn" data-dismiss="modal">Close</button>');
            var h = ($('.modal-dialog').height()) / 2;
            $('.modal-dialog').css({'top': '50%', 'margin-top': '-' + parseFloat(h)+100 + 'px'});
            $('#infomodal').modal('show');
            return false;
        }else{*/
            if ($.trim($('#fromDate').val()) == '' || $.trim($('#fromDate').val()) == 'From') {
                $('#errorMessage').html('<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Enter valid "From" date.');
                $('#dateErorr').show();
                return false;
            } else if ($.trim($('#toDate').val()) == '' || $.trim($('#toDate').val()) == 'To') {
                $('#errorMessage').html('<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Enter valid "TO" date.');
                $('#dateErorr').show();
                return false;
            } else if (state < '6' && parseInt(daydiff(sDate,eDate)) < 1) {
                $('#errorMessage').html('<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Minimum <b>2 Days </b> hiring is allowed for club sets.');
                $('#dateErorr').show();
                return false;
            } else if ((oldState && state != oldState) || (osDate != 'Invalid Date' && osDate != sDate) || (oeDate != 'Invalid Date' && oeDate != eDate)) {
                $('#infomodal .modal-title').html('Alert!');
                $('#info-msg').html('You already have unordered items in your basket. Click <b>"Continue"</b> button to proceed with your old basket or click on <b>"Continue with New Basket"</b> button to add products in your empty basket.')
                var actionbtnhtml = '';
                actionbtnhtml += '<button type="button" class="btn btn-info frontend-primary-btn" onclick="redirectToUrl(\'clubsearch\')" data-dismiss="modal">Continue</button>' +
                    '<button type="button" class="btn btn-info primary-btn" onclick="removeOrder();" data-dismiss="modal" >Continue with New Basket</button>';
                $('#infomodal .modal-footer').html(actionbtnhtml);
                var h = ($('.modal-dialog').height()) / 2;
                $('.modal-dialog').css({'top': '50%', 'margin-top': '-' + h + 'px'});
                $('#infomodal').modal('show');
                return false;
            }
        // }
    });
});
/*Search club end*/
var $ = jQuery;
function dateCheck() {
    var tDate = parseDate($('#todaydate').val());
    var fromDate= $('#fromDate').val();
    var toDate= $('#toDate').val();
    var eDate = parseDate(toDate);
    var sDate = parseDate(fromDate);
    var DayDiff = parseInt(daydiff(tDate,sDate));
    var errorMessage = '';
    if ($.trim(fromDate) != '') {
        if (sDate && eDate && (DayDiff<0)) {
            errorMessage = "You can book the orders only from today onwards. Past days bookings are not allowed.";
        }
    }
    if ($.trim(fromDate) == '' && $.trim(toDate) != '') {
        errorMessage = "Please select from date before to date.";
    } else if ($.trim(fromDate) != '' && $.trim(toDate) != '') {
        if (sDate && eDate && (parseInt(daydiff(sDate,eDate))<0)) {
            errorMessage = "Booking from date should be less than from booking to date.";
        }
    }

    if ($.trim(errorMessage) != '') {
        $('#searchbooking').attr('disabled', true);
        $("#dateErorr").attr('style', 'display:block;');
        $("#errorMessage").html('');
        $("#errorMessage").html('<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> ' + errorMessage);
    } else {
        $('#searchbooking').attr('disabled', false);
        $("#dateErorr").attr('style', 'display:none');
        $("#errorMessage").html('');
    }
}

function setDpInitVals(ele) {
    if($(ele).val()>0){
        $('#fromDate, #toDate').attr('disabled',false);
        var noDays = 3;
        if($(ele).val() == 6){
            noDays = 2;
        }else if($(ele).val() == 4){
            noDays = 6;
        }else if($(ele).val() == 5){
            noDays = 4;
        }
        var date = new Date();var today = date.getDay();
        var month = date.getMonth();
        month = parseInt(month) + 1;
        /*if(today >= 6 && month == 11){
            if(noDays < 4 && today == 5){
                date.setDate(date.getDate()+4);
            }else{
                date.setDate(date.getDate()+noDays);
            }
        }else{
            date.setDate(6);
            date.setMonth(10);
        }*/
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
            minDate.setDate(minDate.getDate()+($(ele).val() == '6' || $(ele).val() == '7'?0:1))
            $('#toDate').datepicker('setStartDate', minDate);
            $('#fromDate').datepicker('hide');
            $("#toDate").focus();
        });

        $("#toDate").datepicker({

            autoclose: true,
        })
            .on('changeDate', function (selected) {
                var minDate = new Date(selected.date.valueOf());
                $('#fromDate').datepicker('setStartDate', date);
                $('#toDate').datepicker('hide');
            });
    }else{
        $('#fromDate, #toDate').attr('disabled',true);
    }
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

function parseDate(str) {
    var mdy = str.split('/');
    return new Date(mdy[2], mdy[0],  mdy[1]);
}

function daydiff(first, second) {
    return Math.round((second-first)/(1000*60*60*24));
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

function getPreDetOrder(order_reference_id) {
    var jdata = {
        order_reference_id: order_reference_id
    }
    $.ajax({
        datatype: "json",
        url: siteRelPath+"getAjaxPreorderDetails",
        type: "POST",
        cache: false,
        data: jdata,
        success: function (html) {
            html = $.parseJSON(html);
            if(html.code == 200){
                $('#oldfromdate').val(html.oldfromdate);
                $('#oldtodate').val(html.oldtodate);
                $('#orderrefid').val(html.orderrefid);
                $('#oldstate').val(html.oldstate);
            }
        }
    });
}

function removeOrder() {
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
function redirectToUrl(redirectUrl) {
    if(redirectUrl == 'clubsearch'){
        localStorage.setItem('scrollToDiv','1');
    }else {
        localStorage.removeItem('scrollToDiv');
    }
    window.location.href = siteRelPath + redirectUrl;
}