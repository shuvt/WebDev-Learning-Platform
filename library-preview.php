<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

if (!isLoggedIn()) {
    header('Location: /login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Предпросмотр — Приложение «Библиотека»</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: rgb(248, 248, 248);
            color: rgb(67, 35, 35);
        }

        /* Шапка страницы предпросмотра */
        .preview-header {
            background: linear-gradient(135deg, rgb(47, 87, 85), rgb(90, 150, 144));
            color: white;
            padding: 14px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .preview-header-title {
            font-size: 1rem;
            font-weight: 500;
        }

        .preview-header-back {
            color: white;
            text-decoration: none;
            font-size: 0.9rem;
            padding: 6px 16px;
            border: 1px solid rgba(255,255,255,0.5);
            border-radius: 6px;
            transition: background 0.2s;
        }

        .preview-header-back:hover { background: rgba(255,255,255,0.15); }

        /* Обёртка приложения */
        .app-wrapper {
            max-width: 1280px;
            margin: 28px auto;
            padding: 0 20px 40px;
        }

        .app-label {
            font-size: 0.82rem;
            color: rgb(120, 120, 120);
            margin-bottom: 10px;
            text-align: center;
        }

        /* Само "приложение" */
        .app {
            display: flex;
            height: 620px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 40px rgba(30,45,74,0.18);
            border: 1px solid #dde2ee;
            background: #f5f7fa;
        }

        /* Сайдбар */
        .sidebar {
            width: 210px;
            min-width: 210px;
            background: #1e2d4a;
            display: flex;
            flex-direction: column;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 20px 16px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .logo-icon { font-size: 28px; }

        .logo-icon svg { width: 18px; height: 18px; fill: #fff; }

        .logo-name {
            font-family: 'Playfair Display', serif;
            font-size: 1rem;
            color: #fff;
            font-weight: 600;
            line-height: 1.2;
        }

        .logo-sub {
            font-size: 0.62rem;
            color: rgba(255,255,255,0.4);
            text-transform: uppercase;
            letter-spacing: 0.07em;
        }

        .sidebar-nav {
            flex: 1;
            padding: 12px 8px;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 11px;
            border-radius: 7px;
            color: rgba(255,255,255,0.6);
            font-size: 0.88rem;
            cursor: pointer;
            transition: background 0.15s, color 0.15s;
            user-select: none;
        }

        .nav-item:hover { background: rgba(255,255,255,0.08); color: #fff; }
        .nav-item.active { background: #3b6fc9; color: #fff; font-weight: 500; }

        .nav-icon {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
            opacity: 0.75;
        }

        .nav-item.active .nav-icon { opacity: 1; }

        .sidebar-date {
            padding: 12px 16px;
            font-size: 0.72rem;
            color: rgba(255,255,255,0.25);
            border-top: 1px solid rgba(255,255,255,0.07);
        }

        /* Основная область */
        .main-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .topbar {
            background: #fff;
            border-bottom: 1px solid #dde2ee;
            padding: 14px 24px;
            font-size: 1.05rem;
            font-weight: 600;
            color: #1a2236;
            flex-shrink: 0;
        }

        .content {
            flex: 1;
            padding: 20px 24px;
            overflow-y: auto;
            font-family: 'Inter', sans-serif;
        }

        /* Стили содержимого (те же что в библиотеке) */
        :root {
            --white: #fff;
            --bg: #f5f7fa;
            --bg2: #eef1f6;
            --primary: #3b6fc9;
            --green: #2da05a;
            --red: #d64040;
            --orange: #c87a20;
            --blue: #2a7fc9;
            --text: #1a2236;
            --text-muted: #6b7896;
            --border: #dde2ee;
            --radius: 10px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
            gap: 12px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 14px;
        }

        .stat-value { font-size: 1.7rem; font-weight: 700; color: var(--primary); }
        .stat-label { font-size: 0.7rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: .05em; }
        .stat-card.danger .stat-value { color: var(--red); }

        .dash-row { display: grid; grid-template-columns: 1fr 220px; gap: 14px; }

        .widget {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
        }

        .widget-head {
            padding: 10px 14px;
            font-weight: 600;
            font-size: 0.82rem;
            border-bottom: 1px solid var(--border);
            background: #fafbfd;
            color: var(--text);
        }

        .widget-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 14px;
            border-bottom: 1px solid var(--bg2);
            font-size: 0.82rem;
        }

        .widget-row:last-child { border-bottom: none; }
        .widget-row-title { font-weight: 500; color: var(--text); }
        .widget-row-sub { font-size: 0.72rem; color: var(--text-muted); }
        .right-col { display: flex; flex-direction: column; gap: 12px; }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .b-green  { background: #e4f7ec; color: var(--green); }
        .b-blue   { background: #e4f0fb; color: var(--blue); }
        .b-red    { background: #fdeaea; color: var(--red); }
        .b-orange { background: #fef3e4; color: var(--orange); }

        .table-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
        }

        .table-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 14px;
            border-bottom: 1px solid var(--border);
            background: #fafbfd;
        }

        .table-total { font-size: 0.78rem; color: var(--text-muted); font-weight: 500; }

        .search-input {
            padding: 5px 10px;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 0.78rem;
            background: #fff;
            width: 180px;
        }

        .btn-add {
            padding: 5px 13px;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 0.78rem;
            cursor: pointer;
            font-weight: 500;
        }

        table { width: 100%; border-collapse: collapse; }

        th {
            background: var(--bg);
            padding: 8px 12px;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: var(--text-muted);
            font-weight: 600;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        td {
            padding: 9px 12px;
            font-size: 0.82rem;
            border-bottom: 1px solid var(--bg2);
            color: var(--text);
        }

        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #fafbfd; }
        tr.overdue td { background: #fff8f8; }
        .td-m { color: var(--text-muted); font-size: 0.75rem; }

        .btn-sm {
            padding: 3px 9px;
            border: 1px solid var(--border);
            border-radius: 5px;
            font-size: 0.72rem;
            cursor: pointer;
            background: #fff;
            color: var(--text-muted);
            margin-right: 3px;
        }

        .btn-danger-sm { background: #fdeaea; color: var(--red); border-color: #f0b0b0; }

        .empty { text-align: center; padding: 30px; color: var(--text-muted); font-size: 0.85rem; }

        .form-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 20px;
            max-width: 560px;
        }

        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px; }
        .form-group { display: flex; flex-direction: column; gap: 4px; }
        .form-group.full { grid-column: 1 / -1; }
        .form-group label { font-size: 0.72rem; font-weight: 500; color: var(--text-muted); }
        .form-group input, .form-group select {
            padding: 7px 10px;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 0.82rem;
            background: #fff;
        }

        .form-actions {
            display: flex;
            gap: 8px;
            padding-top: 12px;
            border-top: 1px solid var(--border);
        }

        .btn-primary { padding: 7px 16px; background: var(--primary); color: #fff; border: none; border-radius: 6px; font-size: 0.82rem; cursor: pointer; }
        .btn-outline  { padding: 7px 16px; border: 1px solid var(--border); border-radius: 6px; font-size: 0.82rem; cursor: pointer; background: #fff; color: var(--text); }
    </style>
</head>
<body>

<div class="preview-header">
    <div class="preview-header-title">Предпросмотр приложения «Библиотека»</div>
    <a href="/web-course.php" class="preview-header-back">← Вернуться к курсу</a>
</div>

<div class="app-wrapper">
    <p class="app-label">Интерактивный предпросмотр — нажимайте на пункты меню слева</p>

    <div class="app">

        <!-- Сайдбар -->
        <div class="sidebar">
            <div class="logo">
                <span class="logo-icon" style="filter: brightness(0) invert(1);">🕮</span>
                <div>
                    <div class="logo-name">Библиотека</div>
                    <div class="logo-sub">Система управления</div>
                </div>
            </div>
            <nav class="sidebar-nav" id="nav">
                <div class="nav-item active" onclick="show('home')" data-page="home">
                    Главная
                </div>
                <div class="nav-item" onclick="show('books')" data-page="books">
                    Книги
                </div>
                <div class="nav-item" onclick="show('authors')" data-page="authors">
                    Авторы
                </div>
                <div class="nav-item" onclick="show('publishers')" data-page="publishers">
                    Издательства
                </div>
                <div class="nav-item" onclick="show('users')" data-page="users">
                    Читатели
                </div>
                <div class="nav-item" onclick="show('copies')" data-page="copies">
                    Экземпляры
                </div>
                <div class="nav-item" onclick="show('borrows')" data-page="borrows">
                    Выдача
                </div>
            </nav>
        </div>

        <!-- Контент -->
        <div class="main-area">
            <div class="topbar" id="topbar">Главная</div>
            <div class="content" id="content"></div>
        </div>

    </div>
</div>

<script>
const today = '<?= date('d.m.Y') ?>';

const pages = {

  home: {
    title: 'Главная',
    html: `
<div class="stats-grid">
  <div class="stat-card"><div class="stat-value">10</div><div class="stat-label">Книг</div></div>
  <div class="stat-card"><div class="stat-value">5</div><div class="stat-label">Авторов</div></div>
  <div class="stat-card"><div class="stat-value">7</div><div class="stat-label">Читателей</div></div>
  <div class="stat-card"><div class="stat-value">21</div><div class="stat-label">Экземпляров</div></div>
</div>
<div class="dash-row">
  <div class="widget">
    <div class="widget-head">Последние выдачи</div>
    <div class="widget-row"><div><div class="widget-row-title">Мастер и Маргарита</div><div class="widget-row-sub">Петров И. · 10.03.2026</div></div><span class="badge b-blue">Активна</span></div>
    <div class="widget-row"><div><div class="widget-row-title">Преступление и наказание</div><div class="widget-row-sub">Иванова М. · 05.03.2026</div></div><span class="badge b-green">Возвращена</span></div>
    <div class="widget-row"><div><div class="widget-row-title">Война и мир</div><div class="widget-row-sub">Сидоров А. · 01.03.2026</div></div><span class="badge b-red">Просрочена</span></div>
    <div class="widget-row"><div><div class="widget-row-title">Идиот</div><div class="widget-row-sub">Козлова Е. · 28.02.2026</div></div><span class="badge b-green">Возвращена</span></div>
  </div>
  <div class="right-col">
    <div class="widget">
      <div class="widget-head" style="color:#d64040">Просроченные</div>
      <div class="widget-row"><div><div class="widget-row-title" style="font-size:0.78rem">Война и мир</div><div class="widget-row-sub">Сидоров А.</div></div><div style="font-size:0.72rem;color:#d64040">01.03</div></div>
    </div>
    <div class="widget">
      <div class="widget-head">Популярные</div>
      <div class="widget-row"><div><div class="widget-row-title" style="font-size:0.78rem">Мастер и Маргарита</div><div class="widget-row-sub">Булгаков</div></div><div style="font-size:0.72rem;color:#6b7896">12 раз</div></div>
      <div class="widget-row"><div><div class="widget-row-title" style="font-size:0.78rem">Идиот</div><div class="widget-row-sub">Достоевский</div></div><div style="font-size:0.72rem;color:#6b7896">8 раз</div></div>
    </div>
  </div>
</div>`
  },

  books: {
    title: 'Книги',
    html: `
<div class="table-card">
  <div class="table-toolbar">
    <div style="display:flex;gap:6px">
      <input class="search-input" placeholder="Поиск по названию / автору…">
      <button class="btn-add">+ Добавить</button>
    </div>
  </div>
  <table>
    <thead><tr><th>#</th><th>Название</th><th>Автор</th><th>Жанр</th><th>Год</th><th>Экз./Дост.</th><th>Действия</th></tr></thead>
    <tbody>
      <tr><td class="td-m">1</td><td><strong>Война и мир</strong></td><td>Толстой Л.</td><td>Роман-эпопея</td><td>2020</td><td>2 / <strong>1</strong></td><td><button class="btn-sm">Ред.</button><button class="btn-sm">Экз.</button><button class="btn-sm btn-danger-sm">Удалить</button></td></tr>
      <tr><td class="td-m">2</td><td><strong>Преступление и наказание</strong></td><td>Достоевский Ф.</td><td>Роман</td><td>2019</td><td>2 / <strong>2</strong></td><td><button class="btn-sm">Ред.</button><button class="btn-sm">Экз.</button><button class="btn-sm btn-danger-sm">Удалить</button></td></tr>
      <tr><td class="td-m">3</td><td><strong>Мастер и Маргарита</strong></td><td>Булгаков М.</td><td>Роман</td><td>2018</td><td>2 / <strong>1</strong></td><td><button class="btn-sm">Ред.</button><button class="btn-sm">Экз.</button><button class="btn-sm btn-danger-sm">Удалить</button></td></tr>
      <tr><td class="td-m">4</td><td><strong>Анна Каренина</strong></td><td>Толстой Л.</td><td>Роман</td><td>2022</td><td>3 / <strong>3</strong></td><td><button class="btn-sm">Ред.</button><button class="btn-sm">Экз.</button><button class="btn-sm btn-danger-sm">Удалить</button></td></tr>
      <tr><td class="td-m">5</td><td><strong>Евгений Онегин</strong></td><td>Пушкин А.</td><td>Роман в стихах</td><td>2019</td><td>2 / <strong>2</strong></td><td><button class="btn-sm">Ред.</button><button class="btn-sm">Экз.</button><button class="btn-sm btn-danger-sm">Удалить</button></td></tr>
    </tbody>
  </table>
</div>`
  },

  authors: {
    title: 'Авторы',
    html: `
<div class="table-card">
  <div class="table-toolbar">
    <div style="display:flex;gap:6px">
      <input class="search-input" placeholder="Поиск…">
      <button class="btn-add">+ Добавить</button>
    </div>
  </div>
  <table>
    <thead><tr><th>#</th><th>Фамилия</th><th>Имя</th><th>Дата рождения</th><th>Страна</th><th>Действия</th></tr></thead>
    <tbody>
      <tr><td class="td-m">1</td><td><strong>Толстой</strong></td><td>Лев</td><td>09.09.1828</td><td>Россия</td><td><button class="btn-sm">Ред.</button><button class="btn-sm btn-danger-sm">Удалить</button></td></tr>
      <tr><td class="td-m">2</td><td><strong>Достоевский</strong></td><td>Фёдор</td><td>11.11.1821</td><td>Россия</td><td><button class="btn-sm">Ред.</button><button class="btn-sm btn-danger-sm">Удалить</button></td></tr>
      <tr><td class="td-m">3</td><td><strong>Булгаков</strong></td><td>Михаил</td><td>15.05.1891</td><td>Россия</td><td><button class="btn-sm">Ред.</button><button class="btn-sm btn-danger-sm">Удалить</button></td></tr>
      <tr><td class="td-m">4</td><td><strong>Пушкин</strong></td><td>Александр</td><td>06.06.1799</td><td>Россия</td><td><button class="btn-sm">Ред.</button><button class="btn-sm btn-danger-sm">Удалить</button></td></tr>
      <tr><td class="td-m">5</td><td><strong>Гоголь</strong></td><td>Николай</td><td>20.03.1809</td><td>Россия</td><td><button class="btn-sm">Ред.</button><button class="btn-sm btn-danger-sm">Удалить</button></td></tr>
    </tbody>
  </table>
</div>`
  },

  publishers: {
    title: 'Издательства',
    html: `
<div class="table-card">
  <div class="table-toolbar">
    <div style="display:flex;gap:6px">
      <input class="search-input" placeholder="Поиск…">
      <button class="btn-add">+ Добавить</button>
    </div>
  </div>
  <table>
    <thead><tr><th>#</th><th>Название</th><th>Адрес</th><th>Книг</th><th>Действия</th></tr></thead>
    <tbody>
      <tr><td class="td-m">1</td><td><strong>Эксмо</strong></td><td>Москва, ул. Зорге, 1</td><td><strong>4</strong></td><td><button class="btn-sm">Ред.</button><button class="btn-sm btn-danger-sm">Удалить</button></td></tr>
      <tr><td class="td-m">2</td><td><strong>АСТ</strong></td><td>Москва, Пресненская наб., 6</td><td><strong>3</strong></td><td><button class="btn-sm">Ред.</button><button class="btn-sm btn-danger-sm">Удалить</button></td></tr>
      <tr><td class="td-m">3</td><td><strong>Азбука</strong></td><td>Санкт-Петербург, наб. р. Фонтанки, 78</td><td><strong>3</strong></td><td><button class="btn-sm">Ред.</button><button class="btn-sm btn-danger-sm">Удалить</button></td></tr>
    </tbody>
  </table>
</div>`
  },

  users: {
    title: 'Читатели',
    html: `
<div class="table-card">
  <div class="table-toolbar">
    <div style="display:flex;gap:6px">
      <input class="search-input" placeholder="Поиск…">
      <button class="btn-add">+ Добавить</button>
    </div>
  </div>
  <table>
    <thead><tr><th>#</th><th>Фамилия</th><th>Имя</th><th>Дата рождения</th><th>Регистрация</th><th>Выдач</th><th>Действия</th></tr></thead>
    <tbody>
      <tr><td class="td-m">1</td><td><span style="cursor:pointer">Петров</span></td><td>Иван</td><td>15.05.1990</td><td>10.01.2023</td><td><strong>3</strong></td><td><button class="btn-sm">Карточка</button><button class="btn-sm">Ред.</button><button class="btn-sm btn-danger-sm">Удалить</button></td></tr>
      <tr><td class="td-m">2</td><td><span style="cursor:pointer">Иванова</span></td><td>Мария</td><td>22.08.1985</td><td>15.02.2023</td><td><strong>2</strong></td><td><button class="btn-sm">Карточка</button><button class="btn-sm">Ред.</button><button class="btn-sm btn-danger-sm">Удалить</button></td></tr>
      <tr><td class="td-m">3</td><td><span style="cursor:pointer">Сидоров</span></td><td>Алексей</td><td>03.12.1995</td><td>20.03.2023</td><td><strong>4</strong></td><td><button class="btn-sm">Карточка</button><button class="btn-sm">Ред.</button><button class="btn-sm btn-danger-sm">Удалить</button></td></tr>
    </tbody>
  </table>
</div>`
  },

  copies: {
    title: 'Экземпляры',
    html: `
<div class="table-card">
  <div class="table-toolbar">
    <button class="btn-add">+ Добавить</button>
  </div>
  <table>
    <thead><tr><th>#</th><th>Книга</th><th>Статус</th><th>Действия</th></tr></thead>
    <tbody>
      <tr><td class="td-m">1</td><td>Война и мир</td><td><span class="badge b-blue">Выдана</span></td><td><button class="btn-sm">Ред.</button><button class="btn-sm btn-danger-sm">Удалить</button></td></tr>
      <tr><td class="td-m">2</td><td>Война и мир</td><td><span class="badge b-green">Доступна</span></td><td><button class="btn-sm" style="background:#e4f0fb;color:#2a7fc9;border-color:#b5d4f4">Выдать</button><button class="btn-sm">Ред.</button><button class="btn-sm btn-danger-sm">Удалить</button></td></tr>
      <tr><td class="td-m">3</td><td>Мастер и Маргарита</td><td><span class="badge b-blue">Выдана</span></td><td><button class="btn-sm">Ред.</button><button class="btn-sm btn-danger-sm">Удалить</button></td></tr>
      <tr><td class="td-m">4</td><td>Мастер и Маргарита</td><td><span class="badge b-green">Доступна</span></td><td><button class="btn-sm" style="background:#e4f0fb;color:#2a7fc9;border-color:#b5d4f4">Выдать</button><button class="btn-sm">Ред.</button><button class="btn-sm btn-danger-sm">Удалить</button></td></tr>
      <tr><td class="td-m">5</td><td>Анна Каренина</td><td><span class="badge b-green">Доступна</span></td><td><button class="btn-sm">Ред.</button><button class="btn-sm btn-danger-sm">Удалить</button></td></tr>
    </tbody>
  </table>
</div>`
  },

  borrows: {
    title: 'Записи о выдаче',
    html: `
<div class="table-card">
  <div class="table-toolbar">
    <div style="display:flex;gap:6px;flex-wrap:wrap">
      <input class="search-input" placeholder="Книга / читатель…">
      <select style="padding:5px 8px;border:1px solid #dde2ee;border-radius:6px;font-size:0.78rem;background:#fff">
        <option>Все</option><option>Активные</option><option>Просроченные</option><option>Возвращённые</option>
      </select>
      <button class="btn-add">Выдать книгу</button>
    </div>
  </div>
  <table>
    <thead><tr><th>#</th><th>Книга</th><th>Читатель</th><th>Выдана</th><th>Срок</th><th>Статус</th><th>Действия</th></tr></thead>
    <tbody>
      <tr><td class="td-m">1</td><td>Мастер и Маргарита</td><td><span style="color:#3b6fc9;cursor:pointer">Петров И.</span></td><td>10.03.2026</td><td>24.03.2026</td><td><span class="badge b-blue">Активна</span></td><td><button class="btn-sm" style="background:#3b6fc9;color:#fff;border-color:#3b6fc9">Вернуть</button></td></tr>
      <tr class="overdue"><td class="td-m">2</td><td>Война и мир</td><td><span style="color:#3b6fc9;cursor:pointer">Сидоров А.</span></td><td>15.02.2026</td><td>01.03.2026</td><td><span class="badge b-red">Просрочена</span></td><td><button class="btn-sm" style="background:#3b6fc9;color:#fff;border-color:#3b6fc9">Вернуть</button></td></tr>
      <tr><td class="td-m">3</td><td>Преступление и наказание</td><td><span style="color:#3b6fc9;cursor:pointer">Иванова М.</span></td><td>01.03.2026</td><td>15.03.2026</td><td><span class="badge b-green">Возвращена</span></td><td><span class="td-m">Завершено</span></td></tr>
    </tbody>
  </table>
</div>`
  }
};

function show(key) {
  const p = pages[key];
  document.getElementById('topbar').textContent = p.title;
  document.getElementById('content').innerHTML = p.html;
  document.querySelectorAll('.nav-item').forEach(el => {
    el.classList.toggle('active', el.dataset.page === key);
  });
}

show('home');
</script>

</body>
</html>
