<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item" id="hamburger">
            <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="#" class="nav-link">Home</a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="#" class="nav-link">Contact</a>
        </li>
    </ul>

<!-- SEARCH FORM -->
<form class="form-inline ml-3" id="searchbar">
    <div class="input-group input-group-sm">
        <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
        <div class="input-group-append">
            <button class="btn btn-navbar" type="submit">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </div>
</form>

<!-- Right navbar links -->
<ul class="navbar-nav ml-auto">
    <!-- Messages Dropdown Menu -->
    <li class="nav-item dropdown">
    <a class="nav-link" data-toggle="dropdown" href="#">
        <i class="far fa-comments"></i>
        <span class="badge badge-danger navbar-badge">10</span>
    </a>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">

<?php
    $iterations = 0;
	$db = Cnnct();
	$sth = $db->prepare("SELECT EMP.first_name, EMP.last_name, MSG.msg_cont, MSG.Sent_Dte, EMP.emp_pic_path FROM MSG
    LEFT JOIN EMPLOYEES AS EMP
    ON EMP.ID = MSG.frm_id
    WHERE MSG.to_id = ? ORDER BY Sent_Dte DESC LIMIT 3");
	$sth->execute([$UserInfo['ID']]);
	$r = $sth->fetchAll(PDO::FETCH_ASSOC);
	//fetch tha data from the database for at least one row.
    foreach($r as $row){
        $msgpreview[$iterations] = array('first_name' => $row['first_name'], 'last_name' => $row['last_name'], 'msg_cont' => $row['msg_cont'], 'Sent_Dte' => $row['Sent_Dte'], 'emp_pic_path' => $row['emp_pic_path'], );
        $iterations += 1;
    }

    for($i=0; $i<$iterations; $i++){
?>



    <!-- item to start looping -->
        <a href="#" class="dropdown-item">
        <!-- Message Start -->
        <div class="media">
            <img src="<?php getMsgProfilePic($msgpreview[$i]['emp_pic_path']); ?>" alt="User Avatar" class="img-size-50 mr-3 img-circle">
            <div class="media-body">
                <h3 class="dropdown-item-title">
                    <?php echo($msgpreview[$i]['first_name'] ." ". $msgpreview[$i]['last_name']);?>
                    <span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>
                </h3>
                <p class="text-sm"><?php echo($msgpreview[$i]['msg_cont'])?>...</p>
                <!-- need to replace code for timestamp diff due to messing with timeclock upon loading -->
                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i><?php //$tme = getMsgTimeStampDiff($msgpreview[$i]['Sent_Dte']); echo($tme);?></p>
            </div>
        </div>
        <!-- Message End -->
        </a>
        <div class="dropdown-divider"></div>
    <!-- item to end looping -->
<?php
    }
    //unset($iterations); //clear the variable after use
?>

        <a href="#" class="dropdown-item dropdown-footer">See All Messages</a>
    </div>
    </li>
    <!-- Notifications Dropdown Menu -->
    <li class="nav-item dropdown">
    <a class="nav-link" data-toggle="dropdown" href="#">
        <i class="far fa-bell"></i>
        <span class="badge badge-warning navbar-badge">15</span>
    </a>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
            <span class="dropdown-header">15 Notifications</span>
            <div class="dropdown-divider"></div>
            <a href="#" class="dropdown-item" id="top-bar-new-msg">
            <i class="fas fa-envelope mr-2"></i> <?php echo($messagecount); ?> new messages
            <span class="float-right text-muted text-sm">3 mins</span>
            </a>
            <div class="dropdown-divider"></div>
            <a href="#" class="dropdown-item">
            <i class="fas fa-users mr-2"></i> 8 friend requests
            <span class="float-right text-muted text-sm">12 hours</span>
            </a>
            <div class="dropdown-divider"></div>
            <a href="#" class="dropdown-item">
                <i class="fas fa-file mr-2"></i> 3 new reports
                <span class="float-right text-muted text-sm">2 days</span>
            </a>
            <div class="dropdown-divider"></div>
            <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
        </div>
    </li>
    <li class="nav-item">
    <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#"><i
        class="fas fa-th-large"></i></a>
    </li>
    <li class="nav-item">
    <a class="nav-link" href="model/logout.php"><i class="fas fa-sign-out-alt"></i></a>
    </li>
</ul>
</nav>
<!-- /.navbar -->