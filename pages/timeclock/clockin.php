<?php
//echo('clockin.php content here');
$pageWasRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0';
require('../../model/connect.php');
if($pageWasRefreshed) {
  //do nothing
} else {
    if(isset($_GET['reload'])){
        if(!isset($_SESSION)){
            session_start();
        }
        require('../../model/bar-functions.php');
        require('../../pages/timeclock/clocking_func.php');
        $fullcurdate = getCurDttm(true);
        console_log($fullcurdate);
        //echo($_SESSION['username'] .' get reload found'); 
        if(isset($_SESSION['username'])){
            //called for mailitem.php to be used if the browser reload was not completed.
            $UserInfo = getUserInfo($_SESSION['username']); 
            $curdate = getCurDttm(false);
            //echo($curdate);
            $getcurrstat = CheckTime($UserInfo['ID'], $curdate);
            $db = NULL;
        }

    }
    //else, if you press enter on the address bar to refresh the page.
    else{
        //date_default_timezone_set('America/New_York');
        //require('model/bar-functions.php');
        require('pages/timeclock/clocking_func.php');
        //echo($_SESSION['username'] .' get reload found'); 
        //echo($UserInfo['ID'] .' refresh completely');
        if(isset($_SESSION['username'])){
            //called for mailitem.php to be used if the browser reload was not completed.
            $UserInfo = getUserInfo($_SESSION['username']); 
            $curdate = getCurDttm(false);
            $fullcurdate = getCurDttm(true);
            console_log($fullcurdate);
            $getcurrstat = CheckTime($UserInfo['ID'], $curdate);
            $db = NULL;
        }
        
    }
}

?>
<!-- DataTables -->
<link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.css">
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Time Clock</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Time Clock</li>
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

<section class="content">
    <div class="row">
        <div class="col-md-3">
            <!-- general form elements -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Time Clock</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form role="form">
                    <div class="card-body">
                        <div class="form-group">
                            <h1><?php if(isset($UserInfo)){giveUserFullName($UserInfo);} else {echo("Persons Name");}?></h1>
                        </div>
                        <div class="timeclock-status">
                            <h4>Status:
                                <span id="stat-placeholder" style="color: <?php echo($getcurrstat[3]);?>"><?php echo($getcurrstat[2]); ?></span>
                            </h4>
                            <!--<h4 id="stat-placeholder">Placeholder</h4>-->
                        </div>
                        <div class="timeclock-status" id="tc-status-ln2">
                            <h4>
                                <?php
                                //need to edit so this loads upon initial clock-in for the day
                                if(isset($getcurrstat[7])){
                                ?>
                                at
                                <span id="time-placeholder" style="color: <?php echo($getcurrstat[3]);?>"><?php echo($getcurrstat[7]);?></span>
                                <?php
                                }
                                ?>
                            </h4>
                            <!--<h4 id="stat-placeholder">Placeholder</h4>-->
                        </div>
                        <div class="input-group input-group-lg mb-3">
                            <div class="input-group-prepend">
                                <select class="form-control" id="timeclock-dropdown" name="time-option">
                                    <option value="" selected <?php setSelectedStat($getcurrstat[4], $getcurrstat[5], $getcurrstat[6], 1); ?>>Select Option</option>
                                    <option value="ClockIn" id="in-out" <?php getDisabledStat($getcurrstat[4]) ." ". setSelectedStat($getcurrstat[4], $getcurrstat[5], $getcurrstat[6], 4);?>>Clock-In/Out</option>
                                    <option value="Break" id="break" <?php getDisabledStat($getcurrstat[5]) ." ". setSelectedStat($getcurrstat[4], $getcurrstat[5], $getcurrstat[6], 5);?>>Break</option>
                                    <option value="Lunch" id="lunch" <?php getDisabledStat($getcurrstat[6]) ." ". setSelectedStat($getcurrstat[4], $getcurrstat[5], $getcurrstat[6], 6);?>>Lunch</option>
                                </select>
                            </div>
                                <!-- /btn-group -->
                            <input type="password" id="PIN" name="PIN" class="form-control" placeholder="PIN#">
                        </div>
                        <div id="success">

                        </div>
                            <!-- /input-group -->
                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" id="tc-submit">Submit</button>
                    </div>
                </form>
            </div>
            <!-- general form elements -->
            <div class="card card-primary">
                <div class="card-header">
                <h3 class="card-title">Info on Time Clock</h3>
                </div>
                <!-- /.card-header -->
                    <div class="card-body">
                        <p>The Time Clock works to allow the user to sign in and out of break, lunch, and clock in and out for the day.</p>
                        <p>As soon as you use an option and submit, it updates the Time Clock accordingly with the status, and time by reloading just the clockin.php portion of the page.</p>
                        <p>Currently working on getting the page to load upon full reload of page as currently it is coded to load through Ajax to save on resources.</p>
                    </div>
            </div>
        </div>
        <!-- /.col-md-3 -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">DataTable with default features</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="time-entries-table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Step</th>
                                <th>Status</th>
                                <th>Time</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php

                        //working on this, only returns the last row created for that date
                            $timingsday = allTimingsDay($UserInfo['ID'], $curdate);
                            $i = 0;
                            foreach($timingsday as $row){
                        ?>
                            <tr>
                                <td><?php echo($row['Row_ID']);?></td>
                                <td><?php echo($row['Status']);?></td>
                                <td><?php echo($row['Time_Cur']);?></td>
                                <td>2</td>
                            </tr>
                        <?php
                            } //end timingsdate loop
                            
                        ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Step</th>
                                <th>Status</th>
                                <th>Time</th>
                                <th>Total</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <!-- /.card-body -->
            <!-- /.card -->
            </div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Info on DataTable</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="time-entries-table" class="table table-bordered table-striped">
                        <tbody>
                            <p>DataTable is now refreshed upon the TimeClock submit button being clicked to reload just the clockin.php portion of the page.</p>
                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
        <!-- /.col-md-9 -->
    </div>
</section>
<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<script src="pages/timeclock/js/tc_activate.js"></script>
<!-- DataTables -->
<script src="plugins/datatables/jquery.dataTables.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
<!-- AdminLTE App -->
<!--<script src="dist/js/adminlte.min.js"></script> -->
<!-- AdminLTE for demo purposes -->
<!-- <script src="dist/js/demo.js"></script> -->
<!-- page script -->
<script>
  $(function () {
    $("#time-entries-table").DataTable();
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false,
    });
  });
</script>