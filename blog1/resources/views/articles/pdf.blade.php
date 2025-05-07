<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $article->title }}</title>
    <style>
        @font-face {
            font-family: 'DejaVu Sans';
            font-style: normal;
            font-weight: normal;
            src: url({{ storage_path('fonts/DejaVuSans.ttf') }}) format('truetype');
        }

        body, div, p, strong, em, span, li {
            font-family: 'DejaVu Sans', Arial, sans-serif !important;
        }

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

@if(isset($article->gost_data) && is_array($article->gost_data))
    @foreach($article->gost_data as $item)
        @php
            $key = $item['key'] ?? 'Без названия';
            $content = $item['content'] ?? '';
        @endphp

        <div class="section">
            <div class="section-header">{{ $key }}</div>
            <div class="section-content">
                {!! htmlspecialchars_decode(html_entity_decode($content, ENT_QUOTES, 'UTF-8'), ENT_QUOTES) !!}
            </div>
        </div>
    @endforeach
@endif

</body>
</html>
