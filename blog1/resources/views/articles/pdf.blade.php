<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $article->title }}</title>
    <style>
        /* 1. Явно подключаем шрифт для кириллицы */
        @font-face {
            font-family: 'DejaVu Sans';
            font-style: normal;
            font-weight: normal;
            src: url({{ storage_path('fonts/DejaVuSans.ttf') }}) format('truetype');
        }

        /* 2. Принудительно применяем шрифт ко всем элементам */
        body, div, p, strong, em, span, li {
            font-family: 'DejaVu Sans', Arial, sans-serif !important;
        }

        /* Остальные стили */
        body {
            line-height: 1.6;
            color: #000000;
            padding: 20px;
        }
        .section-header {
            font-size: 1.3em;
            font-weight: bold;
            margin-bottom: 12px;
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 5px;
        }
        .section-content strong {
            font-weight: 600;
            color: #2c3e50;
        }
    </style>
</head>
<body>
<h1>{{ $article->title }}</h1>

@if(isset($article->gost_data))
    @foreach($article->gost_data as $fieldKey => $fieldValue)
            <?php
            $fieldValue = htmlspecialchars_decode(
                html_entity_decode($fieldValue, ENT_QUOTES, 'UTF-8'),
                ENT_QUOTES
            );
            ?>
        <div class="section">
            <div class="section-header">{{ $fieldKey }}</div>
            <div class="section-content">
                {!! $fieldValue !!}
            </div>
        </div>
    @endforeach
@endif
</body>
</html>
