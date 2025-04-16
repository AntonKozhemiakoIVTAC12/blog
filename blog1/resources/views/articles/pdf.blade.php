<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $article->title }}</title>
    <style>
        body {
            font-family: DejaVu Sans;
            line-height: 1.6;
            color: #000000;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-header {
            font-size: 1.2em;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .section-content {
            margin-left: 15px;
        }
        .footer {
            margin-top: 30px;
            border-top: 1px solid #ccc;
            padding-top: 10px;
            font-size: 0.9em;
            color: #6c757d;
        }
    </style>
</head>
<body>
<h1>{{ $article->title }}</h1>

@if(isset($article->gost_data))
    @foreach($article->gost_data as $fieldKey => $fieldValue)
        <div class="section">
            <div class="section-header">{{ $fieldKey }}</div>
            <div class="section-content">
                {!! nl2br(e($fieldValue)) !!}
            </div>
        </div>
    @endforeach
@else
    <p>Нет данных для отображения.</p>
@endif

<div class="footer">
    <p>Автор: {{ $article->user->name ?? 'Неизвестен' }}</p>
    <p>Создано: {{ $article->created_at->format('d.m.Y H:i') }}</p>
</div>
</body>
</html>
