<?php
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.7/quill.min.js"></script>
<script>
var editors = {};
var TOOLBAR = [
    [{ header: [2, 3, 4, false] }],
    ['bold', 'italic', 'underline', 'strike'],
    [{ 'color': [] }, { 'background': [] }],
    [{ list: 'ordered' }, { list: 'bullet' }],
    ['code-block', 'link', 'image'],
    ['clean']
];

function toggleSectionEdit(course, key) {
    var form = document.getElementById('sef-' + key);
    var content = document.getElementById('sc-' + key);
    if (!form || !content) return;
    if (form.style.display === 'none' || !form.style.display) {
        if (!editors[key]) {
            var editorEl = document.getElementById('editor-' + key);
            if (!editorEl) return;
            editors[key] = new Quill(editorEl, {
                theme: 'snow',
                modules: { toolbar: TOOLBAR }
            });
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
        newTopicEditor = new Quill(document.getElementById('editor-new-topic'), {
            theme: 'snow',
            modules: { toolbar: TOOLBAR }
        });
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

        <!-- Содержание -->
        <div class="table-of-contents">
            <h2>Содержание курса</h2>
            <nav class="toc-nav">
                <ul>
                    <li><a href="#introduction">Введение</a></li>
                    <li><a href="#start">Пошаговое создание</a>
                        <ul>
                            <li><a href="#web1"><?= htmlspecialchars(getCourseSectionTitle('web', 'web1') ?? '4.1. Введение') ?></a></li>
                            <li><a href="#web2"><?= htmlspecialchars(getCourseSectionTitle('web', 'web2') ?? '4.2. Проектирование базы данных') ?></a></li>
                            <li><a href="#web3"><?= htmlspecialchars(getCourseSectionTitle('web', 'web3') ?? '4.3. Создание проекта') ?></a></li>
                            <li><a href="#web4"><?= htmlspecialchars(getCourseSectionTitle('web', 'web4') ?? '4.4. Создание базы данных') ?></a></li>
                            <li><a href="#web5"><?= htmlspecialchars(getCourseSectionTitle('web', 'web5') ?? '4.5. Подключение к базе данных') ?></a></li>
                            <li><a href="#web6"><?= htmlspecialchars(getCourseSectionTitle('web', 'web6') ?? '4.6. Шаблон страниц и главная страница') ?></a></li>
                            <li><a href="#web7"><?= htmlspecialchars(getCourseSectionTitle('web', 'web7') ?? '4.7. Управление авторами') ?></a></li>
                            <li><a href="#web8"><?= htmlspecialchars(getCourseSectionTitle('web', 'web8') ?? '4.8. Упрощение кода. Страница издательств') ?></a></li>
                            <li><a href="#web9"><?= htmlspecialchars(getCourseSectionTitle('web', 'web9') ?? '4.9. Страница читателей') ?></a></li>
                            <li><a href="#web10"><?= htmlspecialchars(getCourseSectionTitle('web', 'web10') ?? '4.10. Страница книг') ?></a></li>
                            <li><a href="#web11"><?= htmlspecialchars(getCourseSectionTitle('web', 'web11') ?? '4.11. Страница экземпляров') ?></a></li>
                            <li><a href="#web12"><?= htmlspecialchars(getCourseSectionTitle('web', 'web12') ?? '4.12. Страница выдачи книг') ?></a></li>
                            <li><a href="#web13"><?= htmlspecialchars(getCourseSectionTitle('web', 'web13') ?? 'Готовое приложение') ?></a></li>
                            <?php foreach (getCustomSections('web') as $cs): ?>
                            <li><a href="#<?= htmlspecialchars($cs['section_key']) ?>"><?= htmlspecialchars($cs['title']) ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>

        <div class="course-material">

            <section id="introduction" class="chapter">
                <div class="text-content" id="sc-introduction">
                <?php $__sc = getCourseSection('web', 'introduction'); if ($__sc !== null): echo $__sc; else: ?>
                    <p>Добро пожаловать в курс <strong>по созданию веб-приложения</strong>! Этот курс является финальным — в нём мы будем использовать знания, полученные ранее.</p>
                    <p style="line-height: 2.2;">В данном курсе будет создано следующее веб-приложение: 
                    <a href="/library-preview.php" style="display: inline-block; background: rgba(90,150,144,0.18); color: rgb(37, 72, 70); padding: 0px 15px; border-radius: 30px; text-decoration: none; font-weight: 500; 
                    font-size: 0.99rem; letter-spacing: 0.02em; margin: 0 2px; transition: all 0.2s ease; border: 1px solid transparent;" onmouseover="this.style.background='rgba(90,150,144,0.25)';
                     this.style.borderColor='rgba(47,87,85,0.4)';" onmouseout="this.style.background='rgba(90,150,144,0.18)'; this.style.borderColor='transparent';">
                     предпросмотр</a></p>
                <?php endif; ?>
                </div>
                <?php if (isTeacher()): ?>
                <div style="text-align:right; margin-top:4px">
                    <button class="edit-section-btn" onclick="toggleSectionEdit('web','introduction')">✎ Редактировать</button>
                </div>
                <form method="post" action="/toggle-topic.php" class="section-edit-form" id="sef-introduction" data-key="introduction" style="display:none">
                    <input type="hidden" name="action" value="save_section">
                    <input type="hidden" name="course" value="web">
                    <input type="hidden" name="topic_key" value="introduction">
                    <input type="hidden" name="back" value="/web-course.php#introduction">
                    <div id="editor-introduction"></div>
                    <textarea name="content" style="display:none"></textarea>
                    <div style="margin-top:8px; display:flex; gap:8px">
                        <button type="submit" class="btn btn-primary btn-small">Сохранить</button>
                        <button type="button" class="btn btn-small" onclick="toggleSectionEdit('web','introduction')">Отмена</button>
                    </div>
                </form>
                <?php endif; ?>
            </section>

            <section id="start" class="chapter">
                <h2>Пошаговое создание веб-приложения «Библиотека»</h2>

                <!-- 4.1 -->
                <article id="web1" class="lesson">
                    <?php
                        // 1️⃣ Получаем заголовок из БД, если нет – используем значение по умолчанию
                        $web1Title = getCourseSectionTitle('web', 'web1') ?? '4.1. Введение';
                    ?>
                    <h3><?= htmlspecialchars($web1Title) ?><?php if (isTeacher()): ?> <button class="edit-section-btn" onclick="toggleSectionEdit('web','web1')">Редактировать тему</button><?php endif; ?></h3>

                    <div class="text-content" id="sc-web1">
                        <?php $__sc = getCourseSection('web', 'web1'); if ($__sc !== null): echo $__sc; else: ?>
                            <p>В рамках данного курса будет разработано веб-приложение «Библиотека». Оно позволит хранить информацию о книгах, авторах и пользователях библиотеки, а также отслеживать все движения книг.</p>
                        <p>Для реализации будем использовать следующие технологии:</p>
                        <ol style="margin-left: 20px;">
                            <li>OpenServer — программная среда для создания локального сервера.</li>
                            <li>PHP — язык программирования. Входит в состав OpenServer.</li>
                            <li>MySQL — СУБД. Входит в модули OpenServer.</li>
                            <li>phpMyAdmin — инструмент для администрирования базы данных.</li>
                            <li>HTML и CSS — для визуального оформления приложения.</li>
                        </ol>
                        <p>Начнём с проектирования структуры базы данных.</p>
                        <?php endif; ?>
                    </div>

                    <?php if (isTeacher()): ?>
                    <form method="post" action="/toggle-topic.php" class="section-edit-form" id="sef-web1" data-key="web1" style="display:none">
                        <input type="hidden" name="action" value="save_section">
                        <input type="hidden" name="course" value="web">
                        <input type="hidden" name="topic_key" value="web1">
                        <input type="hidden" name="back" value="/web-course.php#web1">
                        <input type="text" name="title" class="section-title-input" value="<?= htmlspecialchars($web1Title) ?>" placeholder="Заголовок темы">
                        <div id="editor-web1"></div>
                        <textarea name="content" style="display:none"></textarea>
                        <div style="margin-top:8px; display:flex; gap:8px">
                            <button type="submit" class="btn btn-primary btn-small">Сохранить</button>
                            <button type="button" class="btn btn-small" onclick="toggleSectionEdit('web','web1')">Отмена</button>
                        </div>
                    </form>
                    <?php endif; ?>

                    <?= topicButton('web', 'web1', 'web2') ?>
                </article>

                <!-- 4.2 -->
                <article id="web2" class="lesson">
                    <?php $__web2Title = getCourseSectionTitle('web', 'web2') ?? '4.2. Проектирование базы данных «Библиотека»'; ?>
                    <h3><?= htmlspecialchars($__web2Title) ?><?php if (isTeacher()): ?> <button class="edit-section-btn" onclick="toggleSectionEdit('web','web2')">Редактировать тему</button><?php endif; ?></h3>

                    <div class="text-content" id="sc-web2">
                    <?php $__sc = getCourseSection('web', 'web2'); if ($__sc !== null): echo $__sc; else: ?>
                        <p>Для хранения данных веб-приложения необходима база данных. В этом разделе мы поэтапно спроектируем её структуру.</p>
                        <p>Наша база должна хранить информацию об авторах, книгах, их физических экземплярах, зарегистрированных читателях и истории выдачи. На первом этапе нужно составить список таблиц и определить поля в каждой из них.</p>
                        <p>Для полноценной работы потребуются следующие таблицы:</p>
                        <ul style="margin-left: 20px;">
                            <li><strong>Authors</strong> (АВТОРЫ) — id, first_name, last_name, birth_date, country</li>
                            <li><strong>Publishers</strong> (ИЗДАТЕЛЬСТВА) — id, name, address</li>
                            <li><strong>Books</strong> (КНИГИ) — id, title, author_id, publisher_id, genre, page_count, age_limit, publication_date</li>
                            <li><strong>Users</strong> (ПОЛЬЗОВАТЕЛИ) — id, first_name, last_name, registration_date, birth_date</li>
                            <li><strong>Book_copies</strong> (ЭКЗЕМПЛЯРЫ КНИГ) — id, book_id, status</li>
                            <li><strong>Borrow_records</strong> (ЗАПИСИ О ВЫДАЧЕ) — id, user_id, book_copy_id, borrow_date, due_date, return_date</li>
                        </ul>

                        <p style="margin-top: 30px;">Разберём содержимое каждой таблицы:</p>
                        <ol>
                            <li>
                                <strong>AUTHORS</strong> — информация об авторах.
                                <ul>
                                    <li><strong>ID</strong> — уникальный числовой идентификатор, первичный ключ. Присваивается автоматически.</li>
                                    <li><strong>FIRST_NAME, LAST_NAME</strong> — обязательные поля для имени и фамилии.</li>
                                    <li><strong>BIRTH_DATE</strong> — дата рождения, тип DATE.</li>
                                    <li><strong>COUNTRY</strong> — страна происхождения.</li>
                                </ul>
                            </li>
                            <li>
                                <strong>PUBLISHERS</strong> — информация об издательствах.
                                <ul>
                                    <li><strong>ID</strong> — первичный ключ.</li>
                                    <li><strong>NAME</strong> — наименование издательства.</li>
                                    <li><strong>ADDRESS</strong> — адрес.</li>
                                </ul>
                            </li>
                            <li>
                                <strong>BOOKS</strong> — описание книги.
                                <ul>
                                    <li><strong>ID</strong> — первичный ключ.</li>
                                    <li><strong>TITLE</strong> — обязательное поле с названием.</li>
                                    <li><strong>AUTHOR_ID</strong> — внешний ключ, ссылается на AUTHORS.</li>
                                    <li><strong>PUBLISHER_ID</strong> — внешний ключ, ссылается на PUBLISHERS.</li>
                                    <li><strong>GENRE</strong> — жанр.</li>
                                    <li><strong>PAGE_COUNT</strong> — количество страниц.</li>
                                    <li><strong>AGE_LIMIT</strong> — возрастное ограничение.</li>
                                    <li><strong>PUBLICATION_DATE</strong> — дата публикации.</li>
                                </ul>
                            </li>
                            <li>
                                <strong>USERS</strong> — зарегистрированные читатели.
                                <ul>
                                    <li><strong>ID</strong> — первичный ключ.</li>
                                    <li><strong>FIRST_NAME, LAST_NAME</strong> — обязательные поля.</li>
                                    <li><strong>REGISTRATION_DATE</strong> — дата регистрации.</li>
                                    <li><strong>BIRTH_DATE</strong> — дата рождения.</li>
                                </ul>
                            </li>
                            <li>
                                <strong>BOOK_COPIES</strong> — физический экземпляр книги.
                                <ul>
                                    <li><strong>ID</strong> — первичный ключ.</li>
                                    <li><strong>BOOK_ID</strong> — внешний ключ, ссылается на BOOKS.</li>
                                    <li><strong>STATUS</strong> — состояние: <em>available</em> — доступен, <em>borrowed</em> — выдан, <em>damaged</em> — повреждён, <em>lost</em> — утерян.</li>
                                </ul>
                            </li>
                            <li>
                                <strong>BORROW_RECORDS</strong> — операции выдачи и возврата.
                                <ul>
                                    <li><strong>ID</strong> — первичный ключ.</li>
                                    <li><strong>USER_ID</strong> — внешний ключ, ссылается на USERS.</li>
                                    <li><strong>BOOK_COPY_ID</strong> — внешний ключ, ссылается на BOOK_COPIES.</li>
                                    <li><strong>BORROW_DATE</strong> — дата выдачи.</li>
                                    <li><strong>DUE_DATE</strong> — срок возврата.</li>
                                    <li><strong>RETURN_DATE</strong> — фактическая дата возврата. NULL, пока книга не возвращена.</li>
                                </ul>
                            </li>
                        </ol>

                        <p style="margin-top: 30px;">Связи между таблицами:</p>
                        <ul style="margin-left: 20px;">
                            <li>Один автор может написать множество книг.</li>
                            <li>Одно издательство может выпустить множество книг.</li>
                            <li>Одна книга может иметь множество физических экземпляров.</li>
                            <li>Один пользователь может иметь множество записей о выдаче.</li>
                            <li>Один экземпляр может фигурировать в нескольких записях о выдаче.</li>
                        </ul>
                        <p style="margin-top: 15px;">В виде схемы:</p>
                        <p>
                            <?= TAB2 ?>Authors (1) —— Books (N)<br>
                            <?= TAB2 ?>Publishers (1) —— Books (N)<br>
                            <?= TAB2 ?>Books (1) —— Book_copies (N)<br>
                            <?= TAB2 ?>Users (1) —— Borrow_records (N)<br>
                            <?= TAB2 ?>Book_copies (1) —— Borrow_records (N)
                        </p>
                        <p style="margin-top: 15px;">С помощью сервиса <a href="https://drawdb.vercel.app/editor" target="_blank" style="color: rgb(47,87,85);">drawdb.vercel.app</a> можно построить ER-диаграмму и экспортировать готовый SQL-код.</p>
                        <img src="/images/library_db_structure.png"
                            style="width: 80%; height: auto; max-width: calc(100% - 40px); margin-left: 40px; margin-bottom: 2em; margin-top: 1em; display: block;">
                    <?php endif; ?>
                    </div>

                    <?php if (isTeacher()): ?>
                    <form method="post" action="/toggle-topic.php" class="section-edit-form" id="sef-web2" data-key="web2" style="display:none">
                        <input type="hidden" name="action" value="save_section">
                        <input type="hidden" name="course" value="web">
                        <input type="hidden" name="topic_key" value="web2">
                        <input type="hidden" name="back" value="/web-course.php#web2">
                        <input type="text" name="title" class="section-title-input" value="<?= htmlspecialchars($__web2Title) ?>" placeholder="Заголовок темы">
                        <div id="editor-web2"></div>
                        <textarea name="content" style="display:none"></textarea>
                        <div style="margin-top:8px; display:flex; gap:8px">
                            <button type="submit" class="btn btn-primary btn-small">Сохранить</button>
                            <button type="button" class="btn btn-small" onclick="toggleSectionEdit('web','web2')">Отмена</button>
                        </div>
                    </form>
                    <?php endif; ?>

                <?= topicButton('web', 'web2', 'web3') ?>
                </article>

                <!-- 4.3 -->
                <article id="web3" class="lesson">
                <?php
                    $web3Title = getCourseSectionTitle('web', 'web3') ?? '4.3. Создание проекта';
                ?>
                <h3><?= htmlspecialchars($web3Title) ?><?php if (isTeacher()): ?> <button class="edit-section-btn" onclick="toggleSectionEdit('web','web3')">Редактировать тему</button><?php endif; ?></h3>

                <div class="text-content" id="sc-web3">
                    <?php $__sc = getCourseSection('web', 'web3'); if ($__sc !== null): echo $__sc; else: ?>
                        <p>База данных спроектирована, теперь можно приступить к созданию самого веб-приложения и практической реализации нашей БД </p>
                        <p>Для начала необходимо установить последнюю версию Open Server Panel 6.0.0 с официального сайта <a href="https://ospanel.io/" target="_blank" style="color: rgb(47,87,85);">ospanel.io</a>. Вместе с OpenServer’ом также получим PHP и MySQL. В новых версия OpenServer phpMyAdmin необходимо устанавливать отдельно и разместить папку с программой в папке установки OpenServer Panel.</p>
                        <p>Перейдем в папку установки OpenServer и в папке home создадим новый проект <code>library.local</code>. Чтобы он отразился в панеле необходимо в данной директории создать еще одну папку – <code>.osp</code> с конфигурационным файлом проекта – <code>project.ini</code>. Запишем в наш файл следующие настройки конфигурации:</p>
                        <div class="content-placeholder">
                            <strong>Листинг 4.3.1. Файл project.ini</strong><br>
                            <?= TAB1 ?>[library2.local]<br>
                            <?= TAB1 ?>php_engine = PHP-8.3
                        </div>
                        <p>Основа проекта готова, приложение запускается, но пока там ничего нет. Определим структуру проекта и создадим необходимые папки и файлы приложения.</p>
                        <div class="content-placeholder">
                            <strong>Листинг 4.3.2. Структура проекта</strong><br>
                            <?= TAB1 ?>library2.local/<br>
                            <?= TAB2 ?>includes/<br>
                            <?= TAB2 ?><?= TAB1 ?>db.php&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;- подключение к базе данных<br>
                            <?= TAB2 ?><?= TAB1 ?>helpers.php&nbsp;&nbsp;&lt;- вспомогательные функции<br>
                            <?= TAB2 ?><?= TAB1 ?>layout.php&nbsp;&nbsp;&nbsp;&lt;- шаблон страниц<br>
                            <?= TAB2 ?>assets/css/<br>
                            <?= TAB2 ?><?= TAB1 ?>style.css&nbsp;&nbsp;&nbsp;&nbsp;&lt;- стили<br>
                            <?= TAB2 ?>index.php&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;- главная страница<br>
                            <?= TAB2 ?>books.php&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;- книги<br>
                            <?= TAB2 ?>authors.php&nbsp;&nbsp;&nbsp;&nbsp;&lt;- авторы<br>
                            <?= TAB2 ?>publishers.php&nbsp;&lt;- издательства<br>
                            <?= TAB2 ?>users.php&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;- читатели<br>
                            <?= TAB2 ?>copies.php&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;- экземпляры<br>
                            <?= TAB2 ?>borrows.php&nbsp;&nbsp;&nbsp;&nbsp;&lt;- выдача и возврат
                        </div>
                    <?php endif; ?>
                </div>

                    <?php if (isTeacher()): ?>
                    <form method="post" action="/toggle-topic.php" class="section-edit-form" id="sef-web3" data-key="web3" style="display:none">
                        <input type="hidden" name="action" value="save_section">
                        <input type="hidden" name="course" value="web">
                        <input type="hidden" name="topic_key" value="web3">
                        <input type="hidden" name="back" value="/web-course.php#web3">
                        <input type="text" name="title" class="section-title-input" value="<?= htmlspecialchars($web3Title) ?>" placeholder="Заголовок темы">
                        <div id="editor-web3"></div>
                        <textarea name="content" style="display:none"></textarea>
                        <div style="margin-top:8px; display:flex; gap:8px">
                            <button type="submit" class="btn btn-primary btn-small">Сохранить</button>
                            <button type="button" class="btn btn-small" onclick="toggleSectionEdit('web','web3')">Отмена</button>
                        </div>
                    </form>
                    <?php endif; ?>

                    <?= topicButton('web', 'web3', 'web4') ?>
                </article>

                <!-- 4.4 -->
                <article id="web4" class="lesson">
                <?php
                    $web4Title = getCourseSectionTitle('web', 'web4') ?? '4.4. Создание базы данных';
                ?>
                <h3><?= htmlspecialchars($web4Title) ?><?php if (isTeacher()): ?> <button class="edit-section-btn" onclick="toggleSectionEdit('web','web4')">Редактировать тему</button><?php endif; ?></h3>

                <div class="text-content" id="sc-web4">
                    <?php $__sc = getCourseSection('web', 'web4'); if ($__sc !== null): echo $__sc; else: ?>
                        <p>Перейдём к практической реализации спроектированной базы данных. Откроем phpMyAdmin через меню OpenServer в разделе проектов и создадим базу данных <code>library_db</code>.</p>
                        <p>Базу и таблицы можно создать вручную, либо с помощью SQL-кода:</p>
                        <div class="content-placeholder">
                            <strong>Листинг 4.4.1 Создание таблиц</strong><br>
                            <?= TAB1 ?>CREATE DATABASE IF NOT EXISTS library_db<br>
                            <?= TAB2 ?>CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;<br>
                            <?= TAB1 ?>USE library_db;<br><br>
                            <?= TAB1 ?>CREATE TABLE Authors (<br>
                            <?= TAB2 ?>id         INT AUTO_INCREMENT PRIMARY KEY,<br>
                            <?= TAB2 ?>first_name VARCHAR(100) NOT NULL,<br>
                            <?= TAB2 ?>last_name  VARCHAR(100) NOT NULL,<br>
                            <?= TAB2 ?>birth_date DATE,<br>
                            <?= TAB2 ?>country    VARCHAR(100)<br>
                            <?= TAB1 ?>);<br><br>
                            <?= TAB1 ?>CREATE TABLE Publishers (<br>
                            <?= TAB2 ?>id      INT AUTO_INCREMENT PRIMARY KEY,<br>
                            <?= TAB2 ?>name    VARCHAR(200) NOT NULL,<br>
                            <?= TAB2 ?>address VARCHAR(300)<br>
                            <?= TAB1 ?>);<br><br>
                            <?= TAB1 ?>CREATE TABLE Books (<br>
                            <?= TAB2 ?>id               INT AUTO_INCREMENT PRIMARY KEY,<br>
                            <?= TAB2 ?>title            VARCHAR(300) NOT NULL,<br>
                            <?= TAB2 ?>author_id        INT NOT NULL,<br>
                            <?= TAB2 ?>publisher_id     INT NOT NULL,<br>
                            <?= TAB2 ?>genre            VARCHAR(100),<br>
                            <?= TAB2 ?>page_count       INT,<br>
                            <?= TAB2 ?>age_limit        INT DEFAULT 0,<br>
                            <?= TAB2 ?>publication_date DATE,<br>
                            <?= TAB2 ?>FOREIGN KEY (author_id)    REFERENCES Authors(id),<br>
                            <?= TAB2 ?>FOREIGN KEY (publisher_id) REFERENCES Publishers(id)<br>
                            <?= TAB1 ?>);<br><br>
                            <?= TAB1 ?>CREATE TABLE Users (<br>
                            <?= TAB2 ?>id                INT AUTO_INCREMENT PRIMARY KEY,<br>
                            <?= TAB2 ?>first_name        VARCHAR(100) NOT NULL,<br>
                            <?= TAB2 ?>last_name         VARCHAR(100) NOT NULL,<br>
                            <?= TAB2 ?>registration_date DATE NOT NULL DEFAULT (CURRENT_DATE),<br>
                            <?= TAB2 ?>birth_date        DATE<br>
                            <?= TAB1 ?>);<br><br>
                            <?= TAB1 ?>CREATE TABLE Book_copies (<br>
                            <?= TAB2 ?>id      INT AUTO_INCREMENT PRIMARY KEY,<br>
                            <?= TAB2 ?>book_id INT NOT NULL,<br>
                            <?= TAB2 ?>status  ENUM('available','borrowed','damaged','lost')<br>
                            <?= TAB2 ?><?= TAB1 ?>NOT NULL DEFAULT 'available',<br>
                            <?= TAB2 ?>FOREIGN KEY (book_id) REFERENCES Books(id)<br>
                            <?= TAB1 ?>);<br><br>
                            <?= TAB1 ?>CREATE TABLE Borrow_records (<br>
                            <?= TAB2 ?>id            INT AUTO_INCREMENT PRIMARY KEY,<br>
                            <?= TAB2 ?>user_id       INT NOT NULL,<br>
                            <?= TAB2 ?>book_copy_id  INT NOT NULL,<br>
                            <?= TAB2 ?>borrow_date   DATE NOT NULL DEFAULT (CURRENT_DATE),<br>
                            <?= TAB2 ?>due_date      DATE NOT NULL,<br>
                            <?= TAB2 ?>return_date   DATE DEFAULT NULL,<br>
                            <?= TAB2 ?>FOREIGN KEY (user_id)      REFERENCES Users(id),<br>
                            <?= TAB2 ?>FOREIGN KEY (book_copy_id) REFERENCES Book_copies(id)<br>
                            <?= TAB1 ?>);
                        </div>
                        <p>Заполним таблицы тестовыми данными:</p>
                        <div class="content-placeholder">
                            <strong>Листинг 4.4.2. Тестовые данные</strong><br>
                            <?= TAB1 ?>INSERT INTO Authors (first_name, last_name, birth_date, country) VALUES<br>
                            <?= TAB2 ?>('Лев', 'Толстой', '1828-09-09', 'Россия'),<br>
                            <?= TAB2 ?>('Фёдор', 'Достоевский', '1821-11-11', 'Россия'),<br>
                            <?= TAB2 ?>('Михаил', 'Булгаков', '1891-05-15', 'Россия'),<br>
                            <?= TAB2 ?>('Александр', 'Пушкин', '1799-06-06', 'Россия'),<br>
                            <?= TAB2 ?>('Николай', 'Гоголь', '1809-03-20', 'Россия');<br><br>
                            <?= TAB1 ?>INSERT INTO Publishers (name, address) VALUES<br>
                            <?= TAB2 ?>('Эксмо', 'Москва, ул. Зорге, 1'),<br>
                            <?= TAB2 ?>('АСТ', 'Москва, Пресненская наб., 6'),<br>
                            <?= TAB2 ?>('Азбука', 'Санкт-Петербург, наб. р. Фонтанки, 78');<br><br>
                            <?= TAB1 ?>INSERT INTO Books (title, author_id, publisher_id, genre, page_count, age_limit, publication_date) VALUES<br>
                            <?= TAB2 ?>('Война и мир', 1, 1, 'Роман-эпопея', 1300, 12, '2020-01-15'),<br>
                            <?= TAB2 ?>('Преступление и наказание', 2, 2, 'Роман', 672, 16, '2019-03-20'),<br>
                            <?= TAB2 ?>('Мастер и Маргарита', 3, 3, 'Роман', 480, 16, '2018-11-07'),<br>
                            <?= TAB2 ?>('Анна Каренина', 1, 1, 'Роман', 864, 12, '2022-02-28'),<br>
                            <?= TAB2 ?>('Идиот', 2, 2, 'Роман', 640, 16, '2021-04-10'),<br>
                            <?= TAB2 ?>('Собачье сердце', 3, 3, 'Повесть', 352, 12, '2020-09-05'),<br>
                            <?= TAB2 ?>('Евгений Онегин', 4, 1, 'Роман в стихах', 320, 6, '2019-06-20'),<br>
                            <?= TAB2 ?>('Мёртвые души', 5, 2, 'Поэма', 416, 12, '2021-11-15'),<br>
                            <?= TAB2 ?>('Пиковая дама', 4, 3, 'Повесть', 224, 12, '2022-03-01'),<br>
                            <?= TAB2 ?>('Ревизор', 5, 1, 'Комедия', 256, 12, '2020-07-18');
                        </div>
                <?php endif; ?>
                    </div>

                    <?php if (isTeacher()): ?>
                    <form method="post" action="/toggle-topic.php" class="section-edit-form" id="sef-web4" data-key="web4" style="display:none">
                        <input type="hidden" name="action" value="save_section">
                        <input type="hidden" name="course" value="web">
                        <input type="hidden" name="topic_key" value="web4">
                        <input type="hidden" name="back" value="/web-course.php#web4">
                        <input type="text" name="title" class="section-title-input" value="<?= htmlspecialchars($web4Title) ?>" placeholder="Заголовок темы">
                        <div id="editor-web4"></div>
                        <textarea name="content" style="display:none"></textarea>
                        <div style="margin-top:8px; display:flex; gap:8px">
                            <button type="submit" class="btn btn-primary btn-small">Сохранить</button>
                            <button type="button" class="btn btn-small" onclick="toggleSectionEdit('web','web4')">Отмена</button>
                        </div>
                    </form>
                    <?php endif; ?>

                    <?= topicButton('web', 'web4', 'web5') ?>
                </article>

<!-- 4.5 -->
<article id="web5" class="lesson">
    <?php
        $web5Title = getCourseSectionTitle('web', 'web5') ?? '4.5. Подключение к базе данных';
    ?>
    <h3><?= htmlspecialchars($web5Title) ?><?php if (isTeacher()): ?> <button class="edit-section-btn" onclick="toggleSectionEdit('web','web5')">Редактировать тему</button><?php endif; ?></h3>

    <div class="text-content" id="sc-web5">
        <?php $__sc = getCourseSection('web', 'web5'); if ($__sc !== null): echo $__sc; else: ?>

                        <p>Для подключения к базе через PHP используется расширение PDO. Напишем код подключения в файл <code>db.php</code>.</p>
                        <p>Для начала необходимо задать параметры подключения к базе:</p>

                        <div class="content-placeholder">
                            <strong>Листинг 4.5.1. Параметры подключения</strong><br>
                            <?= TAB1 ?>&lt;?php<br>
                            <?= TAB1 ?>define('DB_HOST', 'MySQL-8.2');   // имя хоста (у каждого своё)<br>
                            <?= TAB1 ?>define('DB_USER', 'root');        // пользователь<br>
                            <?= TAB1 ?>define('DB_PASS', '');            // пароль пустой<br>
                            <?= TAB1 ?>define('DB_NAME', 'library_db');  // имя нашей базы
                        </div>

                        <p>С помощью <code>define()</code> задаются постоянные значения (хост, имя пользователя и т.д.), их нельзя изменить после объявления.</p>

                        <p>Теперь напишем функцию для подключения к БД:</p>

                        <div class="content-placeholder">
                            <strong>Листинг 4.5.2. Функция подключения</strong><br>
                            <?= TAB1 ?>function db(): PDO {<br>
                            <?= TAB2 ?>static $pdo = null;<br>
                            <?= TAB2 ?>if ($pdo === null) {<br>
                            <?= TAB2 ?><?= TAB1 ?>$pdo = new PDO(<br>
                            <?= TAB2 ?><?= TAB2 ?>"mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",<br>
                            <?= TAB2 ?><?= TAB2 ?>DB_USER, DB_PASS,<br>
                            <?= TAB2 ?><?= TAB2 ?>[PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]<br>
                            <?= TAB2 ?><?= TAB1 ?>);<br>
                            <?= TAB2 ?>}<br>
                            <?= TAB2 ?>return $pdo;<br>
                            <?= TAB1 ?>}
                        </div>

                        <p>Функция <code>db()</code> создаёт объект PDO ровно один раз (благодаря <code>static $pdo</code>). При повторном вызове она просто вернёт уже существующее соединение.</p>

                        <p><code>new PDO(...)</code> – конструктор, который устанавливает подключение к серверу базы данных. Принимает несколько параметров:</p>
                        <ul>
                            <li>Первый – строка подключения. В ней мы через точку с запятой перечисляем настройки: тип базы данных (<code>mysql</code>), имя сервера (хост), имя базы данных (<code>library_db</code>) и кодировку <code>utf8mb4</code> (чтобы любые символы отражались корректно).</li>
                            <li>Второй и третий параметры – имя пользователя базы данных и пароль. Обычно на локальном сервере это <code>root</code> и пустой пароль.</li>
                            <li>Четвёртый параметр – массив дополнительных настроек. Самая важная из них: <code>PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC</code>. Она означает, что при возвращении результата запроса PDO будет представлять каждую строку в виде <strong>ассоциативного массива</strong>, где ключи – это имена столбцов. Это облегчит нам работу, ведь мы сможем обратиться к полю, используя его имя (например <code>$row['first_name']</code>), вместо запоминания номера колонки.</li>
                        </ul>
                        <br>
                        <p>Теперь разберём, как выполняются запросы к базе в PHP. Последовательность действий следующая: <strong>подготовка → выполнение → получение результата</strong>.</p>
                        <p>Объект PDO умеет подготавливать запросы к выполнению. Для этого вызывается метод <code>prepare()</code>, который принимает строку SQL. В строке вместо конкретных значений ставятся знаки вопроса <code>?</code> – на следующем этапе вместо них подставятся необходимые значения. 
                        <br> Например: <code>"SELECT * FROM Authors WHERE id = ?"</code>.</p>
                        <p><code>prepare()</code> возвращает другой объект – подготовленный запрос (PDOStatement). Именно у этого объекта вызывается метод <code>execute()</code>, в который мы передаём массив значений для подстановки на место каждого знака вопроса. Это необходимо для безопасности: значения отделяются от кода SQL, и никто не может «сломать» запрос, вставив в поле ввода свой SQL‑код.</p>
                        <p>Выполнение запроса происходит именно в момент вызова <code>execute()</code>. После выполнения мы можем получить результат:</p>
                        <ul>
                            <li>Если запрос был типа <code>SELECT</code>, вызываем <code>fetch()</code> – он возвращает одну строку в виде массива (в том формате, который мы задали в опциях), или <code>fetchAll()</code> – для получения всех строк сразу.</li>
                            <li>Если запрос был <code>INSERT</code>, <code>UPDATE</code> или <code>DELETE</code>, то <code>execute()</code> просто выполняет действие, и результат нам не нужен.</li>
                        </ul>

                        <p>Приведём пример получения автора для редактирования:</p>
                        <div class="content-placeholder">
                            <?= TAB1 ?>$sql = "SELECT * FROM Authors WHERE id = ?";<br>
                            <?= TAB1 ?>$stmt = db()->prepare($sql);<br>
                            <?= TAB1 ?>$stmt->execute([$id]);<br>
                            <?= TAB1 ?>$author = $stmt->fetch();
                        </div>

                        <p>Вернёмся к заполнению файла <code>db.php</code>. Добавим в него несколько функций, которые упростят нам работу и сделают код чище.</p>

                        <p><strong>Первая функция – <code>query()</code>.</strong> Она предназначена для запросов, которые возвращают множество строк (например, список книг, авторов, читателей). В неё мы передаём строку SQL и массив параметров, которые должны подставиться на место знаков вопроса. Внутри функция подготавливает запрос через <code>db()->prepare()</code>, выполняет его с переданными параметрами и возвращает все найденные строки с помощью <code>fetchAll()</code>. Результат всегда будет массивом (пустым, если ничего не найдено). Это удобно, например, когда мы выводим таблицу: достаточно вызвать <code>query()</code> и перебрать результат в цикле <code>foreach</code>.</p>

                        <div class="content-placeholder">
                            <strong>Листинг 4.5.3. Функция query()</strong><br>
                            <?= TAB1 ?>function query(string $sql, array $params = []): array {<br>
                            <?= TAB2 ?>$stmt = db()->prepare($sql);<br>
                            <?= TAB2 ?>$stmt->execute($params);<br>
                            <?= TAB2 ?>return $stmt->fetchAll();<br>
                            <?= TAB1 ?>}
                        </div>

                        <p><strong>Вторая функция – <code>row()</code>.</strong> Она похожа на <code>query()</code>, но возвращает только одну строку – либо ассоциативный массив, либо <code>false</code>, если запись не найдена. Будем использовать, например, когда мы открываем форму редактирования автора и нам нужно загрузить его текущие данные.</p>

                        <div class="content-placeholder">
                            <strong>Листинг 4.5.4. Функция row()</strong><br>
                            <?= TAB1 ?>function row(string $sql, array $params = []): array|false {<br>
                            <?= TAB2 ?>$stmt = db()->prepare($sql);<br>
                            <?= TAB2 ?>$stmt->execute($params);<br>
                            <?= TAB2 ?>return $stmt->fetch();<br>
                            <?= TAB1 ?>}
                        </div>

                        <p><strong>Третья функция – <code>val()</code>.</strong> Она возвращает одно единственное значение – первый столбец первой строки результата. Например, чтобы узнать, сколько всего книг в библиотеке, достаточно написать <code>val("SELECT COUNT(*) FROM Books")</code>.</p>

                        <div class="content-placeholder">
                            <strong>Листинг 4.5.5. Функция val()</strong><br>
                            <?= TAB1 ?>function val(string $sql, array $params = []): mixed {<br>
                            <?= TAB2 ?>$stmt = db()->prepare($sql);<br>
                            <?= TAB2 ?>$stmt->execute($params);<br>
                            <?= TAB2 ?>return $stmt->fetchColumn();<br>
                            <?= TAB1 ?>}
                        </div>

                        <p><strong>Четвёртая функция – <code>run()</code>.</strong> Она предназначена для запросов, которые изменяют данные: <code>INSERT</code>, <code>UPDATE</code>, <code>DELETE</code>. Внутри она так же подготавливает и выполняет запрос, но ничего не возвращает (тип <code>void</code>).</p>

                        <div class="content-placeholder">
                            <strong>Листинг 4.5.6. Функция run()</strong><br>
                            <?= TAB1 ?>function run(string $sql, array $params = []): void {<br>
                            <?= TAB2 ?>db()->prepare($sql)->execute($params);<br>
                            <?= TAB1 ?>}
                             <?php endif; ?>
                    </div>
                    <?php if (isTeacher()): ?>
                    <form method="post" action="/toggle-topic.php" class="section-edit-form" id="sef-web5" data-key="web5" style="display:none">
                        <input type="hidden" name="action" value="save_section">
                        <input type="hidden" name="course" value="web">
                        <input type="hidden" name="topic_key" value="web5">
                        <input type="hidden" name="back" value="/web-course.php#web5">
                        <!-- Добавляем поле для заголовка -->
                        <input type="text" name="title" class="section-title-input" value="<?= htmlspecialchars($web5Title) ?>" placeholder="Заголовок темы">
                        <div id="editor-web5"></div>
                        <textarea name="content" style="display:none"></textarea>
                        <div style="margin-top:8px; display:flex; gap:8px">
                            <button type="submit" class="btn btn-primary btn-small">Сохранить</button>
                            <button type="button" class="btn btn-small" onclick="toggleSectionEdit('web','web5')">Отмена</button>
                        </div>
                    </form>
                    <?php endif; ?>

                    <?= topicButton('web', 'web5', 'web6') ?>
                </article>

                <!-- 4.6 -->
                <article id="web6" class="lesson">
                <?php
                    $web6Title = getCourseSectionTitle('web', 'web6') ?? '4.6. Шаблон страниц и главная страница';
                ?>
                <h3><?= htmlspecialchars($web6Title) ?><?php if (isTeacher()): ?> <button class="edit-section-btn" onclick="toggleSectionEdit('web','web6')">Редактировать тему</button><?php endif; ?></h3>

                <div class="text-content" id="sc-web6">
                    <?php $__sc = getCourseSection('web', 'web6'); if ($__sc !== null): echo $__sc; else: ?>
                        <p><strong>Оформление стилей (CSS).</strong> В данном курсе мы сосредоточимся на логике работы приложения: подключении к базе данных, обработке форм, SQL-запросах и построении страниц. Создание визуального оформления (цвета, шрифты, расположение блоков) мы не будем разбирать подробно. Вы можете заранее ознакомиться с полным файлом стилей, используемых в приложении, по ссылке ниже или создать своё собственное оформление.</p> 
                    <p style="line-height: 2.2;"> <a href="images/library_style.css" style="display: inline-block; background: rgba(90,150,144,0.18); color: rgb(37, 72, 70); padding: 0px 15px; border-radius: 30px; text-decoration: none; font-weight: 500; 
                    font-size: 0.99rem; letter-spacing: 0.02em; margin: 0 2px; transition: all 0.2s ease; border: 1px solid transparent;" onmouseover="this.style.background='rgba(90,150,144,0.25)';
                     this.style.borderColor='rgba(47,87,85,0.4)';" onmouseout="this.style.background='rgba(90,150,144,0.18)'; this.style.borderColor='transparent';">
                     Открыть style.css</a></p>

                        <p>Прежде чем создавать главную страницу, подготовим общий «скелет» для всех страниц нашего приложения. Это избавит нас от повторения одного и того же HTML-кода (шапка, меню, подвал) на каждой странице. В файле <code>layout.php</code> определим две функции:</p>
                        <ul>
                            <li><code>pageTop($title, $active)</code> – она будет выводить начало HTML документа: боковое меню и верхнюю панель с заголовком.</li>
                            <li><code>pageBottom()</code> – она будет закрывать все открытые теги.</li>
                        </ul>
                        <p>Таким образом, все страницы нашего приложения будут иметь одинаковую структуру:</p>
                        <div class="content-placeholder">
                            <?= TAB1 ?>pageTop('Заголовок страницы', 'ключ_меню');<br>
                            <?= TAB1 ?>// … уникальное содержимое страницы …<br>
                            <?= TAB1 ?>pageBottom();
                        </div>

                        <p><strong>Начнём с функции <code>pageTop</code>:</strong></p>
                        <div class="content-placeholder">
                            <strong>Листинг 4.6.1. Функция pageTop()</strong><br>
                            <?= TAB1 ?>&lt;?php<br>
                            <?= TAB1 ?>function pageTop(string $title, string $active = ''): void {<br>
                            <?= TAB2 ?>$nav = [<br>
                            <?= TAB2 ?><?= TAB1 ?>'index'      => ['index.php', 'Главная'],<br>
                            <?= TAB2 ?><?= TAB1 ?>'books'      => ['books.php', 'Книги'],<br>
                            <?= TAB2 ?><?= TAB1 ?>'authors'    => ['authors.php', 'Авторы'],<br>
                            <?= TAB2 ?><?= TAB1 ?>'publishers' => ['publishers.php', 'Издательства'],<br>
                            <?= TAB2 ?><?= TAB1 ?>'users'      => ['users.php', 'Читатели'],<br>
                            <?= TAB2 ?><?= TAB1 ?>'copies'     => ['copies.php', 'Экземпляры'],<br>
                            <?= TAB2 ?><?= TAB1 ?>'borrows'    => ['borrows.php', 'Выдача'],<br>
                            <?= TAB2 ?>];<br>
                            <?= TAB2 ?>?&gt;<br>
                            <?= TAB2 ?>&lt;!DOCTYPE html&gt;<br>
                            <?= TAB2 ?>&lt;html lang="ru"&gt;<br>
                            <?= TAB2 ?>&lt;head&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?>&lt;meta charset="UTF-8"&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?>&lt;title&gt;&lt;?= e($title) ?&gt; — Библиотека&lt;/title&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?>&lt;link rel="stylesheet" href="assets/css/style.css"&gt;<br>
                            <?= TAB2 ?>&lt;/head&gt;<br>
                            <?= TAB2 ?>&lt;body&gt;<br>
                            <?= TAB2 ?>&lt;aside class="sidebar"&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?>&lt;div class="logo"&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?>&lt;span class="logo-icon" style="filter: brightness(0) invert(1);"&gt;🕮&lt;/span&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?>&lt;div&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;div class="logo-name"&gt;Библиотека&lt;/div&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;div class="logo-sub"&gt;Система управления&lt;/div&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?>&lt;/div&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?>&lt;/div&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?>&lt;nav&gt;<br>
                            <?= TAB2 ?>&lt;?php foreach ($nav as $key => [$url, $label]): ?&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?>&lt;a href="&lt;?= $url ?&gt;" class="nav-link &lt;?= $active === $key ? 'active' : '' ?&gt;"&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?>&lt;?= $label ?&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?>&lt;/a&gt;<br>
                            <?= TAB2 ?>&lt;?php endforeach ?&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?>&lt;/nav&gt;<br>
                            <?= TAB2 ?>&lt;/aside&gt;<br>
                            <?= TAB2 ?>&lt;div class="page"&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?>&lt;header class="topbar"&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?>&lt;h1&gt;&lt;?= e($title) ?&gt;&lt;/h1&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?>&lt;/header&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?>&lt;main class="main"&gt;<br>
                            <?= TAB2 ?>&lt;?php<br>
                            <?= TAB1 ?>}<br>
                            <?= TAB1 ?>?&gt;
                        </div>

                        <p>Внутри функции сначала объявляется массив <code>$nav</code>, где каждый элемент соответствует одному пункту бокового меню: ключ — это идентификатор раздела (например <code>'index'</code> или <code>'books'</code>), а значение — массив из двух строк: путь к файлу и название пункта.
                        <br> Затем функция выводит DOCTYPE, открывающие теги <code>&lt;html&gt;</code> и <code>&lt;head&gt;</code>, внутри которых устанавливается кодировка UTF-8, заголовок страницы (к переменной <code>$title</code> применяется функция <code>e()</code> для преобразования спецсимволов в HTML‑сущности) и подключается файл стилей <code>assets/css/style.css</code>. 
                        <br>Далее идёт тело документа: контейнер <code>&lt;aside class="sidebar"&gt;</code> с логотипом и навигацией. Навигация формируется циклом <code>foreach</code> по массиву <code>$nav</code>: для каждого пункта создаётся ссылка <code>&lt;a href="..."&gt;</code>, где атрибут <code>href</code> берётся из первого элемента вложенного массива. При этом проверяется равенство параметра <code>$active</code> с ключом текущего пункта — если они совпадают, ссылке добавляется класс <code>active</code> (позже в CSS это будет использовано для подсветки активного раздела). После меню выводятся блоки <code>&lt;div class="page"&gt;</code>, <code>&lt;header class="topbar"&gt;</code> с заголовком (снова экранированным через <code>e()</code>) и открывающий тег <code>&lt;main class="main"&gt;</code>. Все эти теги остаются незакрытыми — закрытие происходит в функции <code>pageBottom()</code>.</p>

                        <p><strong>Функция <code>pageBottom</code></strong> – она работает очень просто, закрывает теги, открытые в <code>pageTop()</code>:</p>
                        <div class="content-placeholder">
                            <strong>Листинг 4.6.2. Функция pageBottom()</strong><br>
                            <?= TAB1 ?>function pageBottom(): void {<br>
                            <?= TAB2 ?>?&gt;<br>
                            <?= TAB2 ?>&lt;/main&gt;<br>
                            <?= TAB2 ?>&lt;/div&gt;<br>
                            <?= TAB2 ?>&lt;/body&gt;<br>
                            <?= TAB2 ?>&lt;/html&gt;<br>
                            <?= TAB2 ?>&lt;?php<br>
                            <?= TAB1 ?>}
                        </div>

                        <p>В коде мы использовали <strong>вспомогательную функцию <code>e()</code></strong>, размещена она будет в файле <code>helpers.php</code>. Когда мы выводим на страницу данные, которые пришли от пользователя или из базы данных, они могут содержать спецсимволы (<code>&lt;</code>, <code>&gt;</code>, <code>&amp;</code>, <code>"</code> или <code>'</code>), что может привести к поломке разметки страницы. Функция <code>e()</code> решает эту проблему. Она принимает строку и возвращает её с символами, заменёнными на их HTML‑версию. Например: <code>&lt;</code> превращается в <code>&amp;lt;</code>, а <code>&amp;</code> — в <code>&amp;amp;</code>.</p>
                        <div class="content-placeholder">
                            <strong>Листинг 4.6.3. Функция e()</strong><br>
                            <?= TAB1 ?>function e(?string $str): string {<br>
                            <?= TAB2 ?>return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');<br>
                            <?= TAB1 ?>}
                        </div>

                        <p><br>Теперь мы можем перейти к наполнению <strong>главной страницы</strong> (index.php). Она будет содержать основную информацию о состоянии нашей библиотеки: статистика, последние выдачи, просроченные выдачи и популярные книги.</p>

                        <p><strong>Начнём со статистики.</strong> Реализуем с помощью отдельной функции, которую разместим в <code>helpers.php</code>.</p>
                        <div class="content-placeholder">
                            <strong>Листинг 4.6.4. Функция getStats()</strong><br>
                            <?= TAB1 ?>function getStats(): array {<br>
                            <?= TAB2 ?>return [<br>
                            <?= TAB2 ?><?= TAB1 ?>'books'   => val("SELECT COUNT(*) FROM Books"),<br>
                            <?= TAB2 ?><?= TAB1 ?>'authors' => val("SELECT COUNT(*) FROM Authors"),<br>
                            <?= TAB2 ?><?= TAB1 ?>'users'   => val("SELECT COUNT(*) FROM Users"),<br>
                            <?= TAB2 ?><?= TAB1 ?>'copies'  => val("SELECT COUNT(*) FROM Book_copies"),<br>
                            <?= TAB2 ?>];<br>
                            <?= TAB1 ?>}
                        </div>
                        <p>Функция <code>val()</code> из <code>db.php</code> выполняет SQL‑запрос и возвращает количество записей в таблице.</p>

                        <p>Переходим к следующим трём информационным блокам. Для каждого из них будем делать отдельный запрос к базе данных с помощью функции <code>query()</code>.</p>

                        <p><strong>Запрос последних выдач:</strong></p>
                        <div class="content-placeholder">
                            <?= TAB1 ?>$recentBorrows = query("<br>
                            <?= TAB1 ?>    SELECT br.borrow_date, br.due_date, br.return_date,<br>
                            <?= TAB1 ?>           CONCAT(u.first_name, ' ', u.last_name) AS user_name,<br>
                            <?= TAB1 ?>           b.title<br>
                            <?= TAB1 ?>    FROM Borrow_records br<br>
                            <?= TAB1 ?>    JOIN Users u        ON br.user_id      = u.id<br>
                            <?= TAB1 ?>    JOIN Book_copies bc ON br.book_copy_id = bc.id<br>
                            <?= TAB1 ?>    JOIN Books b        ON bc.book_id      = b.id<br>
                            <?= TAB1 ?>    ORDER BY br.borrow_date DESC<br>
                            <?= TAB1 ?>    LIMIT 8<br>
                            <?= TAB1 ?>");
                        </div>
                        <p>Таблица <code>Borrow_records</code> хранит только числовые идентификаторы читателя и экземпляра книги. Чтобы получить текстовые названия, нужно «присоединить» другие таблицы через <code>JOIN</code>.</p>
                        <ul>
                            <li>Соединяем <code>Borrow_records</code> с <code>Users</code> по <code>user_id</code> – получаем имя и фамилию читателя.</li>
                            <li>Соединяем с <code>Book_copies</code> по <code>book_copy_id</code> – узнаём, какой экземпляр.</li>
                            <li>Соединяем с <code>Books</code> по <code>book_id</code> – получаем название книги.</li>
                        </ul>
                        <p>Функция <code>CONCAT(u.first_name, ' ', u.last_name)</code> склеивает имя и фамилию в одно поле <code>user_name</code>.<br>
                        <code>ORDER BY br.borrow_date DESC</code> – сортируем от самых свежих записей.<br>
                        <code>LIMIT 8</code> – оставляем только 8 последних выдач.</p>

                        <p><strong>Запрос для получения просроченных выдач:</strong></p>
                        <div class="content-placeholder">
                            <?= TAB1 ?>$overdueList = query("<br>
                            <?= TAB1 ?>    SELECT CONCAT(u.first_name, ' ', u.last_name) AS user_name,<br>
                            <?= TAB1 ?>           b.title, br.due_date<br>
                            <?= TAB1 ?>    FROM Borrow_records br<br>
                            <?= TAB1 ?>    JOIN Users u        ON br.user_id      = u.id<br>
                            <?= TAB1 ?>    JOIN Book_copies bc ON br.book_copy_id = bc.id<br>
                            <?= TAB1 ?>    JOIN Books b        ON bc.book_id      = b.id<br>
                            <?= TAB1 ?>    WHERE br.return_date IS NULL AND br.due_date &lt; CURDATE()<br>
                            <?= TAB1 ?>    ORDER BY br.due_date ASC<br>
                            <?= TAB1 ?>    LIMIT 5<br>
                            <?= TAB1 ?>");
                        </div>
                        <p>Отличие от предыдущего запроса – условие <code>WHERE</code>.</p>
                        <ul>
                            <li><code>br.return_date IS NULL</code> – книга ещё не возвращена.</li>
                            <li><code>br.due_date &lt; CURDATE()</code> – срок возврата меньше сегодняшней даты (то есть уже прошёл).</li>
                        </ul>
                        <p><code>CURDATE()</code> – это функция MySQL, которая возвращает текущую дату.<br>
                        Сортировка по <code>due_date ASC</code> показывает самые старые просрочки первыми (видим, что нужно вернуть в первую очередь).<br>
                        <code>LIMIT 5</code> – оставляем только 5 самых критичных просрочек.</p>

                        <p><strong>Запрос популярных книг:</strong></p>
                        <div class="content-placeholder">
                            <?= TAB1 ?>$popularBooks = query("<br>
                            <?= TAB1 ?>    SELECT b.title, a.last_name, COUNT(br.id) AS cnt<br>
                            <?= TAB1 ?>    FROM Books b<br>
                            <?= TAB1 ?>    JOIN Authors a ON b.author_id = a.id<br>
                            <?= TAB1 ?>    JOIN Book_copies bc ON bc.book_id = b.id<br>
                            <?= TAB1 ?>    JOIN Borrow_records br ON br.book_copy_id = bc.id<br>
                            <?= TAB1 ?>    GROUP BY b.id<br>
                            <?= TAB1 ?>    ORDER BY cnt DESC<br>
                            <?= TAB1 ?>    LIMIT 3<br>
                            <?= TAB1 ?>");
                        </div>
                        <p>Здесь мы считаем, сколько раз книгу выдавали.</p>
                        <ul>
                            <li>Соединяем <code>Books</code> с <code>Authors</code> – чтобы получить фамилию автора.</li>
                            <li>Соединяем с <code>Book_copies</code> – чтобы перейти от книги к её экземплярам.</li>
                            <li>Соединяем с <code>Borrow_records</code> – чтобы учесть каждую выдачу каждого экземпляра.</li>
                            <li><code>COUNT(br.id)</code> считает количество записей о выдаче для каждой книги.</li>
                            <li><code>GROUP BY b.id</code> – группируем результаты по книге.</li>
                            <li><code>ORDER BY cnt DESC</code> – сортируем по убыванию количества выдач.</li>
                        </ul>

                        <p>При отображении последних и просроченных выдач мы будем указывать дату, поэтому в <code>helpers.php</code> добавим функцию для форматирования даты:</p>
                        <div class="content-placeholder">
                            <strong>Листинг 4.6.5. Функция dateRu()</strong><br>
                            <?= TAB1 ?>// Форматирование даты: '2024-01-15' -> '15.01.2024'<br>
                            <?= TAB1 ?>function dateRu(?string $date): string {<br>
                            <?= TAB2 ?>if (!$date) return '—';<br>
                            <?= TAB2 ?>return date('d.m.Y', strtotime($date));<br>
                            <?= TAB1 ?>}
                        </div>

                        <p>Таким образом, на данном этапе наш файл <code>index.php</code> выглядит так:</p>
                        <div class="content-placeholder">
                            <strong>Листинг 4.6.6. Полный код index.php</strong><br>
                            <?= TAB1 ?>&lt;?php<br>
                            <?= TAB1 ?>session_start();<br>
                            <?= TAB1 ?>require_once 'includes/db.php';<br>
                            <?= TAB1 ?>require_once 'includes/helpers.php';<br>
                            <?= TAB1 ?>require_once 'includes/layout.php';<br><br>
                            <?= TAB1 ?>$stats = getStats();<br><br>
                            <?= TAB1 ?>$recentBorrows = query(" ... ");<br>
                            <?= TAB1 ?>$overdueList   = query(" ... ");<br>
                            <?= TAB1 ?>$popularBooks  = query(" ... ");<br><br>
                            <?= TAB1 ?>pageTop('Главная', 'index');<br>
                            <?= TAB1 ?>?&gt;<br>
                            <?= TAB1 ?>&lt;!-- Статистика --&gt;<br>
                            <?= TAB1 ?>&lt;div class="stats-grid"&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?>&lt;div class="stat-card"&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;div class="stat-value"&gt;&lt;?= $stats['books'] ?&gt;&lt;/div&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;div class="stat-label"&gt;Книг&lt;/div&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?>&lt;/div&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?>&lt;div class="stat-card"&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;div class="stat-value"&gt;&lt;?= $stats['authors'] ?&gt;&lt;/div&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;div class="stat-label"&gt;Авторов&lt;/div&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?>&lt;/div&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?>&lt;div class="stat-card"&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;div class="stat-value"&gt;&lt;?= $stats['users'] ?&gt;&lt;/div&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;div class="stat-label"&gt;Читателей&lt;/div&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?>&lt;/div&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?>&lt;div class="stat-card"&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;div class="stat-value"&gt;&lt;?= $stats['copies'] ?&gt;&lt;/div&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;div class="stat-label"&gt;Экземпляров&lt;/div&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?>&lt;/div&gt;<br>
                            <?= TAB1 ?>&lt;/div&gt;<br><br>
                            <?= TAB1 ?>&lt;div class="dash-row"&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?>&lt;div class="widget"&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;div class="widget-head"&gt;Последние выдачи&lt;/div&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;div class="widget-body"&gt;<br>
                            <?= TAB1 ?>&lt;?php if ($recentBorrows): ?&gt;<br>
                            <?= TAB1 ?>&lt;?php foreach ($recentBorrows as $r): ?&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?>&lt;div class="widget-row"&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;div&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;div class="widget-row-title"&gt;&lt;?= e($r['title']) ?&gt;&lt;/div&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;div class="widget-row-sub"&gt;&lt;?= e($r['user_name']) ?&gt; · &lt;?= dateRu($r['borrow_date']) ?&gt;&lt;/div&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;/div&gt;<br>
                            <?= TAB1 ?>&lt;?php if ($r['return_date']): ?&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?>&lt;span class="badge green"&gt;Вернул&lt;/span&gt;<br>
                            <?= TAB1 ?>&lt;?php elseif ($r['due_date'] &lt; date('Y-m-d')): ?&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?>&lt;span class="badge red"&gt;Просрочена&lt;/span&gt;<br>
                            <?= TAB1 ?>&lt;?php else: ?&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?>&lt;span class="badge blue"&gt;Активна&lt;/span&gt;<br>
                            <?= TAB1 ?>&lt;?php endif ?&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?>&lt;/div&gt;<br>
                            <?= TAB1 ?>&lt;?php endforeach ?&gt;<br>
                            <?= TAB1 ?>&lt;?php else: ?&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?>&lt;div class="empty"&gt;&lt;div class="empty-text"&gt;Выдач пока нет&lt;/div&gt;&lt;/div&gt;<br>
                            <?= TAB1 ?>&lt;?php endif ?&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;/div&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?>&lt;/div&gt;<br><br>
                            <?= TAB1 ?><?= TAB1 ?>&lt;div class="right-col"&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;div class="widget"&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;div class="widget-head" style="color:var(--red)"&gt;Просроченные&lt;/div&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;div class="widget-body"&gt;<br>
                            <?= TAB1 ?>&lt;?php if ($overdueList): ?&gt;<br>
                            <?= TAB1 ?>&lt;?php foreach ($overdueList as $r): ?&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?>&lt;div class="widget-row"&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;div&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;div class="widget-row-title"&gt;&lt;?= e($r['title']) ?&gt;&lt;/div&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;div class="widget-row-sub"&gt;&lt;?= e($r['user_name']) ?&gt;&lt;/div&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;/div&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;div class="widget-row-val widget-row-red"&gt;&lt;?= dateRu($r['due_date']) ?&gt;&lt;/div&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?>&lt;/div&gt;<br>
                            <?= TAB1 ?>&lt;?php endforeach ?&gt;<br>
                            <?= TAB1 ?>&lt;?php else: ?&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?>&lt;div class="widget-row"&gt;&lt;span style="color:var(--green)"&gt;Просроченных нет!&lt;/span&gt;&lt;/div&gt;<br>
                            <?= TAB1 ?>&lt;?php endif ?&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;/div&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;/div&gt;<br><br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;div class="widget"&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;div class="widget-head"&gt;Популярные книги&lt;/div&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;div class="widget-body"&gt;<br>
                            <?= TAB1 ?>&lt;?php foreach ($popularBooks as $r): ?&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?>&lt;div class="widget-row"&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;div&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;div class="widget-row-title"&gt;&lt;?= e($r['title']) ?&gt;&lt;/div&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;div class="widget-row-sub"&gt;&lt;?= e($r['last_name']) ?&gt;&lt;/div&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;/div&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;div class="widget-row-val"&gt;&lt;?= $r['cnt'] ?&gt; раз&lt;/div&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?>&lt;/div&gt;<br>
                            <?= TAB1 ?>&lt;?php endforeach ?&gt;<br>
                            <?= TAB1 ?>&lt;?php if (!$popularBooks): ?&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?>&lt;div class="empty"&gt;&lt;div class="empty-text"&gt;Нет данных&lt;/div&gt;&lt;/div&gt;<br>
                            <?= TAB1 ?>&lt;?php endif ?&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;/div&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;/div&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?>&lt;/div&gt;<br>
                            <?= TAB1 ?>&lt;/div&gt;<br>
                            <?= TAB1 ?>&lt;?php pageBottom() ?&gt;
                        </div>

                        <p><strong>Теперь наша главная страница выглядит так:</strong></p>
                        <img src="/images/library1.png" alt="Главная страница библиотеки" style="width: 100%; max-width: 800px; margin: 20px auto; display: block; border: 1px solid #ccc; border-radius: 8px;">

                        <p>Добавим несколько записей о выдаче, чтобы проверить корректность отображения информации на главной странице. Для этого сначала добавим экземпляры книг (копии), а затем создадим несколько выдач через интерфейс приложения. В результате контейнеры наполнятся данными:</p>
                        <img src="/images/library2.png" alt="Главная страница с данными" style="width: 100%; max-width: 800px; margin: 20px auto; display: block; border: 1px solid #ccc; border-radius: 8px;">
                        <?php endif; ?>
                    </div>

                    <?php if (isTeacher()): ?>
                    <form method="post" action="/toggle-topic.php" class="section-edit-form" id="sef-web6" data-key="web6" style="display:none">
                        <input type="hidden" name="action" value="save_section">
                        <input type="hidden" name="course" value="web">
                        <input type="hidden" name="topic_key" value="web6">
                        <input type="hidden" name="back" value="/web-course.php#web6">
                        <input type="text" name="title" class="section-title-input" value="<?= htmlspecialchars($web6Title) ?>" placeholder="Заголовок темы">
                        <div id="editor-web6"></div>
                        <textarea name="content" style="display:none"></textarea>
                        <div style="margin-top:8px; display:flex; gap:8px">
                            <button type="submit" class="btn btn-primary btn-small">Сохранить</button>
                            <button type="button" class="btn btn-small" onclick="toggleSectionEdit('web','web6')">Отмена</button>
                        </div>
                    </form>
                    <?php endif; ?>

                    <?= topicButton('web', 'web6', 'web7') ?>

                </article>


                <article id="web7" class="lesson">
                <?php
                    $web7Title = getCourseSectionTitle('web', 'web7') ?? '4.7. Управление авторами';
                ?>
                <h3><?= htmlspecialchars($web7Title) ?><?php if (isTeacher()): ?> <button class="edit-section-btn" onclick="toggleSectionEdit('web','web7')">Редактировать тему</button><?php endif; ?></h3>

                <div class="text-content" id="sc-web7">
                    <?php $__sc = getCourseSection('web', 'web7'); if ($__sc !== null): echo $__sc; else: ?>

                    <p>На данной странице будет располагаться таблица с информацией об авторах, с возможностью их удаления, редактирования и поиском по таблице, а также с возможностью добавления новых авторов.</p>

                    <p>В начале файла мы подключаем необходимые файлы и запускаем сессию:</p>

                    <div class="content-placeholder">
                        <strong>Листинг 4.9. Начало файла authors.php</strong><br>
                        <?= TAB1 ?>&lt;?php<br>
                        <?= TAB1 ?>session_start();<br>
                        <?= TAB1 ?>require_once 'includes/db.php';<br>
                        <?= TAB1 ?>require_once 'includes/helpers.php';<br>
                        <?= TAB1 ?>require_once 'includes/layout.php';
                    </div>

                    <p>Начнём с вывода таблицы‑списка авторов:</p>

                    <div class="content-placeholder">
                        <?= TAB1 ?>$authors = query("SELECT * FROM Authors ORDER BY last_name");
                    </div>

                    <p>Выводим страницу с таблицей:</p>

                    <div class="content-placeholder">
                        <strong>Листинг 4.10. Первая версия таблицы авторов</strong><br>
                        pageTop('Авторы', 'authors');<br>
                        ?&gt;<br>
                        &lt;div class="table-card"&gt;<br>
                        <?= TAB1 ?>&lt;table class="data-table"&gt;<br>
                        <?= TAB2 ?>&lt;thead&gt;<br>
                        <?= TAB2 ?><?= TAB1 ?>&lt;tr&gt;&lt;th&gt;#&lt;/th&gt;&lt;th&gt;Фамилия&lt;/th&gt;&lt;th&gt;Имя&lt;/th&gt;&lt;th&gt;Дата рождения&lt;/th&gt;&lt;th&gt;Страна&lt;/th&gt;&lt;th&gt;Действия&lt;/th&gt;&lt;/tr&gt;<br>
                        <?= TAB2 ?>&lt;/thead&gt;<br>
                        <?= TAB2 ?>&lt;tbody&gt;<br>
                        &lt;?php foreach ($authors as $a): ?&gt;<br>
                        <?= TAB2 ?>&lt;tr&gt;<br>
                        <?= TAB2 ?><?= TAB1 ?>>&lt;td&gt;&lt;?= $a['id'] ?&gt;&lt;/td&gt;<br>
                        <?= TAB2 ?><?= TAB1 ?>&lt;td&gt;&lt;strong&gt;&lt;?= e($a['last_name']) ?&gt;&lt;/strong&gt;&lt;/td&gt;<br>
                        <?= TAB2 ?><?= TAB1 ?>&lt;td&gt;&lt;?= e($a['first_name']) ?&gt;&lt;/td&gt;<br>
                        <?= TAB2 ?><?= TAB1 ?>&lt;td&gt;&lt;?= dateRu($a['birth_date']) ?&gt;&lt;/td&gt;<br>
                        <?= TAB2 ?><?= TAB1 ?>&lt;td&gt;&lt;?= e($a['country'] ?? '—') ?&gt;&lt;/td&gt;<br>
                        <?= TAB2 ?><?= TAB1 ?>&lt;td&gt;<br>
                        <?= TAB2 ?><?= TAB2 ?>&lt;a href="#" class="btn btn-outline btn-sm"&gt;✎&lt;/a&gt;<br>
                        <?= TAB2 ?><?= TAB2 ?>&lt;a href="#" class="btn btn-danger btn-sm"&gt;✕&lt;/a&gt;<br>
                        <?= TAB2 ?><?= TAB1 ?>&lt;/td&gt;<br>
                        <?= TAB2 ?>&lt;/tr&gt;<br>
                        &lt;?php endforeach; ?&gt;<br>
                        <?= TAB2 ?>&lt;/tbody&gt;<br>
                        <?= TAB1 ?>&lt;/table&gt;<br>
                        &lt;/div&gt;<br>
                        &lt;?php pageBottom(); ?&gt;
                    </div>

                    <p>Проверяем – открываем <code>authors.php</code>. Видим таблицу со всеми авторами. Кнопки пока ведут на <code>#</code> (ничего не делают).</p>
                    <img src="/images/library3.png" style="width: 100%; max-width: 800px; margin: 20px auto; display: block; border: 1px solid #ccc; border-radius: 8px;">
                    <h4>Добавляем поиск по таблице</h4>
                    <p>Теперь добавим строку поиска. Она будет отправлять GET-параметр <code>q</code>. При его наличии будем изменять SQL-запрос.</p>

                    <div class="content-placeholder">
                        <?= TAB1 ?>$search = trim($_GET['q'] ?? '');
                    </div>

                    <p>Модифицируем запрос к таблице:</p>

                    <div class="content-placeholder">
                        <strong>Листинг 4.11. Запрос с поиском</strong><br>
                        <?= TAB1 ?>if ($search) {<br>
                        <?= TAB2 ?>$authors = query("<br>
                        <?= TAB2 ?>    SELECT * FROM Authors <br>
                        <?= TAB2 ?>    WHERE CONCAT(first_name, ' ', last_name) LIKE ? OR country LIKE ?<br>
                        <?= TAB2 ?>    ORDER BY last_name<br>
                        <?= TAB2 ?>", ["%$search%", "%$search%"]);<br>
                        <?= TAB1 ?>} else {<br>
                        <?= TAB2 ?>$authors = query("SELECT * FROM Authors ORDER BY last_name");<br>
                        <?= TAB1 ?>}
                    </div>

                    <p><code>CONCAT(first_name, ' ', last_name)</code> – склеивает имя и фамилию через пробел, чтобы искать по полному имени.<br>
                    <code>LIKE ?</code> – плейсхолдер, а <code>"%$search%"</code> означает «содержит подстроку».<br>
                    Процент <code>%</code> до и после – ищем вхождение в любом месте.</p>

                    <p>Добавим форму поиска перед таблицей:</p>

                    <div class="content-placeholder">
                        &lt;div class="table-toolbar"&gt;<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&lt;form method="get" style="display:flex;gap:8px"&gt;<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;input type="text" name="q" class="search-input" placeholder="Поиск по имени, фамилии или стране" value="&lt;?= e($search) ?&gt;"&gt;<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;button type="submit" class="btn btn-outline btn-sm"&gt;Найти&lt;/button&gt;<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&lt;/form&gt;<br>
                        &lt;/div&gt;
                    </div>

                    <p>Теперь при вводе текста и нажатии «Найти» страница перезагружается с <code>?q=...</code>, и таблица фильтруется. Проверяем – поиск работает.</p>

                    <img src="/images/library4.png" style="width: 100%; max-width: 800px; margin: 20px auto; display: block; border: 1px solid #ccc; border-radius: 8px;">
                    <br>
                    <p>Теперь сделаем кнопки <strong>«Редактировать»</strong> и <strong>«Удалить»</strong> рабочими</p>
                    <p>Заменим <code>#</code> на реальные адреса с параметрами. Для редактирования будем передавать <code>action=edit</code> и <code>id</code> автора. Для удаления – <code>action=delete</code> и <code>id</code>.</p>

                    <div class="content-placeholder">
                        &lt;td&gt;<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&lt;a href="?action=edit&amp;id=&lt;?= $a['id'] ?&gt;" class="btn btn-outline btn-sm"&gt;✎&lt;/a&gt;<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&lt;a href="?action=delete&amp;id=&lt;?= $a['id'] ?&gt;" class="btn btn-danger btn-sm" onclick="return confirm('Удалить автора?')"&gt;✕&lt;/a&gt;<br>
                        &lt;/td&gt;
                    </div>

                    <p><code>?action=edit&amp;id=&lt;?= $a['id'] ?&gt;</code> – при клике браузер перейдёт на тот же файл <code>authors.php</code>, но с GET-параметрами. Например, <code>authors.php?action=edit&amp;id=5</code>.<br>
                    Для удаления добавили <code>onclick="return confirm('Удалить автора?')"</code> – это защита от случайного удаления, вызывающая стандартное диалоговое окно с вопросом. Если пользователь нажмёт «Отмена», переход по ссылке не произойдёт.</p>

                    <p>Добавим кнопку «Добавить автора» в верхнюю панель, рядом с поиском:</p>

                    <div class="content-placeholder">
                        &lt;a href="?action=add" class="btn btn-primary btn-sm"&gt;+ Добавить автора&lt;/a&gt;
                    </div>

                    <p>Теперь при клике на эту кнопку мы переходим на <code>authors.php?action=add</code>. Пока эта страница ничего не показывает, потому что мы ещё не написали обработку <code>action=add</code> и <code>action=edit</code>.</p>

                    <h4>Формы</h4>
                    <p>Сейчас весь код <code>authors.php</code> выполняется всегда одинаково: запрос к базе и вывод таблицы. Но нам нужно, чтобы при <code>action=add</code> или <code>action=edit</code> показывалась <strong>форма</strong>, а не таблица. Для этого введём переменную <code>$action</code>.</p>

                    <p>В самом верху файла, сразу после подключений, напишем:</p>

                    <div class="content-placeholder">
                        <?= TAB1 ?>$action = $_GET['action'] ?? 'list';<br>
                        <?= TAB1 ?>$id     = (int)($_GET['id'] ?? 0);
                    </div>

                    <ul>
                        <li><code>$action</code> – из адресной строки считываем параметр <code>action</code>. Он определяет, что нужно сделать: показать список (<code>list</code>), форму добавления (<code>add</code>), форму редактирования (<code>edit</code>) или удалить запись (<code>delete</code>). Если параметр не передан, по умолчанию – <code>list</code>.</li>
                        <li><code>$id</code> – идентификатор автора, который будет редактироваться или удаляться. Приводится к целому числу для безопасности.</li>
                    </ul>

                    <p>Теперь весь код, который выводит таблицу и поиск, нужно поместить под условие <code>if ($action === 'list')</code>. А для <code>add</code> и <code>edit</code> пока напишем заглушку.</p>

                    <div class="content-placeholder">
                        <strong>Листинг 4.12. Структура с разделением режимов</strong><br>
                        <?= TAB1 ?>// ---- РЕЖИМ ПОКАЗА ФОРМЫ ----<br>
                        <?= TAB1 ?>if ($action === 'add' || $action === 'edit') {<br>
                        <?= TAB2 ?>echo "Форма добавления/редактирования";<br>
                        <?= TAB2 ?>exit;<br>
                        <?= TAB1 ?>}<br><br>
                        <?= TAB1 ?>// ---- РЕЖИМ СПИСКА ----<br>
                        <?= TAB1 ?>if ($action === 'list') {<br>
                        <?= TAB2 ?>// ... весь предыдущий код (поиск, запрос, pageTop, таблица, pageBottom)<br>
                        <?= TAB1 ?>}
                    </div>

                    <p>Проверьте – переход по кнопкам «Добавить» и «Редактировать» теперь показывает текст-заглушку, а не таблицу. Значит, разделение работает.</p>

                    <h4>Создаём форму добавления / редактирования</h4>
                    <p>Внутри блока <code>if ($action === 'add' || $action === 'edit')</code> напишем код загрузки данных автора (если редактируем) и вывода формы.</p>

                    <div class="content-placeholder">
                        <strong>Листинг 4.13. Загрузка данных для формы</strong><br>
                        <?= TAB1 ?>if ($action === 'edit' && $id) {<br>
                        <?= TAB2 ?>$author = row("SELECT * FROM Authors WHERE id = ?", [$id]);<br>
                        <?= TAB2 ?>if (!$author) {<br>
                        <?= TAB2 ?><?= TAB1 ?>flash('error', 'Автор не найден');<br>
                        <?= TAB2 ?><?= TAB1 ?>go('authors.php');<br>
                        <?= TAB2 ?>}<br>
                        <?= TAB1 ?>} else {<br>
                        <?= TAB2 ?>$author = ['id' => 0, 'first_name' => '', 'last_name' => '', 'birth_date' => '', 'country' => ''];<br>
                        <?= TAB1 ?>}
                    </div>

                    <p><code>row()</code> – функция из <code>db.php</code>, возвращает одну строку в виде ассоциативного массива или <code>false</code>, если запись не найдена.<br>
                    Если мы редактируем, но автора с таким <code>id</code> нет в базе – показываем сообщение об ошибке через <code>flash()</code> и перенаправляем обратно в список.<br>
                    Для режима <code>add</code> создаём массив с пустыми полями – это нужно, чтобы форма не ругалась на неопределённые переменные.</p>

                    <p>Функции <code>flash()</code> и <code>getFlash()</code> (размещаются в <code>helpers.php</code>):</p>

                    <div class="content-placeholder">
                        <strong>Листинг 4.14. Flash-сообщения</strong><br>
                        <?= TAB1 ?>// Сохранить flash-сообщение<br>
                        <?= TAB1 ?>function flash(string $type, string $text): void {<br>
                        <?= TAB2 ?>$_SESSION['flash'] = compact('type', 'text');<br>
                        <?= TAB1 ?>}<br><br>
                        <?= TAB1 ?>// Получить и удалить flash-сообщение<br>
                        <?= TAB1 ?>function getFlash(): string {<br>
                        <?= TAB2 ?>if (empty($_SESSION['flash'])) return '';<br>
                        <?= TAB2 ?>['type' => $type, 'text' => $text] = $_SESSION['flash'];<br>
                        <?= TAB2 ?>unset($_SESSION['flash']);<br>
                        <?= TAB2 ?>return '&lt;div class="flash flash-' . e($type) . '"&gt;' . e($text) . '&lt;/div&gt;';<br>
                        <?= TAB1 ?>}
                    </div>

                    <p>После загрузки данных выводим шапку страницы, обязательно вызываем <code>getFlash()</code> (чтобы показать возможные ошибки, например «Автор не найден»), а затем саму форму:</p>

                    <div class="content-placeholder">
                        <strong>Листинг 4.15. HTML-форма автора</strong><br>
                        <?= TAB1 ?>pageTop($id ? 'Редактировать автора' : 'Добавить автора', 'authors');<br>
                        <?= TAB1 ?>echo getFlash();  // показываем flash-сообщения (ошибки, уведомления)<br>
                        <?= TAB1 ?>?&gt;<br>
                        &lt;div class="breadcrumb"&gt;<br>
                        <?= TAB2 ?>&lt;a href="authors.php"&gt;Авторы&lt;/a&gt; &lt;span class="bc-sep"&gt;›&lt;/span&gt;<br>
                        <?= TAB2 ?>&lt;span&gt;&lt;?= $id ? 'Редактировать' : 'Добавить' ?&gt;&lt;/span&gt;<br>
                        &lt;/div&gt;<br>
                        &lt;div class="form-card"&gt;<br>
                        <?= TAB2 ?>&lt;form method="post" action=""&gt;<br>
                        <?= TAB2 ?><?= TAB1 ?>&lt;div class="form-grid"&gt;<br>
                        <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?>&lt;div class="form-group"&gt;<br>
                        <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;label&gt;Имя *&lt;/label&gt;<br>
                        <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;input type="text" name="first_name" value="&lt;?= e($author['first_name']) ?&gt;" required maxlength="100"&gt;<br>
                        <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?>&lt;/div&gt;<br>
                        <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?>&lt;div class="form-group"&gt;<br>
                        <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;label&gt;Фамилия *&lt;/label&gt;<br>
                        <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;input type="text" name="last_name" value="&lt;?= e($author['last_name']) ?&gt;" required maxlength="100"&gt;<br>
                        <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?>&lt;/div&gt;<br>
                        <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?>&lt;div class="form-group"&gt;<br>
                        <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;label&gt;Дата рождения&lt;/label&gt;<br>
                        <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;input type="date" name="birth_date" value="&lt;?= e($author['birth_date']) ?&gt;"&gt;<br>
                        <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?>&lt;/div&gt;<br>
                        <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?>&lt;div class="form-group"&gt;<br>
                        <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;label&gt;Страна&lt;/label&gt;<br>
                        <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;input type="text" name="country" value="&lt;?= e($author['country']) ?&gt;" maxlength="100"&gt;<br>
                        <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?>&lt;/div&gt;<br>
                        <?= TAB2 ?><?= TAB1 ?>&lt;/div&gt;<br>
                        <?= TAB2 ?><?= TAB1 ?>&lt;div class="form-actions"&gt;<br>
                        <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?>&lt;button type="submit" class="btn btn-primary"&gt;Сохранить&lt;/button&gt;<br>
                        <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?>&lt;a href="authors.php" class="btn btn-outline"&gt;Отмена&lt;/a&gt;<br>
                        <?= TAB2 ?><?= TAB1 ?>&lt;/div&gt;<br>
                        <?= TAB2 ?>&lt;/form&gt;<br>
                        &lt;/div&gt;<br>
                        &lt;?php<br>
                        <?= TAB1 ?>pageBottom();<br>
                        <?= TAB1 ?>exit;
                    </div>

                    <p><strong>Пояснения:</strong> форма отправляется методом <code>POST</code> на ту же самую страницу (пустой <code>action</code>). Поля имеют имена, совпадающие с колонками в таблице <code>Authors</code>. <code>value="&lt;?= e($author['first_name']) ?&gt;"</code> подставляет текущее значение (для редактирования – данные из БД, для добавления – пустые строки). Атрибут <code>required</code> не даст отправить форму, если поля не заполнены. Кнопка «Отмена» ведёт на <code>authors.php</code> (список). В конце – <code>exit</code>, чтобы после формы не выполнился код списка.</p>

                    <p><strong>Проверка:</strong> перейдите по ссылке <code>?action=add</code> – должна появиться пустая форма. Перейдите по ссылке <code>?action=edit&amp;id=1</code> – форма должна показать данные автора с <code>id=1</code> (например, Льва Толстого).</p>
                    <img src="/images/library5.png" style="width: 100%; max-width: 800px; margin: 20px auto; display: block; border: 1px solid #ccc; border-radius: 8px;"> 
                    <h4>Обработка POST-запроса (сохранение)</h4>
                    <p>Когда пользователь заполняет форму и нажимает «Сохранить», браузер отправляет <strong>POST-запрос</strong> на <code>authors.php</code>. Нам нужно перехватить этот запрос, взять данные из <code>$_POST</code> и сохранить их в базу: либо добавить нового автора (<code>INSERT</code>), либо обновить существующего (<code>UPDATE</code>).</p>

                    <p>Этот код должен располагаться <strong>до</strong> всех проверок <code>$action</code>, потому что POST-запрос приходит независимо от того, какая страница показана. Лучше всего поместить его сразу после получения <code>$action</code> и <code>$id</code>.</p>

                    <div class="content-placeholder">
                        <strong>Листинг 4.16. Обработчик сохранения</strong><br>
                        <?= TAB1 ?>if ($_SERVER['REQUEST_METHOD'] === 'POST') {<br>
                        <?= TAB2 ?>// Собираем данные из формы<br>
                        <?= TAB2 ?>$data = [<br>
                        <?= TAB2 ?><?= TAB1 ?>'first_name' => trim($_POST['first_name'] ?? ''),<br>
                        <?= TAB2 ?><?= TAB1 ?>'last_name'  => trim($_POST['last_name'] ?? ''),<br>
                        <?= TAB2 ?><?= TAB1 ?>'birth_date' => $_POST['birth_date'] ?: null,<br>
                        <?= TAB2 ?><?= TAB1 ?>'country'    => trim($_POST['country'] ?? '') ?: null,<br>
                        <?= TAB2 ?>];<br><br>
                        <?= TAB2 ?>// Простая проверка обязательных полей<br>
                        <?= TAB2 ?>if (empty($data['first_name']) || empty($data['last_name'])) {<br>
                        <?= TAB2 ?><?= TAB1 ?>flash('error', 'Имя и фамилия обязательны для заполнения');<br>
                        <?= TAB2 ?><?= TAB1 ?>$redirect = $id ? "?action=edit&id=$id" : "?action=add";<br>
                        <?= TAB2 ?><?= TAB1 ?>go($redirect);<br>
                        <?= TAB2 ?>}<br><br>
                        <?= TAB2 ?>if ($id) {<br>
                        <?= TAB2 ?><?= TAB1 ?>// Обновление существующего автора<br>
                        <?= TAB2 ?><?= TAB1 ?>run("UPDATE Authors SET first_name=?, last_name=?, birth_date=?, country=? WHERE id=?",<br>
                        <?= TAB2 ?><?= TAB2 ?>[$data['first_name'], $data['last_name'], $data['birth_date'], $data['country'], $id]);<br>
                        <?= TAB2 ?><?= TAB1 ?>flash('success', 'Автор обновлён');<br>
                        <?= TAB2 ?>} else {<br>
                        <?= TAB2 ?><?= TAB1 ?>// Добавление нового автора<br>
                        <?= TAB2 ?><?= TAB1 ?>run("INSERT INTO Authors (first_name, last_name, birth_date, country) VALUES (?, ?, ?, ?)",<br>
                        <?= TAB2 ?><?= TAB2 ?>[$data['first_name'], $data['last_name'], $data['birth_date'], $data['country']]);<br>
                        <?= TAB2 ?><?= TAB1 ?>flash('success', 'Автор добавлен');<br>
                        <?= TAB2 ?>}<br><br>
                        <?= TAB2 ?>// После сохранения перенаправляем на список авторов<br>
                        <?= TAB2 ?>go('authors.php');<br>
                        <?= TAB1 ?>}
                    </div>

                    <p><strong>Пояснение:</strong><br>
                    <code>$_SERVER['REQUEST_METHOD'] === 'POST'</code> – определяем, что запрос пришёл методом POST.<br>
                    <code>trim($_POST['first_name'] ?? '')</code> – убираем лишние пробелы, если поле не пришло – подставляем пустую строку.<br>
                    Проверяем, что имя и фамилия не пустые. Если пустые – сохраняем flash-сообщение об ошибке и перенаправляем обратно в форму с сохранением <code>id</code>.<br>
                    Если <code>$id > 0</code> – выполняем <code>UPDATE</code>, иначе – <code>INSERT</code>.<br>
                    Функция <code>run()</code> (из <code>db.php</code>) выполняет запрос и не возвращает результат.<br>
                    После успешного сохранения – flash-сообщение и редирект на <code>authors.php</code> (список).</p>

                    <p>Вспомогательная функция <code>go()</code> (также в <code>helpers.php</code>):</p>

                    <div class="content-placeholder">
                        <?= TAB1 ?>function go(string $url): never {<br>
                        <?= TAB2 ?>header("Location: $url");<br>
                        <?= TAB2 ?>exit;<br>
                        <?= TAB1 ?>}
                    </div>

                    <h4>Реализуем удаление автора</h4>
                    <p>Удаление происходит по GET-запросу: когда пользователь кликает на крестик, браузер переходит по ссылке <code>?action=delete&amp;id=...</code>. Обработчик удаления ставим сразу после обработки POST, но до проверки <code>$action</code>.</p>

                    <div class="content-placeholder">
                        <strong>Листинг 4.17. Обработчик удаления</strong><br>
                        <?= TAB1 ?>if ($action === 'delete' && $id) {<br>
                        <?= TAB2 ?>try {<br>
                        <?= TAB2 ?><?= TAB1 ?>run("DELETE FROM Authors WHERE id = ?", [$id]);<br>
                        <?= TAB2 ?><?= TAB1 ?>flash('success', 'Автор удалён');<br>
                        <?= TAB2 ?>} catch (PDOException $e) {<br>
                        <?= TAB2 ?><?= TAB1 ?>// Если у автора есть книги, удаление вызовет ошибку внешнего ключа<br>
                        <?= TAB2 ?><?= TAB1 ?>flash('error', 'Нельзя удалить автора, у которого есть книги');<br>
                        <?= TAB2 ?>}<br>
                        <?= TAB2 ?>go('authors.php');<br>
                        <?= TAB1 ?>}
                    </div>

                    <p><code>run("DELETE ...")</code> пытается удалить запись. Если у автора есть связанные записи в таблице <code>Books</code>, MySQL не даст его удалить (из-за внешнего ключа) и выбросит исключение <code>PDOException</code>. Мы ловим его и показываем понятное пользователю сообщение.</p>

                    <p>Чтобы сообщения об успешном добавлении, обновлении или удалении отображались на странице списка, необходимо в режиме <code>list</code> после <code>pageTop()</code> также вызвать <code>getFlash()</code>.</p>

                    <div class="content-placeholder">
                        <strong>Листинг 4.18. Вывод flash в режиме списка</strong><br>
                        <?= TAB1 ?>pageTop('Авторы', 'authors');<br>
                        <?= TAB1 ?>echo getFlash();  // показываем сообщения после операций<br>
                        // ... далее таблица и поиск
                    </div>
                     <?php endif; ?>
                </div>

                <?php if (isTeacher()): ?>
                <form method="post" action="/toggle-topic.php" class="section-edit-form" id="sef-web7" data-key="web7" style="display:none">
                    <input type="hidden" name="action" value="save_section">
                    <input type="hidden" name="course" value="web">
                    <input type="hidden" name="topic_key" value="web7">
                    <input type="hidden" name="back" value="/web-course.php#web7">
                    <input type="text" name="title" class="section-title-input" value="<?= htmlspecialchars($web7Title) ?>" placeholder="Заголовок темы">
                    <div id="editor-web7"></div>
                    <textarea name="content" style="display:none"></textarea>
                    <div style="margin-top:8px; display:flex; gap:8px">
                        <button type="submit" class="btn btn-primary btn-small">Сохранить</button>
                        <button type="button" class="btn btn-small" onclick="toggleSectionEdit('web','web7')">Отмена</button>
                    </div>
                </form>
                <?php endif; ?>

                <?= topicButton('web', 'web7', 'web8') ?>
            </article>


                <!-- 4.8 -->
                <article id="web8" class="lesson">
                <?php
                    $web8Title = getCourseSectionTitle('web', 'web8') ?? '4.8. Упрощение кода. Страница издательств';
                ?>
                <h3><?= htmlspecialchars($web8Title) ?><?php if (isTeacher()): ?> <button class="edit-section-btn" onclick="toggleSectionEdit('web','web8')">Редактировать тему</button><?php endif; ?></h3>

                <div class="text-content" id="sc-web8">
                    <?php $__sc = getCourseSection('web', 'web8'); if ($__sc !== null): echo $__sc; else: ?>
                        <p>При написании страницы authors.php мы создали функции для удаления, добавления, редактирования записей в таблице, сохранения формы, а также для поиска по таблице. Эти функции нам пригодятся и на других страницах приложения, поэтому правильнее будет вынести их в общий файл helpers.php, чтобы избежать дублирования кода.</p>

                        <p>Начнем с функции сохранения формы -- saveRecord(). Она будет принимать следующие данные: имя таблицы, массив данных, идентификатор записи (0 — новая запись, >0 — обновление), массив обязательных полей, текст успешного сообщения и URL для перенаправления.</p>

                        <div class="content-placeholder">
                            <strong>Листинг 6.3.1. Функция saveRecord()</strong><br>
                            <?= TAB1 ?>function saveRecord(string $table, array $data, int $id, array $required, string $successMsg, string $redirect): void {<br>
                            <?= TAB2 ?>// Проверка обязательных полей<br>
                            <?= TAB2 ?>foreach ($required as $field) {<br>
                            <?= TAB2 ?><?= TAB1 ?>if (empty($data[$field])) {<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?>flash('error', 'Заполните обязательные поля');<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?>go($redirect);<br>
                            <?= TAB2 ?><?= TAB1 ?>}<br>
                            <?= TAB2 ?>}<br><br>
                            <?= TAB2 ?>if ($id) {<br>
                            <?= TAB2 ?><?= TAB1 ?>// UPDATE<br>
                            <?= TAB2 ?><?= TAB1 ?>$setParts = [];<br>
                            <?= TAB2 ?><?= TAB1 ?>$values = [];<br>
                            <?= TAB2 ?><?= TAB1 ?>foreach ($data as $field => $value) {<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?>if ($field !== 'id') {<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>$setParts[] = "$field=?";<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>$values[] = $value;<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?>}<br>
                            <?= TAB2 ?><?= TAB1 ?>}<br>
                            <?= TAB2 ?><?= TAB1 ?>$values[] = $id;<br>
                            <?= TAB2 ?><?= TAB1 ?>run("UPDATE $table SET " . implode(', ', $setParts) . " WHERE id=?", $values);<br>
                            <?= TAB2 ?><?= TAB1 ?>flash('success', $successMsg . ' обновлён(а)');<br>
                            <?= TAB2 ?>} else {<br>
                            <?= TAB2 ?><?= TAB1 ?>// INSERT<br>
                            <?= TAB2 ?><?= TAB1 ?>$fields = array_keys($data);<br>
                            <?= TAB2 ?><?= TAB1 ?>$placeholders = array_fill(0, count($data), '?');<br>
                            <?= TAB2 ?><?= TAB1 ?>run("INSERT INTO $table (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")", array_values($data));<br>
                            <?= TAB2 ?><?= TAB1 ?>flash('success', $successMsg . ' добавлен(а)');<br>
                            <?= TAB2 ?>}<br>
                            <?= TAB2 ?>go($redirect);<br>
                            <?= TAB1 ?>}
                        </div>

                        <p>Сначала мы перебираем все обязательные поля. Если хотя бы одно из них в массиве <code>$data</code> пустое, вызываем <code>flash('error', ...)</code> и перенаправляем обратно. Благодаря этому пользователь не сможет сохранить запись с незаполненными обязательными полями.</p>
                        <p>Далее проверяем <code>$id</code>. Если параметр не равен нулю — значит, нужно <strong>обновить</strong> существующую запись. Для обновления готовим массив <code>$setParts</code> для фрагментов <code>поле=?</code> и массив <code>$values</code> для значений. Проходим по всем полям <code>$data</code>, пропуская ключ <code>id</code> (он пойдёт в <code>WHERE</code>), после цикла добавляем в <code>$values</code> сам <code>$id</code> и наконец формируем запрос <code>UPDATE ... SET поле1=?, поле2=? WHERE id=?</code>. Выполняем через <code>run()</code> и сохраняем flash‑сообщение об успешном обновлении.</p>
                        <p>Если <code>$id == 0</code> — нужно <strong>добавить</strong> новую запись. Извлекаем имена полей из ключей массива <code>$data</code>, создаём массив плейсхолдеров <code>['?', '?', ...]</code> нужной длины. Формируем <code>INSERT INTO table (поле1, поле2) VALUES (?, ?)</code> и выполняем. В конце сохраняем flash‑сообщение об успешном добавлении.</p>
                        <p>В обоих случаях после сохранения делаем редирект на указанный URL.</p>
                        <p>Благодаря этой функции в <code>authors.php</code> блок с POST‑запросом сокращается до нескольких строк: мы просто собираем <code>$data</code> и вызываем <code>saveRecord()</code>.</p>

                        <p>Далее сделаем функцию удаления записи с проверкой внешних ключей (чтобы, например, нельзя было удалить автора, у которого есть книги).</p>

                        <div class="content-placeholder">
                            <strong>Листинг 6.3.2. Функция deleteRecord()</strong><br>
                            <?= TAB1 ?>function deleteRecord(string $table, int $id, string $successMsg, string $errorMsg, string $redirect): void {<br>
                            <?= TAB2 ?>try {<br>
                            <?= TAB2 ?><?= TAB1 ?>run("DELETE FROM $table WHERE id=?", [$id]);<br>
                            <?= TAB2 ?><?= TAB1 ?>flash('success', $successMsg);<br>
                            <?= TAB2 ?>} catch (PDOException $e) {<br>
                            <?= TAB2 ?><?= TAB1 ?>flash('error', $errorMsg);<br>
                            <?= TAB2 ?>}<br>
                            <?= TAB2 ?>go($redirect);<br>
                            <?= TAB1 ?>}
                        </div>

                        <p>Пытаемся выполнить <code>DELETE FROM table WHERE id = ?</code>. Если удаление прошло успешно — сохраняем flash‑сообщение об успехе. Если возникает исключение <code>PDOException</code> (например, нарушение целостности внешнего ключа), перехватываем его и показываем понятное сообщение об ошибке, которое передаётся в параметре <code>$errorMsg</code>. В любом случае после попытки удаления перенаправляем пользователя обратно на страницу списка.</p>

                        <p>Также неплохо было бы сделать функцию для вывода цепочки навигации — мы будем использовать это в каждом файле.</p>

                        <div class="content-placeholder">
                            <strong>Листинг 6.3.3. Функция breadcrumb()</strong><br>
                            <?= TAB1 ?>function breadcrumb(array $items): void {<br>
                            <?= TAB2 ?>$last = array_pop($items);<br>
                            <?= TAB2 ?>echo '&lt;div class="breadcrumb"&gt;';<br>
                            <?= TAB2 ?>foreach ($items as $url => $label) {<br>
                            <?= TAB2 ?><?= TAB1 ?>echo '&lt;a href="' . $url . '"&gt;' . e($label) . '&lt;/a&gt; &lt;span class="bc-sep"&gt;›&lt;/span&gt; ';<br>
                            <?= TAB2 ?>}<br>
                            <?= TAB2 ?>echo '&lt;span&gt;' . e($last) . '&lt;/span&gt;';<br>
                            <?= TAB2 ?>echo '&lt;/div&gt;';<br>
                            <?= TAB1 ?>}
                        </div>

                        <p>Передаём ассоциативный массив, где ключи — URL, значения — названия разделов. Последний элемент считается текущим и выводится без ссылки. Например: <code>breadcrumb(['authors.php' => 'Авторы', '' => 'Добавить']);</code> выведет: «Авторы › Добавить».</p>

                        <p>Далее создадим функцию <code>renderForm()</code> для отрисовки HTML‑формы по описанию полей. Это избавляет от ручного написания однотипного кода форм для вывода разных таблиц.</p>

                        <div class="content-placeholder">
                            <strong>Листинг 6.3.4. Функция renderForm()</strong><br>
                            <?= TAB1 ?>function renderForm(string $action, array $fields, string $submitLabel, string $cancelUrl): void {<br>
                            <?= TAB2 ?>?&gt;<br>
                            <?= TAB2 ?>&lt;div class="form-card"&gt;<br>
                            <?= TAB2 ?>&lt;form method="post" action="&lt;?= $action ?&gt;"&gt;<br>
                            <?= TAB2 ?>&lt;div class="form-grid"&gt;<br>
                            &lt;?php foreach ($fields as $f):<br>
                            <?= TAB2 ?><?= TAB2 ?>$fullClass = ($f['full'] ?? false) ? 'full' : '';<br>
                            <?= TAB2 ?><?= TAB2 ?>$value = $f['value'] ?? '';<br>
                            <?= TAB2 ?><?= TAB2 ?>$required = ($f['required'] ?? false) ? 'required' : '';<br>
                            <?= TAB2 ?>?&gt;<br>
                            <?= TAB2 ?>&lt;div class="form-group &lt;?= $fullClass ?&gt;"&gt;<br>
                            <?= TAB2 ?>&lt;label&gt;&lt;?= e($f['label']) ?&gt; &lt;?= $required ? '*' : '' ?&gt;&lt;/label&gt;<br>
                            &lt;?php if ($f['type'] === 'select'): ?&gt;<br>
                            <?= TAB2 ?>&lt;select name="&lt;?= $f['name'] ?&gt;" &lt;?= $required ?&gt;&gt;<br>
                            &lt;?php foreach ($f['options'] as $optVal => $optLabel): ?&gt;<br>
                            <?= TAB2 ?><?= TAB2 ?>&lt;option value="&lt;?= $optVal ?&gt;" &lt;?= (string)$value === (string)$optVal ? 'selected' : '' ?&gt;&gt;&lt;?= e($optLabel) ?&gt;&lt;/option&gt;<br>
                            &lt;?php endforeach ?&gt;<br>
                            <?= TAB2 ?>&lt;/select&gt;<br>
                            &lt;?php elseif ($f['type'] === 'number'): ?&gt;<br>
                            <?= TAB2 ?>&lt;input type="number" name="&lt;?= $f['name'] ?&gt;" value="&lt;?= e($value) ?&gt;" &lt;?= $required ?&gt; &lt;?= $f['attrs'] ?? '' ?&gt;&gt;<br>
                            &lt;?php elseif ($f['type'] === 'date'): ?&gt;<br>
                            <?= TAB2 ?>&lt;input type="date" name="&lt;?= $f['name'] ?&gt;" value="&lt;?= e($value) ?&gt;" &lt;?= $required ?&gt;&gt;<br>
                            &lt;?php else: ?&gt;<br>
                            <?= TAB2 ?>&lt;input type="&lt;?= $f['type'] ?&gt;" name="&lt;?= $f['name'] ?&gt;" value="&lt;?= e($value) ?&gt;" &lt;?= $required ?&gt; &lt;?= $f['attrs'] ?? '' ?&gt;&gt;<br>
                            &lt;?php endif ?&gt;<br>
                            <?= TAB2 ?>&lt;/div&gt;<br>
                            &lt;?php endforeach ?&gt;<br>
                            <?= TAB2 ?>&lt;/div&gt;<br>
                            <?= TAB2 ?>&lt;div class="form-actions"&gt;<br>
                            <?= TAB2 ?>&lt;button type="submit" class="btn btn-primary"&gt;&lt;?= $submitLabel ?&gt;&lt;/button&gt;<br>
                            <?= TAB2 ?>&lt;a href="&lt;?= $cancelUrl ?&gt;" class="btn btn-outline"&gt;Отмена&lt;/a&gt;<br>
                            <?= TAB2 ?>&lt;/div&gt;<br>
                            <?= TAB2 ?>&lt;/form&gt;<br>
                            <?= TAB2 ?>&lt;/div&gt;<br>
                            &lt;?php<br>
                            <?= TAB1 ?>}
                        </div>

                        <p><strong>Параметры:</strong> <code>$action</code> — URL, на который отправляется форма (обычно пустая строка — отправляем на ту же страницу). <code>$fields</code> — массив описаний полей. Каждое описание — массив с ключами: <code>label</code> (текст метки), <code>type</code> (тип поля: text, date, number, select), <code>name</code> (имя поля), <code>value</code> (текущее значение), <code>required</code> (обязательно ли поле, добавляет звёздочку и атрибут required), <code>attrs</code> (дополнительные атрибуты, например <code>maxlength="100"</code>), <code>options</code> (для типа select — массив значение => текст), <code>full</code> (если true, поле занимает всю ширину формы).</p>
                        <p><code>$submitLabel</code> — текст на кнопке «Сохранить». <code>$cancelUrl</code> — URL для кнопки «Отмена».</p>
                        <p>Функция автоматически подставляет значения, добавляет звёздочку для обязательных полей и генерирует правильный HTML.</p>

                        <p>Следующая функция <code>renderSearchBar()</code>. Она будет выводить строку поиска, фильтры (если есть) и кнопку добавления новой записи.</p>

                        <div class="content-placeholder">
                            <strong>Листинг 6.3.5. Функция renderSearchBar()</strong><br>
                            <?= TAB1 ?>function renderSearchBar(string $searchValue, array $filters = [], string $addUrl = '', string $addLabel = '+ Добавить'): void {<br>
                            <?= TAB2 ?>?&gt;<br>
                            <?= TAB2 ?>&lt;div class="table-toolbar"&gt;<br>
                            <?= TAB2 ?>&lt;span class="table-total"&gt;Всего: &lt;?= $GLOBALS['total'] ?? 0 ?&gt;&lt;/span&gt;<br>
                            <?= TAB2 ?>&lt;div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap"&gt;<br>
                            <?= TAB2 ?>&lt;form method="get" style="display:flex;gap:8px"&gt;<br>
                            <?= TAB2 ?>&lt;input type="text" name="q" class="search-input" placeholder="Поиск…" value="&lt;?= e($searchValue) ?&gt;"&gt;<br>
                            &lt;?php foreach ($filters as $filter): ?&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?>&lt;select name="&lt;?= $filter['name'] ?&gt;" class="filter-select"&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?>&lt;option value=""&gt;&lt;?= $filter['placeholder'] ?? 'Все' ?&gt;&lt;/option&gt;<br>
                            &lt;?php foreach ($filter['options'] as $val => $label): ?&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?>&lt;option value="&lt;?= $val ?&gt;" &lt;?= ($filter['value'] ?? '') == $val ? 'selected' : '' ?&gt;&gt;&lt;?= e($label) ?&gt;&lt;/option&gt;<br>
                            &lt;?php endforeach ?&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?>&lt;/select&gt;<br>
                            &lt;?php endforeach ?&gt;<br>
                            <?= TAB2 ?>&lt;button type="submit" class="btn btn-outline btn-sm"&gt;Найти&lt;/button&gt;<br>
                            <?= TAB2 ?>&lt;/form&gt;<br>
                            &lt;?php if ($addUrl): ?&gt;<br>
                            <?= TAB2 ?>&lt;a href="&lt;?= $addUrl ?&gt;" class="btn btn-primary btn-sm"&gt;&lt;?= $addLabel ?&gt;&lt;/a&gt;<br>
                            &lt;?php endif ?&gt;<br>
                            <?= TAB2 ?>&lt;/div&gt;<br>
                            <?= TAB2 ?>&lt;/div&gt;<br>
                            &lt;?php<br>
                            <?= TAB1 ?>}
                        </div>

                        <p><code>$searchValue</code> — текущий поисковый запрос (подставляется в поле). <code>$filters</code> — массив фильтров (для книг мы будем передавать фильтр по жанру, для выдач — по статусу). Сейчас для авторов и издательств фильтров нет, поэтому передаём пустой массив. <code>$addUrl</code> — ссылка на форму добавления (например, <code>authors.php?action=add</code>). <code>$addLabel</code> — текст на кнопке добавления.</p>
                        <p>Обратите внимание: общее количество записей берётся из глобальной переменной <code>$GLOBALS['total']</code>. Перед вызовом <code>renderSearchBar()</code> мы должны вычислить <code>$total</code> и сохранить его в глобальной области (или передавать параметром, но для простоты используем <code>$GLOBALS</code>).</p>

                        <p>И последняя функция будет выводить сообщение, когда таблица не содержит данных. Объединяет ячейку на все столбцы таблицы.</p>

                        <div class="content-placeholder">
                            <strong>Листинг 6.3.6. Функция renderEmptyState()</strong><br>
                            <?= TAB1 ?>function renderEmptyState(int $colspan, string $text = 'Ничего не найдено'): void {<br>
                            <?= TAB2 ?>echo '&lt;tr&gt;&lt;td colspan="' . $colspan . '"&gt;&lt;div class="empty"&gt;';<br>
                            <?= TAB2 ?>echo '&lt;div class="empty-text"&gt;' . $text . '&lt;/div&gt;&lt;/div&gt;&lt;/td&gt;&lt;/tr&gt;';<br>
                            <?= TAB1 ?>}
                        </div>

                        <p>Принимает два параметра: <code>$colspan</code> — количество столбцов в таблице (чтобы ячейка растянулась на всю ширину), <code>$text</code> — текст сообщения.</p>

                        <p>После того как мы добавили в <code>helpers.php</code> все необходимые универсальные функции, код страницы <code>authors.php</code> можно значительно сократить и сделать более читаемым (см. листинг 6.3.7 в документации).</p>

                        <p>Теперь, когда в <code>helpers.php</code> есть все необходимые универсальные функции, создание страницы <code>publishers.php</code> становится очень простым. Она будет почти полностью повторять структуру <code>authors.php</code>, но у издательства всего два поля (<code>name</code> и <code>address</code>).</p>

                        <p>В самом начале подключаем все необходимые модули, запускаем сессию, определяем <code>$action</code> и <code>$id</code>:</p>

                        <div class="content-placeholder">
                            <strong>Листинг 6.3.8. Начало файла publishers.php</strong><br>
                            &lt;?php<br>
                            <?= TAB1 ?>session_start();<br>
                            <?= TAB1 ?>require_once 'includes/db.php';<br>
                            <?= TAB1 ?>require_once 'includes/helpers.php';<br>
                            <?= TAB1 ?>require_once 'includes/layout.php';<br><br>
                            <?= TAB1 ?>$action = $_GET['action'] ?? 'list';<br>
                            <?= TAB1 ?>$id     = (int)($_GET['id'] ?? 0);
                        </div>

                        <p>Как и в <code>authors.php</code>, <code>$action</code> может принимать значения <code>list</code> (по умолчанию), <code>add</code>, <code>edit</code>, <code>delete</code>. <code>$id</code> — идентификатор издательства, которое нужно отредактировать или удалить.</p>

                        <p><strong>Обработка POST-запроса (сохранение).</strong> Мы собираем данные в массив <code>$data</code>, а затем вызываем <code>saveRecord()</code>.</p>

                        <div class="content-placeholder">
                            <?= TAB1 ?>if ($_SERVER['REQUEST_METHOD'] === 'POST') {<br>
                            <?= TAB2 ?>$data = [<br>
                            <?= TAB2 ?><?= TAB1 ?>'name'    => trim($_POST['name'] ?? ''),<br>
                            <?= TAB2 ?><?= TAB1 ?>'address' => trim($_POST['address'] ?? '') ?: null,<br>
                            <?= TAB2 ?>];<br>
                            <?= TAB2 ?>saveRecord('Publishers', $data, $id, ['name'], 'Издательство', 'publishers.php');<br>
                            <?= TAB1 ?>}
                        </div>

                        <p><code>trim($_POST['name'] ?? '')</code> — удаляем лишние пробелы из названия. Если поле не заполнено, подставляем пустую строку. <code>trim($_POST['address'] ?? '') ?: null</code> — если пользователь не ввёл адрес, вместо пустой строки сохраняем <code>null</code>. В базе данных это будет означать отсутствие значения. <code>saveRecord()</code> проверит, что поле <code>name</code> не пустое (обязательное поле), и выполнит либо <code>INSERT</code>, либо <code>UPDATE</code> в зависимости от значения <code>$id</code>. После сохранения произойдёт перенаправление на <code>publishers.php</code>.</p>

                        <p><strong>Обработка удаления.</strong> Если в адресной строке есть <code>?action=delete&amp;id=...</code>, вызываем <code>deleteRecord()</code>:</p>

                        <div class="content-placeholder">
                            <?= TAB1 ?>if ($action === 'delete' && $id) {<br>
                            <?= TAB2 ?>deleteRecord('Publishers', $id, 'Издательство удалено', 'Нельзя удалить: есть связанные книги', 'publishers.php');<br>
                            <?= TAB1 ?>}
                        </div>

                        <p>Функция попытается удалить запись. Если у издательства есть хотя бы одна книга (внешний ключ в таблице <code>Books</code>), удаление не произойдёт, и пользователь увидит сообщение «Нельзя удалить: есть связанные книги».</p>

                        <p><strong>Вывод формы (добавление / редактирование).</strong> Если <code>$action</code> равен <code>add</code> или <code>edit</code>, показываем форму. Сначала загружаем данные издательства (если редактируем), затем вызываем <code>pageTop()</code>, <code>getFlash()</code>, <code>breadcrumb()</code>, готовим массив <code>$fields</code> и вызываем <code>renderForm()</code>.</p>

                        <div class="content-placeholder">
                            <?= TAB1 ?>if ($action === 'add' || $action === 'edit') {<br>
                            <?= TAB2 ?>$pub = $id ? row("SELECT * FROM Publishers WHERE id=?", [$id]) : ['id'=>0,'name'=>'','address'=>''];<br>
                            <?= TAB2 ?>pageTop($id ? 'Редактировать издательство' : 'Добавить издательство', 'publishers');<br>
                            <?= TAB2 ?>echo getFlash();<br>
                            <?= TAB2 ?>breadcrumb(['publishers.php' => 'Издательства', '' => $id ? 'Редактировать' : 'Добавить']);<br><br>
                            <?= TAB2 ?>$fields = [<br>
                            <?= TAB2 ?><?= TAB1 ?>['label'=>'Название', 'type'=>'text', 'name'=>'name', 'value'=>$pub['name'], 'required'=>true, 'attrs'=>'maxlength="200"', 'full'=>true],<br>
                            <?= TAB2 ?><?= TAB1 ?>['label'=>'Адрес', 'type'=>'text', 'name'=>'address', 'value'=>$pub['address'], 'attrs'=>'maxlength="300"', 'full'=>true],<br>
                            <?= TAB2 ?>];<br>
                            <?= TAB2 ?>renderForm('', $fields, 'Сохранить', 'publishers.php');<br>
                            <?= TAB2 ?>pageBottom();<br>
                            <?= TAB2 ?>exit;<br>
                            <?= TAB1 ?>}
                        </div>

                        <p><strong>Особенности:</strong> в массиве <code>$fields</code> у каждого поля указан ключ <code>'full' => true</code>. Это заставляет поле занимать всю ширину формы (класс <code>full</code> в CSS). Для издательств удобно, чтобы поля «Название» и «Адрес» растягивались на всю доступную ширину, а не располагались в две колонки. Поле <code>address</code> не обязательное, поэтому у него нет <code>'required' => true</code>. <code>breadcrumb()</code> показывает цепочку: «Издательства › Добавить» или «Издательства › Редактировать».</p>
                        <p>После вывода формы вызываем <code>pageBottom()</code> и <code>exit</code>, чтобы скрипт не пошёл дальше и не вывел таблицу.</p>

                        <p><strong>Вывод списка издательств.</strong> Если ни одно из предыдущих условий не сработало, значит, нужно показать таблицу со всеми издательствами.</p>

                        <div class="content-placeholder">
                            <?= TAB1 ?>$search = trim($_GET['q'] ?? '');<br><br>
                            <?= TAB1 ?>if ($search) {<br>
                            <?= TAB2 ?>$pubs = query("<br>
                            <?= TAB2 ?>    SELECT p.*, COUNT(b.id) AS book_count<br>
                            <?= TAB2 ?>    FROM Publishers p<br>
                            <?= TAB2 ?>    LEFT JOIN Books b ON b.publisher_id = p.id<br>
                            <?= TAB2 ?>    WHERE p.name LIKE ? OR p.address LIKE ?<br>
                            <?= TAB2 ?>    GROUP BY p.id<br>
                            <?= TAB2 ?>    ORDER BY p.name<br>
                            <?= TAB2 ?>", ["%$search%", "%$search%"]);<br>
                            <?= TAB1 ?>} else {<br>
                            <?= TAB2 ?>$pubs = query("<br>
                            <?= TAB2 ?>    SELECT p.*, COUNT(b.id) AS book_count<br>
                            <?= TAB2 ?>    FROM Publishers p<br>
                            <?= TAB2 ?>    LEFT JOIN Books b ON b.publisher_id = p.id<br>
                            <?= TAB2 ?>    GROUP BY p.id<br>
                            <?= TAB2 ?>    ORDER BY p.name<br>
                            <?= TAB2 ?>");<br>
                            <?= TAB1 ?>}<br><br>
                            <?= TAB1 ?>pageTop('Издательства', 'publishers');<br>
                            <?= TAB1 ?>echo getFlash();<br>
                            <?= TAB1 ?>renderSearchBar($search, [], 'publishers.php?action=add');<br>
                            <?= TAB1 ?>?&gt;<br>
                            &lt;table class="data-table"&gt;<br>
                            ...<br>
                            &lt;/table&gt;<br>
                            &lt;?php pageBottom(); ?&gt;
                        </div>

                        <p><strong>Пояснения к запросу:</strong> <code>LEFT JOIN Books b ON b.publisher_id = p.id</code> — присоединяем таблицу книг, чтобы посчитать, сколько книг выпустило каждое издательство. <code>COUNT(b.id) AS book_count</code> — количество книг. Если у издательства нет книг, <code>book_count</code> будет равен 0 (благодаря <code>LEFT JOIN</code>). <code>GROUP BY p.id</code> — группируем по издательству, чтобы <code>COUNT()</code> работал правильно. <code>ORDER BY p.name</code> — сортируем по названию издательства. Если есть поисковый запрос, добавляем <code>WHERE p.name LIKE ? OR p.address LIKE ?</code> с подстановкой <code>%$search%</code> (ищем вхождение подстроки).</p>

                        <p>Страница получилась короткой и понятной. Все сложные операции (сохранение, удаление, генерация формы, поисковая строка) вынесены в <code>helpers.php</code>. Нам осталось только указать необходимые поля в функциях и написать простой SQL-запрос для получения списка с учётом поиска.</p>
                        
                                            
                   <?php endif; ?>
                 </div>

                <?php if (isTeacher()): ?>
                <form method="post" action="/toggle-topic.php" class="section-edit-form" id="sef-web8" data-key="web8" style="display:none">
                    <input type="hidden" name="action" value="save_section">
                    <input type="hidden" name="course" value="web">
                    <input type="hidden" name="topic_key" value="web8">
                    <input type="hidden" name="back" value="/web-course.php#web8">
                    <input type="text" name="title" class="section-title-input" value="<?= htmlspecialchars($web8Title) ?>" placeholder="Заголовок темы">
                    <div id="editor-web8"></div>
                    <textarea name="content" style="display:none"></textarea>
                    <div style="margin-top:8px; display:flex; gap:8px">
                        <button type="submit" class="btn btn-primary btn-small">Сохранить</button>
                        <button type="button" class="btn btn-small" onclick="toggleSectionEdit('web','web8')">Отмена</button>
                    </div>
                </form>
                <?php endif; ?>

                <?= topicButton('web', 'web8', 'web9') ?>
            </article>

                <!-- 4.9 -->
                <article id="web9" class="lesson">
                <?php
                    $web9Title = getCourseSectionTitle('web', 'web9') ?? '4.9. Страница читателей';
                ?>
                <h3><?= htmlspecialchars($web9Title) ?><?php if (isTeacher()): ?> <button class="edit-section-btn" onclick="toggleSectionEdit('web','web9')">Редактировать тему</button><?php endif; ?></h3>

                <div class="text-content" id="sc-web9">
                    <?php $__sc = getCourseSection('web', 'web9'); if ($__sc !== null): echo $__sc; else: ?>
                        <p>Теперь создадим страницу <code>users.php</code>, которая управляет читателями библиотеки. Она будет во многом похожа на <code>publishers.php</code>, но добавится новая возможность – просмотр карточки читателя с историей выданных книг. Это будет сделано через новое действие (<code>$action</code>) <code>view</code>, которое мы реализуем отдельно.</p>
                        <p>Как и раньше, мы используем универсальные функции из <code>helpers.php</code> для сохранения, удаления, отрисовки формы и поисковой строки.</p>

                        <p>Начинаем создание страницы с подключения необходимых файлов и определения <code>$action</code> и <code>$id</code>. Эта часть никак не отличается от того, что мы делали в файлах <code>authors.php</code> и <code>publishers.php</code>.</p>

                        <p><strong>Обработка POST-запроса сохранения данных</strong>, отличие от предыдущих файлов лишь в содержании массива <code>$data</code>.</p>

                        <div class="content-placeholder">
                            <?= TAB1 ?>if ($_SERVER['REQUEST_METHOD'] === 'POST') {<br>
                            <?= TAB2 ?>$data = [<br>
                            <?= TAB2 ?><?= TAB1 ?>'first_name'        => trim($_POST['first_name'] ?? ''),<br>
                            <?= TAB2 ?><?= TAB1 ?>'last_name'         => trim($_POST['last_name'] ?? ''),<br>
                            <?= TAB2 ?><?= TAB1 ?>'registration_date' => $_POST['registration_date'] ?: date('Y-m-d'),<br>
                            <?= TAB2 ?><?= TAB1 ?>'birth_date'        => $_POST['birth_date'] ?: null,<br>
                            <?= TAB2 ?>];<br>
                            <?= TAB2 ?>saveRecord('Users', $data, $id, ['first_name', 'last_name'], 'Читатель', 'users.php');<br>
                            <?= TAB1 ?>}
                        </div>

                        <p>Если не указали дату регистрации, подставляем текущую дату через <code>date('Y-m-d')</code>, а <code>birth_date</code> – может быть пустым.</p>

                        <p><strong>Удаление читателя</strong> возможно только если у него нет записей о выдаче книг. В противном случае сработает защита внешнего ключа.</p>

                        <div class="content-placeholder">
                            <?= TAB1 ?>if ($action === 'delete' && $id) {<br>
                            <?= TAB2 ?>deleteRecord('Users', $id, 'Читатель удалён', 'Нельзя удалить: есть записи о выдаче', 'users.php');<br>
                            <?= TAB1 ?>}
                        </div>

                        <p>Базовый функционал сделан, теперь перейдем к добавлению совершенно нового действия – просмотр карточки читателя (<code>action=view</code>). При переходе по ссылке <code>users.php?action=view&amp;id=...</code> будем показывать подробную информацию о читателе и список всех книг, которые он когда-либо брал.</p>

                        <div class="content-placeholder">
                            <strong>Листинг 4.9.1. Карточка читателя</strong><br>
                            <?= TAB1 ?>if ($action === 'view' && $id) {<br>
                            <?= TAB2 ?>// Загружаем данные читателя<br>
                            <?= TAB2 ?>$user = row("SELECT * FROM Users WHERE id=?", [$id]);<br>
                            <?= TAB2 ?>if (!$user) {<br>
                            <?= TAB2 ?><?= TAB1 ?>flash('error', 'Читатель не найден');<br>
                            <?= TAB2 ?><?= TAB1 ?>go('users.php');<br>
                            <?= TAB2 ?>}<br><br>
                            <?= TAB2 ?>// Загружаем историю выдач<br>
                            <?= TAB2 ?>$borrows = query(" <br>
                            <?= TAB2 ?>    SELECT br.borrow_date, br.due_date, br.return_date, b.title<br>
                            <?= TAB2 ?>    FROM Borrow_records br<br>
                            <?= TAB2 ?>    JOIN Book_copies bc ON br.book_copy_id = bc.id<br>
                            <?= TAB2 ?>    JOIN Books b        ON bc.book_id      = b.id<br>
                            <?= TAB2 ?>    WHERE br.user_id = ?<br>
                            <?= TAB2 ?>    ORDER BY br.borrow_date DESC<br>
                            <?= TAB2 ?>", [$id]);<br><br>
                            <?= TAB2 ?>pageTop($user['first_name'] . ' ' . $user['last_name'], 'users');<br>
                            <?= TAB2 ?>echo getFlash();<br>
                            <?= TAB2 ?>breadcrumb(['users.php' => 'Читатели', '' => $user['first_name'] . ' ' . $user['last_name']]);<br>
                            <?= TAB2 ?>?&gt;<br>
                            &lt;div class="user-card"&gt;<br>
                            <?= TAB2 ?>&lt;div class="user-avatar"&gt;&lt;?= mb_substr($user['first_name'], 0, 1) ?&gt;&lt;/div&gt;<br>
                            <?= TAB2 ?>&lt;div&gt;<br>
                            <?= TAB2 ?>&lt;div class="user-name"&gt;&lt;?= e($user['first_name'] . ' ' . $user['last_name']) ?&gt;&lt;/div&gt;<br>
                            <?= TAB2 ?>&lt;div class="user-meta"&gt;<br>
                            <?= TAB2 ?>    Зарегистрирован: &lt;?= dateRu($user['registration_date']) ?&gt;<br>
                            <?= TAB2 ?>    &lt;?= $user['birth_date'] ? ' · Дата рождения: ' . dateRu($user['birth_date']) : '' ?&gt;<br>
                            <?= TAB2 ?>&lt;/div&gt;<br>
                            <?= TAB2 ?>&lt;div style="margin-top:10px;display:flex;gap:8px"&gt;<br>
                            <?= TAB2 ?>    &lt;a href="?action=edit&amp;id=&lt;?= $id ?&gt;" class="btn btn-outline btn-sm"&gt;Редактировать&lt;/a&gt;<br>
                            <?= TAB2 ?>    &lt;a href="borrows.php?action=add&amp;user_id=&lt;?= $id ?&gt;" class="btn btn-primary btn-sm"&gt;Выдать книгу&lt;/a&gt;<br>
                            <?= TAB2 ?>&lt;/div&gt;<br>
                            <?= TAB2 ?>&lt;/div&gt;<br>
                            &lt;/div&gt;<br>
                            &lt;div class="table-card"&gt;<br>
                            <?= TAB2 ?>&lt;div class="widget-head" style="padding:14px 18px"&gt;История выдач (&lt;?= count($borrows) ?&gt;)&lt;/div&gt;<br>
                            <?= TAB2 ?>&lt;table class="data-table"&gt;<br>
                            <?= TAB2 ?>&lt;thead&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?>&lt;tr&gt;&lt;th&gt;Книга&lt;/th&gt;&lt;th&gt;Выдана&lt;/th&gt;&lt;th&gt;Срок&lt;/th&gt;&lt;th&gt;Возвращена&lt;/th&gt;&lt;th&gt;Статус&lt;/th&gt;&lt;/tr&gt;<br>
                            <?= TAB2 ?>&lt;/thead&gt;<br>
                            <?= TAB2 ?>&lt;tbody&gt;<br>
                            &lt;?php if ($borrows): ?&gt;<br>
                            &lt;?php foreach ($borrows as $r):<br>
                            <?= TAB2 ?><?= TAB1 ?>$overdue = !$r['return_date'] && $r['due_date'] &lt; date('Y-m-d');<br>
                            ?&gt;<br>
                            &lt;tr class="&lt;?= $overdue ? 'overdue' : '' ?&gt;"&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?>&lt;td&gt;&lt;?= e($r['title']) ?&gt;&lt;/td&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?>&lt;td&gt;&lt;?= dateRu($r['borrow_date']) ?&gt;&lt;/td&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?>&lt;td&gt;&lt;?= dateRu($r['due_date']) ?&gt;&lt;/td&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?>&lt;td&gt;&lt;?= dateRu($r['return_date']) ?&gt;&lt;/td&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?>&lt;td&gt;<br>
                            &lt;?php if ($r['return_date']): ?&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?>&lt;span class="badge green"&gt;Возвращена&lt;/span&gt;<br>
                            &lt;?php elseif ($overdue): ?&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?>&lt;span class="badge red"&gt;Просрочена&lt;/span&gt;<br>
                            &lt;?php else: ?&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?>&lt;span class="badge blue"&gt;Активна&lt;/span&gt;<br>
                            &lt;?php endif ?&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?>&lt;/td&gt;<br>
                            &lt;/tr&gt;<br>
                            &lt;?php endforeach ?&gt;<br>
                            &lt;?php else: ?&gt;<br>
                            &lt;tr&gt;&lt;td colspan="5"&gt;&lt;div class="empty"&gt;&lt;div class="empty-text"&gt;Выдач не было&lt;/div&gt;&lt;/div&gt;&lt;/td&gt;&lt;/tr&gt;<br>
                            &lt;?php endif ?&gt;<br>
                            <?= TAB2 ?>&lt;/tbody&gt;<br>
                            <?= TAB2 ?>&lt;/table&gt;<br>
                            &lt;/div&gt;<br>
                            &lt;?php<br>
                            <?= TAB2 ?>pageBottom();<br>
                            <?= TAB2 ?>exit;<br>
                            <?= TAB1 ?>}
                        </div>

                        <p>Сначала загружаем данные читателя через <code>row()</code>, если читатель не найден – показываем ошибку и перенаправляем на список. Затем запросом <code>query()</code> получаем все записи о выдачах этого читателя, объединяя таблицы <code>Borrow_records</code>, <code>Book_copies</code> и <code>Books</code>, и сортируем по дате выдачи (от новых к старым).</p>
                        <p>Выводим карточку читателя: аватар (первая буква имени), имя, даты регистрации и рождения, а также кнопки «Редактировать» и «Выдать книгу».</p>
                        <p>Ниже показываем таблицу с историей выдач. Для каждой строки определяем статус (возвращена, просрочена, активна) и добавляем класс <code>overdue</code> для просроченных записей (CSS покрасит их в красноватый фон). Если выдач нет – выводим пустое состояние.</p>

                        <p><strong>Форма добавления/редактирования</strong> читателя выполняется аналогично <code>authors.php</code> и <code>publishers.php</code>.</p>

                        <div class="content-placeholder">
                            <?= TAB1 ?>if ($action === 'add' || $action === 'edit') {<br>
                            <?= TAB2 ?>$user = $id ? row("SELECT * FROM Users WHERE id=?", [$id]) : ['id'=>0,'first_name'=>'','last_name'=>'','registration_date'=>date('Y-m-d'),'birth_date'=>''];<br>
                            <?= TAB2 ?>pageTop($id ? 'Редактировать читателя' : 'Добавить читателя', 'users');<br>
                            <?= TAB2 ?>echo getFlash();<br>
                            <?= TAB2 ?>breadcrumb(['users.php' => 'Читатели', '' => $id ? 'Редактировать' : 'Добавить']);<br><br>
                            <?= TAB2 ?>$fields = [<br>
                            <?= TAB2 ?><?= TAB1 ?>['label'=>'Имя', 'type'=>'text', 'name'=>'first_name', 'value'=>$user['first_name'], 'required'=>true, 'attrs'=>'maxlength="100"'],<br>
                            <?= TAB2 ?><?= TAB1 ?>['label'=>'Фамилия', 'type'=>'text', 'name'=>'last_name', 'value'=>$user['last_name'], 'required'=>true, 'attrs'=>'maxlength="100"'],<br>
                            <?= TAB2 ?><?= TAB1 ?>['label'=>'Дата регистрации', 'type'=>'date', 'name'=>'registration_date', 'value'=>$user['registration_date']],<br>
                            <?= TAB2 ?><?= TAB1 ?>['label'=>'Дата рождения', 'type'=>'date', 'name'=>'birth_date', 'value'=>$user['birth_date']],<br>
                            <?= TAB2 ?>];<br>
                            <?= TAB2 ?>renderForm('', $fields, 'Сохранить', 'users.php');<br>
                            <?= TAB2 ?>pageBottom();<br>
                            <?= TAB2 ?>exit;<br>
                            <?= TAB1 ?>}
                        </div>

                        <p><strong>Вывод списка читателей.</strong> Если ни одно из предыдущих условий не сработало, показываем таблицу со всеми читателями. Поиск осуществляется по имени и фамилии, а фамилия читателя будет являться ссылкой на карточку (<code>?action=view&amp;id=...</code>). Также добавим кнопку просмотра рядом с кнопками редактирования и удаления.</p>

                        <div class="content-placeholder">
                            <?= TAB1 ?>$search = trim($_GET['q'] ?? '');<br><br>
                            <?= TAB1 ?>if ($search) {<br>
                            <?= TAB2 ?>$users = query("<br>
                            <?= TAB2 ?>    SELECT * FROM Users<br>
                            <?= TAB2 ?>    WHERE CONCAT(first_name, ' ', last_name) LIKE ?<br>
                            <?= TAB2 ?>    ORDER BY last_name<br>
                            <?= TAB2 ?>", ["%$search%"]);<br>
                            <?= TAB1 ?>} else {<br>
                            <?= TAB2 ?>$users = query("SELECT * FROM Users ORDER BY last_name");<br>
                            <?= TAB1 ?>}<br><br>
                            <?= TAB1 ?>pageTop('Читатели', 'users');<br>
                            <?= TAB1 ?>echo getFlash();<br>
                            <?= TAB1 ?>renderSearchBar($search, [], 'users.php?action=add');<br>
                            <?= TAB1 ?>?&gt;<br>
                            &lt;table class="data-table"&gt;<br>
                            &lt;thead&gt;<br>
                            &lt;tr&gt;&lt;th&gt;#&lt;/th&gt;&lt;th&gt;Фамилия&lt;/th&gt;&lt;th&gt;Имя&lt;/th&gt;&lt;th&gt;Дата рождения&lt;/th&gt;&lt;th&gt;Регистрация&lt;/th&gt;&lt;th&gt;Действия&lt;/th&gt;&lt;/tr&gt;<br>
                            &lt;/thead&gt;<br>
                            &lt;tbody&gt;<br>
                            &lt;?php if ($users): foreach ($users as $u): ?&gt;<br>
                            &lt;tr&gt;<br>
                            &lt;td class="td-muted"&gt;&lt;?= $u['id'] ?&gt;&lt;/td&gt;<br>
                            &lt;td&gt;&lt;strong&gt;&lt;a href="?action=view&amp;id=&lt;?= $u['id'] ?&gt;" style="color:var(--primary);text-decoration:none"&gt;&lt;?= e($u['last_name']) ?&gt;&lt;/a&gt;&lt;/strong&gt;&lt;/td&gt;<br>
                            &lt;td&gt;&lt;?= e($u['first_name']) ?&gt;&lt;/td&gt;<br>
                            &lt;td&gt;&lt;?= dateRu($u['birth_date']) ?&gt;&lt;/td&gt;<br>
                            &lt;td&gt;&lt;?= dateRu($u['registration_date']) ?&gt;&lt;/td&gt;<br>
                            &lt;td style="display:flex;gap:6px"&gt;<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&lt;a href="?action=view&amp;id=&lt;?= $u['id'] ?&gt;" class="btn btn-outline btn-sm"&gt;Карточка&lt;/a&gt;<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&lt;a href="?action=edit&amp;id=&lt;?= $u['id'] ?&gt;" class="btn btn-outline btn-sm"&gt;✎&lt;/a&gt;<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&lt;a href="?action=delete&amp;id=&lt;?= $u['id'] ?&gt;" class="btn btn-danger btn-sm" onclick="return confirm('Удалить читателя?')"&gt;✕&lt;/a&gt;<br>
                            &lt;/td&gt;<br>
                            &lt;/tr&gt;<br>
                            &lt;?php endforeach; else: renderEmptyState(6, 'Читатели не найдены'); endif; ?&gt;<br>
                            &lt;/tbody&gt;<br>
                            &lt;/table&gt;<br>
                            &lt;?php pageBottom(); ?&gt;
                        </div>
                         <?php endif; ?>
                        </div>

                        <?php if (isTeacher()): ?>
                        <form method="post" action="/toggle-topic.php" class="section-edit-form" id="sef-web9" data-key="web9" style="display:none">
                            <input type="hidden" name="action" value="save_section">
                            <input type="hidden" name="course" value="web">
                            <input type="hidden" name="topic_key" value="web9">
                            <input type="hidden" name="back" value="/web-course.php#web9">
                            <input type="text" name="title" class="section-title-input" value="<?= htmlspecialchars($web9Title) ?>" placeholder="Заголовок темы">
                            <div id="editor-web9"></div>
                            <textarea name="content" style="display:none"></textarea>
                            <div style="margin-top:8px; display:flex; gap:8px">
                                <button type="submit" class="btn btn-primary btn-small">Сохранить</button>
                                <button type="button" class="btn btn-small" onclick="toggleSectionEdit('web','web9')">Отмена</button>
                            </div>
                        </form>
                        <?php endif; ?>

                        <?= topicButton('web', 'web9', 'web10') ?>
                    </article>

                <!-- 4.10 -->
                <article id="web10" class="lesson">
                <?php
                    $web10Title = getCourseSectionTitle('web', 'web10') ?? '4.10. Страница книг';
                ?>
                <h3><?= htmlspecialchars($web10Title) ?><?php if (isTeacher()): ?> <button class="edit-section-btn" onclick="toggleSectionEdit('web','web10')">✎</button><?php endif; ?></h3>

                <div class="text-content" id="sc-web10">
                    <?php $__sc = getCourseSection('web', 'web10'); if ($__sc !== null): echo $__sc; else: ?>
                        <p>Теперь мы переходим к самой информативной странице приложения – управлению книгами. Книга – центральная сущность библиотеки. Она связана с автором и с издательством. Кроме того, у книги может быть несколько физических экземпляров (<code>Book_copies</code>), которые выдаются читателям.</p>
                        <p>Таким образом, на странице <code>books.php</code> мы реализуем: список всех книг с поиском по названию/автору/жанру, добавление/удаление/редактирование книг, отображение количества экземпляров и доступных для выдачи.</p>

                        <p>Как обычно, начинаем с подключения модулей и запуска сессии. Определяем <code>$action</code> и <code>$id</code>.</p>
                        <p>В форме добавления/редактирования книги нам понадобятся выпадающие списки авторов и издательств. Также для поля «Жанр» мы сделаем удобный элемент <code>datalist</code> – он позволяет выбирать из существующих жанров или вводить новый. Поэтому перед обработкой POST (и вообще в любом месте, где может понадобиться форма) мы получим эти данные. Однако, чтобы не делать лишних запросов при показе списка, мы вынесем их в отдельные переменные только внутри блока формы. Но для фильтрации жанров в списке книг нам тоже нужен список всех жанров – получим его заранее.</p>

                        <div class="content-placeholder">
                            <?= TAB1 ?>$genres = query("SELECT DISTINCT genre FROM Books WHERE genre IS NOT NULL ORDER BY genre");
                        </div>

                        <p>Этот запрос выбирает уникальные значения жанров из таблицы <code>Books</code>, исключая пустые значения, и сортирует по алфавиту.</p>

                        <p><strong>Обработка POST-запроса сохранения.</strong> Собираем данные в массив <code>$data</code> и вызываем <code>saveRecord()</code>. <code>author_id</code> и <code>publisher_id</code> – это числовые идентификаторы, поэтому приводим их к целому типу. Для <code>genre</code> и <code>page_count</code> делаем проверку на пустоту и подставляем <code>null</code> при необходимости. <code>age_limit</code> по умолчанию 0.</p>

                        <div class="content-placeholder">
                            <?= TAB1 ?>if ($_SERVER['REQUEST_METHOD'] === 'POST') {<br>
                            <?= TAB2 ?>$data = [<br>
                            <?= TAB2 ?><?= TAB1 ?>'title'            => trim($_POST['title'] ?? ''),<br>
                            <?= TAB2 ?><?= TAB1 ?>'author_id'        => (int)($_POST['author_id'] ?? 0),<br>
                            <?= TAB2 ?><?= TAB1 ?>'publisher_id'     => (int)($_POST['publisher_id'] ?? 0),<br>
                            <?= TAB2 ?><?= TAB1 ?>'genre'            => trim($_POST['genre'] ?? '') ?: null,<br>
                            <?= TAB2 ?><?= TAB1 ?>'page_count'       => (int)($_POST['page_count'] ?? 0) ?: null,<br>
                            <?= TAB2 ?><?= TAB1 ?>'age_limit'        => (int)($_POST['age_limit'] ?? 0),<br>
                            <?= TAB2 ?><?= TAB1 ?>'publication_date' => $_POST['publication_date'] ?: null,<br>
                            <?= TAB2 ?>];<br>
                            <?= TAB2 ?>saveRecord('Books', $data, $id, ['title', 'author_id', 'publisher_id'], 'Книга', 'books.php');<br>
                            <?= TAB1 ?>}
                        </div>

                        <p><strong>Удалить книгу</strong> можно только если у неё нет ни одного экземпляра (<code>Book_copies</code>). Если экземпляры есть, сработает защита внешнего ключа, и <code>deleteRecord()</code> покажет сообщение об ошибке.</p>

                        <div class="content-placeholder">
                            <?= TAB1 ?>if ($action === 'delete' && $id) {<br>
                            <?= TAB2 ?>deleteRecord('Books', $id, 'Книга удалена', 'Нельзя удалить: есть связанные экземпляры', 'books.php');<br>
                            <?= TAB1 ?>}
                        </div>

                        <p><strong>Если action равен add или edit, мы должны показать форму.</strong> Здесь нам понадобятся списки авторов и издательств, а также список жанров для <code>datalist</code>. Получим их из базы.</p>

                        <div class="content-placeholder">
                            <strong>Листинг 4.10.1. Форма добавления/редактирования книги</strong><br>
                            <?= TAB1 ?>if ($action === 'add' || $action === 'edit') {<br>
                            <?= TAB2 ?>// Загружаем данные книги, если редактируем<br>
                            <?= TAB2 ?>$book = $id ? row("SELECT * FROM Books WHERE id=?", [$id]) : [<br>
                            <?= TAB2 ?><?= TAB1 ?>'id' => 0,<br>
                            <?= TAB2 ?><?= TAB1 ?>'title' => '',<br>
                            <?= TAB2 ?><?= TAB1 ?>'author_id' => 0,<br>
                            <?= TAB2 ?><?= TAB1 ?>'publisher_id' => 0,<br>
                            <?= TAB2 ?><?= TAB1 ?>'genre' => '',<br>
                            <?= TAB2 ?><?= TAB1 ?>'page_count' => '',<br>
                            <?= TAB2 ?><?= TAB1 ?>'age_limit' => 0,<br>
                            <?= TAB2 ?><?= TAB1 ?>'publication_date' => ''<br>
                            <?= TAB2 ?>];<br><br>
                            <?= TAB2 ?>// Получаем списки для выпадающих меню<br>
                            <?= TAB2 ?>$authors    = query("SELECT id, CONCAT(first_name, ' ', last_name) AS name FROM Authors ORDER BY last_name");<br>
                            <?= TAB2 ?>$publishers = query("SELECT id, name FROM Publishers ORDER BY name");<br><br>
                            <?= TAB2 ?>// Преобразуем массивы в формат, удобный для renderForm (options)<br>
                            <?= TAB2 ?>$authorOptions = ['' => '— выберите автора —'];<br>
                            <?= TAB2 ?>foreach ($authors as $a) {<br>
                            <?= TAB2 ?><?= TAB1 ?>$authorOptions[$a['id']] = $a['name'];<br>
                            <?= TAB2 ?>}<br><br>
                            <?= TAB2 ?>$publisherOptions = ['' => '— выберите издательство —'];<br>
                            <?= TAB2 ?>foreach ($publishers as $p) {<br>
                            <?= TAB2 ?><?= TAB1 ?>$publisherOptions[$p['id']] = $p['name'];<br>
                            <?= TAB2 ?>}<br><br>
                            <?= TAB2 ?>$ageOptions = [0 => '0+', 6 => '6+', 12 => '12+', 16 => '16+', 18 => '18+'];<br><br>
                            <?= TAB2 ?>pageTop($id ? 'Редактировать книгу' : 'Добавить книгу', 'books');<br>
                            <?= TAB2 ?>echo getFlash();<br>
                            <?= TAB2 ?>breadcrumb(['books.php' => 'Книги', '' => $id ? 'Редактировать' : 'Добавить']);<br><br>
                            <?= TAB2 ?>$fields = [<br>
                            <?= TAB2 ?><?= TAB1 ?>['label'=>'Название', 'type'=>'text', 'name'=>'title', 'value'=>$book['title'], 'required'=>true, 'attrs'=>'maxlength="300"', 'full'=>true],<br>
                            <?= TAB2 ?><?= TAB1 ?>['label'=>'Автор', 'type'=>'select', 'name'=>'author_id', 'value'=>$book['author_id'], 'required'=>true, 'options'=>$authorOptions],<br>
                            <?= TAB2 ?><?= TAB1 ?>['label'=>'Издательство', 'type'=>'select', 'name'=>'publisher_id', 'value'=>$book['publisher_id'], 'required'=>true, 'options'=>$publisherOptions],<br>
                            <?= TAB2 ?><?= TAB1 ?>['label'=>'Жанр', 'type'=>'text', 'name'=>'genre', 'value'=>$book['genre'], 'attrs'=>'maxlength="100" list="genres-list"'],<br>
                            <?= TAB2 ?><?= TAB1 ?>['label'=>'Дата публикации', 'type'=>'date', 'name'=>'publication_date', 'value'=>$book['publication_date']],<br>
                            <?= TAB2 ?><?= TAB1 ?>['label'=>'Количество страниц', 'type'=>'number', 'name'=>'page_count', 'value'=>$book['page_count'], 'attrs'=>'min="1"'],<br>
                            <?= TAB2 ?><?= TAB1 ?>['label'=>'Возрастное ограничение', 'type'=>'select', 'name'=>'age_limit', 'value'=>$book['age_limit'], 'options'=>$ageOptions],<br>
                            <?= TAB2 ?>];<br><br>
                            <?= TAB2 ?>// Выводим datalist для поля "Жанр" – он будет доступен для автодополнения<br>
                            <?= TAB2 ?>echo '&lt;datalist id="genres-list"&gt;';<br>
                            <?= TAB2 ?>foreach ($genres as $g) {<br>
                            <?= TAB2 ?><?= TAB1 ?>echo '&lt;option value="' . e($g['genre']) . '"&gt;';<br>
                            <?= TAB2 ?>}<br>
                            <?= TAB2 ?>echo '&lt;/datalist&gt;';<br><br>
                            <?= TAB2 ?>renderForm('', $fields, 'Сохранить', 'books.php');<br>
                            <?= TAB2 ?>pageBottom();<br>
                            <?= TAB2 ?>exit;<br>
                            <?= TAB1 ?>}
                        </div>

                        <p>Список авторов получаем с помощью <code>CONCAT(first_name, ' ', last_name)</code>, чтобы в выпадающем списке отображались полные имена. Для <code>renderForm()</code> мы готовим ассоциативные массивы <code>$authorOptions</code> и <code>$publisherOptions</code>, где ключ – <code>id</code>, значение – текст. Первый элемент – пустая строка с подсказкой «— выберите —».</p>
                        <p>Поле «Жанр» – обычное текстовое, но с атрибутом <code>list="genres-list"</code>, который связывает его с элементом <code>&lt;datalist&gt;</code>. Благодаря этому пользователь может начать вводить текст и выбрать подходящий жанр из уже существующих.</p>
                        <p>Поле «Возрастное ограничение» – выпадающий список с фиксированными значениями.</p>
                        <p>Мы выводим <code>&lt;datalist&gt;</code> отдельно, перед вызовом <code>renderForm()</code>, потому что <code>renderForm()</code> не умеет сама генерировать <code>datalist</code> (но может быть расширена). В нашем случае это просто дополнительный HTML-код.</p>

                        <p><strong>Список книг.</strong> Теперь самое интересное – список книг. Мы выводим все книги, но с возможностью поиска по названию или автору, а также фильтрации по жанру. Постраничная навигация отсутствует – все книги на одной странице. Для каждой книги мы также показываем количество экземпляров (<code>total_copies</code>) и количество доступных (<code>avail_copies</code>).</p>

                        <p>Сначала получаем параметры поиска и фильтра из <code>$_GET</code>:</p>
                        <div class="content-placeholder">
                            <?= TAB1 ?>$search = trim($_GET['q'] ?? '');<br>
                            <?= TAB1 ?>$genreFilter = trim($_GET['genre'] ?? '');
                        </div>

                        <p>Теперь формируем SQL-запрос. Нам нужно выбрать данные из таблицы <code>Books</code>, присоединить автора (<code>Authors</code>) и издателя (<code>Publishers</code>), а также подзапросами подсчитать общее количество экземпляров и доступных.</p>

                        <div class="content-placeholder">
                            <strong>Листинг 4.10.2. Запрос для получения списка книг</strong><br>
                            <?= TAB1 ?>$sql = "<br>
                            <?= TAB2 ?>SELECT <br>
                            <?= TAB2 ?>    b.*,<br>
                            <?= TAB2 ?>    CONCAT(a.first_name, ' ', a.last_name) AS author_name,<br>
                            <?= TAB2 ?>    p.name AS publisher_name,<br>
                            <?= TAB2 ?>    (SELECT COUNT(*) FROM Book_copies WHERE book_id = b.id) AS total_copies,<br>
                            <?= TAB2 ?>    (SELECT COUNT(*) FROM Book_copies WHERE book_id = b.id AND status = 'available') AS avail_copies<br>
                            <?= TAB2 ?>FROM Books b<br>
                            <?= TAB2 ?>JOIN Authors a ON b.author_id = a.id<br>
                            <?= TAB2 ?>JOIN Publishers p ON b.publisher_id = p.id<br>
                            <?= TAB1 ?>";<br><br>
                            <?= TAB1 ?>// Добавляем условия поиска и фильтра<br>
                            <?= TAB1 ?>$where = [];<br>
                            <?= TAB1 ?>$params = [];<br><br>
                            <?= TAB1 ?>if ($search) {<br>
                            <?= TAB2 ?>$where[] = "(b.title LIKE ? OR CONCAT(a.first_name, ' ', a.last_name) LIKE ?)";<br>
                            <?= TAB2 ?>$params[] = "%$search%";<br>
                            <?= TAB2 ?>$params[] = "%$search%";<br>
                            <?= TAB1 ?>}<br>
                            <?= TAB1 ?>if ($genreFilter) {<br>
                            <?= TAB2 ?>$where[] = "b.genre = ?";<br>
                            <?= TAB2 ?>$params[] = $genreFilter;<br>
                            <?= TAB1 ?>}<br><br>
                            <?= TAB1 ?>if ($where) {<br>
                            <?= TAB2 ?>$sql .= " WHERE " . implode(' AND ', $where);<br>
                            <?= TAB1 ?>}<br><br>
                            <?= TAB1 ?>$sql .= " ORDER BY b.title";<br><br>
                            <?= TAB1 ?>$books = query($sql, $params);
                        </div>

                        <p><strong>Объяснение:</strong></p>
                        <ul>
                            <li>Мы используем подзапросы (<code>SELECT COUNT(*)</code> ...) для подсчёта экземпляров. Это удобно, потому что не требует дополнительных <code>JOIN</code> и <code>GROUP BY</code>, которые могли бы дублировать строки.</li>
                            <li>Поиск идёт по двум полям: названию книги (<code>b.title</code>) и полному имени автора (склеенному через <code>CONCAT</code>). Обратите внимание: в <code>WHERE</code> мы используем <code>CONCAT(a.first_name, ' ', a.last_name)</code>, что совпадает с тем, как мы формируем <code>author_name</code> в основном запросе.</li>
                            <li>Фильтр по жанру добавляет условие <code>b.genre = ?</code>.</li>
                            <li>Если оба условия присутствуют, они соединяются через <code>AND</code>.</li>
                            <li>Сортировка по названию книги.</li>
                        </ul>

                        <p>Теперь выводим страницу:</p>

                        <div class="content-placeholder">
                            <strong>Листинг 4.10.3. Вывод списка книг</strong><br>
                            <?= TAB1 ?>pageTop('Книги', 'books');<br>
                            <?= TAB1 ?>echo getFlash();<br><br>
                            <?= TAB1 ?>// Готовим опции для фильтра по жанру<br>
                            <?= TAB1 ?>$genreOptions = ['' => 'Все жанры'];<br>
                            <?= TAB1 ?>foreach ($genres as $g) {<br>
                            <?= TAB2 ?>$genreOptions[$g['genre']] = $g['genre'];<br>
                            <?= TAB1 ?>}<br><br>
                            <?= TAB1 ?>// Передаём в renderSearchBar массив с одним фильтром<br>
                            <?= TAB1 ?>$filters = [<br>
                            <?= TAB2 ?>['name' => 'genre', 'placeholder' => 'Все жанры', 'value' => $genreFilter, 'options' => $genreOptions]<br>
                            <?= TAB1 ?>];<br><br>
                            <?= TAB1 ?>renderSearchBar($search, $filters, 'books.php?action=add');<br>
                            <?= TAB1 ?>?&gt;<br><br>
                            &lt;table class="data-table"&gt;<br>
                            &lt;thead&gt;<br>
                            &lt;tr&gt;<br>
                            &lt;th&gt;#&lt;/th&gt;&lt;th&gt;Название&lt;/th&gt;&lt;th&gt;Автор&lt;/th&gt;&lt;th&gt;Издательство&lt;/th&gt;<br>
                            &lt;th&gt;Жанр&lt;/th&gt;&lt;th&gt;Страниц&lt;/th&gt;&lt;th&gt;Возраст&lt;/th&gt;&lt;th&gt;Год&lt;/th&gt;<br>
                            &lt;th&gt;Экз. / Доступно&lt;/th&gt;&lt;th&gt;Действия&lt;/th&gt;<br>
                            &lt;/tr&gt;<br>
                            &lt;/thead&gt;<br>
                            &lt;tbody&gt;<br>
                            &lt;?php if ($books): foreach ($books as $b): ?&gt;<br>
                            &lt;tr&gt;<br>
                            &lt;td class="td-muted"&gt;&lt;?= $b['id'] ?&gt;&lt;/td&gt;<br>
                            &lt;td&gt;&lt;strong&gt;&lt;?= e($b['title']) ?&gt;&lt;/strong&gt;&lt;/td&gt;<br>
                            &lt;td&gt;&lt;?= e($b['author_name']) ?&gt;&lt;/td&gt;<br>
                            &lt;td&gt;&lt;?= e($b['publisher_name']) ?&gt;&lt;/td&gt;<br>
                            &lt;td&gt;&lt;?= e($b['genre'] ?? '—') ?&gt;&lt;/td&gt;<br>
                            &lt;td&gt;&lt;?= $b['page_count'] ?? '—' ?&gt;&lt;/td&gt;<br>
                            &lt;td&gt;&lt;?= $b['age_limit'] ?&gt;+&lt;/td&gt;<br>
                            &lt;td&gt;&lt;?= $b['publication_date'] ? date('Y', strtotime($b['publication_date'])) : '—' ?&gt;&lt;/td&gt;<br>
                            &lt;td&gt;&lt;?= $b['total_copies'] ?&gt; / &lt;strong&gt;&lt;?= $b['avail_copies'] ?&gt;&lt;/strong&gt;&lt;/td&gt;<br>
                            &lt;td style="display:flex;gap:6px"&gt;<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&lt;a href="?action=edit&amp;id=&lt;?= $b['id'] ?&gt;" class="btn btn-outline btn-sm"&gt;✎&lt;/a&gt;<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&lt;a href="copies.php?book_id=&lt;?= $b['id'] ?&gt;" class="btn btn-outline btn-sm"&gt;Экземпляры&lt;/a&gt;<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&lt;a href="?action=delete&amp;id=&lt;?= $b['id'] ?&gt;" class="btn btn-danger btn-sm" <br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;onclick="return confirm('Удалить книгу «&lt;?= e(addslashes($b['title'])) ?&gt;»?')"&gt;✕&lt;/a&gt;<br>
                            &lt;/td&gt;<br>
                            &lt;/tr&gt;<br>
                            &lt;?php endforeach; else: renderEmptyState(10, 'Книги не найдены'); endif; ?&gt;<br>
                            &lt;/tbody&gt;<br>
                            &lt;/table&gt;<br><br>
                            &lt;?php pageBottom(); ?&gt;
                        </div>

                        <p><strong>Пояснения к таблице:</strong></p>
                        <ul>
                            <li>В столбце «Год» мы извлекаем только год из даты публикации с помощью <code>date('Y', strtotime(...))</code>.</li>
                            <li>Количество экземпляров и доступных показано дробью: <code>total_copies / avail_copies</code>.</li>
                            <li>Кнопка «Экземпляры» ведёт на страницу <code>copies.php</code> с параметром <code>book_id</code>, чтобы показать все экземпляры конкретной книги.</li>
                            <li>При удалении в диалоге подтверждения выводится название книги (экранированное через <code>addslashes</code>, чтобы кавычки не сломали JavaScript).</li>
                            <li>В <code>renderEmptyState</code> передаём <code>colspan=10</code> – количество столбцов в таблице.</li>
                        </ul>

                        <p><strong>Итог:</strong> Теперь читатель может добавлять книги, указывая автора и издательство, и видеть, сколько экземпляров есть в библиотеке. Следующий шаг – управление экземплярами (<code>copies.php</code>), где мы добавим возможность массового добавления копий и изменения их статуса.</p>
                     <?php endif; ?>
                </div>

                <?php if (isTeacher()): ?>
                <form method="post" action="/toggle-topic.php" class="section-edit-form" id="sef-web10" data-key="web10" style="display:none">
                    <input type="hidden" name="action" value="save_section">
                    <input type="hidden" name="course" value="web">
                    <input type="hidden" name="topic_key" value="web10">
                    <input type="hidden" name="back" value="/web-course.php#web10">
                    <input type="text" name="title" class="section-title-input" value="<?= htmlspecialchars($web10Title) ?>" placeholder="Заголовок темы">
                    <div id="editor-web10"></div>
                    <textarea name="content" style="display:none"></textarea>
                    <div style="margin-top:8px; display:flex; gap:8px">
                        <button type="submit" class="btn btn-primary btn-small">Сохранить</button>
                        <button type="button" class="btn btn-small" onclick="toggleSectionEdit('web','web10')">Отмена</button>
                    </div>
                </form>
                <?php endif; ?>

                <?= topicButton('web', 'web10', 'web11') ?>
            </article>

                <!-- 4.11 -->
                <article id="web11" class="lesson">
                <?php
                    $web11Title = getCourseSectionTitle('web', 'web11') ?? '4.11. Страница экземпляров';
                ?>
                <h3><?= htmlspecialchars($web11Title) ?><?php if (isTeacher()): ?> <button class="edit-section-btn" onclick="toggleSectionEdit('web','web11')">Редактировать тему</button><?php endif; ?></h3>

                <div class="text-content" id="sc-web11">
                    <?php $__sc = getCourseSection('web', 'web11'); if ($__sc !== null): echo $__sc; else: ?>
                        <p>После того как мы научились добавлять книги, авторов и издательства, нужно позаботиться о физических экземплярах. Одна книга может существовать в библиотеке в нескольких экземплярах: например, роман «Война и мир» может быть в трёх экземплярах, и каждый из них может находиться в разном состоянии (доступен, выдан, утерян, повреждён). Страница <code>copies.php</code> отвечает за управление этими экземплярами.</p>

                        <p>На этой странице мы реализуем:</p>
                        <ul>
                            <li>список всех экземпляров с возможностью фильтрации по конкретной книге (через параметр <code>book_id</code> в URL);</li>
                            <li>добавление одного или нескольких экземпляров одной книги одновременно;</li>
                            <li>редактирование статуса экземпляра;</li>
                            <li>удаление экземпляра (с проверкой – есть ли у него записи о выдаче);</li>
                            <li>кнопку «Выдать» для доступных экземпляров, которая ведёт на страницу выдачи с предварительно выбранным экземпляром.</li>
                        </ul>

                        <p>Как и прежде, мы будем использовать универсальные функции, но для массового добавления нам понадобится небольшая дополнительная логика.</p>

                        <p>В начале файла, как обычно, подключаем модули, запускаем сессию, определяем <code>$action</code> и <code>$id</code>. Также нам нужно получить параметр <code>book_id</code> из <code>$_GET</code> – он нужен для фильтрации списка и для предварительного выбора книги в форме добавления.</p>

                        <div class="content-placeholder">
                            <?= TAB1 ?>$bookFilter = (int)($_GET['book_id'] ?? 0);   // фильтр по книге
                        </div>

                        <p><code>$bookFilter</code> будет использоваться в двух местах: в условии <code>WHERE</code> для списка экземпляров и в качестве предустановленного значения <code>book_id</code> в форме добавления.</p>

                        <p>В форме добавления/редактирования экземпляра нужно выбрать книгу. Поэтому мы заранее получим список всех книг (id и название) из базы.</p>

                        <div class="content-placeholder">
                            <?= TAB1 ?>$books = query("SELECT id, title FROM Books ORDER BY title");
                        </div>

                        <p>Этот массив понадобится и для фильтрации (чтобы показать название книги в заголовке, если фильтр активен), и для выпадающего списка в форме.</p>

                        <p><strong>Обработка POST-запроса (сохранение).</strong> Здесь есть важное отличие от предыдущих страниц: при добавлении новых экземпляров пользователь может указать количество копий (от 1 до 50), и мы должны создать указанное число записей в таблице <code>Book_copies</code>. При редактировании (изменении статуса) – обновляем только один экземпляр.</p>

                        <div class="content-placeholder">
                            <strong>Листинг 4.11.1. Обработка POST в copies.php</strong><br>
                            <?= TAB1 ?>if ($_SERVER['REQUEST_METHOD'] === 'POST') {<br>
                            <?= TAB2 ?>$book_id = (int)($_POST['book_id'] ?? 0);<br>
                            <?= TAB2 ?>$status  = $_POST['status'] ?? 'available';<br>
                            <?= TAB2 ?>$count   = max(1, min(50, (int)($_POST['count'] ?? 1)));<br><br>
                            <?= TAB2 ?>if (!$book_id) {<br>
                            <?= TAB2 ?><?= TAB1 ?>flash('error', 'Выберите книгу');<br>
                            <?= TAB2 ?><?= TAB1 ?>go($bookFilter ? "copies.php?book_id=$bookFilter" : 'copies.php');<br>
                            <?= TAB2 ?>}<br><br>
                            <?= TAB2 ?>if ($id) {<br>
                            <?= TAB2 ?><?= TAB1 ?>// Режим редактирования: обновляем статус одного экземпляра<br>
                            <?= TAB2 ?><?= TAB1 ?>run("UPDATE Book_copies SET book_id=?, status=? WHERE id=?", [$book_id, $status, $id]);<br>
                            <?= TAB2 ?><?= TAB1 ?>flash('success', 'Экземпляр обновлён');<br>
                            <?= TAB2 ?>} else {<br>
                            <?= TAB2 ?><?= TAB1 ?>// Режим добавления: создаём $count новых экземпляров<br>
                            <?= TAB2 ?><?= TAB1 ?>for ($i = 0; $i < $count; $i++) {<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?>run("INSERT INTO Book_copies (book_id, status) VALUES (?,?)", [$book_id, $status]);<br>
                            <?= TAB2 ?><?= TAB1 ?>}<br>
                            <?= TAB2 ?><?= TAB1 ?>flash('success', "Добавлено экземпляров: $count");<br>
                            <?= TAB2 ?>}<br><br>
                            <?= TAB2 ?>// После сохранения перенаправляем обратно, сохраняя фильтр по книге<br>
                            <?= TAB2 ?>go($bookFilter ? "copies.php?book_id=$bookFilter" : 'copies.php');<br>
                            <?= TAB1 ?>}
                        </div>

                        <p><strong>Пояснения:</strong></p>
                        <ul>
                            <li><code>$book_id</code> – идентификатор книги, к которой относится экземпляр (обязательное поле).</li>
                            <li><code>$status</code> – состояние экземпляра: <code>available</code> (доступна), <code>borrowed</code> (выдана), <code>damaged</code> (повреждена), <code>lost</code> (утеряна).</li>
                            <li><code>$count</code> – количество добавляемых экземпляров. Мы ограничиваем его от 1 до 50 с помощью <code>max(1, min(50, ...))</code>, чтобы пользователь случайно не создал тысячи записей.</li>
                            <li>Если <code>$id</code> передан (режим редактирования), обновляем только один экземпляр.</li>
                            <li>Если <code>$id == 0</code> (добавление), запускаем цикл <code>for</code>, который выполняется <code>$count</code> раз и вставляет новые записи.</li>
                            <li>После сохранения перенаправляем на ту же страницу, сохраняя параметр <code>book_id</code>, если он был (чтобы фильтр не сбросился).</li>
                        </ul>

                        <p><strong>Удаление экземпляра</strong> возможно только если у него нет связанных записей в таблице <code>Borrow_records</code> (то есть он никогда не выдавался или уже возвращён). В противном случае сработает внешний ключ.</p>

                        <div class="content-placeholder">
                            <?= TAB1 ?>if ($action === 'delete' && $id) {<br>
                            <?= TAB2 ?>deleteRecord('Book_copies', $id, 'Экземпляр удалён', 'Нельзя удалить: есть записи о выдаче', <br>
                            <?= TAB2 ?><?= TAB1 ?>$bookFilter ? "copies.php?book_id=$bookFilter" : 'copies.php');<br>
                            <?= TAB1 ?>}
                        </div>

                        <p><strong>Форма добавления/редактирования.</strong> Форма для экземпляров отличается от предыдущих тем, что при добавлении мы показываем поле <code>count</code> (количество копий), а при редактировании – только выбор книги и статуса. Также мы можем предварительно выбрать книгу, если перешли с <code>books.php</code> через параметр <code>book_id</code>.</p>

                        <div class="content-placeholder">
                            <strong>Листинг 4.11.2. Форма для экземпляров</strong><br>
                            <?= TAB1 ?>if ($action === 'add' || $action === 'edit') {<br>
                            <?= TAB2 ?>// Загружаем данные экземпляра, если редактируем<br>
                            <?= TAB2 ?>if ($id) {<br>
                            <?= TAB2 ?><?= TAB1 ?>$copy = row("SELECT * FROM Book_copies WHERE id=?", [$id]);<br>
                            <?= TAB2 ?><?= TAB1 ?>if (!$copy) {<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?>flash('error', 'Экземпляр не найден');<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?>go($bookFilter ? "copies.php?book_id=$bookFilter" : 'copies.php');<br>
                            <?= TAB2 ?><?= TAB1 ?>}<br>
                            <?= TAB2 ?>} else {<br>
                            <?= TAB2 ?><?= TAB1 ?>$copy = ['id' => 0, 'book_id' => $bookFilter, 'status' => 'available'];<br>
                            <?= TAB2 ?>}<br><br>
                            <?= TAB2 ?>$pageTitle = $id ? 'Редактировать экземпляр' : 'Добавить экземпляры';<br>
                            <?= TAB2 ?>pageTop($pageTitle, 'copies');<br>
                            <?= TAB2 ?>echo getFlash();<br><br>
                            <?= TAB2 ?>// Если есть фильтр по книге, ссылка ведёт на copies.php с book_id<br>
                            <?= TAB2 ?>$backUrl = $bookFilter ? "copies.php?book_id=$bookFilter" : 'copies.php';<br>
                            <?= TAB2 ?>breadcrumb([$backUrl => 'Экземпляры', '' => $id ? 'Редактировать' : 'Добавить']);<br><br>
                            <?= TAB2 ?>// Подготавливаем options для выпадающих списков<br>
                            <?= TAB2 ?>$bookOptions = ['' => '— выберите книгу —'];<br>
                            <?= TAB2 ?>foreach ($books as $b) {<br>
                            <?= TAB2 ?><?= TAB1 ?>$bookOptions[$b['id']] = $b['title'];<br>
                            <?= TAB2 ?>}<br><br>
                            <?= TAB2 ?>$statusOptions = [<br>
                            <?= TAB2 ?><?= TAB1 ?>'available' => 'Доступна',<br>
                            <?= TAB2 ?><?= TAB1 ?>'borrowed'  => 'Выдана',<br>
                            <?= TAB2 ?><?= TAB1 ?>'damaged'   => 'Повреждена',<br>
                            <?= TAB2 ?><?= TAB1 ?>'lost'      => 'Утеряна'<br>
                            <?= TAB2 ?>];<br><br>
                            <?= TAB2 ?>$fields = [<br>
                            <?= TAB2 ?><?= TAB1 ?>['label'=>'Книга', 'type'=>'select', 'name'=>'book_id', 'value'=>$copy['book_id'], 'required'=>true, 'options'=>$bookOptions, 'full'=>true],<br>
                            <?= TAB2 ?><?= TAB1 ?>['label'=>'Статус', 'type'=>'select', 'name'=>'status', 'value'=>$copy['status'], 'options'=>$statusOptions],<br>
                            <?= TAB2 ?>];<br><br>
                            <?= TAB2 ?>// При добавлении добавляем поле для количества экземпляров<br>
                            <?= TAB2 ?>if (!$id) {<br>
                            <?= TAB2 ?><?= TAB1 ?>$fields[] = ['label'=>'Количество экземпляров (до 50)', 'type'=>'number', 'name'=>'count', 'value'=>1, 'attrs'=>'min="1" max="50"'];<br>
                            <?= TAB2 ?>}<br><br>
                            <?= TAB2 ?>renderForm('', $fields, 'Сохранить', $backUrl);<br>
                            <?= TAB2 ?>pageBottom();<br>
                            <?= TAB2 ?>exit;<br>
                            <?= TAB1 ?>}
                        </div>

                        <p><strong>Что важно:</strong></p>
                        <ul>
                            <li>При редактировании мы не показываем поле <code>count</code>, потому что изменяем только один экземпляр.</li>
                            <li>Поле <code>book_id</code> обязательно, но при добавлении через ссылку с <code>book_id</code> оно уже предзаполнено.</li>
                            <li>Кнопка «Отмена» ведёт на страницу списка экземпляров, сохраняя фильтр по книге (если он был).</li>
                        </ul>

                        <p><strong>Вывод списка экземпляров (таблица).</strong> Теперь самая важная часть – таблица экземпляров. Мы выводим все экземпляры, но если передан параметр <code>book_id</code>, то показываем только экземпляры указанной книги. Также мы показываем название книги (даже если фильтр не активен) и статус в виде цветного бейджа.</p>

                        <div class="content-placeholder">
                            <strong>Листинг 4.11.3. Таблица экземпляров</strong><br>
                            <?= TAB1 ?>// Формируем условие WHERE<br>
                            <?= TAB1 ?>$where = $bookFilter ? "WHERE bc.book_id = $bookFilter" : '';<br><br>
                            <?= TAB1 ?>// Получаем список экземпляров<br>
                            <?= TAB1 ?>$copies = query("<br>
                            <?= TAB2 ?>    SELECT bc.*, b.title AS book_title<br>
                            <?= TAB2 ?>    FROM Book_copies bc<br>
                            <?= TAB2 ?>    JOIN Books b ON bc.book_id = b.id<br>
                            <?= TAB2 ?>    $where<br>
                            <?= TAB2 ?>    ORDER BY b.title, bc.id<br>
                            <?= TAB1 ?>");<br><br>
                            <?= TAB1 ?>// Если есть фильтр по книге, получаем её название для заголовка<br>
                            <?= TAB1 ?>if ($bookFilter) {<br>
                            <?= TAB2 ?>$bookName = val("SELECT title FROM Books WHERE id=?", [$bookFilter]);<br>
                            <?= TAB2 ?>$pageTitle = "Экземпляры книги: $bookName";<br>
                            <?= TAB1 ?>} else {<br>
                            <?= TAB2 ?>$pageTitle = "Все экземпляры";<br>
                            <?= TAB1 ?>}<br><br>
                            <?= TAB1 ?>pageTop($pageTitle, 'copies');<br>
                            <?= TAB1 ?>echo getFlash();<br><br>
                            <?= TAB1 ?>// Если есть фильтр по книге, показываем хлебные крошки для навигации<br>
                            <?= TAB1 ?>if ($bookFilter) {<br>
                            <?= TAB2 ?>breadcrumb(['books.php' => 'Книги', 'copies.php' => 'Экземпляры', '' => $bookName ?? '']);<br>
                            <?= TAB1 ?>}<br><br>
                            <?= TAB1 ?>// Выводим панель инструментов: общее количество и кнопка добавления<br>
                            <?= TAB1 ?>$total = count($copies);<br>
                            <?= TAB1 ?>?&gt;<br>
                            &lt;div class="table-card"&gt;<br>
                            &lt;div class="table-toolbar"&gt;<br>
                            &lt;span class="table-total"&gt;Всего экземпляров: &lt;?= $total ?&gt;&lt;/span&gt;<br>
                            &lt;a href="copies.php?action=add&lt;?= $bookFilter ? "&amp;book_id=$bookFilter" : '' ?&gt;" class="btn btn-primary btn-sm"&gt;+ Добавить экземпляр&lt;/a&gt;<br>
                            &lt;/div&gt;<br><br>
                            &lt;table class="data-table"&gt;<br>
                            &lt;thead&gt;<br>
                            &lt;tr&gt;&lt;th&gt;#&lt;/th&gt;&lt;th&gt;Книга&lt;/th&gt;&lt;th&gt;Статус&lt;/th&gt;&lt;th&gt;Действия&lt;/th&gt;&lt;/tr&gt;<br>
                            &lt;/thead&gt;<br>
                            &lt;tbody&gt;<br>
                            &lt;?php if ($copies): foreach ($copies as $c): ?&gt;<br>
                            &lt;tr&gt;<br>
                            &lt;td class="td-muted"&gt;&lt;?= $c['id'] ?&gt;&lt;/td&gt;<br>
                            &lt;td&gt;&lt;?= e($c['book_title']) ?&gt;&lt;/td&gt;<br>
                            &lt;td&gt;&lt;?= statusLabel($c['status']) ?&gt;&lt;/td&gt;<br>
                            &lt;td style="display:flex;gap:6px"&gt;<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&lt;a href="?action=edit&amp;id=&lt;?= $c['id'] ?&gt;&lt;?= $bookFilter ? "&amp;book_id=$bookFilter" : '' ?&gt;" class="btn btn-outline btn-sm"&gt;✎&lt;/a&gt;<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&lt;a href="?action=delete&amp;id=&lt;?= $c['id'] ?&gt;&lt;?= $bookFilter ? "&amp;book_id=$bookFilter" : '' ?&gt;" class="btn btn-danger btn-sm" <br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;onclick="return confirm('Удалить экземпляр #&lt;?= $c['id'] ?&gt;?')"&gt;✕&lt;/a&gt;<br>
                            &lt;?php if ($c['status'] === 'available'): ?&gt;<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&lt;a href="borrows.php?action=add&amp;copy_id=&lt;?= $c['id'] ?&gt;" class="btn btn-primary btn-sm"&gt;Выдать&lt;/a&gt;<br>
                            &lt;?php endif ?&gt;<br>
                            &lt;/td&gt;<br>
                            &lt;/tr&gt;<br>
                            &lt;?php endforeach; else: renderEmptyState(4, 'Экземпляров не найдено'); endif; ?&gt;<br>
                            &lt;/tbody&gt;<br>
                            &lt;/table&gt;<br>
                            &lt;/div&gt;<br><br>
                            &lt;?php pageBottom(); ?&gt;
                        </div>

                        <p><strong>Пояснения:</strong></p>
                        <ul>
                            <li>Запрос <code>JOIN</code> с таблицей <code>Books</code> нужен, чтобы получить название книги для каждого экземпляра.</li>
                            <li>Если <code>$bookFilter</code> не ноль, добавляется <code>WHERE bc.book_id = $bookFilter</code>.</li>
                            <li>Сортировка по названию книги, а внутри – по <code>id</code> экземпляра.</li>
                            <li>Кнопка «Выдать» появляется только для экземпляров со статусом <code>available</code> и ведёт на страницу <code>borrows.php</code> с параметром <code>copy_id</code>, что позволяет быстро начать выдачу.</li>
                            <li>В ссылках на редактирование и удаление мы сохраняем параметр <code>book_id</code>, чтобы после действия фильтр не сбросился.</li>
                        </ul>

                        <p>Для отображения статуса используется функция <code>statusLabel()</code>, которая возвращает цветной бейдж. В <code>helpers.php</code> добавим новую функцию, которая преобразует код статуса в HTML-бейдж:</p>

                        <div class="content-placeholder">
                            <strong>Листинг 4.11.4. Функция statusLabel()</strong><br>
                            <?= TAB1 ?>function statusLabel(string $status): string {<br>
                            <?= TAB2 ?>return match($status) {<br>
                            <?= TAB2 ?><?= TAB1 ?>'available' => '&lt;span class="badge green"&gt;Доступна&lt;/span&gt;',<br>
                            <?= TAB2 ?><?= TAB1 ?>'borrowed'  => '&lt;span class="badge blue"&gt;Выдана&lt;/span&gt;',<br>
                            <?= TAB2 ?><?= TAB1 ?>'damaged'   => '&lt;span class="badge orange"&gt;Повреждена&lt;/span&gt;',<br>
                            <?= TAB2 ?><?= TAB1 ?>'lost'      => '&lt;span class="badge red"&gt;Утеряна&lt;/span&gt;',<br>
                            <?= TAB2 ?><?= TAB1 ?>default     => '&lt;span class="badge"&gt;?&lt;/span&gt;',<br>
                            <?= TAB2 ?>};<br>
                            <?= TAB1 ?>}
                        </div>

                        <p>Благодаря этому в таблице статусы выглядят наглядно и цвет соответствует состоянию.</p>
                    <?php endif; ?>
                    </div>

                    <?php if (isTeacher()): ?>
                    <form method="post" action="/toggle-topic.php" class="section-edit-form" id="sef-web11" data-key="web11" style="display:none">
                        <input type="hidden" name="action" value="save_section">
                        <input type="hidden" name="course" value="web">
                        <input type="hidden" name="topic_key" value="web11">
                        <input type="hidden" name="back" value="/web-course.php#web11">
                        <input type="text" name="title" class="section-title-input" value="<?= htmlspecialchars($web11Title) ?>" placeholder="Заголовок темы">
                        <div id="editor-web11"></div>
                        <textarea name="content" style="display:none"></textarea>
                        <div style="margin-top:8px; display:flex; gap:8px">
                            <button type="submit" class="btn btn-primary btn-small">Сохранить</button>
                            <button type="button" class="btn btn-small" onclick="toggleSectionEdit('web','web11')">Отмена</button>
                        </div>
                    </form>
                    <?php endif; ?>

                    <?= topicButton('web', 'web11', 'web12') ?>
                </article>


                <!-- 4.12 -->
                <article id="web12" class="lesson">
                <?php
                    $web12Title = getCourseSectionTitle('web', 'web12') ?? '4.12. Страница выдачи книг';
                ?>
                <h3><?= htmlspecialchars($web12Title) ?><?php if (isTeacher()): ?> <button class="edit-section-btn" onclick="toggleSectionEdit('web','web12')">Редактировать тему</button><?php endif; ?></h3>

                <div class="text-content" id="sc-web12">
                    <?php $__sc = getCourseSection('web', 'web12'); if ($__sc !== null): echo $__sc; else: ?>
                        <p>Страница <code>borrows.php</code> — ключевая и заключительная в нашем приложении, потому что именно здесь происходят основные действия библиотеки: выдача книг читателям и возврат. Здесь мы будем работать сразу с несколькими таблицами: <code>Borrow_records</code> (записи о выдаче), <code>Users</code> (читатели), <code>Book_copies</code> (экземпляры книг) и <code>Books</code> (книги). При выдаче нужно не только создать запись в <code>Borrow_records</code>, но и изменить статус экземпляра на <code>borrowed</code>. При возврате – установить дату возврата и вернуть статус экземпляра в <code>available</code>.</p>

                        <p>На странице <code>borrows.php</code> мы реализуем:</p>
                        <ul>
                            <li>Список всех выдач с возможностью фильтрации по статусу (все, активные, просроченные, возвращённые). Для каждой выдачи показываем книгу, экземпляр, читателя, даты выдачи и срока, а также фактическую дату возврата (если книга возвращена). Строки с просроченными выдачами подсвечиваются красным фоном.</li>
                            <li>Форму выдачи новой книги, где нужно выбрать читателя и свободный экземпляр. Дата выдачи подставляется текущая, а срок возврата должен быть в будущем. При отправке формы создаётся запись в <code>Borrow_records</code> и обновляется статус экземпляра.</li>
                            <li>Обработку возврата – нажатие на кнопку «Вернуть» обновляет запись: устанавливает <code>return_date</code> на текущую дату и меняет статус экземпляра обратно на <code>available</code>.</li>
                            <li>Как и на других страницах, мы будем использовать универсальные функции из <code>helpers.php</code> для форм, поиска и фильтров, но здесь нет универсального <code>saveRecord()</code>, потому что операция выдачи затрагивает две таблицы.</li>
                        </ul>

                        <p>В начале файла, как обычно, подключаем модули, запускаем сессию, определяем <code>$action</code> и <code>$id</code>.</p>

                        <p><strong>Возврат книги</strong> – это самое простое действие. Когда администратор нажимает кнопку «Вернуть» рядом с записью о выдаче, мы передаём <code>?action=return&amp;id=...</code>. Нам нужно найти эту запись, получить <code>book_copy_id</code>, обновить <code>return_date</code>, а затем изменить статус экземпляра.</p>

                        <div class="content-placeholder">
                            <strong>Листинг 4.12.1. Обработка возврата книги</strong><br>
                            <?= TAB1 ?>if ($action === 'return' && $id) {<br>
                            <?= TAB2 ?>// Получаем запись о выдаче и заодно book_copy_id<br>
                            <?= TAB2 ?>$record = row("<br>
                            <?= TAB2 ?>    SELECT br.*, bc.id AS copy_id <br>
                            <?= TAB2 ?>    FROM Borrow_records br <br>
                            <?= TAB2 ?>    JOIN Book_copies bc ON br.book_copy_id = bc.id <br>
                            <?= TAB2 ?>    WHERE br.id = ?<br>
                            <?= TAB2 ?>", [$id]);<br><br>
                            <?= TAB2 ?>if ($record && !$record['return_date']) {<br>
                            <?= TAB2 ?><?= TAB1 ?>// Устанавливаем дату возврата (сегодня)<br>
                            <?= TAB2 ?><?= TAB1 ?>run("UPDATE Borrow_records SET return_date = CURDATE() WHERE id = ?", [$id]);<br>
                            <?= TAB2 ?><?= TAB1 ?>// Возвращаем экземпляр в доступные<br>
                            <?= TAB2 ?><?= TAB1 ?>run("UPDATE Book_copies SET status = 'available' WHERE id = ?", [$record['copy_id']]);<br>
                            <?= TAB2 ?><?= TAB1 ?>flash('success', 'Книга отмечена как возвращённая');<br>
                            <?= TAB2 ?>} else {<br>
                            <?= TAB2 ?><?= TAB1 ?>flash('error', 'Ошибка: запись не найдена или книга уже возвращена');<br>
                            <?= TAB2 ?>}<br>
                            <?= TAB2 ?>go('borrows.php');<br>
                            <?= TAB1 ?>}
                        </div>

                        <p><strong>Пояснения:</strong> <code>CURDATE()</code> – функция MySQL, возвращающая текущую дату в формате <code>ГГГГ-ММ-ДД</code>. Важно проверить, что книга ещё не возвращена (<code>!$record['return_date']</code>), чтобы случайно не обработать возврат дважды. Мы используем <code>JOIN</code> с <code>Book_copies</code>, чтобы сразу получить <code>copy_id</code> – идентификатор экземпляра, который нужно освободить.</p>

                        <p><strong>Выдача новой книги (POST-запрос).</strong> Когда администратор заполняет форму выдачи и нажимает «Выдать книгу», приходит POST-запрос. Здесь нужно:</p>
                        <ol>
                            <li>Проверить, что выбраны читатель, экземпляр и указан срок возврата.</li>
                            <li>Убедиться, что выбранный экземпляр имеет статус <code>available</code> (свободен).</li>
                            <li>Создать запись в <code>Borrow_records</code>.</li>
                            <li>Изменить статус экземпляра на <code>borrowed</code>.</li>
                            <li>Показать сообщение об успехе и перенаправить на список выдач.</li>
                        </ol>

                        <div class="content-placeholder">
                            <strong>Листинг 4.12.2. Обработка выдачи книги (POST)</strong><br>
                            <?= TAB1 ?>if ($_SERVER['REQUEST_METHOD'] === 'POST') {<br>
                            <?= TAB2 ?>$user_id = (int)($_POST['user_id'] ?? 0);<br>
                            <?= TAB2 ?>$copy_id = (int)($_POST['book_copy_id'] ?? 0);<br>
                            <?= TAB2 ?>$borrow_date = $_POST['borrow_date'] ?: date('Y-m-d');<br>
                            <?= TAB2 ?>$due_date    = $_POST['due_date'] ?? '';<br><br>
                            <?= TAB2 ?>// Базовая проверка<br>
                            <?= TAB2 ?>if (!$user_id || !$copy_id || !$due_date) {<br>
                            <?= TAB2 ?><?= TAB1 ?>flash('error', 'Заполните все обязательные поля');<br>
                            <?= TAB2 ?><?= TAB1 ?>go('borrows.php?action=add');<br>
                            <?= TAB2 ?>}<br><br>
                            <?= TAB2 ?>// Проверяем, что экземпляр доступен<br>
                            <?= TAB2 ?>$status = val("SELECT status FROM Book_copies WHERE id = ?", [$copy_id]);<br>
                            <?= TAB2 ?>if ($status !== 'available') {<br>
                            <?= TAB2 ?><?= TAB1 ?>flash('error', 'Этот экземпляр недоступен для выдачи (уже выдан, повреждён или утерян)');<br>
                            <?= TAB2 ?><?= TAB1 ?>go('borrows.php?action=add');<br>
                            <?= TAB2 ?>}<br><br>
                            <?= TAB2 ?>// Начинаем транзакцию (для согласованности данных)<br>
                            <?= TAB2 ?>$pdo = db();<br>
                            <?= TAB2 ?>try {<br>
                            <?= TAB2 ?><?= TAB1 ?>$pdo->beginTransaction();<br><br>
                            <?= TAB2 ?><?= TAB1 ?>// Вставляем запись о выдаче<br>
                            <?= TAB2 ?><?= TAB1 ?>run("<br>
                            <?= TAB2 ?><?= TAB1 ?>    INSERT INTO Borrow_records (user_id, book_copy_id, borrow_date, due_date) <br>
                            <?= TAB2 ?><?= TAB1 ?>    VALUES (?, ?, ?, ?)<br>
                            <?= TAB2 ?><?= TAB1 ?>", [$user_id, $copy_id, $borrow_date, $due_date]);<br><br>
                            <?= TAB2 ?><?= TAB1 ?>// Обновляем статус экземпляра<br>
                            <?= TAB2 ?><?= TAB1 ?>run("UPDATE Book_copies SET status = 'borrowed' WHERE id = ?", [$copy_id]);<br><br>
                            <?= TAB2 ?><?= TAB1 ?>$pdo->commit();<br>
                            <?= TAB2 ?><?= TAB1 ?>flash('success', 'Книга выдана');<br>
                            <?= TAB2 ?>} catch (PDOException $e) {<br>
                            <?= TAB2 ?><?= TAB1 ?>$pdo->rollBack();<br>
                            <?= TAB2 ?><?= TAB1 ?>flash('error', 'Ошибка при выдаче. Попробуйте снова.');<br>
                            <?= TAB2 ?>}<br><br>
                            <?= TAB2 ?>go('borrows.php');<br>
                            <?= TAB1 ?>}
                        </div>

                        <p><strong>Важные детали:</strong> Мы используем транзакцию (<code>beginTransaction</code>, <code>commit</code>, <code>rollBack</code>), чтобы обе операции (вставка в <code>Borrow_records</code> и обновление <code>Book_copies</code>) выполнились вместе или не выполнились вовсе. Это гарантирует целостность данных. Перед выдачей повторно проверяем статус экземпляра – даже если в форме выбрали экземпляр, пока пользователь заполнял форму, его могли выдать другому читателю, поэтому такая проверка обязательна.</p>

                        <p><strong>Форма выдачи книги.</strong> Форма использует выпадающие списки: читатели (из таблицы <code>Users</code>) и свободные экземпляры (из <code>Book_copies</code> со статусом <code>available</code>). Также можно передать параметры <code>copy_id</code> или <code>user_id</code> через GET (например, со страницы списка экземпляров или карточки читателя), чтобы предустановить значения.</p>

                        <div class="content-placeholder">
                            <strong>Листинг 4.12.3. Форма выдачи книги (action=add)</strong><br>
                            <?= TAB1 ?>if ($action === 'add') {<br>
                            <?= TAB2 ?>// Получаем параметры для предзаполнения из GET (если есть)<br>
                            <?= TAB2 ?>$preCopyId = (int)($_GET['copy_id'] ?? 0);<br>
                            <?= TAB2 ?>$preUserId = (int)($_GET['user_id'] ?? 0);<br><br>
                            <?= TAB2 ?>// Список читателей (ФИО)<br>
                            <?= TAB2 ?>$users = query("SELECT id, CONCAT(first_name, ' ', last_name) AS name FROM Users ORDER BY last_name");<br>
                            <?= TAB2 ?>// Список доступных экземпляров (с названием книги)<br>
                            <?= TAB2 ?>$copies = query("<br>
                            <?= TAB2 ?>    SELECT bc.id, CONCAT(b.title, ' (экз. #', bc.id, ')') AS label<br>
                            <?= TAB2 ?>    FROM Book_copies bc<br>
                            <?= TAB2 ?>    JOIN Books b ON bc.book_id = b.id<br>
                            <?= TAB2 ?>    WHERE bc.status = 'available'<br>
                            <?= TAB2 ?>    ORDER BY b.title, bc.id<br>
                            <?= TAB2 ?>");<br><br>
                            <?= TAB2 ?>$userOptions = ['' => '— выберите читателя —'];<br>
                            <?= TAB2 ?>foreach ($users as $u) {<br>
                            <?= TAB2 ?><?= TAB1 ?>$userOptions[$u['id']] = $u['name'];<br>
                            <?= TAB2 ?>}<br><br>
                            <?= TAB2 ?>$copyOptions = ['' => '— выберите экземпляр —'];<br>
                            <?= TAB2 ?>foreach ($copies as $c) {<br>
                            <?= TAB2 ?><?= TAB1 ?>$copyOptions[$c['id']] = $c['label'];<br>
                            <?= TAB2 ?>}<br><br>
                            <?= TAB2 ?>pageTop('Выдать книгу', 'borrows');<br>
                            <?= TAB2 ?>echo getFlash();<br>
                            <?= TAB2 ?>breadcrumb(['borrows.php' => 'Выдача', '' => 'Новая выдача']);<br><br>
                            <?= TAB2 ?>$fields = [<br>
                            <?= TAB2 ?><?= TAB1 ?>['label' => 'Читатель', 'type' => 'select', 'name' => 'user_id', 'value' => $preUserId, 'required' => true, 'options' => $userOptions, 'full' => true],<br>
                            <?= TAB2 ?><?= TAB1 ?>['label' => 'Экземпляр книги', 'type' => 'select', 'name' => 'book_copy_id', 'value' => $preCopyId, 'required' => true, 'options' => $copyOptions, 'full' => true],<br>
                            <?= TAB2 ?><?= TAB1 ?>['label' => 'Дата выдачи', 'type' => 'date', 'name' => 'borrow_date', 'value' => date('Y-m-d')],<br>
                            <?= TAB2 ?><?= TAB1 ?>['label' => 'Срок возврата', 'type' => 'date', 'name' => 'due_date', 'value' => '', 'required' => true, 'attrs' => 'min="' . date('Y-m-d') . '"'],<br>
                            <?= TAB2 ?>];<br><br>
                            <?= TAB2 ?>renderForm('', $fields, 'Выдать книгу', 'borrows.php');<br>
                            <?= TAB2 ?>pageBottom();<br>
                            <?= TAB2 ?>exit;<br>
                            <?= TAB1 ?>}
                        </div>

                        <p><strong>Обратите внимание:</strong> В поле <code>due_date</code> мы добавляем атрибут <code>min</code>, чтобы браузер не позволял выбрать дату в прошлом. Список доступных экземпляров формируется с помощью <code>CONCAT(b.title, ' (экз. #', bc.id, ')')</code>, чтобы пользователь видел название книги и номер экземпляра. Параметры <code>copy_id</code> и <code>user_id</code> из GET позволяют быстро открыть форму выдачи со страницы списка экземпляров (кнопка «Выдать») или из карточки читателя (кнопка «Выдать книгу»).</p>

                        <p><strong>Таблица всех выдач.</strong> Теперь самое объёмное – таблица всех выдач. Мы выводим все записи, но можем фильтровать: активные (не возвращённые), просроченные (не возвращённые и срок прошёл) или возвращённые. Фильтр реализуется через выпадающий список, который передаётся в <code>renderSearchBar()</code>.</p>

                        <div class="content-placeholder">
                            <strong>Листинг 4.12.4. Таблица выдач с фильтрацией</strong><br>
                            <?= TAB1 ?>// Получаем параметры поиска и фильтра<br>
                            <?= TAB1 ?>$search = trim($_GET['q'] ?? '');<br>
                            <?= TAB1 ?>$statusFilter = $_GET['status'] ?? '';<br><br>
                            <?= TAB1 ?>// Формируем WHERE<br>
                            <?= TAB1 ?>$where = [];<br>
                            <?= TAB1 ?>$params = [];<br><br>
                            <?= TAB1 ?>if ($search) {<br>
                            <?= TAB2 ?>$where[] = "(b.title LIKE ? OR CONCAT(u.first_name, ' ', u.last_name) LIKE ?)";<br>
                            <?= TAB2 ?>$params[] = "%$search%";<br>
                            <?= TAB2 ?>$params[] = "%$search%";<br>
                            <?= TAB1 ?>}<br>
                            <?= TAB1 ?>if ($statusFilter === 'active') {<br>
                            <?= TAB2 ?>$where[] = "br.return_date IS NULL";<br>
                            <?= TAB1 ?>} elseif ($statusFilter === 'returned') {<br>
                            <?= TAB2 ?>$where[] = "br.return_date IS NOT NULL";<br>
                            <?= TAB1 ?>} elseif ($statusFilter === 'overdue') {<br>
                            <?= TAB2 ?>$where[] = "br.return_date IS NULL AND br.due_date &lt; CURDATE()";<br>
                            <?= TAB1 ?>}<br><br>
                            <?= TAB1 ?>$w = $where ? 'WHERE ' . implode(' AND ', $where) : '';<br><br>
                            <?= TAB1 ?>// Основной запрос<br>
                            <?= TAB1 ?>$borrows = query("<br>
                            <?= TAB2 ?>    SELECT <br>
                            <?= TAB2 ?>        br.*,<br>
                            <?= TAB2 ?>        b.title,<br>
                            <?= TAB2 ?>        bc.id AS copy_id,<br>
                            <?= TAB2 ?>        CONCAT(u.first_name, ' ', u.last_name) AS user_name,<br>
                            <?= TAB2 ?>        u.id AS user_id<br>
                            <?= TAB2 ?>    FROM Borrow_records br<br>
                            <?= TAB2 ?>    JOIN Users u ON br.user_id = u.id<br>
                            <?= TAB2 ?>    JOIN Book_copies bc ON br.book_copy_id = bc.id<br>
                            <?= TAB2 ?>    JOIN Books b ON bc.book_id = b.id<br>
                            <?= TAB2 ?>    $w<br>
                            <?= TAB2 ?>    ORDER BY br.borrow_date DESC, br.id DESC<br>
                            <?= TAB1 ?>", $params);<br><br>
                            <?= TAB1 ?>pageTop('Записи о выдаче', 'borrows');<br>
                            <?= TAB1 ?>echo getFlash();<br><br>
                            <?= TAB1 ?>// Готовим фильтр по статусу<br>
                            <?= TAB1 ?>$statusOptions = [<br>
                            <?= TAB2 ?>''         => 'Все',<br>
                            <?= TAB2 ?>'active'   => 'Активные',<br>
                            <?= TAB2 ?>'overdue'  => 'Просроченные',<br>
                            <?= TAB2 ?>'returned' => 'Возвращённые'<br>
                            <?= TAB1 ?>];<br>
                            <?= TAB1 ?>$filters = [['name' => 'status', 'value' => $statusFilter, 'options' => $statusOptions]];<br><br>
                            <?= TAB1 ?>renderSearchBar($search, $filters, 'borrows.php?action=add', 'Выдать книгу');<br>
                            <?= TAB1 ?>?&gt;<br><br>
                            &lt;div class="table-card"&gt;<br>
                            &lt;table class="data-table"&gt;<br>
                            &lt;thead&gt;<br>
                            &lt;tr&gt;<br>
                            &lt;th&gt;#&lt;/th&gt;&lt;th&gt;Книга&lt;/th&gt;&lt;th&gt;Экз.&lt;/th&gt;&lt;th&gt;Читатель&lt;/th&gt;<br>
                            &lt;th&gt;Выдана&lt;/th&gt;&lt;th&gt;Срок&lt;/th&gt;&lt;th&gt;Возвращена&lt;/th&gt;&lt;th&gt;Статус&lt;/th&gt;&lt;th&gt;Действия&lt;/th&gt;<br>
                            &lt;/tr&gt;<br>
                            &lt;/thead&gt;<br>
                            &lt;tbody&gt;<br>
                            &lt;?php if ($borrows): foreach ($borrows as $r): <br>
                                $overdue = !$r['return_date'] && $r['due_date'] &lt; date('Y-m-d');<br>
                            ?&gt;<br>
                            &lt;tr class="&lt;?= $overdue ? 'overdue' : '' ?&gt;"&gt;<br>
                            &lt;td class="td-muted"&gt;&lt;?= $r['id'] ?&gt;&lt;/td&gt;<br>
                            &lt;td&gt;&lt;?= e($r['title']) ?&gt;&lt;/td&gt;<br>
                            &lt;td class="td-muted"&gt;#&lt;?= $r['copy_id'] ?&gt;&lt;/td&gt;<br>
                            &lt;td&gt;&lt;a href="users.php?action=view&amp;id=&lt;?= $r['user_id'] ?&gt;" class="user-link"&gt;&lt;?= e($r['user_name']) ?&gt;&lt;/a&gt;&lt;/td&gt;<br>
                            &lt;td&gt;&lt;?= dateRu($r['borrow_date']) ?&gt;&lt;/td&gt;<br>
                            &lt;td&gt;&lt;?= dateRu($r['due_date']) ?&gt;&lt;/td&gt;<br>
                            &lt;td&gt;&lt;?= dateRu($r['return_date']) ?&gt;&lt;/td&gt;<br>
                            &lt;td&gt;<br>
                                &lt;?php if ($r['return_date']): ?&gt;<br>
                                    &lt;span class="badge green"&gt;Возвращена&lt;/span&gt;<br>
                                &lt;?php elseif ($overdue): ?&gt;<br>
                                    &lt;span class="badge red"&gt;Просрочена&lt;/span&gt;<br>
                                &lt;?php else: ?&gt;<br>
                                    &lt;span class="badge blue"&gt;Активна&lt;/span&gt;<br>
                                &lt;?php endif ?&gt;<br>
                            &lt;/td&gt;<br>
                            &lt;td&gt;<br>
                                &lt;?php if (!$r['return_date']): ?&gt;<br>
                                    &lt;a href="?action=return&amp;id=&lt;?= $r['id'] ?&gt;" class="btn btn-primary btn-sm" onclick="return confirm('Отметить книгу как возвращённую?')"&gt;Вернуть&lt;/a&gt;<br>
                                &lt;?php else: ?&gt;<br>
                                    &lt;span class="td-muted"&gt;Завершено&lt;/span&gt;<br>
                                &lt;?php endif ?&gt;<br>
                            &lt;/td&gt;<br>
                            &lt;/tr&gt;<br>
                            &lt;?php endforeach; else: renderEmptyState(9, 'Записей не найдено'); endif; ?&gt;<br>
                            &lt;/tbody&gt;<br>
                            &lt;/table&gt;<br>
                            &lt;/div&gt;<br><br>
                            &lt;?php pageBottom(); ?&gt;
                        </div>

                        <p><strong>Пояснения к таблице:</strong> <code>$overdue</code> вычисляется внутри цикла: <code>true</code>, если книга не возвращена и срок меньше сегодняшней даты. Класс <code>overdue</code> добавляется к строке <code>&lt;tr&gt;</code>, что позволяет CSS окрасить её фон (например, светло-красный). Ссылка на читателя ведёт на карточку читателя (<code>users.php?action=view&amp;id=...</code>), чтобы быстро посмотреть историю выдач. Кнопка «Вернуть» появляется только если <code>return_date</code> пуст. Для пустой таблицы используем <code>renderEmptyState()</code> с <code>colspan=9</code>.</p>

                        <p><strong>Итог.</strong> Страница <code>borrows.php</code> завершает логику нашего приложения. Она объединяет читателей и экземпляры книг, позволяя выполнять основные библиотечные операции. Здесь мы научились:</p>
                        <ul>
                            <li>Использовать транзакции для согласованного изменения нескольких таблиц.</li>
                            <li>Фильтровать список по нескольким условиям (активные, просроченные, возвращённые).</li>
                            <li>Визуально выделять просроченные выдачи с помощью CSS.</li>
                            <li>Передавать параметры через GET для предзаполнения форм (например, <code>copy_id</code> со страницы экземпляров).</li>
                        </ul>
                        <p>Теперь наше приложение полностью готово. Все страницы используют единые универсальные функции, код компактен и легко поддерживается. При необходимости добавить новую сущность (например, «Категории») достаточно создать новый файл и следовать уже отработанному шаблону.</p>
                                                                              
                <?php endif; ?>
                    </div>

                    <?php if (isTeacher()): ?>
                    <form method="post" action="/toggle-topic.php" class="section-edit-form" id="sef-web12" data-key="web12" style="display:none">
                        <input type="hidden" name="action" value="save_section">
                        <input type="hidden" name="course" value="web">
                        <input type="hidden" name="topic_key" value="web12">
                        <input type="hidden" name="back" value="/web-course.php#web12">
                        <input type="text" name="title" class="section-title-input" value="<?= htmlspecialchars($web12Title) ?>" placeholder="Заголовок темы">
                        <div id="editor-web12"></div>
                        <textarea name="content" style="display:none"></textarea>
                        <div style="margin-top:8px; display:flex; gap:8px">
                            <button type="submit" class="btn btn-primary btn-small">Сохранить</button>
                            <button type="button" class="btn btn-small" onclick="toggleSectionEdit('web','web12')">Отмена</button>
                        </div>
                    </form>
                    <?php endif; ?>

                    <?= topicButton('web', 'web12', 'web13') ?>
                </article>

                    <!-- 4.13 -->
                    <article id="web13" class="lesson">
                        <?php
                            $web13Title = getCourseSectionTitle('web', 'web13') ?? 'Готовое приложение';
                        ?>
                        <h3><?= htmlspecialchars($web13Title) ?></h3>

                        <div class="text-content" id="sc-web13">
                            <?php $__sc = getCourseSection('web', 'web13'); if ($__sc !== null): echo $__sc; else: ?>
                                <p>Приложение готово. Оно включает семь страниц: главную со статистикой, а также управление книгами, авторами, издательствами, читателями, экземплярами и выдачей книг.</p>
                                <p>Ниже можно посмотреть, как выглядит интерфейс готового приложения — нажмите кнопку, чтобы открыть интерактивный предпросмотр:</p>

                                <div class="preview-block">
                                    <a href="/library-preview.php" class="preview-btn" target="_blank">
                                        Открыть интерактивный предпросмотр приложения
                                    </a>
                                </div>

                                <p><br></p>
                                <p>Если в процессе создания приложения что-то пошло не так, вы можете скачать архив с готовым проектом и сравнить свой код с эталонным, нажав на кнопку ниже:</p>
                                <p>
                                    <a href="images/library.local.zip" download class="preview-btn" style="display:inline-block; padding: 12px 30px; text-decoration:none; font-weight:500; font-size:1rem; border-radius:8px; background:rgb(47,87,85); color:#fff; transition:background 0.2s, transform 0.1s;" onmouseover="this.style.background='rgb(90,150,144)'; this.style.transform='translateY(-2px)';" onmouseout="this.style.background='rgb(47,87,85)'; this.style.transform='translateY(0)';">
                                        Скачать архив готового проекта
                                    </a>
                                </p>
                            <?php endif; ?>
                        </div>

                        <?= topicButton('web', 'web13') ?>
                    </article>

                <!-- Динамические темы из БД -->
                <?php
                $customSections = getCustomSections('web');
                foreach ($customSections as $cs):
                    $csKey = htmlspecialchars($cs['section_key']);
                    $csTitle = htmlspecialchars($cs['title']);
                ?>
                <article id="<?= $csKey ?>" class="lesson">
                    <h3>
                        <?= $csTitle ?>
                        <?php if (isTeacher()): ?>
                        <button class="edit-section-btn" onclick="toggleSectionEdit('web','<?= $csKey ?>')">Редактировать тему</button>
                        <form method="post" action="/toggle-topic.php" style="display:inline" onsubmit="return confirm('Удалить тему «<?= $csTitle ?>»?')">
                            <input type="hidden" name="action" value="delete_section">
                            <input type="hidden" name="course" value="web">
                            <input type="hidden" name="topic_key" value="<?= $csKey ?>">
                            <input type="hidden" name="back" value="/web-course.php">
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
                        <input type="hidden" name="course" value="web">
                        <input type="hidden" name="topic_key" value="<?= $csKey ?>">
                        <input type="hidden" name="back" value="/web-course.php#<?= $csKey ?>">
                        <input type="text" name="title" class="section-title-input" value="<?= $csTitle ?>" placeholder="Заголовок темы">
                        <div id="editor-<?= $csKey ?>"></div>
                        <textarea name="content" style="display:none"></textarea>
                        <div style="margin-top:8px; display:flex; gap:8px">
                            <button type="submit" class="btn btn-primary btn-small">Сохранить</button>
                            <button type="button" class="btn btn-small" onclick="toggleSectionEdit('web','<?= $csKey ?>')">Отмена</button>
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
                    <input type="hidden" name="course" value="web">
                    <input type="hidden" name="back" value="/web-course.php">
                    <label style="display:block; font-weight:600; margin-bottom:4px">Название темы:</label>
                    <input type="text" name="title" placeholder="Например: 4.14. Дополнительные возможности" required>
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

<script>
(function () {
    var tocLinks = document.querySelectorAll('.toc-nav a[href^="#"]');
    if (!tocLinks.length) return;
    var sections = [];
    tocLinks.forEach(function (link) {
        var id = link.getAttribute('href').slice(1);
        var el = document.getElementById(id);
        if (el) sections.push({ link: link, el: el });
    });
    if (!sections.length) return;

    function setActive() {
        var pos = window.scrollY + 110;
        var current = sections[0];
        for (var i = 0; i < sections.length; i++) {
            if (sections[i].el.offsetTop <= pos) current = sections[i];
        }
        if (current.link.classList.contains('toc-active')) return;
        tocLinks.forEach(function (l) { l.classList.remove('toc-active'); });
        current.link.classList.add('toc-active');
        if (current.link.scrollIntoView) {
            current.link.scrollIntoView({ block: 'nearest' });
        }
    }

    var ticking = false;
    window.addEventListener('scroll', function () {
        if (!ticking) {
            requestAnimationFrame(function () { setActive(); ticking = false; });
            ticking = true;
        }
    }, { passive: true });
    setActive();
})();
</script>

<?php require_once __DIR__ . '/templates/footer.php'; ?>