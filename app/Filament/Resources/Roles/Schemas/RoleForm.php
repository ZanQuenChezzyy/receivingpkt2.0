<?php

namespace App\Filament\Resources\Roles\Schemas;

use App\Models\Permission;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        $groupedPermissions = self::getGroupedPermissions();
        $permissionFieldMap = self::getPermissionFieldMap($groupedPermissions);

        return $schema->components([
            self::getRoleInformationSection($permissionFieldMap),
            self::getPermissionsTabs($groupedPermissions),
        ])->columns(3);
    }

    /**
     * LEFT PANEL — ROLE INFO
     */
    // 2. Tambahkan tipe data Collection pada parameter
    private static function getRoleInformationSection(Collection $permissionFieldMap): Section
    {
        return Section::make(__('role.role_information'))
            ->description(__('role.role_information_desc'))
            ->icon(Heroicon::Identification)
            ->schema([
                TextInput::make('name')
                    ->label(__('role.role_name'))
                    ->placeholder(__('role.role_name_placeholder'))
                    ->required()
                    ->minLength(3)
                    ->maxLength(45)
                    ->unique(ignoreRecord: true)
                    ->columnSpanFull(),

                Toggle::make('select_all')
                    ->label(__('role.select_all'))
                    ->helperText(__('role.enable_role'))
                    ->dehydrated(false)
                    ->live()
                    ->onIcon(Heroicon::ShieldCheck)
                    ->offIcon(Heroicon::ShieldExclamation)

                    ->afterStateHydrated(function ($set, $record) use ($permissionFieldMap) {
                        if (!$record) {
                            return;
                        }

                        $selected = $record->permissions->pluck('id')->sort()->values()->all();
                        $all = $permissionFieldMap->flatten()->sort()->values()->all(); // Hapus collect() karena sudah berbentuk Collection
            
                        $set('select_all', $selected === $all);
                    })

                    ->afterStateUpdated(function (bool $state, callable $set) use ($permissionFieldMap) {
                        foreach ($permissionFieldMap as $field => $permissionIds) {
                            $set($field, $state ? $permissionIds : []);
                        }
                    }),
            ])
            ->columns(1)
            ->columnSpan(1)
            ->collapsible();
    }

    /**
     * RIGHT PANEL — PERMISSIONS TABS
     */
    // 3. Tambahkan tipe data Collection pada parameter
    private static function getPermissionsTabs(Collection $groupedPermissions): Tabs
    {
        $tabs = $groupedPermissions
            ->map(function ($permissions, string $resource) {
                $fieldName = 'permissions_' . Str::snake($resource);

                return Tab::make(Str::headline($resource))
                    ->icon(Heroicon::Key)
                    ->schema([
                        Section::make(Str::headline($resource))
                            ->description("App\\Models\\{$resource}")
                            ->collapsible()
                            ->schema([
                                CheckboxList::make($fieldName)
                                    ->label(__('role.permission_name'))
                                    ->relationship(
                                        'permissions',
                                        'name',
                                        modifyQueryUsing: fn($query) => $query->whereIn('id', $permissions->pluck('id'))
                                    )
                                    ->getOptionLabelFromRecordUsing(function ($record) {
                                        $name = strtolower($record->name);
                                        $map = [
                                            'view any' => 'view_any',
                                            'view' => 'view',
                                            'create' => 'create',
                                            'update' => 'update',
                                            'delete' => 'delete',
                                            'restore' => 'restore',
                                            'force delete' => 'force_delete',
                                        ];
                                        $key = collect($map)->first(fn($value, $startsWith) => str_starts_with($name, $startsWith));
                                        return $key ? __('role.' . $key) : __($record->name);
                                    })
                                    ->bulkToggleable()
                                    ->searchable()
                                    ->gridDirection('row')
                                    ->columns([
                                        'default' => 1,
                                        'md' => 2,
                                        'lg' => 3,
                                    ])
                                    ->live(),
                            ]),
                    ]);
            })
            ->values()
            ->all();

        return Tabs::make(__('role.permission'))
            ->contained()
            ->columnSpan(2)
            ->vertical()
            ->tabs($tabs);
    }

    /**
     * Query untuk mengelompokkan permission berdasarkan Model.
     */
    // 4. Tambahkan return type : Collection
    private static function getGroupedPermissions(): Collection
    {
        return Permission::query()
            ->get()
            ->groupBy(function (Permission $permission) {
                $parts = explode(' ', $permission->name);

                if (count($parts) > 2) {
                    if ($parts[0] === 'View' && $parts[1] === 'Any') {
                        return Str::studly(implode(' ', array_slice($parts, 2)));
                    }

                    if ($parts[0] === 'Force' && $parts[1] === 'Delete') {
                        return Str::studly(implode(' ', array_slice($parts, 2)));
                    }

                    return Str::studly(implode(' ', array_slice($parts, 1)));
                }

                return Str::studly(end($parts));
            });
    }

    /**
     * Membuat mapping antara resource field name dan ID permission.
     */
    // 5. Tambahkan Collection pada parameter dan return type
    private static function getPermissionFieldMap(Collection $groupedPermissions): Collection
    {
        return $groupedPermissions->mapWithKeys(function ($permissions, $resource) {
            return [
                'permissions_' . Str::snake($resource) => $permissions->pluck('id')->values()->all(),
            ];
        });
    }

    /**
     * Method untuk merge semua permissions per tab sebelum disimpan
     */
    public static function mergePermissions(array $data): array
    {
        $allPermissions = collect($data)
            ->filter(fn($value, $key) => str_starts_with($key, 'permissions_'))
            ->flatten()
            ->unique()
            ->values()
            ->all();

        return array_merge($data, ['permissions' => $allPermissions]);
    }
}
