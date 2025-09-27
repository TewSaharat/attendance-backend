<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeaveTypeSeeder extends Seeder
{
    public function run()
    {
        DB::table('leave_types')->insert([
            ['id' => 1,  'name' => 'ลากิจส่วนตัว', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2,  'name' => 'ลาป่วย', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3,  'name' => 'ลาพักร้อน', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4,  'name' => 'ลาพักผ่อน', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5,  'name' => 'ลาพักผ่อนประจำปี', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6,  'name' => 'ลาคลอด', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7,  'name' => 'ลาแต่งงาน', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8,  'name' => 'ลาอุปสมบท/ลาบวช', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9,  'name' => 'ลาไปศึกษา ฝึกอบรม ปฏิบัติการวิจัย หรือดูงาน', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 10, 'name' => 'ลาไปช่วยเหลือครอบครัว', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 11, 'name' => 'ลาเข้ารับการตรวจเลือกทหารหรือเข้ารับการเตรียมพล', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 12, 'name' => 'ลาไม่รับค่าจ้าง', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 13, 'name' => 'ลาอื่น ๆ', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
