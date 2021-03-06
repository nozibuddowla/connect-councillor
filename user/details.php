<?php
session_start();
if (isset($_GET['id'])) {
    $userName = $_SESSION["username"];
    $ward = $_SESSION["wardNumber"];
    if (empty($_SESSION["username"])) {
        header("Location: ../index.php"); // Redirecting To Home Page
    }
} else {
    header("Location: feedback.php");
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Comment System using PHP and Ajax</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</head>

<body>

    <?php include('userNav.html') ?>
    <!--        user homepage -->
    <section>
        <div class="container">
            <div class="row">
                <div class="col-md-6 offset-3">
                    <?php
                    $conn = new mysqli("localhost", "root", "", "connectcouncillor");

                    $query = "SELECT * FROM `complain` WHERE `userName` = '$userName'";
                    $result = $conn->query($query);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $title = $row['title'];
                            $complain = $row['postedComplain'];
                            $id = $row['complainId'];
                            $status = $row['complainStatus'];
                            if ($status == 0) {
                                $stat = "false";
                            } else {
                                $stat = "true";
                            }
                            echo "$title</a>";
                            echo "<h6>:  $complain </h6>";
                            echo "<br>";
                        }
                    }
                    ?>
                </div>


                <div class="container">
                    <form method="POST" id="comment_form">

                        <div class="form-group">
                            <input type="text" name="post_id" id="post_id" class="form-control" readonly="readonly"
                                placeholder="postid" value="<?php echo $_GET['id'] ?>" />
                        </div>
                        <div class="form-group">
                            <input type="text" name="comment_name" id="comment_name" class="form-control"
                                placeholder="Enter Name" />
                        </div>
                        <div class="form-group">
                            <textarea name="comment_content" id="comment_content" class="form-control"
                                placeholder="Enter Comment" rows="5"></textarea>
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="comment_id" id="comment_id" value="0" />
                            <input type="submit" name="submit" id="submit" class="btn btn-info" value="Submit" />

                        </div>
                    </form>


                    <span id="comment_message"></span>
                    <br />
                    <div id="display_comment"></div>
                </div>

                <div class="col-md-12 text-center">
                <input style="margin-bottom:10px " type="submit" class="btn btn-success statusupdate" value="Mark Solved" />
                </div>
               
</body>

</html>

<script>
$(document).ready(function() {

    $('#comment_form').on('submit', function(event) {
        event.preventDefault();
        var form_data = $(this).serialize();
        $.ajax({
            url: "../comment/add_comment.php",
            method: "POST",
            data: form_data,
            dataType: "JSON",
            success: function(data) {
                if (data.error != '') {
                    $('#comment_form')[0].reset();
                    $('#comment_message').html(data.error);
                    $('#comment_id').val('0');
                    load_comment();
                }
            }
        })
    });

    load_comment();

    function load_comment() {
        $.ajax({
            url: "../comment/fetch_comment.php",
            method: "POST",
            data: ({
                post_id: <?php echo $_GET['id']?>
            }),
            success: function(data) {
                $('#display_comment').html(data);
            }
        })
    }

    $(document).on('click', '.reply', function() {
        var comment_id = $(this).attr("id");
        $('#comment_id').val(comment_id);
        $('#comment_name').focus();
    });

    $('.statusupdate').click(function() {
        $.ajax({
            url: "../comment/status.php",
            method: "POST",
            data: ({
                post_id: <?php echo $_GET['id']?>
            }),
        });
    });


});
</script>