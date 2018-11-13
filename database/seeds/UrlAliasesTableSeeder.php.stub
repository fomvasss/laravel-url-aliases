<?php

use Illuminate\Database\Seeder;

class UrlAliasesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $aliases = [
            [
                'source' => 'system/page/1',
                'alias' => 'about',
                'locale' => 'de'
            ],
            [
                'source' => 'system/page/2',
                'alias' => 'about',
                'locale' => 'en'
            ],
            [
                'source' => 'system/article/1',
                'alias' => 'nature/animals/elephants',
            ],
            [
                'source' => 'system/article/2',
                'alias' => 'nature/forest/grobere-walder',
                'locale' => 'de',
            ],
            [
                'source' => 'system/article/3',
                'alias' => 'nature/forest/bigger-woods',
                'locale' => 'en',
            ],
            [
                'source' => 'https://www.google.com',
                'alias' => 'my-favority-search',
                'type' => '301',
            ],
        ];

        foreach ($aliases as $i => $data) {
            // Use aliases model
            $alias = Fomvasss\UrlAliases\Models\UrlAlias::updateOrCreate($data);

            $this->command->info(($i+1).". Saved -> $alias->source | $alias->alias | $alias->locale | $alias->type");
        }
    }
}
