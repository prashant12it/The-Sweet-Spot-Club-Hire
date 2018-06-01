<!-- BEGIN: PAGE SCRIPTS -->

<!-- jQuery -->
<script src="{{ URL::asset('theme/vendor/jquery/jquery_ui/jquery-ui.min.js') }}"></script>

<!-- Select2 Plugin Plugin -->
<script src="{{ URL::asset('theme/vendor/plugins/select2/select2.min.js') }}"></script>
<!-- Typeahead Plugin -->
<script src="{{ URL::asset('theme/vendor/plugins/typeahead/typeahead.bundle.min.js') }}"></script>

<!-- Theme Javascript -->
<script src="{{ URL::asset('theme/assets/js/utility/utility.js') }}"></script>
<script src="{{ URL::asset('theme/assets/js/demo/demo.js') }}"></script>
<script src="{{ URL::asset('theme/assets/js/main.js') }}"></script>
<script src="{{ URL::asset('theme/assets/js/custom.js') }}"></script>
<script src="{{ URL::asset('theme/vendor/plugins/select2/select2.min.js') }}"></script>
<!-- Widget Javascript -->
<script src="{{ URL::asset('theme/assets/js/demo/widgets.js') }}"></script>
<script src="{{ URL::asset('theme/assets/js/prashant.js') }}"></script>
<script src="{{ URL::asset('theme/assets/js/bootstrap-datepicker.min.js') }}"></script>
<script type="text/javascript">
jQuery(document).ready(function () {

  "use strict";

  // Init Demo JS
  Demo.init();

    // Init Theme Core    
    Core.init();
    
     $('.datepicker').datepicker({
         autoclose: true
     });
     
  $.ajaxSetup({
          headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
      });
      @if (!empty($scriptName) && ($scriptName == 'Add multiselect script' || $scriptName == 'Edit multiselect script'))
          $('#loader').show();
    $("#group_products").select2();


      setTimeout(function () {
@if($scriptName == 'Edit multiselect script')
          enableDisableFields('editProductForm');
      autoConfigureProducts();
@endif
 @if($scriptName == 'Add multiselect script')

          @if(old('category'))
              enableDisableFields('addProductForm');
@endif
@if(old('product_type'))
          autoConfigureProducts();
@endif

@endif
        },1000);
      setTimeout(function () {
          $("#group_products").select2(
              'val', valArr
          );
      },1500);
      setTimeout(function () {
          $("#group_products").select2({
              placeholder: "Select products",
              allowClear: true
          });
          $(".proAttrOpt").select2();
          // Init Select2 - Basic Multiple
          $(".proAttrOpt").select2({
              placeholder: "Select attribute option",
              allowClear: true
          });
          $(".upsellProOpt").select2();
          // Init Select2 - Basic Multiple
          $(".upsellProOpt").select2({
              placeholder: "Select upsell products",
              allowClear: true
          });
      },500);

if($('#sale').is(':checked')){
    $('#sale_price').attr('readonly',false);
    $('.rent-sec').hide();
    $('.sale-sec').show();
      }

      if($('#rent').is(':checked')){
          $('#rent_price').attr('readonly',false);
          $('.sale-sec').hide();
          $('.rent-sec').show();
      }
      var ele = '#price';
    if ($(ele).val() > 0) {
        $('#sale_price').attr('max', $(ele).val());
        $('#rent_price').attr('max', $(ele).val());
    }
      $('#loader').hide();

    @endif

});

</script>