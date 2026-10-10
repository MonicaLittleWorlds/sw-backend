<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LumenFieldCatalogSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $models = [
                ['space_models', 2026100701, [
                    'model_source' => 'Assets/spaces/models/lumen_field_concert.xml',
                    'model_icon' => 'spaces/models/lumen_field_concert.png',
                    'model_desc' => 'Lumen Field',
                    'model_details' => 'A Little Worlds night festival with music-reactive screens, floor and stage effects.',
                    'model_tags' => 'concert,festival,music',
                ]],
                ['items', 2026100702, [
                    'model_source' => 'Assets/items/lumen_concert/dj-stand.xml',
                    'model_icon' => 'items/lumen_concert/dj-stand-icon.png',
                    'model_cid' => 'com.smallworlds.entity.item.WidgetItem',
                    'model_desc' => 'Lumen Field DJ Stand',
                    'model_details' => 'DJ controls for shared music, video, screens and stage effects.',
                    'model_tags' => 'widget,music,concert',
                    'gridActive' => true,
                    'persistedConfig' => true,
                ]],
            ];
            foreach ($models as [$table, $id, $fields]) {
                $existing = DB::table($table)->where('model_id', $id)->first();
                if ($existing) {
                    if ($existing->model_source !== $fields['model_source']) {
                        throw new \RuntimeException("Lumen model ID {$id} is already occupied");
                    }
                    continue;
                }
                DB::table($table)->insert(['model_id' => $id] + $fields);
            }
        });
    }
}
