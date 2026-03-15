/**
 * 评论相关功能
 */

import { getCookie, setCookie } from './utils.js';
import { Notyf } from '../../lib/notyf/notyf.es.js';


export default function () {
  initCommentDelete();
  initCommentReply();
  initInlineReply(); // 新增：初始化内联回复功能
}


function showNotification(message, type) {
  const notyf = new Notyf({
    duration: 3000,
    position: { x: 'center', y: 'top' }
  });

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



/**
 * 初始化评论删除功能
 */
function initCommentDelete() {
  // 为所有删除链接添加点击事件
  document.querySelectorAll('.delete-comment').forEach(link => {
    bindDeleteButton(link);
  });
}


/**
 * 更新评论计数显示
 */
function updateCommentCount(increment = false) {
  const countElement = document.querySelector('.comments-title');
  if (countElement) {
    const currentCount = parseInt(countElement.textContent.match(/\d+/)?.[0] || 0);
    const newCount = increment ? currentCount + 1 : Math.max(0, currentCount - 1);

    if (newCount === 0) {
      countElement.textContent = i18n.no_comments;
    } else if (newCount === 1) {
      countElement.textContent = i18n.one_comment;
    } else {
      countElement.textContent = newCount + ' ' + i18n.multiple_comments;
    }
  }
}


/**
 * 初始化评论回复功能
 */
function initCommentReply() {
  const form = document.getElementById('commentform');
    
  // 如果没有找到表单，直接退出
  if (!form) return;

  if (typeof ajax_object === 'undefined') {
      console.error('未找到 ajax_object 变量，请检查页面源码。');
      return;
  }

  form.addEventListener('submit', function(e) {
      e.preventDefault(); // 阻止默认提交

      const submitBtn = form.querySelector('input[type="submit"]');
      if (!submitBtn) return;

      // ========== 新增：获取评论内容并验证 ==========
      const commentField = form.querySelector('textarea[name="comment"]');
      const content = commentField ? commentField.value.trim() : '';
      const maxLength = 1000;
      
      // 验证不能为空
      if (!content || content.length === 0) {
          showNotification(i18n.comment_cannot_be_empty, 'error');
          submitBtn.value = originalText;
          submitBtn.disabled = false;
          return;
      }
      
      // 验证最大长度
      if (content.length > maxLength) {
          showNotification(
              i18n.comment_max_length.replace('%d', maxLength),
              'error'
          );
          submitBtn.value = originalText;
          submitBtn.disabled = false;
          return;
      }
      // ============================================

      const originalText = submitBtn.value;
      
      // 锁定按钮
      submitBtn.value = i18n.submitting;
      submitBtn.disabled = true;

      // 构建数据
      const formData = new FormData(form);
      formData.append('action', 'custom_submit_comment');
      formData.append('nonce', ajax_object.nonce);

      // 发送请求
      fetch(ajax_object.ajax_url, {
          method: 'POST',
          body: formData
      })
      .then(response => {
          return response.json().then(data => {
            console.log('Response:', data);
            // 如果 HTTP 状态不是 200-299，或者 data.success 为 false，则视为错误
            if (!response.ok || !data.success) {
                throw new Error(data.data && data.data.message ? data.data.message : i18n.submit_failed);
            }
            return data;
          });
      })
      .then(data => {
          // 成功逻辑
          if (data.success) {
              showNotification(data.data.message, 'success');
              form.reset(); // 清空表单
              
              // 动态添加新评论到评论列表
              if (data.data.approved === '1') {
                  addNewCommentToList(data.data);
              }
          }
      })
      .catch(error => {
          // 错误逻辑
          // error.message 包含了我们在 PHP 中设置的错误信息
          showNotification(error.message || i18n.network_error_retry, 'error');
          console.error('提交失败:', error, error.message);
      })
      .finally(() => {
          // 恢复按钮
          submitBtn.value = originalText;
          submitBtn.disabled = false;
      });
  });
}


/**
 * 动态添加新评论到评论列表
 */
function addNewCommentToList(commentData, parentId = null) {
  const commentList = document.querySelector('.comment-list');
  if (!commentList) return;

  console.log('commentData, parentId', commentData, parentId);

  // 构建评论 HTML
  const authorName = commentData.author.name;
  const authorUrl = commentData.author.url;
  const avatar = commentData.author.avatar;
  const commentDate = commentData.date;
  const commentContent = commentData.content;
  const commentId = commentData.comment_id;
  const userId = commentData.user_id;
  
  // 构建作者链接 HTML
  let authorHtml = '';
  if (authorUrl) {
    authorHtml = `
      <a href="javascript:;" target="_blank" data-url="${authorUrl}">
        ${authorName}
        <i class="fa-solid fa-globe"></i>
      </a>
    `;
  } else {
    authorHtml = authorName;
  }
  
  // 构建删除链接 HTML（仅当用户登录且是评论作者时显示）
  let deleteLinkHtml = '';
  if (userId > 0 && typeof ajax_object !== 'undefined' && ajax_object.current_user_id == userId) {
    deleteLinkHtml = `
      <a href="javascript:;" class="delete-comment" data-comment-id="${commentId}">
        ${i18n.delete}
      </a>
    `;
  }
  
  // 构建待审核提示 HTML
  let statusHtml = '';
  if (commentData.approved === '0') {
    statusHtml = `
      <div class="comment-status">
        ${i18n.awaiting_moderation}
      </div>
    `;
  }
  
  // 创建评论项 HTML（注意：这里只创建 li 内部的 div，不包含外层 li）
  const commentItemHtml = `
    <div class="comment-item" id="comment-${commentId}">
      <div class="comment-header">
        <div class="comment-author">
          ${avatar}
          <span class="nickname">
            ${authorHtml}
          </span>
        </div>
        <div class="comment-meta">
          ${commentDate}
        </div>
      </div>
      ${statusHtml}
      <div class="comment-content">
        ${commentContent}
      </div>
      <div class="comment-actions">
        <a href="?replytocom=${commentId}#respond" class="reply-link">${i18n.reply}</a>
        ${deleteLinkHtml}
      </div>
    </div>
  `;
  
  // 如果是回复评论，添加到父评论的 .children 容器中
  if (parentId) {
    console.log('回复 parentId');
    const parentComment = document.getElementById(`comment-${parentId}`);
    if (parentComment) {
      // 检查是否有 .children 容器，没有则创建
      let childrenContainer = parentComment.querySelector('.children');
      if (!childrenContainer) {
        childrenContainer = document.createElement('ul');
        childrenContainer.className = 'children';
        // 将 children 容器添加到父评论 li 内部（在 .comment-item 之后）
        parentComment.appendChild(childrenContainer);
      }
      
      // 创建子评论的 li 元素
      const childLi = document.createElement('li');
      childLi.className = 'child';
      childLi.id = `comment-${commentId}`;
      childLi.innerHTML = commentItemHtml;
      
      // 添加到子评论列表
      childrenContainer.appendChild(childLi);
      
      // 滚动到新评论
      const newComment = document.getElementById(`comment-${commentId}`);
      if (newComment) {
        setTimeout(() => {
          newComment.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
          newComment.style.opacity = '0';
          newComment.style.transition = 'opacity 0.5s ease-in';
          requestAnimationFrame(() => {
            newComment.style.opacity = '1';
          });
        }, 100);
      }
    }
  } else {
    console.log('回复 顶级评论');

    // 顶级评论，添加到评论列表末尾
    const parentLi = document.createElement('li');
    parentLi.className = 'parent';
    parentLi.id = `comment-${commentId}`;
    parentLi.innerHTML = commentItemHtml;
    
    commentList.insertAdjacentElement('beforeend', parentLi);
    
    // 更新评论计数
    updateCommentCount(true);
    
    // 滚动到新评论位置
    const newComment = document.getElementById(`comment-${commentId}`);
    if (newComment) {
      setTimeout(() => {
        newComment.scrollIntoView({ behavior: 'smooth', block: 'center' });
        // 添加淡入动画效果
        newComment.style.opacity = '0';
        newComment.style.transition = 'opacity 0.5s ease-in';
        requestAnimationFrame(() => {
          newComment.style.opacity = '1';
        });
      }, 100);
    }
  }
  
  // 重新初始化删除功能和内联回复功能（只为新添加的元素绑定事件）
  const newCommentElement = document.getElementById(`comment-${commentId}`);
  if (newCommentElement) {
    // 只处理新增元素的回复按钮
    const replyLink = newCommentElement.querySelector('.comment-actions a.reply-link');
    if (replyLink) {
      bindReplyButton(replyLink);
    }
    
    // 只处理新增元素的删除按钮
    const deleteLink = newCommentElement.querySelector('.delete-comment');
    if (deleteLink) {
      bindDeleteButton(deleteLink);
    }
  }
}


/**
 * 初始化内联回复功能 - 阻止跳转并显示内联表单
 */
function initInlineReply() {
  // 为所有回复链接添加点击事件
  document.querySelectorAll('.comment-actions a.reply-link').forEach(link => {
    bindReplyButton(link);
  });
}


/**
 * 创建内联回复表单
 */
function createInlineReplyForm(commentId, commentItem) {
  const form = document.createElement('div');
  form.className = 'inline-reply-form';
  
  form.innerHTML = `
    <div class="reply-form-container">
      <textarea placeholder="${i18n.reply_to_comment}" rows="3"></textarea>
      <div class="reply-form-actions">
        <button type="submit" class="submit-reply">${i18n.submit_reply}</button>
        <button type="button" class="cancel-reply">${i18n.cancel}</button>
      </div>
    </div>
  `;
  
  // 绑定取消按钮事件
  const cancelBtn = form.querySelector('.cancel-reply');
  cancelBtn.addEventListener('click', () => {
    form.remove();
  });
  
  // 绑定提交事件
  const submitBtn = form.querySelector('.submit-reply');
  submitBtn.addEventListener('click', async (e) => {
    e.preventDefault();
    
    const textarea = form.querySelector('textarea');
    const content = textarea.value.trim();
    const maxLength = 1000;
    
    if (!content) {
      showNotification(i18n.please_enter_reply, 'error');
      return;
    }
    
    // ========== 新增：字数验证 ==========
    // 验证不能为空
    if (content.length === 0) {
      showNotification(i18n.comment_cannot_be_empty, 'error');
      return;
    }
    
    // 验证最大长度
    if (content.length > maxLength) {
      showNotification(
        i18n.comment_max_length.replace('%d', maxLength),
        'error'
      );
      return;
    }
    // ====================================
    
    // 锁定按钮
    const originalText = submitBtn.textContent;
    submitBtn.textContent = i18n.submitting_reply;
    submitBtn.disabled = true;
    
    try {
      // 获取主表单的数据
      const mainForm = document.getElementById('commentform');
      if (!mainForm) {
        showNotification(i18n.comment_form_not_found, 'error');
        return;
      }
      
      // 构建 FormData
      const formData = new FormData(mainForm);
      formData.set('comment', content); // 设置回复内容
      formData.set('action', 'custom_submit_comment');
      formData.append('nonce', ajax_object.nonce);
      
      // 设置 parent 参数
      formData.set('comment_parent', commentId);
      
      // 发送请求
      const response = await fetch(ajax_object.ajax_url, {
        method: 'POST',
        body: formData
      });
      
      const result = await response.json();
      
      if (result.success) {
        showNotification(result.data.message, 'success');
        form.remove(); // 移除表单
        
        // 如果评论已审核通过，添加到列表
        if (result.data.approved === '1') {
          addNewCommentToList(result.data, commentId);
        }
      } else {
        showNotification(result.data.message || i18n.submit_failed, 'error');
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
      }
    } catch (error) {
      console.error('Submit reply error:', error);
      showNotification(i18n.network_error_retry, 'error');
      submitBtn.textContent = originalText;
      submitBtn.disabled = false;
    }
  });
  
  return form;
}


/**
 * 绑定单个回复按钮的事件
 */
function bindReplyButton(link) {
  link.addEventListener('click', function(e) {
    e.preventDefault();
    
    const url = this.href;
    const match = url.match(/replytocom=(\d+)/);
    if (!match) return;
    
    const commentId = match[1];
    const commentItem = document.getElementById(`comment-${commentId}`);
    
    if (!commentItem) return;
    
    const existingForm = commentItem.querySelector('.inline-reply-form');
    if (existingForm) {
      existingForm.remove();
      return;
    }
    
    document.querySelectorAll('.inline-reply-form').forEach(form => form.remove());
    
    const replyForm = createInlineReplyForm(commentId, commentItem);
    
    // 查找是否有 .children 容器，如果有则插入到其前面，否则追加到末尾
    const childrenContainer = commentItem.querySelector('.children');
    if (childrenContainer) {
      commentItem.insertBefore(replyForm, childrenContainer);
    } else {
      commentItem.appendChild(replyForm);
    }
    
    const textarea = replyForm.querySelector('textarea');
    if (textarea) {
      textarea.focus();
    }
  });
}


/**
 * 绑定单个删除按钮的事件
 */
function bindDeleteButton(link) {
  link.addEventListener('click', async function (e) {
    e.preventDefault();

    if (!confirm(i18n.confirm_delete_comment)) {
      return;
    }

    const commentId = this.dataset.commentId;
    const commentItem = this.closest('.comment-item');

    const originalText = this.textContent;
    this.textContent = i18n.deleting;
    this.style.pointerEvents = 'none';

    try {
      const response = await fetch(ajax_object.ajax_url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
          action: 'delete_comment',
          comment_id: commentId,
          nonce: ajax_object.nonce
        })
      });

      const result = await response.json();

      if (result.success) {
        if (commentItem) {
          commentItem.style.transition = 'opacity 0.3s ease';
          commentItem.style.opacity = '0';

          setTimeout(() => {
            commentItem.remove();
            updateCommentCount(false);
            showNotification(i18n.delete_success, 'success');
          }, 300);
        }
      } else {
        showNotification(result.data.message || i18n.delete_failed, 'error');
        this.textContent = originalText;
        this.style.pointerEvents = 'auto';
      }
    } catch (error) {
      console.error('删除评论失败:', error);
      showNotification(i18n.network_error_retry, 'error');
      this.textContent = originalText;
      this.style.pointerEvents = 'auto';
    }
  });
}
