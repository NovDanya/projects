import { errors } from '../utils/dictionarty';

export function validateDate(dateString: string): {
  isValid: boolean;
  message: string;
} {
  if (!dateString || typeof dateString !== 'string') {
    return {
      isValid: false,
      message: errors.date.required,
    };
  }

  const invalidCharsPattern = /[a-zA-Z<>@!#$%^&*()_+={}[\]:;"'|\\?/~`]/;
  if (invalidCharsPattern.test(dateString)) {
    return {
      isValid: false,
      message: errors.date.invalidCharacters,
    };
  }

  const datePattern = /^\d{2}\.\d{2}\.\d{4}$/;

  if (!datePattern.test(dateString)) {
    return {
      isValid: false,
      message: errors.date.pattern,
    };
  }

  const [day, month, year] = dateString.split('.').map(Number);

  const inputDate = new Date(year, month - 1, day);

  const today = new Date();
  today.setHours(0, 0, 0, 0);

  if (inputDate.getDate() !== day || inputDate.getMonth() !== month - 1 || inputDate.getFullYear() !== year) {
    return {
      isValid: false,
      message: errors.date.invalid,
    };
  }

  if (inputDate < today) {
    return {
      isValid: false,
      message: errors.date.past,
    };
  }

  return {
    isValid: true,
    message: errors.date.valid,
  };
}
