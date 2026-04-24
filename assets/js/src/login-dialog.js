/**
 * 登录/注册/找回密码对话框 - REST API版本
 */

import __ from './i18n.js';
import dialogTools from './dialog.js';


export default function() {
  initLoginDialog();
}

function initLoginDialog() {
  // console.log('vtheme captain - REST API version');
  // console.log('Notyf', Notyf);

  const dialogElement = document.querySelector('.login-register-dialog');
  if(!dialogElement) return;

  // console.log('dialogElement', dialogElement);
  
  // 注册对话框功能
  dialogTools.registerDialog(dialogElement);
  
  // 打开登录对话框
  const loginBtns = document.querySelectorAll('.open-login-dialog');
  if(loginBtns.length > 0) {
    loginBtns.forEach(function(loginBtn) {
      loginBtn.addEventListener('click', function(e) {
        e.preventDefault();
        dialogElement.showModal();
      });
    });
  }
  
  const tabBtns = document.querySelectorAll('a.tab-nav');
  tabBtns.forEach(btn => {
    btn.addEventListener('click', function() {
      // 移除所有活动状态
      document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
         
      // 添加当前活动状态
      document.getElementById(`tab-${this.dataset.tab}`).classList.add('active');

      // 如果切换到找回密码标签，重置为第一步
      if(this.dataset.tab === 'forgot') {
        showForgotStep1();
      }
    });
  });


  // Tab切换功能
  /*
  const tabBtns = document.querySelectorAll('.tab-btn');
  tabBtns.forEach(btn => {
    btn.addEventListener('click', function() {
      // 移除所有活动状态
      document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
      
      // 添加当前活动状态
      this.classList.add('active');
      document.getElementById(`tab-${this.dataset.tab}`).classList.add('active');
      
      // 更新标题
      document.querySelector('.dialog-header .title').textContent = this.textContent;
      
      // 如果切换到找回密码标签，重置为第一步
      if(this.dataset.tab === 'forgot') {
        showForgotStep1();
      }
    });
  });
  */
  
  // 登录表单提交
  const loginForm = document.getElementById('login-form');
  if(loginForm) {
    loginForm.addEventListener('submit', handleLoginFormSubmit);
  }
  
  // 注册表单提交
  const registerForm = document.getElementById('register-form');
  if(registerForm) {
    registerForm.addEventListener('submit', handleRegisterFormSubmit);
  }
  
  // 添加发送验证码功能
  const sendVerificationBtn = document.getElementById('send-verification-code');
  if(sendVerificationBtn) {
    sendVerificationBtn.addEventListener('click', handleSendVerificationCode);
  }
  
  // 找回密码第一步表单提交
  const forgotFormStep1 = document.getElementById('forgot-form-step1');
  if(forgotFormStep1) {
    forgotFormStep1.addEventListener('submit', handleForgotStep1Submit);
  }
  
  // 找回密码第二步表单提交
  const forgotFormStep2 = document.getElementById('forgot-form-step2');
  if(forgotFormStep2) {
    forgotFormStep2.addEventListener('submit', handleForgotStep2Submit);
  }
}

// 显示找回密码第一步
function showForgotStep1() {
  document.getElementById('forgot-form-step1').style.display = 'block';
  document.getElementById('forgot-form-step2').style.display = 'none';
  document.getElementById('forgot-back').style.display = 'none';
}

// 显示找回密码第二步
function showForgotStep2() {
  document.getElementById('forgot-form-step1').style.display = 'none';
  document.getElementById('forgot-form-step2').style.display = 'block';
  document.getElementById('forgot-back').style.display = 'block';
}

// 获取REST API基础URL
function getRestApiBaseUrl() {
  return window.location.origin + '/wp-json/vtheme/v1';
}

// 通用的API请求函数 - 按照RESTful规范处理响应
async function apiRequest(endpoint, options = {}) {
  const url = getRestApiBaseUrl() + endpoint;
  const defaultOptions = {
    headers: {
      'Content-Type': 'application/json',
    },
  };
  
  const mergedOptions = {
    ...defaultOptions,
    ...options,
    headers: {
      ...defaultOptions.headers,
      ...options.headers,
    },
  };

  try {
    const response = await fetch(url, mergedOptions);
    const result = await response.json();
    
    // 按照RESTful规范：通过HTTP状态码判断成功/失败
    if (response.ok) {
      // 2xx 状态码表示成功，直接返回资源数据
      return {
        success: true,
        data: result,
        status: response.status
      };
    } else {
      // 非 2xx 状态码表示失败，返回错误信息
      return {
        success: false,
        error: result.error || { message: '请求失败' },
        status: response.status
      };
    }
  } catch (error) {
    console.error('API request failed:', error);
    return {
      success: false,
      error: { message: '网络错误，请稍后重试' },
      status: 0
    };
  }
}

async function handleSendVerificationCode() {
  const emailInput = document.getElementById('register-email');
  const email = emailInput.value;
  
  // 验证邮箱格式
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!email || !emailRegex.test(email)) {
    showNotification(__('Please enter a valid email address'), 'error');
    return;
  }
  
  // 更改按钮状态
  const btn = this;
  btn.disabled = true;
  const originalText = btn.textContent;
  btn.textContent = __('Sending...');
  
  try {
    const result = await apiRequest('/accounts/send-verification-code', {
      method: 'POST',
      body: JSON.stringify({ email: email })
    });
    
    if(result.success) {
      showNotification(__('Verification code has been sent, please check your email'), 'success');
      
      // 启动倒计时
      startCountdown(btn, originalText, 60);
    } else {
      const errorMessage = result.error?.message || __('Failed to send verification code');
      showNotification(errorMessage, 'error');
      btn.disabled = false;
      btn.textContent = originalText;
    }
  } catch(error) {
    showNotification(__('Network error, please try again later'), 'error');
    btn.disabled = false;
    btn.textContent = originalText;
  }
}

function startCountdown(button, originalText, seconds) {
  if(seconds <= 0) {
    button.disabled = false;
    button.textContent = originalText;
    return;
  }
  
  button.textContent = __('Retry in %d seconds').replace('%d', seconds);
  setTimeout(() => {
    startCountdown(button, originalText, seconds - 1);
  }, 1000);
}

async function handleLoginFormSubmit(e) {
  e.preventDefault();

  const formData = new FormData(this);
  const username = formData.get('username');
  const password = formData.get('password');
  
  // 简单验证
  if (!username || !password) {
    showNotification(__('Please fill in all required fields'), 'error');
    return;
  }
  
  // 显示加载状态
  const submitBtn = this.querySelector('button[type="submit"]');
  const originalText = submitBtn.textContent;
  submitBtn.textContent = __('Logging in...');
  submitBtn.disabled = true;
  
  try {
    const result = await apiRequest('/accounts/login', {
      method: 'POST',
      body: JSON.stringify({
        username: username,
        password: password
      })
    });
    
    if(result.success) {
      showNotification(__('Login successful, redirecting...'), 'success');
      // 登录成功，刷新页面
      setTimeout(() => {
        location.reload();
      }, 1000);
    } else {
      const errorMessage = result.error?.message || __('Login failed, please try again');
      showNotification(errorMessage, 'error');
    }
  } catch(error) {
    showNotification(__('Network error, please try again later'), 'error');
  } finally {
    // 恢复按钮状态
    submitBtn.textContent = originalText;
    submitBtn.disabled = false;
  }
}

async function handleRegisterFormSubmit(e) {
  e.preventDefault();
  
  const formData = new FormData(this);
  const username = formData.get('username');
  const email = formData.get('email');
  const password = formData.get('password');
  const verificationCode = formData.get('verification_code');
  
  // 验证
  if (!username || !email || !password || !verificationCode) {
    showNotification(__('Please fill in all required fields'), 'error');
    return;
  }
  
  if (password.length < 6) {
    showNotification(__('Password must be at least 6 characters long'), 'error');
    return;
  }
  
  // 验证邮箱格式
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) {
    showNotification(__('Please enter a valid email address'), 'error');
    return;
  }
  
  // 显示加载状态
  const submitBtn = this.querySelector('button[type="submit"]');
  const originalText = submitBtn.textContent;
  submitBtn.textContent = __('Registering...');
  submitBtn.disabled = true;
  
  try {
    const result = await apiRequest('/accounts/register', {
      method: 'POST',
      body: JSON.stringify({
        username: username,
        email: email,
        password: password,
        verification_code: verificationCode
      })
    });
    
    if(result.success) {
      showNotification(__('Registration successful, logging in automatically...'), 'success');
      // 注册成功，刷新页面以反映登录状态
      setTimeout(() => {
        location.reload();
      }, 1000);
    } else {
      const errorMessage = result.error?.message || __('Registration failed, please try again');
      showNotification(errorMessage, 'error');
    }
  } catch(error) {
    showNotification(__('Network error, please try again later'), 'error');
  } finally {
    // 恢复按钮状态
    submitBtn.textContent = originalText;
    submitBtn.disabled = false;
  }
}

async function handleForgotStep1Submit(e) {
  e.preventDefault();
  
  const formData = new FormData(this);
  const email = formData.get('email');
  
  if (!email) {
    showNotification(__('Please enter your email address'), 'error');
    return;
  }
  
  // 验证邮箱格式
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) {
    showNotification(__('Please enter a valid email address'), 'error');
    return;
  }
  
  // 显示加载状态
  const submitBtn = this.querySelector('button[type="submit"]');
  const originalText = submitBtn.textContent;
  submitBtn.textContent = __('Sending...');
  submitBtn.disabled = true;
  
  try {
    const result = await apiRequest('/accounts/forgot-password', {
      method: 'POST',
      body: JSON.stringify({ email: email })
    });
    
    if(result.success) {
      showNotification(__('Password reset verification code has been sent to your email'), 'success');
      // 显示第二步表单
      showForgotStep2();
    } else {
      const errorMessage = result.error?.message || __('Failed to send, please try again');
      showNotification(errorMessage, 'error');
    }
  } catch(error) {
    showNotification(__('Network error, please try again later'), 'error');
  } finally {
    // 恢复按钮状态
    submitBtn.textContent = originalText;
    submitBtn.disabled = false;
  }
}

async function handleForgotStep2Submit(e) {
  e.preventDefault();
  
  const formData = new FormData(this);
  const email = document.getElementById('forgot-email').value; // 从前一步获取邮箱
  const code = formData.get('code');
  const newPassword = formData.get('new_password');
  
  // 验证
  if (!email || !code || !newPassword) {
    showNotification(__('Please fill in all required fields'), 'error');
    return;
  }
  
  if (newPassword.length < 6) {
    showNotification(__('Password must be at least 6 characters long'), 'error');
    return;
  }
  
  // 显示加载状态
  const submitBtn = this.querySelector('button[type="submit"]');
  const originalText = submitBtn.textContent;
  submitBtn.textContent = __('Resetting...');
  submitBtn.disabled = true;
  
  try {
    const result = await apiRequest('/accounts/reset-password', {
      method: 'POST',
      body: JSON.stringify({
        email: email,
        code: code,
        new_password: newPassword
      })
    });
    
    if(result.success) {
      showNotification(__('Password reset successful, logging in automatically...'), 'success');
      // 重置密码成功后刷新页面以反映登录状态
      setTimeout(() => {
        location.reload();
      }, 1500);
    } else {
      const errorMessage = result.error?.message || __('Reset failed, please try again');
      showNotification(errorMessage, 'error');
    }
  } catch(error) {
    showNotification(__('Network error, please try again later'), 'error');
  } finally {
    // 恢复按钮状态
    submitBtn.textContent = originalText;
    submitBtn.disabled = false;
  }
}

function switchToTab(tabName, title) {
  // 移除所有活动状态
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
  
  // 添加当前活动状态
  document.querySelector(`.tab-btn[data-tab="${tabName}"]`).classList.add('active');
  document.getElementById(`tab-${tabName}`).classList.add('active');
  document.querySelector('.dialog-header .title').textContent = title;
}

// function showNotification(message, type) {
//   const notyf = new Notyf({
//     duration: 3000,
//     position: { x: 'center', y: 'top' }
//   });

//   if (typeof notyf !== 'undefined') {
//     if (type === 'success') {
//       notyf.success(message);
//     } else {
//       notyf.error(message);
//     }
//   } else {
//     alert(message);
//   }
// }