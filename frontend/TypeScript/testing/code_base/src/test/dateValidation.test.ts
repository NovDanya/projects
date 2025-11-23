import { validateDate } from '../validate/validateDate';

describe('Валидация даты', () => {
    test('пропускает корректную дату в формате ДД.ММ.ГГГГ', () => {
        const result = validateDate('25.12.2025');
        expect(result.isValid).toBe(true);
        expect(result.message).toBe('Date is valid.');
    });

    test('не пропускает спецсимволы (например, @, #, $)', () => {
        const result = validateDate('25@12.2025');
        expect(result.isValid).toBe(false);
        expect(result.message).toBe('Date contains invalid characters.');
    });

    test('не пропускает буквенные значения', () => {
        const result = validateDate('25.12.202a');
        expect(result.isValid).toBe(false);
        expect(result.message).toBe('Date contains invalid characters.');
    });

    test('не пропускает формат без точки или с неправильным разделителем', () => {
        expect(validateDate('25-12-2025').isValid).toBe(false);
        expect(validateDate('25/12/2025').isValid).toBe(false);
        expect(validateDate('25 12 2025').isValid).toBe(false);
    });

    test('выдаёт ошибку, если дата раньше текущей', () => {
        const yesterday = new Date();
        yesterday.setDate(yesterday.getDate() - 1);
        const dateString = yesterday.toLocaleDateString('ru-RU'); // ДД.ММ.ГГГГ

        const result = validateDate(dateString);
        expect(result.isValid).toBe(false);
        expect(result.message).toBe('Date cannot be in the past.');
    });

    test('пропускает сегодняшнюю дату', () => {
        const today = new Date();
        const dateString = today.toLocaleDateString('ru-RU');
        const result = validateDate(dateString);
        expect(result.isValid).toBe(true);
    });

    test('не пропускает некорректные даты (например, 32.01.2025)', () => {
        const result = validateDate('32.01.2025');
        expect(result.isValid).toBe(false);
        expect(result.message).toBe('Date is invalid.');
    });

    test('не пропускает пустую строку', () => {
        const result = validateDate('');
        expect(result.isValid).toBe(false);
        expect(result.message).toBe('Date is required.');
    });

    test('не пропускает формат, не соответствующий ДД.ММ.ГГГГ', () => {
        expect(validateDate('1.1.2025').isValid).toBe(false);     // без ведущих нулей
        expect(validateDate('01.1.2025').isValid).toBe(false);
        expect(validateDate('01.01.25').isValid).toBe(false);
    });
});