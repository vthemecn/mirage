/**
 * 登录/注册/找回密码对话框
 */

import dialogTools from './dialog.js';

export default function() {
  initLoginDialog();
}

function initLoginDialog() {
  const dialogElement = document.querySelector('.login-register-dialog');
  if(!dialogElement) return;
  
  // 注册对话框功能
  dialogTools.registerDialog(dialogElement);
  
  // 打开登录对话框
  const loginBtn = document.querySelector('.open-login-dialog');
  if(loginBtn) {
    loginBtn.addEventListener('click', function(e) {
      e.preventDefault();
      dialogElement.showModal();
    });
  }
  
  // Tab切换功能
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
    });
  });
  
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
  
  // 找回密码表单提交
  const forgotForm = document.getElementById('forgot-form');
  if(forgotForm) {
    forgotForm.addEventListener('submit', handleForgotFormSubmit);
  }
}

async function handleSendVerificationCode() {
  const emailInput = document.getElementById('register-email');
  const email = emailInput.value;
  
  // 验证邮箱格式
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!email || !emailRegex.test(email)) {
    showNotification('请输入有效的邮箱地址', 'error');
    return;
  }
  
  // 更改按钮状态
  const btn = this;
  btn.disabled = true;
  const originalText = btn.textContent;
  btn.textContent = '发送中...';
  
  try {
    const response = await fetch(ajax_object.ajax_url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: `action=send_verification_code&email=${encodeURIComponent(email)}&security=${ajax_object.nonce}`
    });
    
    const result = await response.json();
    
    if(result.success) {
      showNotification('验证码已发送，请查收邮件', 'success');
      
      // 启动倒计时
      startCountdown(btn, originalText, 60);
    } else {
      showNotification(result.data || result.message || '验证码发送失败', 'error');
      btn.disabled = false;
      btn.textContent = originalText;
    }
  } catch(error) {
    showNotification('网络错误，请稍后重试', 'error');
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
  
  button.textContent = `${seconds}秒后重试`;
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
    showNotification('请填写所有必填字段', 'error');
    return;
  }
  
  // 显示加载状态
  const submitBtn = this.querySelector('button[type="submit"]');
  const originalText = submitBtn.textContent;
  submitBtn.textContent = '登录中...';
  submitBtn.disabled = true;
  
  try {
    const response = await fetch(ajax_object.ajax_url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: `action=login_user&username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}&security=${ajax_object.nonce}`
    });
    
    const result = await response.json();
    
    if(result.success) {
      showNotification('登录成功，正在跳转...', 'success');
      // 登录成功，刷新页面
      setTimeout(() => {
        location.reload();
      }, 1000);
    } else {
      showNotification(result.data || result.message || '登录失败，请重试', 'error');
    }
  } catch(error) {
    showNotification('网络错误，请稍后重试', 'error');
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
  const confirmPassword = formData.get('confirm_password');
  const verificationCode = formData.get('verification_code');
  
  // 验证
  if (!username || !email || !password || !confirmPassword || !verificationCode) {
    showNotification('请填写所有必填字段', 'error');
    return;
  }
  
  if (password !== confirmPassword) {
    showNotification('两次输入的密码不一致', 'error');
    return;
  }
  
  if (password.length < 6) {
    showNotification('密码长度至少为6位', 'error');
    return;
  }
  
  // 验证邮箱格式
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) {
    showNotification('请输入有效的邮箱地址', 'error');
    return;
  }
  
  // 显示加载状态
  const submitBtn = this.querySelector('button[type="submit"]');
  const originalText = submitBtn.textContent;
  submitBtn.textContent = '注册中...';
  submitBtn.disabled = true;
  
  try {
    const response = await fetch(ajax_object.ajax_url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: `action=register_user&username=${encodeURIComponent(username)}&email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}&verification_code=${encodeURIComponent(verificationCode)}&security=${ajax_object.nonce}`
    });
    
    const result = await response.json();
    
    if(result.success) {
      showNotification('注册成功，正在自动登录...', 'success');
      // 注册成功，刷新页面以反映登录状态
      setTimeout(() => {
        location.reload();
      }, 1000);
    } else {
      showNotification(result.data || result.message || '注册失败，请重试', 'error');
    }
  } catch(error) {
    showNotification('网络错误，请稍后重试', 'error');
  } finally {
    // 恢复按钮状态
    submitBtn.textContent = originalText;
    submitBtn.disabled = false;
  }
}

async function handleForgotFormSubmit(e) {
  e.preventDefault();
  
  const formData = new FormData(this);
  const email = formData.get('email');
  
  if (!email) {
    showNotification('请输入邮箱地址', 'error');
    return;
  }
  
  // 验证邮箱格式
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) {
    showNotification('请输入有效的邮箱地址', 'error');
    return;
  }
  
  // 显示加载状态
  const submitBtn = this.querySelector('button[type="submit"]');
  const originalText = submitBtn.textContent;
  submitBtn.textContent = '发送中...';
  submitBtn.disabled = true;
  
  try {
    const response = await fetch(ajax_object.ajax_url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: `action=forgot_password&email=${encodeURIComponent(email)}&security=${ajax_object.nonce}`
    });
    
    const result = await response.json();
    
    if(result.success) {
      showNotification('重置密码链接已发送到您的邮箱', 'success');
      this.reset();
    } else {
      showNotification(result.data || result.message || '发送失败，请重试', 'error');
    }
  } catch(error) {
    showNotification('网络错误，请稍后重试', 'error');
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

function showNotification(message, type) {
  // 检查Notyf是否存在
  if (typeof notyf !== 'undefined') {
    if (type === 'success') {
      notyf.success(message);
    } else {
      notyf.error(message);
    }
  } else {
    alert(message);
  }
}