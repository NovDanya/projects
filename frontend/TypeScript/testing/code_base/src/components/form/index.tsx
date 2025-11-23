import { ChangeEvent, SetStateAction, useState } from 'react';

import './index.css';
import { VARIABLES } from '../../utils/stub';
import { validateCityName } from '../../validate/validateCity';
import { Booking } from '../../utils/generateBookings';

function MyForm(props: { setResults: React.Dispatch<SetStateAction<Booking[]>> }): React.JSX.Element {
  const [textInput, setTextInput] = useState('');
  const [dateInput1, setDateInput1] = useState('');
  const [dateInput2, setDateInput2] = useState('');
  const [error, setError] = useState('');

  const handleTextInputChange = (event: { target: { value: string } }) => {
    const value = event.target.value;
    setTextInput(value);
    if (validateCityName(value).isValid === false) {
      setError(validateCityName(value).message);
    } else {
      setError('');
    }
  };

  const findItems = (text: string, date1 = '', date2 = '') => {
    const results = VARIABLES.filter((booking) => {
      const matchesText = booking.location.toLowerCase().includes(text.toLowerCase());
      const matchesDate1 = date1 === '' || booking.checkIn === date1;
      const matchesDate2 = date2 === '' || booking.checkOut === date2;

      return matchesText && matchesDate1 && matchesDate2;
    });

    return props.setResults(results);
  };

  const handleDateInputChange = (
    event: ChangeEvent<HTMLInputElement>,
    setDate: { (value: SetStateAction<string>): void; (value: SetStateAction<string>): void; (arg0: string): void },
  ) => {
    const value = event.target.value.replace(/\D/g, '');
    const parts = [];

    if (value.length > 0) {
      parts.push(value.substring(0, 2));
    }
    if (value.length > 2) {
      parts.push(value.substring(2, 4));
    }
    if (value.length > 4) {
      parts.push(value.substring(4, 8));
    }

    setDate(parts.join('.'));
  };

  const handleSubmit = (event: { preventDefault: () => void }) => {
    event.preventDefault();
    findItems(textInput, dateInput1, dateInput2);
  };

  return (
    <form
      className="form"
      onSubmit={handleSubmit}
      style={{ maxWidth: '300px', margin: '20px auto', display: 'flex', flexDirection: 'column', gap: '10px' }}
    >
      <div className="item">
        <label htmlFor="textInput" className="label">
          Куда отправимся?
        </label>
        <input
          className="input"
          type="text"
          id="textInput"
          name="textInput"
          value={textInput}
          onChange={handleTextInputChange}
          placeholder="Введите текст"
          autoComplete="off"
        />
        {error && <p className="error">{error}</p>}
      </div>
      <div className="item">
        <label htmlFor="dateInput1" className="label">
          Дата заезда:
        </label>
        <input
          type="text"
          id="dateInput1"
          name="dateInput1"
          value={dateInput1}
          onChange={(e) => handleDateInputChange(e, setDateInput1)}
          placeholder="ДД.ММ.ГГГГ"
          maxLength={10}
          className="input"
        />
      </div>

      <div className="item">
        <label htmlFor="dateInput2" className="label">
          Дата выезда:
        </label>
        <input
          type="text"
          id="dateInput2"
          name="dateInput2"
          value={dateInput2}
          onChange={(e) => handleDateInputChange(e, setDateInput2)}
          placeholder="ДД.ММ.ГГГГ"
          maxLength={10}
          className="input"
        />
      </div>

      <button type="submit" className="create-button" disabled={!textInput || error.length > 0}>
        Найти
      </button>
    </form>
  );
}

export default MyForm;
