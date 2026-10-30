<?php

namespace App\Filament\Resources\Pages\Pages;

use App\Filament\Resources\Pages\PageResource;
use App\Filament\Resources\Pages\Schemas\PageForm;
use App\Services\SeoAnalyzer;
use Filament\Resources\Pages\CreateRecord;

class CreatePage extends CreateRecord
{
    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [PageForm::previewAction()];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['is_system'] = false;
        $data['type'] = static::getResource()::type();
        if ($data['type'] !== 'page') {
            $data['template'] = $data['type']; // landing / post use their own design
        }
        $data['template'] ??= 'builder';

        return PageForm::mergeContent($data, $data['template']);
    }

    protected function afterCreate(): void
    {
        $this->record->forceFill(['seo_score' => SeoAnalyzer::analyze($this->record)['score']])->saveQuietly();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
