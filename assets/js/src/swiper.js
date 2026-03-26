/**
 * 幻灯片
 */
export default function () {
  const swiperContainer = document.querySelector('.swiper-container');
  if (!swiperContainer) return;

  // 根据 data-autoplay 属性决定是否自动播放
  const autoplay = swiperContainer.dataset.autoplay == '1';
  const effect = swiperContainer.dataset.effect;

  let swiperMain = new Swiper('.swiper-container', {
    pagination: {
      el: '.swiper-pagination',
    },
    // 根据 autoplay 变量决定是否启用自动播放
    autoplay: autoplay ? {
      delay: 5000,
      disableOnInteraction: false,
    } : false,
    loop: true,
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
    effect: effect
  });
}
