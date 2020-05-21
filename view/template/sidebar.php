
<?php
//The $UserInfo and $accesslvl variables are already created in the toprightbar.php which is called before this sidebar.php in the main page.
//$mnuoptions = getMnuOptions($accesslvl['lvl_ord']);
?>
  
  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index.php" class="brand-link">
      <img src="dist/img/MTD.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
          style="opacity: .8">
      <span class="brand-text font-weight-light">MTDesigns</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="<?php giveUserPicLoc($UserInfo);?>" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block"><?php giveUserFullName($UserInfo);?></a>
          <a href="#" class="d-block"><?php getAccessLvl($UserInfo['lvl_ID'], true);?></a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-header">EXAMPLES</li>
          <!-- Add icons to the links using the .nav-icon class
              with font-awesome or any other icon font library -->
<?php
	$loopiter = 0;
	$retval = array();
	$db = Cnnct();
	$sth = $db->prepare("SELECT * FROM NAV_MNU_OPTS WHERE mnu_access_lvl >= ? AND mnu_sub_mnu = 0 ORDER BY mnu_sort ASC");
	$sth->execute([$accesslvl['lvl_ord']]);
	$r = $sth->fetchAll(PDO::FETCH_ASSOC);
  $i = 0;
	foreach($r as $row){
    $retval[$i] = array('mnu_id' => $row['mnu_id'], 'mnu_nme' => $row['mnu_nme'], 'mnu_sub_mnu' => $row['mnu_sub_mnu'], 'mnu_has_sub' => $row['mnu_has_sub'], 'mnu_under' => $row['mnu_under'], 'mnu_sort' => $row['mnu_sort'], 'mnu_access_lvl' => $row['mnu_access_lvl'], 'mnu_ico' => $row['mnu_ico'], 'mnu_pth' => $row['mnu_pth']);
    //$retval[$i]['mnu_nme'];
    $i += 1;
  }
  $db = NULL;
  

$i = 0;
foreach($retval as $mnu){

  if($retval[$i]['mnu_sub_mnu'] == 0){
?>

          <li class="nav-item <?php if($retval[$i]['mnu_has_sub'] == 1){ echo('has-treeview');} ?>">
            <a href="#" class="nav-link" id="<?php echo($retval[$i]['mnu_nme']);?>">
              <i class="nav-icon fas <?php echo($retval[$i]['mnu_ico']);?>"></i>
              <p>
              <?php echo($retval[$i]['mnu_nme']);?>
              
                <?php 
                if($retval[$i]['mnu_has_sub'] == 1){
                  //input little arrow to show there are options within
                  echo("<i class='right fas fa-angle-left'></i>");
                }
                ?>
              </p>
            </a>
<?php 
  }
  if($retval[$i]['mnu_has_sub'] == 0){
?>
          </li>
<?php } else { ?>
  <ul class="nav nav-treeview">
<?php
  $subretval = array();
  $db2 = Cnnct();
	$sth2 = $db2->prepare("SELECT * FROM NAV_MNU_OPTS WHERE mnu_access_lvl >= ? AND mnu_under = ? ORDER BY mnu_sort ASC");
	$sth2->execute([$accesslvl['lvl_ord'], $retval[$i]['mnu_id']]);
	$sr = $sth2->fetchAll(PDO::FETCH_ASSOC);
  $j = 0;
	foreach($sr as $subrow){
    $subretval[$j] = array('mnu_id' => $subrow['mnu_id'], 'mnu_nme' => $subrow['mnu_nme'], 'mnu_sub_mnu' => $subrow['mnu_sub_mnu'], 'mnu_has_sub' => $subrow['mnu_has_sub'], 'mnu_under' => $subrow['mnu_under'], 'mnu_sort' => $subrow['mnu_sort'], 'mnu_access_lvl' => $subrow['mnu_access_lvl'], 'mnu_ico' => $subrow['mnu_ico'], 'mnu_pth' => $subrow['mnu_pth']);
          ?>
              <li class="nav-item">
                <a href="#" class="nav-link" id="<?php echo($subretval[$j]['mnu_nme']);?>">
                  <i class="far <?php echo($subretval[$j]['mnu_ico']); ?> nav-icon"></i>
                  <p>
                    <?php echo($subretval[$j]['mnu_nme']);?>
                  </p>
                </a>
              </li>
            
<?php
    $j += 1;
  }
  $db2 = NULL;
  unset($sr);
  unset($subretval);
?>
            </ul>
          </li>
          <?php
  $i += 1;
  }
}
?>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->


    </div>
    <!-- /.sidebar -->
  </aside>