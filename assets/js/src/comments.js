/**
 * 评论相关功能
 */

import { getCookie, setCookie } from './utils.js';
import { Notyf } from '../../lib/notyf/notyf.es.js';


export default function () {
  initCommentDelete();
  initCommentReply();
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
    link.addEventListener('click', async function (e) {
      e.preventDefault();

      // 确认删除
      if (!confirm('确定要删除这条评论吗？')) {
        return;
      }

      const commentId = this.dataset.commentId;
      const commentItem = this.closest('.comment-item');

      // 显示加载状态
      const originalText = this.textContent;
      this.textContent = '删除中...';
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
          // 删除成功，移除评论 DOM 元素
          if (commentItem) {
            commentItem.style.transition = 'opacity 0.3s ease';
            commentItem.style.opacity = '0';

            setTimeout(() => {
              commentItem.remove();

              // 更新评论计数
              updateCommentCount(false);

              // 显示成功消息
              showNotification('评论删除成功', 'success');
            }, 300);
          }
        } else {
          showNotification(result.data.message || '删除失败', 'error');
          // 恢复按钮状态
          this.textContent = originalText;
          this.style.pointerEvents = 'auto';
        }
      } catch (error) {
        console.error('删除评论失败:', error);
        showNotification('网络错误，请重试', 'error');
        // 恢复按钮状态
        this.textContent = originalText;
        this.style.pointerEvents = 'auto';
      }
    });
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
      countElement.textContent = '暂无评论';
    } else if (newCount === 1) {
      countElement.textContent = '一条评论';
    } else {
      countElement.textContent = newCount + ' 条评论';
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

      const originalText = submitBtn.value;
      
      // 锁定按钮
      submitBtn.value = '提交中...';
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
                throw new Error(data.data && data.data.message ? data.data.message : '提交失败');
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
          showNotification(error.message || '网络错误，请稍后重试。', 'error');
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
function addNewCommentToList(commentData) {
  const commentList = document.querySelector('.comment-list');
  if (!commentList) return;

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
        Delete
      </a>
    `;
  }
  
  // 构建待审核提示 HTML
  let statusHtml = '';
  if (commentData.approved === '0') {
    statusHtml = `
      <div class="comment-status">
        Your comment is awaiting moderated.
      </div>
    `;
  }
  
  // 创建评论项 HTML
  const commentHtml = `
    <li class="parent" id="comment-${commentId}">
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
          <a href="?replytocom=${commentId}#respond" login="false">Reply</a>
          ${deleteLinkHtml}
        </div>
      </div>
    </li>
  `;
  
  // 添加到评论列表末尾
  commentList.insertAdjacentHTML('beforeend', commentHtml);
  
  // 更新评论计数
  updateCommentCount(true);
  
  // 重新初始化删除功能（为新添加的删除链接绑定事件）
  initCommentDelete();
  
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
