<?php

namespace App\Traits;

trait GostFieldsTrait
{
    /**
     * Возвращает поля для выбранного стандарта
     */
    public function getGostFields(string $standard): array
    {
        return match ($standard) {
            'gost34' => $this->getGost34Fields(),
            'gost19' => $this->getGost19Fields(),
            'ieee830' => $this->getIeee830Fields(),
            'iso29148' => $this->getIso29148Fields(),
            default => $this->getGost34Fields(),
        };
    }

    private function getGost34Fields(): array
    {
        return [
            'general_info' => '1. Общие сведения',
            'purpose' => '2. Назначение и цели создания системы',
            'objects_automation' => '3. Характеристика объектов автоматизации',
            'system_requirements' => '4. Требования к системе',
            'work_content' => '5. Состав и содержание работ',
            'control_acceptance' => '6. Порядок контроля и приемки',
            'preparation_requirements' => '7. Требования к подготовке объекта',
            'documentation_requirements' => '8. Требования к документированию',
            'sources' => '9. Источники разработки'
        ];
    }

    private function getGost19Fields(): array
    {
        return [
            'introduction' => '1. Введение',
            'development_basis' => '2. Основания для разработки',
            'purpose' => '3. Назначение разработки',
            'program_requirements' => '4. Требования к программе',
            'documentation_requirements' => '5. Требования к документации',
            'economic_indicators' => '6. Технико-экономические показатели',
            'development_stages' => '7. Стадии и этапы разработки',
            'control_acceptance' => '8. Порядок контроля и приемки',
            'appendix' => '9. Приложения'
        ];
    }

    private function getIeee830Fields(): array
    {
        return [
            'introduction' => '1. Introduction',
            'purpose' => '2. Purpose',
            'definitions' => '3. Definitions',
            'system_overview' => '4. System Overview',
            'functional_requirements' => '5. Functional Requirements',
            'non_functional' => '6. Non-Functional Requirements',
            'appendix' => '7. Appendix'
        ];
    }

    private function getIso29148Fields(): array
    {
        return [
            'scope' => '1. Scope',
            'conformance' => '2. Conformance',
            'terms' => '3. Terms and Definitions',
            'requirements' => '4. Requirements',
            'verification' => '5. Verification',
            'annex' => 'A. Annex'
        ];
    }
}
