<!-- BEGIN: PAGE SCRIPTS -->

  <!-- jQuery -->
  <script src="{{ URL::asset('theme/vendor/jquery/jquery-1.11.1.min.js') }}"></script>
  <script src="{{ URL::asset('theme/vendor/jquery/jquery_ui/jquery-ui.min.js') }}"></script>

  <!-- CanvasBG Plugin(creates mousehover effect) -->
  <script src="{{ URL::asset('theme/vendor/plugins/canvasbg/canvasbg.js') }}"></script>

  <!-- Theme Javascript -->
  <script src="{{ URL::asset('theme/assets/js/utility/utility.js') }}"></script>
  <script src="{{ URL::asset('theme/assets/js/demo/demo.js') }}"></script>
  <script src="{{ URL::asset('theme/assets/js/main.js') }}"></script>
  <script src="{{ URL::asset('theme/assets/js/countries.js') }}"></script>

  <!-- Page Javascript -->
  <script type="text/javascript">
  jQuery(document).ready(function() {

    "use strict";

    // Init Theme Core      
    Core.init();

    // Init Demo JS
    Demo.init();

    // Init CanvasBG and pass target starting location
    CanvasBG.init({
      Loc: {
        x: window.innerWidth / 2,
        y: window.innerHeight / 3.3
      },
    });
populateCountries("country", "state");
  });
  </script>

  <!-- END: PAGE SCRIPTS -->