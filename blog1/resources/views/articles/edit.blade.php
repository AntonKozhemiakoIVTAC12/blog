@extends('layouts.app')
@section('content')
    @include('components.head.tinymce-config')
    <div class="container-fluid py-4">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
        <style>
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
                                <option value="{{ $key }}" {{ $article->standard === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
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

            <!-- Основная форма редактирования -->
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

                    <form action="{{ route('articles.update', $article) }}" method="POST" id="editForm">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="title" class="form-label fw-bold">
                                <i class="fas fa-heading me-2"></i>Название документа
                            </label>
                            <input type="text"
                                   class="form-control form-control-lg"
                                   id="title"
                                   name="title"
                                   value="{{ old('title', $article->title) }}"
                                   placeholder="Введите название документа"
                                   required>
                        </div>

                        <input type="hidden" name="standard" id="selectedStandard" value="{{ old('standard', $article->standard) }}">

                        <!-- Скрытое поле для сериализованных данных -->
                        <input type="hidden" name="gost_data_serialized" id="gostDataSerialized">

                        <!-- Контейнер для выбранных компонентов -->
                        <div id="selectedComponents" class="components-container">
                            <!-- Компоненты будут динамически добавлены здесь -->
                        </div>

                        <!-- Кнопка отправки -->
                        <div class="mt-4 text-end">
                            <a href="{{ route('articles.index') }}" class="btn btn-secondary btn-lg me-3">
                                <i class="fas fa-arrow-left me-2"></i>Назад
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>Обновить документ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const componentsList = document.getElementById('componentsList');
            const selectedComponents = document.getElementById('selectedComponents');
            const editForm = document.getElementById('editForm');

            // === Drag & Drop с левой панели ===
            new Sortable(componentsList, {
                group: {
                    name: 'shared',
                    pull: 'clone',
                    put: false
                },
                sort: false,
                animation: 150,
                ghostClass: 'ghost',
                onEnd: function (evt) {
                    const componentKey = evt.item.dataset.key;
                    const newIndex = [...selectedComponents.children].indexOf(evt.item);
                    addComponentToForm(componentKey, newIndex);
                }
            });

            // === Сортировка внутри формы ===
            new Sortable(selectedComponents, {
                group: 'shared',
                animation: 150,
                handle: '.drag-handle',
                ghostClass: 'ghost',
                chosenClass: 'chosen',
                onSort: updateComponentOrder
            });

            // === Функция добавления компонента по индексу ===
            function addComponentToForm(key, index = selectedComponents.children.length, content = '') {
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
                              rows="4"
                              required>${content}</textarea>
                `;
                if (index >= 0 && index < selectedComponents.children.length) {
                    selectedComponents.insertBefore(field, selectedComponents.children[index]);
                } else {
                    selectedComponents.appendChild(field);
                }

                initTinyMCE(uniqueId);
                addRemoveListener(field);
                updateComponentOrder();
            }

            // === Инициализация TinyMCE ===
            function initTinyMCE(id) {
                tinymce.init({
                    selector: `#${id}`,
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

            // === Обновление порядка компонентов перед отправкой ===
            function updateComponentOrder() {
                const data = [];
                const components = selectedComponents.querySelectorAll('.dynamic-field');

                components.forEach(field => {
                    const key = field.dataset.fieldKey;
                    const editorId = field.querySelector('textarea').id;
                    const content = tinymce.get(editorId)?.getContent() || '';

                    data.push({ key, content });
                });

                document.getElementById('gostDataSerialized').value = JSON.stringify(data);
            }

            // === Загрузка существующих данных при открытии формы ===
            @if(isset($article->gost_data) && is_array($article->gost_data))
            @foreach($article->gost_data as $item)
            @php
                $key = $item['key'] ?? '';
                $content = $item['content'] ?? '';
            @endphp
            addComponentToForm("{{ $key }}", null, "{!! addslashes($content) !!}");
            @endforeach
            @endif

            // === Инициализация формы при загрузке ===
            updateComponentOrder();

            // === Обработчик отправки формы ===
            editForm.addEventListener('submit', function (e) {
                e.preventDefault();
                tinymce.triggerSave();
                updateComponentOrder();
                editForm.submit();
            });

            // === Загрузка компонентов при изменении стандарта ===
            document.getElementById('standardSelector').addEventListener('change', function () {
                const standard = this.value;
                selectedStandard.value = standard;
                fetch(`/get-components/${standard}`)
                    .then(response => response.json())
                    .then(components => {
                        componentsList.innerHTML = components.map(component => `
                            <div class="component-item" data-key="${component.key}">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-grip-vertical drag-handle"></i>
                                    <span>${component.label}</span>
                                </div>
                            </div>
                        `).join('');
                    });
            });
        });
    </script>
@endsection
