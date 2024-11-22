import axios from '../../lib/axios/axios.esm';
import toast from './toast';

// axios.defaults.baseURL = 'https://dev.vtheme.cn';
axios.defaults.baseURL = document.querySelector('meta[name="api-url"]').getAttribute('content');
axios.defaults.headers.common['Authorization'] = 'AUTH_TOKEN';
axios.defaults.headers.post['Content-Type'] = 'application/json';
axios.defaults.headers['Content-Type'] = 'application/json';
// config.headers['content-type'] = 'application/json';


// 添加请求拦截器
axios.interceptors.request.use(function (config) {
  // 在发送请求之前做些什么
  return config;
}, function (error) {
  // 对请求错误做些什么
  return Promise.reject(error);
});


// 添加响应拦截器
axios.interceptors.response.use(function (response) {
  // 2xx 范围内的状态码都会触发该函数。
  // 对响应数据做点什么
  console.log('axios 响应拦截器：', response);
  return response;
}, function (error) {
  // 超出 2xx 范围的状态码都会触发该函数。
  // 对响应错误做点什么

  if (error.message == 'Network Error') {
    toast.open({ title: '网络错误，请检查网络' });
    return error.response;
  }
  
  if(error.response.status == 401){
    var loginModal = document.querySelector('.modal.login-modal');
    if(loginModal){ loginModal.classList.add('modal-show'); }
    return error.response;
  }
  
  if(error.response.status == 400){
    let errorMessage = error.response.data.error ? error.response.data.error : '请求错误';
    toast.open({title:errorMessage});
    return error.response;
  }
  
  if(error.response.status == 500){
    var message = error.response.data.error ? error.response.data.error : '500 错误';
    toast.open({'title': message });
    return error.response;
  }

  if (error.request && error.response.data && error.response.data.error) {
    toast.open({ title: error.response.data.error });
    return error.response;
  }

  return Promise.reject(error);
});


// 移除拦截器
// const myInterceptor = axios.interceptors.request.use(function () {/*...*/});
// axios.interceptors.request.eject(myInterceptor);


export default axios;
