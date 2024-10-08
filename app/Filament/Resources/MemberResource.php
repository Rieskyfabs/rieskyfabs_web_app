<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MemberResource\Pages;
use App\Filament\Resources\MemberResource\RelationManagers;
use App\Models\Member;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MemberResource extends Resource
{
    protected static ?string $model = Member::class;

    protected static ?string $navigationGroup = 'Preferences';

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Members';

    protected static ?int $navigationSort = 1;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->placeholder('Enter Name'),
                TextInput::make('designation')
                    ->required()
                    ->placeholder('Enter Designation'),
                TextInput::make('tw_url')->url()
                    ->label('Twitter URL')
                    ->placeholder('Twitter URL'),
                TextInput::make('fb_url')->url()
                    ->label('Facebook URL')
                    ->placeholder('Facebook URL'),
                TextInput::make('in_url')->url()
                    ->label('Instagram URL')
                    ->placeholder('Instagram URL'),
                TextInput::make('yt_url')->url()
                    ->label('Youtube URL')
                    ->placeholder('Youtube URL'),
                TextInput::make('tt_url')->url()
                    ->label('Tiktok URL')
                    ->placeholder('Tiktok URL'),
                FileUpload::make('image')
                    ->image()
                    ->imageEditor()
                    ->imageCropAspectRatio('4:5')
                    ->imageResizeTargetWidth('1080')
                    ->imageResizeTargetHeight('1280')
                    ->maxSize(5000),
                
                Select::make('status')
                    ->options([
                        1 => 'Active',
                        0 => 'Block',
                    ])
                    ->native(false)
                    ->preload()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')->width(100),
                TextColumn::make('name'),
                TextColumn::make('designation'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        '1' => 'success',
                        '0' => 'danger',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        '1' => 'Active',
                        '0' => 'Block',
                        default => 'Draft',
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
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
            'index' => Pages\ListMembers::route('/'),
            'create' => Pages\CreateMember::route('/create'),
            'edit' => Pages\EditMember::route('/{record}/edit'),
        ];
    }    
}
