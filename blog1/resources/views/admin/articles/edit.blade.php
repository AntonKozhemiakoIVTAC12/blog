@extends('layouts.admin_layout')

@section('content')
    <div class="container-fluid py-4">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

        <style>
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
            <!-- Левая панель компонентов -->
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

            <!-- Основная форма -->
            <div class="col-lg-9">
                <div class="form-panel">
                    <form action="{{ route('admin.articles.update', $article) }}" method="POST" id="editForm">
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
                                   required>
                        </div>

                        <input type="hidden" name="standard" id="selectedStandard" value="{{ $article->standard }}">

                        <div id="selectedComponents" class="components-container">
                            @if($article->gost_data)
                                @foreach($article->gost_data as $key => $value)
                                    <div class="dynamic-field" data-field-key="{{ $key }}">
                                        <i class="fas fa-times remove-component"></i>
                                        <label class="form-label fw-bold mb-3">{{ $key }}</label>
                                        <textarea class="form-control"
                                                  name="gost_data[{{ $key }}]"
                                                  rows="4"
                                                  required>{{ $value }}</textarea>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>Обновить документ
                            </button>
                            <a href="{{ route('admin.articles.index') }}" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times me-2"></i>Отмена
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const standardSelector = document.getElementById('standardSelector');
            const componentsList = document.getElementById('componentsList');
            const selectedComponents = document.getElementById('selectedComponents');
            const selectedStandard = document.getElementById('selectedStandard');

            // Инициализация Sortable для списка доступных компонентов
            new Sortable(componentsList, {
                group: {
                    name: 'shared',
                    pull: 'clone',
                    put: false
                },
                sort: false,
                animation: 150,
                ghostClass: 'ghost',
                chosenClass: 'chosen',
                onEnd: function (evt) {
                    const componentKey = evt.item.dataset.key;
                    addComponentToForm(componentKey);
                }
            });

            // Инициализация Sortable для выбранных компонентов
            new Sortable(selectedComponents, {
                group: 'shared',
                animation: 150,
                handle: '.drag-handle',
                ghostClass: 'ghost',
                chosenClass: 'chosen',
                onSort: function (evt) {
                    updateComponentOrder();
                }
            });

            // Загрузка компонентов при изменении стандарта
            standardSelector.addEventListener('change', function () {
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

            // Добавление компонента в форму
            function addComponentToForm(key) {
                if (document.querySelector(`[data-field-key="${key}"]`)) return;

                const field = document.createElement('div');
                field.className = 'dynamic-field';
                field.dataset.fieldKey = key;
                field.innerHTML = `
                    <i class="fas fa-times remove-component"></i>
                    <label class="form-label fw-bold mb-3">${key}</label>
                    <textarea class="form-control"
                              name="gost_data[${key}]"
                              rows="4"
                              required></textarea>
                `;

                selectedComponents.appendChild(field);
                addRemoveListener(field);
                updateComponentOrder();
            }

            // Добавление функционала удаления компонента
            function addRemoveListener(element) {
                element.querySelector('.remove-component').addEventListener('click', function () {
                    element.remove();
                    updateComponentOrder();
                });
            }

            function updateComponentOrder() {
                const components = Array.from(selectedComponents.children).map(el => el.dataset.fieldKey);
            }

            @if(old('components'))
            const savedComponents = @json(old('components'));
            savedComponents.forEach(key => addComponentToForm(key));
            @endif
        });
    </script>
@endsection
