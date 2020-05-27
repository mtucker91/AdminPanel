<?php
//require('model/connect.php');
$db = Cnnct();
$sth = $db->prepare("SELECT MSG.frm_id, EMP.first_name, EMP.last_name, MSG.msg_cont, MSG.Sent_Dte FROM MSG LEFT JOIN EMPLOYEES AS EMP ON MSG.frm_id = EMP.ID WHERE MSG.to_id = ? ORDER BY MSG.Sent_Dte DESC");
$sth->execute([$UserInfo['ID']]);
$r = $sth->fetchAll(PDO::FETCH_ASSOC);
$i = 0;
foreach($r as $row){
    $retval[$i] = array('frm_id' => $row['frm_id'], 'first_name' => $row['first_name'], 'last_name' => $row['last_name'], 'msg_cont' => $row['msg_cont'], 'Sent_Dte' => $row['Sent_Dte']);
    //console_log($retval[$i]['last_name']);
    $i += 1;
}
$db = NULL;
$i = 0;
$id = 1;
foreach($retval as $mnu){

?>
    <tr>
        <td>
            <div class="icheck-primary">
                <input type="checkbox" value="" id="check<?php echo($id);?>">
                <label for="check<?php echo($id);?>"></label>
            </div>
        </td>
            <td class="mailbox-star"><a href="#"><i class="fas fa-star text-warning"></i></a></td>
            <td class="mailbox-name"><a href="read-mail.html"><?php echo($retval[$i]['first_name'] . ' ' . $retval[$i]['last_name']);?></a></td>
            <td class="mailbox-subject"><?php echo($retval[$i]['msg_cont']) ?>
        </td>
        <td class="mailbox-attachment"><i class="fas fa-paperclip"></i></td>
        <td class="mailbox-date"><?php $tme = getMsgTimeStampDiff($retval[$i]['Sent_Dte']); echo($tme ." ago");?></td>
    </tr>
<?php
$i += 1;
$id += 1;
}

?>