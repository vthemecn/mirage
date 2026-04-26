<!-- 登录/注册/找回密码对话框 -->
<div class="vt-dialog login-register-dialog" id="login-register-dialog">
    <div class="dialog-widget">
        <a class="close close-button" href="javascript:;">
            <i class="fa-solid fa-xmark"></i>
        </a>
        <div class="dialog-header"></div>
        <div class="dialog-body">
            <div class="auth-logo">
                <?php if(vt_get_config('site_logo', '') == ''):?>
                    <?=get_bloginfo('name')?>
                <?php else: ?>
                    <img light src="<?= vt_get_config('site_logo', ''); ?>" alt="<?php bloginfo('name') ?>">
                    <img darkness src="<?= vt_get_config('site_logo_darkness', ''); ?>" alt="<?php bloginfo('name') ?>">
                <?php endif; ?>
            </div>
            <div class="auth-tabs">
                <!-- 登录表单 -->
                <div class="tab-content active" id="tab-login">
                    <form id="login-form">
                        <div class="login-form-group field-text">
                            <label for="login-username"><?php _e('Username or Email', 'vt'); ?></label>
                            <input type="text" id="login-username" name="username" required>
                        </div>
                        <div class="login-form-group field-text">
                            <label for="login-password"><?php _e('Password', 'vt'); ?></label>
                            <input type="password" id="login-password" name="password" required>
                        </div>
                        <div class="login-form-group field-button">
                            <button type="submit" class="btn primary"><?php _e('Login', 'vt'); ?></button>
                        </div>
                    </form>
                    <div class="login-more">
                        <a href="javascript:;" class="tab-nav" data-tab="forgot"><?php _e('Forgot Password?', 'vt'); ?></a>
                        <a href="javascript:;" class="tab-nav" data-tab="register"><?php _e('Register Account', 'vt'); ?></a>
                    </div>
                    <div class="login-privacy">
                        <?php 
                        $privacy_policy_url = vt_get_config('privacy_policy', get_privacy_policy_url());
                        $terms_of_service_url = vt_get_config('terms_of_service');
                        printf(
                            __("By registering/logging in, you agree to our <a href='%s'>Privacy Policy</a> and <a href='%s'>Terms of Service</a>", 'vt'),
                            esc_url($privacy_policy_url),
                            esc_url($terms_of_service_url)
                        ); 
                        ?>
                    </div>
                </div>
                
                <!-- 注册表单 -->
                <div class="tab-content" id="tab-register">
                    <form id="register-form">
                        <div class="login-form-group field-text">
                            <label for="register-username"><?php _e('Username', 'vt'); ?></label>
                            <input type="text" id="register-username" name="username" required>
                        </div>
                        <div class="login-form-group field-text">
                            <label for="register-email"><?php _e('Email', 'vt'); ?></label>
                            <input type="email" id="register-email" name="email" required>
                        </div>
                        <div class="login-form-group field-text">
                            <label for="register-password"><?php _e('Password', 'vt'); ?></label>
                            <input type="password" id="register-password" name="password" required>
                        </div>
                        <div class="login-form-group field-text">
                            <label for="register-verification-code"><?php _e('Verification Code', 'vt'); ?></label>
                            <div class="verification-input-group">
                                <input type="text" id="register-verification-code" name="verification_code" placeholder="<?php _e('Please enter verification code', 'vt'); ?>" maxlength="6" required>
                                <button type="button" class="btn secondary" id="send-verification-code"><?php _e('Send Verification Code', 'vt'); ?></button>
                            </div>
                        </div>
                        <div class="login-form-group field-button">
                            <button type="submit" class="btn primary"><?php _e('Register', 'vt'); ?></button>
                        </div>
                    </form>
                    <div class="login-more">
                        <a href="javascript:;" class="tab-nav" data-tab="forgot"><?php _e('Forgot Password?', 'vt'); ?></a>
                        <a href="javascript:;" class="tab-nav" data-tab="login"><?php _e('Login Account', 'vt'); ?></a>
                    </div>
                    <div class="login-privacy">
                        <?php 
                        $privacy_policy_url = vt_get_config('privacy_policy', get_privacy_policy_url());
                        $terms_of_service_url = vt_get_config('terms_of_service');
                        printf(
                            __("By registering/logging in, you agree to our <a href='%s'>Privacy Policy</a> and <a href='%s'>Terms of Service</a>", 'vt'),
                            esc_url($privacy_policy_url),
                            esc_url($terms_of_service_url)
                        ); 
                        ?>
                    </div>
                </div>
                
                <!-- 找回密码表单 -->
                <div class="tab-content" id="tab-forgot">
                    <form id="forgot-form-step1">
                        <div class="login-form-group field-text">
                            <label for="forgot-email"><?php _e('Email', 'vt'); ?></label>
                            <input type="email" id="forgot-email" name="email" required>
                        </div>
                        <div class="login-form-group field-button">
                            <button type="submit" class="btn primary"><?php _e('Send Verification Code', 'vt'); ?></button>
                        </div>
                    </form>
                    
                    <form id="forgot-form-step2" style="display:none;">
                        <div class="login-form-group field-text">
                            <label for="forgot-code"><?php _e('Verification Code', 'vt'); ?></label>
                            <input type="text" id="forgot-code" name="code" placeholder="<?php _e('Please enter the verification code you received', 'vt'); ?>" maxlength="6" required>
                        </div>
                        <div class="login-form-group field-text">
                            <label for="forgot-new-password"><?php _e('New Password', 'vt'); ?></label>
                            <input type="password" id="forgot-new-password" name="new_password" placeholder="<?php _e('Please enter new password', 'vt'); ?>" required>
                        </div>
                        <div class="login-form-group field-button">
                            <button type="submit" class="btn primary"><?php _e('Reset Password', 'vt'); ?></button>
                        </div>
                    </form>
                    
                    <div id="forgot-back" style="display:none; text-align:center; margin-top:15px;">
                        <a href="javascript:;" onclick="showForgotStep1()"><?php _e('← Back to enter email', 'vt'); ?></a>
                    </div>

                    <div class="login-more">
                        <a href="javascript:;" class="tab-nav" data-tab="login"><?php _e('Login to Account', 'vt'); ?></a>
                        <a href="javascript:;" class="tab-nav" data-tab="register"><?php _e('Register Account', 'vt'); ?></a>
                    </div>
                    <div class="login-privacy">
                        <?php 
                        $privacy_policy_url = vt_get_config('privacy_policy', get_privacy_policy_url());
                        $terms_of_service_url = vt_get_config('terms_of_service');
                        printf(
                            __("By registering/logging in, you agree to our <a href='%s'>Privacy Policy</a> and <a href='%s'>Terms of Service</a>", 'vt'),
                            esc_url($privacy_policy_url),
                            esc_url($terms_of_service_url)
                        ); 
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // 显示第一步：输入邮箱
    function showForgotStep1() {
        document.getElementById('forgot-form-step1').style.display = 'block';
        document.getElementById('forgot-form-step2').style.display = 'none';
        document.getElementById('forgot-back').style.display = 'none';
    }
    
    // 显示第二步：输入验证码和新密码
    function showForgotStep2() {
        document.getElementById('forgot-form-step1').style.display = 'none';
        document.getElementById('forgot-form-step2').style.display = 'block';
        document.getElementById('forgot-back').style.display = 'block';
    }
    
    // 确保在DOM加载完成后初始化对话框
    document.addEventListener('DOMContentLoaded', function() {
        // 如果使用模块化JavaScript，这里可以初始化登录对话框
        if (typeof initLoginDialog === 'function') {
            initLoginDialog();
        }
    });
</script>