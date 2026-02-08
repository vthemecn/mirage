/**
 * 评论相关功能
 */

import {getCookie, setCookie} from './utils.js';

export default function() {
    // 初始化评论删除功能
    initCommentDelete();
    
    // 其他评论相关功能...
}

/**
 * 初始化评论删除功能
 */
function initCommentDelete() {
    // 为所有删除链接添加点击事件
    document.querySelectorAll('.delete-comment').forEach(link => {
        link.addEventListener('click', async function(e) {
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
                    // 删除成功，移除评论DOM元素
                    if (commentItem) {
                        commentItem.style.transition = 'opacity 0.3s ease';
                        commentItem.style.opacity = '0';
                        
                        setTimeout(() => {
                            commentItem.remove();
                            
                            // 更新评论计数
                            updateCommentCount();
                            
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
function updateCommentCount() {
    const countElement = document.querySelector('.comments-title');
    if (countElement) {
        const currentCount = parseInt(countElement.textContent.match(/\d+/)?.[0] || 0);
        const newCount = Math.max(0, currentCount - 1);
        
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
 * 显示通知消息
 */
function showNotification(message, type) {
    // 创建通知元素
    const notification = document.createElement('div');
    notification.className = `comment-notification ${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        padding: 12px 24px;
        border-radius: 4px;
        color: white;
        font-size: 14px;
        z-index: 9999;
        transition: all 0.3s ease;
        ${type === 'success' ? 'background-color: #28a745;' : 'background-color: #dc3545;'}
    `;
    
    document.body.appendChild(notification);
    
    // 3秒后自动消失
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(-50%) translateY(-20px)';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}


