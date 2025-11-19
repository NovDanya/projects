(function() {
    //заголовок
    function createAppTitle(title) {
        let appTitle = document.createElement('h2');
        appTitle.innerHTML = title;
        return appTitle;
    }

    //форма для создания тела
    function createTodoItemForm() {
        let form = document.createElement('form');
        let input = document.createElement('input');
        let buttonWrapper = document.createElement('div');
        let button = document.createElement('button');

        form.classList.add('input-group','mb-3');
        input.classList.add('form-control');
        input.placeholder = 'Введите название нового дела';
        buttonWrapper.classList.add('input-group-append');
        button.classList.add('btn','btn-primary');
        button.textContent = 'Добавить дело';
        button.setAttribute('disabled', true);

        input.oninput = function(){
            if (input.value) {
                button.removeAttribute('disabled');
            }
            if (input.value.length === 0) {
                button.setAttribute('disabled', true);
            }
        }

        buttonWrapper.append(button);
        form.append(input);
        form.append(buttonWrapper);

        return {
            form,
            input,
            button,
        };
    }

    //создаем и возращаем список
    function createTodoList() {
        let list = document.createElement('ul');
        list.classList.add('list-group');
        return list;
    }

    //создаем элемент списка
    function createTodoItem(name) {
        let item = document.createElement('li');

        //кнопки помещаем в блок
        let buttonGroup = document.createElement('div');
        let doneButton = document.createElement('button');
        let deleteButton = document.createElement('button');

        //устанавливаем стили для li, а также размещаем кнопки в правой его части с помощью flex
        item.classList.add('list-group-item', 'd-flex','justify-content-between','aligh-items-center');
        item.textContent = name;

        buttonGroup.classList.add('btn-group','btn-group-sm');
        doneButton.classList.add('btn','btn-success');
        doneButton.textContent='Готово';
        deleteButton.classList.add('btn','btn-danger');
        deleteButton.textContent='Удалить';

        buttonGroup.append(doneButton);
        buttonGroup.append(deleteButton);
        item.append(buttonGroup);

        return {
            item,
            doneButton,
            deleteButton,
        };
    }

    //получение id
    function getId(todos) {
        let maxId = 0;
        for (let i = 0; i < todos.length; i++) {
            if (todos[i].id > maxId) {
                maxId = todos[i].id;
            }
        }
        return maxId + 1;
    }

    //сохранение в лс
    function saveTodoList(key, data) {
        localStorage.setItem(key, JSON.stringify(data));
    }
    
    //загрузка из лс
    function loadTodoList(key) {
        return JSON.parse(localStorage.getItem(key));
    }
    
    function createTodoApp(container, title='Список дел', listName) {
        let todos = loadTodoList(listName) || []; //загружаем список дел из лс или создаем пустой массив
        
        let todoAppTitle = createAppTitle(title);
        let todoItemForm = createTodoItemForm();
        let todoList = createTodoList();
        
        container.append(todoAppTitle);
        container.append(todoItemForm.form);
        container.append(todoList);
    
        //выводим существующие дела из лс
        for (let i = 0; i < todos.length; i++) {
            let todoItem = todos[i];
            let todoElement = createTodoItem(todoItem.name);
            
            if (todoItem.done) {
                todoElement.item.classList.add('list-group-item-success');
            }
            
            todoList.append(todoElement.item);
        
            todoElement.doneButton.addEventListener('click', function() {
                todoElement.item.classList.toggle('list-group-item-success');
                todoItem.done = !todoItem.done;
                updateTodos(); //сохраняем обновленный список дел после изменения статуса
            });
            
            todoElement.deleteButton.addEventListener('click', function() {
                if (confirm('Вы уверены')) {
                    todoElement.item.remove();
                    todos = todos.filter(item => item.id !== todoItem.id);
                    updateTodos(); //сохраняем обновленный список дел после удаления
                }
            });
        }
        
        //сохраняем обновленный список дел в лс
        function updateTodos() {
            saveTodoList(listName, todos);
        }
       
        //браузер создает submit на форме по нажатию кнопки или enter
        todoItemForm.form.addEventListener('submit', function(e) {
            e.preventDefault(); //чтобы страница браузера не перезагружалась
        
            //игнорируем создание элемента, если пользователь ничего не ввел
            if (!todoItemForm.input.value) {
                return;
            }
            
            //создаем новый объект дела
            let todoItem = {
                name: todoItemForm.input.value,
                done: false,
                id: getId(todos),
            };
            
            todos.push(todoItem);
            updateTodos(); // Сохраняем обновленный список дел после добавления
            
            let todoElement = createTodoItem(todoItem.name);
            todoList.append(todoElement.item);
        
            todoElement.doneButton.addEventListener('click', function() {
                todoElement.item.classList.toggle('list-group-item-success');
                todoItem.done = !todoItem.done;
                updateTodos(); //сохраняем обновленный список дел после изменения статуса
            });
            
            todoElement.deleteButton.addEventListener('click', function() {
                if (confirm('Вы уверены')) {
                    todoElement.item.remove(); //удаляем с экрана
                    todos = todos.filter(item => item.id !== todoItem.id); //удаляем дело из массива
                    updateTodos(); //сохраняем обновленный список дел после удаления
                }
            });
            
           todoItemForm.input.value = '';
           todoItemForm.button.setAttribute('disabled', true);
        });
    }        
    
    window.createTodoApp = createTodoApp;
})();