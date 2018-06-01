<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="{{ URL::asset('frontend/js/bootstrap.min.js') }}"></script>
<script src="{{ URL::asset('theme/assets/js/custom.js') }}"></script>
<script src="{{ URL::asset('theme/assets/js/prashant.js') }}"></script>
<script src="{{ URL::asset('theme/assets/js/bootstrap-datepicker.min.js') }}"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script>
    $.ajaxSetup({
        headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
    });
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        var target = $(this).attr('href');
        $(target).css('left','-'+$(window).width()+'px');
        var left = $(target).offset().left;
        $(target).css({left:left}).animate({"left":"0px"}, "10");
    })

    $(document).ready(function() {

//        getsiteFooter('http://tssclubhire.com/wordpress/');
        scrollToDivOnHirePage();
        $('.navbar-toggle').on('click', function(e) {
            $('body').add(this).toggleClass('over-hide');
        });
        $('.datepicker').datepicker({
            autoclose: true
        });
        /*var bkfromDate = $('#bkfromDate');
        bkfromDate.datepicker();
        bkfromDate.datepicker('setDate', new Date());*/

    });


    var headerHeight = $("#header").height();

    // Attach the click event
    $('.scroll-down-link').bind("click", function(e) {
        $('html, body').animate({
            scrollTop: $("#first-section").offset().top - headerHeight
        }, 1000);
    });

    $(function() {
        var action;
        $(".number-spinner button").mousedown(function () {
            btn = $(this);
            input = btn.closest('.number-spinner').find('input');
            btn.closest('.number-spinner').find('button').prop("disabled", false);

            if (btn.attr('data-dir') == 'up') {
                action = setInterval(function(){
                    if ( input.attr('max') == undefined || parseInt(input.val()) < parseInt(input.attr('max')) ) {
                        input.val(parseInt(input.val())+1);
                    }else{
                        btn.prop("disabled", true);
                        clearInterval(action);
                    }
                }, 300);
            } else {
                action = setInterval(function(){
                    if ( input.attr('min') == undefined || parseInt(input.val()) > parseInt(input.attr('min')) ) {
                        input.val(parseInt(input.val())-1);
                    }else{
                        btn.prop("disabled", true);
                        clearInterval(action);
                    }
                }, 300);
            }
        }).mouseup(function(){
            clearInterval(action);
        });
    });
    
</script>