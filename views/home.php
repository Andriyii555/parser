<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet"
          href="https://v4-alpha.getbootstrap.com/dist/css/bootstrap.min.css">
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <title>Parser pages</title>
    <script src='https://www.google.com/recaptcha/api.js'></script>
</head>
<body>
<br>
<div class="content">
    <form class="form-horizontal" action="" method="post">
        <div class="form-group">
            <label class="control-label col-sm-10" for="url">Insert link by whitespace separating:</label>
            <div class="col-sm-10">
                <textarea name="url" class="form-control form_to_send" id="url"
                          rows="30"
                          placeholder="https://example"><?php
                    echo (isset($_POST['url']) and !empty($_POST['url']))
                        ? $_POST['url']
                        : "";
                ?></textarea>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-10">
                <button type="submit" class="btn btn-success col-sm-12">Submit!</button>
            </div>
        </div>
    </form><?php
    require_once 'parser.php'; ?>
</div>
<!-- ---------- JavaScript ---------- -->
<script src="https://npmcdn.com/tether@1.2.4/dist/js/tether.min.js"></script>
<script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
<script src="https://v4-alpha.getbootstrap.com/dist/js/bootstrap.min.js"></script>
</body>
</html>