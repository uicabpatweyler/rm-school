<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Filament\Resources\RoleResource\RelationManagers;
use App\Models\Role;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        TextInput::make('display_name')
                            ->label(__('Display Name'))
                            ->placeholder(__('Display Name'))
                            ->hint(__('required'))
                            ->hintIcon('heroicon-s-question-mark-circle')
                            ->hintColor('danger')
                            ->helperText('Helper text for Display Name')
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function (Closure $set, $state) {
                                $set('name', Str::slug($state));
                            })
                            ->dehydrateStateUsing(fn ($state) => Str::title($state)),
                        TextInput::make('name')
                            ->label(__('Name Role'))
                            ->placeholder(__('Name Role'))
                            ->hint(__('required'))
                            ->hintIcon('heroicon-s-question-mark-circle')
                            ->hintColor('danger')
                            ->helperText('Helper text for Name Role')
                            ->minLength(3)
                            ->maxLength(30)
                            ->required(),
                        Checkbox::make('is_active')
                            ->label(__('Role Active'))
                            ->default(true)
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->getStateUsing( function (Role $record) : string {
                    return $record->name;
                }),
                Tables\Columns\TextColumn::make('display_name')
                    ->getStateUsing( function (Role $record) : string {
                    return Str::title($record->display_name);
                }),
                IconColumn::make('is_active')->label(__('Active'))
                    ->boolean()
                    ->trueIcon('heroicon-o-badge-check')
                    ->falseIcon('heroicon-o-x-circle')
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                //Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()->action( function (Model $record){
                    /* protect super-admin role from delete */
                    if($record->name!='super-admin')
                        $record->delete();
                }),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()->action(function (Collection $records){
                    /* protect super-admin role from delete */
                    dd($records);
                }),
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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
