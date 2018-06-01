<section id="hire-page-banner">
    <div class="overlap-black-shadow"></div>
    <div class="hire-caption">
        <h3>Thank You!</h3>
        <p>{{$PageDescription1}}<br/>
            {{$PageDescription2}}</p>
        @if($FirstTime == '1' && $discountErr == '0')
            <p class="alert alert-success">Your order has been successfully placed.</p>
        @elseif($FirstTime == '1' && $discountErr == '1')
            <p class="alert alert-success">Discount can not be greater than 100%. Please try again.</p>
        @endif
    </div>
</section>