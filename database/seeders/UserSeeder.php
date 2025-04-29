<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::truncate();
        $User=[
            [
                'name' => 'Claudemir Ivanio Conzatti',
                'email' => 'claudemir@plannersolucoes.com.br',
                'password' => bcrypt('123')
            ],
            [
                'name' => 'Simone',
                'email' => 'cobranca@decorbras.com',
                'password' => bcrypt('123')
            ],
            [
                'name' => 'Kamilla',
                'email' => 'kamillaklopes@hotmail.com',
                'password' => bcrypt('123')
            ],
            [
                'name' => 'Iris',
                'email' => 'iriszerna@msn.com',
                'password' => bcrypt('123')
            ],
            [
                'name' => 'Katiussia',
                'email' => 'financeiro@decorbras.com',
                'password' => bcrypt('123')
            ],
            [
                'name' => 'Suelym',
                'email' => 'nfe.decorbras@gmail.com',
                'password' => bcrypt('123')
            ],
            [
                'name' => 'Juli',
                'email' => 'pedidosdecorbras@yahoo.com.br',
                'password' => bcrypt('123')
            ],
        ];
        User::insert($User);
    }
}
