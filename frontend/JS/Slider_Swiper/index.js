new Swiper('.image-slider', {
  // стрелки
  navigation: {
    nextEl: '.swiper-button-next',
    prevEl: '.swiper-button-prev'
  },

  // пэйджер
  pagination: {
    el: '.swiper-pagination',
    clickable: true,
    dynamicBullets: true,
    renderBullet: function (index, className) {
      return '<span class="' + className + '">' + (index + 1) + '</span>';
    }
  },

  // переключение с клавиатуры
  keyboard: {
    enable: true,
    onlyInViewport: true,
  },

  slidesPerView: 3,
  slidesPerGroup: 1,
  centeredSlides: true,
  loop: true,

  breakpoints: {
    0: {
      slidesPerView: 1,
    },
    992: {
      slidesPerView: 2,
    },
    1440: {
      slidesPerView: 3,
    }
  }
});
