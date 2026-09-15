<?php
if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['done'])){
  
    $pass=$_POST['password'];
    $conpass=$_POST['conpass'];
    $email=$_POST['email'];
    $un=$_POST['un'];
    
    $error=true;
    $error_email = $error_pass = $error_n = "";

    if(empty($un) || strlen($un)<=3){
        $error_n="Please enter your Username (min 4 characters)";
        $error=false;
    }

    if (empty($pass)) {
        $error_pass = "Please enter your password";
        $error = false;
    } elseif (strlen($pass) <= 6) {
        $error_pass = "Password must be longer than 6 characters";
        $error = false;
    } elseif (!preg_match("/[a-z]/", $pass) || 
             !preg_match("/[0-9]/", $pass) || 
             !preg_match("/[\W_]/", $pass)) {
        $error_pass = "Password must contain letters, numbers, and special characters";
        $error = false;
    }

    if ($pass !== $conpass) {
        $error_pass = "Passwords do not match";
        $error = false;
    }

    if(empty($email)){
        $error_email="Please enter your email";
        $error=false;
    }elseif(!filter_var($email,FILTER_VALIDATE_EMAIL)){
        $error_email="Please enter a correct email address";
        $error=false;
    }

    if($error==true){
        include "config.php";
        $hash_pass = password_hash($pass, PASSWORD_DEFAULT);
        $sql="INSERT INTO `users` (`user_name`, `user_email`, `user_pass`) VALUES ('$un', '$email', '$hash_pass')";
        mysqli_query($con, $sql);
        header("Location: login.php");
        exit();
    } 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Nizar Store</title>
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
    
<div class="container py-5">
    <div class="modern-form-container text-center">
        <!-- Logo Area Placeholder -->
        <div class="login-logo">Nizar</div>
        
        <h3 class="fw-bold mb-4 text-dark">Create an Account</h3>
        
        <form method="post" class="text-start">
            
            <!-- Username Input -->
            <div class="form-floating mb-3">
                <input type="text" class="form-control <?php echo !empty($error_n) ? 'is-invalid' : ''; ?>" id="usernameInput" name="un" placeholder="Username" value="<?= htmlspecialchars($un ?? '') ?>">
                <label for="usernameInput"><i class="bi bi-person me-2 text-muted"></i>Username</label>
                <?php if(!empty($error_n)): ?>
                    <div class="invalid-feedback fw-medium"><?= $error_n ?></div>
                <?php endif; ?>
            </div>

            <!-- Email Input -->
            <div class="form-floating mb-3">
                <input type="email" class="form-control <?php echo !empty($error_email) ? 'is-invalid' : ''; ?>" id="emailInput" name="email" placeholder="name@example.com" value="<?= htmlspecialchars($email ?? '') ?>">
                <label for="emailInput"><i class="bi bi-envelope me-2 text-muted"></i>Email address</label>
                <?php if(!empty($error_email)): ?>
                    <div class="invalid-feedback fw-medium"><?= $error_email ?></div>
                <?php endif; ?>
            </div>
            
            <!-- Password Input -->
            <div class="form-floating mb-3">
                <input type="password" class="form-control <?php echo !empty($error_pass) ? 'is-invalid' : ''; ?>" id="passwordInput" name="password" placeholder="Password">
                <label for="passwordInput"><i class="bi bi-lock me-2 text-muted"></i>Password</label>
            </div>

            <!-- Confirm Password Input -->
            <div class="form-floating mb-4">
                <input type="password" class="form-control <?php echo !empty($error_pass) ? 'is-invalid' : ''; ?>" id="conpassInput" name="conpass" placeholder="Confirm Password">
                <label for="conpassInput"><i class="bi bi-shield-lock me-2 text-muted"></i>Confirm Password</label>
                <?php if(!empty($error_pass)): ?>
                    <div class="invalid-feedback fw-medium"><?= $error_pass ?></div>
                <?php endif; ?>
            </div>
            
            <!-- Submit Button -->
            <button class="btn btn-primary-custom w-100 py-2 fs-5 mb-3" type="submit" name="done">
                Sign Up
            </button>
            
            <!-- Login Link -->
            <div class="text-center mt-4">
                <p class="text-muted mb-0">Already have an account? <a href="login.php" class="text-primary-custom fw-semibold text-decoration-none">Sign In</a></p>
            </div>
        </form>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
