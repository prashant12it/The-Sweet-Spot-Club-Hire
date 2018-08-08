@extends('layouts.dashboard')

@section('content')

    <header id="topbar" class="alt">
        <div class="topbar-left">
            <ol class="breadcrumb">
                <li class="crumb-active">
                    <a href="{{url('/dashboard')}}"><span class="glyphicon glyphicon-home"></span> Dashboard</a>
                </li>
            </ol>
        </div>


    </header>
    <section id="content" class="table-layout">
        <div class="tray tray-center">
            <div id="loader"></div>
            <!-- recent orders table -->
            <div class="panel">
                <form method="POST" enctype="multipart/form-data" action="{{ url('/dashboard') }}"
                      class="form-horizontal" role="form" id="dashboard">
                    {{ csrf_field() }}
                    <div class="panel-menu admin-form theme-primary">
                        <div class="row">
                            <?php
                            if (session('searchDashboard')) {
                                $searchDashboard = session('searchDashboard');
                            } else {
                                $searchDashboard = array();
                                $searchDashboard['dt_from'] = date("m/d/Y", strtotime('monday this week'));
                                $searchDashboard['week_count'] = '2';
                                $searchDashboard['product_type'] = '';
                                $searchDashboard['product_name'] = '';
                            }
                            ?>
                            <div class="col-md-4">
                                <label for="filter-datepicker" class="field prepend-picker-icon date"
                                       data-provide="datepicker">
                                    <input value="{{$searchDashboard['dt_from']}}" id="dt_from"
                                           name="searchDashboard[dt_from]" class="gui-input hasDatepicker"
                                           placeholder="From Date" type="text">
                                    <button type="button" class="ui-datepicker-trigger"><i class="fa fa-calendar-o"></i>
                                    </button>
                                </label>
                            </div>
                            <div class="col-md-3">
                                <label class="field select">

                                    <select id="week_count" name="searchDashboard[week_count]">
                                        <option value="1" {{($searchDashboard['week_count'] === '1' ? "selected='selected'":"")}}>
                                            1 Week
                                        </option>
                                        <option value="2" {{($searchDashboard['week_count'] === '2' ? "selected='selected'":"")}}>
                                            2 Weeks
                                        </option>
                                        <option value="3" {{($searchDashboard['week_count'] === '3' ? "selected='selected'":"")}}>
                                            3 Weeks
                                        </option>
                                        <option value="4" {{($searchDashboard['week_count'] === '4' ? "selected='selected'":"")}}>
                                            4 Weeks
                                        </option>
                                        <option value="5" {{($searchDashboard['week_count'] === '5' ? "selected='selected'":"")}}>
                                            5 Weeks
                                        </option>
                                        <option value="6" {{($searchDashboard['week_count'] === '6' ? "selected='selected'":"")}}>
                                            6 Weeks
                                        </option>
                                        <option value="7" {{($searchDashboard['week_count'] === '7' ? "selected='selected'":"")}}>
                                            7 Weeks
                                        </option>
                                        <option value="8" {{($searchDashboard['week_count'] === '8' ? "selected='selected'":"")}}>
                                            8 Weeks
                                        </option>
                                    </select>
                                    <i class="arrow double"></i>
                                </label>
                            </div>
                            <div class="col-md-3">
                                <label for="filter-product" class="field">
                                    <input value="{{$searchDashboard['product_name']}}" id="filter-user"
                                           name="searchDashboard[product_name]" class="gui-input"
                                           placeholder="Product Name/Description/SKU" type="text">
                                </label>
                            </div>

                            <div class="col-md-1">
                                <button type="submit" class="button form-control btn-info pull-right"><i
                                            class="fa fa-search" aria-hidden="true"></i></button>
                            </div>
                            <div class="col-md-1">
                                <a href="{{ url('/dashboard') }}" class="button form-control btn-danger pull-right"><i
                                            class="fa fa-refresh" aria-hidden="true"></i> </a>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="panel-body pn">
                    <div class="table-responsive booking-calendar-view calendar-weeks-<?php echo $searchDashboard['week_count'];?>">
                        <table class="table admin-form theme-warning tc-checkbox-1 fs13 booking-events-table">
                            <tbody>
                            <tr class="bg-light">
                                <th class="text-left upper-case item-heading" width="200px"><h4>Items & Categories</h4>
                                </th>
                                <td class="text-left upper-case days-colmns" id="remainingTdWidth">
                                    <div class="booking-weeks-<?php echo $searchDashboard['week_count'];?> booking-weeksdays">
                                        <table>
                                            <thead>
                                            <tr>
                                                <?php
                                                if (!empty($dateAry)) {
                                                foreach ($dateAry as $dayNumber => $date) {
                                                $day_id = "day_" . $dayNumber;
                                                ?>
                                                <td class='upper-case days-col' id="<?php echo $day_id;?>">
                                                    <?php echo date("M d", strtotime($date));?>
                                                    <br/><?php echo substr(date("l", strtotime($date)), 0, 3);?>
                                                </td>
                                                <?php
                                                }
                                                }
                                                ?>
                                            </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                            <?php
                            if (!empty($productsAry)) {
                            $dtToday = date('Y-m-d');
                            foreach ($productsAry as $pro_key => $parent_product) {
                            ?>
                            <tr>
                                <td class="text-left" colspan="<?php echo $total_days + 1; ?>">
                                    <div class="panel">
                                        <div class="panel-heading dashboard-panel-heading">
                                            <span class="panel-title"> <?php echo $parent_product->name; ?></span>
                                            <span class="panel-controls">
                                                        <a href="javascript:void(0)" class="hide"
                                                           id="show-pro-<?php echo (int)$parent_product->id; ?>"
                                                           onclick="showHideChildProducts('<?php echo (int)$parent_product->id; ?>', 'show')"><i
                                                                    class="fa fa-arrow-down" aria-hidden="true"></i></a>
                                                        <a href="javascript:void(0)" class="show"
                                                           id="hide-pro-<?php echo (int)$parent_product->id; ?>"
                                                           onclick="showHideChildProducts('<?php echo (int)$parent_product->id; ?>', 'hide')"><i
                                                                    class="fa fa-arrow-up" aria-hidden="true"></i></a>
                                                    </span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php
                            if (!empty($parent_product->childProductAry)) {
                            foreach ($parent_product->childProductAry as $childKey=>$childProduct) {
                            ?>
                            <tr class="parent_pro_<?php echo (int)$parent_product->id; ?>">
                                <td class="text-left upper-case child-product-title">
                                    <strong><?php echo $childProduct->name; ?></strong><br/>
                                    <br/>
                                </td>
                                <td>
                                    <!--<div class="panel">-->
                                <!--<div class="panel-body dashboard-panel-body show" id="parent_pro_<?php echo (int)$parent_product->id; ?>">-->
                                    <table class="table admin-form theme-warning tc-checkbox-1 fs13">
                                        <tbody>
                                        <tr class="bg-light">
                                            <td colspan="<?php echo $total_days;?>">
                                                <div class="booking-table booking-weeks-<?php echo $searchDashboard['week_count'];?>">
                                                    <table>
                                                        <tbody>
                                                        <?php
                                                        //                                                                                    print_R($childProduct);
                                                        if (!empty($childProduct->calendarRowArray)) {
                                                        foreach ($childProduct->calendarRowArray as $iRow => $bookedProductAry) {
                                                        $remaing_td_id = $childProduct->product_id."_".$childKey . "_" . $iRow;
                                                        ?>
                                                        <tr>
                                                            <?php
                                                            $remaingCols = $total_days;
                                                            if (!empty($bookedProductAry['bookingAry'])) {
                                                            foreach ($bookedProductAry['bookingAry'] as $column => $booking) {
                                                            //                                                                                                        print_R($booking);
                                                            $remaingCols = $remaingCols - $booking['colSpan'];
                                                            $td_id = $childProduct->product_id."_".$childKey . "_" . $iRow . "_" . $column;

                                                            if((int)$booking['colSpan'] > 0){
                                                            if((int)$booking['isEmptyCol'] == 0){
                                                            $divID = "booked_" . $booking['order_id'] . "_" . $booking['product_id'];
                                                            $divIDYel = "booked_" . $booking['order_id'] . "_" . $booking['product_id'] . "_yel";
                                                            $divIDPre = "booked_" . $booking['order_id'] . "_" . $booking['product_id'] . "_pre";
                                                            $extendedDays = $servicingDays[$booking['state_id']];
                                                            $remaingCols = $remaingCols - $extendedDays;
                                                            $dayOfBooking = date('l', strtotime($booking['dt_booked_from']));
                                                            $preBookingTransitDays = 1;
                                                            if ($dayOfBooking == 'Monday') {
                                                                $preBookingTransitDays = 4;
                                                            }

                                                            $remaingCols = $remaingCols - $preBookingTransitDays;
                                                            ?>
                                                            <td colspan="<?php echo (int)$preBookingTransitDays;?>"
                                                                class="bookedProEvent"
                                                                id="<?php echo $td_id . 'pre';?>">
                                                                <div class="booking_div_parent_yellow">
                                                                    <p class="booking-text">
                                                                        <?php echo substr($booking['user_name'], 0, 10);?>
                                                                        <i class="fa fa-info" aria-hidden="true"
                                                                           id="info_<?php echo $divIDPre;?>"
                                                                           onmouseover="showBookingDetails('<?php echo $divIDPre;?>','show');"
                                                                           onmouseout="showBookingDetails('<?php echo $divIDPre;?>','hide');"></i>
                                                                    </p>
                                                                    <div class="booking_details"
                                                                         id="<?php echo $divIDPre;?>"
                                                                         style="display:none;">
                                                                        <strong>Order
                                                                            ID: </strong>#<?php echo $booking['order_id'];?>
                                                                        <br/>
                                                                        <strong>Pre Booking Transit
                                                                            From: </strong><?php echo date('M d Y', strtotime($booking['dt_booked_from'] . ' - ' . $preBookingTransitDays . ' days'));?>
                                                                        <br/>
                                                                        <strong>Pre Booking Transit From
                                                                            Upto: </strong><?php echo date('M d Y', strtotime($booking['dt_booked_from']));?>
                                                                        <br/>
                                                                        <strong>Customer
                                                                            Name: </strong><?php echo $booking['user_name'];?>
                                                                        <br/>
                                                                        <strong>Customer
                                                                            Email: </strong><?php echo $booking['user_email'];?>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td colspan="<?php echo (int)$booking['colSpan'];?>"
                                                                class="bookedProEvent" id="<?php echo $td_id;?>">
                                                                <div class="booking_div_parent">
                                                                    <p class="booking-text">
                                                                        <?php echo substr($booking['user_name'], 0, 10);?>
                                                                        <i class="fa fa-info" aria-hidden="true"
                                                                           id="info_<?php echo $divID;?>"
                                                                           onmouseover="showBookingDetails('<?php echo $divID;?>','show');"
                                                                           onmouseout="showBookingDetails('<?php echo $divID;?>','hide');"></i>
                                                                    </p>
                                                                    <div class="booking_details"
                                                                         id="<?php echo $divID;?>"
                                                                         style="display:none;">
                                                                        <strong>Order
                                                                            ID: </strong>#<?php echo $booking['order_id'];?>
                                                                        <br/>
                                                                        <strong>Booked
                                                                            From: </strong><?php echo date('M d Y', strtotime($booking['dt_booked_from']));?>
                                                                        <br/>
                                                                        <strong>Booked
                                                                            Upto: </strong><?php echo date('M d Y', strtotime($booking['dt_booked_upto']));?>
                                                                        <br/>
                                                                        <strong>Customer
                                                                            Name: </strong><?php echo $booking['user_name'];?>
                                                                        <br/>
                                                                        <strong>Customer
                                                                            Email: </strong><?php echo $booking['user_email'];?>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td colspan="<?php echo (int)$extendedDays;?>"
                                                                class="bookedProEvent"
                                                                id="<?php echo $td_id . 'yel';?>">
                                                                <div class="booking_div_parent_yellow">
                                                                    <p class="booking-text">
                                                                        <?php echo substr($booking['user_name'], 0, 10);?>
                                                                        <i class="fa fa-info" aria-hidden="true"
                                                                           id="info_<?php echo $divIDYel;?>"
                                                                           onmouseover="showBookingDetails('<?php echo $divIDYel;?>','show');"
                                                                           onmouseout="showBookingDetails('<?php echo $divIDYel;?>','hide');"></i>
                                                                    </p>
                                                                    <div class="booking_details"
                                                                         id="<?php echo $divIDYel;?>"
                                                                         style="display:none;">
                                                                        <strong>Order
                                                                            ID: </strong>#<?php echo $booking['order_id'];?>
                                                                        <br/>
                                                                        <strong>Transit/Cleaned time
                                                                            From: </strong><?php echo date('M d Y', strtotime($booking['dt_booked_upto'] . ' + 1 day'));?>
                                                                        <br/>
                                                                        <strong>Transit/Cleaned time
                                                                            Upto: </strong><?php echo date('M d Y', strtotime($booking['dt_booked_upto'] . ' +' . $extendedDays . ' days'));?>
                                                                        <br/>
                                                                        <strong>Customer
                                                                            Name: </strong><?php echo $booking['user_name'];?>
                                                                        <br/>
                                                                        <strong>Customer
                                                                            Email: </strong><?php echo $booking['user_email'];?>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <?php
                                                            }
                                                            else{
                                                            ?>
                                                            <td colspan="<?php echo (int)$booking['colSpan']-1;?>"
                                                                class="emptyProEvent" id="<?php echo $td_id;?>">
                                                                &nbsp;
                                                            </td>
                                                            <?php
                                                            }
                                                            }

                                                            }
                                                            }
                                                            if((int)$remaingCols > 0){
                                                            ?>
                                                            <td colspan="<?php echo $remaingCols+1;?>"
                                                                class="emptyProEvent" id="<?php echo $remaing_td_id;?>">
                                                                &nbsp;
                                                            </td>
                                                            <?php
                                                            }
                                                            ?>

                                                        </tr>
                                                        <?php
                                                        }
                                                        }
                                                        ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <!--</div>-->
                                    <!--</div>-->
                                </td>
                            </tr>
                            <?php
                            }
                            }
                            else {
                            ?>
                            <tr>
                                <td colspan="<?php echo $total_days + 1; ?>">No Product found.</td>
                            </tr>
                            <?php
                            }
                            }
                            }
                            else {
                            ?>
                            <tr>
                                <td colspan="<?php echo $total_days + 1; ?>">No Product found.</td>
                            </tr>
                            <?php
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </section>
    <script type="text/javascript">
        $(window).ready(function () {
            $('#loader').show();
        });
        $(window).load(function () {
            $('#loader').show();
            setTableWidth();
            $('#loader').hide();
        });
        $(window).resize(function () {
            $('#loader').show();
            setTableWidth();
            $('#loader').hide();
        });

        function setTableWidth() {
            var Tablewidth = $('.booking-events-table').width();

            var remainingTdWidth = parseInt(Tablewidth) - 200;

//        alert("Table Width "+Tablewidth);
//        alert("Remainig Width "+remainingTdWidth);

            var weeks = '<?php echo $searchDashboard['week_count'];?>';

            var tdWidth = remainingTdWidth / (weeks * 8);

            $("#remainingTdWidth").attr('width', remainingTdWidth + 'px');

                <?php
                if (!empty($dateAry)) {
                foreach ($dateAry as $dayNumber => $date) {
                $day_id = "day_" . $dayNumber;
                ?>
            var day_id = "<?php echo $day_id;?>";
            $("#" + day_id).css("width", tdWidth + "px");
                <?php
                }
                }

                if (!empty($productsAry)) {
                $dtToday = date('Y-m-d');
                foreach ($productsAry as $pro_key => $parent_product) {
                if (!empty($parent_product->childProductAry)) {
                foreach ($parent_product->childProductAry as $childKey=>$childProduct) {
                if (!empty($childProduct->calendarRowArray)) {
                foreach ($childProduct->calendarRowArray as $iRow => $bookedProductAry) {
                $remaing_td_id = $childProduct->product_id."_".$pro_key . "_" . $iRow;
                if (!empty($bookedProductAry['bookingAry'])) {
                foreach ($bookedProductAry['bookingAry'] as $column => $booking) {
                $td_id = $childKey . "_" . $iRow . "_" . $column;
                ?>
            var td_id = "<?php echo $td_id;?>";
            var td_id_pre = "<?php echo $td_id . 'pre';?>";
            var td_id_yel = "<?php echo $td_id . 'yel';?>";
            var rmng_td_id = "<?php echo $remaing_td_id;?>";
            var td_col_span = $("#" + td_id).attr("colspan");
            var td_col_span_pre = $("#" + td_id_pre).attr("colspan");
            var td_col_span_yel = $("#" + td_id_yel).attr("colspan");
            var td_width = tdWidth * parseInt(td_col_span);
            var td_width_pre = tdWidth * parseInt(td_col_span_pre);
            var td_width_yel = tdWidth * parseInt(td_col_span_yel);
//

            $("#" + td_id).css("width", td_width + "px");
            $("#" + td_id_pre).css("width", td_width_pre + "px");
            $("#" + td_id_yel).css("width", td_width_yel + "px");
                <?php
                }
                } ?>
            var rmng_td_col_spn = $("#" + rmng_td_id).attr("colspan");//
            var rmng_width = tdWidth * rmng_td_col_spn;
            $("#" + rmng_td_id).css("width", rmng_width + "px");
            <?php }
    }
    }
    }
    }
    }
    ?>
$('#loader').show();
        }
    </script>
@endsection