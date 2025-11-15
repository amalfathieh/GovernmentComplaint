<?php

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrganizationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $entities = [
            'وزارة الداخلية والجماعات المحلية',
            'وزارة الثقافة والفنون',
            'وزارة الشؤون الخارجية',
            'المديرية العامة للأمن الوطني',
            'وزارة التعليم العالي والبحث العلمي',
            'وزارة الصحة والسكان وإصلاح المستشفيات',
            'الوكالة الوطنية لتحسين السكن',
            'مؤسسة توزيع الكهرباء والغاز',
        ];

        foreach ($entities as $entity) {
            Organization::create(['name' => $entity]);
        }
    }

}
