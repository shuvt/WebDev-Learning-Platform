<?php
// php-course.php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/toggle-topic.php';

if (!isLoggedIn()) {
    header('Location: /login.php');
    exit;
}

$user = getCurrentUser();
require_once __DIR__ . '/templates/header.php';

define('TAB1', '&nbsp;&nbsp;&nbsp;&nbsp;');
define('TAB2', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
define('SPACE', '&nbsp;&nbsp;');

?>

<link rel="stylesheet" href="/templates/course.css">

<?php if (isTeacher()): ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.7/quill.snow.css" rel="stylesheet">
<style>
.edit-section-btn {
    background: none; border: 1px solid #bbb; border-radius: 4px;
    padding: 1px 8px; margin-left: 10px; cursor: pointer;
    font-size: 0.78rem; color: #666; vertical-align: middle;
}
.edit-section-btn:hover { background: #f0f0f0; border-color: #888; }
.section-edit-form {
    background: #f8f8f8; border: 1px solid #ddd; border-radius: 6px;
    padding: 14px; margin: 10px 0;
}
.ql-container { font-size: 14px; background: white; }
.ql-editor { min-height: 200px; }
.add-topic-btn {
    display: block; width: 100%; margin: 18px 0 10px;
    padding: 10px; background: #eaf4f2; border: 2px dashed #7bb8b3;
    border-radius: 8px; color: #2e5d5a; font-size: 1rem;
    cursor: pointer; text-align: center;
}
.add-topic-btn:hover { background: #d5ece9; }
.add-topic-form {
    background: #f8f8f8; border: 1px solid #ddd; border-radius: 8px;
    padding: 18px; margin: 10px 0;
}
.add-topic-form input[type=text] {
    width: 100%; box-sizing: border-box; padding: 8px 10px;
    font-size: 1rem; border: 1px solid #ccc; border-radius: 4px; margin-bottom: 10px;
}
.delete-section-btn {
    background: none; border: 1px solid #e08080; border-radius: 4px;
    padding: 1px 8px; margin-left: 6px; cursor: pointer;
    font-size: 0.78rem; color: #c0392b; vertical-align: middle;
}
.delete-section-btn:hover { background: #fdf0f0; }
.section-title-input {
    width: 100%; box-sizing: border-box; padding: 7px 10px;
    font-size: 1rem; font-weight: 600; border: 1px solid #ccc;
    border-radius: 4px; margin-bottom: 10px; font-family: inherit;
}
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.7/quill.min.js"></script>
<script>
var editors = {};
var TOOLBAR = [
    [{ header: [2, 3, 4, false] }],
    ['bold', 'italic', 'underline'],
    [{ list: 'ordered' }, { list: 'bullet' }],
    ['code-block', 'link'],
    ['clean']
];

function toggleSectionEdit(course, key) {
    var form = document.getElementById('sef-' + key);
    var content = document.getElementById('sc-' + key);
    if (!form || !content) return;
    if (form.style.display === 'none' || !form.style.display) {
        if (!editors[key]) {
            editors[key] = new Quill('#editor-' + key, { theme: 'snow', modules: { toolbar: TOOLBAR } });
        }
        editors[key].clipboard.dangerouslyPasteHTML(content.innerHTML.trim());
        form.style.display = 'block';
        content.style.outline = '2px dashed #aaa';
    } else {
        form.style.display = 'none';
        content.style.outline = '';
    }
}

var newTopicEditor = null;
function toggleAddTopic() {
    var form = document.getElementById('add-topic-form');
    if (!form) return;
    form.style.display = (form.style.display === 'none' || !form.style.display) ? 'block' : 'none';
    if (form.style.display === 'block' && !newTopicEditor) {
        newTopicEditor = new Quill('#editor-new-topic', { theme: 'snow', modules: { toolbar: TOOLBAR } });
    }
}

document.addEventListener('submit', function(e) {
    if (e.target.classList.contains('section-edit-form')) {
        var key = e.target.dataset.key;
        if (key && editors[key]) {
            e.target.querySelector('textarea[name=content]').value = editors[key].root.innerHTML;
        }
    }
    if (e.target.id === 'add-topic-form') {
        if (newTopicEditor) {
            document.getElementById('new-topic-content').value = newTopicEditor.root.innerHTML;
        }
    }
});
</script>
<?php endif; ?>

<div class="course-container">
    <div class="course-content">
        <div class="table-of-contents">
            <h2>Содержание курса</h2>
            <nav class="toc-nav">
                <ul>
                    <li><a href="#introduction">Введение</a></li>
                    <li><a href="#basic-php">2. ОСНОВЫ PHP</a>
                        <ul>
                            <li><a href="#intro-php"><?= htmlspecialchars(getCourseSectionTitle('php', 'intro-php') ?? '2.1. Введение в PHP') ?></a></li>
                            <li><a href="#basic-syntax-php"><?= htmlspecialchars(getCourseSectionTitle('php', 'basic-syntax-php') ?? '2.2. Основы синтаксиса') ?></a></li>
                            <li><a href="#work-with-data"><?= htmlspecialchars(getCourseSectionTitle('php', 'work-with-data') ?? '2.3. Работа с данными') ?></a></li>
                            <li><a href="#functions-and-OOP"><?= htmlspecialchars(getCourseSectionTitle('php', 'functions-and-OOP') ?? '2.4. Функции и ООП') ?></a></li>
                            <li><a href="#work-with-files"><?= htmlspecialchars(getCourseSectionTitle('php', 'work-with-files') ?? '2.5. Работа с файлами') ?></a></li>
                            <li><a href="#php+sql"><?= htmlspecialchars(getCourseSectionTitle('php', 'php+sql') ?? '2.6. Связка PHP+SQL') ?></a></li>
                            <?php foreach (getCustomSections('php') as $cs): ?>
                            <li><a href="#<?= htmlspecialchars($cs['section_key']) ?>"><?= htmlspecialchars($cs['title']) ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>

        <div class="course-material">
            <!-- Введение -->
            <section id="introduction" class="chapter">
                <div class="text-content" id="sc-introduction">
                <?php $__sc = getCourseSection('php', 'introduction'); if ($__sc !== null): echo $__sc; else: ?>
                    <p>Добро пожаловать в курс <strong>по PHP</strong>! Этот курс познакомит вас с основами языка.</p>
                <?php endif; ?>
                </div>
                <?php if (isTeacher()): ?>
                <div style="text-align:right; margin-top:4px">
                    <button class="edit-section-btn" onclick="toggleSectionEdit('php','introduction')">✎ Редактировать</button>
                </div>
                <form method="post" action="/toggle-topic.php" class="section-edit-form" id="sef-introduction" data-key="introduction" style="display:none">
                    <input type="hidden" name="action" value="save_section">
                    <input type="hidden" name="course" value="php">
                    <input type="hidden" name="topic_key" value="introduction">
                    <input type="hidden" name="back" value="/php-course.php#introduction">
                    <div id="editor-introduction"></div>
                    <textarea name="content" style="display:none"></textarea>
                    <div style="margin-top:8px; display:flex; gap:8px">
                        <button type="submit" class="btn btn-primary btn-small">Сохранить</button>
                        <button type="button" class="btn btn-small" onclick="toggleSectionEdit('php','introduction')">Отмена</button>
                    </div>
                </form>
                <?php endif; ?>
            </section>

            <!-- ОСНОВЫ PHP -->
            <section id="basic-php" class="chapter">
                <h2>2. ОСНОВЫ PHP</h2>

                <!-- 2.1 Введение в PHP -->
                <article id="intro-php" class="lesson">
                    <?php $__intro_phpTitle = getCourseSectionTitle('php', 'intro-php') ?? '2.1. Введение в PHP'; ?>
                    <h3><?= htmlspecialchars($__intro_phpTitle) ?><?php if (isTeacher()): ?> <button class="edit-section-btn" onclick="toggleSectionEdit('php','intro-php')">✎</button><?php endif; ?></h3>

                    <div class="text-content" id="sc-intro-php">
                    <?php $__sc = getCourseSection('php', 'intro-php'); if ($__sc !== null): echo $__sc; else: ?>
                        <h4>2.1.1. Что такое PHP? Его назначение.</h4>
                        <p><strong>PHP</strong> (сокращение от англ. Hypertext Preprocessor)) — это серверный язык программирования, созданный специально для веб-разработки. PHP работает на сервере и генерирует HTML-код, отправляемый пользователю</p>

                        <p>Одним из ключевых преимуществ PHP является его тесная интеграция с HTML, что позволяет легко встраивать код PHP прямо в веб-страницы. Также PHP поддерживает множество баз данных, что позволяет создавать собственные системы управления контентом (CMS), интернет-магазины, форумы и другие веб-приложения, требующие хранения и обработки данных.</p>

                        <h4 style="margin-top: 30px;">2.1.2. Установка и настройка окружения.</h4>
                        <ol style="margin-left: 20px;">
                            <li>Скачать последнюю версию PHP можно с официального сайта: https://www.php.net/</li>
                            <li>Распакуйте архив в удобное место (например, C:\php)</li>
                            <li>В полученной папке вы обнаружите два конфигурационных файла: <br>
                                <u>php.ini-development</u> — для разработки. <br>
                                <u>php.ini-production</u> — для сервера. <br>
                                Переименуйте файл “php.ini-production” в “php.ini”. <br>
                                Php.ini — это главный конфигурационный файл, который управляет 	работой PHP. При необходимости можно раскомментировать любые нужные расширения (extensions), удалив точку с запятой в начале строки.</li>
                            <li>Добавьте путь к PHP в системные переменные: Откройте "Свойства системы" → "Дополнительно" → "Переменные среды". В разделе "Системные переменные" найдите Path и добавьте путь к папке с PHP (например, C:\php).<br> Перезапустите компьютер.</li>
                            <li>Работать с PHP можно в любой удобной вам IDE, например, в Visual Studio Code</li>
                        </ol>

                        <h4 style="margin-top: 30px;">2.1.3. Первый скрипт.</h4>
                        <p style="margin-bottom: 0.4em;"><strong>Подготовка рабочей среды</strong></p>
                        <p>Перед созданием первого скрипта убедимся, что PHP установлен. Проверить это можно выполнив команду php -v в терминале. Если PHP установлен, будет показана его версия. </p>
                        <p style="margin-bottom: 0.4em;"><strong>Создание PHP-файла</strong></p>
                        <p>В Visual Studio Code создаем новую папку для проекта с любым названием. В этой папке создаем файл с расширением .php, например, hello.php.
                            <br>Внутри файла мы напишем простейший PHP-скрипт, который будет выводить текст в консоль.</p>
                        <p style="margin-bottom: 0.4em;"><strong>Написание и запуск первого скрипта</strong></p>
                        <p>Открываем созданный файл и вводим следующий код:</p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 2.1. Первый код на PHP</strong><br>
                                &lt;?php<br>
                                <?= TAB1 ?>echo "Hello, World!";<br>
                                ?>
                            </p>
                         </div>
                        <p>Для выполнения скрипта открываем терминал в Visual Studio Code или используем системную командную строку. Для этого переходим в папку с нашим скриптом с помощью команды cd, затем выполняем командой php hello.php. После нажатия Enter в консоли появится текст "Hello, World. </p>
                        <p>Теперь можно поэкспериментировать с кодом. Например, попробовать вывести другую строку или использовать несколько команд echo подряд. </p>
                    <?php endif; ?>
                    </div>

                    <?php if (isTeacher()): ?>
                    <form method="post" action="/toggle-topic.php" class="section-edit-form" id="sef-intro-php" data-key="intro-php" style="display:none">
                        <input type="hidden" name="action" value="save_section">
                        <input type="hidden" name="course" value="php">
                        <input type="hidden" name="topic_key" value="intro-php">
                        <input type="hidden" name="back" value="/php-course.php#intro-php">
                        <input type="text" name="title" class="section-title-input" value="<?= htmlspecialchars($__intro_phpTitle) ?>" placeholder="Заголовок темы">
                        <div id="editor-intro-php"></div>
                        <textarea name="content" style="display:none"></textarea>
                        <div style="margin-top:8px; display:flex; gap:8px">
                            <button type="submit" class="btn btn-primary btn-small">Сохранить</button>
                            <button type="button" class="btn btn-small" onclick="toggleSectionEdit('php','intro-php')">Отмена</button>
                        </div>
                    </form>
                    <?php endif; ?>

                <?= topicButton('php', 'intro-php', 'basic-syntax-php') ?>
                </article>

                <!-- 2.2 Основы синтаксиса -->
                <article id="basic-syntax-php" class="lesson">
                    <?php $__basic_syntax_phpTitle = getCourseSectionTitle('php', 'basic-syntax-php') ?? '2.2. Основы синтаксиса'; ?>
                    <h3><?= htmlspecialchars($__basic_syntax_phpTitle) ?><?php if (isTeacher()): ?> <button class="edit-section-btn" onclick="toggleSectionEdit('php','basic-syntax-php')">✎</button><?php endif; ?></h3>
                    <div class="text-content" id="sc-basic-syntax-php">
                    <?php $__sc = getCourseSection('php', 'basic-syntax-php'); if ($__sc !== null): echo $__sc; else: ?>
                        <h4>2.2.1. Структура кода. Теги, операторы вывода, комментарии</h4>
                        <p style="margin-bottom: 0.4em;"><strong>Теги</strong></p>
                        <p>Основу PHP-кода составляет использование специальных тегов, которые сообщают интерпретатору, где начинается и заканчивается исполняемый код. Стандартным является парный тег &lt;?php ... ?&gt;, внутри которого располагаются все PHP-инструкции. Важной особенностью является то, что эти теги могут многократно чередоваться с HTML-кодом в пределах одного файла, что обеспечивает удобство генерации динамического содержимого.</p>
                        <p>Для краткого вывода значений существует сокращенный синтаксис &lt;?= ... ?&gt;, который автоматически выводит результат выражения. Однако его использование требует особой настройки сервера, поэтому в профессиональной разработке предпочтение отдается полной форме с явным указанием функции echo.</p>
                        <p style="margin-bottom: 0.4em;"><strong>Операторы вывода</strong></p>
                        <p>В PHP для вывода данных используются два основных оператора - echo и print. Они похожи, но имеют некоторые различия, которые следует учитывать.</p>
                        <p><strong>Оператор echo </strong>является наиболее распространенным средством вывода информации. Он может принимать несколько аргументов одновременно, разделенных запятыми. Важной особенностью echo является то, что он не возвращает значения после выполнения. Синтаксически echo может использоваться как с круглыми скобками, так и без них, поскольку технически это языковая конструкция, а не функция.</p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 2.2. Примеры использования оператора echo</strong><br>
                                <?= TAB1 ?>echo "Hello World";       // Вывод строки<br>
                                <?= TAB1 ?>echo $username;           // Вывод переменной<br>
                                <?= TAB1 ?>echo "Привет, $username"; // Интерполяция переменной в строку<br>
                                <?= TAB1 ?>echo "Имя: ", $name, ", возраст: ", $age;  // Вывод нескольких аргументов
                            </p>
                        </div>
                        <p><strong>Оператор print</strong>, в отличие от echo, всегда возвращает значение 1, что позволяет использовать его в контексте выражений. Однако print принимает только один аргумент.</p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 2.3. Пример использования оператора print</strong><br>
                                <?= TAB1 ?>echo "Строка 1", "Строка 2";  // Работает<br>
                                <?= TAB1 ?>print "Строка 1", "Строка 2";  // Ошибка
                            </p>
                        </div>
                        <p><strong>Функция printf()</strong>используется для сложного форматированного вывода с использованием шаблонов. Спецификаторы:<br>
                                %s — строка<br>
                                %d — целое число<br>
                                %f — число с плавающей точкой<br>
                                %.2f — число с 2 знаками после запятой
                        </p>
                        <p>Пример использования:</p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 2.3. Примеры использования функции printf()</strong><br>
                                <?= TAB1 ?>$name = "Иван";<br>
                                <?= TAB1 ?>$age = 30;<br>
                                <?= TAB1 ?>$height = 1.85;<br>
                                <?= TAB1 ?>printf("Имя: %s, возраст: %d лет, рост: %.2f м", $name, $age, $height);
                            </p>
                        </div>
                        <p><strong>Функция print_r() </strong>предназначена для удобного отображения сложных структур данных (массивов, объектов).<br>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 2.4. Пример использования функции print_r()</strong><br>
                                <?= TAB1 ?>$user = [<br>
                                <?= TAB2 ?>'name' => 'Иван',<br>
                                <?= TAB2 ?>'age' => 30,<br>
                                <?= TAB2 ?>'skills' => ['PHP', 'JavaScript']<br>
                                <?= TAB1 ?>];<br>
                                <br>
                                <?= TAB1 ?>print_r($user);<br>
                                <br>
                                <?= TAB1 ?>Выведет:<br>
                                <?= TAB1 ?>Array<br>
                                <?= TAB1 ?>(<br>
                                <?= TAB2 ?>[name] => Иван<br>
                                <?= TAB2 ?>[age] => 30<br>
                                <?= TAB2 ?>[skills] => Array<br>
                                <?= TAB2 ?><?= TAB1 ?>(<br>
                                <?= TAB2 ?><?= TAB2 ?>[0] => PHP<br>
                                <?= TAB2 ?><?= TAB2 ?>[1] => JavaScript<br>
                                <?= TAB2 ?><?= TAB1 ?>)<br>
                                <?= TAB1 ?>)
                            </p>
                        </div>
                        <p style="margin-bottom: 0.4em;"><strong>Комментарии</strong></p>
                        <p>Нужны для пояснения логики, временного отключения кода или заметок для других разработчиков<br>
                            Однострочные комментарии - // или #<br>
                            Многострочные комментарии – Обрамляются /* и */<br>
                        </p>
                        <p>Примеры использования различных комментариев:</p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 2.5. Примеры использования различных комментариев</strong><br>
                                &lt;?php<br>
                                <?= TAB1 ?>// Это комментарий<br>
                                <?= TAB1 ?>echo "hello"; # И это тоже<br>
                                ?&gt;<br>
                                <br>
                                &lt;?php<br>
                                <?= TAB1 ?>/*<br>
                                <?= TAB2 ?>Этот комментарий<br>
                                <?= TAB2 ?>может занимать<br>
                                <?= TAB2 ?>несколько строк<br>
                                <?= TAB1 ?>*/<br>
                                <?= TAB1 ?>echo "Hello";<br>
                                ?&gt;
                            </p>
                        </div>

                        <h4 style="margin-top: 2em">2.2.2. Переменные и типы данных.</h4>
                        <p style="margin-bottom: 0.4em"><strong>Переменные</strong></p>
                        <p>Переменные в PHP обозначаются знаком доллара, за которым идёт имя переменной. Имя переменной чувствительно к регистру. Правильное название переменной начинается с буквы в диапазонах A-Z или a-z, ASCII-символа в диапазоне со 128-го по 255-й байт или символа подчёркивания. Затем идёт произвольное количество букв, цифр или подчёркиваний.</p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 2.6. Примеры создания переменных</strong><br>
                                &lt;?php<br>
                                <?= TAB1 ?>$var = 'Маша';<br>
                                <?= TAB1 ?>$Var = 'Саша';<br>
                                <?= TAB1 ?>echo "$var, $Var";<br>
                                <br>
                                <?= TAB1 ?>$_4site = 'привет';<br>
                                <?= TAB1 ?>$4site = 'hello'; // Неправильно, название начинается с цифры<br>
                                ?&gt;
                            </p>
                        </div>
                        <p>По умолчанию в PHP переменные присваиваются по значению. При таком подходе, когда одной переменной присваивается значение другой, создается независимая копия данных. В результате изменения одной из переменных, вторая останется прежней, так как они хранят отдельные экземпляры информации. </p>
                        <p>PHP также предоставляет альтернативный метод работы с переменными - передачу по ссылке. В данном случае новая переменная не получает копию значения, а становится своеобразным указателем на исходные данные. Любые манипуляции с такой переменной-ссылкой автоматически изменяют первоначальное значение, и наоборот - корректировка оригинала сразу отражается на всех связанных переменных.</p>
                        <p>Для создания ссылочного присваивания необходимо перед именем исходной переменной поставить символ амперсанда (&). </p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 2.7. Пример ссылочного присваивания переменных</strong><br>
                                &lt;?php<br>
                                <?= TAB1 ?>$A = 'Саша'; // Присваивает переменной $A значение «Саша»<br>
                                <?= TAB1 ?>$B = &$A; // Ссылка на значение переменной $A через переменную $B<br>
                                <?= TAB1 ?>$B = "Меня зовут $B"; // Изменение значения переменной $B<br>
                                <?= TAB1 ?>echo $B;<br>
                                <?= TAB1 ?>echo $A; // ...меняет и значение переменной $A<br>
                                ?&gt;
                            </p>
                        </div>
                        <p>Выведет: <br>«Меня зовут Саша»<br>«Меня зовут Саша»</p>
                        <p style="margin-bottom: 0.4em"><strong>Типы данных</strong></p>
                        <p>Особенностью PHP является динамическая типизация, при которой тип переменной определяется автоматически на основе присваиваемого значения.<br>Базовые типы в PHP делятся на три категории:</p>
                        <p style="margin-bottom: 0.4em"><strong>Скалярные типы:</strong></p>
                        <ul style="margin-left: 20px;">
                            <li>int</li>
                            <li>float</li>
                            <li>string </li>
                            <li>bool - принимает только два значения, которые выражают истинность: true или false. Обе константы регистронезависимы.</li>
                        </ul>
                        <p style="margin-bottom: 0.4em"><strong>Составные типы:</strong></p>
                        <ul style="margin-left: 20px;">
                            <li>array</li>
                            <li>object</li>
                        </ul>
                        <p style="margin-bottom: 0.4em"><strong>Специальные типы:</strong></p>
                        <ul style="margin-left: 20px;">
                            <li>NULL</li>
                            <li>resource</li>
                        </ul>    
                        <p><strong>Целые числа (integer) </strong>используются для хранения целочисленных значений без дробной части и могут быть как положительными, так и отрицательными.</p>
                        <p>Для записи в восьмеричной системе счисления перед числом ставят ноль — 0. Начиная с PHP 8.1.0 восьмеричную нотацию также дополнили символами 0o или 0O, которые записывают перед числом. Для записи в шестнадцатеричной системе счисления перед числом записывают 0x. Для записи в двоичной системе счисления перед числом указывают символы 0b.</p>
                        <p>Начиная с PHP 7.4.0 при записи целочисленных литералов между цифрами разрешается указывать символы подчёркивания — _, которые улучшают читаемость кода. Подчёркивания удаляются PHP-сканером.</p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 2.8. Примеры записи целых чисел</strong><br>
                                &lt;?php<br>
                                <?= TAB1 ?>$a = 1234; // Десятичное число<br>
                                <?= TAB1 ?>$a = 0123; // Восьмеричное число<br>
                                <?= TAB1 ?>$a = 0o123; // Восьмеричное число (начиная с PHP 8.1.0)<br>
                                <?= TAB1 ?>$a = 0x1A; // Шестнадцатеричное число<br>
                                <?= TAB1 ?>$a = 0b11111111; // Двоичное число<br>
                                <?= TAB1 ?>$a = 1_234_567; // Десятичное число (с PHP 7.4.0)
                            </p>
                        </div>
                        <p>Если PHP обнаружил, что число превышает размер типа int, язык будет интерпретировать число как float.<br>Пример записи чисел с плавающей точкой</p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 2.9. Пример записи чисел с плавающей точкой</strong><br>
                                &lt;?php<br>
                                <?= TAB1 ?>$a = 1.234;<br>
                                <?= TAB1 ?>$b = 1.2e3;<br>
                                <?= TAB1 ?>$c = 7E-10;<br>
                                <?= TAB1 ?>$d = 1_234.567; // Начиная с PHP 7.4.0<br>
                                ?&gt;
                            </p>
                        </div>
                        <p><strong>Логический тип (bool) </strong>принимает только два значения: true или false. Обе константы регистронезависимы.</p>
                        <p><strong>Объекты (object) </strong>являются экземплярами классов и позволяют использовать принципы объектно-ориентированного программирования, инкапсулируя данные и методы для работы с ними в единую структуру.</p>
                        <p>Специальный <strong>тип null </strong>используется для обозначения переменных без значения. </p>
                        <p><strong>Ресурсы (resource) —  </strong>особый тип, используемый для хранения ссылок на внешние ресурсы, такие как файловые дескрипторы или соединения с базами данных.</p>

                        <h4>2.2.3. Операторы: арифметические, логические, сравнения.</h4>
                        <p style="margin-bottom: 0.4em"><strong>Арифметические операторы</strong></p>
                        <p>Используются для математических вычислений. Они работают с числовыми значениями и включают в себя стандартные операции: сложение (+), вычитание (-), умножение (*), деление (/), остаток от деления (%) и возведение в степень (**).</p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 2.10. Арифметические операторы</strong><br>
                                <?= TAB1 ?>$a = 10 + 5;  // 15<br>
                                <?= TAB1 ?>$b = 20 - 8;  // 12<br>
                                <?= TAB1 ?>$c = 6 * 3;   // 18<br>
                                <?= TAB1 ?>$d = 15 / 3;  // 5<br>
                                <?= TAB1 ?>$e = 10 % 3;  // 1 (остаток от деления)<br>
                                <?= TAB1 ?>$f = 2 ** 4;  // 16 (2 в степени 4)
                            </p>
                        </div>
                         <p style="margin-bottom: 0.4em"><strong>Операторы сравнения</strong></p>
                        <p>Позволяют сравнивать значения между собой и возвращают булево значение (true или false). К ним относятся: равенство (==), строгое равенство (===), неравенство (!= или <>), строгое неравенство (!==), а также операторы больше (>), меньше (<), больше или равно (>=), меньше или равно (<=).</p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 2.11. Операторы сравнения</strong><br>
                                <?= TAB1 ?>5 == "5";     // true (равенство, учитывает значение)<br>
                                <?= TAB1 ?>5 === "5";    // false (строгое равенство, учитывает значение и тип данных)<br>
                                <?= TAB1 ?>10 != 7;      // true<br>
                                <?= TAB1 ?>10 !== "10";  // true<br>
                                <?= TAB1 ?>15 > 10;      // true<br>
                                <?= TAB1 ?>20 < 25;      // true<br>
                                <?= TAB1 ?>30 >= 30;     // true<br>
                                <?= TAB1 ?>40 <= 35;     // false
                            </p>
                        </div>
                        <p style="margin-bottom: 0.4em"><strong>Логические операторы</strong></p>
                        <p>Применяются для комбинирования условий: И (&& или and), ИЛИ (|| или or), НЕ (!), а также исключающее ИЛИ (xor). Они часто используются в условных конструкциях:</p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 2.12. Логические операторы</strong><br>
                                <?= TAB1 ?>true && false;   // false<br>
                                <?= TAB1 ?>true || false;   // true<br>
                                <?= TAB1 ?>!true;           // false<br>
                                <?= TAB1 ?>true xor false;  // true<br>
                                <?= TAB1 ?>false xor false; // false
                            </p>
                        </div>
                        <p>При работе с логическими операторами важно помнить о приоритете выполнения. Оператор ! (НЕ) имеет наивысший приоритет, затем идет && (И), и только потом || (ИЛИ). Для явного указания порядка выполнения можно использовать скобки:</p>
                        <p style="margin-bottom: 0.4em"><strong>Тернарный оператор (?:)</strong></p>
                        <p>Представляет собой сокращенную форму записи условного выражения. Он имеет следующий синтаксис: условие ? значение_если_истина : значение_если_ложь. </p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 2.13. Тернарный оператор</strong><br>
                                <?= TAB1 ?>$age = 20;<br>
                                <?= TAB1 ?>$status = ($age >= 18) ? "Взрослый" : "Ребёнок";
                            </p>
                        </div>
                        <h4>2.2.4 Управляющие конструкции: условия, циклы</h4>
                        <p style="margin-bottom: 0.4em"><strong>Условные конструкции</strong></p>
                        <p style="margin-bottom: 0.4em"><strong>IF-ELSE-ELSEIF</strong></p>
                        <p>Синтаксис: <br> if (выражение)<br><?= TAB1 ?>инструкция<br>Если PHP вычислит выражение как true, он выполнит инструкцию, а если вычислит выражение как false — проигнорирует.</p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 2.14. Условные конструкции</strong><br>
                                &lt;?php<br>
                                <?= TAB1 ?>if ($a > $b) {<br>
                                <?= TAB2 ?>echo "a больше b";<br>
                                <?= TAB1 ?>} elseif ($a == $b) {<br>
                                <?= TAB2 ?>echo "a равно b";<br>
                                <?= TAB1 ?>} else {<br>
                                <?= TAB2 ?>echo "a меньше b";<br>
                                <?= TAB1 ?>}<br>
                                ?>
                            </p>
                        </div>
                        <p style="margin-bottom: 0.4em"><strong>SWITCH-CASE</strong></p>
                        <p>Инструкция switch полезна, когда необходимо сравнить одну переменную или выражение с множеством возможных значений. Является альтернативой длинным цепочкам if-elseif-else. </p>
                        <p>Управление переходит к случаю по умолчанию (default) при несовпадении значения выражения switch со значениями выражений других случаев</p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em">
                                <strong>Листинг 2.15. Пример использования SWITCH-CASE</strong><br>
                                &lt;?php<br>
                                <?= TAB1 ?>$operation = '+';<br>
                                <?= TAB1 ?>$a = 5;<br>
                                <?= TAB1 ?>$b = 3;<br>
                                <br>
                                <?= TAB1 ?>switch ($operation) {<br>
                                <?= TAB2 ?>case '+':<br>
                                <?= TAB2 ?><?= TAB1 ?>echo $a + $b;<br>
                                <?= TAB2 ?><?= TAB1 ?>break;<br>
                                <?= TAB2 ?>case '-':<br>
                                <?= TAB2 ?><?= TAB1 ?>echo $a - $b;<br>
                                <?= TAB2 ?><?= TAB1 ?>break;<br>
                                <?= TAB2 ?>case '*':<br>
                                <?= TAB2 ?><?= TAB1 ?>echo $a * $b;<br>
                                <?= TAB2 ?><?= TAB1 ?>break;<br>
                                <?= TAB2 ?>case '/':<br>
                                <?= TAB2 ?><?= TAB1 ?>echo $b != 0 ? $a / $b : "На ноль делить нельзя";<br>
                                <?= TAB2 ?><?= TAB1 ?>break;<br>
                                <?= TAB2 ?>default:<br>
                                <?= TAB2 ?><?= TAB1 ?>echo "Неизвестная операция";<br>
                                <?= TAB1 ?>}<br>
                                ?>
                            </p>
                        </div>
                        <p>В PHP существует четыре основных типа циклов: <u> while</u>, <u>do-while</u>, <u>for</u> и <u>foreach</u></p>
                        <p style="margin-bottom: 0.4em"><strong>WHILE</strong></p>
                        <p>Особенностью этого цикла является проверка условия перед каждой итерацией, что означает возможность нулевого количества выполнений, если условие изначально ложно. </p>
                        <p>Управление переходит к случаю по умолчанию (default) при несовпадении значения выражения switch со значениями выражений других случаев</p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em">
                                <strong>Листинг 2.16. Синтаксис WHILE</strong><br>
                                <?= TAB1 ?>while (условие) {<br>
                                <?= TAB2 ?>// Тело цикла<br>
                                <?= TAB1 ?>}
                            </p>
                        </div>
                        <p style="margin-bottom: 0.4em"><strong>DO-WHILE</strong></p>
                        <p>Модификацией while выступает цикл do-while, который отличается тем, что проверяет условие после выполнения тела цикла. Эта особенность гарантирует как минимум однократное выполнение блока кода, даже если условие изначально не выполняется. </p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em">
                                <strong>Листинг 2.17. Синтаксис DO-WHILE</strong><br>
                                <?= TAB1 ?>do {<br>
                                <?= TAB2 ?>// Тело цикла<br>
                                <?= TAB1 ?>} while (условие);
                            </p>
                        </div>
                        <p style="margin-bottom: 0.4em"><strong>FOR</strong></p>
                        <p>Для случаев, когда количество итераций известно заранее или может быть вычислено перед началом цикла, оптимальным выбором становится конструкция for. Этот цикл объединяет в одной строке инициализацию счетчика, условие продолжения и оператор изменения.</p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em">
                                <strong>Листинг 2.18. Синтаксис FOR</strong><br>
                                <?= TAB1 ?>for (инициализация; условие; изменение) {<br>
                                <?= TAB2 ?>// Тело цикла<br>
                                <?= TAB1 ?>} while (условие);
                            </p>
                        </div>
                        <p><u>Инициализация</u> —  выполняется один раз перед началом цикла<br><u>Условие</u> —  проверяется перед каждой итерацией<br><u>Изменение</u> —  выполняется после каждой итерации</p>
                        <p style="margin-bottom: 0.4em"><strong>FOREACH</strong></p>
                        <p>Простой способ перебора массивов. Конструкция foreach работает только с массивами и объектами, и будет выдавать ошибку при попытке использовать её с переменными других типов данных или неинициализированными переменными. Foreach существует в двух вариантах — простом, когда доступно только значение элемента, и расширенном, с одновременным получением ключа и значения.</p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em">
                                <strong>Листинг 2.19. Синтаксис FOREACH</strong><br>
                                <?= TAB1 ?>foreach ($массив as $значение) {<br>
                                <?= TAB2 ?>// Тело цикла<br>
                                <?= TAB1 ?>}<br>
                                <?= TAB1 ?>foreach ($массив as $ключ => $значение) {<br>
                                <?= TAB2 ?>// Тело цикла<br>
                                <?= TAB1 ?>}
                            </p>
                        </div>
                        <p style="margin-bottom: 0.4em"><strong>Break и continue</strong></p>
                        <p>Важной особенностью работы с циклами в PHP являются операторы управления выполнением. Break позволяет досрочно прервать выполнение цикла, а continue, напротив, переводит выполнение к следующей итерации, пропуская часть кода в текущем проходе. </p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 2.20. Примеры использования break и continue</strong><br>
                                &lt;?php<br>
                                <?= TAB1 ?>for ($i = 0; $i &lt; 10; $i++) {<br>
                                <?= TAB2 ?>if ($i == 5) {<br>
                                <?= TAB2 ?><?= TAB1 ?>break; // Выход из цикла при $i = 5<br>
                                <?= TAB2 ?>}<br>
                                <?= TAB2 ?>echo $i . " ";<br>
                                <?= TAB1 ?>} // Результат: 0 1 2 3 4<br>
                                ?><br>
                                <br>
                                &lt;?php<br>
                                <?= TAB1 ?>for ($i = 0; $i &lt; 10; $i++) {<br>
                                <?= TAB2 ?>if ($i % 2 == 0) {<br>
                                <?= TAB2 ?><?= TAB1 ?>continue; // Пропускаем четные числа<br>
                                <?= TAB2 ?>}<br>
                                <?= TAB2 ?>echo $i . " ";<br>
                                <?= TAB1 ?>} // Результат: 1 3 5 7 9<br>
                                ?>
                            </p>
                        </div>
                    <?php endif; ?>
                    </div>

                    <?php if (isTeacher()): ?>
                    <form method="post" action="/toggle-topic.php" class="section-edit-form" id="sef-basic-syntax-php" data-key="basic-syntax-php" style="display:none">
                        <input type="hidden" name="action" value="save_section">
                        <input type="hidden" name="course" value="php">
                        <input type="hidden" name="topic_key" value="basic-syntax-php">
                        <input type="hidden" name="back" value="/php-course.php#basic-syntax-php">
                        <input type="text" name="title" class="section-title-input" value="<?= htmlspecialchars($__basic_syntax_phpTitle) ?>" placeholder="Заголовок темы">
                        <div id="editor-basic-syntax-php"></div>
                        <textarea name="content" style="display:none"></textarea>
                        <div style="margin-top:8px; display:flex; gap:8px">
                            <button type="submit" class="btn btn-primary btn-small">Сохранить</button>
                            <button type="button" class="btn btn-small" onclick="toggleSectionEdit('php','basic-syntax-php')">Отмена</button>
                        </div>
                    </form>
                    <?php endif; ?>

                <?= topicButton('php', 'basic-syntax-php', 'work-with-data') ?>
                </article>

                <!-- 2.3 Работа с данными -->
                <article id="work-with-data" class="lesson">
                    <?php $__work_with_dataTitle = getCourseSectionTitle('php', 'work-with-data') ?? '2.3. Работа с данными'; ?>
                    <h3><?= htmlspecialchars($__work_with_dataTitle) ?><?php if (isTeacher()): ?> <button class="edit-section-btn" onclick="toggleSectionEdit('php','work-with-data')">✎</button><?php endif; ?></h3>
                    <div class="text-content" id="sc-work-with-data">
                    <?php $__sc = getCourseSection('php', 'work-with-data'); if ($__sc !== null): echo $__sc; else: ?>
                        <h4>2.3.1. Строки: конкатенация, интерполяция, функции. </h4>
                        <p>Важно учесть, что агрегатные функции, за исключением COUNT(*), не учитывают значения NULL.</p>
                        <p style="margin-bottom: 0.4em"><strong>Интерполяция, конкатенация</strong></p>
                        <p>Строки являются одним из фундаментальных типов данных в PHP. Строки —  это последовательности символов, используемые для хранения и обработки текстовой информации. В PHP строки могут быть объявлены с использованием как одинарных (' '), так и двойных кавычек (" "), но между этими способами есть различия. Строки являются одним из фундаментальных типов данных в PHP. Строки —  это последовательности символов, используемые для хранения и обработки текстовой информации. В PHP строки могут быть объявлены с использованием как одинарных (' '), так и двойных кавычек (" "), но между этими способами есть различия. </p>
                        <p>Строки в двойных кавычках поддерживают интерполяцию переменных, что позволяет непосредственно встраивать значения переменных в текстовую строку без необходимости конкатенации, тогда как строки в одинарных кавычках трактуются буквально, и все символы внутри них (за исключением экранированных кавычек и обратного слеша) выводятся как есть. Например, строка "Привет, $name!" при значении переменной $name = "Иван" будет автоматически преобразована в "Привет, Иван!", в то время как строка 'Привет, $name!' останется без изменений.</p>
                        <p>Конкатенация строк, то есть их объединение, выполняется с помощью оператора точка (.), который позволяет создавать сложные текстовые конструкции из отдельных фрагментов.</p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 2.21. Примеры конкатенации строк</strong><br>
                                <?= TAB1 ?>$name = "Анна";<br>
                                <?= TAB1 ?>$age = 25;<br>
                                <?= TAB1 ?>$greeting = "Привет, " . $name . "!"; // "Привет, Анна!"<br>
                                <?= TAB1 ?>$info = "Имя: " . $name . ", возраст: " . $age; // "Имя: Анна, возраст: 25"
                            </p>
                        </div>
                        <p style="margin-bottom: 1.5em; line-height: 2;">
                            <strong>Функции для работы со строками:</strong><br>
                            <?= TAB1 ?><strong>strlen()</strong> - возвращает длину строки в байтах.<br>
                            <?= TAB1 ?><strong>substr()</strong> - извлекает часть строки.<br>
                            <?= TAB1 ?><strong>strpos()</strong> - находит позицию первого вхождения подстроки.<br>
                            <?= TAB1 ?><strong>str_replace()</strong> – заменяет текст.<br>
                            <?= TAB1 ?>Функции изменения регистра: <strong>strtolower()</strong>, <strong>strtoupper()</strong>, <strong>ucfirst()</strong>, <strong>ucwords()</strong><br>
                            <?= TAB1 ?><strong>trim()</strong> - удаляет пробелы и спецсимволы с обоих концов строки.<br>
                            <?= TAB1 ?><strong>explode()</strong> - разбивает строку по разделителю на массив.<br>
                            <?= TAB1 ?><strong>implode()</strong> - соединяет массив в строку.<br>
                            <?= TAB1 ?><strong>strcmp()</strong> - сравнивает строки с учетом регистра.
                        </p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 2.22. Примеры использования различных функций со строками</strong><br>
                                <br>
                                <?= TAB1 ?>$text = "Программирование";<br>
                                <?= TAB1 ?>echo substr($text, 0, 6); 	// "Програ"<br>
                                <br>
                                <?= TAB1 ?>$text = "Привет";<br>
                                <?= TAB1 ?>echo strlen($text);  // 6<br>
                                <br>
                                <?= TAB1 ?>$text = "Я изучаю PHP";<br>
                                <?= TAB1 ?>$pos = strpos($text, "PHP"); 	// 9<br>
                                <br>
                                <?= TAB1 ?>$text = "Я люблю Java";<br>
                                <?= TAB1 ?>echo str_replace("Java", "PHP", $text);   // "Я люблю PHP"<br>
                                <br>
                                <?= TAB1 ?>$text = "Test String";<br>
                                <?= TAB1 ?>echo strtolower($text);	 // "test string"<br>
                                <?= TAB1 ?>echo strtoupper($text);	 // "TEST STRING"<br>
                                <?= TAB1 ?>echo ucfirst($text);	 // "Test string"<br>
                                <?= TAB1 ?>echo ucwords($text);	 // "Test String"<br>
                                <br>
                                <?= TAB1 ?>$text = "  текст  ";<br>
                                <?= TAB1 ?>echo trim($text); // "текст"<br>
                                <br>
                                <?= TAB1 ?>$text = "яблоко,груша,банан";<br>
                                <?= TAB1 ?>$fruits = explode(",", $text);	// ["яблоко", "груша", "банан"]<br>
                                <?= TAB1 ?>echo implode("; ", $fruits); // "яблоко; груша; банан"<br>
                                <br>
                                <?= TAB1 ?>if (strcmp($str1, $str2) === 0) {<br>
                                <?= TAB2 ?>echo "Строки идентичны";<br>
                                <?= TAB1 ?>}<br>
                                <br>
                                <?= TAB1 ?>$text = "Пример строки";<br>
                                <?= TAB1 ?>echo str_contains($text, "строка"); // true<br>
                                <?= TAB1 ?>echo str_starts_with($text, "Пример"); // true<br>
                                <?= TAB1 ?>echo str_ends_with($text, "строки"); // true<br>
                                <?= TAB1 ?>echo str_repeat("-", 10); // "----------"<br>
                                <?= TAB1 ?>echo str_shuffle("abcdef"); // например, "dbfaec"
                            </p>
                        </div>

                        <h4>2.3.2. Массивы: индексированные, ассоциативные и многомерные. Основные функции. </h4>
                        <p><strong>Массив в PHP</strong> — упорядоченная структура данных, которая связывает значения и ключи. Этот тип данных оптимизирован для разных целей, поэтому с ним работают как с массивом, списком (вектором), хеш-таблицей (реализацией карты), словарём, коллекцией, стеком, очередью и, возможно, чем-то ещё. Поскольку значениями массива бывают другие массивы, также доступны деревья и многомерные массивы.</p>
                        <p>Массив создают языковой конструкцией array(). В качестве аргументов она принимает любое количество разделённых запятыми пар ключ => значение.</p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 2.23. Синтаксис массивов</strong><br>
                                <br>
                                <?= TAB1 ?>array(<br>
                                <?= TAB2 ?>key1  => value,<br>
                                <?= TAB2 ?>key2 => value2,<br>
                                <?= TAB2 ?>key3 => value3,<br>
                                <?= TAB2 ?>...<br>
                                <?= TAB1 ?>);<br>
                                <br>
                                <?= TAB1 ?>или<br>
                                <br>
                                <?= TAB1 ?>$arr = [<br>
                                <?= TAB2 ?>key1 => value1,<br>
                                <?= TAB2 ?>key2 => value2,<br>
                                <?= TAB2 ?>key3 => value3<br>
                                <?= TAB1 ?>];
                            </p>
                        </div>
                        <p>Массивы бывают нескольких типов:</p>
                        <p><strong>1. Индексированные массивы.</strong> Используют числовые индексы, начинающиеся с 0:</p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 2.24. Два варианта создания индексированных массивов</strong><br>
                                <?= TAB1 ?>$colors = ["красный", "зеленый", "синий"];<br>
                                <?= TAB1 ?>или<br>
                                <?= TAB1 ?>$numbers = array(10, 20, 30);
                            </p>
                        </div>

                        <p><strong>2. Ассоциативные массивы.</strong> Используют строковые ключи:</p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 2.25. Создание ассоциативного массива</strong><br>
                                <?= TAB1 ?>$user = [<br>
                                <?= TAB2 ?>"name" => "Анна",<br>
                                <?= TAB2 ?>"age" => 25,<br>
                                <?= TAB2 ?>"is_admin" => true<br>
                                <?= TAB1 ?>];
                            </p>
                        </div>

                        <p><strong>3. Многомерные массивы.</strong> Содержат другие массивы в качестве элементов:</p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 2.26. Создание многомерного массива</strong><br>
                                <?= TAB1 ?>$employees = [<br>
                                <?= TAB2 ?>["id" => 1, "name" => "Иван"],<br>
                                <?= TAB2 ?>["id" => 2, "name" => "Мария"]<br>
                                <?= TAB1 ?>];
                            </p>
                        </div>

                        <p style="margin-bottom: 1.5em; line-height: 2;">
                            <strong>Функции для работы с массивами</strong><br>
                            <?= TAB1 ?><strong>array_push()</strong> – Добавляет элементы в конец:<br>
                            <?= TAB1 ?><strong>array_pop()</strong> – Удаляет последний элемент:<br>
                            <?= TAB1 ?><strong>in_array()</strong> – Проверяет наличие значения:<br>
                            <?= TAB1 ?><strong>array_search()</strong> – Ищет значение и возвращает ключ:<br>
                            <?= TAB1 ?><strong>sort()</strong> – Сортирует по значениям:<br>
                            <?= TAB1 ?><strong>array_merge()</strong> – Сливает массивы:<br>
                            <?= TAB1 ?><strong>implode()</strong> – Соединяет в строку:<br>
                            <?= TAB1 ?><strong>array_keys()</strong> – Получает все ключи:<br>
                            <?= TAB1 ?><strong>array_column()</strong> – Извлекает данные из многомерного массива.
                        </p>

                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 2.27. Примеры использования различных функций с массивами</strong><br>
                                <br>
                                <?= TAB1 ?>$arr = [1, 2];<br>
                                <?= TAB1 ?>array_push($arr, 3);<br>
                                <?= TAB1 ?>print_r($arr); 	// [1, 2, 3]<br>
                                <br>
                                <?= TAB1 ?>$last = array_pop($arr);<br>
                                <?= TAB1 ?>echo $last; 		// 3<br>
                                <br>
                                <?= TAB1 ?>if (in_array("яблоко", ["апельсин", "яблоко"])) {<br>
                                <?= TAB2 ?>echo "Найдено!";	 // Выведет это<br>
                                <?= TAB1 ?>}<br>
                                <br>
                                <?= TAB1 ?>$key = array_search("зеленый", ["красный", "зеленый"]);<br>
                                <?= TAB1 ?>echo $key; 		// 1<br>
                                <br>
                                <?= TAB1 ?>$nums = [3, 1, 2];<br>
                                <?= TAB1 ?>sort($nums);<br>
                                <?= TAB1 ?>print_r($nums); // [1, 2, 3]<br>
                                <br>
                                <?= TAB1 ?>$merged = array_merge([1, 2], [3, 4]);<br>
                                <?= TAB1 ?>print_r($merged); // [1, 2, 3, 4]<br>
                                <br>
                                <?= TAB1 ?>$str = implode(", ", ["PHP", "JS", "Python"]);<br>
                                <?= TAB1 ?>echo $str; // "PHP, JS, Python"<br>
                                <br>
                                <?= TAB1 ?>$keys = array_keys(["name" => "Иван", "age" => 30]);<br>
                                <?= TAB1 ?>print_r($keys); // ["name", "age"]<br>
                                <br>
                                <?= TAB1 ?>$users = [<br>
                                <?= TAB2 ?>["id" => 1, "name" => "Анна"],<br>
                                <?= TAB2 ?>["id" => 2, "name" => "Петр"]<br>
                                <?= TAB1 ?>];<br>
                                <?= TAB1 ?>$names = array_column($users, "name");<br>
                                <?= TAB1 ?>print_r($names); // ["Анна", "Петр"]
                            </p>
                        </div>
                    <?php endif; ?>
                    </div>

                    <?php if (isTeacher()): ?>
                    <form method="post" action="/toggle-topic.php" class="section-edit-form" id="sef-work-with-data" data-key="work-with-data" style="display:none">
                        <input type="hidden" name="action" value="save_section">
                        <input type="hidden" name="course" value="php">
                        <input type="hidden" name="topic_key" value="work-with-data">
                        <input type="hidden" name="back" value="/php-course.php#work-with-data">
                        <input type="text" name="title" class="section-title-input" value="<?= htmlspecialchars($__work_with_dataTitle) ?>" placeholder="Заголовок темы">
                        <div id="editor-work-with-data"></div>
                        <textarea name="content" style="display:none"></textarea>
                        <div style="margin-top:8px; display:flex; gap:8px">
                            <button type="submit" class="btn btn-primary btn-small">Сохранить</button>
                            <button type="button" class="btn btn-small" onclick="toggleSectionEdit('php','work-with-data')">Отмена</button>
                        </div>
                    </form>
                    <?php endif; ?>

                <?= topicButton('php', 'work-with-data', 'functions-and-OOP') ?>
                </article>

                <!-- 2.4 Функции и ООП -->
                <article id="functions-and-OOP" class="lesson">
                    <?php $__functions_and_OOPTitle = getCourseSectionTitle('php', 'functions-and-OOP') ?? '2.4. Функции и ООП'; ?>
                    <h3><?= htmlspecialchars($__functions_and_OOPTitle) ?><?php if (isTeacher()): ?> <button class="edit-section-btn" onclick="toggleSectionEdit('php','functions-and-OOP')">✎</button><?php endif; ?></h3>
                    <div class="text-content" id="sc-functions-and-OOP">
                    <?php $__sc = getCourseSection('php', 'functions-and-OOP'); if ($__sc !== null): echo $__sc; else: ?>
                        <h4>2.4.1. Функции. Создание функций. Параметры и возврат значений. Анонимные и стрелочные функции.</h4>
                        
                        <p>Функции — это именованные блоки кода, которые могут быть вызваны многократно из различных частей кода. Основная цель функций — устранение дублирования кода.</p>
                        
                        <p>Функции определяют ключевым словом function, за которым через пробел идёт название функции и круглые скобки. Круглые скобки оставляют пустыми или определяют в скобках список параметров; параметры разделяют символом ,. За круглыми скобками идёт пара фигурных скобок — тело функции. Приведём пример определения функции.</p>
                        
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 2.28. Пример простой функции</strong><br>
                                <?= TAB1 ?>function foo($arg_1, $arg_2, /* ..., */ $arg_n)<br>
                                <?= TAB1 ?>{<br>
                                <?= TAB2 ?>echo "Пример функции.\n";<br>
                                <?= TAB2 ?>return $retval;<br>
                                <?= TAB1 ?>}
                            </p>
                        </div>
                        
                        <p style="margin-bottom: 0.4em; margin-top: 1.2em;"><strong>Анонимные функции</strong></p>
                        
                        <p>Анонимные функции (или замыкания) — это функции без имени, которые можно сохранять в переменных или передавать в другие функции.</p>
                        
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 2.29. Пример анонимной функции</strong><br>
                                <?= TAB1 ?>$greet = function($name) {<br>
                                <?= TAB2 ?>echo "Привет, $name!";<br>
                                <?= TAB1 ?>};<br>
                                <?= TAB1 ?>$greet("Мария");
                            </p>
                        </div>
                        <p>Замыкания могут "запоминать" переменные из внешней области видимости с помощью ключевого слова use.</p> 
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 2.30. Функция-счётчик. Увеличивает значение при каждом вызове</strong><br>
                                <?= TAB1 ?>function createCounter() {<br>
                                <?= TAB2 ?>$count = 0;<br>
                                <?= TAB2 ?>return function() use (&$count) {<br>
                                <?= TAB2 ?><?= TAB1 ?>$count++;<br>
                                <?= TAB2 ?><?= TAB1 ?>return $count;<br>
                                <?= TAB2 ?>};<br>
                                <?= TAB1 ?>}<br>
                                <br>
                                <?= TAB1 ?>$counter = createCounter();<br>
                                <?= TAB1 ?>echo $counter(); // 1<br>
                                <?= TAB1 ?>echo $counter(); // 2
                            </p>
                        </div>
                        <p>Здесь $count сохраняется между вызовами благодаря передаче по ссылке (&).</p>
                        <p style="margin-bottom: 0.4em; margin-top: 1.2em;"><strong>Стрелочные функции</strong></p>
                        <p>Стрелочные функции — сокращенный синтаксис для простых анонимных функций. Они работают так же, как анонимные функции, за исключением того, что доступ к переменным родительской области стрелочные функции получают автоматически. [7]</p>
                        
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 2.31. Пример стрелочной функции</strong><br>
                                <?= TAB1 ?>$numbers = [1, 2, 3];<br>
                                <?= TAB1 ?>$doubled = array_map(fn($n) => $n * 2, $numbers);<br>
                                <?= TAB1 ?>// Результат: [2, 4, 6]
                            </p>
                        </div>

                        <h4>2.4.2 Классы и объекты. Свойства, методы, конструктор.</h4>
                        <p><strong>Классы</strong></p>
                        <p>Ключевыми понятиями ООП являются "класс" и "объект". Описанием объекта является класс, а объект представляет экземпляр этого класса. Можно провести следующую аналогию: например, у каждого человека есть имя, определенный возраст, вес, какие-то другие параметры. То есть некоторый шаблон, который содержит набор параметров человека - этот шаблон можно назвать классом. А реально же существующий человек с конкретным именем, возрастом, весом и т.д. является объектом или экземпляром этого класса.</p>
                        <p><strong>Свойства класса</strong> — это его внутренние переменные, которые хранят состояние объекта. В PHP свойства могут быть трёх видов: публичные (public), защищённые (protected) и приватные (private). Публичные свойства доступны отовсюду — как из методов класса, так и из внешнего кода. Защищённые свойства видны только внутри класса и его потомков, а приватные — исключительно внутри того класса, где они объявлены. Такое разделение доступа реализует принцип инкапсуляции — одного из ключевых понятий ООП, которое позволяет скрывать внутреннюю реализацию объекта и предоставлять строго определённый интерфейс для работы с ним.</p>
                        <p><strong>Методы класса</strong> — это функции, определённые внутри класса, которые описывают его поведение. Методы могут работать со свойствами своего класса, выполнять вычисления, взаимодействовать с другими объектами. Особое место среди методов занимает <strong>конструктор</strong> — специальный метод с именем __construct(), который автоматически вызывается при создании нового объекта. Конструктор чаще всего используется для инициализации свойств объекта, то есть для задания их начальных значений. Например, при создании объекта "Книга" в конструкторе можно сразу установить её название, автора и год издания, что гарантирует, что объект никогда не окажется в неопределённом состоянии.</p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 2.32. Пример объявления класса</strong><br>
                                <?= TAB1 ?>class User {<br>
                                <?= TAB2 ?>// Свойства<br>
                                <?= TAB2 ?>public $name;<br>
                                <?= TAB2 ?>private $email;<br>
                                <br>
                                <?= TAB2 ?>// Конструктор<br>
                                <?= TAB2 ?>public function __construct($name, $email) {<br>
                                <?= TAB2 ?><?= TAB1 ?>$this-&gt;name = $name;<br>
                                <?= TAB2 ?><?= TAB1 ?>$this-&gt;email = $email;<br>
                                <?= TAB2 ?>}<br>
                                <br>
                                <?= TAB2 ?>// Метод<br>
                                <?= TAB2 ?>public function getEmail() {<br>
                                <?= TAB2 ?><?= TAB1 ?>return $this-&gt;email;<br>
                                <?= TAB2 ?>}<br>
                                <?= TAB1 ?>}
                            </p>
                        </div>
                        <p><strong>Объект</strong> — это конкретная реализация класса, созданная с помощью оператора new</p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 2.33. Пример создания объекта класса</strong><br>
                                <?= TAB1 ?>$user1 = new User("Анна", "anna@example.com");<br>
                                <?= TAB1 ?>$user2 = new User("Иван", "ivan@example.com");
                            </p>
                        </div>
                        <p>Здесь $user1 и $user2 — разные объекты с уникальными значениями свойств name и email.</p>
                        <p>PHP включает большое количество готовых классов. Одним из таких классов является DateTime, который позволяет оперировать датой и временем.</p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 2.34. Использование встроенного класса DateTime</strong><br>
                                &lt;?php<br>
                                <?= TAB1 ?>$date = new DateTime();<br>
                                <?= TAB1 ?>echo $date->format('d-m-Y H:i:s'); // 15-04-2025 16:26:34<br>
                                ?>
                            </p>
                        </div>
                        <h4>2.4.3 Наследование, инкапсуляция, полиморфизм. Магические методы.</h4>
                        <p><strong>Наследование</strong> — одно из основных понятий ООП. При помощи механизма наследования вы можете создавать новые типы данных не с нуля, а взяв за основу некоторый уже существующий класс, который в этом случае называют базовым (base class). Получившийся же класс носит имя производного (derived class).</p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 2.35. Пример классов с наследованием</strong><br>
                                <?= TAB1 ?>class Animal {<br>
                                <?= TAB2 ?>protected $name;<br>
                                <br>
                                <?= TAB2 ?>public function __construct($name) {<br>
                                <?= TAB2 ?><?= TAB1 ?>$this-&gt;name = $name;<br>
                                <?= TAB2 ?>}<br>
                                <br>
                                <?= TAB2 ?>public function makeSound() {<br>
                                <?= TAB2 ?><?= TAB1 ?>return "Неизвестный звук";<br>
                                <?= TAB2 ?>}<br>
                                <?= TAB1 ?>}<br>
                                <br>
                                <?= TAB1 ?>class Dog extends Animal {<br>
                                <?= TAB2 ?>public function makeSound() {<br>
                                <?= TAB2 ?><?= TAB1 ?>return "Гав-гав!";<br>
                                <?= TAB2 ?>}<br>
                                <?= TAB1 ?>}<br>
                                <br>
                                <?= TAB1 ?>$dog = new Dog("Бобик");<br>
                                <?= TAB1 ?>echo $dog-&gt;makeSound(); // Выведет: "Гав-гав!"
                            </p>
                        </div>
                        <p>Ключевое слово extends говорит о том, что создаваемый класс Dog является лишь «расширением» класса Animal. То есть класс Dog содержит те же самые свойства и методы, что и Animal, но, помимо них, и еще некоторые дополнительные, «свои».</p>
                        <p><strong>Инкапсуляция</strong> — это принцип сокрытия внутренней реализации класса и предоставления строго определённого интерфейса для взаимодействия с ним.<br>
                        Уровни доступа:<br>
                        public — доступ отовсюду<br>
                        protected — доступ только внутри класса и его потомков<br>
                        private — доступ только внутри класса</p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 2.36. Пример инкапсуляции</strong><br>
                                <?= TAB1 ?>class BankAccount {<br>
                                <?= TAB2 ?>private $balance = 0;<br>
                                <br>
                                <?= TAB2 ?>public function deposit($amount) {<br>
                                <?= TAB2 ?><?= TAB1 ?>if ($amount > 0) {<br>
                                <?= TAB2 ?><?= TAB2 ?>$this-&gt;balance += $amount;<br>
                                <?= TAB2 ?><?= TAB1 ?>}<br>
                                <?= TAB2 ?>}<br>
                                <br>
                                <?= TAB2 ?>public function getBalance() {<br>
                                <?= TAB2 ?><?= TAB1 ?>return $this-&gt;balance;<br>
                                <?= TAB2 ?>}<br>
                                <?= TAB1 ?>}<br>
                                <br>
                                <?= TAB1 ?>$account = new BankAccount();<br>
                                <?= TAB1 ?>$account-&gt;deposit(100);<br>
                                <?= TAB1 ?>echo $account-&gt;getBalance(); // 100<br>
                                <?= TAB1 ?>// $account->balance = 1000; // Ошибка: свойство private
                            </p>
                        </div>
                        <p><strong>Полиморфизм</strong> — это принцип, который позволяет объектам разных классов обрабатывать данные через один и тот же интерфейс. В общих словах, полиморфность — это способность объекта использовать методы не собственного класса, а производного, даже если на момент определения базового класса производный еще не существует.</p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 2.36. Пример полиморфизма</strong><br>
                                <?= TAB1 ?>class Animal {<br>
                                <?= TAB2 ?>public function makeSound(): string {<br>
                                <?= TAB2 ?><?= TAB1 ?>return "Some generic animal sound";<br>
                                <?= TAB2 ?>}<br>
                                <?= TAB1 ?>}<br>
                                <?= TAB1 ?>class Dog extends Animal {<br>
                                <?= TAB2 ?>public function makeSound(): string {<br>
                                <?= TAB2 ?><?= TAB1 ?>return "Гав-гав!";<br>
                                <?= TAB2 ?>}<br>
                                <?= TAB1 ?>}<br>
                                <?= TAB1 ?>class Cat extends Animal {<br>
                                <?= TAB2 ?>public function makeSound(): string {<br>
                                <?= TAB2 ?><?= TAB1 ?>return "Мяу!";<br>
                                <?= TAB2 ?>}<br>
                                <?= TAB1 ?>}<br>
                                <?= TAB1 ?>class Cow extends Animal {<br>
                                <?= TAB2 ?>public function makeSound(): string {<br>
                                <?= TAB2 ?><?= TAB1 ?>return "Мууу!";<br>
                                <?= TAB2 ?>}<br>
                                <?= TAB2 ?>function animalSound(Animal $animal) {<br>
                                <?= TAB2 ?><?= TAB1 ?>echo $animal-&gt;makeSound() . "\n";<br>
                                <?= TAB2 ?>}<br>
                                <?= TAB1 ?>}<br>
                                <br>
                                <?= TAB1 ?>$dog = new Dog();<br>
                                <?= TAB1 ?>$cat = new Cat();<br>
                                <?= TAB1 ?>$cow = new Cow();<br>
                                <br>
                                <?= TAB1 ?>// Демонстрация полиморфизма. Одна и та же функция работает с разными животными<br>
                                <?= TAB1 ?>animalSound($dog); // Гав-гав!<br>
                                <?= TAB1 ?>animalSound($cat); // Мяу!<br>
                                <?= TAB1 ?>animalSound($cow); // Мууу!
                            </p>
                        </div>
                        <p><strong>Магические методы</strong> — методы, которые переопределяют действие PHP по умолчанию, когда над объектом выполняются отдельные действия. Они всегда начинаются с двойного подчеркивания (__).<br>
                        Следующие названия методов считаются магическими: __construct(), __destruct(), __call(), __callStatic(), __get(), __set(), __isset(), __unset(), __sleep(), __wakeup(), __serialize(), __unserialize() __toString(), __invoke(), __set_state(), __clone() и __debugInfo()  [7]<br>
                        Рассмотрим наиболее популярные.</p>
                        <p>1. __construct() — Конструктор. Вызывается при создании объекта</p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 2.37. Магические методы. Конструктор</strong><br>
                                <?= TAB1 ?>class User {<br>
                                <?= TAB2 ?>public function __construct(public string $name) {<br>
                                <?= TAB2 ?><?= TAB1 ?>echo "Создан пользователь: $name";<br>
                                <?= TAB2 ?>}<br>
                                <?= TAB1 ?>}<br>
                                <?= TAB1 ?>$user = new User("Иван"); // Выведет: "Создан пользователь: Иван"
                            </p>
                        </div>
                        <p>2. __destruct() — Деструктор. Вызывается при удалении объекта.</p>
                        <p>3. __get() и __set() — Геттеры и сеттеры для "невидимых" свойств.<br>
                        Вызываются при попытке прочитать (__get) или изменить (__set) несуществующее/недоступное свойство.</p>
                        <p>4. __toString() — Преобразование объекта в строку.<br>
                        Вызывается, когда объект пытаются использовать как строку (например, в echo или при конкатенации).</p>
                        <p>5. __call() и __callStatic() — Перехват вызовов методов.<br>
                        __call() — вызывается при вызове несуществующего метода объекта.<br>
                        __callStatic() — то же, но для статических методов.</p>
                    <?php endif; ?>
                    </div>

                    <?php if (isTeacher()): ?>
                    <form method="post" action="/toggle-topic.php" class="section-edit-form" id="sef-functions-and-OOP" data-key="functions-and-OOP" style="display:none">
                        <input type="hidden" name="action" value="save_section">
                        <input type="hidden" name="course" value="php">
                        <input type="hidden" name="topic_key" value="functions-and-OOP">
                        <input type="hidden" name="back" value="/php-course.php#functions-and-OOP">
                        <input type="text" name="title" class="section-title-input" value="<?= htmlspecialchars($__functions_and_OOPTitle) ?>" placeholder="Заголовок темы">
                        <div id="editor-functions-and-OOP"></div>
                        <textarea name="content" style="display:none"></textarea>
                        <div style="margin-top:8px; display:flex; gap:8px">
                            <button type="submit" class="btn btn-primary btn-small">Сохранить</button>
                            <button type="button" class="btn btn-small" onclick="toggleSectionEdit('php','functions-and-OOP')">Отмена</button>
                        </div>
                    </form>
                    <?php endif; ?>

                <?= topicButton('php', 'functions-and-OOP', 'work-with-files') ?>
                </article>

                <!-- 2.5. Работа с файлами -->
                <article id="work-with-files" class="lesson">
                    <?php $__work_with_filesTitle = getCourseSectionTitle('php', 'work-with-files') ?? '2.5. Работа с файлами'; ?>
                    <h3><?= htmlspecialchars($__work_with_filesTitle) ?><?php if (isTeacher()): ?> <button class="edit-section-btn" onclick="toggleSectionEdit('php','work-with-files')">✎</button><?php endif; ?></h3>
                    <div class="text-content" id="sc-work-with-files">
                    <?php $__sc = getCourseSection('php', 'work-with-files'); if ($__sc !== null): echo $__sc; else: ?>
                        <h4>2.5.1. Файловая система. Основные операции с файлами</h4>
                        
                        <p>Для открытия файлов используется функция fopen(), которая возвращает файловый указатель (ресурс). После завершения работы файл нужно закрыть с помощью fclose(), чтобы освободить ресурсы.</p>
                        
                        <p>Режимы открытия файла:<br>
                        <?= TAB1 ?>r &nbsp;&nbsp;Чтение (курсор в начале файла)<br>
                        <?= TAB1 ?>r+ &nbsp;Чтение и запись (курсор в начале)<br>
                        <?= TAB1 ?>w &nbsp;&nbsp;Запись (перезаписывает файл или создаёт новый)<br>
                        <?= TAB1 ?>w+ &nbsp;Чтение и запись (перезаписывает файл)<br>
                        <?= TAB1 ?>a &nbsp;&nbsp;Добавление в конец файла (курсор в конце)<br>
                        <?= TAB1 ?>a+ &nbsp;Чтение и добавление в конец<br>
                        <?= TAB1 ?>x &nbsp;&nbsp;Эксклюзивное создание (ошибка, если файл существует)<br>
                        <?= TAB1 ?>x+ &nbsp;Чтение и запись (только для нового файла)</p>
                        
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 2.38. Пример использования fopen() и fclose()</strong><br>
                                <?= TAB1 ?>$file = fopen("data.txt", "r"); // Открытие в режиме чтения<br>
                                <?= TAB1 ?>if ($file) {<br>
                                <?= TAB2 ?>// операции с файлом...<br>
                                <?= TAB2 ?>fclose($file); // Обязательно закрываем!<br>
                                <?= TAB1 ?>} else {<br>
                                <?= TAB2 ?>die("Ошибка открытия файла!");<br>
                                <?= TAB1 ?>}
                            </p>
                        </div>
                        
                        <p>Способы чтения данных из файла:<br>
                        <?= TAB1 ?>• fgets() <?= TAB2 ?><?= TAB2 ?><?= TAB1 ?>- Построчное чтение<br>
                        <?= TAB1 ?>• file_get_contents() &nbsp;- Чтение всего файла<br>
                        <?= TAB1 ?>• file() <?= TAB2 ?><?= TAB2 ?><?= TAB1 ?><?= SPACE ?><?= SPACE ?>- Чтение файла в массив строк</p>
                        
                        <p>Запись данных в файл осуществляется с помощью функции fwrite().</p>
                        
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 2.39. Пример записи в файл</strong><br>
                                <?= TAB1 ?>$file = fopen("output.txt", "w");<br>
                                <?= TAB1 ?>fwrite($file, "Привет, мир!\n");<br>
                                <?= TAB1 ?>fwrite($file, "Это тестовая запись.");<br>
                                <?= TAB1 ?>fclose($file);
                            </p>
                        </div>
                        
                        <p>Основные функции для работы с директориями:<br>
                        <?= TAB1 ?>• is_dir() &nbsp;&nbsp;&nbsp;&nbsp;- Проверка на существование директории.<br>
                        <?= TAB1 ?>• mkdir() &nbsp;&nbsp;&nbsp;- Создание директории.<br>
                        <?= TAB1 ?>• rmdir() &nbsp;&nbsp;&nbsp;&nbsp;- Удаление директории (только для пустых папок).<br>
                        <?= TAB1 ?>• scandir() &nbsp;- Получение списка файлов в директории.</p>
                    <?php endif; ?>
                    </div>

                    <?php if (isTeacher()): ?>
                    <form method="post" action="/toggle-topic.php" class="section-edit-form" id="sef-work-with-files" data-key="work-with-files" style="display:none">
                        <input type="hidden" name="action" value="save_section">
                        <input type="hidden" name="course" value="php">
                        <input type="hidden" name="topic_key" value="work-with-files">
                        <input type="hidden" name="back" value="/php-course.php#work-with-files">
                        <input type="text" name="title" class="section-title-input" value="<?= htmlspecialchars($__work_with_filesTitle) ?>" placeholder="Заголовок темы">
                        <div id="editor-work-with-files"></div>
                        <textarea name="content" style="display:none"></textarea>
                        <div style="margin-top:8px; display:flex; gap:8px">
                            <button type="submit" class="btn btn-primary btn-small">Сохранить</button>
                            <button type="button" class="btn btn-small" onclick="toggleSectionEdit('php','work-with-files')">Отмена</button>
                        </div>
                    </form>
                    <?php endif; ?>

                <?= topicButton('php', 'work-with-files', 'php+sql') ?>
                </article>

                <!-- 2.6 Связка PHP + SQL -->
                <article id="php+sql" class="lesson">
                    <?php $__php_sqlTitle = getCourseSectionTitle('php', 'php+sql') ?? '2.6 Связка PHP + SQL'; ?>
                    <h3><?= htmlspecialchars($__php_sqlTitle) ?><?php if (isTeacher()): ?> <button class="edit-section-btn" onclick="toggleSectionEdit('php','php+sql')">✎</button><?php endif; ?></h3>
                    <div class="text-content" id="sc-php+sql">
                    <?php $__sc = getCourseSection('php', 'php+sql'); if ($__sc !== null): echo $__sc; else: ?>
                        <h4>Подключение PHP к Firebird</h4>
                        <p>Для взаимодействия с базами данных в PHP используется расширение PDO (PHP Data Objects). Подключиться к базе данных SQL Firebird можно следующим образом:</p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <?= TAB1 ?>$dbh = new PDO("firebird:dbname=localhost:/path/to/database.fdb;charset=utf8", "user", "password");
                            </p>
                        </div>
                        <p>По умолчанию в PHP модуль pdo_firebird не подключен. Для его подключения необходимо расскоментировать строку extension = pdo_firebird в файле php.ini.</p>
                        <h4>Выполнение запросов</h4>
                        <p>В PDO есть несколько основных методов для выполнения запросов:<br>
                        <ul style="margin-left: 20px;">
                            <li><strong>exec()</strong> — выполняет SQL-запрос без возврата данных (CREATE, INSERT, UPDATE, DELETE);</li>
                            <li><strong>query()</strong> — отправляет запрос и возвращает результат в виде объекта PDOStatement (используется для SELECT);</li>
                            <li><strong>prepare()</strong> + execute() — создает подготовленный запрос с параметрами для безопасного выполнения (защита от SQL-инъекций), затем выполняет его с переданными значениями.</li>
                        </ul>
                        <p>Примеры использования exec():</p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 2.40. Создание таблицы</strong><br>
                                <?= TAB1 ?>$pdo-&gt;exec("CREATE TABLE users (<br>
                                <?= TAB2 ?>id INT PRIMARY KEY,<br>
                                <?= TAB2 ?>username VARCHAR(50) NOT NULL<br>
                                <?= TAB1 ?>)");<br>
                            </p>
                        </div>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 2.41. Вставка данных</strong><br>
                                <?= TAB1 ?>$pdo-&gt;exec("INSERT INTO users (id, username) VALUES (1, 'admin')");
                            </p>
                        </div>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 2.42. Пример использования query()</strong><br>
                                <?= TAB1 ?>$result = $pdo-&gt;query("SELECT * FROM users")-&gt;fetchAll(PDO::FETCH_ASSOC);<br>
                                <?= TAB1 ?>print_r($result);
                            </p>
                        </div>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 2.43. Примеры использования prepare() + execute():</strong><br>
                                <?= TAB1 ?>$stmt = $dbh-&gt;prepare("INSERT INTO notes (title, content) VALUES (?, ?)");<br>
                                <?= TAB1 ?>$stmt-&gt;execute(['Заголовок', 'Текст заметки']);<br>
                                <br>
                                <?= TAB1 ?>$stmt = $dbh-&gt;prepare("SELECT * FROM notes WHERE id = ?");<br>
                                <?= TAB1 ?>$stmt-&gt;execute([$id]);<br>
                                <?= TAB1 ?>$note = $stmt-&gt;fetch();
                            </p>
                        </div>
                        <p><strong>fetchAll()</strong> — Извлекает все строки результата SQL-запроса. Параметр PDO::FETCH_ASSOC указывает, что данные нужно вернуть в виде ассоциативного массива (где ключи - названия столбцов).</p>
                    <?php endif; ?>
                    </div>

                    <?php if (isTeacher()): ?>
                    <form method="post" action="/toggle-topic.php" class="section-edit-form" id="sef-php+sql" data-key="php+sql" style="display:none">
                        <input type="hidden" name="action" value="save_section">
                        <input type="hidden" name="course" value="php">
                        <input type="hidden" name="topic_key" value="php+sql">
                        <input type="hidden" name="back" value="/php-course.php#php+sql">
                        <input type="text" name="title" class="section-title-input" value="<?= htmlspecialchars($__php_sqlTitle) ?>" placeholder="Заголовок темы">
                        <div id="editor-php+sql"></div>
                        <textarea name="content" style="display:none"></textarea>
                        <div style="margin-top:8px; display:flex; gap:8px">
                            <button type="submit" class="btn btn-primary btn-small">Сохранить</button>
                            <button type="button" class="btn btn-small" onclick="toggleSectionEdit('php','php+sql')">Отмена</button>
                        </div>
                    </form>
                    <?php endif; ?>

                <?= topicButton('php', 'php+sql', null) ?>
                </article>

                <!-- Динамические темы из БД (после всех статических) -->
                <?php
                $customSections = getCustomSections('php');
                foreach ($customSections as $cs):
                    $csKey = htmlspecialchars($cs['section_key']);
                    $csTitle = htmlspecialchars($cs['title']);
                ?>
                <article id="<?= $csKey ?>" class="lesson">
                    <h3>
                        <?= $csTitle ?>
                        <?php if (isTeacher()): ?>
                        <button class="edit-section-btn" onclick="toggleSectionEdit('php','<?= $csKey ?>')">✎</button>
                        <form method="post" action="/toggle-topic.php" style="display:inline" onsubmit="return confirm('Удалить тему «<?= $csTitle ?>»?')">
                            <input type="hidden" name="action" value="delete_section">
                            <input type="hidden" name="course" value="php">
                            <input type="hidden" name="topic_key" value="<?= $csKey ?>">
                            <input type="hidden" name="back" value="/php-course.php">
                            <button type="submit" class="delete-section-btn">✕</button>
                        </form>
                        <?php endif; ?>
                    </h3>
                    <div class="text-content" id="sc-<?= $csKey ?>">
                        <?= $cs['content'] ?>
                    </div>
                    <?php if (isTeacher()): ?>
                    <form method="post" action="/toggle-topic.php" class="section-edit-form" id="sef-<?= $csKey ?>" data-key="<?= $csKey ?>" style="display:none">
                        <input type="hidden" name="action" value="save_section">
                        <input type="hidden" name="course" value="php">
                        <input type="hidden" name="topic_key" value="<?= $csKey ?>">
                        <input type="hidden" name="back" value="/php-course.php#<?= $csKey ?>">
                        <input type="text" name="title" class="section-title-input" value="<?= $csTitle ?>" placeholder="Заголовок темы">
                        <div id="editor-<?= $csKey ?>"></div>
                        <textarea name="content" style="display:none"></textarea>
                        <div style="margin-top:8px; display:flex; gap:8px">
                            <button type="submit" class="btn btn-primary btn-small">Сохранить</button>
                            <button type="button" class="btn btn-small" onclick="toggleSectionEdit('php','<?= $csKey ?>')">Отмена</button>
                        </div>
                    </form>
                    <?php endif; ?>
                </article>
                <?php endforeach; ?>

                <!-- Форма добавления новой темы (только для учителей) -->
                <?php if (isTeacher()): ?>
                <button class="add-topic-btn" onclick="toggleAddTopic()">+ Добавить новую тему</button>
                <form method="post" action="/toggle-topic.php" class="add-topic-form" id="add-topic-form" style="display:none">
                    <input type="hidden" name="action" value="add_section">
                    <input type="hidden" name="course" value="php">
                    <input type="hidden" name="back" value="/php-course.php">
                    <label style="display:block; font-weight:600; margin-bottom:4px">Название темы:</label>
                    <input type="text" name="title" placeholder="Например: 2.7. Работа с сессиями" required>
                    <label style="display:block; font-weight:600; margin: 10px 0 4px">Содержание:</label>
                    <div id="editor-new-topic"></div>
                    <textarea name="content" id="new-topic-content" style="display:none"></textarea>
                    <div style="margin-top:12px; display:flex; gap:8px">
                        <button type="submit" class="btn btn-primary">Добавить тему</button>
                        <button type="button" class="btn" onclick="toggleAddTopic()">Отмена</button>
                    </div>
                </form>
                <?php endif; ?>

            </section>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/templates/footer.php'; ?>