<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar se o usuário admin já existe
        $adminExists = User::where('email', 'fanumero1dotiaoecarreiro@admin.com')->exists();

        if (!$adminExists) {
            User::create([
                'name' => 'Admin Tião e Carreiro',
                'email' => 'fanumero1dotiaoecarreiro@admin.com',
                'password' => Hash::make('boisoberano'),
                'is_admin' => true,
                'email_verified_at' => now()
            ]);

            $this->command->info('Usuário admin criado com sucesso!');
            $this->command->info('Email: fanumero1dotiaoecarreiro@admin.com');
            $this->command->info('Senha: boisoberano');
        } else {
            $this->command->info('Usuário admin já existe!');
        }
    }
}
