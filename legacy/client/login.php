<?php
session_start();
if (isset($_POST['send'])) {

    $pass =$_POST['password'];
    $email = trim($_POST['email']);
    
    $error = true;
    $error_email = $error_pass = "";
    

    // التحقق من صحة الإيميل
    if (empty($email)) {
        $error_email = "Please enter your Email";
        $error = false;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_email = "Please enter a valid email address";
        $error = false;
    }

    // التحقق من صحة كلمة المرور
    if (empty($pass)) {
        $error_pass = "Please enter your password";
        $error = false;
    } elseif (strlen($pass) <= 6) {
        $error_pass = "Password must be longer than 6 characters";
        $error = false;
    } elseif (!preg_match("/[a-z]/i", $pass) || 
             !preg_match("/[0-9]/", $pass) || 
             !preg_match("/[\W_]/", $pass)) {
        $error_pass = "Password must contain letters, numbers, and special characters";
        $error = false;
    }
    // $pass=password_hash($pass,PASSWORD_DEFAULT);

    if ($error) {
         include("config.php");


if ($stmt = $con->prepare("SELECT * FROM `users` WHERE user_email = ?")) {
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      // Verify password against the hashed version in the database
      if (password_verify($pass, $row['user_pass'])) {
          $_SESSION['user']=$row['user_name'];
          $_SESSION['user_id']=$row['user_id'];
          $_SESSION['user_role']=$row['user_role'];
          header("Location: add.php");
          exit();
      } else {
          $error_pass="Invalid password.";
      }
  } else {
      $error_email="Invalid email.";
  }
  $stmt->close();
} else {
  echo "Error preparing statement.";
}
}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Nizar Store</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom Style -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background-color: var(--background-color);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .login-logo {
            font-size: 3rem;
            color: var(--primary-color);
            font-weight: 800;
            letter-spacing: -1px;
            margin-bottom: 2rem;
            display: inline-block;
        }
    </style>
</head>
<body>
    
<div class="container">
    <div class="modern-form-container text-center">
        <!-- Logo Area Placeholder -->
        <div class="login-logo">Nizar</div>
        
        <h3 class="fw-bold mb-4 text-dark">Welcome Back</h3>
        
        <form method="post" action="login.php" class="text-start">
            
            <!-- Email Input -->
            <div class="form-floating mb-3">
                <input type="email" class="form-control <?php echo !empty($error_email) ? 'is-invalid' : ''; ?>" id="emailInput" name="email" placeholder="name@example.com" value="<?= htmlspecialchars($email ?? '') ?>">
                <label for="emailInput"><i class="bi bi-envelope me-2 text-muted"></i>Email address</label>
                <?php if(!empty($error_email)): ?>
                    <div class="invalid-feedback fw-medium"><?= $error_email ?></div>
                <?php endif; ?>
            </div>
            
            <!-- Password Input -->
            <div class="form-floating mb-4">
                <input type="password" class="form-control <?php echo !empty($error_pass) ? 'is-invalid' : ''; ?>" id="passwordInput" name="password" placeholder="Password" value="<?= htmlspecialchars($pass ?? '') ?>">
                <label for="passwordInput"><i class="bi bi-lock me-2 text-muted"></i>Password</label>
                <?php if(!empty($error_pass)): ?>
                    <div class="invalid-feedback fw-medium"><?= $error_pass ?></div>
                <?php endif; ?>
            </div>
            
            <!-- Submit Button -->
            <button class="btn btn-primary-custom w-100 py-2 fs-5 mb-3" type="submit" name="send">
                Sign In
            </button>
            
            <!-- Signup Link -->
            <div class="text-center mt-4">
                <p class="text-muted mb-0">New Account? <a href="na.php" class="text-primary-custom fw-semibold text-decoration-none">Sign Up</a></p>
            </div>
        </form>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
