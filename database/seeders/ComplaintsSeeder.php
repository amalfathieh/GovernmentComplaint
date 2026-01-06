<?php

namespace Database\Seeders;

use App\Models\Complaint;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ComplaintsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $complaintData = [
            ['type' => 'خدمي', 'title' => 'انقطاع مياه الشرب', 'desc' => 'المياه مقطوعة عن الحي منذ ٣ أيام دون سابق إنذار.'],
            ['type' => 'فني', 'title' => 'عطل في محولة الكهرباء', 'desc' => 'صدر صوت انفجار من المحولة الرئيسية مما أدى لانقطاع التيار.'],
            ['type' => 'إداري', 'title' => 'تأخر في معالجة طلب', 'desc' => 'قدمت طلباً للحصول على رخصة منذ شهر ولم يصلني رد حتى الآن.'],
            ['type' => 'نظافة', 'title' => 'تراكم النفايات', 'desc' => 'تراكمت النفايات في الشارع الرئيسي مما أدى لانتشار الروائح الكريهة.'],
            ['type' => 'طرقات', 'title' => 'حفرة كبيرة في الطريق', 'desc' => 'توجد حفرة عميقة تعيق حركة السير وتسبب ضرراً للسيارات.'],
        ];

        for ($i = 0; $i < 3; $i++)
            foreach ($complaintData as $complaint)
                Complaint::create([
                    'user_id' => \App\Models\User::where('role', 'user')->inRandomOrder()->first()->id ?? 1,
                    'organization_id' => \App\Models\Organization::inRandomOrder()->first()->id ?? 1,
                    'type' => $complaint['type'],
                    'title' => $complaint['title'],
                    'description' => $complaint['desc'],
                    'location' => array_rand(['دمشق - المزة', 'حلب - الجميلية', 'حمص - الإنشاءات', 'اللاذقية - المشروع الأول']),
                    'status' => 'new',
                    'created_at' => now()->subDays(rand(1, 30)),
                ]);
    }
}
