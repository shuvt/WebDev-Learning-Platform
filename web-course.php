<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

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
                            <li><a href="#web1">4.1. Введение</a></li>
                            <li><a href="#web2">4.2. Проектирование базы данных</a></li>
                            <li><a href="#web3">4.3. Создание проекта</a></li>
                            <li><a href="#web4">4.4. Создание базы данных</a></li>
                            <li><a href="#web5">4.5. Подключение к базе данных</a></li>
                            <li><a href="#web6">4.6. Вспомогательные функции</a></li>
                            <li><a href="#web7">4.7. Шаблон страниц</a></li>
                            <li><a href="#web8">4.8. Управление авторами</a></li>
                            <li><a href="#web9">4.9. Остальные страницы</a></li>
                            <li><a href="#web10">4.10. Готовое приложение</a></li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>

        <div class="course-material">

            <section id="introduction" class="chapter">
                <div class="text-content">
                    <p>Добро пожаловать в курс <strong>по созданию веб-приложения</strong>! Этот курс является финальным — в нём мы будем использовать знания, полученные ранее.</p>
                    <p style="line-height: 2.2;">В данном курсе будет создано следующее веб-приложение: 
                    <a href="/library-preview.php" style="display: inline-block; background: rgba(90,150,144,0.18); color: rgb(37, 72, 70); padding: 0px 15px; border-radius: 30px; text-decoration: none; font-weight: 500; 
                    font-size: 0.99rem; letter-spacing: 0.02em; margin: 0 2px; transition: all 0.2s ease; border: 1px solid transparent;" onmouseover="this.style.background='rgba(90,150,144,0.25)';
                     this.style.borderColor='rgba(47,87,85,0.4)';" onmouseout="this.style.background='rgba(90,150,144,0.18)'; this.style.borderColor='transparent';">
                     предпросмотр</a></p>
                </div>
            </section>

            <section id="start" class="chapter">
                <h2>Пошаговое создание веб-приложения «Библиотека»</h2>

                <!-- 4.1 -->
                <article id="web1" class="lesson">
                    <h3>4.1. Введение</h3>
                    
                    <div class="text-content">
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
                    </div>
                </article>

                <!-- 4.2 -->
                <article id="web2" class="lesson">
                    <div class="text-content">
                        <h3>4.2. Проектирование базы данных «Библиотека»</h3>
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
                    </div>
                </article>

                <!-- 4.3 -->
                <article id="web3" class="lesson">
                    <div class="text-content">
                        <h3>4.3. Создание проекта</h3>
                        <p>Установите <strong>Open Server Panel</strong> с сайта <a href="https://ospanel.io/" target="_blank" style="color: rgb(47,87,85);">ospanel.io</a>. Вместе с ним вы получите PHP и MySQL. phpMyAdmin устанавливается отдельно.</p>
                        <p>В папке <code>home</code> создайте папку <code>library2.local</code>, внутри — папку <code>.osp</code> с файлом <code>project.ini</code>:</p>
                        <div class="content-placeholder">
                            <strong>Листинг 4.1. Файл project.ini</strong><br>
                            <?= TAB1 ?>[library2.local]<br>
                            <?= TAB1 ?>php_engine = PHP-8.3
                        </div>
                        <p>Структура файлов проекта:</p>
                        <div class="content-placeholder">
                            <strong>Листинг 4.2. Структура проекта</strong><br>
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
                    </div>
                </article>

                <!-- 4.4 -->
                <article id="web4" class="lesson">
                    <div class="text-content">
                        <h3>4.4. Создание базы данных</h3>
                        <p>Откройте phpMyAdmin и создайте базу <code>library_db</code>. Выполните следующий SQL-код для создания таблиц:</p>
                        <div class="content-placeholder">
                            <strong>Листинг 4.3. Создание таблиц</strong><br>
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
                            <strong>Листинг 4.4. Тестовые данные</strong><br>
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
                    </div>
                </article>

                <!-- 4.5 -->
                <article id="web5" class="lesson">
                    <div class="text-content">
                        <h3>4.5. Подключение к базе данных</h3>
                        <p>Создайте файл <code>includes/db.php</code>. Он содержит настройки подключения и четыре функции для работы с БД — через них выполняются все запросы в приложении.</p>
                        <div class="content-placeholder">
                            <strong>Листинг 4.5. Файл includes/db.php</strong><br>
                            <?= TAB1 ?>&lt;?php<br>
                            <?= TAB1 ?>// Настройки — измените под свой сервер<br>
                            <?= TAB1 ?>define('DB_HOST', 'MySQL-8.2'); // название модуля в OpenServer<br>
                            <?= TAB1 ?>define('DB_USER', 'root');<br>
                            <?= TAB1 ?>define('DB_PASS', '');<br>
                            <?= TAB1 ?>define('DB_NAME', 'library_db');<br><br>
                            <?= TAB1 ?>// Подключение создаётся один раз за запрос<br>
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
                            <?= TAB1 ?>}<br><br>
                            <?= TAB1 ?>// Получить все строки<br>
                            <?= TAB1 ?>function query(string $sql, array $params = []): array {<br>
                            <?= TAB2 ?>$stmt = db()->prepare($sql);<br>
                            <?= TAB2 ?>$stmt->execute($params);<br>
                            <?= TAB2 ?>return $stmt->fetchAll();<br>
                            <?= TAB1 ?>}<br><br>
                            <?= TAB1 ?>// Получить одну строку<br>
                            <?= TAB1 ?>function row(string $sql, array $params = []): array|false {<br>
                            <?= TAB2 ?>$stmt = db()->prepare($sql);<br>
                            <?= TAB2 ?>$stmt->execute($params);<br>
                            <?= TAB2 ?>return $stmt->fetch();<br>
                            <?= TAB1 ?>}<br><br>
                            <?= TAB1 ?>// Получить одно значение<br>
                            <?= TAB1 ?>function val(string $sql, array $params = []): mixed {<br>
                            <?= TAB2 ?>$stmt = db()->prepare($sql);<br>
                            <?= TAB2 ?>$stmt->execute($params);<br>
                            <?= TAB2 ?>return $stmt->fetchColumn();<br>
                            <?= TAB1 ?>}<br><br>
                            <?= TAB1 ?>// Выполнить INSERT / UPDATE / DELETE<br>
                            <?= TAB1 ?>function run(string $sql, array $params = []): void {<br>
                            <?= TAB2 ?>db()->prepare($sql)->execute($params);<br>
                            <?= TAB1 ?>}<br>
                            <?= TAB1 ?>?&gt;
                        </div>
                        <p>Вместо трёх строк на каждый запрос — одна. Например, чтобы получить книгу по id:</p>
                        <div class="content-placeholder">
                            <?= TAB1 ?>$book = row("SELECT * FROM Books WHERE id=?", [$id]);
                        </div>
                        <p>Знак <code>?</code> — параметр-заполнитель. Реальное значение передаётся вторым аргументом. Это правильный способ передавать данные в запрос — PDO корректно обрабатывает любые значения, в том числе строки со спецсимволами.</p>
                    </div>
                </article>

                <!-- 4.6 -->
                <article id="web6" class="lesson">
                    <div class="text-content">
                        <h3>4.6. Вспомогательные функции</h3>
                        <p>Создайте файл <code>includes/helpers.php</code> — небольшие функции, которые используются на всех страницах приложения. Они избавляют код от повторяющихся строк</p>
                        <div class="content-placeholder">
                            <strong>Листинг 4.6. Файл includes/helpers.php</strong><br>
                            <?= TAB1 ?>&lt;?php<br>
                            <?= TAB1 ?>// Безопасный вывод текста<br>
                            <?= TAB1 ?>// Преобразует символы &lt; &gt; &quot; в безопасные HTML-коды<br>
                            <?= TAB1 ?>function e(string $str): string {<br>
                            <?= TAB2 ?>return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');<br>
                            <?= TAB1 ?>}<br><br>
                            <?= TAB1 ?>// Форматирование даты: '2024-03-15' -> '15.03.2024'<br>
                            <?= TAB1 ?>function dateRu(?string $date): string {<br>
                            <?= TAB2 ?>if (!$date) return '—';<br>
                            <?= TAB2 ?>return date('d.m.Y', strtotime($date));<br>
                            <?= TAB1 ?>}<br><br>
                            <?= TAB1 ?>// Перенаправление на другую страницу<br>
                            <?= TAB1 ?>function go(string $url): never {<br>
                            <?= TAB2 ?>header("Location: $url");<br>
                            <?= TAB2 ?>exit;<br>
                            <?= TAB1 ?>}<br><br>
                            <?= TAB1 ?>// Сохранить уведомление (показывается после перенаправления)<br>
                            <?= TAB1 ?>function flash(string $type, string $text): void {<br>
                            <?= TAB2 ?>$_SESSION['flash'] = compact('type', 'text');<br>
                            <?= TAB1 ?>}<br><br>
                            <?= TAB1 ?>// Показать и удалить уведомление<br>
                            <?= TAB1 ?>function getFlash(): string {<br>
                            <?= TAB2 ?>if (empty($_SESSION['flash'])) return '';<br>
                            <?= TAB2 ?>['type' => $type, 'text' => $text] = $_SESSION['flash'];<br>
                            <?= TAB2 ?>unset($_SESSION['flash']);<br>
                            <?= TAB2 ?>return '&lt;div class="flash flash-' . e($type) . '"&gt;' . e($text) . '&lt;/div&gt;';<br>
                            <?= TAB1 ?>}<br>
                            <?= TAB1 ?>?&gt;
                        </div>
                        <p>Иногда нужно будет выводить данные из базы данных, которые могут содержать символы &lt; &gt; &quot;. Если вывести их напрямую, браузер может интерпретировать их как HTML-теги, что приведёт к поломке страницы. Функция <code>e()</code> решает эту проблему.</p>
                        <p>Функция <code>flash()</code> сохраняет сообщение в сессии, а <code>getFlash()</code> — читает и удаляет его. Это нужно, чтобы передать уведомление («Автор добавлен») на страницу после перенаправления.</p>
                    
                    </div>
                </article>

                <!-- 4.7 -->
                <article id="web7" class="lesson">
                    <div class="text-content">
                        <h3>4.7. Шаблон страниц </h3>
                        <p>Файл <code>includes/layout.php</code> содержит общую структуру HTML-документа: шапку с меню, область для контента и подвал. Это избавляет от дублирования кода на каждой странице.</p>
                        <p>Мы выносим повторяющиеся элементы в отдельный файл, а на каждой странице вызываем функцию <code>pageTop()</code> в начале и <code>pageBottom()</code> в конце. Всё, что находится между ними, становится уникальным содержимым страницы.</p>

                        <div class="content-placeholder">
                            <strong>Листинг 4.7. Файл includes/layout.php</strong><br>
                            <?= TAB1 ?>&lt;?php<br>
                            <?= TAB1 ?>function pageTop(string $title, string $active = ''): void {<br>
                            <?= TAB2 ?>?&gt;<br>
                            <?= TAB2 ?>&lt;!DOCTYPE html&gt;<br>
                            <?= TAB2 ?>&lt;html lang="ru"&gt;<br>
                            <?= TAB2 ?>&lt;head&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?>&lt;meta charset="UTF-8"&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?>&lt;meta name="viewport" content="width=device-width, initial-scale=1.0"&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?>&lt;title&gt;&lt;?= e($title) ?&gt; — Библиотека&lt;/title&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?>&lt;link rel="stylesheet" href="/assets/css/style.css"&gt;<br>
                            <?= TAB2 ?>&lt;/head&gt;<br>
                            <?= TAB2 ?>&lt;body&gt;<br>
                            <?= TAB2 ?>&lt;div class="app-container"&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?>&lt;aside class="sidebar"&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?>&lt;div class="logo"&gt; Библиотека&lt;/div&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?>&lt;nav class="nav"&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;a href="index.php" class="nav-item &lt;?= $active === 'index' ? 'active' : '' ?&gt;"&gt; Главная&lt;/a&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;a href="books.php" class="nav-item &lt;?= $active === 'books' ? 'active' : '' ?&gt;"&gt; Книги&lt;/a&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;a href="authors.php" class="nav-item &lt;?= $active === 'authors' ? 'active' : '' ?&gt;"&gt; Авторы&lt;/a&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;a href="publishers.php" class="nav-item &lt;?= $active === 'publishers' ? 'active' : '' ?&gt;"&gt; Издательства&lt;/a&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;a href="users.php" class="nav-item &lt;?= $active === 'users' ? 'active' : '' ?&gt;"&gt; Читатели&lt;/a&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;a href="copies.php" class="nav-item &lt;?= $active === 'copies' ? 'active' : '' ?&gt;"&gt; Экземпляры&lt;/a&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;a href="borrows.php" class="nav-item &lt;?= $active === 'borrows' ? 'active' : '' ?&gt;"&gt; Выдача&lt;/a&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?>&lt;/nav&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?>&lt;/aside&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?>&lt;main class="main-content"&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?>&lt;div class="page-header"&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;h1&gt;&lt;?= e($title) ?&gt;&lt;/h1&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?>&lt;/div&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?>&lt;div class="page-content"&gt;<br>
                            <?= TAB2 ?>&lt;?php<br>
                            <?= TAB1 ?>}<br><br>
                            <?= TAB1 ?>function pageBottom(): void {<br>
                            <?= TAB2 ?>?&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?>&lt;/div&gt; &lt;!-- .page-content --&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?>&lt;/main&gt;<br>
                            <?= TAB2 ?>&lt;/div&gt; &lt;!-- .app-container --&gt;<br>
                            <?= TAB2 ?>&lt;/body&gt;<br>
                            <?= TAB2 ?>&lt;/html&gt;<br>
                            <?= TAB2 ?>&lt;?php<br>
                            <?= TAB1 ?>}<br>
                            <?= TAB1 ?>?&gt;
                        </div>

                        <p>Использование шаблона на каждой странице:</p>
                        <div class="content-placeholder">
                            <strong>Листинг 4.8. Пример использования шаблона (books.php)</strong><br>
                            <?= TAB1 ?>&lt;?php<br>
                            <?= TAB1 ?>session_start();                      // обязательно перед любым выводом<br>
                            <?= TAB1 ?>require_once 'includes/db.php';      // подключение к БД<br>
                            <?= TAB1 ?>require_once 'includes/helpers.php'; // вспомогательные функции<br>
                            <?= TAB1 ?>require_once 'includes/layout.php';  // шаблон страниц<br><br>
                            <?= TAB1 ?>pageTop('Книги', 'books'); // выводим шапку, второй параметр — активный пункт меню<br>
                            <?= TAB1 ?>?&gt;<br><br>
                            <?= TAB1 ?>&lt;!-- Здесь будет уникальное содержимое страницы --&gt;<br>
                            <?= TAB1 ?>&lt;?= getFlash() ?&gt; &lt;!-- выводим уведомления --&gt;<br>
                            <?= TAB1 ?>&lt;div class="table-card"&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?>&lt;!-- таблица с книгами --&gt;<br>
                            <?= TAB1 ?>&lt;/div&gt;<br><br>
                            <?= TAB1 ?>&lt;?php pageBottom(); ?&gt; &lt;!-- выводим подвал --&gt;
                        </div>
                        
                        <p>Второй аргумент <code>pageTop()</code> — ключ активного пункта меню. Благодаря ему нужный пункт будет выделен в боковой панели. Это помогает пользователю понимать, на какой странице он находится.</p>
                    </div>
                </article>

                <!-- 4.8 -->
                <article id="web8" class="lesson">
                    <div class="text-content">
                        <h3>4.8. Управление авторами</h3>
                        <p>Файл <code>authors.php</code> — пример типовой CRUD-страницы (Create, Read, Update, Delete). Все остальные страницы построены по такой же схеме. Действие определяется параметром <code>?action=</code> в адресе:</p>
                        <ul style="margin-left: 20px;">
                            <li><code>authors.php</code> — список авторов (действие по умолчанию)</li>
                            <li><code>authors.php?action=add</code> — форма добавления нового автора</li>
                            <li><code>authors.php?action=edit&amp;id=3</code> — форма редактирования автора с id=3</li>
                            <li><code>authors.php?action=delete&amp;id=3</code> — удаление автора с id=3</li>
                        </ul>
                        
                        <p style="display: block; margin-top: 20px; margin-bottom: 0px;">Как работает обработка форм?</p>
                        <p>Все страницы построены по единому принципу:</p>
                        <ol>
                            <li>Проверяем параметр <code>action</code> в URL</li>
                            <li>Если это удаление — выполняем DELETE и перенаправляем обратно</li>
                            <li>Если это форма (add/edit) — показываем форму</li>
                            <li>Если пришёл POST-запрос — обрабатываем сохранение</li>
                            <li>В остальных случаях показываем список</li>
                        </ol>

                        <div class="content-placeholder">
                            <strong>Листинг 4.9. Структура файла authors.php</strong><br>
                            <?= TAB1 ?>&lt;?php<br>
                            <?= TAB1 ?>session_start();<br>
                            <?= TAB1 ?>require_once 'includes/db.php';<br>
                            <?= TAB1 ?>require_once 'includes/helpers.php';<br>
                            <?= TAB1 ?>require_once 'includes/layout.php';<br><br>
                            <?= TAB1 ?>$action = $_GET['action'] ?? 'list';  // определяем действие<br>
                            <?= TAB1 ?>$id     = (int)($_GET['id'] ?? 0);      // получаем ID, если есть<br><br>
                            <?= TAB1 ?>// ── ОБРАБОТКА POST-ЗАПРОСА (СОХРАНЕНИЕ) ──<br>
                            <?= TAB1 ?>if ($_SERVER['REQUEST_METHOD'] === 'POST') {<br>
                            <?= TAB2 ?>$first_name = trim($_POST['first_name'] ?? '');<br>
                            <?= TAB2 ?>$last_name  = trim($_POST['last_name']  ?? '');<br>
                            <?= TAB2 ?>$birth_date = $_POST['birth_date'] ?: null;<br>
                            <?= TAB2 ?>$country    = trim($_POST['country'] ?? '') ?: null;<br><br>
                            <?= TAB2 ?>// Валидация: имя и фамилия обязательны<br>
                            <?= TAB2 ?>if (!$first_name || !$last_name) {<br>
                            <?= TAB2 ?><?= TAB1 ?>flash('error', 'Введите имя и фамилию');<br>
                            <?= TAB2 ?><?= TAB1 ?>go('authors.php?action=add');<br>
                            <?= TAB2 ?>}<br><br>
                            <?= TAB2 ?>if ($id) {<br>
                            <?= TAB2 ?><?= TAB1 ?>// Обновление существующего автора<br>
                            <?= TAB2 ?><?= TAB1 ?>run("UPDATE Authors SET first_name=?, last_name=?, birth_date=?, country=? WHERE id=?",<br>
                            <?= TAB2 ?><?= TAB2 ?>[$first_name, $last_name, $birth_date, $country, $id]);<br>
                            <?= TAB2 ?><?= TAB1 ?>flash('success', 'Автор обновлён');<br>
                            <?= TAB2 ?>} else {<br>
                            <?= TAB2 ?><?= TAB1 ?>// Добавление нового автора<br>
                            <?= TAB2 ?><?= TAB1 ?>run("INSERT INTO Authors (first_name, last_name, birth_date, country) VALUES (?,?,?,?)",<br>
                            <?= TAB2 ?><?= TAB2 ?>[$first_name, $last_name, $birth_date, $country]);<br>
                            <?= TAB2 ?><?= TAB1 ?>flash('success', 'Автор добавлен');<br>
                            <?= TAB2 ?>}<br>
                            <?= TAB2 ?>go('authors.php'); // после сохранения возвращаемся к списку<br>
                            <?= TAB1 ?>}<br><br>
                            <?= TAB1 ?>// ── УДАЛЕНИЕ ──<br>
                            <?= TAB1 ?>if ($action === 'delete' && $id) {<br>
                            <?= TAB2 ?>try {<br>
                            <?= TAB2 ?><?= TAB1 ?>run("DELETE FROM Authors WHERE id=?", [$id]);<br>
                            <?= TAB2 ?><?= TAB1 ?>flash('success', 'Автор удалён');<br>
                            <?= TAB2 ?>} catch (PDOException) {<br>
                            <?= TAB2 ?><?= TAB1 ?>// Если у автора есть книги, MySQL выдаст ошибку внешнего ключа<br>
                            <?= TAB2 ?><?= TAB1 ?>flash('error', 'Нельзя удалить: есть связанные книги');<br>
                            <?= TAB2 ?>}<br>
                            <?= TAB2 ?>go('authors.php');<br>
                            <?= TAB1 ?>}<br><br>
                            <?= TAB1 ?>// ── ПОКАЗ ФОРМЫ (ДОБАВЛЕНИЕ / РЕДАКТИРОВАНИЕ) ──<br>
                            <?= TAB1 ?>if ($action === 'add' || $action === 'edit') {<br>
                            <?= TAB2 ?>// Загружаем данные автора для редактирования<br>
                            <?= TAB2 ?>$author = ['id'=>0, 'first_name'=>'', 'last_name'=>'', 'birth_date'=>'', 'country'=>''];<br>
                            <?= TAB2 ?>if ($id) {<br>
                            <?= TAB2 ?><?= TAB1 ?>$author = row("SELECT * FROM Authors WHERE id=?", [$id]);<br>
                            <?= TAB2 ?>}<br><br>
                            <?= TAB2 ?>pageTop($id ? 'Редактировать автора' : 'Добавить автора', 'authors');<br>
                            <?= TAB2 ?>?&gt;<br>
                            <?= TAB2 ?>&lt;?= getFlash() ?&gt;<br>
                            <?= TAB2 ?>&lt;div class="form-card"&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?>&lt;form method="post"&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?>&lt;div class="form-grid"&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;div class="form-group"&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;label&gt;Имя *&lt;/label&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;input type="text" name="first_name" value="&lt;?= e($author['first_name']) ?&gt;" required&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;/div&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;div class="form-group"&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;label&gt;Фамилия *&lt;/label&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;input type="text" name="last_name" value="&lt;?= e($author['last_name']) ?&gt;" required&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;/div&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;div class="form-group"&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;label&gt;Дата рождения&lt;/label&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;input type="date" name="birth_date" value="&lt;?= e($author['birth_date'] ?? '') ?&gt;"&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;/div&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;div class="form-group"&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;label&gt;Страна&lt;/label&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;input type="text" name="country" value="&lt;?= e($author['country'] ?? '') ?&gt;"&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;/div&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?>&lt;/div&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?>&lt;div class="form-actions"&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;button type="submit" class="btn btn-primary"&gt;Сохранить&lt;/button&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;a href="authors.php" class="btn btn-outline"&gt;Отмена&lt;/a&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?><?= TAB1 ?>&lt;/div&gt;<br>
                            <?= TAB2 ?><?= TAB1 ?>&lt;/form&gt;<br>
                            <?= TAB2 ?>&lt;/div&gt;<br>
                            <?= TAB2 ?>&lt;?php<br>
                            <?= TAB2 ?>pageBottom();<br>
                            <?= TAB2 ?>exit;<br>
                            <?= TAB1 ?>}<br><br>
                            <?= TAB1 ?>// ── ПОКАЗ СПИСКА ──<br>
                            <?= TAB1 ?>// ... код для отображения таблицы с авторами ...<br>
                            <?= TAB1 ?>?&gt;
                        </div>
                        <p>Важные моменты:</p>
                        <ul>
                            <li>Перед сохранением всегда проверяем, что обязательные поля заполнены. Если нет — показываем ошибку и возвращаем пользователя обратно в форму.</li>
                            <li>Используем try/catch. Если у автора есть книги, MySQL не позволит его удалить из-за внешнего ключа. Мы ловим исключение и показываем понятное сообщение вместо технической ошибки.</li>
                            <li>После успешного сохранения или удаления сохраняем сообщение в сессии и перенаправляем на страницу списка. Сообщение будет показано один раз.</li>
                        </ul>
                    </div>
                </article>

                <!-- 4.9 -->
                <article id="web9" class="lesson">
                    <div class="text-content">
                        <h3>4.9. Остальные страницы</h3>
                        <h4>4.9.1. Страница книг (books.php)</h4>
                        <p>Страница книг — самая сложная из всех, потому что книги связаны с авторами, издательствами и экземплярами. Рассмотрим её подробно.</p> 
                        <p style="display: block; margin-top: 20px; margin-bottom: 0px;">Возможности данной страницы:</p>
                        <ul>
                            <li>Показывает список всех книг с возможностью поиска и фильтрации по жанру</li>
                            <li>Позволяет добавлять, редактировать и удалять книги</li>
                            <li>Отображает количество экземпляров и доступных для выдачи</li>
                            <li>Имеет ссылки на управление экземплярами каждой книги</li>
                        </ul>

                        <strong style="display: block; margin-top: 30px; margin-bottom: 0px;">Подготовка данных для выпадающих списков</strong>
                        <div class="content-placeholder">
                            <strong>Листинг 4.10. Получение списков авторов и издательств</strong><br>
                            <?= TAB1 ?>// Данные для выпадающих списков в форме<br>
                            <?= TAB1 ?>$authors    = query("SELECT id, CONCAT(first_name,' ',last_name) AS name FROM Authors ORDER BY last_name");<br>
                            <?= TAB1 ?>$publishers = query("SELECT id, name FROM Publishers ORDER BY name");<br>
                            <?= TAB1 ?>$genres     = query("SELECT DISTINCT genre FROM Books WHERE genre IS NOT NULL ORDER BY genre");<br><br>
                            <?= TAB1 ?>// CONCAT склеивает имя и фамилию в одно поле для удобного отображения
                        </div>
                        <p>Обратите внимание на использование <code>CONCAT()</code> в SQL-запросе — это объединяет имя и фамилию автора в одну строку. Так проще выводить в выпадающем списке.</p>
                        
                        <strong style="display: block; margin-top: 30px; margin-bottom: 0px;">Поиск и пагинация</strong>
                        <div class="content-placeholder">
                            <strong>Листинг 4.11. Реализация поиска и постраничного вывода</strong><br>
                            <?= TAB1 ?>$search  = trim($_GET['q'] ?? '');    // поисковый запрос<br>
                            <?= TAB1 ?>$genreF  = trim($_GET['genre'] ?? ''); // фильтр по жанру<br>
                            <?= TAB1 ?>$page    = max(1, (int)($_GET['p'] ?? 1)); // текущая страница<br>
                            <?= TAB1 ?>$limit   = 15;   // количество записей на странице<br>
                            <?= TAB1 ?>$offset  = ($page - 1) * $limit; // смещение для LIMIT<br><br>
                            <?= TAB1 ?>// Строим WHERE-условие динамически<br>
                            <?= TAB1 ?>$where = [];<br>
                            <?= TAB1 ?>$params = [];<br>
                            <?= TAB1 ?>if ($search) {<br>
                            <?= TAB2 ?>// Ищем по названию книги или по имени/фамилии автора<br>
                            <?= TAB2 ?>$where[]  = "(b.title LIKE ? OR CONCAT(a.first_name,' ',a.last_name) LIKE ?)";<br>
                            <?= TAB2 ?>$params[] = "%$search%";<br>
                            <?= TAB2 ?>$params[] = "%$search%";<br>
                            <?= TAB1 ?>}<br>
                            <?= TAB1 ?>if ($genreF) {<br>
                            <?= TAB2 ?>$where[]  = "b.genre = ?";<br>
                            <?= TAB2 ?>$params[] = $genreF;<br>
                            <?= TAB1 ?>}<br>
                            <?= TAB1 ?>$w = $where ? 'WHERE ' . implode(' AND ', $where) : '';<br><br>
                            <?= TAB1 ?>// Получаем общее количество записей для пагинации<br>
                            <?= TAB1 ?>$total = (int) val("SELECT COUNT(*) FROM Books b JOIN Authors a ON b.author_id=a.id $w", $params);<br>
                            <?= TAB1 ?>$pages = (int) ceil($total / $limit);<br><br>
                            <?= TAB1 ?>// Получаем книги для текущей страницы<br>
                            <?= TAB1 ?>$books = query("
                            <?= TAB1 ?>    SELECT b.*, 
                            <?= TAB1 ?>           CONCAT(a.first_name,' ',a.last_name) AS author_name, 
                            <?= TAB1 ?>           p.name AS publisher_name,
                            <?= TAB1 ?>           (SELECT COUNT(*) FROM Book_copies WHERE book_id=b.id) AS total_copies,
                            <?= TAB1 ?>           (SELECT COUNT(*) FROM Book_copies WHERE book_id=b.id AND status='available') AS avail_copies
                            <?= TAB1 ?>    FROM Books b
                            <?= TAB1 ?>    JOIN Authors a    ON b.author_id    = a.id
                            <?= TAB1 ?>    JOIN Publishers p ON b.publisher_id = p.id
                            <?= TAB1 ?>    $w ORDER BY b.title LIMIT $limit OFFSET $offset
                            <?= TAB1 ?>", $params);
                        </div>
                        <p>Здесь используются подзапросы для подсчёта количества экземпляров и доступных книг. Это позволяет получить всю необходимую информацию одним запросом, вместо выполнения нескольких.</p>
                        
                        <strong style="display: block; margin-top: 30px; margin-bottom: 0px;">Форма добавления/редактирования книги</strong>
                        <div class="content-placeholder">
                            <strong>Листинг 4.12. Форма книги с выпадающими списками и datalist</strong><br>
                            <?= TAB1 ?>&lt;form method="post"&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?>&lt;div class="form-grid"&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;div class="form-group full"&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;label&gt;Название *&lt;/label&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;input type="text" name="title" value="&lt;?= e($book['title']) ?&gt;" required&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;/div&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;div class="form-group"&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;label&gt;Автор *&lt;/label&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;select name="author_id" required&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;option value=""&gt;— выберите —&lt;/option&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;?php foreach ($authors as $a): ?&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;option value="&lt;?= $a['id'] ?&gt;" &lt;?= $book['author_id'] == $a['id'] ? 'selected' : '' ?&gt;&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;?= e($a['name']) ?&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;/option&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;?php endforeach ?&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;/select&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;/div&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;div class="form-group"&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;label&gt;Жанр&lt;/label&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;input type="text" name="genre" value="&lt;?= e($book['genre'] ?? '') ?&gt;" list="genres-list"&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;datalist id="genres-list"&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;?php foreach ($genres as $g): ?&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;option value="&lt;?= e($g['genre']) ?&gt;"&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;?php endforeach ?&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;/datalist&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;/div&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?>&lt;/div&gt;<br>
                            <?= TAB1 ?>&lt;/form&gt;
                        </div>
                        <p>Обратите внимание на использование <code>&lt;datalist&gt;</code> для поля "Жанр" — это позволяет пользователю как выбрать жанр из существующих, так и ввести новый.</p>
                        
                        <strong style="display: block; margin-top: 30px; margin-bottom: 0px;">Отображение списка книг</strong>
                        <div class="content-placeholder">
                            <strong>Листинг 4.13. Таблица с книгами</strong><br>
                            <?= TAB1 ?>&lt;table&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?>&lt;thead&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;tr&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;th&gt;#&lt;/th&gt;&lt;th&gt;Название&lt;/th&gt;&lt;th&gt;Автор&lt;/th&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;th&gt;Издательство&lt;/th&gt;&lt;th&gt;Жанр&lt;/th&gt;&lt;th&gt;Стр.&lt;/th&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;th&gt;Возр.&lt;/th&gt;&lt;th&gt;Год&lt;/th&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;th&gt;Экз. / Дост.&lt;/th&gt;&lt;th&gt;Действия&lt;/th&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;/tr&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?>&lt;/thead&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?>&lt;tbody&gt;<br>
                            <?= TAB1 ?>&lt;?php foreach ($books as $b): ?&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?>&lt;tr&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;td class="td-muted"&gt;&lt;?= $b['id'] ?&gt;&lt;/td&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;td&gt;&lt;strong&gt;&lt;?= e($b['title']) ?&gt;&lt;/strong&gt;&lt;/td&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;td&gt;&lt;?= e($b['author_name']) ?&gt;&lt;/td&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;td&gt;&lt;?= e($b['publisher_name']) ?&gt;&lt;/td&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;td&gt;&lt;?= e($b['genre'] ?? '—') ?&gt;&lt;/td&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;td&gt;&lt;?= $b['page_count'] ?? '—' ?&gt;&lt;/td&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;td&gt;&lt;?= $b['age_limit'] ?&gt;+&lt;/td&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;td&gt;&lt;?= $b['publication_date'] ? date('Y', strtotime($b['publication_date'])) : '—' ?&gt;&lt;/td&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;td&gt;&lt;?= $b['total_copies'] ?&gt; / &lt;strong&gt;&lt;?= $b['avail_copies'] ?&gt;&lt;/strong&gt;&lt;/td&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;td style="display:flex;gap:6px"&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;a href="books.php?action=edit&id=&lt;?= $b['id'] ?&gt;" class="btn btn-outline btn-sm"&gt;✎ Ред.&lt;/a&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;a href="copies.php?book_id=&lt;?= $b['id'] ?&gt;" class="btn btn-outline btn-sm"&gt;Экземпляры&lt;/a&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;a href="books.php?action=delete&id=&lt;?= $b['id'] ?&gt;" class="btn btn-danger btn-sm"<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>onclick="return confirm('Удалить книгу?')"&gt;✕&lt;/a&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?><?= TAB1 ?>&lt;/td&gt;<br>
                            <?= TAB1 ?><?= TAB1 ?>&lt;/tr&gt;<br>
                            <?= TAB1 ?>&lt;?php endforeach ?&gt;<br>
                            <?= TAB1 ?>&lt;/tbody&gt;<br>
                            <?= TAB1 ?>&lt;/table&gt;
                        </div>

                        <h4>4.9.2. Управление издательствами и читателями (publishers.php/users.php)</h4>
                        <strong style="display: block; margin-top: 30px; margin-bottom: 0px;">Издательства</strong>
                        <p>Страницы <code>publishers.php</code> и <code>users.php</code> построены по такому же принципу, как <code>authors.php</code>. Рассмотрим их особенности.</p>
                        
                        <p>В списке издательств отображается количество книг, выпущенных каждым издательством. Это реализовано с помощью <code>LEFT JOIN</code> и <code>COUNT()</code>:</p>
                        <div class="content-placeholder">
                            <strong>Листинг 4.14. Подсчёт книг издательства</strong><br>
                            <?= TAB1 ?>$pubs = query("
                            <?= TAB1 ?>    SELECT p.*, COUNT(b.id) AS book_count
                            <?= TAB1 ?>    FROM Publishers p
                            <?= TAB1 ?>    LEFT JOIN Books b ON b.publisher_id = p.id
                            <?= TAB1 ?>    GROUP BY p.id ORDER BY p.name
                            <?= TAB1 ?>");
                        </div>
                        <p><code>LEFT JOIN</code> используется, чтобы издательства без книг тоже попали в результат (у них <code>book_count</code> будет 0). Если бы мы использовали <code>INNER JOIN</code>, такие издательства не отобразились бы.</p>
                        
                        <strong style="display: block; margin-top: 30px; margin-bottom: 0px;">Читатели</strong>
                        <p>Страница читателей имеет дополнительную функцию — просмотр карточки читателя с историей выдач:</p>
                        <div class="content-placeholder">
                            <strong>Листинг 4.15. Карточка читателя</strong><br>
                            <?= TAB1 ?>if ($action === 'view' && $id) {<br>
                            <?= TAB2 ?>$user = row("SELECT * FROM Users WHERE id=?", [$id]);<br>
                            <?= TAB2 ?>if (!$user) go('users.php');<br><br>
                            <?= TAB2 ?>$borrows = query("
                            <?= TAB2 ?>    SELECT br.borrow_date, br.due_date, br.return_date, b.title
                            <?= TAB2 ?>    FROM Borrow_records br
                            <?= TAB2 ?>    JOIN Book_copies bc ON br.book_copy_id = bc.id
                            <?= TAB2 ?>    JOIN Books b        ON bc.book_id      = b.id
                            <?= TAB2 ?>    WHERE br.user_id = ?
                            <?= TAB2 ?>    ORDER BY br.borrow_date DESC
                            <?= TAB2 ?>", [$id]);<br>
                            <?= TAB2 ?>// ... отображение карточки и таблицы с историей ...<br>
                            <?= TAB1 ?>}
                        </div>
                        <p>Здесь мы показываем не только информацию о читателе, но и все книги, которые он когда-либо брал, с указанием статуса (активна/просрочена/возвращена).</p>
                            <strong style="display: block; margin-top: 30px; margin-bottom: 0px;">Отображение статуса выдачи</strong>
                            <p>На страницах <code>users.php</code> и <code>borrows.php</code> используется одинаковый код для определения статуса выдачи:</p>
                            <div class="content-placeholder">
                                <?= TAB1 ?>&lt;?php<br>
                                <?= TAB1 ?>if ($r['return_date']):<br>
                                <?= TAB2 ?>    &lt;span class="badge green"&gt;Возвращена&lt;/span&gt;<br>
                                <?= TAB1 ?>elseif ($r['due_date'] < date('Y-m-d')):<br>
                                <?= TAB2 ?>    &lt;span class="badge red"&gt;Просрочена&lt;/span&gt;<br>
                                <?= TAB1 ?>else:<br>
                                <?= TAB2 ?>    &lt;span class="badge blue"&gt;Активна&lt;/span&gt;<br>
                                <?= TAB1 ?>endif;<br>
                                <?= TAB1 ?>?&gt;
                            </div>
                    </div>

                    <div class="text-content">
                        <h4>4.9.3. Управление экземплярами книг (copies.php)</h4>
                        <p>Экземпляры — это физические копии книг. Одна книга может иметь несколько экземпляров, и каждый может быть в разном состоянии (доступен, выдан, утерян).</p>
                        
                        <strong style="display: block; margin-top: 30px; margin-bottom: 0px;">Массовое добавление экземпляров</strong>
                        <p>В форме добавления предусмотрена возможность добавить сразу несколько экземпляров одной книги. Это реализовано с помощью цикла:</p>
                        <div class="content-placeholder">
                            <strong>Листинг 4.16. Добавление нескольких экземпляров</strong><br>
                            <?= TAB1 ?>$count = max(1, min(50, (int)($_POST['count'] ?? 1)));<br><br>
                            <?= TAB1 ?>for ($i = 0; $i < $count; $i++) {<br>
                            <?= TAB2 ?>run("INSERT INTO Book_copies (book_id, status) VALUES (?,?)", [$book_id, $status]);<br>
                            <?= TAB1 ?>}<br>
                            <?= TAB1 ?>flash('success', "Добавлено экземпляров: $count");
                        </div>
                        <p>Обратите внимание на <code>min(50, ...)</code> — это ограничение, чтобы пользователь случайно не создал тысячи записей одной операцией.</p>
                        
                        <strong style="display: block; margin-top: 30px; margin-bottom: 0px;">Фильтрация по книге</strong>
                        <p>На странице экземпляров есть возможность фильтровать по конкретной книге. Для этого используется параметр <code>book_id</code> в URL:</p>
                        <div class="content-placeholder">
                            <strong>Листинг 4.17. Фильтрация экземпляров</strong><br>
                            <?= TAB1 ?>$bookFilter = (int)($_GET['book_id'] ?? 0);<br>
                            <?= TAB1 ?>$w = $bookFilter ? "WHERE bc.book_id = $bookFilter" : '';<br><br>
                            <?= TAB1 ?>// Если выбран фильтр, показываем название книги в заголовке<br>
                            <?= TAB1 ?>if ($bookFilter) {<br>
                            <?= TAB2 ?>$bookName = val("SELECT title FROM Books WHERE id=?", [$bookFilter]);<br>
                            <?= TAB2 ?>$pageTitle = "Экземпляры: $bookName";<br>
                            <?= TAB1 ?>}
                        </div>
                        <p>Это позволяет создавать удобные ссылки: с <code>books.php</code> мы можем перейти к списку экземпляров конкретной книги, нажав кнопку "Экземпляры".</p>
                        
                        <strong style="display: block; margin-top: 30px; margin-bottom: 0px;">Кнопка "Выдать" для доступных экземпляров</strong>
                        <p>В списке экземпляров для свободных книг есть кнопка "Выдать", которая ведёт на страницу выдачи с предварительно выбранным экземпляром:</p>
                        <div class="content-placeholder">
                            <strong>Листинг 4.18. Быстрая выдача из списка экземпляров</strong><br>
                            <?= TAB1 ?>&lt;?php if ($c['status'] === 'available'): ?&gt;<br>
                            <?= TAB1 ?>&lt;a href="borrows.php?action=add&copy_id=&lt;?= $c['id'] ?&gt;" class="btn btn-primary btn-sm"&gt;Выдать&lt;/a&gt;<br>
                            <?= TAB1 ?>&lt;?php endif ?&gt;
                        </div>
                        <p>Параметр <code>copy_id</code> в URL будет передан на страницу <code>borrows.php</code>, где автоматически выберется нужный экземпляр в выпадающем списке.</p>
                    </div>

                    <div class="text-content">
                        <h4>4.9.4. Выдача и возврат книг (borrows.php)</h4>
                        <p>Это самая важная страница приложения — здесь реализована основная бизнес-логика работы библиотеки: выдача книг читателям и их возврат.</p>                       
                        <strong> Транзакционность операций</strong>
                        <p>Выдача книги требует изменения двух таблиц:</p>
                        <ol>
                            <li>Создать запись в <code>Borrow_records</code> (история выдачи)</li>
                            <li>Изменить статус экземпляра в <code>Book_copies</code> на 'borrowed'</li>
                        </ol>
                        <p>В идеале эти операции должны быть обёрнуты в транзакцию, чтобы если одна из них не выполнилась, откатились обе. В нашем примере мы опустили транзакции для простоты, но в реальном проекте их стоит использовать.</p>
                        
                        <strong style="display: block; margin-top: 30px; margin-bottom: 0px;">Выдача книги</strong>
                        <div class="content-placeholder">
                            <strong>Листинг 4.19. Обработка выдачи книги</strong><br>
                            <?= TAB1 ?>if ($_SERVER['REQUEST_METHOD'] === 'POST') {<br>
                            <?= TAB2 ?>$user_id  = (int)$_POST['user_id'];<br>
                            <?= TAB2 ?>$copy_id  = (int)$_POST['book_copy_id'];<br>
                            <?= TAB2 ?>$borrow   = $_POST['borrow_date'] ?: date('Y-m-d');<br>
                            <?= TAB2 ?>$due      = $_POST['due_date'] ?? '';<br><br>
                            <?= TAB2 ?>// Валидация<br>
                            <?= TAB2 ?>if (!$user_id || !$copy_id || !$due) {<br>
                            <?= TAB2 ?><?= TAB1 ?>flash('error', 'Заполните все поля');<br>
                            <?= TAB2 ?><?= TAB1 ?>go('borrows.php?action=add');<br>
                            <?= TAB2 ?>}<br><br>
                            <?= TAB2 ?>// Проверяем, что экземпляр свободен<br>
                            <?= TAB2 ?>if (val("SELECT status FROM Book_copies WHERE id=?", [$copy_id]) !== 'available') {<br>
                            <?= TAB2 ?><?= TAB1 ?>flash('error', 'Этот экземпляр недоступен для выдачи');<br>
                            <?= TAB2 ?><?= TAB1 ?>go('borrows.php?action=add');<br>
                            <?= TAB2 ?>}<br><br>
                            <?= TAB2 ?>// Создаём запись о выдаче<br>
                            <?= TAB2 ?>run("INSERT INTO Borrow_records (user_id, book_copy_id, borrow_date, due_date) VALUES (?,?,?,?)",<br>
                            <?= TAB2 ?><?= TAB1 ?>[$user_id, $copy_id, $borrow, $due]);<br><br>
                            <?= TAB2 ?>// Меняем статус экземпляра<br>
                            <?= TAB2 ?>run("UPDATE Book_copies SET status='borrowed' WHERE id=?", [$copy_id]);<br><br>
                            <?= TAB2 ?>flash('success', 'Книга выдана');<br>
                            <?= TAB2 ?>go('borrows.php');<br>
                            <?= TAB1 ?>}
                        </div>
                        
                        <strong style="display: block; margin-top: 30px; margin-bottom: 0px;">Возврат книги</strong>
                        <div class="content-placeholder">
                            <strong>Листинг 4.20. Обработка возврата</strong><br>
                            <?= TAB1 ?>if ($action === 'return' && $id) {<br>
                            <?= TAB2 ?>// Получаем запись о выдаче<br>
                            <?= TAB2 ?>$rec = row("SELECT br.*, bc.id AS copy_id FROM Borrow_records br JOIN Book_copies bc ON br.book_copy_id=bc.id WHERE br.id=?", [$id]);<br>
                            <?= TAB2 ?>if ($rec && !$rec['return_date']) {<br>
                            <?= TAB2 ?><?= TAB1 ?>// Устанавливаем дату возврата<br>
                            <?= TAB2 ?><?= TAB1 ?>run("UPDATE Borrow_records SET return_date=CURDATE() WHERE id=?", [$id]);<br>
                            <?= TAB2 ?><?= TAB1 ?>// Возвращаем экземпляр в доступные<br>
                            <?= TAB2 ?><?= TAB1 ?>run("UPDATE Book_copies SET status='available' WHERE id=?", [$rec['copy_id']]);<br>
                            <?= TAB2 ?><?= TAB1 ?>flash('success', 'Книга отмечена как возвращённая');<br>
                            <?= TAB2 ?>}<br>
                            <?= TAB2 ?>go('borrows.php');<br>
                            <?= TAB1 ?>}
                        </div>
                        
                        <strong style="display: block; margin-top: 30px; margin-bottom: 0px;"> Фильтрация по статусу</strong>
                        <p>На странице выдач есть фильтр по статусу: все, активные, просроченные, возвращённые. Это реализовано с помощью условий WHERE:</p>
                        <div class="content-placeholder">
                            <strong>Листинг 4.21. Фильтрация по статусу выдачи</strong><br>
                            <?= TAB1 ?>if ($statusF === 'active')   { $where[] = "br.return_date IS NULL"; }<br>
                            <?= TAB1 ?>if ($statusF === 'returned') { $where[] = "br.return_date IS NOT NULL"; }<br>
                            <?= TAB1 ?>if ($statusF === 'overdue')  { $where[] = "br.return_date IS NULL AND br.due_date < CURDATE()"; }
                        </div>
                        <p>Обратите внимание на использование <code>CURDATE()</code> — это функция MySQL, возвращающая текущую дату. Сравнение с ней позволяет определить просроченные выдачи.</p>
                        
                        <div class="tip-box">
                            <strong>Визуальное выделение просроченных выдач</strong>
                            <p>В таблице выдач строки с просроченными книгами подсвечиваются красным:</p>
                            <div class="content-placeholder">
                                <?= TAB1 ?>&lt;?php $overdue = !$r['return_date'] && $r['due_date'] < date('Y-m-d'); ?&gt;<br>
                                <?= TAB1 ?>&lt;tr class="&lt;?= $overdue ? 'overdue' : '' ?&gt;"&gt;
                            </div>
                            <p>В CSS можно задать цвет фона для этого класса, чтобы сразу привлекать внимание к проблемным выдачам.</p>
                        </div>
                    </div>

                    <div class="text-content">
                        <h4>4.9.5 Главная страница со статистикой (index.php)</h4>
                        <p>Главная страница — это дашборд, который даёт общее представление о состоянии библиотеки. Здесь отображаются:</p>
                        <ul>
                            <li>Общее количество книг, авторов, читателей, экземпляров</li>
                            <li>Последние выдачи</li>
                            <li>Просроченные выдачи</li>
                            <li>Популярные книги (по количеству выдач)</li>
                        </ul>
                        
                        <strong style="display: block; margin-top: 30px; margin-bottom: 0px;">Статистические карточки</strong>
                        <div class="content-placeholder">
                            <strong>Листинг 4.22. Получение статистики</strong><br>
                            <?= TAB1 ?>function getStats(): array {<br>
                            <?= TAB2 ?>return [<br>
                            <?= TAB2 ?><?= TAB1 ?>'books'  => (int) val("SELECT COUNT(*) FROM Books"),<br>
                            <?= TAB2 ?><?= TAB1 ?>'authors'=> (int) val("SELECT COUNT(*) FROM Authors"),<br>
                            <?= TAB2 ?><?= TAB1 ?>'users'  => (int) val("SELECT COUNT(*) FROM Users"),<br>
                            <?= TAB2 ?><?= TAB1 ?>'copies' => (int) val("SELECT COUNT(*) FROM Book_copies")<br>
                            <?= TAB2 ?>];<br>
                            <?= TAB1 ?>}
                        </div>
                        <p>Функция <code>getStats()</code> вынесена в <code>helpers.php</code>, чтобы при необходимости её можно было использовать и на других страницах.</p>
                        
                        <strong style="display: block; margin-top: 30px; margin-bottom: 0px;">Последние выдачи</strong>
                        <div class="content-placeholder">
                            <strong>Листинг 4.23. Запрос последних выдач</strong><br>
                            <?= TAB1 ?>$recentBorrows = query("
                            <?= TAB1 ?>    SELECT br.borrow_date, br.due_date, br.return_date,
                            <?= TAB1 ?>           CONCAT(u.first_name, ' ', u.last_name) AS user_name,
                            <?= TAB1 ?>           b.title
                            <?= TAB1 ?>    FROM Borrow_records br
                            <?= TAB1 ?>    JOIN Users u        ON br.user_id      = u.id
                            <?= TAB1 ?>    JOIN Book_copies bc ON br.book_copy_id = bc.id
                            <?= TAB1 ?>    JOIN Books b        ON bc.book_id      = b.id
                            <?= TAB1 ?>    ORDER BY br.borrow_date DESC
                            <?= TAB1 ?>    LIMIT 8
                            <?= TAB1 ?>");
                        </div>
                        
                        <strong style="display: block; margin-top: 30px; margin-bottom: 0px;">Популярные книги</strong>
                        <div class="content-placeholder">
                            <strong>Листинг 4.24. Рейтинг книг по количеству выдач</strong><br>
                            <?= TAB1 ?>$popularBooks = query("
                            <?= TAB1 ?>    SELECT b.title, a.last_name, COUNT(br.id) AS cnt
                            <?= TAB1 ?>    FROM Books b
                            <?= TAB1 ?>    JOIN Authors a ON b.author_id = a.id
                            <?= TAB1 ?>    JOIN Book_copies bc ON bc.book_id = b.id
                            <?= TAB1 ?>    JOIN Borrow_records br ON br.book_copy_id = bc.id
                            <?= TAB1 ?>    GROUP BY b.id
                            <?= TAB1 ?>    ORDER BY cnt DESC
                            <?= TAB1 ?>    LIMIT 5s
                            <?= TAB1 ?>");
                        </div>
                        <p>Здесь мы считаем количество записей в <code>Borrow_records</code> для каждой книги. Чем больше выдач, тем популярнее книга.</p>
                        
                    </div>

                </article>

                <!-- 4.10 -->
                <article id="web10" class="lesson">
                    <div class="text-content">
                        <h3>4.10. Готовое приложение</h3>
                        <p>Приложение готово. Оно включает семь страниц: главную со статистикой, а также управление книгами, авторами, издательствами, читателями, экземплярами и выдачей книг.</p>
                        <p>Ниже можно посмотреть, как выглядит интерфейс готового приложения — нажмите кнопку, чтобы открыть интерактивный предпросмотр:</p>

                        <div class="preview-block">
                            <a href="/library-preview.php" class="preview-btn" target="_blank">
                                Открыть интерактивный предпросмотр приложения
                            </a>
                        </div>
                    </div>
                </article>

            </section>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/templates/footer.php'; ?>
