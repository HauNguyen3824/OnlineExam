<?php
include('../conn/conn_database.php');
include('../template/Tmenubar.php');

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

// Function to generate a recovery code
function generateRecoveryCode($length = 6) {
  $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $charactersLength = strlen($characters);
  $recoveryCode = '';
  for ($i = 0; $length > $i; $i++) {
      $recoveryCode .= $characters[rand(0, $charactersLength - 1)];
  }
  return $recoveryCode;
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
                $recoveryCode = generateRecoveryCode();
                 $sql = "INSERT INTO Users (UserId, FullName, Username, Password, Role, Class, Year, RecoveryCode)
                            VALUES ('$userid', '$fullname', '$username', '$passwordHashed', '$role', '$class', '$year', '$recoveryCode')";

                        if ($conn->query($sql) === TRUE) {
                            echo "<script>alert('Đăng ký User thành công!');</script>";
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
                        echo "<script>alert('Đăng ký Admin thành công!');</script>";
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
                                       <input type="text" class="form-control" name="fullname" id="fullname" placeholder="Họ và tên" required>
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
                                         <input type="text" class="form-control" name="orderNumber" id="orderNumber" placeholder="Số thứ tự" required>
                                    </div>

                                 <div class="form-group">
                                      <label for="schoolyear">Năm học</label>
                                      <input type="text" class="form-control" name="schoolyear" id="schoolyear" placeholder="Năm học" required>
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
                                 <input type="text" class="form-control" name="fullname" id="fullname" placeholder="Họ và tên" required>
                               </div>
                                    <!-- Tên đăng nhập -->
                               <div class="form-group">
                                <label for="username">Tên đăng nhập</label>
                                    <input type="text" class="form-control" name="username" id="username" placeholder="Tên đăng nhập" required>
                              </div>

                                 <!-- Mật khẩu -->
                                <div class="form-group">
                                  <label for="password">Mật khẩu</label>
                                    <input type="password" class="form-control" name="password" id="password" placeholder="Mật khẩu" required>
                                </div>
                                <!-- Nhập lại mật khẩu -->
                                <div class="form-group">
                                   <label for="confirmPassword">Xác nhận mật khẩu</label>
                                   <input type="password" class="form-control" name="confirmPassword" id="confirmPassword" placeholder="Nhập lại mật khẩu" required>
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
                                      <input type="tel" class="form-control" name="phone" id="phone" placeholder="Số điện thoại" required>
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
               alert('Năm học phải bao gồm 4 chữ số (Ví dụ: 2024)');
               return false;
          }

          if (orderNumber.length !== 3) {
                alert('Số thứ tự phải bao gồm 3 chữ số (Ví dụ: 001)');
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