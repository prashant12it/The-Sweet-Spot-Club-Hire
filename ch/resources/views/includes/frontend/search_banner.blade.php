<section id="banner" xmlns:>
    <div class="overlap-black-shadow"></div>
    <div class="text-caption">
        <h3>Making golf travel<br/> simple.</h3>
        <div class="booking-info">
            <form class="search-form" id="set-search-form" method="POST" action="{{ url('/clubsearch') }}">
                {{ csrf_field() }}
                <select name="states" id="states" onchange="setDpInitVals(this);">
                    <option value="0">State</option>
                    <option value="1">Victoria</option>
                    <option value="2">New South Wales</option>
                    <option value="3">South Australia</option>
                    <option value="4">Tasmania</option>
                    <option value="5">Queensland</option>
                </select>
                <input id="fromDate" disabled="disabled" onfocus="if (this.value == 'From') {this.value = '';}"
                       onblur="if (this.value == '') {this.value = 'From';}" value="From" name="fromDate"
                       placeholder="From" class="hasDatepicker date datepicker" data-provide="datepicker" type="text"
                       onchange="dateCheck();" required="required">
                <input onfocus="if (this.value == 'To') {this.value = '';}"
                       onblur="if (this.value == '') {this.value = 'To';}" disabled="disabled" value="To" id="toDate" name="toDate"
                       placeholder="To" class="hasDatepicker date datepicker" data-provide="datepicker" type="text"
                       onchange="dateCheck();" required="required">

                <input type="hidden" id="oldfromdate" name="oldfromdate"
                       value="{{(!empty($PreOrderDetsArr[0]->dt_book_from)?date('m/d/y',strtotime($PreOrderDetsArr[0]->dt_book_from)):'')}}"/>
                <input type="hidden" id="oldtodate" name="oldtodate"
                       value="{{(!empty($PreOrderDetsArr[0]->dt_book_upto)?date('m/d/y',strtotime($PreOrderDetsArr[0]->dt_book_upto)):'')}}"/>
                <input type="hidden" name="orderrefid" id="orderrefid" value="{{( isset( $_COOKIE['order_reference_id'] ) ? $_COOKIE['order_reference_id'] : null )}}" />
                <input type="hidden" name="oldstate" id="oldstate" value="{{(!empty($PreOrderDetsArr[0]->state_id)?$PreOrderDetsArr[0]->state_id:'')}}" />
                <button id="searchbooking" type="submit">search</button>

            </form>
            <br/>
            <div class="alert alert-warning" width="75%" id="dateErorr" {{(Session::get('error') != '' ? "":'style="display:none;"')}}>
            <p id="errorMessage" class="text-left"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> {{
                Session::get('error') }}</p>
        </div>
        <input type="hidden" id="todaydate" name="todaydate" value="{{date('m/d/Y')}}"/>
    </div>
    </div>
    <a href="#first-section" title="" class="scroll-down-link"></a>
</section>
<div id="popupbs">
    <div class="modal fade" id="infomodal" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        &times;
                    </button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <p id="info-msg"></p>
                </div>
                <div class="modal-footer">
                </div>
            </div>

        </div>
    </div>

</div>
<script type="text/javascript">
    $(document).ready(function () {


        $('form').submit(function () {
            var sDate = parseDate($('#fromDate').val());
            var eDate = parseDate($('#toDate').val());
            var osDate = parseDate($('#oldfromdate').val());
            var oeDate = parseDate($('#oldtodate').val());
            var oldState = $('#oldstate').val();
            var state = $('#states').val();
            if ($.trim($('#fromDate').val()) == '' || $.trim($('#fromDate').val()) == 'From') {
                $('#errorMessage').html('<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Enter valid "From" date.');
                $('#dateErorr').show();
                return false;
            } else if ($.trim($('#toDate').val()) == '' || $.trim($('#toDate').val()) == 'To') {
                $('#errorMessage').html('<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Enter valid "TO" date.');
                $('#dateErorr').show();
                return false;
            } else if (parseInt(daydiff(sDate,eDate)) < 2) {
                $('#errorMessage').html('<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Minimum <b>2 Days </b> hiring is allowed for club sets.');
                $('#dateErorr').show();
                return false;
            } else if ((oldState && state != oldState) || (osDate && osDate != sDate) || (oeDate && oeDate != eDate)) {
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
        });
    });
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
</script>