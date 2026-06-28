<?php
// html-css-course.php
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
        <div class="table-of-contents">
            <h2>Содержание курса</h2>
            <nav class="toc-nav">
                <ul>
                    <li><a href="#introduction">Введение</a></li>
                    <li><a href="#basic-html">3. HTML+CSS</a>
                        <ul>
                            <li><a href="#intro-html"><?= htmlspecialchars(getCourseSectionTitle('html', 'intro-html') ?? '3.1. Введение') ?></a></li>
                            <li><a href="#text-work"><?= htmlspecialchars(getCourseSectionTitle('html', 'text-work') ?? '3.2. Работа с текстом') ?></a></li>
                            <li><a href="#photo-work"><?= htmlspecialchars(getCourseSectionTitle('html', 'photo-work') ?? '3.3. Ссылки, изображения и таблицы') ?></a></li>
                            <li><a href="#intro-css"><?= htmlspecialchars(getCourseSectionTitle('html', 'intro-css') ?? '3.4. Введение, основные понятия CSS') ?></a></li>
                            <?php foreach (getCustomSections('html') as $cs): ?>
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
                <?php $__sc = getCourseSection('html', 'introduction'); if ($__sc !== null): echo $__sc; else: ?>
                    <p>Добро пожаловать в курс <strong>по основам HTML и CSS</strong>! Этот курс познакомит вас с основами создания веб-страниц. </p>
                <?php endif; ?>
                </div>
                <?php if (isTeacher()): ?>
                <div style="text-align:right; margin-top:4px">
                    <button class="edit-section-btn" onclick="toggleSectionEdit('html','introduction')">✎ Редактировать</button>
                </div>
                <form method="post" action="/toggle-topic.php" class="section-edit-form" id="sef-introduction" data-key="introduction" style="display:none">
                    <input type="hidden" name="action" value="save_section">
                    <input type="hidden" name="course" value="html">
                    <input type="hidden" name="topic_key" value="introduction">
                    <input type="hidden" name="back" value="/html-css-course.php#introduction">
                    <div id="editor-introduction"></div>
                    <textarea name="content" style="display:none"></textarea>
                    <div style="margin-top:8px; display:flex; gap:8px">
                        <button type="submit" class="btn btn-primary btn-small">Сохранить</button>
                        <button type="button" class="btn btn-small" onclick="toggleSectionEdit('html','introduction')">Отмена</button>
                    </div>
                </form>
                <?php endif; ?>
            </section>

            <!-- ОСНОВЫ HTML и CSS -->
            <section id="basic-html" class="chapter">
                <h2>3. ОСНОВЫ HTML и CSS</h2>

                <!-- 3.1 Введение, основные понятия HTML+CSS  -->
                <article id="intro-html" class="lesson">
                    <?php $__intro_htmlTitle = getCourseSectionTitle('html', 'intro-html') ?? '3.1. Введение, основные понятия HTML+CSS'; ?>
                    <h3><?= htmlspecialchars($__intro_htmlTitle) ?><?php if (isTeacher()): ?> <button class="edit-section-btn" onclick="toggleSectionEdit('html','intro-html')">Редактировать тему</button><?php endif; ?></h3>

                    <div class="text-content" id="sc-intro-html">
                    <?php $__sc = getCourseSection('html', 'intro-html'); if ($__sc !== null): echo $__sc; else: ?>
                        <h4>3.1.1. Что такое HTML?</h4>
                        <p><strong>HTML</strong> (HyperText Markup Language) — это язык разметки, который используется для создания веб-страниц. Он определяет структуру и содержание документа с помощью специальных элементов — тегов.</p>

                        <p>При открытии сайта в браузере, он считывает HTML-код и отображает текст, изображения, ссылки и другие элементы так, как они были размечены. Без HTML веб-страницы были бы просто неформатированным текстом без оформления и интерактивности.</p>

                        <h4 style="margin-top: 30px;">3.1.2. Теги и атрибуты, структура HTML-документа.</h4>
                        <p><strong>Теги</strong> – команды для браузера. Бывают двух видов: Первый вид – парные, бывают открывающие / закрывающие, последний включает в себя косую черту (/). И второй вид - одиночные.</p>
                        <p><strong>Атрибуты</strong> – дополнительные настройки для тегов. Всегда пишутся в открывающем теге.</p>
                        <p>Чтобы понять базовую структура HTML-документа рассмотрим пример самой простой веб-страницы</p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 2.41. Пример HTML-страницы</strong><br>
                                <?= TAB1 ?>&lt;!DOCTYPE html&gt;<br>
                                <?= TAB1 ?>&lt;html&gt;<br>
                                <?= TAB1 ?>&lt;head&gt;<br>
                                <?= TAB2 ?>&lt;meta charset="UTF-8"&gt;<br>
                                <?= TAB2 ?>&lt;title&gt;Пример страницы&lt;/title&gt;<br>
                                <?= TAB1 ?>&lt;/head&gt;<br>
                                <?= TAB1 ?>&lt;body&gt;<br>
                                <?= TAB2 ?>&lt;h1&gt;Привет!&lt;/h1&gt;<br>
                                <?= TAB2 ?>&lt;p&gt;Это моя первая HTML-страница&lt;/p&gt;<br>
                                <?= TAB1 ?>&lt;/body&gt;<br>
                                <?= TAB1 ?>&lt;/html&gt;
                            </p>
                        </div>
                        <p>	Разберем подробнее основные элменты:</p>
                        <ul>
                            <li><strong>&lt;!DOCTYPE html&gt;</strong> - данный тег указывает тип документа, с помощью него браузер понимает, что перед ним документ в стандарте HTML5.</li>
                            <li><strong>&lt;html&gt;</strong> - обязательный тег, внутри него находится все содержимое веб-страницы.</li>
                            <li><strong>&lt;head&gt;</strong> - содержит метаданные, техническую часть страницы. Например: кодировка (&lt;meta charset="UTF-8"&gt;), заголовок (&lt;title&gt;Пример страницы&lt;/title&gt;) и другие.</li>
                            <li><strong>&lt;body&gt;</strong> - тело документа, внутри этого тега размещаются все элементы веб-страницы, которые видны пользователю.</li>
                            <li><strong>&lt;h1&gt;–&lt;h6&gt;</strong> - заголовки от 1 до 6 уровня. &lt;h1&gt; - высший уровень, самый большой заголовок.</li>
                            <li><strong>&lt;p&gt;</strong> - определяет абзац.</li>
                        </ul>
                        <h4>3.1.3. Создание первой веб-страницы.</h4>
                        <p>Создадим первую, простую веб-страницу. Для этого достаточно открыть любой текстовый редактор и создать файл с расширением .html (например, index.html).</p>
                        <p>В файл поместим код простой веб-страницы из примера 1, сохраним и откроем. </p>
                    <?php endif; ?>
                    </div>

                    <?php if (isTeacher()): ?>
                    <form method="post" action="/toggle-topic.php" class="section-edit-form" id="sef-intro-html" data-key="intro-html" style="display:none">
                        <input type="hidden" name="action" value="save_section">
                        <input type="hidden" name="course" value="html">
                        <input type="hidden" name="topic_key" value="intro-html">
                        <input type="hidden" name="back" value="/html-css-course.php#intro-html">
                        <input type="text" name="title" class="section-title-input" value="<?= htmlspecialchars($__intro_htmlTitle) ?>" placeholder="Заголовок темы">
                        <div id="editor-intro-html"></div>
                        <textarea name="content" style="display:none"></textarea>
                        <div style="margin-top:8px; display:flex; gap:8px">
                            <button type="submit" class="btn btn-primary btn-small">Сохранить</button>
                            <button type="button" class="btn btn-small" onclick="toggleSectionEdit('html','intro-html')">Отмена</button>
                        </div>
                    </form>
                    <?php endif; ?>

                <?= topicButton('html', 'intro-html', 'text-work') ?>
                </article>

                <!-- 3.2. Работа с текстом -->
                <article id="text-work" class="lesson">
                    <?php $__text_workTitle = getCourseSectionTitle('html', 'text-work') ?? '3.2. Работа с текстом'; ?>
                    <h3><?= htmlspecialchars($__text_workTitle) ?><?php if (isTeacher()): ?> <button class="edit-section-btn" onclick="toggleSectionEdit('html','text-work')">Редактировать тему</button><?php endif; ?></h3>
                    <div class="text-content" id="sc-text-work">
                    <?php $__sc = getCourseSection('html', 'text-work'); if ($__sc !== null): echo $__sc; else: ?>
                        <h4>3.2.1 Различные теги и их атрибуты </h4>
                            <p>Рассмотрим теги и их атрибуты более подробно.</p>
                            <ol>
                                <li>
                                    <strong>Параграф (&lt;p&gt;).</strong> Атрибут: align - "выравнивание"<br>
                                    Выравнивание по центру: &lt;p align="center"&gt;Текст&lt;/p&gt;<br>
                                    Выравнивание по левому краю: &lt;p align="left"&gt;Текст&lt;/p&gt;<br>
                                    Выравнивание по правому краю: &lt;p align="right"&gt;Текст&lt;/p&gt;<br>
                                    Выравнивание по обоим краям: &lt;p align="justify"&gt;Текст&lt;/p&gt;<br>
                                    По умолчанию текст выравнивается по левому краю, атрибут "left" можно не указывать.<br>
                                    Альтернативой &lt;p align="right"&gt; является тег &lt;center&gt;&lt;/center&gt;
                                </li>
                                <li><strong>Шрифт (&lt;font&gt;).</strong> Атрибуты: size – размер, color – цвет</li>
                                <li>
                                    <strong>Заголовки &lt;h1&gt;–&lt;h6&gt;</strong> - заголовки от 1 до 6 уровня. &lt;h1&gt; - высший уровень, самый большой заголовок<br>
                                    &lt;h1&gt; Заголовок 1 уровня &lt;/h1&gt;<br>
                                    &lt;h2&gt; Заголовок 2 уровня &lt;/h2&gt;<br>
                                    &lt;h3&gt; Заголовок 3 уровня &lt;/h3&gt;<br>
                                    &lt;h4&gt; Заголовок 4 уровня &lt;/h4&gt;<br>
                                    &lt;h5&gt; Заголовок 5 уровня &lt;/h5&gt;<br>
                                    &lt;h6&gt; Заголовок 6 уровня &lt;/h6&gt;
                                </li>                            
                                <li>
                                    <strong>Стилизация текста</strong><br>
                                    Тег &lt;b&gt;&lt;/b&gt; предназначен для выделения текста полужирным начертанием шрифта. Чтобы отобразить текст с курсивным начертанием шрифта, его следует поместить между тегами &lt;i&gt;&lt;/i&gt;.<br>
                                    Тег &lt;u&gt;&lt;/u&gt; нужен для подчеркивания текста, а тег &lt;s&gt;&lt;/s&gt; для зачеркивания.<br>
                                    Элемент &lt;sup&gt;&lt;/sup&gt; используется для выделения символов, которые должны быть отображены как надстрочные, к примеру, ряд математических понятий, таких как возведение в степень (2<sup>2</sup> и т.п.).<br>
                                    Элемент &lt;sub&gt;&lt;/sub&gt; используется для отображения символов как подстрочных. Подстрочные символы, как правило, используются в химических формулах, таких как Н<sub>2</sub>O.
                                </li>
                                <li><strong>Перевод строк в пределах абзаца</strong> - одиночный тег &lt;br&gt;</li>
                                <li><strong>Горизонтальная линия</strong> – одиночный тег &lt;hr&gt;. Атрибуты: size – размер, color – цвет, width – ширина</li>
                                <li><strong>Тег для выделения цитаты</strong> - &lt;blockquote&gt;&lt;/blockquote&gt;</li>  
                                <li><strong>Упорядоченные списки</strong> создаются при помощи элемента &lt;ol&gt;&lt;/ol&gt;. Каждый элемент списка помещается между открывающим тегом &lt;li&gt; и закрывающим тегом &lt;/li&gt;. Списки могут быть вложенными</li>      
                                <li><strong>Неупорядоченные списки</strong> создаются при помощи элемента &lt;ul&gt;&lt;/ul&gt; </li>
                            </ol>

                        <h4>3.2.2 Применение на практике </h4>
                        <p>Создадим простую веб-страницу, используя изученные выше теги:</p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 3.2. Пример создания веб-страницы с изученными тегами</strong><br>
                                <?= TAB1 ?>&lt;!DOCTYPE html&gt;<br>
                                <?= TAB1 ?>&lt;html&gt;<br>
                                <?= TAB1 ?>&lt;head&gt;<br>
                                <?= TAB2 ?>&lt;meta charset="UTF-8"&gt;<br>
                                <?= TAB2 ?>&lt;title&gt;Пример использования HTML-тегов&lt;/title&gt;<br>
                                <?= TAB1 ?>&lt;/head&gt;<br>
                                <?= TAB1 ?>&lt;body&gt;<br>
                                <?= TAB2 ?>&lt;h1 align="center"&gt;Демонстрация HTML-тегов&lt;/h1&gt;<br>
                                <?= TAB2 ?>&lt;hr size="3" color="blue"&gt;<br>
                                <?= TAB2 ?><br>
                                <?= TAB2 ?>&lt;h2&gt;1. Работа с текстом&lt;/h2&gt;<br>
                                <?= TAB2 ?>&lt;p align="justify"&gt;<br>
                                <?= TAB2 ?><?= TAB1 ?>&lt;font size="5" color="darkgreen"&gt;<br>
                                <?= TAB2 ?><?= TAB2 ?>&nbsp;&nbsp;&nbsp;&nbsp;Это основной абзац текста с выравниванием по ширине. Здесь мы демонстрируем <br>
                                <?= TAB2 ?><?= TAB2 ?>&lt;b&gt;полужирное начертание&lt;/b&gt;, &lt;i&gt;курсивное начертание&lt;/i&gt;, <br>
                                <?= TAB2 ?><?= TAB2 ?>&lt;u&gt;подчёркнутый текст&lt;/u&gt; и &lt;s&gt;зачёркнутый текст&lt;/s&gt;.<br>
                                <?= TAB2 ?><?= TAB1 ?>&lt;/font&gt;<br>
                                <?= TAB2 ?>&lt;/p&gt;<br>
                                <?= TAB2 ?><br>
                                <?= TAB2 ?>&lt;p align="right"&gt;<br>
                                <?= TAB2 ?><?= TAB1 ?>&lt;font size="5" color="red"&gt;<br>
                                <?= TAB2 ?><?= TAB2 ?>&nbsp;&nbsp;&nbsp;&nbsp;Этот абзац выровнен по правому краю и содержит формулы:&lt;br&gt;<br>
                                <?= TAB2 ?><?= TAB2 ?>E=mc&lt;sup&gt;2&lt;/sup&gt; (формула энергии)&lt;br&gt;<br>
                                <?= TAB2 ?><?= TAB2 ?>H&lt;sub&gt;2&lt;/sub&gt;SO&lt;sub&gt;4&lt;/sub&gt; (формула серной кислоты)<br>
                                <?= TAB2 ?><?= TAB1 ?>&lt;/font&gt;<br>
                                <?= TAB2 ?>&lt;/p&gt;<br>
                                <?= TAB2 ?><br>
                                <?= TAB2 ?>&lt;h3&gt;2. Цитаты и списки&lt;/h3&gt;<br>
                                <?= TAB2 ?>&lt;blockquote&gt;<br>
                                <?= TAB2 ?><?= TAB1 ?>&lt;font size="3" color="navy"&gt;<br>
                                <?= TAB2 ?><?= TAB2 ?>&nbsp;&nbsp;&nbsp;&nbsp;"Это очень важная цитата, созданная с помощью специального тега. С обеих сторон автоматически создаются отступы."<br>
                                <?= TAB2 ?><?= TAB1 ?>&lt;/font&gt;<br>
                                <?= TAB2 ?>&lt;/blockquote&gt;<br>
                                <?= TAB2 ?><br>
                                <?= TAB2 ?>&lt;h4&gt;Маркированный список:&lt;/h4&gt;<br>
                                <?= TAB2 ?>&lt;ul&gt;<br>
                                <?= TAB2 ?><?= TAB1 ?>&lt;li&gt;Первый элемент списка&lt;/li&gt;<br>
                                <?= TAB2 ?><?= TAB1 ?>&lt;li&gt;Второй элемент списка &lt;/li&gt;<br>
                                <?= TAB2 ?><?= TAB1 ?>&lt;li&gt;Третий элемент спискаx&lt;/li&gt;<br>
                                <?= TAB2 ?>&lt;/ul&gt;<br>
                                <?= TAB2 ?><br>
                                <?= TAB2 ?>&lt;h4&gt;Нумерованный список:&lt;/h4&gt;<br>
                                <?= TAB2 ?>&lt;ol&gt;<br>
                                <?= TAB2 ?><?= TAB1 ?>&lt;li&gt;Элемент один&lt;/li&gt;<br>
                                <?= TAB2 ?><?= TAB1 ?>&lt;li&gt;Элемент два &lt;/li&gt;<br>
                                <?= TAB2 ?><?= TAB1 ?>&lt;li&gt;Элемент три&lt;/li&gt;<br>
                                <?= TAB2 ?>&lt;/ol&gt;<br>
                                <?= TAB1 ?>&lt;/body&gt;<br>
                                <?= TAB1 ?>&lt;/html&gt;
                            </p>
                        </div>
                        <p>Результат выполнения:</p>
                        <img src="/images/html_example.png" 
                            alt="Описание изображения" 
                            style="width: calc(100% - 40px); height: auto; max-width: calc(100% - 40px); margin-left: 40px; margin-bottom: 2em; margin-top: 1em; display: block;">
                    <?php endif; ?>
                    </div>

                    <?php if (isTeacher()): ?>
                    <form method="post" action="/toggle-topic.php" class="section-edit-form" id="sef-text-work" data-key="text-work" style="display:none">
                        <input type="hidden" name="action" value="save_section">
                        <input type="hidden" name="course" value="html">
                        <input type="hidden" name="topic_key" value="text-work">
                        <input type="hidden" name="back" value="/html-css-course.php#text-work">
                        <input type="text" name="title" class="section-title-input" value="<?= htmlspecialchars($__text_workTitle) ?>" placeholder="Заголовок темы">
                        <div id="editor-text-work"></div>
                        <textarea name="content" style="display:none"></textarea>
                        <div style="margin-top:8px; display:flex; gap:8px">
                            <button type="submit" class="btn btn-primary btn-small">Сохранить</button>
                            <button type="button" class="btn btn-small" onclick="toggleSectionEdit('html','text-work')">Отмена</button>
                        </div>
                    </form>
                    <?php endif; ?>

                <?= topicButton('html', 'text-work', 'photo-work') ?>
                </article>

                <!-- 3.3. Ссылки, изображения и таблицы -->
                <article id="photo-work" class="lesson">
                    <?php $__photo_workTitle = getCourseSectionTitle('html', 'photo-work') ?? '3.3. Ссылки, изображения и таблицы'; ?>
                    <h3><?= htmlspecialchars($__photo_workTitle) ?><?php if (isTeacher()): ?> <button class="edit-section-btn" onclick="toggleSectionEdit('html','photo-work')">Редактировать тему</button><?php endif; ?></h3>
                    <div class="text-content" id="sc-photo-work">
                    <?php $__sc = getCourseSection('html', 'photo-work'); if ($__sc !== null): echo $__sc; else: ?>
                        <p><strong>Ссылки</strong></p>
                        <p>Ссылки создаются с помощью тега <a> </a> и атрибута href. Значением атрибута href является адрес страницы, на которую вы собираетесь перейти, когда щелкнете мышью по ссылке. </p>
                        <p>При создании ссылок на другие страницы вашего сайта нет необходимости указывать в URL aдpece его доменное имя. Вместо этого вы можете воспользоваться сокращенным вариантом, называемым также относительным URL-aдpecoм.</p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 3.3. Пример создания ссылок</strong><br>
                                <?= TAB1 ?>&lt;a href="https://example.com"&gt;Пример сайта&lt;/a&gt; <br>
                                <?= TAB1 ?><br>
                                <?= TAB1 ?><strong>Ссылка с подсказкой и открытием в новой вкладке</strong><br>
                                <?= TAB1 ?>&lt;a href="https://google.com" target="_blank" title="Перейти в Google"&gt;Google&lt;/a&gt;
                            </p>
                        </div>
                        <p><strong>Изображения</strong></p>
                        <p>Для вставки изображения на веб-страницу используется одиночный тег &lt;img&gt;. У данного тега есть несколько атрибутов:</p>
                        <ol>
                            <li><strong>Обязательный атрибут src</strong>, который указывает путь к изображению. Путь может быть относительным (images/photo.jpg) или абсолютным (https://example.com/images/photo.jpg).</li>
                            <li><strong>Атрибут alt</strong> - предоставляет текстовое описание изображения, выводимое на экран в случае, если нет возможности показать само изображение.</li>
                            <li><strong>Атрибут title</strong> – при наведении на изображение, на экране появляется всплывающая подсказка.</li>
                            <li><strong>Атрибуты для настройки размеров изображения</strong> – height (высота) и width (ширина).</li>
                        </ol>

                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 3.4. Код для демонстрации работы с изображениями</strong><br>
                                <?= TAB1 ?>&lt;!DOCTYPE html&gt;<br>
                                <?= TAB1 ?>&lt;html&gt;<br>
                                <?= TAB1 ?>&lt;body&gt;<br>
                                <?= TAB2 ?>&lt;h2&gt;Демонстрация работы с изображениями&lt;/h2&gt;<br>
                                <?= TAB2 ?><br>
                                <?= TAB2 ?>&lt;img src="forest.jpg" <br>
                                <?= TAB2 ?><?= TAB1 ?>alt="Красивый лес" <br>
                                <?= TAB2 ?><?= TAB1 ?>title="Фотография сделана в 2025 году"<br>
                                <?= TAB2 ?><?= TAB1 ?>width="600" height="400"&gt;<br>
                                <?= TAB1 ?>&lt;/body&gt;<br>
                                <?= TAB1 ?>&lt;/html&gt;
                            </p>
                        </div>

                        <p>Результат: </p>
                        <img src="/images/example2.png" 
                            style="width: 70%; height: auto; max-width: calc(100% - 40px); margin-left: 40px; margin-bottom: 2em; margin-top: 1em; display: block;">
                        <p><strong>Таблицы</strong></p>
                        <p>Помимо прочих объектов в свой сайт можно добавить таблицы. Тег &lt;&gt; задает начало и конец таблицы, а теги &lt;tr&gt; и &lt;td&gt; - строки и столбцы соответственно. Тег &lt;th&gt; (table header) отвечает за заголовок столбца или строки. По умолчанию текст жирный и выровнен по центру.</p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 3.5. Код для демонстрации работы с таблицами</strong><br>
                                <?= TAB1 ?>&lt;!DOCTYPE html&gt;<br>
                                <?= TAB1 ?>&lt;html&gt;<br>
                                <?= TAB1 ?>&lt;body&gt;<br>
                                <?= TAB2 ?>&lt;table border="1"&gt;<br>
                                <?= TAB2 ?><?= TAB1 ?>&lt;tr&gt;<br>
                                <?= TAB2 ?><?= TAB2 ?>&lt;th&gt;№&lt;/th&gt;<br>
                                <?= TAB2 ?><?= TAB2 ?>&lt;th&gt;Наименование&lt;/th&gt;<br>
                                <?= TAB2 ?><?= TAB2 ?>&lt;th&gt;Цена&lt;/th&gt;<br>
                                <?= TAB2 ?><?= TAB1 ?>&lt;/tr&gt;<br>
                                <?= TAB2 ?><?= TAB1 ?>&lt;tr&gt;<br>
                                <?= TAB2 ?><?= TAB2 ?>&lt;td&gt;1&lt;/td&gt;<br>
                                <?= TAB2 ?><?= TAB2 ?>&lt;td&gt;Ноутбук&lt;/td&gt;<br>
                                <?= TAB2 ?><?= TAB2 ?>&lt;td&gt;45 000 ₽&lt;/td&gt;<br>
                                <?= TAB2 ?><?= TAB1 ?>&lt;/tr&gt;<br>
                                <?= TAB2 ?><?= TAB1 ?>&lt;tr&gt;<br>
                                <?= TAB2 ?><?= TAB2 ?>&lt;td&gt;2&lt;/td&gt;<br>
                                <?= TAB2 ?><?= TAB2 ?>&lt;td&gt;Смартфон&lt;/td&gt;<br>
                                <?= TAB2 ?><?= TAB2 ?>&lt;td&gt;32 000 ₽&lt;/td&gt;<br>
                                <?= TAB2 ?><?= TAB1 ?>&lt;/tr&gt;<br>
                                <?= TAB2 ?><?= TAB1 ?>&lt;tr&gt;<br>
                                <?= TAB2 ?><?= TAB2 ?>&lt;td&gt;3&lt;/td&gt;<br>
                                <?= TAB2 ?><?= TAB2 ?>&lt;td&gt;Планшет&lt;/td&gt;<br>
                                <?= TAB2 ?><?= TAB2 ?>&lt;td&gt;28 000 ₽&lt;/td&gt;<br>
                                <?= TAB2 ?><?= TAB1 ?>&lt;/tr&gt;<br>
                                <?= TAB2 ?>&lt;/table&gt;<br>
                                <?= TAB1 ?>&lt;/body&gt;<br>
                                <?= TAB1 ?>&lt;/html&gt;
                            </p>
                        </div>
                        <p>Результат:</p>
                        <img src="/images/example3.png" style="height: auto; margin-left: 40px; margin-bottom:2em; margin-top:1em">
                    <?php endif; ?>
                    </div>

                    <?php if (isTeacher()): ?>
                    <form method="post" action="/toggle-topic.php" class="section-edit-form" id="sef-photo-work" data-key="photo-work" style="display:none">
                        <input type="hidden" name="action" value="save_section">
                        <input type="hidden" name="course" value="html">
                        <input type="hidden" name="topic_key" value="photo-work">
                        <input type="hidden" name="back" value="/html-css-course.php#photo-work">
                        <input type="text" name="title" class="section-title-input" value="<?= htmlspecialchars($__photo_workTitle) ?>" placeholder="Заголовок темы">
                        <div id="editor-photo-work"></div>
                        <textarea name="content" style="display:none"></textarea>
                        <div style="margin-top:8px; display:flex; gap:8px">
                            <button type="submit" class="btn btn-primary btn-small">Сохранить</button>
                            <button type="button" class="btn btn-small" onclick="toggleSectionEdit('html','photo-work')">Отмена</button>
                        </div>
                    </form>
                    <?php endif; ?>

                <?= topicButton('html', 'photo-work', 'intro-css') ?>
                </article>

                <!-- 3.4.  Введение, основные понятия CSS -->
                <article id="intro-css" class="lesson">
                    <?php $__intro_cssTitle = getCourseSectionTitle('html', 'intro-css') ?? '3.4.  Введение, основные понятия CSS'; ?>
                    <h3><?= htmlspecialchars($__intro_cssTitle) ?><?php if (isTeacher()): ?> <button class="edit-section-btn" onclick="toggleSectionEdit('html','intro-css')">Редактировать тему</button><?php endif; ?></h3>
                    <div class="text-content" id="sc-intro-css">
                    <?php $__sc = getCourseSection('html', 'intro-css'); if ($__sc !== null): echo $__sc; else: ?>
                        <h4>3.4.1. Что такое CSS?</h4>
                        <p><strong>CSS</strong> (Cascading Style Sheets) — это формальный язык описания внешнего вида документа. В то время как HTML отвечает за структуру и содержание веб-страницы, CSS определяет её визуальное представление: расположение элементов, цвета, шрифты и адаптацию под разные устройства. Файл формата .css связывают с основным документом при помощи тега следующим образом: </p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 3.6. Подключение стилей CSS</strong><br>
                                <?= TAB1 ?>&lt;head&gt;<br>
                                <?= TAB2 ?>&lt;link rel="stylesheet" href="main.css"&gt;<br>
                                <?= TAB1 ?>&lt;/head&gt;
                            </p>
                        </div>   
                        
                        <h4>3.4.2 Селекторы, цвет, текст, отступы.</h4>
                        <p><strong>Селекторы</strong></p>
                        <p>Селекторы — это шаблоны, которые определяют, к каким элементам HTML будут применены стили. </p>
                        <div class="content-placeholder">
                            <p style="margin-bottom: 0em;">
                                <strong>Листинг 3.7. Пример простого селектора h1</strong><br>
                                <?= TAB1 ?>h1 {<br>
                                <?= TAB2 ?>color: red;<br>
                                <?= TAB1 ?>}
                            </p>
                        </div>

                        <p>В данном примере селектором является h1. В результате выполнения, все заголовки первого уровня будут красного цвета.</p>
                        <p><strong>Цвета</strong></p>
                        <p>Цвета задаются с помощью свойства color. Существует несколько способов задания цвета:</p>
                        <ol>
                            <li>По имени (red, blue и т.д).</li>
                            <li>HEX-кодом (#FF0000).</li>
                            <li>RGB/RGBA (rgb(255, 0, 0), rgba(255, 0, 0, 0.5))</li>
                            <li>HSL/HSLA (hsl(0, 100%, 50%))</li>
                        </ol>

                        <p>Цвет фона задается с помощью свойства background-color, теми же способами, что и color.</p>
                        <p><strong>Текст</strong></p>
                        <p>Шрифты текста управляются следующими свойствами:</p>
                        <ol>
                            <li><strong>font-family</strong> — гарнитура шрифта (Arial, "Times New Roman"). Чтобы сайт корректно отображался, на компьютерах пользователей должна быть установлена указанная гарнитура шрифта.</li>
                            <li><strong>font-size</strong> — размер текста (16px, 1rem, 120%)</li>
                            <li><strong>font-weight</strong> — насыщенность шрифта (normal, bold, 700)</li>
                            <li><strong>font-style</strong> — начертание шрифта. Способы: italic - курсивное, normal – прямое, oblique – наклонное.</li>
                            <li><strong>line-height</strong> — межстрочный интервал</li>
                        </ol>
                        <p><strong>Отступы</strong></p>
                        <p>Есть два типа отступов: </p>
                        <ol>
                            <li><strong>Внешние отступы (margin).</strong> Создают пространство между элементами, формируя "воздух" в композиции. Не имеют фона и прозрачны.</li>
                            <li><strong>Внутренние отступы (padding).</strong> Определяют пространство внутри элемента между его содержимым и границами. Наследуют фон элемента.</li>
                        </ol>
                        <div class="content-placeholder">
                        <p style="margin-bottom: 0em;">
                            <strong>Листинг 3.8. Пример создания отступов</strong><br>
                            <?= TAB1 ?>div {<br>
                            <?= TAB2 ?>margin: 10px;         /* Внешние отступы: 10px со всех сторон */<br>
                            <?= TAB2 ?>padding: 5px 20px;    /* Внутренние отступы: 5px сверху/снизу, 20px слева/справа */<br>
                            <?= TAB1 ?>}
                        </p>
                    </div>
                    <?php endif; ?>
                    </div>

                    <?php if (isTeacher()): ?>
                    <form method="post" action="/toggle-topic.php" class="section-edit-form" id="sef-intro-css" data-key="intro-css" style="display:none">
                        <input type="hidden" name="action" value="save_section">
                        <input type="hidden" name="course" value="html">
                        <input type="hidden" name="topic_key" value="intro-css">
                        <input type="hidden" name="back" value="/html-css-course.php#intro-css">
                        <input type="text" name="title" class="section-title-input" value="<?= htmlspecialchars($__intro_cssTitle) ?>" placeholder="Заголовок темы">
                        <div id="editor-intro-css"></div>
                        <textarea name="content" style="display:none"></textarea>
                        <div style="margin-top:8px; display:flex; gap:8px">
                            <button type="submit" class="btn btn-primary btn-small">Сохранить</button>
                            <button type="button" class="btn btn-small" onclick="toggleSectionEdit('html','intro-css')">Отмена</button>
                        </div>
                    </form>
                    <?php endif; ?>

                <?= topicButton('html', 'intro-css', null) ?>
                </article>

                <!-- Динамические темы из БД (после всех статических) -->
                <?php
                $customSections = getCustomSections('html');
                foreach ($customSections as $cs):
                    $csKey = htmlspecialchars($cs['section_key']);
                    $csTitle = htmlspecialchars($cs['title']);
                ?>
                <article id="<?= $csKey ?>" class="lesson">
                    <h3>
                        <?= $csTitle ?>
                        <?php if (isTeacher()): ?>
                        <button class="edit-section-btn" onclick="toggleSectionEdit('html','<?= $csKey ?>')">Редактировать тему</button>
                        <form method="post" action="/toggle-topic.php" style="display:inline" onsubmit="return confirm('Удалить тему «<?= $csTitle ?>»?')">
                            <input type="hidden" name="action" value="delete_section">
                            <input type="hidden" name="course" value="html">
                            <input type="hidden" name="topic_key" value="<?= $csKey ?>">
                            <input type="hidden" name="back" value="/html-css-course.php">
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
                        <input type="hidden" name="course" value="html">
                        <input type="hidden" name="topic_key" value="<?= $csKey ?>">
                        <input type="hidden" name="back" value="/html-css-course.php#<?= $csKey ?>">
                        <input type="text" name="title" class="section-title-input" value="<?= $csTitle ?>" placeholder="Заголовок темы">
                        <div id="editor-<?= $csKey ?>"></div>
                        <textarea name="content" style="display:none"></textarea>
                        <div style="margin-top:8px; display:flex; gap:8px">
                            <button type="submit" class="btn btn-primary btn-small">Сохранить</button>
                            <button type="button" class="btn btn-small" onclick="toggleSectionEdit('html','<?= $csKey ?>')">Отмена</button>
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
                    <input type="hidden" name="course" value="html">
                    <input type="hidden" name="back" value="/html-css-course.php">
                    <label style="display:block; font-weight:600; margin-bottom:4px">Название темы:</label>
                    <input type="text" name="title" placeholder="Например: 3.5. Адаптивный дизайн" required>
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