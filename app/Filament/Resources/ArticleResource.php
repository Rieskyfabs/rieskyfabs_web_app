<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleResource\Pages;
use App\Filament\Resources\ArticleResource\RelationManagers;
use App\Models\Article;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static ?string $navigationGroup = 'Menus';

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?string $navigationLabel = 'Articles';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->required()
                    ->placeholder('title'),
                Select::make('category_id')
                    ->label('Category')
                    ->native(false)
                    ->options(
                        Category::all()->pluck('name', 'id')
                    ),
                TextInput::make('author')
                    ->placeholder('Author'),
                FileUpload::make('image')
                    ->image()
                    ->imageEditor()
                    ->imageCropAspectRatio('3:2')
                    ->imageResizeTargetWidth('720')
                    ->imageResizeTargetHeight('720')
                    ->maxSize(5000),
                RichEditor::make('content')
                    ->columnSpan(2),
                Select::make('status')->options([
                    1 => 'Active',
                    0 => 'Block'
                ])
                ->native(false)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Thumbnail'),
                TextColumn::make('title')->searchable(),
                TextColumn::make('author')->searchable()
                    ->badge(),
                TextColumn::make('content')
                    ->label('Content')
                    ->toggleable()
                    ->limit(20),
                TextColumn::make('category.name')
                    ->label('Category')
                    ->toggleable()
                    ->color('info')
                    ->badge(),
                TextColumn::make('created_at')
                    ->label('Published At')
                    ->toggleable(),
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
                Filter::make('is_featured')->toggle()
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
            'index' => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }    
}
