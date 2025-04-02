function formatDate(date) {
  const day = String(date.getDate()).padStart(2, "0");
  const month = String(date.getMonth() + 1).padStart(2, "0");
  const year = date.getFullYear();
  return `${day}.${month}.${year}`;
}

function calculateAge(birthDate) {
  const today = new Date();
  let age = today.getFullYear() - birthDate.getFullYear();
  const m = today.getMonth() - birthDate.getMonth();
  if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
    age--;
  }
  return age;
}

// Этап 2. Создайте массив объектов студентов.Добавьте в него объекты студентов, например 5 студентов.
const studentsList = [
  { firstName: "Иван", lastName: "Иванов", middleName: "Иванович", birthDate: new Date(2000, 5, 15), startYear: 2019, faculty: "Математика" },
  { firstName: "Петр", lastName: "Петров", middleName: "Петрович", birthDate: new Date(1998, 2, 10), startYear: 2018, faculty: "Физика" },
  { firstName: "Сидор", lastName: "Сидоров", middleName: "Сидорович", birthDate: new Date(1999, 8, 20), startYear: 2019, faculty: "Химия" },
  { firstName: "Анна", lastName: "Аннова", middleName: "Петровна", birthDate: new Date(2001, 1, 5), startYear: 2021, faculty: "Математика" },
  { firstName: "Мария", lastName: "Маринова", middleName: "Ивановна", birthDate: new Date(1997, 3, 25), startYear: 2020, faculty: "Информатика" },
];

// Этап 3. Создайте функцию вывода одного студента в таблицу, по аналогии с тем, как вы делали вывод одного дела в модуле 8. Функция должна вернуть html элемент с информацией и пользователе.У функции должен быть один аргумент - объект студента.
function getStudentItem(studentObj) {
  const fio = `${studentObj.lastName} ${studentObj.firstName} ${studentObj.middleName}`;
  const fac = `${studentObj.faculty}`;
  const birthDateFormatted = formatDate(studentObj.birthDate);
  const age = calculateAge(studentObj.birthDate);
  const studyYears = `${studentObj.startYear}-${studentObj.startYear + 4}`;
  const course = new Date().getFullYear() >= studentObj.startYear + 4 ? "закончил" : new Date().getFullYear() - studentObj.startYear + 1;

  const row = document.createElement("tr");
  row.innerHTML = `
    <td>${fio}</td>
    <td>${fac}</td>
    <td>${birthDateFormatted} (${age} лет)</td>
    <td>${studyYears} (${course})</td>
  `;
  return row;
}

// Этап 4. Создайте функцию отрисовки всех студентов. Аргументом функции будет массив студентов.Функция должна использовать ранее созданную функцию создания одной записи для студента.Цикл поможет вам создать список студентов.Каждый раз при изменении списка студента вы будете вызывать эту функцию для отрисовки таблицы.
function renderStudentsTable(studentsArray) {
  const tableBody = document.querySelector("tbody");
  tableBody.innerHTML = "";
  studentsArray.forEach((student) => {
    tableBody.appendChild(getStudentItem(student));
  });
}

// Этап 5. К форме добавления студента добавьте слушатель события отправки формы, в котором будет проверка введенных данных.Если проверка пройдет успешно, добавляйте объект с данными студентов в массив студентов и запустите функцию отрисовки таблицы студентов, созданную на этапе 4.
document.getElementById("add-student-form").addEventListener("submit", (e) => {
  e.preventDefault();
  const errorMessage = document.getElementById("error-message");
  errorMessage.textContent = "";
  const errors = [];

  const firstName = document.getElementById("first-name").value.trim();
  const lastName = document.getElementById("last-name").value.trim();
  const middleName = document.getElementById("middle-name").value.trim();
  const birthDateInput = document.getElementById("birth-date");
  const startYear = parseInt(document.getElementById("start-year").value.trim());
  const faculty = document.getElementById("faculty").value.trim();

  const birthDate = new Date(birthDateInput.value);
  const currentDate = new Date();
  if (birthDate < new Date(1900, 0, 1) || birthDate > currentDate) {
    errors.push("Дата рождения должна быть в диапазоне от 01.01.1900 до текущей даты.");
  }

  const minStartYear = 2000;
  const maxStartYear = currentDate.getFullYear();
  if (startYear < minStartYear || startYear > maxStartYear) {
    errors.push(`Год начала обучения должен быть в диапазоне от ${minStartYear} до ${maxStartYear}.`);
  }

  if (errors.length > 0) {
    errorMessage.textContent = errors.join("\n");
    return;
  }

  // Добавление нового студента
  studentsList.push({
    firstName,
    lastName,
    middleName,
    birthDate,
    startYear,
    faculty,
  });

  e.target.reset(); // Очистка формы

  renderStudentsTable(studentsList); // Перерисовка таблицы
});

// Этап 5. Создайте функцию сортировки массива студентов и добавьте события кликов на соответствующие колонки.
let currentSortKey = null;
let sortOrder = 1;

function sortStudents(students, key) {
  if (key === currentSortKey) {
    sortOrder *= -1; // Переключение порядка сортировки
  } else {
    currentSortKey = key;
    sortOrder = 1; // Начальный порядок сортировки
  }

  return [...students].sort((a, b) => {
    let aValue, bValue;

    if (key === "fio") {
      aValue = `${a.lastName} ${a.firstName} ${a.middleName}`.toLowerCase();
      bValue = `${b.lastName} ${b.firstName} ${b.middleName}`.toLowerCase();
    } else if (key === "faculty") {
      aValue = a.faculty.toLowerCase();
      bValue = b.faculty.toLowerCase();
    } else if (key === "birthday") {
      aValue = a.birthDate.getTime();
      bValue = b.birthDate.getTime();
    } else if (key === "study-years") {
      aValue = a.startYear;
      bValue = b.startYear;
    }

    return sortOrder * (aValue > bValue ? 1 : -1);
  });
}

// Обработчики кликов на заголовках таблицы
document.querySelectorAll("th").forEach((th) => {
  th.addEventListener("click", () => {
    const sortKey = th.dataset.sort;
    const sortedStudents = sortStudents(studentsList, sortKey);
    renderStudentsTable(sortedStudents);
  });
});

// Этап 6. Создайте функцию фильтрации массива студентов и добавьте события для элементов формы.
// Функция фильтрации студентов
function filterStudents(students) {
  const filters = {
    name: document.getElementById("filter-name").value.trim().toLowerCase(),
    faculty: document.getElementById("filter-faculty").value.trim().toLowerCase(),
    startYear: parseInt(document.getElementById("filter-start-year").value.trim()) || null,
    endYear: parseInt(document.getElementById("filter-end-year").value.trim()) || null,
  };

  return students.filter((student) => {
    const fio = `${student.lastName} ${student.firstName} ${student.middleName}`.toLowerCase();
    return (
      (!filters.name || fio.includes(filters.name)) &&
      (!filters.faculty || student.faculty.toLowerCase().includes(filters.faculty)) &&
      (!filters.startYear || student.startYear === filters.startYear) &&
      (!filters.endYear || student.startYear + 4 === filters.endYear)
    );
  });
}

// Обновление таблицы при изменении фильтров
const filterFields = ["filter-name", "filter-faculty", "filter-start-year", "filter-end-year"];
filterFields.forEach((fieldId) => {
  document.getElementById(fieldId).addEventListener("input", () => {
    const filteredStudents = filterStudents(studentsList);
    renderStudentsTable(filteredStudents);
  });
});

renderStudentsTable(filterStudents(studentsList));
