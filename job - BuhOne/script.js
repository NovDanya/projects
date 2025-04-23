document.addEventListener('DOMContentLoaded', () => {
  const slides = document.querySelectorAll('.text-slide');
  const dots = document.querySelectorAll('.slider-dot');
  const prevBtn = document.querySelector('.prev-btn');
  const nextBtn = document.querySelector('.next-btn');

  let currentSlide = 0;
  const slideCount = slides.length;

  function goToSlide(n) {
    slides[currentSlide].classList.remove('active');
    dots[currentSlide].classList.remove('active');

    currentSlide = (n + slideCount) % slideCount;

    slides[currentSlide].classList.add('active');
    dots[currentSlide].classList.add('active');
  }

  nextBtn.addEventListener('click', () => {
    goToSlide(currentSlide + 1);
  });

  prevBtn.addEventListener('click', () => {
    goToSlide(currentSlide - 1);
  });

  dots.forEach((dot, index) => {
    dot.addEventListener('click', () => {
      goToSlide(index);
    });
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'ArrowRight') {
      goToSlide(currentSlide + 1);
    } else if (e.key === 'ArrowLeft') {
      goToSlide(currentSlide - 1);
    }
  });
  slides[currentSlide].classList.add('active');
  dots[currentSlide].classList.add('active');


  const form = document.querySelector('.form');
  const popup = document.getElementById('success-popup');
  const closePopupButton = document.querySelector('.popup__close');

  function showPopup() {
    popup.style.display = 'flex';
  }

  function hidePopup() {
    popup.style.display = 'none';
  }

  function validateName(input, errorElement) {
    const value = input.value.trim();
    const isValid = /^[a-zA-Zа-яА-ЯёЁ]+$/.test(value);
    if (!isValid) {
      input.classList.add('error');
      errorElement.style.display = 'block';
    } else {
      input.classList.remove('error');
      errorElement.style.display = 'none';
    }
    return isValid;
  }

  function validateMessage(input, errorElement) {
    const value = input.value.trim();
    const isValid = value.length >= 10;
    if (!isValid) {
      input.classList.add('error');
      errorElement.style.display = 'block';
    } else {
      input.classList.remove('error');
      errorElement.style.display = 'none';
    }
    return isValid;
  }

  const nameInput = document.querySelector('input[name="name"]');
  const nameError = document.getElementById('name-error');
  nameInput.addEventListener('input', () => validateName(nameInput, nameError));

  const surnameInput = document.querySelector('input[name="surname"]');
  const surnameError = document.getElementById('surname-error');
  surnameInput.addEventListener('input', () => validateName(surnameInput, surnameError));

  const messageInput = document.querySelector('textarea[name="comment"]');
  const messageError = document.getElementById('message-error');
  messageInput.addEventListener('input', () => validateMessage(messageInput, messageError));

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    const isNameValid = validateName(nameInput, nameError);
    const isSurnameValid = validateName(surnameInput, surnameError);
    const isMessageValid = validateMessage(messageInput, messageError);

    if (isNameValid && isSurnameValid && isMessageValid) {
      showPopup();
      form.reset();

      nameInput.classList.remove('error');
      surnameInput.classList.remove('error');
      messageInput.classList.remove('error');
      nameError.style.display = 'none';
      surnameError.style.display = 'none';
      messageError.style.display = 'none';
    }
  });

  closePopupButton.addEventListener('click', hidePopup);

  popup.addEventListener('click', (e) => {
    if (e.target === popup) {
      hidePopup();
    }
  });
});
