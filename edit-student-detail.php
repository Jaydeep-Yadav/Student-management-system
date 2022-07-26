<?php

session_start();

if (!$_SESSION['email']) {
    $_SESSION['login_first'] = "Please login first";
    header('location:index.php');
}

include 'dbconnect.php';

$type_error = $size_error = '';

//! add is name of submit button
if (isset($_POST['update'])) {
    $edit_student_id = $_GET['edit_student'];
    $fname = mysqli_real_escape_string($conn, $_POST['fname']);
    $lname = mysqli_real_escape_string($conn, $_POST['lname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $fathername = mysqli_real_escape_string($conn, $_POST['fathername']);
    $birthdate = mysqli_real_escape_string($conn, $_POST['birthdate']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $state = mysqli_real_escape_string($conn, $_POST['state']);
    $nationality = mysqli_real_escape_string($conn, $_POST['nationality']);
    $district = mysqli_real_escape_string($conn, $_POST['district']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);

    //! name = image , in image input
    // var_dump($_FILES["image"]["error"]);

    if (isset($_FILES['image'])) {
        $image = $_FILES['image']['name'];
        $image_type = $_FILES['image']['type'];
        $image_size = $_FILES['image']['size'];
        $image_tmp = $_FILES['image']['tmp_name'];
    }

    //! To delete image from folder
    $image_query = " SELECT * FROM student_detail WHERE id = '$edit_student_id' ";
    $run = mysqli_query($conn, $image_query);
    while ($row = mysqli_fetch_assoc($run)) {
        $img = $row['photo'];
    }

    unlink('student_images/' . $img);

    if (!$image_type == 'image/jpg' or !$image_type == 'image/png') {
        $type_error = "Invalid Image Format";
    } else if ($image_size < 2048) {

        $size_error = "Image size is larger.Image should be less than 2mb";
    } else {

        if (!file_exists('student_images')) {
            mkdir('student_images', 0777, true);
        }
        // move_uploaded_file($_FILES['image']['tmp_name'], __DIR__.'/../../uploads/'. $_FILES["image"]['name'])

        move_uploaded_file($image_tmp, 'student_images/' . $image);

        $sql = "UPDATE student_detail SET fname = '$fname',lname='$lname',email='$email',fathername='$fathername',birthdate='$birthdate',mobile='$mobile',gender='$gender',city='$city',state='$state',district='$district',nation='$nationality',photo='$image' WHERE id = '$edit_student_id' ";

        $run = mysqli_query($conn, $sql);

        if ($run) {

            echo "<script>window.open('view-student.php?update_success=Student data updated successfully','_self')</script>";
        } else {
            echo "<script>window.open('view-student.php?update_error=update error','_self')</script>";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management System</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <link rel="stylesheet" href="sidebar.css">

    <!-- //!Button Logic -->
    <script>
        jQuery(document).ready(function($) {
            $('#toggler').click(function(event) {
                event.preventDefault();
                $('#wrap').toggleClass('toggled');

            });
        });
    </script>
</head>

<body>
    <div class="d-flex" id="wrap">
        <!-- //!Side Bar -->
        <div class="sidebar bg-light border-light">

            <div class="sidebar-heading">
                <p class="text-center">Manage students</p>
            </div>

            <ul class="list-group list-group-flush">

                <a href="main.php" class="list-group-item list-group-item-action">
                    <i class="fa fa-vcard-o"></i>Dashboard
                </a>

                <a href="add-teacher.php" class="list-group-item list-group-item-action">
                    <i class="fa fa-user"></i>Add Teacher Detail
                </a>

                <a href="view-teacher.php" class="list-group-item list-group-item-action">
                    <i class="fa fa-eye"></i>View Teacher Detail
                </a>

                <a href="search-teacher.php" class="list-group-item list-group-item-action">
                    <i class="fa fa-search"></i>Search Teacher Detail
                </a>

                <a href="add-student.php" class="list-group-item list-group-item-action">
                    <i class="fa fa-user"></i>Add Student Detail
                </a>

                <a href="view-student.php" class="list-group-item list-group-item-action">
                    <i class="fa fa-eye"></i>View Student Detail
                </a>

                <a href="search-student.php" class="list-group-item list-group-item-action">
                    <i class="fa fa-search"></i>Search Student Detail
                </a>

                <a href="logout.php" class="list-group-item list-group-item-action">
                    <i class="fa fa-sign-out"></i>Logout
                </a>

            </ul>
        </div> <!-- //!Side Bar ends -->

        <!-- //! Main Body -->
        <div class="main-body">

            <button class="btn btn-outline-light bg-danger" id="toggler">
                <i class="fa fa-bars"></i>
            </button>

            <section id="main-form">



                <h2 class="text-center text-danger pt-3 font-weight-bold">Student Management System</h2>

                <div class="container bg-danger" id="formsetting">

                    <h3 class="text-center text-white pb-4 pt-2 font-weight-bold">
                        Edit Student Detail
                    </h3>

                    <?php

                    if (isset($_GET['edit_student'])) {

                        $edit_student_id = $_GET['edit_student'];
                        $query = "SELECT * FROM student_detail WHERE id = '$edit_student_id' ";
                        $run = mysqli_query($conn, $query);

                        while ($row = mysqli_fetch_assoc($run)) {

                    ?>

                            <form method="POST" action="" enctype="multipart/form-data">
                                <!-- //! enctype for image -->

                                <div class="row">


                                    <!-- //! Left side form  -->

                                    <div class="col-md-5 col sm-5 col-12 m-auto">
                                        <div class="form-group">
                                            <label class="text-white" for="fname">First Name</label>
                                            <input type="text" name="fname" placeholder="Enter your first name" class="form-control" required="required" value="<?php echo $row['fname']; ?>">
                                        </div>
                                        <div class="form-group">
                                            <label class="text-white" for="email">Email</label>
                                            <input type="email" name="email" placeholder="Enter your email" class="form-control" required="required" value="<?php echo $row['email']; ?>">
                                        </div>
                                        <div class="form-group">
                                            <label class="text-white" for="fathername">Father Name</label>
                                            <input type="text" name="fathername" placeholder="Enter your father's name" class="form-control" required="required" value="<?php echo $row['fathername']; ?>">
                                        </div>

                                        <div class="form-group">
                                            <label class="text-white" for="city">City</label>
                                            <input type="text" name="city" placeholder="Enter your City" class="form-control" required="required" value="<?php echo $row['city']; ?>">
                                        </div>
                                        <div class="form-group">
                                            <label class="text-white" for="nationality">Nationality</label>
                                            <input type="text" name="nationality" placeholder="Enter your Nationality" class="form-control" required="required" value="<?php echo $row['nation']; ?>">
                                        </div>

                                        <div class="form-group">
                                            <label class="text-white" for="radio">Gender</label>
                                            <input type="radio" name="gender" value="male" class="form-check-input ml-2" <?php if ($row['gender'] == 'male') echo "checked" ?>>
                                            <label class="form-check-label text-white pl-4">Male</label>
                                            <input type="radio" name="gender" value="female" class="form-check-input ml-2" <?php if ($row['gender'] == 'female') echo "checked" ?>>
                                            <label class="form-check-label text-white pl-4">Female</label>
                                        </div>
                                    </div> <!-- //! Left side form ends-->


                                    <!-- //! Right side form  -->

                                    <div class="col-md-5 col sm-5 col-12 m-auto">
                                        <div class="form-group">
                                            <label class="text-white" for="lname">Last Name</label>
                                            <input type="text" name="lname" placeholder="Enter your last name" class="form-control" required="required" value="<?php echo $row['lname']; ?>">
                                        </div>
                                        <div class="form-group">
                                            <label class="text-white" for="birthdate">Birth Date</label>
                                            <input type="date" name="birthdate" placeholder="Enter your Birth Date" class="form-control" required="required" value="<?php echo $row['birthdate']; ?>">
                                        </div>
                                        <div class="form-group">
                                            <label class="text-white" for="mobile">Mobile</label>
                                            <input type="text" name="mobile" placeholder="Enter your Mobile" class="form-control" required="required" value="<?php echo $row['mobile']; ?>">
                                        </div>
                                        <div class="form-group">
                                            <label class="text-white" for="district">District</label>
                                            <input type="text" name="district" placeholder="Enter your District" class="form-control" required="required" value="<?php echo $row['district']; ?>">
                                        </div>
                                        <div class="form-group">
                                            <label class="text-white" for="state">State</label>
                                            <input type="text" name="state" placeholder="Enter your State" class="form-control" required="required" value="<?php echo $row['state']; ?>">
                                        </div>

                                        <div class="input-group">
                                            <!-- <label class="text-white">Student Photo</label> -->
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroupFileAddon01">Upload</span>
                                            </div>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="inputGroupFile01" aria-describedby="inputGroupFileAddon01" name="image">
                                                <label for="inputGroupFile01" class="custom-file-label">Choose File</label>
                                            </div>
                                        </div>

                                <?php
                            }
                        }
                                ?>

                                <div class="text-center">
                                    <input type="submit" name="update" value="Update Details" class="btn btn-success px-5 mt-2 text-center">
                                </div>
                                <span style="display:block" class="text-center text-sucess text-white font-weight-bold"><?php echo $type_error;
                                                                                                                        echo $size_error; ?></span>

                                    </div> <!-- //! Right side form  ends-->


                                </div>

                            </form>


                </div>
            </section>
        </div> <!-- //! Main Body ends -->
    </div>
</body>

</html>