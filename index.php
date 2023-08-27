<!DOCTYPE html>
<html lang="jp">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>文字数カウント付きメモ</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <style>
    body {
      background: #f3f3f3;
      font-family: sans-serif;
    }

    .box-color {
      background: #efefef;
    }
  </style>
  <script>
    history.pushState(null, null, location.href);
    window.addEventListener('popstate', (e) => {
      history.go(1);
    });
    function KeyUp(value, endStr) {
      Length = value.replace(/\s/g, "").length;
      var buttonType = "submit_button_" + endStr;
      var lengthType = "length_" + endStr;
      if (Length != 0) {
        document.getElementById(buttonType).removeAttribute("disabled");
      } else {
        document.getElementById(buttonType).setAttribute("disabled", true);
      }
      document.getElementById(lengthType).textContent = Length + "文字";
    }
    function edit_modal(ID, edit_Length) {
      var specializedId = "contentBy" + ID;
      var content = document.getElementById(specializedId).textContent;
      document.getElementById("edit_content").textContent = content;
      document.getElementById("length_edit").textContent = edit_Length + "文字";
      document.getElementById("submit_button_edit").value = ID;
    }
    function delete_confirm() {
      return confirm("削除しますか？");
    }
  </script>
  <?php
  if (isset($_POST["content"])) {
    try {
      $pdo = new PDO('mysql:host=localhost;dbname=forpdotest', 'user', 'pass');
      $stmt_post = $pdo->prepare("INSERT INTO data (content, date, length) VALUES (:content, :date, :length)");
      $strings = preg_replace('/\s|　/', "", $_POST["content"]);
      $stmt_post->bindValue(':content', $_POST["content"]);
      $stmt_post->bindValue(':date', date('Y-m-d H:i:s'));
      $stmt_post->bindValue(':length', mb_strlen($strings, "UTF-8"));
      $flag = $stmt_post->execute();
      if (!$flag) {
        $info_post = $stmt->errorInfo();
        echo $info_post[2];
      } else {
        $alert_message = "保存しました";
      }
    } catch (PDOException $e) {
      echo $e->getMessage();
    } finally {
      $pdo = null;
    }
  } elseif (isset($_POST["edit"])) {
    try {
      $pdo = new PDO('mysql:host=localhost;dbname=forpdotest', 'user', 'pass');
      $stmt_post = $pdo->prepare("UPDATE data SET content = :edit_content, date = :edit_date, length = :edit_length WHERE id = :id");
      $strings = preg_replace('/\s|　/', "", $_POST["edit_content"]);
      $stmt_post->bindValue(':id', $_POST["edit"]);
      $stmt_post->bindValue(':edit_content', $_POST["edit_content"]);
      $stmt_post->bindValue(':edit_date', date('Y-m-d H:i:s'));
      $stmt_post->bindValue(':edit_length', mb_strlen($strings, "UTF-8"));
      $flag = $stmt_post->execute();
      if (!$flag) {
        $info_post = $stmt->errorInfo();
        echo $info_post[2];
      } else {
        $alert_message = "保存しました";
      }
    } catch (PDOException $e) {
      echo $e->getMessage();
    } finally {
      $pdo = null;
    }
  } elseif (isset($_POST["delete"])) {
    try {
      $pdo = new PDO('mysql:host=localhost;dbname=forpdotest', 'user', 'pass');
      $stmt_post = $pdo->prepare("DELETE FROM data WHERE id = :id");
      $stmt_post->bindValue(':id', $_POST["delete"]);
      $flag = $stmt_post->execute();
      if (!$flag) {
        $info_post = $stmt->errorInfo();
        echo $info_post[2];
      } else {
        $alert_message = "削除しました";
      }
    } catch (PDOException $e) {
      echo $e->getMessage();
    } finally {
      $pdo = null;
    }
  }
  ?>
</head>

<body>
  <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
    <symbol id="check-circle-fill" fill="currentColor" viewBox="0 0 16 16">
      <path
        d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
    </symbol>
    <symbol id="exclamation-triangle-fill" fill="currentColor" viewBox="0 0 16 16">
      <path
        d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
    </symbol>
  </svg>
  <?php
  if (isset($alert_message)) {
    echo <<<EOM
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 5">
      <div class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
          <div class="toast-body">
            <svg class="bi flex-shrink-0 me-2" width="24" height="24"><use xlink:href="#check-circle-fill" /></svg>
            $alert_message
          </div>
          <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
      </div>
    </div>
    EOM;
  }
  ?>
  <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="staticBackdropLabel">編集画面</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="post" action="">
          <div class="modal-body">
            <textarea class="form-control" rows="10" id="edit_content" name="edit_content" placeholder="メモを記入する"
              onkeyup="KeyUp(value, 'edit')"></textarea>
            <p class="text-end fs-5" id="length_edit"></p>
            <div class="d-grid">
              <button type="submit" class="btn btn-success" id="submit_button_edit" name="edit" value=""
                disabled>メモを保存</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="container">
    <div class="row">
      <div class="col-md-10 offset-md-1 mt-5">
        <h3 class="mb-5 text-center" id="sampleTarget">文字数カウント付きメモ</h3>
        <form method="post" action="">
          <div class="mb-3">
            <textarea class="form-control" rows="10" name="content" placeholder="メモを記入する"
              onkeyup="KeyUp(value, 0)"></textarea>
          </div>
          <p class="text-end fs-5" id="length_0">0文字</p>
          <div class="d-grid">
            <button type="submit" class="btn btn-success" id="submit_button_0" disabled>メモを保存</button>
          </div>
        </form>
        <hr class="featurette-divider mt-5">
        <h3 class="mt-5 mb-5 text-center">メモ一覧</h3>
        <?php
        try {
          $pdo = new PDO('mysql:host=localhost;dbname=forpdotest', 'user', 'pass');
          $stmt = $pdo->query("SELECT * FROM data WHERE content != '' ORDER BY date DESC");
          if (!$stmt) {
            $info = $stmt->errorInfo();
            echo $info[2];
          } else {
            $edit_button_svg = <<<EOM
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
              <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
              <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
            </svg>
            EOM;
            $delete_button_svg = <<<EOM
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
              <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z" />
              <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z" />
            </svg>
            EOM;
            $week_name = array("日", "月", "火", "水", "木", "金", "土");
            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
              $content_display = nl2br($data['content']);
              $length = $data['length'];
              $id = $data['id'];
              $timestamp = strtotime($data['date']);
              $date_display = [date('Y年n月j日', $timestamp), date('G時i分s秒', $timestamp)];
              $w = date('w', $timestamp);
              echo <<<EOM
            <div class='bg-body-tertiary border rounded-3'><div class='box-color' id="contentBy$id">$content_display<br></div></div>
            <form method="post" action="" onSubmit="return delete_confirm()"><p class='text-end'>
              <button type="button" class="btn btn-outline-secondary p-0" data-bs-toggle="modal" data-bs-target="#staticBackdrop" onClick="edit_modal($id, $length)">
                $edit_button_svg
              </button>
              {$length}文字  $date_display[0]($week_name[$w])$date_display[1]
              <button type="submit" class="btn btn-outline-secondary p-0" name="delete" value="$id">
                $delete_button_svg
              </button>
            </p></form>
            EOM;
            }
          }
        } catch (PDOException $e) {
          echo $e->getMessage();
        } finally {
          $pdo = null;
        }
        ?>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
    crossorigin="anonymous"></script>
  <script>
    var toastElList = [].slice.call(document.querySelectorAll('.toast'));
    var toastList = toastElList.map(function (toastEl) {
      var toast = new bootstrap.Toast(toastEl, { autohide: true, delay: 2500 });
      toast.show();
      return toast;
    });
  </script>
</body>

</html>