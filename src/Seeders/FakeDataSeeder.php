<?php

namespace Baytek\Laravel\Content\Types\Committee\Seeders;

use Baytek\Laravel\Content\Types\Committee\Models\Committee;
use Baytek\Laravel\Content\Types\Document\Models\File;
use Baytek\Laravel\Content\Types\Document\Models\Folder;
use Baytek\Laravel\Content\Types\Webpage\Webpage;
use Baytek\Laravel\Users\Members\Models\Member;
use Illuminate\Database\Seeder;

use Faker\Factory as Faker;

class FakeDataSeeder extends Seeder
{
	/**
     * Simplified list of MIME types instead of the many faker ones
     *
     * @var array MIME types
     */
    protected $mimeTypes = [
        'image/gif',
        'image/png',
        'image/jpeg',
        'application/pdf',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/msword',
        'application/zip',
        'application/x-gzip',
        'application/vnd.ms-powerpoint',
        'text/plain',
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->generateCommittees();
        $this->populateCommitteeMembers();
        $this->generateWebpages();
        $this->generateFolders();
        $this->generateFiles();
    }

    public function generateCommittees($total = 5)
    {
    	$content_type = content_id('content-type/committee');
    }

    public function populateCommitteeMembers($total = 3)
    {
    	
    }

    public function generateFolders($total = 50)
    {
        $content_type = content_id('content-type/folder');

        $folder_ids = collect([$content_type]);

        foreach(range(1,$total) as $index) {
            $folder = (factory(Folder::class)->make());
            $folder->save();

            //Add relationships
            $folder->saveRelation('content-type', $content_type);
            $folder->saveRelation('parent-id', $folder_ids->random());

            //Add metadata
            $folder->saveMetadata('author_id', 1);

            //Add ID to list of folders
            $folder_ids->push($folder->id);
        }
    }

    public function generateFiles($total = 50)
    {
        $faker = Faker::create();

        $content_type = content_id('content-type/file');
        $folders = Folder::all();

        //Make sure the folder in storage exists
        if (!file_exists(storage_path('app/resources'))) {
            \Storage::makeDirectory('resources');
        }

        foreach(range(1,$total) as $index) {
            $file = (factory(File::class)->make());
            $file->save();

            //Create an empty text file
            $path = 'resources/example_'.str_random(20).'.txt';
            touch(storage_path('app/'.$path));

            //Add relationships
            $file->saveRelation('content-type', $content_type);
            $file->saveRelation('parent-id', $folders->random()->id);

            //Add metadata
            $file->saveMetadata('author_id', 1);
            $file->saveMetadata('file', $path);
            $file->saveMetadata('original', 'example.txt');
            $file->saveMetadata('size', rand(1000,1000000000));
            $file->saveMetadata('mime', $this->mimeTypes[rand(0, count($this->mimeTypes) - 1)]);
        }
    }
}
