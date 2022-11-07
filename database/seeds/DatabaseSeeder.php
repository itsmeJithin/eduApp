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
        $this->call(CourseSeeder::class);
        $this->call(ClassSeeder::class);
        $this->call(ClassGroupSeeder::class);
        $this->call(SyllabusSeeder::class);
        $this->call(ClassGroupSyllabusSeeder::class);
        $this->call(SubscriptionMonthSeeder::class);
        $this->call(AddSyllabusSubscriptionMonths::class);
        $this->call(SubjectsSeeder::class);
        $this->call(ClassGroupSyllabusSubjectSeeder::class);
        $this->call(ChapterSeeder::class);
        $this->call(TopicSeeder::class);
        $this->call(AddStudyMaterialsSeeder::class);
        $this->call(AddLiveClassSeeder::class);
    }
}
