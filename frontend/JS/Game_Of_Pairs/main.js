function startGame(game, cardsCount) {

  // Создание массива
  function createNumbersArray(count) {
    const result = [];
    for (let i = 1; i <= count; i++) {
      result.push(i, i);
    }
    return result;
  }
  createNumbersArray(cardsCount);

  // Перемешивание массива чисел
  function shuffle(arr) {
    for (let i = 0; i < arr.length; i++) {
      let randomIndex = Math.floor(Math.random() * arr.length);
      temp = arr[i];
      arr[i] = arr[randomIndex];
      arr[randomIndex] = temp;
    }
    return arr;
  }
  result = shuffle(createNumbersArray(cardsCount));
  console.log(result);

  // настройка сетки
  let columns = 2;
  if (cardsCount === 3) {
    columns = 3;
  }
  if (cardsCount > 3 && cardsCount % 2 == 0) {
    columns = 4;
  }
  if (cardsCount > 3 && cardsCount % 2 != 0) {
    columns = 5;
  }
  game.style = `grid-template-columns: repeat(${columns}, 1fr);`


  let firstCard = null;
  let secondCard = null;
  // создаем карточки
  for (const cardNumber of result) {
    let card = document.createElement('div');
    card.classList.add('card');
    card.textContent = cardNumber;

    // клик по карточке
    card.addEventListener('click', () => {
      if (card.classList.contains('open') || card.classList.contains('success')) {
        return;
      }

      if (firstCard !== null && secondCard !== null) {
        firstCard.classList.remove('open');
        secondCard.classList.remove('open');
        firstCard = null;
        secondCard = null;
      }
      card.classList.add('open');

      if (firstCard === null) {
        firstCard = card;
      }
      else {
        secondCard = card;
      }

      if (firstCard !== null && secondCard !== null) {
        let firstCardNumber = firstCard.textContent;
        let secondCardNumber = secondCard.textContent;

        if (firstCardNumber === secondCardNumber) {
          firstCard.classList.add('success');
          secondCard.classList.add('success');
        }
      }

      if (result.length === document.querySelectorAll('.success').length) {
        setTimeout(function () {
          game.innerHTML = '';
          alert('ПОБЕДА! Сыграем еще раз?');
          let cardsCount = Number(prompt("Введите количество пар", 8));
          startGame(game, cardsCount);
        }, 300)
      }
    })
    game.append(card);
  }
};

// создаем блок карточек
const divCards = document.getElementById('game');
let cardsCount = Number(prompt("Введите кол-во пар", 8));
startGame(game, cardsCount);
