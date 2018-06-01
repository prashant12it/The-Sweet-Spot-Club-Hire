<div id="popupbs">
	<div class="modal fade" id="infomodal" role="dialog">
		<div class="modal-dialog">

			<!-- Modal content-->
			<div class="modal-content">

				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" onclick="redirectToUrl('{{$Page}}')">
						&times;
					</button>
					<h4 class="modal-title"></h4>
				</div>
				<div class="modal-body">
					<p id="info-msg"></p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-info frontend-primary-btn" data-dismiss="modal"
					        onclick="redirectToUrl('{{$Page}}')">Okay
					</button>
				</div>
			</div>

		</div>
	</div>

</div>
<script src="{{ URL::asset('theme/assets/js/masonry.pkgd.js') }}"></script>
<script src="{{ URL::asset('theme/assets/js/masonry.pkgd.min.js') }}"></script>

<div id="popupproddet">
    <div class="modal fade" id="prodinfomodal" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        &times;
                    </button>
                    <h4 class="modal-title">Product Info </h4>
                </div>
                <div class="modal-body">
                    <section class="wrapper">
                        <ul class="tabs">
                            <li class="showupsell"><a href="#tab1">Description</a></li>
                            <li class="hideupsell"><a href="#tab2">Set Inclusions</a></li>
                            <li class="hideupsell"><a href="#tab3">Promotions</a></li>
                            <li class="hideupsell"><a href="#tab4">Delivery</a></li>
                        </ul>
                        <div class="clr"></div>
                        <section class="block black-content">
                            <article id="tab1">
                                <div class="row">
                                    <div class="col-md-3 col-xs-7 col-sm-4" id="imgsec">

                                    </div>
                                    <div class="col-md-9 col-xs-60 col-sm-8">
                                        <p id="prod-desc-popup"></p>
                                    </div>
                                </div>
                            </article>
                            <article id="tab2">
                                <div class="masonry">

                                </div>

                            </article>
                            <article id="tab3">
                                <p>Free sleeve of 3 TP5 golf balls with any 7day+ hire.</p>
                                <p>Free TSS bottle opener with every set hire.</p>
                                <p>save up to 15% when you hire more than one club set.</p>
                            </article>
                            <article id="tab4">
                                <p>Delivery is available to the states of Victoria, New South Wales, Tasmania, Queensland and South Australia. </p>
                                <p>Delivery is FREE to metropolitan Melbourne.</p>
                            </article>

                        </section>
                    </section>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info frontend-primary-btn" data-dismiss="modal">Close
                    </button>
                </div>
            </div>

        </div>
    </div>

</div>


    <div class="modal fade" id="giftinfomodal" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        &times;
                    </button>
                    <div class="col-md-12">
                        <div class="row center-content">
                            <h2 class="btn btn-info frontend-primary-btn gift-btn">Your length of hire entitles you to these FREE gifts. Simply hire any club set to claim.</h2><img id="freegiftlogo" src="{{ URL::asset('frontend/images/gift-loop.gif') }}"/>
                        </div>
                    </div>
                </div>
                <div class="modal-body">

                            <div class="row" id="allgifts">

                            </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info frontend-primary-btn" data-dismiss="modal">Thanks!
                    </button>
                </div>
            </div>

        </div>
    </div>

<script type="text/javascript">
    $(document).ready(function(){
        $("#myTab li:eq(0) a").tab('show');
        $('.grid').masonry({
            // options...
            itemSelector: '.grid-item',
            columnWidth: 400
        });
    });
    $(function(){
        $('ul.tabs li:first').addClass('active');
        $('.block article').hide();
        $('.block article:first').show();
        $('ul.tabs li').on('click',function(){
            $('ul.tabs li').removeClass('active');
            $(this).addClass('active')
            $('.block article').hide();
            var activeTab = $(this).find('a').attr('href');
            $(activeTab).show();
            return false;
        });
    })
</script>