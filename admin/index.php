<?php
/**
 * Created by IntelliJ IDEA.
 * User: leo
 * Date: 08/09/2015
 * Time: 08:49
 */
$dbfilename = '../oncall.db';
if (file_exists($dbfilename)) {
    $db = new PDO('sqlite:../oncall.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
} else {
    $db = new PDO('sqlite:../oncall.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    $db->exec("create table oncall(engineer VARCHAR(64) , phonenumber VARCHAR (32), oncall SMALLINT, team VARCHAR(32) )");
    $db->exec("CREATE TABLE incomingnumbers(number VARCHAR(32) PRIMARY KEY NOT NULL,team VARCHAR(32) NOT NULL,support_cat VARCHAR(32) NOT NULL);");
}
//$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

if ($_POST["editsubmit"] != "") {
    $engineer = $_POST['engineer'];
    //$phonenumber = htmlspecialchars($_POST['phonenumber']);
    $phonenumber = str_replace(' ', '', $_POST['phonenumber']);
    if (substr($phonenumber, 0, 1) == '0') {
        $phonenumber = '+44' . substr($phonenumber, 1);
    }
    if (isset($_POST['oncall'])) {
        $oncall = 1;
    } else {
        $oncall = 0;
    }
    $team = $_POST["team"];
    $oldname = htmlspecialchars($_POST['oldname']);
    $db->exec("UPDATE oncall SET oncall=0 WHERE team='$team' ");
    if ($oldname == '') {
        $sqlu = "INSERT INTO oncall VALUES('" . $engineer . "','" . $phonenumber . "'," . $oncall . ",'" . $team . "')";
    } else {
        $sqlu = "UPDATE oncall SET engineer='" . $engineer . "', phonenumber='" . $phonenumber . "', oncall=" . $oncall . ", team='" . $team . "' WHERE phonenumber='" . $oldname . "'";
    }
    error_log($sqlu);
    $db->exec($sqlu);
}


$SQL = "SELECT * FROM oncall ORDER BY engineer";
$res = $db->query($SQL);

$SQLin = "SELECT * FROM incomingnumbers ORDER BY team";
$resin = $db->query($SQLin);

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="ISO-8859-1">
    <title>Phone Directory</title>
    <link href="/style.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="/jquery-1.11.0.min.js"></script>
    <style type="text/css">
        #backgroundPopup {
            display: none;
            position: fixed;
            _position: absolute; /* hack for internet explorer 6*/
            height: 100%;
            width: 100%;
            top: 0;
            left: 0;
            background: #000000;
            border: 1px solid #cecece;
            z-index: 1;
        }

        #editrec {
            display: none;
            position: fixed;
            _position: absolute; /* hack for internet explorer 6*/
            height: 180px;
            width: 500px;
            background: #FFFFFF;
            border: 2px solid #cecece;
            z-index: 2;
            padding: 12px;
            font-size: 13px;
        }

        #editrec h1 {
            text-align: left;
            color: #6FA5FD;
            font-size: 22px;
            font-weight: 700;
            border-bottom: 1px dotted #D3D3D3;
            padding-bottom: 2px;
            margin-bottom: 20px;
        }

        #editrecClose {
            font-size: 14px;
            line-height: 14px;
            right: 6px;
            top: 4px;
            position: absolute;
            color: #6fa5fd;
            font-weight: 700;
            display: block;
        }

        #custtable table, #incomingtable {
            color: #000000;
            border: thin;
            border: #999999;
            font-size: small;
        }

        .d0 {
            background-color: #CCCCCC;
        }

        .d1 {
            background-color: #FFFFFF;
        }

    </style>
    <script type="text/javascript">
        var popupStatus = 0;
        function loadPopup() {
            //loads popup only if it is disabled
            if (popupStatus == 0) {
                $("#backgroundPopup").css({
                    "opacity": "0.7"
                });
                $("#backgroundPopup").fadeIn("slow");
                $("#editrec").fadeIn("slow");
                popupStatus = 1;
            }
        }
        function disablePopup() {
            //disables popup only if it is enabled
            if (popupStatus == 1) {
                $("#backgroundPopup").fadeOut("slow");
                $("#editrec").fadeOut("slow");
                popupStatus = 0;
            }
        }
        function centerPopup() {
            //request data for centering
            var windowWidth = document.documentElement.clientWidth;
            var windowHeight = document.documentElement.clientHeight;
            var popupHeight = $("#editrec").height();
            var popupWidth = $("#editrec").width();
            //centering
            $("#editrec").css({
                "position": "absolute",
                "top": windowHeight / 2 - popupHeight / 2,
                "left": windowWidth / 2 - popupWidth / 2
            });
            //only need force for IE6

            $("#backgroundPopup").css({
                "height": windowHeight
            });

        }
        $(document).ready(function () {
            $("#editrecClose").click(function () {
                disablePopup();
            });
            //Click out event!
            $("#backgroundPopup").click(function () {
                disablePopup();
            });
            //Press Escape event!
            $(document).keypress(function (e) {
                if (e.keyCode == 27 && popupStatus == 1) {
                    disablePopup();
                }
            });
        });
        $(document).ready(function () {
            $('#editrecClose').hover(function () {
                $(this).css('cursor', 'pointer');
            }, function () {
                $(this).css('cursor', 'auto');
            });
        });
        function editrec(engineer, phonenumber, oncall, team) {
            $("#edittitle").text("Edit Record");
            $("#engineer").val(engineer);
            $("#phonenumber").val(phonenumber);
            $("#team").val(team)
            if (oncall == 1) {
                $("#oncall").prop('checked', true);
            }
            else {
                $("#oncall").prop('checked', false);
            }
            $("#oldname").val(phonenumber);
            centerPopup();
            loadPopup();
        }
        function addrecord() {

            $("#edittitle").text("Add Record");
            $("#engineer").val($("#nextnum").val());
            $("#oldname").val('');
            $("#phonenumber").val('');
            $("#team").val('');
            centerPopup();
            loadPopup();
        }
        function deleterec(ridx, recname) {
            if (confirm("Delete record for " + recname)) {
                $.ajax({
                    type: 'POST',
                    url: 'deletenumber.php',
                    data: {'recname': recname},
                    success: function (data) {
                        document.getElementById("dirtable").deleteRow(ridx + 1);
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        alert(xhr.status);
                        alert(thrownError);
                    }
                });
            }
            else {
                return false;
            }
        }
        function deleterecin(ridx, number) {
            alert("Function Not yet Implemented");
            return false;
        }
        function editrecin(number, team, supportcat) {
            alert("Function not yet implemented");
            return false;
        }

    </script>
</head>
<body>
<div align="center" style="height: 30px; width: 600px; overflow: hidden;">
    <table width="90%" border="0">
        <tr>
            <th>On Call Engineers</th>
        </tr>
    </table>
</div>
<div id="custtable" align="center" id="customer table" style="height: 300px; overflow: auto; width: 600px;">
    <table id="dirtable" width="90%">
        <tr>
            <td>&nbsp;</td>
            <th>Engineer</th>
            <th>Phone Number</th>
            <th>Team</th>
            <th>On Call</th>
            <td>&nbsp;</td>
        </tr>
        <?php
        $rowidx = 0;
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            ?>
            <tr align="left" class="d<?php echo $rowidx % 2 ?>">
                <td><a href="#"
                       onclick="editrec('<?php echo $row['engineer'] ?>','<?php echo $row['phonenumber'] ?>','<?php echo $row['oncall'] ?>', '<?php echo $row['team'] ?>')">Edit</a>
                </td>
                <td><?php echo $row['engineer'] ?></td>
                <td><?php echo $row['phonenumber'] ?></td>
                <td><?php echo $row['team'] ?></td>
                <td>
                    <?php if ($row['oncall'] == 1) {
                        echo('On Call');
                    } else {
                        echo('&nbsp;');
                    }
                    ?>
                </td>
                <td><a href="#" onclick="deleterec(<?php echo $rowidx ?>,'<?php echo $row['engineer'] ?>')">Delete</a>
                </td>
            </tr>

            <?php
            $rowidx++;
        }
        ?>

    </table>
    <input type="hidden" name="nextnum" id="nextnum" value="<?php echo $nextnum; ?>">
    <div><input type="button" name="addrec" id="addrec" onclick="addrecord()" value="Add Record"></div>
</div>

<div align="center" style="height: 30px; width: 600px; overflow: hidden;">
    <table width="90%" border="0">
        <tr>
            <th>Incoming Phone Numbers</th>
        </tr>
    </table>
</div>
<div id="incoming" align="center" id="incoming" style="height: 300px; overflow: auto; width: 600px;">
    <table id="incomingtable" width="90%">
        <tr>
            <td>&nbsp;</td>
            <th>Phone Number</th>
            <th>Team</th>
            <th>Support Category</th>
            <td>&nbsp;</td>
        </tr>
        <?php
        $rowidx = 0;
        while ($rowin = $resin->fetch(PDO::FETCH_ASSOC)) {
            ?>
            <tr align="left" class="d<?php echo $rowidx % 2 ?>">
                <td><a href="#"
                       onclick="editrecin('<?php echo $rowin['number'] ?>','<?php echo $rowin['team'] ?>','<?php echo $rowin['support_cat'] ?>')">Edit</a>
                </td>
                <td><?php echo $rowin['number'] ?></td>
                <td><?php echo $rowin['team'] ?></td>
                <td><?php echo $rowin['support_cat'] ?></td>
                <td><a href="#" onclick="deleterecin(<?php echo $rowidx ?>,'<?php echo $rowin['number'] ?>')">Delete</a>
                </td>
            </tr>

            <?php
            $rowidx++;
        }
        ?>

</table>
</div>

<?php
$db = null;
?>
<div id="editrec">
    <a id="editrecClose">x</a>

    <h1 id="edittitle" name="edittitle">Edit Customer</h1>

    <form method="post" name="editform">
        <input type="hidden" name="oldname" id="oldname">
        <table border="0">
            <tr>
                <td align="right">Engineer</td>
                <td><input type="text" name="engineer" id="engineer"></td>
            </tr>
            <tr>
                <td align="right">Phone Number</td>
                <td><input type="text" name="phonenumber" id="phonenumber" maxlength="64"></td>
            </tr>
            <tr>
                <td align="right">Team</td>
                <td><select id="team" name="team">
                        <option value="Server">Server</option>
                        <option value="Network">Network</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td align="right">On Call</td>
                <td><input type="checkbox" name="oncall" id="oncall"></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td align="right"><input type="submit" name="editsubmit" value="Update"></td>
            </tr>
        </table>
    </form>
</div>
<div id="backgroundPopup"></div>

<!--<div><input type="button" name="printpage" id="printpage" onclick="window.print();" value="Print Page"></div>-->

</body>
</html>
