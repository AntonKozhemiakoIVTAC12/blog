@extends('layouts.admin_layout')

@section('content')
    @include('components.head.tinymce-config')

    <div class="container-fluid py-4">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

        <style>
            /* Ваши существующие стили */
            #selectedComponents .component-item {
                display: none;
            }
            .components-panel {
                background: #f8f9fa;
                border-radius: 12px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
                height: calc(100vh - 100px);
                overflow-y: auto;
            }

            .form-panel {
                background: #ffffff;
                border-radius: 12px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                min-height: 80vh;
                padding: 2rem;
            }

            .component-item {
                padding: 1rem;
                margin: 0.5rem 0;
                background: white;
                border: 1px solid #dee2e6;
                border-radius: 8px;
                cursor: grab;
                transition: all 0.2s ease;
            }

            .component-item:hover {
                transform: translateX(5px);
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            }

            .dynamic-field {
                background: #f8f9fa;
                border-radius: 8px;
                padding: 1.5rem;
                margin-bottom: 1.5rem;
                border: 1px solid #dee2e6;
                position: relative;
            }

            .remove-component {
                position: absolute;
                top: 10px;
                right: 10px;
                cursor: pointer;
                color: #dc3545;
                opacity: 0.7;
                transition: opacity 0.2s;
            }

            .remove-component:hover {
                opacity: 1;
            }

            .ghost {
                opacity: 0.5;
                background: #e9ecef;
            }

            .drag-handle {
                cursor: move;
                margin-right: 10px;
                color: #6c757d;
            }
        </style>

        <div class="row g-4">
            <!-- Левая панель с компонентами -->
            <div class="col-lg-3">
                <div class="components-panel p-3">
                    <h5 class="mb-3 fw-bold text-primary">
                        <i class="fas fa-cubes me-2"></i>Компоненты документа
                    </h5>
                    <a href="{{ route('components.create') }}" class="btn btn-info btn-lg">
                        <i class="fas fa-cog me-2"></i>Создать компонент
                    </a>

                    <div class="mb-4">
                        <select id="standardSelector" class="form-select">
                            @foreach($standards as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="componentsList" class="components-list">
                        @foreach($defaultComponents as $component)
                            <div class="component-item" data-key="{{ $component->label }}">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-grip-vertical drag-handle"></i>
                                    <span>{{ $component->label }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <input type="hidden" name="component_order" id="componentOrder">
            <!-- Основная форма -->
            <div class="col-lg-9">
                <div class="form-panel">
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show mb-4">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Ошибки валидации:</strong>
                            </div>
                            <ul class="mt-2 mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.articles.store') }}" method="POST" id="articleForm">
                        @csrf

                        <!-- Заголовок документа -->
                        <div class="mb-4">
                            <label for="title" class="form-label fw-bold">
                                <i class="fas fa-heading me-2"></i>Название документа
                            </label>
                            <input type="text"
                                   class="form-control form-control-lg"
                                   id="title"
                                   name="title"
                                   placeholder="Введите название документа"
                                   required>
                        </div>

                        <!-- Скрытое поле для стандарта -->
                        <input type="hidden" name="gost_data_serialized" id="gostDataSerialized">
                        <input type="hidden" name="standard" id="selectedStandard" value="gost34">

                        <!-- Контейнер для компонентов -->
                        <div id="selectedComponents" class="components-container">
                            <!-- Компоненты будут добавляться здесь -->
                        </div>

                        <!-- Кнопки отправки -->
                        <div class="mt-4 text-end">
                            <a href="{{ route('admin.articles.index') }}" class="btn btn-secondary btn-lg me-3">
                                <i class="fas fa-arrow-left me-2"></i>Назад
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>Создать документ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('articleForm').addEventListener('submit', function (e) {
            const components = selectedComponents.querySelectorAll('.dynamic-field');
            const data = [];

            components.forEach(field => {
                const key = field.dataset.fieldKey;
                const editorId = field.querySelector('textarea').id;
                const content = tinymce.get(editorId).getContent();

                data.push({ key, content });
            });

            // Записываем в скрытое поле
            document.getElementById('gostDataSerialized').value = JSON.stringify(data);

            // Убираем старый gost_data из формы, если он есть
            const gostDataInput = document.querySelector('input[name="gost_data"]');
            if (gostDataInput) {
                gostDataInput.remove();
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('articleForm').addEventListener('submit', function (e) {
                const components = selectedComponents.querySelectorAll('.dynamic-field');
                const data = [];

                components.forEach(field => {
                    const key = field.dataset.fieldKey;
                    const editorId = field.querySelector('textarea').id;
                    const content = tinymce.get(editorId).getContent();

                    data.push({ key, content });
                });

                // Записываем данные в скрытое поле
                document.getElementById('gostDataSerialized').value = JSON.stringify(data);
            });
            const componentsList = document.getElementById('componentsList');
            const selectedComponents = document.getElementById('selectedComponents');

            new Sortable(componentsList, {
                group: {
                    name: 'shared',
                    pull: 'clone',   // разрешаем клонировать
                    put: false       // но запрещаем класть обратно
                },
                sort: false,
                animation: 150,
                ghostClass: 'ghost',
                onEnd: function (evt) {
                    const draggedEl = evt.item;
                    const componentKey = draggedEl.dataset.key;

                    // ✅ Получаем место, куда бросили
                    const newIndex = [...selectedComponents.children].indexOf(evt.item);

                    // ✅ Добавляем dynamic-field по нужному индексу
                    addComponentToForm(componentKey, newIndex);
                }
            });

            // === Перетаскивание внутри формы ===
            new Sortable(selectedComponents, {
                group: 'shared',
                animation: 150,
                handle: '.drag-handle',
                ghostClass: 'ghost',
                chosenClass: 'chosen'
            });

            // === Функция добавления компонента по индексу ===
            function addComponentToForm(key, index = selectedComponents.children.length) {
                const count = Array.from(selectedComponents.querySelectorAll(`[data-field-key="${key}"]`)).length;
                const uniqueId = `editor-${key}-${count}`;
                const field = document.createElement('div');
                field.className = 'dynamic-field';
                field.dataset.fieldKey = key;
                field.innerHTML = `
            <i class="fas fa-times remove-component"></i>
            <label class="form-label fw-bold mb-3">${key}</label>
            <textarea id="${uniqueId}"
                      class="form-control tinymce-editor"
                      name="gost_data[${key}][]"
                      rows="4"
                      required></textarea>
        `;

                // Вставляем по индексу
                if (index >= 0 && index < selectedComponents.children.length) {
                    selectedComponents.insertBefore(field, selectedComponents.children[index]);
                } else {
                    selectedComponents.appendChild(field);
                }

                // Инициализация TinyMCE
                tinymce.init({
                    selector: `#${uniqueId}`,
                    plugins: 'advlist autolink lists link image charmap preview anchor pagebreak code visualblocks visualchars fullscreen autoresize',
                    toolbar: 'undo redo | styleselect | bold italic | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | code',
                    content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }',
                    height: 300,
                    autoresize_bottom_margin: 50,
                    images_upload_url: '/upload-image',
                    automatic_uploads: true,
                    relative_urls: false,
                    convert_urls: true,
                    setup: editor => {
                        editor.on('change', () => editor.save());
                    }
                });

                addRemoveListener(field);
                updateComponentOrder();
            }

            // === Обработчик удаления ===
            function addRemoveListener(element) {
                element.querySelector('.remove-component').addEventListener('click', function () {
                    const textarea = element.querySelector('textarea');
                    const editor = tinymce.get(textarea.id);
                    if (editor) editor.remove();
                    element.remove();
                    updateComponentOrder();
                });
            }

            // === Обновление порядка ===
            function updateComponentOrder() {
                const order = Array.from(selectedComponents.querySelectorAll('.dynamic-field'))
                    .filter(el => el.dataset?.fieldKey)
                    .map(el => el.dataset.fieldKey);

                console.log("Текущий порядок:", order);
            }
        });
    </script>
@endsection
