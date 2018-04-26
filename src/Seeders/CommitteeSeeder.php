<?php
namespace Baytek\Laravel\Content\Types\Committee\Seeders;

use Baytek\Laravel\Content\Seeder;

class CommitteeSeeder extends Seeder
{
    private $data = [
        [
            'key' => 'committee',
            'title' => 'Committee',
            'content' => \Baytek\Laravel\Content\Types\Committee\Models\Committee::class,
            'relations' => [
                ['parent-id', 'content-type']
            ]
        ],
        [
            'key' => 'document-menu',
            'title' => 'Committee Navigation Menu',
            'content' => '',
            'relations' => [
                ['content-type', 'menu'],
                ['parent-id', 'admin-menu'],
            ]
        ],
        [
            'key' => 'document-index',
            'title' => 'Committees',
            'content' => 'committee.index',
            'meta' => [
                'type' => 'route',
                'class' => 'item',
                'append' => '</span>',
                'prepend' => '<i class="handshake outline left icon"></i><span class="collapseable-text">',
                'permission' => 'View Committee',
            ],
            'relations' => [
                ['content-type', 'menu-item'],
                ['parent-id', 'document-menu'],
            ]
        ]
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->seedStructure($this->data);
    }
}
