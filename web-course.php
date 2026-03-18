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

<div class="course-container">
    <div class="course-content">

        <!-- Содержание -->
        <div class="table-of-contents">
            <h2>Содержание курса</h2>
            <nav class="toc-nav">
                <ul>
                    <li><a href="#introduction">Введение</a></li>
                    <li><a href="#sql-firebird">Пошаговое создание</a>
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
                </div>
            </section>

            <section id="sql-firebird" class="chapter">
                <h1 style="margin-bottom: 30px;">Пошаговое создание веб-приложения «Библиотека»</h1>

                <!-- 4.1 -->
                <article id="web1" class="lesson">
                    <div class="text-content">
                        <h2>4.1. Введение</h2>
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
                        <h2>4.2. Проектирование базы данных «Библиотека»</h2>
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
                        <h2>4.3. Создание проекта</h2>
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
                        <h2>4.4. Создание базы данных</h2>
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
                        <h2>4.5. Подключение к базе данных</h2>
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
                        <h2>4.6. Вспомогательные функции</h2>
                        <p>Создайте файл <code>includes/helpers.php</code> — небольшие функции, которые используются на всех страницах приложения.</p>
                        <div class="content-placeholder">
                            <strong>Листинг 4.6. Файл includes/helpers.php</strong><br>
                            <?= TAB1 ?>&lt;?php<br>
                            <?= TAB1 ?>// Безопасный вывод текста<br>
                            <?= TAB1 ?>// Преобразует символы &lt; &gt; &quot; в безопасные HTML-коды<br>
                            <?= TAB1 ?>// Используйте при выводе любых данных из БД<br>
                            <?= TAB1 ?>function e(string $str): string {<br>
                            <?= TAB2 ?>return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');<br>
                            <?= TAB1 ?>}<br><br>
                            <?= TAB1 ?>// Форматирование даты: '2024-03-15' → '15.03.2024'<br>
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
                        <p>Функция <code>flash()</code> сохраняет сообщение в сессии, а <code>getFlash()</code> — читает и удаляет его. Это нужно, чтобы передать уведомление («Автор добавлен») на страницу после перенаправления.</p>
                    </div>
                </article>

                <!-- 4.7 -->
                <article id="web7" class="lesson">
                    <div class="text-content">
                        <h2>4.7. Шаблон страниц</h2>
                        <p>Файл <code>includes/layout.php</code> содержит шапку и подвал, общие для всех страниц. Функция <code>pageTop()</code> выводит боковое меню и заголовок, <code>pageBottom()</code> — закрывает теги.</p>
                        <p>Использование шаблона на каждой странице:</p>
                        <div class="content-placeholder">
                            <strong>Листинг 4.7. Шаблон использования</strong><br>
                            <?= TAB1 ?>&lt;?php<br>
                            <?= TAB1 ?>session_start();<br>
                            <?= TAB1 ?>require_once 'includes/db.php';<br>
                            <?= TAB1 ?>require_once 'includes/helpers.php';<br>
                            <?= TAB1 ?>require_once 'includes/layout.php';<br><br>
                            <?= TAB1 ?>pageTop('Книги', 'books'); // шапка страницы<br>
                            <?= TAB1 ?>?&gt;<br><br>
                            <?= TAB1 ?>&lt;!-- содержимое страницы --&gt;<br><br>
                            <?= TAB1 ?>&lt;?php pageBottom(); ?&gt; &lt;!-- подвал --&gt;
                        </div>
                        <p>Второй аргумент <code>pageTop()</code> — ключ активного пункта меню. Благодаря ему нужный пункт будет выделен в боковой панели.</p>
                    </div>
                </article>

                <!-- 4.8 -->
                <article id="web8" class="lesson">
                    <div class="text-content">
                        <h2>4.8. Управление авторами</h2>
                        <p>Файл <code>authors.php</code> — пример типовой страницы. Все остальные страницы построены по такой же схеме. Действие определяется параметром <code>?action=</code> в адресе:</p>
                        <ul style="margin-left: 20px;">
                            <li><code>authors.php</code> — список</li>
                            <li><code>authors.php?action=add</code> — форма добавления</li>
                            <li><code>authors.php?action=edit&amp;id=3</code> — редактирование</li>
                            <li><code>authors.php?action=delete&amp;id=3</code> — удаление</li>
                        </ul>
                        <div class="content-placeholder">
                            <strong>Листинг 4.8. Сохранение формы</strong><br>
                            <?= TAB1 ?>$action = $_GET['action'] ?? 'list';<br>
                            <?= TAB1 ?>$id     = (int)($_GET['id'] ?? 0);<br><br>
                            <?= TAB1 ?>if ($_SERVER['REQUEST_METHOD'] === 'POST') {<br>
                            <?= TAB2 ?>$first_name = trim($_POST['first_name'] ?? '');<br>
                            <?= TAB2 ?>$last_name  = trim($_POST['last_name']  ?? '');<br>
                            <?= TAB2 ?>$birth_date = $_POST['birth_date'] ?: null;<br>
                            <?= TAB2 ?>$country    = trim($_POST['country'] ?? '') ?: null;<br><br>
                            <?= TAB2 ?>if (!$first_name || !$last_name) {<br>
                            <?= TAB2 ?><?= TAB1 ?>flash('error', 'Введите имя и фамилию');<br>
                            <?= TAB2 ?><?= TAB1 ?>go('authors.php?action=add');<br>
                            <?= TAB2 ?>}<br><br>
                            <?= TAB2 ?>if ($id) {<br>
                            <?= TAB2 ?><?= TAB1 ?>run("UPDATE Authors SET first_name=?, last_name=?, birth_date=?, country=? WHERE id=?",<br>
                            <?= TAB2 ?><?= TAB2 ?>[$first_name, $last_name, $birth_date, $country, $id]);<br>
                            <?= TAB2 ?><?= TAB1 ?>flash('success', 'Автор обновлён');<br>
                            <?= TAB2 ?>} else {<br>
                            <?= TAB2 ?><?= TAB1 ?>run("INSERT INTO Authors (first_name, last_name, birth_date, country) VALUES (?,?,?,?)",<br>
                            <?= TAB2 ?><?= TAB2 ?>[$first_name, $last_name, $birth_date, $country]);<br>
                            <?= TAB2 ?><?= TAB1 ?>flash('success', 'Автор добавлен');<br>
                            <?= TAB2 ?>}<br>
                            <?= TAB2 ?>go('authors.php');<br>
                            <?= TAB1 ?>}
                        </div>
                        <p>При удалении используется блок <code>try/catch</code>: если у автора есть книги, база данных запретит удаление из-за внешнего ключа, и блок <code>catch</code> покажет понятное сообщение вместо технической ошибки.</p>
                        <div class="content-placeholder">
                            <strong>Листинг 4.9. Удаление записи</strong><br>
                            <?= TAB1 ?>if ($action === 'delete' && $id) {<br>
                            <?= TAB2 ?>try {<br>
                            <?= TAB2 ?><?= TAB1 ?>run("DELETE FROM Authors WHERE id=?", [$id]);<br>
                            <?= TAB2 ?><?= TAB1 ?>flash('success', 'Автор удалён');<br>
                            <?= TAB2 ?>} catch (PDOException) {<br>
                            <?= TAB2 ?><?= TAB1 ?>flash('error', 'Нельзя удалить: есть связанные книги');<br>
                            <?= TAB2 ?>}<br>
                            <?= TAB2 ?>go('authors.php');<br>
                            <?= TAB1 ?>}
                        </div>
                    </div>
                </article>

                <!-- 4.9 -->
                <article id="web9" class="lesson">
                    <div class="text-content">
                        <h2>4.9. Остальные страницы</h2>
                        <p>Страницы <code>books.php</code>, <code>publishers.php</code>, <code>users.php</code>, <code>copies.php</code> устроены точно так же, как <code>authors.php</code>.</p>
                        <p>Страница выдачи <code>borrows.php</code> сложнее: при одной операции нужно обновить сразу две таблицы — создать запись в <code>Borrow_records</code> и изменить статус экземпляра в <code>Book_copies</code>:</p>
                        <div class="content-placeholder">
                            <strong>Листинг 4.10. Выдача книги</strong><br>
                            <?= TAB1 ?>// Создаём запись о выдаче<br>
                            <?= TAB1 ?>run("INSERT INTO Borrow_records (user_id, book_copy_id, borrow_date, due_date) VALUES (?,?,?,?)",<br>
                            <?= TAB2 ?>[$user_id, $copy_id, $borrow, $due]);<br><br>
                            <?= TAB1 ?>// Помечаем экземпляр как выданный<br>
                            <?= TAB1 ?>run("UPDATE Book_copies SET status = 'borrowed' WHERE id = ?", [$copy_id]);
                        </div>
                        <p>При возврате — обратная операция: устанавливается дата возврата и статус экземпляра меняется обратно на <code>available</code>.</p>
                    </div>
                </article>

                <!-- 4.10 -->
                <article id="web10" class="lesson">
                    <div class="text-content">
                        <h2>4.10. Готовое приложение</h2>
                        <p>Приложение готово. Оно включает семь страниц: главную со статистикой, а также управление книгами, авторами, издательствами, читателями, экземплярами и выдачей книг.</p>
                        <p>Ниже можно посмотреть, как выглядит интерфейс готового приложения — нажмите кнопку, чтобы открыть интерактивный предпросмотр:</p>

                        <div class="preview-block">
                            <a href="/library-preview.php" class="preview-btn" target="_blank">
                                Открыть интерактивный предпросмотр приложения
                            </a>
                            <p class="preview-note">Кликайте по пунктам меню, чтобы просмотреть все экраны</p>
                        </div>
                    </div>
                </article>

            </section>
        </div>
    </div>
</div>

<style>
    .course-container {
        color: rgb(47, 87, 85);
        width: 1200px;
        margin: 0 auto;
        padding: 10px;
    }

    .course-content {
        display: grid;
        grid-template-columns: 290px minmax(0, 1fr);
        gap: 40px;
    }

    .table-of-contents {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(67, 35, 35, 0.1);
        height: fit-content;
        position: sticky;
        top: 20px;
    }

    .table-of-contents h2 {
        color: rgb(47, 87, 85);
        margin-bottom: 20px;
        font-size: 1.3rem;
    }

    .toc-nav ul { list-style: none; padding: 0; margin: 0; }
    .toc-nav li { margin-bottom: 8px; }

    .toc-nav a {
        color: rgb(38, 76, 73);
        text-decoration: none;
        padding: 5px 0 5px 10px;
        display: block;
        transition: color 0.3s;
        border-left: 3px solid transparent;
    }

    .toc-nav a:hover {
        color: rgb(90, 150, 144);
        border-left-color: rgb(90, 150, 144);
        background-color: rgba(174, 216, 212, 0.1);
        border-radius: 4px;
    }

    .toc-nav ul ul { margin-left: 15px; margin-top: 5px; }
    .toc-nav ul ul a { font-size: 0.9rem; color: rgb(47, 87, 85); }

    .course-material {
        color: rgb(47, 87, 85);
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(67, 35, 35, 0.1);
        width: 100%;
        box-sizing: border-box;
    }

    .chapter { margin-bottom: 50px; }

    .chapter h2 {
        color: rgb(47, 87, 85);
        border-bottom: 3px solid rgb(90, 150, 144);
        padding-bottom: 10px;
        margin-bottom: 25px;
    }

    .lesson {
        margin-bottom: 35px;
        padding-bottom: 25px;
        border-bottom: 1px solid rgb(224, 217, 217);
    }

    .lesson:last-child { border-bottom: none; }

    .content-placeholder {
        background: rgb(243, 246, 246);
        padding: 18px 20px;
        border-radius: 8px;
        border-left: 4px solid rgb(90, 150, 144);
        color: rgb(47, 87, 85);
        font-family: 'Courier New', monospace;
        font-size: 0.87rem;
        line-height: 1.75;
        margin: 1em 0;
    }

    .text-content p { margin-bottom: 1em; }

    code {
        background: rgb(243, 246, 246);
        padding: 2px 6px;
        border-radius: 4px;
        font-family: 'Courier New', monospace;
        font-size: 0.9em;
        color: rgb(47, 87, 85);
    }

    ol { margin-left: 30px; }

    ul {
        margin-left: 40px;
        margin-top: 0.6em;
        margin-bottom: 0.8em;
    }

    li { margin-bottom: 10px; line-height: 1.3; }

    .chapter, .lesson { scroll-margin-top: 20px; }

    .preview-block {
        margin-top: 24px;
        padding: 30px;
        background: rgba(90, 150, 144, 0.07);
        border: 2px solid rgb(90, 150, 144);
        border-radius: 12px;
        text-align: center;
    }

    .preview-btn {
        display: inline-block;
        background: rgb(47, 87, 85);
        color: white;
        padding: 13px 32px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 1rem;
        font-weight: 600;
        transition: background 0.2s;
    }

    .preview-btn:hover { background: rgb(90, 150, 144); }

    .preview-note {
        margin-top: 12px;
        font-size: 0.88rem;
        color: rgb(90, 150, 144);
        margin-bottom: 0;
    }
</style>

<?php require_once __DIR__ . '/templates/footer.php'; ?>
