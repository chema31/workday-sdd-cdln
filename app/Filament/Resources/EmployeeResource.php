<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Models\User;
use App\Services\User\UserService;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EmployeeResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $modelLabel = 'Employee';

    protected static ?string $pluralModelLabel = 'Employees';

    protected static ?string $slug = 'employees';

    /**
     * The administrator account is never managed as an employee.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('email', '!=', config('app.admin_email'));
    }

    public static function form(Form $form): Form
    {
        $isEdit = $form->getOperation() === 'edit';

        return $form->schema([
            TextInput::make('name')
                ->required()
                ->maxLength(255),

            TextInput::make('email')
                ->email()
                ->required()
                ->unique(table: 'users', column: 'email', ignoreRecord: true),

            // The plain password is handed to UserService, which is the only
            // place that hashes it. Hashing here too would store a hash of a
            // hash and no employee could ever log in.
            TextInput::make('password')
                ->password()
                ->required(! $isEdit)
                ->minLength(8)
                ->rules(['regex:/[0-9]/'])
                ->helperText('Minimum 8 characters, must contain at least one number.')
                ->dehydrated(fn ($state) => filled($state)),

            Toggle::make('is_active')
                ->label('Active')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('email')->searchable(),
                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                TextColumn::make('created_at')->dateTime('d/m/Y')->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')->label('Status'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
                    ->before(function (DeleteAction $action, User $record) {
                        if (app(UserService::class)->isProtectedAdmin($record)) {
                            Notification::make()
                                ->title('Cannot delete the administrator account.')
                                ->danger()
                                ->send();

                            $action->cancel();
                        }
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
