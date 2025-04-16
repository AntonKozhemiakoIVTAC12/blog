<?php

$componentsData = [
    'gost34' => [
        ['key' => 'general_info', 'label' => '1. Общие сведения'],
        ['key' => 'purpose', 'label' => '2. Назначение и цели создания системы'],
        ['key' => 'objects_automation', 'label' => '3. Характеристика объектов автоматизации'],
        ['key' => 'system_requirements', 'label' => '4. Требования к системе'],
        ['key' => 'work_content', 'label' => '5. Состав и содержание работ'],
        ['key' => 'control_acceptance', 'label' => '6. Порядок контроля и приемки'],
        ['key' => 'preparation_requirements', 'label' => '7. Требования к подготовке объекта'],
        ['key' => 'documentation_requirements', 'label' => '8. Требования к документированию'],
        ['key' => 'sources', 'label' => '9. Источники разработки'],
    ],
    'gost19' => [
        ['key' => 'general_info', 'label' => '1. Общие сведения'],
        ['key' => 'purpose', 'label' => '2. Назначение и цели создания системы'],
        ['key' => 'objects_automation', 'label' => '3. Характеристика объектов автоматизации'],
        ['key' => 'system_requirements', 'label' => '4. Требования к системе'],
        ['key' => 'work_content', 'label' => '5. Состав и содержание работ'],
        ['key' => 'control_acceptance', 'label' => '6. Порядок контроля и приемки'],
        ['key' => 'preparation_requirements', 'label' => '7. Требования к подготовке объекта'],
        ['key' => 'documentation_requirements', 'label' => '8. Требования к документированию'],
        ['key' => 'sources', 'label' => '9. Источники разработки'],
    ],
];

foreach ($componentsData as $standardKey => $components) {
    foreach ($components as $index => $component) {
        \App\Models\Component::create([
            'standard_key' => $standardKey,
            'key' => $component['key'],
            'label' => $component['label'],
            'description' => null,
            'order' => $index + 1,
        ]);
    }
}

echo "Компоненты успешно созданы!";
