<section class="section flex-center height-90 gap-30">

  <div class="text-center">
    <h1 class="sub-header bold">Hi, Welcome Back!</h1>
    <p class="gray-text">Sign in now to access your account.</p>
  </div>

  <form id="loginForm" class="flex-column gap-50 bg-white padding-50 rounded shadow">
    
    <div id="feedback" class="red-text text-center font-size-14"></div>

    <div class="input-container">
      <label for="username">Username</label>
      <input type="text" name="username" placeholder="Enter your username" required>
    </div>

    <div class="input-container">
      <label for="password">Password</label>
      <div class="passwordField">
        <input type="password" name="password" id="password" placeholder="Enter your password" required>
        <span class="password-toggle" id="togglePassword">
          <i class="fa-regular fa-eye fa-lg show-icon"></i>
          <i class="fa-regular fa-eye-slash fa-lg hide-icon" style="display:none;"></i>
        </span>
      </div>
    </div>

    <button type="submit" class="btn solidBtn">Sign in</button>
  </form>

</section>