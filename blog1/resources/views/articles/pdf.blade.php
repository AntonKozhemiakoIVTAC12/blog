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

        body {
            font-family: 'DejaVu Sans', sans-serif !important;
        }

        .section-header {
            font-size: 1.3em;
            font-weight: bold;
            margin-bottom: 12px;
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 5px;
        }

        img {
            max-width: 100%;
            height: auto;
            margin: 10px 0;
        }
    </style>
</head>
<body>

@if(isset($article->gost_data) && is_array($article->gost_data))
    @foreach($article->gost_data as $item)
        @php
            $content = $item['content'] ?? '';

            $content = str_replace(
                ['src="/storage', 'src="storage/'],
                'src="'.public_path('storage').'/',
                $content
            );
        @endphp

        <div class="section">
            <div class="section-content">
                {!! $content !!}
            </div>
        </div>
    @endforeach
@endif

</body>
</html>
