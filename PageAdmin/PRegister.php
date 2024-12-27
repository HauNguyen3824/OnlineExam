<?php
include('../conn/conn_database.php');
include('../template/Tmenubar.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
  $userId = $_POST['userId'] ?? '';
  $username = $_POST['username'] ?? '';
  $password = $_POST['password'] ?? '';
  $confirmPassword = $_POST['confirmPassword'] ?? '';
  $fullName = $_POST['fullName'] ?? '';
  $class = $_POST['class'] ?? '';
  $year = $_POST['year'] ?? '';

  if ($password !== $confirmPassword) {
      $error_message = "Mật khẩu và xác nhận mật khẩu không khớp.";
  } else {
      // Kiểm tra xem UserId có bị trùng không
      $sql_check_user = "SELECT * FROM Users WHERE UserId = ?";
      $stmt_check_user = $conn->prepare($sql_check_user);
      $stmt_check_user->bind_param("s", $userId);
      $stmt_check_user->execute();
      $result_check_user = $stmt_check_user->get_result();

      if ($result_check_user->num_rows > 0) {
          $error_message = "UserId đã tồn tại.";
      } else {
          // Mã hóa mật khẩu
          $hashed_password = md5($password); // Sử dụng md5 để mã hóa mật khẩu

          // Thêm người dùng mới vào cơ sở dữ liệu
          $sql_insert_user = "INSERT INTO Users (UserId, Username, Password, FullName, Class, Year, Role) VALUES (?, ?, ?, ?, ?, ?, 'user')";
          $stmt_insert_user = $conn->prepare($sql_insert_user);
          $stmt_insert_user->bind_param("ssssss", $userId, $username, $hashed_password, $fullName, $class, $year);

          if ($stmt_insert_user->execute()) {
              $success_message = "Đăng ký thành công!";
          } else {
              $error_message = "Lỗi khi đăng ký: " . $stmt_insert_user->error;
          }

          $stmt_insert_user->close();
      }

      $stmt_check_user->close();
  }
}

// Function to generate a user ID
function generateUserId($class, $year, $orderNumber)
{
  $base_id = substr($year, -2) . $class . str_pad($orderNumber, 3, '0', STR_PAD_LEFT) ;
   return  $base_id;
}

// Function to generate an admin ID
function generateAdminId($conn) {
  $sql_last_id = "SELECT MAX(CAST(SUBSTRING(UserId, 6) AS UNSIGNED)) AS max_id FROM Users WHERE SUBSTRING(UserId, 1, 5) = 'admin'";
  $result = $conn->query($sql_last_id);
  $max_id = 0;

  if ($result && $row = $result->fetch_assoc()) {
       $max_id = $row["max_id"];
        if ($max_id === null) {
           $max_id = 0;
       }
  }

  return  'admin' . strval(intval($max_id) + 1);
}

// Form submission processing
if (isset($_POST['submit'])) {
  $formType = $_POST['formType'];

  // Processing for the user form
    if ($formType == 'user') {

        $fullname = $_POST['fullname'] ?? ''; // set default value if not available using coalescing operator
        $class = $_POST['class'] ?? '';
        $year = $_POST['schoolyear'] ?? '';
         $orderNumber = $_POST['orderNumber'] ?? ''; // use coalescing

        // Check if required fields are not empty, otherwise error
          if(empty($fullname) || empty($class) || empty($year) || empty($orderNumber) )
           {
               echo "<script>alert('Please fill all the required fields');</script>";
            }
           // school year must have a 4 character constraint and order number with 3 character constraints.
            else if( strlen($year) !== 4){
                   echo "<script>alert('School Year must have 4 digits (e.g., 2024)');</script>";
           } else if (strlen($orderNumber) !== 3){
                 echo "<script>alert('Order Number must have 3 digits (e.g., 001)');</script>";
          }
          else
           {
              // Generating the user's UserID using helper function, include order number
              $userid =  generateUserId($class, $year, $orderNumber);

                //Insert into User
                $username = $userid;
                $password = '123';
                $passwordHashed = password_hash($password, PASSWORD_DEFAULT);
                $role = 'user';
                 $sql = "INSERT INTO Users (UserId, FullName, Username, Password, Role, Class, Year)
                            VALUES ('$userid', '$fullname', '$username', '$passwordHashed', '$role', '$class', '$year')";

                        if ($conn->query($sql) === TRUE) {
                            echo "<script>alert('User registered successfully!');</script>";
                         } else {
                             echo "Error: " . $sql . "<br>" . $conn->error;
                        }
          }
        //Processing for Admin form
    } else if ($formType == 'admin') {

        $fullname = $_POST['fullname']??''; // also add checks here for all
        $username = $_POST['username']??'';
        $password = $_POST['password']??'';
        $confirmPassword = $_POST['confirmPassword']??'';
        $email = $_POST['email']??'';
        $phone = $_POST['phone']??'';

          if(empty($fullname) || empty($username) || empty($password) || empty($confirmPassword) || empty($email) || empty($phone)) {
            echo "<script>alert('Please fill out all the fields');</script>";
        }
        else {
          if ($password != $confirmPassword) {
                 echo "<script>alert('Passwords do not match.');</script>";
                }else {

                     // Generating the Admin's ID using the adminId function
                     $userid = generateAdminId($conn);

                      // password hash and insert
                     $passwordHashed = password_hash($password, PASSWORD_DEFAULT);
                    $role = 'admin';

                   $sql = "INSERT INTO Users (UserId, FullName, Username, Password, Email, Phone, Role)
                        VALUES ('$userid', '$fullname', '$username', '$passwordHashed', '$email', '$phone', '$role')";

                     if ($conn->query($sql) === TRUE) {
                        echo "<script>alert('Admin registered successfully!');</script>";
                    } else {
                      echo "Error: " . $sql . "<br>" . $conn->error;
                  }

                }
       }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <!-- Form đăng ký -->
    <header class="bg-primary text-white text-center py-3">
         <h1>Đăng ký</h1>
    </header>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <!-- Navigation tabs for user/admin -->
                 <nav>
                      <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <!--  added col-6 class to divide space for nav -->
                          <a class="nav-item nav-link active col-6 text-center" id="nav-user-tab" data-toggle="tab" href="#nav-user" role="tab" aria-controls="nav-user" aria-selected="true" onclick="switchForm('user')">User</a>
                           <a class="nav-item nav-link col-6 text-center" id="nav-admin-tab" data-toggle="tab" href="#nav-admin" role="tab" aria-controls="nav-admin" aria-selected="false" onclick="switchForm('admin')">Admin</a>
                      </div>
                </nav>

                 <div class="tab-content" id="nav-tabContent">
                      <div class="tab-pane fade show active" id="nav-user" role="tabpanel" aria-labelledby="nav-user-tab">

                             <form id="userForm" class="border p-4 bg-light" method="post" action="PRegister.php"  onsubmit="return validateUserForm()">
                                <h2 class="text-center">Tạo tài khoản người dùng</h2>

                                 <div class="form-group">
                                      <label for="fullname">Tên đầy đủ</label>
                                       <input type="text" class="form-control" name="fullname" id="fullname" placeholder="Full Name" required>
                                   </div>
                                  <div class="form-group">
                                       <label for="class">Lớp</label>
                                          <select class="form-control" name="class" id="class" required>
                                                 <option value="10">10</option>
                                                 <option value="11">11</option>
                                                  <option value="12">12</option>
                                           </select>
                                     </div>

                                  <div class="form-group">
                                        <label for="orderNumber">Số thứ tự</label>
                                         <input type="text" class="form-control" name="orderNumber" id="orderNumber" placeholder="Số thứ tự (e.g., 001)" required>
                                    </div>

                                 <div class="form-group">
                                      <label for="schoolyear">Năm học</label>
                                      <input type="text" class="form-control" name="schoolyear" id="schoolyear" placeholder="School Year (e.g., 2024)" required>
                                 </div>

                                <input type="hidden" name="formType" id="formType" value="user"/>

                                    <!-- Nút đăng ký -->
                                    <button type="submit" class="btn btn-primary btn-block" name="submit">Đăng ký User</button>

                             </form>

                        </div>
                     <div class="tab-pane fade" id="nav-admin" role="tabpanel" aria-labelledby="nav-admin-tab">
                          <form  id ="adminForm" class="border p-4 bg-light" method="post" action="PRegister.php">
                                <h2 class="text-center">Tạo tài khoản Admin</h2>

                                 <!-- Tên đầy đủ -->
                                <div class="form-group">
                                  <label for="fullname">Tên đầy đủ</label>
                                 <input type="text" class="form-control" name="fullname" id="fullname" placeholder="Full Name" required>
                               </div>
                                    <!-- Tên đăng nhập -->
                               <div class="form-group">
                                <label for="username">Username</label>
                                    <input type="text" class="form-control" name="username" id="username" placeholder="Username" required>
                              </div>

                                 <!-- Mật khẩu -->
                                <div class="form-group">
                                  <label for="password">Password</label>
                                    <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
                                </div>
                                <!-- Nhập lại mật khẩu -->
                                <div class="form-group">
                                   <label for="confirmPassword">Xác nhận Password</label>
                                   <input type="password" class="form-control" name="confirmPassword" id="confirmPassword" placeholder="Confirm Password" required>
                                </div>

                                  <!-- Password visibility toggle (checkbox) -->
                                <div class="form-group form-check">
                                    <input type="checkbox" class="form-check-input" id="showPasswordAdmin" onclick="togglePasswordVisibilityAdmin()">
                                     <label class="form-check-label" for="showPasswordAdmin">Hiện Password</label>
                                 </div>
                                  <!-- Email -->
                                  <div class="form-group">
                                       <label for="email">Email</label>
                                       <input type="email" class="form-control" name="email" id="email" placeholder="Email" required>
                                    </div>

                                    <!-- Số điện thoại -->
                                   <div class="form-group">
                                       <label for="phone">Số điện thoại</label>
                                      <input type="tel" class="form-control" name="phone" id="phone" placeholder="Phone" required>
                                 </div>

                                 <input type="hidden" name="formType" id="formType" value="admin"/>
                                    <!-- Nút đăng ký -->
                                    <button type="submit" class="btn btn-primary btn-block"  name="submit">Đăng ký Admin</button>
                             </form>
                      </div>
                </div>
            </div>
        </div>
    </div>

    <script>
     function switchForm(formType) {
      document.getElementById('formType').value = formType;
        }


    function validateUserForm(){
          const schoolyear = document.getElementById('schoolyear').value;
          const orderNumber = document.getElementById('orderNumber').value;


           if(schoolyear.length !== 4) {
               alert('School Year must have 4 digits (e.g., 2024)');
               return false;
          }

          if (orderNumber.length !== 3) {
                alert('Order Number must have 3 digits (e.g., 001)');
                 return false;
              }


           return true;
      }


    function togglePasswordVisibilityAdmin() {
         const passwordField1 = document.getElementById("password");
         const passwordField2 = document.getElementById("confirmPassword");
          if (passwordField1.type === "password" && passwordField2.type === "password") {
            passwordField1.type = "text";
            passwordField2.type = "text";
         } else {
            passwordField1.type = "password";
            passwordField2.type = "password";
         }
     }

    </script>

    <!-- Bootstrap JavaScript -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
<?php
include('../template/Tfooter.php');
$conn->close();
?>