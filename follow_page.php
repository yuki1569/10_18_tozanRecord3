<?php
session_start();
include("functions.php");
$pdo = connect_to_db();
check_session_id();
$user_id = $_SESSION["user_id"];

$sql = 'SELECT * FROM follow_table
  INNER JOIN tozan_record_table ON tozan_record_table.user_id = follow_table.follow_user_id 
  INNER JOIN users_table ON
  tozan_record_table.user_id = users_table.id
  WHERE follow_table.user_id = :user_id;
  ORDER BY created_at DESC';



$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$records = $stmt->fetchAll();

// print_r( $records[0]['username']);




// if ($_SERVER['REQUEST_METHOD'] != 'POST') {
//   // 画像を取得
//   $sql = 'SELECT * FROM tozan_record_table WHERE user_id=:user_id ORDER BY created_at DESC';
//   $stmt = $pdo->prepare($sql);
//   $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
//   $stmt->execute();
//   $records = $stmt->fetchAll();

//   $sql = 'SELECT * FROM users_table
// WHERE id=:user_id ';
//   $stmt = $pdo->prepare($sql);
//   $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
//   $stmt->execute();
//   $status = $stmt->fetch();
// } else {
//   exit();
// }
// unset($pdo);

?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="utf-8">
  <title>Image Test</title>

  <!DOCTYPE html>
  <html lang="ja">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>登山記録</title>

  <link rel="stylesheet" href="css/panel-btn-icon.css">
  <link rel="stylesheet" href="css/panel-menu.css">
  <link rel="stylesheet" href="css/styles.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

  <!-- <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script> -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

  <style>
    .table {
      /* border-collapse: collapse; */
      table-layout: fixed;
    }

    .table th,
    .table td {
      /* border: 1px solid #CCCCCC; */
      padding: 5px 10px;
      text-align: left;
    }

    .table th {
      background-color: #FFFFFF;
    }

    main {
      margin-top: 50px;
      position: fixed;
      top: 0;
      width: 100%;
      height: 1000px;
      opacity: 1;
      display: none;
      z-index: 10;
    }

    main.on {
      display: inline;
    }

    .thumbnail {
      width: 32.3333%;
      display: inline-block;
      vertical-align: top;
      /* 要素を上揃えにする */
      margin-bottom: 10px;
      padding: 10px;
      box-sizing: border-box;
      /* 崩れ防止 */
    }

    #open,
    #close {
      cursor: pointer;
      width: 200px;
      border: 1px solid #ccc;
      border-radius: 4px;
      text-align: center;
      padding: 12px;
      margin: 16px auto 0;
      background: #4caf50;
      color: white;
    }

    #mask {
      background: rgba(0, 0, 0, 0.4);
      position: fixed;
      top: 0;
      bottom: 0;
      right: 0;
      left: 0;
      z-index: 1;
    }

    #modal {
      background: #fff;
      color: #555;
      width: 55%;
      padding: 40px;
      border-radius: 4px;
      position: absolute;
      top: 40px;
      left: 0;
      right: 0;
      margin: 0 auto;
      z-index: 2;
    }

    #modal p {
      margin: 0 0 20px;
    }

    #mask.hidden {
      display: none;
    }

    #modal.hidden {
      transform: translate(0, -10000px);
    }
  </style>

</head>


<body style="width: 70%; margin: 0 auto;">

  <!-- モーダル表示 -->
  <div id="mask" class="hidden"></div>
  <section id="modal" class="hidden">

  </section>

  <!-- パネルを閉じる用 -->
  <main></main>
  <!-- パネルメニュー -->
  <div class="panel-menu-container">
    <div class="panel-menu">
      <div class="panel-menu-list">


        <form action="execution.php/create.php" method="post" enctype="multipart/form-data">
          <h3>登山記録</h3>

          <table class="table">
            <input type="hidden" name=" user_id" value="<?= $user_id ?>">
            <tr>
              <td>山名:</td>
              <td><input type="text" name="name"></td>
            </tr>
            <tr>
              <td>日付:</td>
              <td><input type="date" name="date"></td>
            </tr>
            <tr>
              <td>時間:</td>
              <td><input type="time" value="00:00:00" step="300" name="time"></td>
            </tr>
            <tr>
              <td>距離:</td>
              <td><input type="text" name="distance"></td>
            </tr>
            <tr>
              <td>最大標高:</td>
              <td><input type="text" name="maximumAltitude"></td>
            </tr>
            <tr>
              <td>画像を選択</td>
              <td><input type="file" name="image" required></td>
            </tr>
            </tbody>

          </table>
          <button type="submit" class="btn btn-primary">保存</button>
        </form>
      </div>
    </div>
  </div>

  <!-- <a id="panel-btn" style="display:block;">+ -->
  <!-- <span id="panel-btn-icon"></span> -->
  <!-- </a><br> -->
  <div id="navbar">
    <ul>
      <li><a href="input.php">TOP</a></li>
      <li><a href="follow_page.php">フォロー</a></li>
      <li><a href="find_page.php">探す</a></li>
      <li><a href="data.php">Record</a></li>
      <li><a href="profile_edit.php">プロフィール</a></li>
    </ul>
  </div>

  <!-- フォローしているユーザーの画像一覧 -->
  <div class="container mt-5">
    <?php for ($i = 0; $i < count($records); $i++) : ?>
      <div class="thumbnail">
        <p><?php echo $records[$i]['username'] ?>さんの投稿</p>
        <a class="modal-btn">
          <img src="images/<?php echo $records[$i]['image_name']; ?>" data-id="<?= $records[$i]['image_id'] ?>" width="100%" height="auto" class="mr-3">
        </a>

        <div class="media-body">
          <!-- <h5><?= $records[$i]['name']; ?> (<?= $records[$i]['maximumAltitude']; ?>m)</h5>
          <h5>日時 <?= $records[$i]['date']; ?></h5>
          <h5>活動時間 <?= $records[$i]['time']; ?></h5>
          <h5>歩いた距離 <?= $records[$i]['distance'] / 1000; ?>km</h5> -->
        </div>
      </div>
    <?php endfor; ?>

  </div>

  <a href="execution.php/logout.php">logout</a>

  <script>
    $('#panel-btn').on('click', function() {
      if ($('.panel-menu').hasClass('active-menu')) {
        $('.panel-menu').removeClass('active-menu');
        $('.panel-menu').addClass('active-menu-remove');
        $('main').removeClass('on');

      } else {
        $('.panel-menu').addClass('active-menu');
        $('.panel-menu').removeClass('active-menu-remove');
        $('main').addClass('on');
      }
    });

    //サイドメニューが表示された時、ほかの部分をクリックするとサイドメニューが閉じる
    $('main').on('click', function() {
      $('.panel-menu').removeClass('active-menu');
      $('.panel-menu').addClass('active-menu-remove');
      $('main').removeClass('on');
      $("#panel-btn-icon").toggleClass("close");
    });

    //パネルボタンのアニメーション
    $(function() {
      $("#panel-btn").click(function() {
        $("#panel").slideToggle(200);
        $("#panel-btn-icon").toggleClass("close");
        $("*").removeClass("panel-button-active");
        return false;
      });
    });

    const targets = document.getElementsByClassName('modal-btn');

    for (let i = 0; i < targets.length; i++) {
      targets[i].addEventListener('click', () => {

        let records = JSON.parse('<?php echo json_encode($records) ?>');
        // alert(records[i]['name']);

        modal.classList.remove('hidden');
        mask.classList.remove('hidden');

        var mydiv = document.getElementById("modal");
        mydiv.innerHTML =
          `<h3>${records[i]['name']}</h3>
            <img src="images/${records[i]['image_name']}" data-id="${records[i]['image_id'] }" width="100%" height="auto" class="mr-3">
            <h5>${records[i]['name']} (${records[i]['maximumAltitude']}m)</h5>
            <h5>日時 ${records[i]['date']}</h5>
            <h5>活動時間 ${records[i]['time']}</h5>
            <h5>歩いた距離 ${records[i]['distance'] / 1000}km</h5>
            <div id="close">閉じる</div>
           `;
      }, false);
    }

    // const open = document.getElementById('open');
    const close = document.getElementById('close');
    const modal = document.getElementById('modal');
    const mask = document.getElementById('mask');

    // open.addEventListener('click', function() {
    //   modal.classList.remove('hidden');
    //   mask.classList.remove('hidden');
    // });
    $(document).on('click', '#close', function() {
      modal.classList.add('hidden');
      mask.classList.add('hidden');
    });
  </script>

  <!-- <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script> -->
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>

</html>