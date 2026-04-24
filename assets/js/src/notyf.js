/**
 * 全局Notyf配置文件
 * 将Notyf实例挂载到window对象，供全局使用
 */

import { Notyf } from '../../lib/notyf/notyf.es.js';


// 创建全局Notyf实例配置
var globalNotyfConfig = {
    duration: 3000,
    position: { 
        x: 'center', 
        y: 'top' 
    },
    ripple: true,
    dismissible: false,
    types: [
        {
            type: 'success',
            background: '#3dc763',
            className: 'notyf__toast--success',
            icon: {
                className: 'notyf__icon--success',
                tagName: 'i'
            }
        },
        {
            type: 'error',
            background: '#ed3d3d',
            className: 'notyf__toast--error',
            icon: {
                className: 'notyf__icon--error',
                tagName: 'i'
            }
        },
        {
            type: 'warning',
            background: 'orange',
            className: 'notyf__toast--warning',
            icon: {
                className: 'notyf__icon--warning',
                tagName: 'i',
                text: '⚠'
            }
        },
        {
            type: 'info',
            background: '#3b82f6',
            className: 'notyf__toast--info',
            icon: {
                className: 'notyf__icon--info',
                tagName: 'i',
                text: 'ℹ'
            }
        }
    ]
};

// 创建全局Notyf实例
var globalNotyf = new Notyf(globalNotyfConfig);

// 挂载到window对象
window.GlobalNotyf = globalNotyf;

// 创建便捷的全局函数
export function showNotification(message, type, options) {
    // 如果没有传入类型，默认为success
    type = type || 'success';
    
    // 合并配置
    var config = Object.assign({
        message: message,
        type: type
    }, options || {});
    
    try {
        switch(type.toLowerCase()) {
            case 'success':
                return globalNotyf.success(message);
            case 'error':
                return globalNotyf.error(message);
            case 'warning':
                return globalNotyf.open(Object.assign(config, { type: 'warning' }));
            case 'info':
                return globalNotyf.open(Object.assign(config, { type: 'info' }));
            default:
                return globalNotyf.success(message);
        }
    } catch (error) {
        console.error('Notyf error:', error);
        // Fallback到浏览器原生alert
        alert(message);
        return null;
    }
}

// 同时挂载到window对象，保持向后兼容
window.showNotification = showNotification;

// 为向后兼容保留原来的showSuccess和showError方法
export function showSuccess(message) {
    return showNotification(message, 'success');
}

export function showError(message) {
    return showNotification(message, 'error');
}

// 同时挂载到window对象
window.showSuccess = showSuccess;
window.showError = showError;
