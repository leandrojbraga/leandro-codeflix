<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        $this->call(CategoriesSeeder::class);
        $this->call(GenresSeeder::class);
        $this->call(CastMembersSeeder::class);
        $this->call(VideosSeeder::class);
        $this->call(ContentDescriptorsSeeder::class);
    }
}
