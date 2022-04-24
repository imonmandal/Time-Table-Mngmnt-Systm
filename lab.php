<?php

require_once("Database/DBController.php");
require_once("Database/time_table.php");

$db = new DBController();
$tt = new time_table($db);

$lec = '1';
$day = 'Monday';
$class = 'TE1';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  if (isset($_POST['lab'])) {
    $lec = $_POST['lec'];
    $day = $_POST['day'];
    $class = $_POST['class'];
  }

  if (isset($_POST['enter-data'])) {

    $lec = $_POST['lec'];
    $day = $_POST['day'];
    $class = $_POST['class'];
    $room = $_POST['room'];
    $teacher = $_POST['teacher'];
    $lab = $_POST['lab'];

    $f1 = $tt->doesTableExists($teacher);
    $f2 = $tt->doesTableExists($room);

    if ($f1 == 0 && $f2 == 1) {
      $s = sprintf('<script type="text/javascript">alert("Table of teacher %s is not there in database.");</script>', $teacher);
      echo $s;
    } elseif ($f1 == 1 && $f2 == 0) {
      $s = sprintf('<script type="text/javascript">alert("Table of room %s is not there in database.");</script>', $room);
      echo $s;
    } elseif ($f1 == 0 && $f2 == 0) {
      $s = sprintf('<script type="text/javascript">alert("Table of teacher %s and room %s are not there in database.");</script>', $teacher, $room);
      echo $s;
    } else {

      $t = 0; // teacher don't have another lec
      $r = 0; // room is vacant

      if (!$tt->isCellNull($teacher, 'Lecture_No', $lec, $day)) {
        $t = 1;
      }
      if (!$tt->isCellNull($room, 'Lecture_No', $lec, $day)) {
        $r = 1;
      }

      if ($t == 0 && $r == 0) {
        $dataC = "";

        $L = (int)$lec;
        if ($L % 2 == 0) {
          $L = $L - 1;
        } else {
          $L = $L + 1;
        }
        if ($tt->isCellNull($class, 'Lecture_No', $lec, $day)) {
          $tt->updateTable($class, 'Lecture_No', $L, $day, "|Lab|");

          $dataC = "lab^" . $teacher . "#" . $room . "#" . $lab;
        } else {
          $d = $tt->getData($class, 'Lecture_No', $lec, $day);
          $dataC = $d . "^" . $teacher . "#" . $room . "#" . $lab;
        }

        $tt->updateTable($teacher, 'Lecture_No', $L, $day, "|Lab|");
        $tt->updateTable($room, 'Lecture_No', $L, $day, "|Lab|");

        $dataT = $class . "#" . $room . "#" . $lab;
        $dataR = $class . "#" . $teacher . "#" . $lab;

        $tt->updateTable($class, 'Lecture_No', $lec, $day, $dataC);
        $tt->updateTable($teacher, 'Lecture_No', $lec, $day, $dataT);
        $tt->updateTable($room, 'Lecture_No', $lec, $day, $dataR);
        echo '<script type="text/javascript">alert("Data entered successfully");</script>';
      } elseif ($t == 1 && $r == 0) {
        $s = sprintf('<script type="text/javascript">alert("%s is having another lecture");</script>', $teacher);
        echo $s;
      } elseif ($t == 0 && $r == 1) {
        $s = sprintf('<script type="text/javascript">alert("Room %s is already occupied");</script>', $room);
        echo $s;
      } else {
        $s = sprintf('<script type="text/javascript">alert("%s is having another lecture and Room %s is already occupied");</script>', $teacher, $room);
        echo $s;
      }
    }
  }

  if (isset($_POST['delete-data'])) {

    $lec = $_POST['lec'];
    $day = $_POST['day'];
    $class = $_POST['class'];
    $room = $_POST['room'];
    $teacher = $_POST['teacher'];
    $index = $_POST['index'];

    $cD = $tt->getData($class, 'Lecture_No', $lec, $day); // cell data
    $lbs = explode('^', $cD);

    $L = (int)$lec;
    if ($L % 2 == 0) {
      $L = $L - 1;
    } else {
      $L = $L + 1;
    }
    if (count($lbs) == 2) {
      $tt->updateTable($class, 'Lecture_No', $L, $day, "NULL");

      $tt->updateTable($class, 'Lecture_No', $lec, $day, 'NULL');
    } else {
      array_splice($lbs, $index, 1); // array, index, how many values to delete
      $d = implode('^', $lbs);
      $tt->updateTable($class, 'Lecture_No', $lec, $day, $d);
    }

    $tt->updateTable($teacher, 'Lecture_No', $L, $day, "NULL");
    $tt->updateTable($room, 'Lecture_No', $L, $day, "NULL");

    $tt->updateTable($teacher, 'Lecture_No', $lec, $day, 'NULL');
    $tt->updateTable($room, 'Lecture_No', $lec, $day, 'NULL');
    echo '<script type="text/javascript">alert("Data deleted successfully");</script>';
  }
}
?>

<!DOCTYPE html>
<html>

<head>
  <title>Lab</title>
  <link rel="stylesheet" href="html/scss/style.css">
  <link rel="stylesheet" href="html/scss/lab.css">
</head>

<body>
  <h1>Lab</h1>

  <div class="container">
    <div class="con">
      <h2>Enter Data for Class <?php echo sprintf("%s %s Lab %s", $class, $day, $lec) ?></h2>
      <form action="class.php" method="post" id="form1-<?php echo $day . "-" . $i; ?>"></form>
      <div class="box">
        <?php
        $cD = $tt->getData($class, 'Lecture_No', $lec, $day); // cell data
        $labs = explode('^', $cD);
        for ($i = 1; $i <= 4; $i++) { // i->0to1  count(labs)->2to5 and 1 also for null
          if ($i + 1 <= count($labs) && !$tt->isCellNull($class, 'Lecture_No', $lec, $day)) {
            $arr = explode("#", $labs[$i]);
            $teacherName = $arr[0];
            $roomNo = $arr[1];
            $labName = $arr[2];
        ?>
            <div class="box2">
              <div class="cell">
                <div class="disp-dataCl">
                  Lab :- <?php echo $labName; ?>
                </div>
                <div class="disp-dataCl">
                  Teacher :- <?php echo $teacherName; ?>
                </div>
                <div class="disp-dataCl">
                  Room :- <?php echo $roomNo; ?>
                </div>
                <div class="disp-dataCl">
                  <form action="lab.php" method="post">
                    <input type="hidden" name='class' value='<?php echo $class; ?>'>
                    <input type="hidden" name='teacher' value='<?php echo $teacherName; ?>'>
                    <input type="hidden" name='room' value='<?php echo $roomNo; ?>'>
                    <input type="hidden" name='lec' value='<?php echo $lec; ?>'>
                    <input type="hidden" name='day' value='<?php echo $day; ?>'>
                    <input type="hidden" name='index' value='<?php echo $i; ?>'>
                    <button type="submit" name="delete-data" class="btn2">Delete</button>
                  </form>
                </div>
              </div>
            </div>
          <?php
          } else {
          ?>
            <div class="box2">
              <div class="cell-form">
                <form action="lab.php" method="post">
                  <div class="form-ele">
                    <label>
                      <span>Select Teacher</span> <input required list="teachers-<?php echo $day . "-" . $i; ?>" name="teacher" class="inp">
                      <datalist id="teachers-<?php echo $day . "-" . $i; ?>">
                        <?php
                        $tableT = $tt->getTableData('teacher');
                        foreach ($tableT as $rowT) :
                          if ($rowT['TeacherName']) {
                            if ($tt->isCellNull($rowT['TeacherName'], 'Lecture_No', $lec, $day)) {
                        ?>
                              <option value="<?php echo $rowT['TeacherName']; ?>">
                          <?php }
                          }
                        endforeach;
                          ?>
                      </datalist>
                    </label>
                  </div>
                  <div class="form-ele">
                    <label>
                      <span class="mpx">Select Room</span> <input required list="rooms-<?php echo $day . "-" . $i; ?>" name="room" class="inp">
                      <datalist id="rooms-<?php echo $day . "-" . $i; ?>">
                        <?php
                        $tableR = $tt->getTableData('room');
                        foreach ($tableR as $rowR) :
                          if ($rowR['RoomNo']) {
                            if ($tt->isCellNull($rowR['RoomNo'], 'Lecture_No', $lec, $day)) {
                        ?>
                              <option value="<?php echo $rowR['RoomNo']; ?>">
                          <?php }
                          }
                        endforeach;
                          ?>
                      </datalist>
                    </label>
                  </div>
                  <div class="form-ele">
                    <label>
                      <span class="mpx2">Select Lab</span> <input required list="subjects-<?php echo $day . "-" . $i; ?>" name="lab" class="inp">
                      <datalist id="subjects-<?php echo $day . "-" . $i; ?>">
                        <?php
                        $tableS = $tt->getTableData('subject');
                        foreach ($tableS as $rowS) :
                          if ($rowS['LabName']) {
                        ?>
                            <option value="<?php echo $rowS['LabName']; ?>">
                          <?php }
                        endforeach;
                          ?>
                      </datalist>
                    </label>
                  </div>
                  <div class="form-ele submit">
                    <!-- for form1 -->
                    <input type="hidden" name='class' value='<?php echo $class; ?>'>
                    <input type="hidden" name='lec' value='<?php echo $lec; ?>'>
                    <input type="hidden" name='day' value='<?php echo $day; ?>'>
                    <button type="submit" class="btn2" name="enter-data">Submit</button>
                  </div>
                </form>
              </div>
            </div>
        <?php
          }
        }
        ?>

      </div>
      <div class="submit">
        <form action="class.php" method="get">
          <input type="hidden" name="class-data" value="<?php echo $class; ?>">
          <button class="btn1" name="class">Back</button>
        </form>
      </div>
    </div>
  </div>
</body>

</html>