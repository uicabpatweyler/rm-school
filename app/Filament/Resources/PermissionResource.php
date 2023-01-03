<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermissionResource\Pages;
use App\Filament\Resources\PermissionResource\RelationManagers;
use App\Models\Module;
use App\Models\Permission;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    protected static ?string $navigationIcon = 'heroicon-o-lock-closed';

    protected static ?int $navigationSort = 3;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    Select::make('module_id')
                        ->label('Module')
                        ->options(Module::all()->pluck('display_name', 'id'))
                        ->reactive()
                        ->afterStateUpdated(function (Closure $get, $set, $state) {
                            if($state!=null && $get('type') != null){
                                $set('name',null);
                                $rowModule = Module::find($state);
                                $set('name',$get('type').'_'.$rowModule->name);
                            }
                        })
                        ->hint(__('required'))
                        ->hintIcon('heroicon-s-question-mark-circle')
                        ->hintColor('danger')
                        ->required(),
                    Select::make('type')->label(__('Type'))
                        ->options([
                            'viewAny' => __('viewAny'),
                            'view' => __('View'),
                            'create' => __('Create'),
                            'update' => __('Update'),
                            'replicate' => __('Replicate'),
                            'delete' => __('Delete'),
                            'forceDelete' => __('Force Delete'),
                            'restore' => __('Restore')
                        ])->reactive()
                        ->afterStateUpdated(function (Closure $get, $set, $state) {
                            if($state!=null && $get('module_id') != null){
                                $set('name',null);
                                $rowModule = Module::find($get('module_id'));
                                $set('name',$state.'_'.$rowModule->name);
                            }else{
                                $set('name',null);
                            }

                        })
                        ->disabled(function (Closure $get){ return $get('module_id') == null; })
                        ->hint(__('required'))
                        ->hintIcon('heroicon-s-question-mark-circle')
                        ->hintColor('danger')
                        ->required(),
                    TextInput::make('name')
                        ->label('Name')
                        ->placeholder(__('Internal permission name'))
                        ->disabled(function (Closure $get){
                            if($get('module_id') == null) return true;
                            else if($get('type') == null) return true;
                            return false;
                        })
                        ->hint(__('required'))
                        ->hintIcon('heroicon-s-question-mark-circle')
                        ->hintColor('danger')
                        ->required()
                        ->unique(ignoreRecord: true),
                    TextInput::make('display_name')
                        ->label(__('Display Name'))
                        ->placeholder(__('Permission name to display'))
                        ->hint(__('required'))
                        ->hintIcon('heroicon-s-question-mark-circle')
                        ->hintColor('danger')
                        ->minLength(3)
                        ->maxLength(120)
                        ->required(),
                    Checkbox::make('is_active')
                        ->label(__('Permission Active'))
                        ->default(true)
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(__('Name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('display_name')->label(__('Display Name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('module.display_name')->label(__('Module')),
                IconColumn::make('is_active')
                    ->label(__('Active'))
                    ->boolean()
                    ->trueIcon('heroicon-o-badge-check')
                    ->falseIcon('heroicon-o-x-circle')
            ])
            ->filters([
                TernaryFilter::make(__('Status'))
                    ->placeholder(__('All'))
                    ->trueLabel(__('Active'))
                    ->falseLabel(__('Not Active'))
                    ->queries(
                        true: fn (Builder $query) => $query->where('is_active', true),
                        false: fn (Builder $query) => $query->where('is_active', false),
                        blank: fn (Builder $query) => $query->get(),
                    ),
                Tables\Filters\TrashedFilter::make()->label(__('Deleted records')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermissions::route('/'),
            'create' => Pages\CreatePermission::route('/create'),
            'edit' => Pages\EditPermission::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    protected static function getNavigationGroup(): ?string
    {
        return __('Authorization');
    }
}
