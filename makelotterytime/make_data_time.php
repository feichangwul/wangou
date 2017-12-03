<?php
$conn = mysql_connect("localhost:3306","root","");
mysql_set_charset('utf8',$conn);
mysql_select_db("lottery",$conn);
$query = "SELECT id,title FROM lottery_type where enable=1 and isDelete=0" ;

$query0="SELECT id,title FROM lottery_type where enable=0 and isDelete=0";

$query_all="SELECT id,title FROM lottery_type";
$lottery_type_all= mysql_query($query_all);
// echo $_SERVER['PHP_SELF']; ///makelotterytime/make_data_time.php
// echo $_SERVER['REQUEST_URI'];
?>
 <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>时间管理</title>
    <!-- Bootstrap -->
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="js/jquery-1.11.3.min.js"></script>
    <script type="text/javascript" src="js/timingfield.js"></script>
    <link href="css/timingfield.css" rel="stylesheet">
    <script src="js/bootstrap.js"></script>
    <link href="css/bootstrap.css" rel="stylesheet">
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $(".timing").timingfield();
            $(".timing1").timingfield();
            $(".timing2").timingfield();
        });
        </script>
    <style>
    .ccenter{
    margin: 0 auto;
    }
    </style>
  </head>
  <body>
<?php
 if(isset($_POST['submit1']))
 {
   $type= $_POST['type'];
   $sec_actionTime= $_POST['actionTime'];
   $actionTime=gmdate("H:i:s", $_POST['actionTime']);
   $sec_stopTime= $_POST['stopTime'];
   $stopTime= gmdate("H:i:s", $_POST['stopTime']);
   $sec_incTime= $_POST['incTime'];



$type_arr = json_decode(urldecode($_POST['type_arr']),true);
$query1 = "SELECT * FROM lottery_data_time where type=".$type." order by actionNo" ;
$lottery_time=  mysql_query($query1);
// $setzero ="SET SQL_SAFE_UPDATES = 0;
// UPDATE lottery_data_time set actionTime = '0' ,stopTime= '0' WHERE type=".$type.";
// SET SQL_SAFE_UPDATES = 1;";
// $setoquery=  mysql_query($setzero);
?>
  <table class="table-bordered" width="100%">
    <thead>
      <tr class="success">
        <th class="danger">id</th>
        <th class="info">type</th>
        <th>actionNo</th>
        <th>actionTime</th>
        <th>stopTime</th>
      </tr>
    </thead>
    <tbody>
    <?php

    if( $_POST['select']==1)
    {
      $upactionTime=$sec_actionTime;
      $upstopTime = $sec_stopTime;

      while($row1 = mysql_fetch_array($lottery_time))
                {
              $upd = "UPDATE `lottery_data_time` SET `actionTime`='".gmdate("H:i:s", $upactionTime)."', `stopTime`='".gmdate("H:i:s", $upstopTime)."' WHERE `id`=".$row1['id'];
               mysql_query($upd);
               $upactionTime +=$sec_incTime;
               $upstopTime  += $sec_incTime;
                }
    }
    else if( $_POST['select']==2)
    {
//#####################################php excel begin ###########################################
      define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

/** Include PHPExcel */
// require_once '../Build/PHPExcel.phar';
require_once dirname(__FILE__) . '/PHPExcel/Classes/PHPExcel.php';
    $atoz= array();
    $j=1;
    for($i = 'A'; $i <= 'Z'; $i++)
    {
             $atoz[$j] = $i;
             $j++;
    }

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("Martin Han")
               ->setLastModifiedBy("Martin Han")
               ->setTitle("PHPExcel for lottery_type")
               ->setSubject("PHPExcel for lottery_type")
               ->setDescription("Test document for PHPExcel, by Martin")
               ->setKeywords("office PHPExcel php")
               ->setCategory("Test result file");
//-----------------------------------------------------------------------------
// Add some data
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'id')
            ->setCellValue('B1', 'type')
            ->setCellValue('C1', '期号')
            ->setCellValue('D1', '开奖时间')
            ->setCellValue('E1', '停止投注时间');
$final = "SELECT * FROM lottery_data_time where type=".$type." order by actionNo";
        $lottery_time_final=  mysql_query($final);
        while($row2 = mysql_fetch_array($lottery_time_final))
                {
                  echo "<tr>
                  <td>".$row2['id']."</td>
                  <td>".$type_arr{$type}."</td>
                  <td>".$row2['actionNo']."</td>
                  <td>".$row2['actionTime']."</td>
                  <td>".$row2['stopTime']."</td></tr>";
            $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue($atoz[1].($row2['id']+1), $row2['id'])
                        ->setCellValue($atoz[2].($row2['id']+1), $type_arr{$type})
                        ->setCellValue($atoz[3].($row2['id']+1), $row2['actionNo'])
                        ->setCellValue($atoz[4].($row2['id']+1), $row2['actionTime'])
                        ->setCellValue($atoz[5].($row2['id']+1), $row2['stopTime']);
                }
//-----------------------------------------------------------------------------

// Rename worksheet
// echo date('H:i:s') , " Rename worksheet".$type_arr{$type} , EOL;
$objPHPExcel->getActiveSheet()->setTitle($type_arr{$type});


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


//################################################# Save Excel 2007 file#################################################
// echo date('H:i:s') , " Write to Excel2007 format" , EOL;
$callStartTime = microtime(true);
// //  header("Pragma: public");
//  //  header("Expires: 0");
//  //  header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
//   header("Content-Type:application/force-download");
//   // header("Content-Type:application/vnd.ms-execl"); //
//   header("Content-Type:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
//   // header("Content-Type:application/octet-stream");
//   // header("Content-Type:application/download");;
//   header("Content-Disposition:attachment;filename=".$type_arr{$type}.".xlsx");
  $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
// $objWriter->save(str_replace('.php', '.xlsx', __FILE__));
//---------------------------------------------------------------
$file =explode('\\',__FILE__);
  //echo end($aa);
   $front="";
   for ($i=0;$i<sizeof($file)-1;$i++)
   {
       $front.=$file[$i]."\\";
   }
   // $file =$front.$type_arr{$type}.'.xlsx';
   $file =$front.'aa.xlsx';
   // rename("/tmp/tmp_file.txt", "/home/user/login/docs/my_file.txt");
 $objWriter->save($file);

  // $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
  // $objWriter->save($file);
//---------------------------------------------------------------
// $callEndTime = microtime(true);
// $callTime = $callEndTime - $callStartTime;

// echo date('H:i:s') , " File written to " , str_replace('.php', '.xlsx', pathinfo(__FILE__, PATHINFO_BASENAME)) , EOL;
// echo 'Call time to write Workbook was ' , sprintf('%.4f',$callTime) , " seconds" , EOL;
// // Echo memory usage
// echo date('H:i:s') , ' Current memory usage: ' , (memory_get_usage(true) / 1024 / 1024) , " MB" , EOL;


// #################################################Save Excel5 file#################################################
// echo date('H:i:s') , " Write to Excel5 format" , EOL;
// $callStartTime = microtime(true);

// $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
// $objWriter->save(str_replace('.php', '.xls', __FILE__));
// $callEndTime = microtime(true);
// $callTime = $callEndTime - $callStartTime;

// echo date('H:i:s') , " File written to " , str_replace('.php', '.xls', pathinfo(__FILE__, PATHINFO_BASENAME)) , EOL;
// echo 'Call time to write Workbook was ' , sprintf('%.4f',$callTime) , " seconds" , EOL;
// // Echo memory usage
// echo date('H:i:s') , ' Current memory usage: ' , (memory_get_usage(true) / 1024 / 1024) , " MB" , EOL;


// // Echo memory peak usage
// echo date('H:i:s') , " Peak memory usage: " , (memory_get_peak_usage(true) / 1024 / 1024) , " MB" , EOL;

// // Echo done
// echo date('H:i:s') , " Done writing files" , EOL;
// echo 'Files have been created in ' , getcwd() , EOL;

//#####################################php excel end ###########################################
 // include_once("simple_xl/excel.php");
    // $title = "Sheet1";
    // $colors = array("red", "blue", "green", "yellow", "orange", "purple");

    // $xls = new Excel($title);

    // foreach ($colors as $color)
    // {
    //     $xls->home();
    //     $xls->label($color);
    //     $xls->right();
    //     $xls->down();
    // };
    // ob_start();
    // $data = ob_get_clean();
    // file_put_contents('report.xls', $data);
    //-----------------------------------------------------------------------------

      // $title = $type_arr{$type};
      // $colors = array("red", "blue", "green", "yellow", "orange", "purple");

      // ob_start();
      // $xls = new Excel($title);
      // $xls->top();
      // $xls->home();
      // foreach ($colors as $color)
      // {
      // $xls->label($color);
      // $xls->right();
      // $xls->down();
      // };
      // $data = ob_get_clean();
      // file_put_contents('report.xls', $data);
      // #############################################################################
    }
    $final = "SELECT * FROM lottery_data_time where type=".$type." order by actionNo";
        $lottery_time_final=  mysql_query($final);
        while($row2 = mysql_fetch_array($lottery_time_final))
                {
                  echo "<tr>
                  <td>".$row2['id']."</td>
                  <td>".$type_arr{$type}."</td>
                  <td>".$row2['actionNo']."</td>
                  <td>".$row2['actionTime']."</td>
                  <td>".$row2['stopTime']."</td></tr>";
                }
      ?>
    </tbody>
  </table>
<?php
 }
else if(isset($_POST['submit0']))
{
  $type_arr = json_decode(urldecode($_POST['type_arr']),true);
  if($_POST['to_open_type'] !== '00'){
    $open_query = "UPDATE `lottery_type` SET `enable`='".$_POST['to_open_type']."' WHERE `id`='".$_POST['to_open']."'";
    mysql_query($open_query);
  }


  ?>
  <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-6 col-md-offset-3">
          <h1 class="text-center">创建更改时间</h1>
        </div>
      </div>
      <hr>
      <div class="row">
        <div class="col-md-8 col-md-offset-4">
          <div class="col-md-3">
            <!-- <label>彩种</label> -->
            <h2><span class="label label-default">彩种</span></h2>
          </div>
          <div class="col-md-3">
            <!-- <label>开奖时间</label> -->
            <h2><span class="label label-default">开奖时间</span></h2>
          </div>
          <div class="col-md-6">
            <!-- <label>停止投注时间</label> -->
            <h2><span class="label label-default">停止投注时间</span></h2>
          </div>
        </div>
        <!-- <div class="col-md-4"></div> -->
      </div>
      <br>
      <div class="row">
        <div class="col-md-8 col-md-offset-4">
          <div class="col-md-3">
            <select class="form-control input-lg ccenter" name="type">
            <?php
                   $lottery_type=  mysql_query($query);
                  while($row = mysql_fetch_array($lottery_type))
                {
                  echo "<option value=".$row['id'].">".$row['title']."</option>";
                  // $type_arr[$row['id']] = $row['title'];
                }
             ?>
          </select>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <input id="timeformat1" type="text" name="actionTime" required="true" class="timing form-control input-lg ccenter time" style="width:94px;">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <input id="timeformat2" type="text" name="stopTime" required="true" class="timing1 form-control input-lg ccenter time" style="width:94px;">
            </div>
          </div>
        </div>
        <!-- <div class="col-md-4"></div> -->
      </div>
      <br>
      <div class="row">
       <div class="col-md-8 col-md-offset-4">
        <div class="col-md-3">
             <select class="form-control input-lg ccenter" name="select">
                <option value="0">只看表</option>
                <option value="1">我要改表时间</option>
                <option value="2">我要导出文件</option>
             </select>
          </div>
          <div class="col-md-1">
              <h2><span class="label label-default">差距</span></h2>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <input id="timeformat3" type="text" name="incTime" required="true" class="timing2 form-control input-lg ccenter time" style="width:94px;">
            </div>
          </div>
          <div class="col-md-5">
              <div class="col-md-4">
                <div class="form-group">
                    <input type="submit" name="submit1" class="form-control btn btn-success" value="执行" style="height:64px;">
                  </div>
              </div>
              <div class="col-md-8">
              </div>
          </div>
        </div>
        <!-- <div class="col-md-4"></div> -->
      </div>
      <br>
      <input type="hidden" name='type_arr' value="<?php echo urlencode(json_encode($type_arr)); ?>">
      </form>
  <?php }

  else{
  ?>
  <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-6 col-md-offset-3">
          <h1 class="text-center">创建更改时间</h1>
        </div>
      </div>
      <hr>
      <div class="row">
        <!-- <div class="col-md-9 col-md-offset-3"> -->
        <div class="col-md-10 col-md-offset-2">
          <div class="col-md-3">
            <!-- <label>彩种</label> -->
            <h2><span class="label label-default">已开彩种</span></h2>
          </div>
          <div class="col-md-3">
            <!-- <label>开奖时间</label> -->
            <h2><span class="label label-default">关闭彩种</span></h2>
          </div>
          <div class="col-md-6">
            <!-- <label>停止投注时间</label> -->
            <h2><span class="label label-default">开关操作</span></h2>
          </div>
        </div>
        <!-- <div class="col-md-4"></div> -->
      </div>
      <br>
      <div class="row">
        <div class="col-md-10 col-md-offset-2">
          <div class="col-md-3">
            <select class="form-control input-lg ccenter" name="open">
            <option>查看开启的彩种</option>
            <?php
             $lottery_type=  mysql_query($query);
                  while($row = mysql_fetch_array($lottery_type))
                {
                  echo "<option value=".$row['id'].">".$row['title']."</option>";
                }
             ?>
          </select>
          </div>
          <div class="col-md-3">
            <select class="form-control input-lg ccenter" name="close">
            <option>查看关闭的彩种</option>
            <?php
            $lottery_type0= mysql_query($query0);
                  while($row = mysql_fetch_array($lottery_type0))
                {
                  echo "<option value=".$row['id'].">".$row['title']."</option>";
                }
             ?>
          </select>
          </div>
          <div class="col-md-3">
             <select class="form-control input-lg ccenter" name="to_open">
            <?php
                  while($row = mysql_fetch_array($lottery_type_all))
                {
                  echo "<option value=".$row['id'].">".$row['title']."</option>";
                  $type_arr[$row['id']] = $row['title'];
                }
             ?>
          </select>
          </div>
          <div class="col-md-3">
            <select class="form-control input-lg ccenter" name="to_open_type">
                <option value="00">不动</option>
                <option value="1">开启</option>
                <option value="0">关闭</option>
             </select>
          </div>
        </div>
        <!-- <div class="col-md-4"></div> -->
      </div>
      <br>
      <div class="row">
       <div class="col-md-10 col-md-offset-2">
              <div class="col-md-3 col-md-offset-9">
                <div class="form-group">
                    <input type="submit" name="submit0" class="form-control btn btn-success" value="执行" style="height:64px;">
                  </div>
              </div>
        </div>
      </div>
      <br>
      <input type="hidden" name='type_arr' value="<?php echo urlencode(json_encode($type_arr)); ?>">
      </form>

  <?php
}
?>
  </body>
  </html>