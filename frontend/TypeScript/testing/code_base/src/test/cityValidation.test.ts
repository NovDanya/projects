import { validateCityName } from '../validate/validateCity';

describe('Валидация названия города', () => {
    test('не пропускает пустое значение', () => {
        const result = validateCityName('');
        expect(result.isValid).toBe(false);
        expect(result.message).toBe('City name is required.');
    });

    test('не пропускает, если есть экранирование (<, >, &, ")', () => {
        const cases = ['<div>', 'City & Country', 'Name "Test"', 'Alert > OK'];

        cases.forEach((input) => {
            const result = validateCityName(input);
            expect(result.isValid).toBe(false);
            expect(result.message).toBe('City name contains characters that need to be escaped.');
        });
    });

    test('пропускает название с дефисами и восклицательным знаком (например, Saint-Louis-du-Ha! Ha!)', () => {
        const result = validateCityName('Saint-Louis-du-Ha! Ha!');
        expect(result.isValid).toBe(true);
        expect(result.message).toBe('City name is valid.');
    });

    test('пропускает название со спецсимволами (например, Ağrı, São Paulo, naïve)', () => {
        const cities = ['Ağrı', 'São Paulo', ' naïve ', 'München', 'Zürich', 'Łódź'];

        cities.forEach((city) => {
            const result = validateCityName(city);
            expect(result.isValid).toBe(true);
        });
    });

    test('пропускает название из одной буквы', () => {
        const result = validateCityName('A');
        expect(result.isValid).toBe(true);
        expect(result.message).toBe('City name is valid.');
    });

    test('не пропускает цифры и символы, не входящие в разрешённый набор', () => {
        const result = validateCityName('City123');
        expect(result.isValid).toBe(false);
        expect(result.message).toBe('City name contains invalid characters.');
    });
});