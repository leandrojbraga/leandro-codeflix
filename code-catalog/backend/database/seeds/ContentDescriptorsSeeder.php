<?php

use App\Models\ContentDescriptor;
use Illuminate\Database\Seeder;

class ContentDescriptorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(ContentDescriptor::class, 20)->create();
    }
}
