<?php

namespace Tests\Unit;
use Tests\TestCase;
//use PHPUnit\Framework\TestCase;
use App\Models\User;
use App\Models\Complaint;
use App\Services\ComplaintService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Queue;

class ComplaintServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ComplaintService $service;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // إنشاء مستخدم وتسجيل الدخول
        $this->user = User::factory()->create();
        Auth::login($this->user);

        // استدعاء الخدمة
        $this->service = app(ComplaintService::class);

        // منع تنفيذ Jobs حقيقية أثناء الاختبار
        Queue::fake();
    }

    public function itupdatescomplaint_successfully()
    {
        // إنشاء شكوى مرتبطة بالمستخدم الحالي
        $complaint = Complaint::factory()->create();

        // تنفيذ التابع update
        $updated = $this->service->update($complaint, 'closed', 'done');

        // التحقق من التعديلات
        $this->assertEquals('closed', $updated->status);
        $this->assertEquals('done', $updated->note);
        $this->assertEquals(2, $updated->version_number);

        // التحقق من إرسال إشعار
        Queue::assertPushed(\App\Jobs\SendComplaintNotification::class);
    }

    public function itthrowsexceptioniflockedbyanother_user()
    {
        $otherUser = User::factory()->create([
            'role' => 'employee',
        ]);

        $complaint = Complaint::factory()->create([
            'status' => 'new',
            'locked_by' => $otherUser->id,
            'locked_until' => now()->addMinutes(10),
        ]);

        $this->expectException(\RuntimeException::class);

        $this->service->update($complaint, 'closed');
    }
    /**
     * A basic unit test example.
     */
    public function test_example(): void
    {
        $this->assertTrue(true);
    }
}
