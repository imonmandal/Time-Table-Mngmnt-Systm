<!DOCTYPE html>
<html>

<head>
  <?php

  function initDB()
  {
    // Database connection properties
    $host = 'localhost';
    $user = 'root';
    $password = '0110';
    $database = 'time_table';

    $con = mysqli_connect($host, $user, $password);
    if ($con->connect_error) {
      echo "Fail " . $con->connect_error;
    } else {
      $query_string = "create database `{$database}`;";
      $result = $con->query($query_string);

      if ($result) {
        mysqli_select_db($con, $database); // select database

        $query_string = "CREATE TABLE `class` (`ClassName` varchar(75));";
        mysqli_query($con, $query_string);

        $query_string = "CREATE TABLE `teacher` (`TeacherName` varchar(75), `MaxNoOfLec` varchar(5));";
        mysqli_query($con, $query_string);

        $query_string = "CREATE TABLE `room` (`RoomNo` varchar(75));";
        mysqli_query($con, $query_string);

        $query_string = "CREATE TABLE `subject` (`SubjectName` varchar(75), `LabName` varchar(75));";
        mysqli_query($con, $query_string);

        $query_string = "CREATE TABLE `week`(`days` varchar(25));";
        mysqli_query($con, $query_string);
        $query_string = "INSERT INTO `week` values ('Monday'), ('Tuesday'), ('Wednesday'), ('Thursday'), ('Friday'), ('Saturday');";
        mysqli_query($con, $query_string);

        $query_string = "CREATE TABLE `lec`(`sr_no` int, `lec` int);";
        mysqli_query($con, $query_string);
        $query_string = "INSERT INTO `lec` values(1, NULL);";
        mysqli_query($con, $query_string);
      }
      $con->close();
    }
  }

  initDB();

  require_once("Database/DBController.php");
  require_once("Database/time_table.php");

  $db = new DBController();
  $tt = new time_table($db);

  if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['del-msg'])) {
      echo '<script type="text/javascript">alert("Data deleted successfully");</script>';
    }
    if (isset($_GET['class-table-not-pres'])) {
      echo sprintf('<script type="text/javascript">alert("Table of class %s is not there in database");</script>', $_GET['class-table-not-pres']);
    }
    if (isset($_GET['teacher-table-not-pres'])) {
      echo sprintf('<script type="text/javascript">alert("Table of teacher %s is not there in database");</script>', $_GET['teacher-table-not-pres']);
    }
    if (isset($_GET['room-table-not-pres'])) {
      echo sprintf('<script type="text/javascript">alert("Table of room %s is not there in database");</script>', $_GET['room-table-not-pres']);
    }
  }

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['clear'])) {
      $tt->dropDatabase();
      header("Location: index.php?del-msg=1");
      exit();
    }

    if (isset($_POST['enter'])) {

      $tt->clear();

      $cA = $_FILES['cls'];
      $tA = $_FILES['techr'];
      $rA = $_FILES['rm'];
      $sA = $_FILES['sb'];
      $n = $_POST['l'];
      $tt->createTables($cA, $tA, $rA, $sA, $n);
      echo '<script type="text/javascript">alert("Data entered successfully");</script>';
    }

    if (isset($_POST['download'])) {

      $p = $_POST['path'];
      if (file_exists($p . "\\table_data_download")) {
        echo '<script type="text/javascript">alert("This path already has folder table_data_download pls select another path or delete that folder");</script>';
      } else {
        $tt->download($p);
        echo '<script type="text/javascript">alert("Data downloaded successfully");</script>';
      }
    }
  }

  ?>
  <title>Time Table</title>
  <link rel="stylesheet" href="html/scss/index.css">
  <style>
    .btm {
      display: flex;
      align-items: center;
      justify-content: center;
      margin-top: 1rem;
    }

    .btm label {
      font-size: 1.5rem;
    }

    .btm label input {
      width: 20rem;
      height: 1.25rem;
      padding: 0 .75rem;
      border: 3px solid rgb(167, 166, 166);
    }

    .btm button {
      font-size: 1rem;
      padding: 0 .5rem;
      border-radius: 2.5px;
    }
  </style>
</head>

<body>
  <h1>Time Table Management System</h1>

  <div class="container">
    <div class="con">
      <form action="index.php" method="post" id="form1" enctype="multipart/form-data"></form>
      <form action="index.php" method="post" id="form2"></form>
      <div class="fm-con1">
        <div class="fm-ele1">
          <label for="cls"><span>Class</span></label>
          <input form="form1" required type="file" placeholder="Upload Class Excelsheet" name="cls" id="cls" />
        </div>
        <div class="fm-ele1">
          <label for="techr"><span>Teacher</span></label>
          <input form="form1" required type="file" placeholder="Upload Teacher Excelsheet" name="techr" id="techr" />
        </div>
        <div class="fm-ele1">
          <label for="rm"><span>Room</span></label>
          <input form="form1" required type="file" placeholder="Upload Room Excelsheet" name="rm" id="rm" />
        </div>
        <div class="fm-ele1">
          <label for="sb"><span>Subject</span></label>
          <input form="form1" required type="file" placeholder="Upload Subject Excelsheet" name="sb" id="sb" />
        </div>
        <div class="fm-ele1">
          <label for="l"><span>No of Lec</span></label>
          <input form="form1" required type="text" placeholder="Enter Number of Lectures" name="l" id="l" />
        </div>
        <div class="fm-ele1">
          <button form="form1" type="submit" name="enter"><span>Enter Data</span></button>
          <button form="form2" type="submit" name="clear"><span>Clear Database</span></button>
        </div>
      </div>
    </div>

    <div class="con fm-con2">
      <h2>View Table</h2>
      <div class="fm-ele2">
        <form action="class.php" method="get">
          <label>
            Select Class <input list="classes" name="class-data">
            <datalist id="classes">
              <?php
              $tableC = $tt->getTableData('class');
              foreach ($tableC as $rowC) :
              ?>
                <option value="<?php echo $rowC['ClassName']; ?>">
                <?php endforeach;
                ?>
            </datalist>
          </label>
          <button type="submit" name="class">Submit</button>
        </form>
      </div>
      <div class="fm-ele2">
        <form action="teacher.php" method="get">
          <label>
            Select Teacher <input list="teachers" name="teacher-data">
            <datalist id="teachers">
              <?php
              $tableT = $tt->getTableData('teacher');
              foreach ($tableT as $rowT) :
              ?>
                <option value="<?php echo $rowT['TeacherName']; ?>">
                <?php endforeach;
                ?>
            </datalist>
          </label>
          <button type="submit" name="teacher">Submit</button>
        </form>
      </div>
      <div class="fm-ele2">
        <form action="room.php" method="get">
          <label>
            Select Room <input list="rooms" name="room-data">
            <datalist id="rooms">
              <?php
              $tableR = $tt->getTableData('room');
              foreach ($tableR as $rowR) :
              ?>
                <option value="<?php echo $rowR['RoomNo']; ?>">
                <?php endforeach;
                ?>
            </datalist>
          </label>
          <button type="submit" name="room">Submit</button>
        </form>
      </div>
    </div>
  </div>

  <div class="btm">
    <form action="index.php" method="post">
      <label>
        Enter Download Path
        <input type="text" name="path" required>
      </label>
      <button type="submit" name="download">Download</button>
    </form>
  </div>
</body>

</html>