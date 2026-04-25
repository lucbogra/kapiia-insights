# Filament v5 Pitfalls & Patterns

## Authorization

- `canCreate()` / `canDelete()` on Resources are **ignored** in v5 when a Policy exists for the model
- Use Laravel Policies for real security
- To hide UI: remove `CreateAction::make()` from `ListRecords::getHeaderActions()` AND drop the `create` route from `getPages()`

## Resources that bypass Eloquent

For resources that must go through a Use Case (like `ScenarioResource`), override `handleRecordCreation()` and `handleRecordUpdate()`:

```php
protected function handleRecordUpdate(Model $record, array $data): Model
{
    app(UpdateScenarioUseCase::class)->execute(
        new UpdateScenarioCommand(/* ... */)
    );

    return $record->fresh();
}
```

## Repeaters: never use `->relationship()` when you take over persistence

When using custom `handleRecordUpdate()` with a Use Case, `->relationship()` on Repeaters and CheckboxLists causes data loss:

- Data doesn't reach `$data` in the handler
- Filament's auto-sync never runs (we override it)

**Correct pattern**: use plain `->options()` and manually hydrate in `mutateFormDataBeforeFill()`:

```php
protected function mutateFormDataBeforeFill(array $data): array
{
    $record = $this->getRecord();

    $data['precisions'] = $record->precisions->map(fn($p) => [
        'precision_definition_id' => $p->precision_definition_id,
        'parameters'              => $p->parameters ?? [],
        'sort_order'              => $p->sort_order,
    ])->values()->all();

    $data['sourceConnections'] = $record->sourceConnections->pluck('id')->all();

    return $data;
}
```

## `dehydrated(false)` removes the field from `$data`

If you need to read a form field server-side (like `population_mode` for XOR logic), do NOT use `dehydrated(false)`. Keep the field dehydrated and clean it from `$data` in the save handler.

## Pages with route parameters

Never create standalone `Pages/` for record-bound routes — they 404 silently. Instead, use Resource pages with `InteractsWithRecord`:

```bash
php artisan make:filament-page RunScenario --resource=ScenarioResource --type=custom
```

```php
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;

class RunScenario extends Page
{
    use InteractsWithRecord;

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }
}
```

Register in the Resource:

```php
'run' => Pages\RunScenario::route('/{record}/run'),
```

Order matters — declare `run` before `edit` if both use `{record}`.

## Generating URLs to Resource pages

Use `Resource::getUrl()` instead of hardcoded route names:

```php
->url(fn($record) => ScenarioResource::getUrl('run', ['record' => $record->id]))
```

## Schema components namespaces (v5 specific)

- Form fields: `Filament\Forms\Components\`
- Layout (Section, Grid, Fieldset): `Filament\Schemas\Components\`
- Actions: `Filament\Actions\` (never `Filament\Tables\Actions\`)
- `Get`, `Set` utilities: `Filament\Schemas\Components\Utilities\`