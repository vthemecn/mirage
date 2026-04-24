/**
 * JavaScript 国际化翻译函数
 * 从 wp_localize_script 注入的 window.i18n 对象中获取翻译
 */
export default function __(key) {
  return (typeof window.i18n !== 'undefined' && window.i18n[key]) ? window.i18n[key] : key;
}
