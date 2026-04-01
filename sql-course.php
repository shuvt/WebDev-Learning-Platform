<?php
// sql-course.php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/toggle-topic.php'; 

// Проверка авторизации
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
        <div class="table-of-contents">
            <h2>Содержание курса</h2>
            <nav class="toc-nav">
                <ul>
                    <li><a href="#introduction">Введение</a></li>
                    <li><a href="#sql-firebird">1. SQL FIREBIRD</a>
                        <ul>
                            <li><a href="#intro-db">1.1. Введение в базы данных</a></li>
                            <li><a href="#basic-sql">1.2. Основы SQL. Запросы</a></li>
                            <li><a href="#aggregation">1.3. Агрегация данных</a></li>
                            <li><a href="#joins">1.4. Соединение таблиц</a></li>
                            <li><a href="#subqueries">1.5. Подзапросы</a></li>
                            <li><a href="#procedural">1.6. Процедурное расширение SQL</a></li>
                            <li><a href="#triggers">1.7. Триггеры</a></li>
                            <li><a href="#transactions">1.8. Транзакции</a></li>
                            <li><a href="#indexes">1.9. Индексы</a></li>
                            <li><a href="#practice">1.10. Практические задания</a></li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>

        <div class="course-material">
            <!-- Введение -->
            <section id="introduction" class="chapter">
                <div class="text-content">
                    <p>Добро пожаловать в курс <strong>SQL Basics</strong>! Этот курс познакомит вас с основами языка запросов SQL и работой с базами данных Firebird.</p>
                    <p>В ходе обучения вы освоите основные конструкции SQL, научитесь создавать запросы, работать с таблицами и управлять данными.</p>
                    
                    <p style="line-height: 2.2;">Если вы уже знакомы с основами SQL - можете сразу перейти к 
                    <a href="#practice" style="display: inline-block; background: rgba(90,150,144,0.18); color: rgb(37, 72, 70); padding: 0px 15px; border-radius: 30px; text-decoration: none; font-weight: 500; 
                    font-size: 0.99rem; letter-spacing: 0.02em; margin: 0 2px; transition: all 0.2s ease; border: 1px solid transparent;" onmouseover="this.style.background='rgba(90,150,144,0.25)';
                     this.style.borderColor='rgba(47,87,85,0.4)';" onmouseout="this.style.background='rgba(90,150,144,0.18)'; this.style.borderColor='transparent';">
                     практическим заданиям</a>. Для новичков рекомендуется пройти весь курс и в конце закрепить знания на практике.</p>
                </div>
            </section>

            <!-- SQL FIREBIRD -->
            <section id="sql-firebird" class="chapter">
                <h2>1. SQL FIREBIRD</h2>

                <!-- 1.1 Введение в базы данных -->
                <article id="intro-db" class="lesson">
                    <h3>1.1. Введение в базы данных</h3>

                    <div class="text-content">
                        <h4>1.1.1. Зачем изучать SQL</h4>
                        <p><strong>SQL</strong> (сокращение от англ. Structured Query Language) — это язык запросов, которые структурированы особым образом. Данный язык применяется для работы с базами данных. Главная задача SQL — составлять запросы так, чтобы находить среди большого объёма информации ту, что нужна для конкретных целей, сортировать её, структурировать и представлять в наиболее простом и понятном виде.</p>

                        <p>В условиях стремительного роста объемов информации, понимание и использование SQL становится необходимых навыком для специалистов в различных областях: от программистов и аналитиков до маркетологов и финансовых экспертов.</p>

                        <p>SQL позволяет нам:</p>
                        <ul style="margin-left: 20px;">
                            <li>собирать и хранить данные в виде таблиц</li>
                            <li>изменять их содержимое и структуру</li>
                            <li>объединять данные и выполнять вычисления</li>
                            <li>защищать и распределять доступ</li>
                        </ul>

                        <h4 style="margin-top: 30px;">1.1.2. Основные понятия. Что такое база данных</h4>
                        <p>Одним из важнейших понятий в теории баз данных является понятие информационной системы. <strong>Информационная система</strong> – программный комплекс, функции которого состоят в: </p>
                        <ul style="margin-left: 20px;">
                            <li>долговременном хранении информации</li>
                            <li>выполнении специфических для данного приложения вычислений</li>
                            <li>предоставлении пользователям удобного и легко осваиваемого интерфейса</li>
                        </ul>
                        <p>Примеры некоторых информационных систем: системы автоматизации управления, банковские системы, системы резервирования билетов и прочие.</p>
                        <p><strong>База данных </strong>(БД) — это набор связанной информации. Например, телефонная книга — это база данных имен, номеров телефонов и адресов всех людей живущих в конкретном районе.</p>
                        <p>Однако поиск информации в телефонной книге вручную может занять много времени, особенно книга велика. Для решения этой проблемы можно использовать систему управления базами данных (СУБД). С помощью СУБД можно легко добавлять, изменять, удалять информацию и выполнять поиск по различным критериям. </p>
                        <p>Таким образом, <strong>система управления базами данных</strong> — совокупность языковых и программных средств, предназначенных для создания и использования баз данных. </p>

                        <h4 style="margin-top: 30px;">1.1.3. Типы баз данных: реляционные и нереляционные</h4>
                        <p><strong>Реляционные базы данных</strong> — это базы, в которых информация хранится в виде строго структурированных, связанных таблиц. Каждая строка таблицы представляет отдельную запись, а столбец — поле с назначенным ей типом данных. Основная идея реляционных баз данных заключается в том, что данные могут быть связаны друг с другом через общие поля. </p>
                        <p>Особенности реляционных баз данных:</p>
                        <ol>
                            <li>
                                <strong>Ключи.</strong> Помогают идентифицировать записи и устанавливать связи между таблицами. Рассмотрим подробнее основные типы ключей:
                                <ul>
                                    <li><strong>Первичный ключ (Primary Key)</strong> — это уникальный идентификатор для каждой записи в таблице. Не может содержать NULL-значения.<br>
                                        <em>Пример: В таблице "Сотрудники" первичным ключом может быть поле "ID сотрудника".</em>
                                    </li>

                                    <li><strong>Внешний ключ (Foreign Key)</strong> — это поле (или набор полей) в одной таблице, которое ссылается на первичный ключ в другой таблице. Он используется для установления и поддержания связи между таблицами.<br>
                                        <em>Пример: В таблице "Отделы" может быть внешний ключ "ID сотрудника", который ссылается на первичный ключ "ID сотрудника" в таблице "Сотрудники".</em>
                                    </li>

                                    <li><strong>Уникальный ключ (Unique Key)</strong> — это ограничение, которое гарантирует, что все значения в определенном столбце (или группе столбцов) уникальны в пределах таблицы. В отличие от первичного ключа, может содержать NULL-значения.<br>
                                        <em>Пример: Поле "Электронная почта" в таблице "Сотрудники" может быть уникальным, чтобы гарантировать, что два человека не могут иметь одинаковый адрес электронной почты.</em>
                                    </li>
                                </ul>
                            </li>

                            <li>
                                <strong>Отношения между таблицами.</strong> Реляционные базы данных поддерживают различные виды связи между таблицами через ключи. Рассмотрим их:
                                <ul>
                                    <li><strong>Один к одному</strong> — при таком типе связи каждой записи в одной таблице соответствует ровно одна запись в другой таблице.<br>
                                        <em>Пример: Таблица Пользователи и таблица Профили. Каждый пользователь может иметь только один профиль, и каждый профиль принадлежит только одному пользователю.</em>
                                    </li>

                                    <li><strong>Один ко многим</strong> — при таком типе связи, одна запись в первой таблице, может соответствовать нескольким записям во второй.<br>
                                        <em>Пример: Таблица авторы и таблица книги. Один автор может написать несколько книг, но каждая книга принадлежит только одному автору.</em>
                                    </li>

                                    <li><strong>Многие ко многим</strong> — при этом типе связи нескольким записям в одной таблице могут соответствовать несколько записей в другой таблице.<br>
                                        <em>Пример: Таблица Студенты и таблица Курсы. Один студент может записаться на несколько курсов, и один курс может иметь нескольких студентов.</em>
                                    </li>
                                </ul>
                            </li>

                            <li style="margin-bottom: 1.5em;">
                                <strong>Язык запросов.</strong> Реляционные базы данных используют SQL для выполнения операций с данными.
                            </li>
                        </ol>

                        <p> <strong>Нереляционные базы данных</strong> — хранят данные без чётких связей друг с другом и чёткой структуры. Вместо структурированных таблиц внутри базы находится множество разнородных документов, в том числе изображения, видео и даже публикации в социальных сетях. В отличие от реляционных БД, нереляционные базы данных не поддерживают запросы SQL.</p>
                        <p>По структуре нереляционные базы данных делятся на два вида:</p>
                        <ol>
                            <li><strong>Иерархические</strong>. Базы данных, в которых один элемент записи является главным, а остальные подчиненными</li>
                            <li><strong>Сетевые</strong>. Базы данных, в которых к вертикальным иерархическим связям добавляются горизонтальные связи</li>
                        </ol>
                        <img src="/images/db_structure.png" alt="Структуры баз данных" style="max-width: 100%; height: auto;">


                    </div>
                
                <?= topicButton('sql', 'intro-db', 'basic-sql') ?>
                </article>

                <!-- 1.2 Основы SQL -->
                <article id="basic-sql" class="lesson">
                    <h3>1.2. Основы SQL. Запросы</h3>
                    <div class="text-content">
                        <h4>1.2.1. Структура SQL-запросов</h4>
                        <p><strong>Запрос в SQL</strong> — это команда на языке SQL, которая позволяет взаимодействовать с реляционной базой данных. Запросы позволяют пользователю извлекать, модифицировать и управлять данными, содержащимися в базе данных. Запрос состоит из нескольких компонентов, или предложений. Обычно в запрос включается по крайней мере два или три из шести доступных предложений.<br> В таблице ниже представлены компоненты запроса и их краткое описание.</p>
                        <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                            <tr style="background-color: rgba(46, 93, 90, 1);; color: white;">
                                <th style="padding: 12px; border: 1px solid #ddd; text-align: left;">Наименование</th>
                                <th style="padding: 12px; border: 1px solid #ddd; text-align: left;">Описание</th>
                            </tr>
                            <tr>
                                <td style="padding: 10px; border: 1px solid #ddd;"><strong>select</strong></td>
                                <td style="padding: 10px; border: 1px solid #ddd;">Определяет, какие столбцы следует включить в результирующий набор запроса</td>
                            </tr>
                            <tr style="background-color: #f8f9fa;">
                                <td style="padding: 10px; border: 1px solid #ddd;"><strong>from</strong></td>
                                <td style="padding: 10px; border: 1px solid #ddd;">Определеяет таблицы, из которых следует выбирать данные, а также таблицы, которые должны быть соединены</td>
                            </tr>
                            <tr>
                                <td style="padding: 10px; border: 1px solid #ddd;"><strong>where</strong></td>
                                <td style="padding: 10px; border: 1px solid #ddd;">Отсеивает ненужные данные</td>
                            </tr>
                            <tr style="background-color: #f8f9fa;">
                                <td style="padding: 10px; border: 1px solid #ddd;"><strong>group by</strong></td>
                                <td style="padding: 10px; border: 1px solid #ddd;">Используется для группировки строк по общим значениям столбцов</td>
                            </tr>
                            <tr>
                                <td style="padding: 10px; border: 1px solid #ddd;"><strong>having</strong></td>
                                <td style="padding: 10px; border: 1px solid #ddd;">Отсеивает ненужные данные</td>
                            </tr>
                            <tr style="background-color: #f8f9fa;">
                                <td style="padding: 10px; border: 1px solid #ddd;"><strong>order by</strong></td>
                                <td style="padding: 10px; border: 1px solid #ddd;">Сортирует строки окончательного результирующего набора по одному или нескольким столбцам</td>
                            </tr>
                        </table>
                        <p>Пример базового SQL-запроса: </p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 1.1. Пример базового SQL-запроса</strong><br>
                                <?= TAB1 ?>SELECT column1, column2<br>
                                <?= TAB1 ?>FROM table_name<br>
                                <?= TAB1 ?>WHERE condition<br>
                                <?= TAB1 ?>ORDER BY column1;
                            </p>
                        </div>
                        <h4 style="margin-top: 2em">1.2.2. Команды (SELECT), Условия (WHERE), Сортировка данных (ORDER BY)</h4>
                        <p style="margin-bottom: 0.4em"><strong>SELECT</strong></p>
                        <p>Хотя оператор SELECT является первым в запросе, сервер базы данных обрабатывает его на одном из последних этапов вычислений. Это происходит из-за того, что для формирования окончательного набора данных необходимо знать все потенциальные столбцы, которые могут быть включены в этот набор. Поэтому, чтобы понять, как функционирует оператор SELECT, важно также разобраться с оператором FROM.</p>
                        <p style="margin-bottom: 0.4em"><strong>FROM</strong></p>
                        <p>Предложение from определяет таблицы, используемые запросом, наряду со средствами связывания таблиц вместе.<br>
                            Рассмотрим для начала такой запрос:
                        </p>

                        <p style="padding: 15px; background: rgb(250, 240, 254); font-style: italic; color: rgb(150, 106, 194);">
                            <strong>Все следующие примеры будут на основе базы данных employee.fdb, которая находится в дистрибутиве Firebird!</strong>
                        </p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 1.2. Простой SQL запрос</strong><br>
                                <?= TAB1 ?>SELECT *<br>
                                <?= TAB1 ?>FROM EMPLOYEE;
                            </p>
                        </div>
                        <p>В этом запросе предложение from перечисляет одну таблицу (EMPLOYEE), а предложение select указывает, что в результирующий набор должны быть включены все столбцы (что обозначено с помощью *) таблицы. Таким образом, этот запрос может быть интерпретирован на обычном языке как: Покажи мне все столбцы и все строки таблицы EMPLOYEE.<br> Вместо использования символа звездочки (*) для обозначения всех столбцов, можно также перечислить конкретные интересующие вас столбцы, например:</p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 1.3. Демонстрация запроса</strong><br>
                                <?= TAB1 ?>SELECT FIRST_NAME, LAST_NAME<br>
                                <?= TAB1 ?>FROM EMPLOYEE;
                            </p>
                        </div>
                        <p>Этот запрос вернет имена и фамилии всех сотрудников из таблицы employees.</p>
                        <p style="margin-bottom: 0.4em;"><strong>ПСЕВДОНИМЫ</strong></p>
                        <p>Хотя Firebird автоматически создает метки для столбцов, возвращаемых вашими запросами, вы можете задать для них собственные названия. Это можно сделать, используя псевдоним столбца после соответствующего элемента в операторе SELECT.</p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 1.4. Использование псевдонимов</strong><br>
                                <?= TAB1 ?>SELECT FIRST_NAME AS 'name'<br>
                                <?= TAB1 ?>FROM EMPLOYEE;
                            </p>
                        </div>
                        <p>В этом примере столбец first_name будет возвращен с псевдонимом "name".</p>
                        <p style="margin-bottom: 0.4em;"><strong>WHERE</strong></p>
                        <p>В некоторых случаях может потребоваться получить все строки из таблицы, особенно для небольших таблиц. Однако в большинстве случаев выбирать все строки из таблицы не требуется, а потому необходим способ отфильтровывать строки, которые не представляют интереса. В этом состоит работа предложения where. Например, чтобы выбрать всех сотрудников из отдела номер 5, мы можем использовать следующий запрос:</p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 1.5. Пример использования where</strong><br>
                                <?= TAB1 ?>SELECT *<br>
                                <?= TAB1 ?>FROM employee <br>
                                <?= TAB1 ?>WHERE dept_no = '5';
                            </p>
                        </div>
                        <p>С помощью операторов AND и OR можно отфильтровать строки сразу по нескольким условиям. Например, выберем сотрудников из отдела номер 5 принятых на работу в 1992 году:</p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 1.6. Пример использования where вместе с and и or</strong><br>
                                <?= TAB1 ?>SELECT * <br>
                                <?= TAB1 ?>FROM employee<br>
                                <?= TAB1 ?>WHERE dept_no = '5' AND hire_date BETWEEN '01.01.1992' AND '31.12.1992';
                            </p>
                        </div>
                        <p>При разделении условия с использованием оператора and, чтобы строка вошла в результирующий набор, все условия должны вычисляться как true. При использовании or для включения строки достаточно, чтобы значение true давало только одно из условий.</p>
                        <p>Когда необходимо использовать AND и OR в одном SQL-запросе, важно правильно расставить скобки, чтобы определить порядок выполнения условий. </p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 1.7. Корректное расставление скобок</strong><br>
                                <?= TAB1 ?>SELECT * <br>
                                <?= TAB1 ?>FROM employee <br>
                                <?= TAB1 ?>WHERE (dept_no = '5' AND hire_date = '01.01.2000') OR (dept_no = '10');
                            </p>
                        </div>
                        <p style="margin-bottom: 0.4em;"><strong>ORDER BY</strong></p>
                        <p>Как правило, строки в результирующем наборе, возвращаемом запросом, не находятся ни в каком конкретном порядке. Если же вы хотите, чтобы ваш результирующий набор был отсортирован, укажите серверу о необходимости сортировать результаты с помощью предложения order by.<br>
                            ORDER BY позволяет сортировать результаты запроса по одному или нескольким столбцам. При этом можно указать порядок сортировки: по возрастанию (ASC) или по убыванию (DESC). Если порядок не указан, по умолчанию применяется сортировка по возрастанию.
                        </p>
                        <div class="content-placeholder">
                        <p style="margin-bottom: 0em;">
                            <strong>Листинг 1.8. Пример использования сортировок</strong><br>
                            <?= TAB1 ?>SELECT * <br>
                            <?= TAB1 ?>FROM employee<br>
                            <?= TAB1 ?>ORDER BY dept_no ASC, salary DESC;
                        </p>
                    </div>
                    </div>
                
                <?= topicButton('sql', 'basic-sql', 'aggregation') ?>
                </article>

                <!-- 1.3 Агрегация данных -->
                <article id="aggregation" class="lesson">
                    <h3>1.3. Агрегация данных</h3>
                    <div class="text-content">
                        <p>Агрегатные функции в SQL позволяют выполнять вычисления над множеством значений и возвращать одно значение. В данном пункте рассмотрим основные агрегатные функции, группировку данных с помощью команды GROUP BY и фильтрацию агрегированных данных с помощью HAVING.</p>
                        <h4>1.3.1. Использование агрегатных функций: COUNT, SUM, AVG, MIN, MAX</h4>
                        <p>Важно учесть, что агрегатные функции, за исключением COUNT(*), не учитывают значения NULL.</p>

                        <p style="margin-bottom: 0.4em;"><strong>COUNT</strong></p>
                        <p>Эта функция возвращает количество строк, соответствующих заданным условиям.<br>
                            Count(*) используется для подсчета количества строк в таблице. Count(столбец) используется для подсчета количества строк, содержащих значения в определенном столбце. <br>
                            Рассмотрим запрос, который возвращает общее количество сотрудников в таблице employees.
                        </p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 1.9. Пример использования агрегатной функции COUNT</strong><br>
                                <?= TAB1 ?>SELECT COUNT(*) AS total_employee<br>
                                <?= TAB1 ?>FROM employee;
                            </p>
                        </div>

                        <p style="margin-bottom: 0.4em;"><strong>ДУБЛИКАТЫ</strong></p>
                        <p>В некоторых случаях запрос может возвращать повторяющиеся строки данных. Таблица employee содержит информацию о сотрудниках, включая их должности. Так как несколько сотрудников могут занимать одну и ту же должность, не исключено наличие дубликатов. Избавиться от дубликатов можно с помощью ключевого слова DISTINCT. </p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 1.10 Получение всех уникальных должностей</strong><br>
                                <?= TAB1 ?>SELECT COUNT(DISTINCT job_code) AS unique_job_codes <br>
                                <?= TAB1 ?>FROM employee;
                            </p>
                        </div>
                        <p style="margin-bottom: 0.4em;"><strong>SUM</strong></p>
                        <p>Функция SUM суммирует значения в указанном столбце. Функции SUM() и AVG() применимы только к столбцам, содержащим числовые данные. </p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 1.11 Вычисление общей суммы зарплат всех сотрудников</strong><br>
                                <?= TAB1 ?>SELECT SUM(salary) AS total_salary<br>
                                <?= TAB1 ?>FROM employee;
                            </p>
                        </div>
                        <p style="margin-bottom: 0.4em;"><strong>AVG</strong></p>
                        <p>Функция AVG вычисляет среднее значение для указанного столбца. </p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 1.12 Вычисление средней зарплаты сотрудников</strong><br>
                                <?= TAB1 ?>SELECT AVG(salary) AS average_salary<br>
                                <?= TAB1 ?>FROM employee;
                            </p>
                        </div>
                        <p style="margin-bottom: 0.4em;"><strong>MIN</strong></p>
                        <p>Функция MIN возвращает минимальное значение в указанном столбце. Функции MIN() и MAX(), кроме числовых значений могут обрабатывать данные типа «дата–время». </p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 1.13 Применение агрегатной функции MIN</strong><br>
                                <?= TAB1 ?>SELECT MIN(salary) AS lowest_salary<br>
                                <?= TAB1 ?>FROM employee;
                            </p>
                        </div>
                        <p style="margin-bottom: 0.4em;"><strong>MAX</strong></p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 1.14 Применение агрегатной функции MAX</strong><br>
                                <?= TAB1 ?>SELECT MAX(salary) AS highest_salary<br>
                                <?= TAB1 ?>FROM employee;
                            </p>
                        </div>

                        <h4>1.3.2. Группировка данных (GROUP BY)</h4>
                        <p>Предложение GROUP BY соединяет записи, имеющие одинаковую комбинацию значений полей, указанных в его списке, в одну запись. Агрегатные функции в списке выбора применяются к каждой группе индивидуально, а не для всего набора в целом. </p>
                        <p>Если в списке выборки содержатся как агрегатные столбцы, так и столбцы, чьи значения зависит от выбираемых строк, то предложение GROUP BY становится обязательным. </p>
                        <p>При работе с группировкой нужно учитывать следующее:</p>
                        <ul style="margin-left: 20px;">
                            <li>В качестве элементов группировки можно использовать только столбцы таблицы или выражения, вычисленные на основе значений в этих столбцах.</li>
                            <li>В выводимой таблице, формируемой с помощью оператора SELECT, могут содержаться либо элементы группировки, либо константы, либо выражения с применением агрегатных функций для тех столбцов, которые не задействованы в группировке.</li>
                            <li>Каждая сформированная группа уникальна, что исключает возможность дублирования, и, следовательно, применение оператора DISTINCT становится нецелесообразным.</li>
                        </ul>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 1.15 Вычисление средней зарплаты сотрудников по отделам</strong><br>
                                <?= TAB1 ?>SELECT dept_no, AVG(salary) AS avg_salary<br>
                                <?= TAB1 ?>FROM employee<br>
                                <?= TAB1 ?>GROUP BY dept_no
                            </p>
                        </div>

                        <h4>1.3.3. Использование HAVING для фильтрации агрегированных данных</h4>
                        <p>При группировке данных также можно применить фильтрующее условие HAVING к данным после того, как были сгенерированы группы. Это позволяет отфильтровывать группы на основе агрегированных значений, в отличие от оператора WHERE, который фильтрует строки перед агрегацией. </p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 1.16 Применение фильтрующего условия</strong><br>
                                <?= TAB1 ?>SELECT dept_no, AVG(salary) AS avg_salary<br>
                                <?= TAB1 ?>FROM employee<br>
                                <?= TAB1 ?>GROUP BY dept_no<br>
                                <?= TAB1 ?>HAVING AVG(salary) > 100000
                            </p>
                        </div>
                    </div>
                
                <?= topicButton('sql', 'aggregation', 'joins') ?>
                </article>

                <!-- 1.4 Соединение таблиц -->
                <article id="joins" class="lesson">
                    <h3>1.4. Соединение таблиц</h3>
                    <div class="text-content">
                        <h4>1.4.1. Типы соединений: INNER JOIN, LEFT JOIN, RIGHT JOIN</h4>
                        <p><strong>Соединения в SQL</strong> — это инструмент для соединения двух и более таблиц по условию.</p>
                        <p>Синтаксис оператора JOIN c ON</p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 1.17 Синтаксис оператора JOIN c ON</strong><br>
                                <?= TAB1 ?>SELECT названия_столбцов <br>
                                <?= TAB2 ?>FROM название_таблицы_1 JOIN название_таблицы_2 <br>
                                <?= TAB2 ?>ON условие
                            </p>
                        </div>
                        <p>Синтаксис оператора JOIN c USING</p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 1.18 Синтаксис оператора JOIN c USING</strong><br>
                                <?= TAB1 ?>SELECT названия_столбцов <br>
                                <?= TAB2 ?>FROM название_таблицы_1 JOIN название_таблицы_2 USING (условие)
                            </p>
                        </div>
                        <p style="margin-bottom: 0.4em;"><strong>Внутреннее соединение </strong></p>
                        <p><strong>INNER JOIN, JOIN</strong> — соединение по умолчанию, из таблиц выделяются все возможные пары, для которых условие соединения истинно. </p>
                        <img src="/images/inner_join.png" alt="Структуры баз данных" style="width: 250px; height: auto;margin-left: 40px;margin-bottom:2em; margin-top:1em">
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 1.19 Пример с соединением таблиц employee и department</strong><br>
                                <?= TAB1 ?>SELECT e.first_name, e.last_name, d.department<br>
                                <?= TAB1 ?>FROM employee e INNER JOIN department d<br>
                                <?= TAB1 ?>ON e.dept_no = d.dept_no
                            </p>
                        </div>

                        <p style="margin-bottom: 0.4em;"><strong>Левое внешнее соединение </strong></p>
                        <p><strong>LEFT JOIN</strong> — из левой таблицы выбираются все записи. Из правой выбираются только те, которые удовлетворяют условию соединения. Если для какой-либо записи левой таблицы не нашлась ни одна пара, то столбцы правой таблицы заменяются NULL-значениями.</p>
                        <img src="/images/left_join.png" alt="Структуры баз данных" style="width: 250px; height: auto;margin-left: 40px;margin-bottom:2em; margin-top:1em">
                        <p>Изменим внутреннее из прошлого примера на LEFT JOIN. Теперь, если у сотрудника нет соответствующего отдела, в результате будет NULL для столбца department. </p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 1.20 Левое внешнее соединение таблиц employee и department</strong><br>
                                <?= TAB1 ?>SELECT e.first_name, e.last_name, d.department<br>
                                <?= TAB1 ?>FROM employee e LEFT JOIN department d<br>
                                <?= TAB1 ?>ON e.dept_no = d.dept_no;
                            </p>
                        </div>

                        <p style="margin-bottom: 0.4em;"><strong>Правое внешнее соединение </strong></p>
                        <p><strong>RIGHT JOIN</strong> — Эквивалентно левому, если поменять таблицы местами. </p>
                        <img src="/images/right_join.png" alt="Структуры баз данных" style="width: 250px; height: auto;margin-left: 40px;margin-bottom:2em; margin-top:1em">
                        <p>Если в прошлом примере использовать RIGHT JOIN вместо LEFT JOIN и в таблице department не будет соответствующего сотрудника, для столбцов из таблицы employee в результате будет NULL-значение. </p>
                        <div class="content-placeholder">
                        <p style="margin-bottom: 0em;">
                            <strong>Листинг 1.21 Правое внешнее соединение таблиц employee и department</strong><br>
                            <?= TAB1 ?>SELECT e.first_name, e.last_name, d.department<br>
                            <?= TAB1 ?>FROM employee e RIGHT JOIN department d<br>
                            <?= TAB1 ?>ON e.dept_no = d.dept_no;
                        </p>
                    </div>

                        <p style="margin-bottom: 0.4em;"><strong>Полное соединение </strong></p>
                        <p><strong>FULL JOIN</strong>— представляет собой результат объединения левого и правого соединения</p>
                        <img src="/images/full_join.png" alt="Структуры баз данных" style="width: 250px; height: auto;margin-left: 40px; margin-bottom:2em; margin-top:1em">

                        <h4>1.4.2 Объединение (оператор UNION)</h4>
                        <p>Оператор <strong>UNION</strong> в SQL используется для объединения результатов двух или более запросов SELECT. Позволяет объединять строки из разных таблиц в один результирующий набор. </p>
                        <p>Синтаксис объединения</p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 1.22 Синтаксис объединения</strong><br>
                                <?= TAB1 ?>SELECT column1, column2, ...<br>
                                <?= TAB1 ?>FROM table1<br>
                                <?= TAB1 ?>UNION<br>
                                <?= TAB1 ?>SELECT column1, column2, ...<br>
                                <?= TAB1 ?>FROM table2;
                            </p>
                        </div>
                        <p>Вывод имен заказчиков и работников</p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 1.23 Вывод имен заказчиков и работников</strong><br>
                                <?= TAB1 ?>SELECT first_name<br>
                                <?= TAB1 ?>FROM employee<br>
                                <?= TAB1 ?>UNION<br>
                                <?= TAB1 ?>SELECT contact_first<br>
                                <?= TAB1 ?>FROM customer;
                            </p>
                        </div>

                    </div>
                
                <?= topicButton('sql', 'joins', 'subqueries') ?>
                </article>

                <!-- 1.5 Подзапросы -->
                <article id="subqueries" class="lesson">
                    <h3>1.5. Подзапросы</h3>
                    <div class="text-content">

                        <h4>1.5.1. Что такое подзапросы и когда их использовать. Виды подзапросов</h4>
                        <p><strong>Подзапрос</strong> — это запрос, который вложен в другой запрос. Подзапрос всегда заключен в круглые скобки и обычно выполняется перед содержащей инструкцией. Подобно любому запросу, подзапрос возвращает результирующий набор, который может состоять из: </p>
                        <ul style="margin-left: 20px;">
                            <li>Одной строки с одним столбцом</li>
                            <li>Нескольких строк с одним столбцом</li>
                            <li>Нескольких строк с несколькими столбцами</li>
                        </ul>
                        <p>Подзапросы могут использоваться в различных частях основного запроса, таких как SELECT, WHERE, FROM и HAVING<br>Когда использовать подзапросы: </p>
                        <ul style="margin-left: 20px;">
                            <li>Когда необходимо использовать результат одного запроса в другом запросе</li>
                            <li>Когда нужно упростить сложные запросы</li>
                            <li>Когда требуется фильтрация данных на основе значений из другой таблицы</li>
                        </ul>
                        <p>Подзапросы делятся на коррелированные и некоррелированные. Дадим им определения. </p>
                        <p>Подзапрос называется <strong>некоррелированным (простым)</strong>, если он может рассматриваться независимо от внешнего запроса. СУБД выполняет такой подзапрос один раз и затем помещает его результат во внешний запрос. Такие подзапросы обрабатываются системой «снизу вверх». Первым обрабатывается вложенный подзапрос самого нижнего уровня, множество значений, полученное в результате его выполнения, используется при реализации подзапроса более высокого уровня и т.д.</p>
                        <p><strong>Коррелированный (сложный)</strong> подзапрос не может рассматриваться независимо от внешнего запроса. В этом случае выполнение оператора начинается с внешнего запроса, который отбирает каждую отдельную строку таблицы. Для каждой выбранной строки СУБД выполняет подзапрос один раз.</p>

                        <h4>1.5.2. Некоррелированные подзапросы и дополнительные операторы к ним IN, ALL, ANY(SOME)</h4>
                        <p>Рассмотрим пример некоррелированного подзапроса</p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 1.24 Пример некоррелированного подзапроса</strong><br>
                                <?= TAB1 ?>SELECT emp_no, first_name, last_name<br>
                                <?= TAB1 ?>FROM employee<br>
                                <?= TAB1 ?>WHERE emp_no = (SELECT MAX(emp_no) FROM employee)
                            </p>
                        </div>
                        <p>Пример, показанный выше — некоррелированный подзапрос, который может быть выполнен отдельно и не ссылается ни на что из содержащей инструкции. Помимо того, что это некоррелированный подзапрос, данный пример также возвращает результирующий набор, содержащий только одну строку и один столбец. Этот тип подзапроса известен как скалярный подзапрос и может появляться на любой стороне условия, использующего обычные операторы сравнения (=, <,>, <=,>=)</p>
                        <p>Если подзапрос возвращает более одной строки, его нельзя использовать в условии равенства. Однако есть четыре дополнительных оператора, которые можно использовать для создания условий с этими типами подзапросов.</p>
                        <p style="margin-bottom: 0.4em;"><strong>Операторы IN и NOT IN </strong></p>
                        <p>Хотя проверить на равенство одно значение с набором значений нельзя, можно проверить, входит ли конкретное значение в набор. Следующий пример (пусть и не использующий подзапрос) демонстрирует, как создать условие, которое использует оператор in для поиска для значения в наборе значений: </p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 1.25 Пример использования оператора IN</strong><br>
                                <?= TAB1 ?>SELECT currency<br>
                                <?= TAB1 ?>FROM country<br>
                                <?= TAB1 ?>WHERE country IN ('Canada', 'USA');
                            </p>
                        </div>
                        <p>Выражение в левой части условия — столбец country, а в правой — набор строк. Оператор in проверяет, входит ли строка из столбца country в этот набор; если да, то условие выполняется и строка добавляется к результирующему набору.</p>
                        <p>Помимо проверки, имеется ли некоторое значение в наборе, вы можете проверить обратное, используя оператор not in.</p>
                        <p>В следующем примере оператор not in используется с подзапросом в правой части условия фильтра, чтобы получить все имена работников, работающих не в Канаде или США:</p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 1.26 Пример использования оператора NOT IN</strong><br>
                                <?= TAB1 ?>SELECT first_name<br>
                                <?= TAB1 ?>FROM employee<br>
                                <?= TAB1 ?>WHERE job_country NOT IN<br>
                                <?= TAB2 ?>(SELECT country<br>
                                <?= TAB2 ?>FROM country <br>
                                <?= TAB2 ?>WHERE country IN ('Canada', 'USA'));
                            </p>
                        </div>
                        <p style="margin-bottom: 0.4em;"><strong>Оператор ALL </strong></p>
                        <p>В то время как оператор in используется, чтобы выяснить, имеется ли выражение в наборе выражений, оператор all позволяет сравнивать отдельное значение и каждое значение в наборе. Чтобы создать такое условие, нужно использовать один из операторов сравнения (=, <,>, и т.д.) в сочетании с оператором all. </p>
                        <p>Рассмотрим следующий пример в котором запрос находит всех сотрудников, чья зарплата больше, чем зарплата всех сотрудников в определенной стране, например, в США</p>
                        <div class="content-placeholder">
                        <p style="margin-bottom: 0em;">
                            <strong>Листинг 1.27 Пример использования оператора ALL</strong><br>
                            <?= TAB1 ?>SELECT first_name<br>
                            <?= TAB1 ?>FROM employee<br>
                            <?= TAB1 ?>WHERE salary > ALL (<br>
                            <?= TAB2 ?>SELECT salary<br>
                            <?= TAB2 ?>FROM employee<br>
                            <?= TAB2 ?>WHERE job_country = (SELECT c.country<br>
                            <?= TAB1 ?><?= TAB2 ?>FROM country c<br>
                            <?= TAB1 ?><?= TAB2 ?>WHERE c.country = 'USA'));
                        </p>
                    </div>
                        <p style="margin-bottom: 0.4em;"><strong>Оператор ANY </strong></p>
                        <p>Как и оператор all, оператор any позволяет сравнивать значение с членами набора значений. В отличие от all, однако, условие, использующее оператор any, вычисляется как истинное, как только найдется хотя бы одно выполняющееся сравнение. </p>
                        <p>В приведенном далее примере выполняется поиск всех всех сотрудников, чья зарплата больше, чем зарплата хотя бы одного отрудника в США</p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 1.28 Пример использования оператора ANY</strong><br>
                                <?= TAB1 ?>SELECT first_name<br>
                                <?= TAB1 ?>FROM employee<br>
                                <?= TAB1 ?>WHERE salary > ANY (<br>
                                <?= TAB2 ?>SELECT salary<br>
                                <?= TAB2 ?>FROM employee<br>
                                <?= TAB2 ?>WHERE job_country = (SELECT c.country<br>
                                <?= TAB1 ?><?= TAB2 ?>FROM country c<br>
                                <?= TAB1 ?><?= TAB2 ?>WHERE c.country = 'USA'));
                            </p>
                        </div>
                        <p>Операторы ANY и SOME эквивалентны, т.е. результат их работы будет одинаковый.</p>

                        <h4>1.5.3 Коррелированные подзапросы. EXISTS, SINGULAR </h4>
                        <p>Все подзапросы, показанные до этого, не зависели от содержащихся в них операторов, Это означает, что вы можете выполнить их автономно и проверить возвращаемые ими результаты. Коррелированный же подзапрос зависит от содержащей его инструкции, ссылаясь на один или несколько ее столбцов. В отличие от некоррелированного подзапроса, коррелированный подзапрос не выполняется один раз перед выполнением содержащей его инструкции. Вместо этого коррелированный подзапрос выполняется по одному разу для каждой строки, которая может быть включена в окончательный результат</p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 1.29 Пример коррелированного подзапроса</strong><br>
                                <?= TAB1 ?>SELECT e.full_name, e.salary,<br>
                                <?= TAB2 ?>(SELECT MAX(e2.salary)<br>
                                <?= TAB2 ?>FROM employee e2<br>
                                <?= TAB2 ?>WHERE e2.job_country = e.job_country) AS max_salary<br>
                                <?= TAB1 ?>FROM employee e
                            </p>
                        </div>
                        <p>Запрос представленный выше выдаст справочную информацию о максимальной оплате в той стране, где работает сотрудник.</p>
                        <p style="margin-bottom: 0.4em;"><strong>Оператор EXISTS(NOT EXISTS) </strong></p>
                        <p>Оператор EXIST определяет, существует ли хотя бы одно значение в выходном результате подзапроса. Противоположным ему является NOT EXISTS.</p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 1.30 Пример использования оператора EXIST</strong><br>
                                <?= TAB1 ?>SELECT e.full_name<br>
                                <?= TAB1 ?>FROM employee e<br>
                                <?= TAB1 ?>WHERE EXISTS<br>
                                <?= TAB2 ?>(SELECT 1 <br>
                                <?= TAB2 ?>FROM project p<br>
                                <?= TAB2 ?>WHERE p.team_leader = e.emp_no);
                            </p>
                        </div>
                        <p>Запрос выведет список сотрудников, которые являются руководителями проектов. </p>

                        <p style="margin-bottom: 0.4em;"><strong>Оператор SINGULAR </strong></p>
                        <p>Оператор SINGULAR проверяет, возвращает ли подзапрос в точности одно значение.</p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 1.31 Пример использования оператора SINGULAR</strong><br>
                                <?= TAB1 ?>SELECT full_name<br>
                                <?= TAB1 ?>FROM employee e<br>
                                <?= TAB1 ?>WHERE SINGULAR <br>
                                <?= TAB2 ?>(SELECT * <br>
                                <?= TAB2 ?>FROM sales s<br>
                                <?= TAB2 ?>WHERE s.sales_rep = e.emp_no);
                            </p>
                        </div>
                        <p>Запрос выведет список сотрудников, которые оформили только один заказ.</p>
                    </div>
                
                <?= topicButton('sql', 'subqueries', 'procedural') ?>
                </article>

                <!-- 1.6 Процедурное расширение -->
                <article id="procedural" class="lesson">
                    <h3>1.6. Процедурное расширение SQL</h3>
                    <div class="text-content">
                        <h4>1.6.1. Процедурный язык PSQL </h4>
                        <p><strong>Хранимая процедура</strong> — это программный модуль, который может быть вызван с клиента, из другой процедуры, функции, выполнимого блока или триггера. Хранимые процедуры, хранимые функции, исполняемые блоки и триггеры пишутся на процедурном языке SQL (PSQL). Большинство операторов SQL доступно и в PSQL, иногда с ограничениями или расширениями. Заметными исключениями являются DDL и операторы управления транзакциями. Хранимые процедуры могут принимать и возвращать множество параметров.</p>
                        <p>Синтаксис процедуры:</p>
                        <div class="content-placeholder">
                        <p style="margin-bottom: 0em;">
                            <strong>Листинг 1.32 Синтаксис процедуры</strong><br>
                            <?= TAB1 ?>CREATE PROCEDURE &lt;имя_процедуры&gt; (&lt;входные_параметры&gt;)<br>
                            <?= TAB1 ?>RETURNS (выходные_параметры)<br>
                            <?= TAB2 ?>AS<br>
                            <?= TAB2 ?>[&lt;список_локальных_переменных&gt;]<br>
                            <?= TAB2 ?>BEGIN<br>
                            <?= TAB1 ?><?= TAB2 ?>&lt;код_процедуры&gt;<br>
                            <?= TAB2 ?>END
                        </p>
                    </div>
                        <p>Существуют два вида хранимых процедур — выполняемые хранимые процедуры и селективные процедуры</p>
                        <ul style="margin-left: 20px;">
                            <li><strong>Выполняемые хранимые процедуры</strong>, осуществляют обработку данных, находящихся в базе данных. Эти процедуры могут получать входные параметры и возвращать одиночный набор выходных (RETURNS) параметров. Такие процедуры выполняются с помощью оператора EXECUTE PROCEDURE. </li>
                            <li><strong>Селективные хранимые процедуры</strong> обычно осуществляют выборку данных из базы данных и возвращают при этом произвольное количество строк. Такие процедуры позволяют получать довольно сложные наборы данных, которые зачастую невозможно или весьма затруднительно получить с помощью обычных SELECT запросов.
                                Обычно такие процедуры выполняют циклический процесс извлечения данных, возможно преобразуя их, прежде чем заполнить выходные переменные (параметры) новыми данными на каждой итерации цикла. Оператор SUSPEND, обычно расположенный в конце каждой итерации, заполняет буфер и ожидает пока вызывающая сторона не выберет (fetch) строку. Селективные хранимые процедуры могут иметь входные параметры и выходное множество, заданное в предложении RETURNS заголовка процедуры.</li>
                        </ul>
                        <p style="margin-bottom: 0.4em;"><strong>Процедуры без параметров </strong></p>
                        <p>Такие процедуры могут выполнять какие-либо действия, которым не требуется входные данные для работы, и которым не требуется выдавать какой-либо результат</p>
                        <p>Пример процедуры без параметров</p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 1.33 Пример процедуры без параметров</strong><br>
                                <?= TAB1 ?>create procedure test<br>
                                <?= TAB1 ?>as<br>
                                <?= TAB1 ?>begin<br>
                                <?= TAB2 ?>insert into testtable (field1) values (1);<br>
                                <?= TAB1 ?>end
                            </p>
                        </div>
                        <p>Каждый оператор в процедуре должен быть завершен символом ;
                            <br>В такой процедуре могут быть объявлены локальные переменные (declare variable), для организации каких-либо вычислений.
                        </p>
                        <p>Объявление локальных переменных в процедуре</p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 1.34 Объявление локальных переменных в процедуре</strong><br>
                                <?= TAB1 ?>create procedure test<br>
                                <?= TAB1 ?>as<br>
                                <?= TAB1 ?>declare variable i int;<br>
                                <?= TAB1 ?>begin<br>
                                <?= TAB2 ?>i=1;<br>
                                <?= TAB2 ?>insert into testtable (field1) values (:i);<br>
                                <?= TAB1 ?>end
                            </p>
                        </div>
                        <p>Такие процедуры вызываются оператором </p>
                        <p style="font-style: italic">EXECUTE PROCEDURE <имя процедуры>
                        </p>
                        <p>Рассмотрим подробнее селективные процедуры. Если использовать execute procedure, то можно получить всегда только одно значение, или как бы "одну строку", в виде набора значений, но не "несколько строк".
                            <br> Процедуры в Firebird могут выдавать данные таким образом, что их можно вызывать через select. Отсюда и название — "селективные" процедуры.
                        </p>
                        <p>Пример селективной процедуры: </p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 1.35 Пример селективной процедуры</strong><br>
                                <?= TAB1 ?>create procedure test<br>
                                <?= TAB1 ?>returns (n varchar(35))<br>
                                <?= TAB1 ?>as<br>
                                <?= TAB1 ?>declare variable ln varchar(20);<br>
                                <?= TAB1 ?>declare variable fn varchar(15);<br>
                                <?= TAB1 ?>begin<br>
                                <?= TAB2 ?>for select last_name, first_name<br>
                                <?= TAB2 ?><?= TAB1 ?>from employee<br>
                                <?= TAB2 ?><?= TAB1 ?>into :ln, :fn<br>
                                <?= TAB2 ?>do<br>
                                <?= TAB2 ?>begin<br>
                                <?= TAB1 ?><?= TAB2 ?>n = :fn || ' ' || :ln;<br>
                                <?= TAB1 ?><?= TAB2 ?>suspend;<br>
                                <?= TAB2 ?>end<br>
                                <?= TAB1 ?>end
                            </p>
                        </div>
                        <p>Процедура перебирает все записи таблицы employee и возвращает нам "склеенные" имя и фамилию сотрудников. Размер возвращаемой переменной n выбран как сумма размеров столбцов first_name и last_name таблицы empoyee и переменных ln и fn, чтобы во время обработки не возникло переполнения.</p>
                        <p>Ключевым в работе процедуры является указание suspend. В тот момент, когда выполнение процедуры доходит до suspend, сервер останавливает выполнение процедуры, и "ждет", пока клиент не попросит получить данные "из процедуры". После получения данных (одной "записи") сервер прокрутит следующий цикл for select до очередного suspend, и так далее, пока клиент не перестанет просить записи, или пока записи в запросе не кончатся. </p>
                        <p>Селективные процедуры нужно вызывать так:</p>
                        <p style="font-style: italic">select * from test </p>
                    </div>
                
                <?= topicButton('sql', 'procedural', 'triggers') ?>
                </article>

                <!-- 1.7 Триггеры -->
                <article id="triggers" class="lesson">
                    <h3>1.7. Триггеры</h3>
                    <div class="text-content">
                        <h4>1.7.1. Понятие триггера </h4>
                        <p><strong>Триггеры</strong> — это особые процедуры, которые автоматически выполняются при определенных событиях, таких как вставка, обновление или удаление записей. </p>
                        <p>Синтаксис триггера</p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 1.36 Синтаксис триггера</strong><br>
                                <?= TAB1 ?>CREATE TRIGGER trigname {<br>
                                <?= TAB2 ?>FOR {tablename | viewname}<br>
                                <?= TAB2 ?>[ACTIVE | INACTIVE]<br>
                                <?= TAB2 ?>{BEFORE | AFTER} &lt;mutation_list&gt;<br>
                                <?= TAB2 ?>[POSITION number]<br>
                                <?= TAB1 ?>AS<br>
                                <?= TAB2 ?>[&lt;declarations&gt;]<br>
                                <?= TAB1 ?>BEGIN<br>
                                <?= TAB2 ?>[&lt;PSQL_statements&gt;]<br>
                                <?= TAB1 ?>END
                            </p>
                        </div>
                        <p>ACTIVE/ INACTIVE - Определяет, дает ли действие триггера эффект, когда тот запускается. По умолчанию ACTIVE</p>
                        <p>BEFORE/AFTER - Определяет, когда срабатывает триггер. До или после события.</p>
                        <p>DELETE/INSERT/UPDATE - Определяет операцию над таблицей, при которой срабатывает триггер.</p>
                        <p>POSITION number - Определяет порядок в котором срабатывают триггеры. Триггер с меньшим номером срабатывает раньше.</p>
                        <p>Пример триггера. Логирование изменений в зарплате сотрудников:</p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 1.37 Пример триггера. Логирование изменений в зарплате сотрудников.</strong><br>
                                <?= TAB1 ?>CREATE TRIGGER employee_salary_update<br>
                                <?= TAB1 ?>FOR employees<br>
                                <?= TAB1 ?>ACTIVE BEFORE UPDATE<br>
                                <?= TAB1 ?>AS<br>
                                <?= TAB1 ?>BEGIN<br>
                                <?= TAB2 ?>IF (NEW.salary &lt;&gt; OLD.salary) THEN<br>
                                <?= TAB2 ?>BEGIN<br>
                                <?= TAB1 ?><?= TAB2 ?>INSERT INTO employee_changes_log (employee_id, change_time, old_salary, new_salary)<br>
                                <?= TAB1 ?><?= TAB2 ?>VALUES (OLD.id, CURRENT_TIMESTAMP, OLD.salary, NEW.salary);<br>
                                <?= TAB2 ?>END<br>
                                <?= TAB1 ?>END;
                            </p>
                        </div>

                        <h4>1.7.2 Типы триггеров (INSERT, UPDATE, DELETE) </h4>
                        <p>Триггеры могут быть настроены для реагирования на различные операции изменения данных в таблицах. Разберем основные виды триггеров по типу выполняемой операции: INSERT, UPDATE, DELETE</p>
                        <p style="margin-bottom: 0.4em;"><strong>Триггеры на вставку (INSERT) </strong></p>
                        <p>Триггеры на вставку срабатывают, когда в таблицу добавляется новая запись. </p>
                        <p>Пример триггера на вставку:</p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 1.38 Пример триггера на вставку</strong><br>
                                <?= TAB1 ?>CREATE TRIGGER before_employee_insert<br>
                                <?= TAB1 ?>FOR employee<br>
                                <?= TAB1 ?>ACTIVE BEFORE INSERT<br>
                                <?= TAB1 ?>AS<br>
                                <?= TAB1 ?>BEGIN<br>
                                <?= TAB2 ?>IF (NEW.salary &lt; 0) THEN<br>
                                <?= TAB2 ?>BEGIN<br>
                                <?= TAB1 ?><?= TAB2 ?>EXCEPTION ex_invalid_salary 'Зарплата не может быть отрицательной';<br>
                                <?= TAB2 ?>END<br>
                                <?= TAB1 ?>END;
                            </p>
                        </div>
                        <p style="margin-bottom: 0.4em;"><strong>Триггеры на обновление (UPDATE)</strong></p>
                        <p>Триггеры на обновление срабатывают, когда в таблице изменяется существующая запись. </p>
                        <p>Пример триггера на обновление: </p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 1.39 Пример триггера на обновления</strong><br>
                                <?= TAB1 ?>CREATE TRIGGER after_employee_update<br>
                                <?= TAB1 ?>FOR employee<br>
                                <?= TAB1 ?>ACTIVE AFTER UPDATE<br>
                                <?= TAB1 ?>AS<br>
                                <?= TAB1 ?>BEGIN<br>
                                <?= TAB2 ?>INSERT INTO employee_changes_log (employee_id, change_time, old_salary, new_salary)<br>
                                <?= TAB2 ?>VALUES (OLD.id, CURRENT_TIMESTAMP, OLD.salary, NEW.salary);<br>
                                <?= TAB1 ?>END;
                            </p>
                        </div>
                        <p style="margin-bottom: 0.4em;"><strong>Триггеры на удаление (DELETE)</strong></p>
                        <p>Триггеры на удаление срабатывают, когда запись удаляется из таблицы. </p>
                        <p>Пример триггера на удаление: </p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 1.40 Пример триггера на удаление</strong><br>
                                <?= TAB1 ?>CREATE TRIGGER before_employee_delete<br>
                                <?= TAB1 ?>FOR employees<br>
                                <?= TAB1 ?>ACTIVE BEFORE DELETE<br>
                                <?= TAB1 ?>AS<br>
                                <?= TAB1 ?>BEGIN<br>
                                <?= TAB2 ?>INSERT INTO employee_deletion_log (employee_id, deletion_time)<br>
                                <?= TAB2 ?>VALUES (OLD.id, CURRENT_TIMESTAMP);<br>
                                <?= TAB1 ?>END;
                            </p>
                        </div>
                    </div>
                
                <?= topicButton('sql', 'triggers', 'transactions') ?>
                </article>

                <!-- 1.8 Транзакции -->
                <article id="transactions" class="lesson">
                    <h3>1.8. Транзакции</h3>
                    <div class="text-content">
                        <h4>1.8.1. Основы транзакций в SQL </h4>
                        <p><strong>Транзакции</strong> — механизм, позволяющий группировать несколько операций в одну логическую единицу. Другими словами, транзакция — это логически сгруппированные операции.</p>
                        <p>Транзакции используются для обеспечения ACID-свойств:</p>
                        <ol style="margin-left: 20px;">
                            <li>Atomicity (Атомарность): Транзакция выполняется полностью или не выполняется вовсе. Если хотя бы одна операция в транзакции завершается с ошибкой, вся транзакция откатывается (ROLLBACK)</li>
                            <li>Consistency (Согласованность): Транзакция переводит базу данных из одного согласованного состояния в другое</li>
                            <li>Isolation (Изолированность): Транзакции выполняются изолированно друг от друга</li>
                            <li>Durability (Долговечность): После завершения транзакции (COMMIT) изменения сохраняются в базе данных</li>
                        </ol>
                        <p>Пример триггера на удаление. Попробовать изменить зарплату сотрудника, но откатить изменения в случае ошибки: </p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 1.41 Пример триггера на удаление. Попробовать изменить зарплату сотрудника, но откатить изменения в случае ошибки.</strong><br>
                                <?= TAB1 ?>START TRANSACTION;<br>
                                <?= TAB1 ?>UPDATE EMPLOYEE<br>
                                <?= TAB1 ?>SET SALARY = SALARY * 1.15<br>
                                <?= TAB1 ?>WHERE EMP_NO = 3;<br>
                                <?= TAB1 ?>IF ((SELECT SALARY FROM EMPLOYEE WHERE EMP_NO = 3) > 100000) THEN<br>
                                <?= TAB2 ?>ROLLBACK;<br>
                                <?= TAB1 ?>ELSE<br>
                                <?= TAB2 ?>COMMIT;<br>
                                <?= TAB1 ?>END IF;
                            </p>
                        </div>

                        <h4>1.8.2 Синтаксис и базовые операторы (COMMIT, ROLLBACK, SAVEPOINT) </h4>
                        <p>Синтаксис транзакции: </p>
                        <div class="content-placeholder">
                        <p style="margin-bottom: 0em;">
                            <strong>Листинг 1.42 Синтаксис транзакции</strong><br>
                            <?= TAB1 ?>SET TRANSACTION [NAME tr_name]<br>
                            <?= TAB2 ?>[READ WRITE | READ ONLY]<br>
                            <?= TAB2 ?>[[ISOLATION LEVEL] {<br>
                            <?= TAB1 ?><?= TAB2 ?>SNAPSHOT [TABLE STABILITY]<br>
                            <?= TAB1 ?><?= TAB2 ?>| READ COMMITTED [[NO] RECORD_VERSION] }]<br>
                            <?= TAB2 ?>[NO WAIT | WAIT [LOCK TIMEOUT seconds]]<br>
                            <?= TAB2 ?>[NO AUTO UNDO]<br>
                            <?= TAB2 ?>[IGNORE LIMBO]<br>
                            <?= TAB2 ?>[RESTART REQUESTS]<br>
                            <?= TAB2 ?>[RESERVING &lt;tables&gt; | USING &lt;dbhandles&gt;]<br>
                            <?= TAB1 ?>&lt;tables&gt; ::= &lt;table_spec&gt; [, &lt;table_spec&gt; ...]<br>
                            <?= TAB1 ?>&lt;table_spec&gt; ::= tablename [, tablename ...]<br>
                            <?= TAB2 ?>[FOR [SHARED | PROTECTED] {READ | WRITE}]<br>
                            <?= TAB1 ?>&lt;dbhandles&gt; ::= dbhandle [, dbhandle ...]
                        </p>
                    </div>
                        <p>Разберем некоторые параметры синтаксиса транзакций подробнее: </p>
                        <ul style="margin-left: 20px;">
                            <li>[READ WRITE | READ ONLY] - Определяет режим доступа транзакции. Чтение и запись / только чтение</li>
                            <li>[ISOLATION LEVEL] - Определяет уровень изолированности транзакции. Уровень влияет на видимость изменений, сделанных другими транзакциями</li>
                            <li>[NO WAIT | WAIT] - Определяет поведение транзакции при возникновении блокировок</li>
                            <li>[NO AUTO UNDO] - Оператор ROLLBACK помечает транзакцию как отменённую без удаления созданных в этой транзакции версий</li>
                            <li>[RESERVING <tables>] - Резервирует таблицы для использования в транзакции. Позволяет указать, какие таблицы будут использоваться, и задать режим доступа к ним</li>
                        </ul>
                        <p>Режимы доступа: </p>
                        <ol style="margin-left: 20px;">
                            <li>SHARED: Разрешает доступ другим транзакциям</li>
                            <li>PROTECTED: Запрещает доступ другим транзакциям</li>
                            <li>READ: Разрешает только чтение</li>
                            <li>WRITE: Разрешает чтение и запись</li>
                        </ol>
                        <p>Таблица совместимости блокировок: </p>
                        <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                            <tr style="background-color: #2e4a47; color: white;">
                                <th style="padding: 12px; border: 1px solid #ddd;"></th>
                                <th style="padding: 12px; border: 1px solid #ddd;">Shared read</th>
                                <th style="padding: 12px; border: 1px solid #ddd;">Shared write</th>
                                <th style="padding: 12px; border: 1px solid #ddd;">Protected read</th>
                                <th style="padding: 12px; border: 1px solid #ddd;">Protected write</th>
                            </tr>
                            <tr>
                                <td style="padding: 10px; border: 1px solid #ddd; background-color: #f8f9fa;"><strong>Shared read</strong></td>
                                <td style="padding: 10px; border: 1px solid #ddd;">да</td>
                                <td style="padding: 10px; border: 1px solid #ddd;">да</td>
                                <td style="padding: 10px; border: 1px solid #ddd;">да</td>
                                <td style="padding: 10px; border: 1px solid #ddd;">да</td>
                            </tr>
                            <tr>
                                <td style="padding: 10px; border: 1px solid #ddd; background-color: #f8f9fa;"><strong>Shared write</strong></td>
                                <td style="padding: 10px; border: 1px solid #ddd;">да</td>
                                <td style="padding: 10px; border: 1px solid #ddd;">да</td>
                                <td style="padding: 10px; border: 1px solid #ddd;">нет</td>
                                <td style="padding: 10px; border: 1px solid #ddd;">нет</td>
                            </tr>
                            <tr>
                                <td style="padding: 10px; border: 1px solid #ddd; background-color: #f8f9fa;"><strong>Protected read</strong></td>
                                <td style="padding: 10px; border: 1px solid #ddd;">да</td>
                                <td style="padding: 10px; border: 1px solid #ddd;">нет</td>
                                <td style="padding: 10px; border: 1px solid #ddd;">да</td>
                                <td style="padding: 10px; border: 1px solid #ddd;">нет</td>
                            </tr>
                            <tr>
                                <td style="padding: 10px; border: 1px solid #ddd; background-color: #f8f9fa;"><strong>Protected write</strong></td>
                                <td style="padding: 10px; border: 1px solid #ddd;">да</td>
                                <td style="padding: 10px; border: 1px solid #ddd;">нет</td>
                                <td style="padding: 10px; border: 1px solid #ddd;">нет</td>
                                <td style="padding: 10px; border: 1px solid #ddd;">нет</td>
                            </tr>
                        </table>

                        <p style="margin-bottom: 0.4em;"><strong>Базовые операторы</strong></p>
                        <p><strong>COMMIT</strong> — Фиксирует все изменения, сделанные в рамках текущей транзакции.</p>
                        <p><strong>ROLLBACK</strong> — Отменяет все изменения, сделанные в рамках текущей транзакции. </p>
                        <p><strong>SAVEPOINT</strong> — Создает точку сохранения внутри транзакции. </p>
                        <p>Пример транзакции. Изменение зарплаты: </p>
                        <div class="content-placeholder">
                        <p style="margin-bottom: 0em;">
                            <strong>Листинг 1.43 Пример транзакции. Изменение зарплаты</strong><br>
                            <?= TAB1 ?>SET TRANSACTION NAME update_salary_transaction<br>
                            <?= TAB2 ?>READ WRITE<br>
                            <?= TAB2 ?>ISOLATION LEVEL SNAPSHOT<br>
                            <?= TAB2 ?>WAIT LOCK TIMEOUT 10<br>
                            <?= TAB2 ?>RESERVING <br>
                            <?= TAB1 ?><?= TAB2 ?>EMPLOYEE FOR PROTECTED WRITE,<br>
                            <?= TAB1 ?><?= TAB2 ?>DEPARTMENT FOR PROTECTED WRITE;<br>
                            <br>
                            <?= TAB1 ?>UPDATE EMPLOYEE<br>
                            <?= TAB1 ?>SET SALARY = SALARY + 1000<br>
                            <?= TAB1 ?>WHERE EMP_NO = 10;<br>
                            <br>
                            <?= TAB1 ?>COMMIT;
                        </p>
                    </div>
                    </div>
                
                <?= topicButton('sql', 'transactions', 'indexes') ?>
                </article>

                <!-- 1.9 Индексы -->
                <article id="indexes" class="lesson">
                    <h3>1.9. Индексы</h3>
                    <div class="text-content">
                        <h4>1.9.1. Что такое индексы и как они работают</h4>
                        <p><strong>Индексы</strong> — это объект базы данных, которые создаются для одной или нескольких колонок таблицы. Они позволяют базе данных быстрее находить данные. </p>
                        <p><strong>Типы индексов:</strong></p>
                        <ol style="margin-left: 20px;">
                            <li>Одно-колоночные индексы - создаются для одной колонки.</li>
                            <li>Составные индексы - создаются для нескольких колонок.</li>
                            <li>Уникальные индексы - гарантируют, что все значения в индексируемой колонке уникальны.</li>
                            <li>Полнотекстовые индексы - используются для поиска по текстовым данным.</li>
                        </ol>
                        <h4 style="margin-top: 30px">1.9.2. Как создавать и использовать индексы для оптимизации запросов</h4>
                        <p>Индекс создается с помощью оператора <strong>CREATE_INDEX</strong>.</p>
                        <p>Синтаксис создания индекса: </p>
                        <div class="content-placeholder">
                        <p style="margin-bottom: 0em;">
                            <strong>Листинг 1.44 Синтаксис создания индекса</strong><br>
                            <?= TAB1 ?>CREATE [UNIQUE] [ASC[ENDING] | DESC[ENDING]] <br>
                            <?= TAB1 ?>INDEX indexname ON tablename <br>
                            <?= TAB1 ?>{(col [, col …]) | COMPUTED BY (&lt;expression&gt;)};
                        </p>
                    </div>
                        <p>Разберем синтаксис подробнее:</p>
                        <ul style="margin-left: 20px;">
                            <li>[UNIQUE] — Создает уникальный индекс.</li>
                            <li>[ASC[ENDING] | DESC[ENDING]] — Определяет порядок сортировки значений в индексе.<br>
                                ASC или ASCENDING (по умолчанию) — Значения сортируются по возрастанию.<br>
                                DESC или DESCENDING — Значения сортируются по убыванию.</li>
                            <li>COMPUTED BY (<expression>) - Позволяет создать индекс для вычисляемой колонки. </li>
                        </ul>
                        <p>Команда <strong>ALTER INDEX</strong> используется для изменения свойств существующего индекса. </p>
                        <p>Примеры активации и деактивации индексов: </p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 1.45 Примеры активации и деактивации индексов</strong><br>
                                <?= TAB1 ?>ALTER INDEX idx_last_name ACTIVE;<br>
                                <br>
                                <?= TAB1 ?>ALTER INDEX idx_last_name INACTIVE;
                            </p>
                        </div>
                        <p>Команда <strong>SET STATISTICS</strong> используется для обновления статистики по таблицам и индексам. Статистика помогает оптимизатору запросов выбирать наиболее эффективный план выполнения запроса.</p>
                        <p>Команда <strong>DROP INDEX</strong> используется для удаления индекса из базы данных. </p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 1.46 Примеры удаления индекса</strong><br>
                                <?= TAB1 ?>DROP INDEX idx_last_name;
                            </p>
                        </div>
                        <p style="margin-bottom: 0.4em;"><strong>СЕЛЕКТИВНОСТЬ ИНДЕКСА</strong></p>
                        <p>Это показатель, который отражает, насколько уникальны значения в индексируемой колонке. Чем выше селективность, тем более эффективен индекс для поиска данных. Селективность вычисляется как отношение количества уникальных значений в колонке к общему количеству строк в таблице.</p>
                        <p><strong>Высокая</strong> — если значения в колонке почти уникальны. Например, колонка с email-адресами.</p>
                        <p><strong>Низкая</strong> — если значения в колонке повторяются. Например, колонка с полом (значения "М" и "Ж").</p>
                    </div>
                
                <?= topicButton('sql', 'indexes', 'practice') ?>
                </article>

                <!-- 1.10 Практические задания -->
                <article id="practice" class="lesson">
                    <h3>1.10. Практические задания</h3>
                    <div class="practice-section">
                        <p>Практические задания по курсу SQL Basics</p>
                        <a href="/sql-practice.php" class="btn">Перейти к заданиям</a>
                    </div>
                
                </article>
            </section>
        </div>
    </div>
</div>


<?php require_once __DIR__ . '/templates/footer.php'; ?>