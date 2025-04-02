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

  slidesPerView: 3, // слайдов для показа
  slidesPerGroup: 1, // колво пролистываемых слайдов
  centeredSlides: true, // активный слайд по центру
  loop: true, // бесконечная прокрутка

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
