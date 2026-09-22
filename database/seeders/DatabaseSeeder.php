<?php

namespace Database\Seeders;

use App\Models\Content;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Setting::firstOrCreate(['key' => 'profile'], ['value' => [
            'name' => 'Bheem Chand', 'title' => 'Full Stack Developer', 'email' => 'bheemchand8126@gmail.com', 'phone' => '6398319676', 'location' => 'Dehradun, India',
            'show_phone' => false, 'show_email' => true, 'show_location' => true,
            'hero_heading' => 'Thoughtful code. Real-world impact.',
            'hero_intro' => 'I’m Bheem, a full stack developer building web applications, APIs, and enterprise dashboards with Laravel and React.',
            'bio' => 'Full Stack Developer with 3+ years of experience building web applications, REST APIs, and enterprise dashboards using PHP, Laravel, React.js, JavaScript, and MySQL. Experienced with OOP, MVC, database optimization, authentication, deployment, and enterprise solutions for Indian Railways.',
            'story' => 'My experience spans backend services, frontend integrations, and production deployments. At Dream Tech, I built CRUD applications and REST APIs. At Beatle Analytics, I work on Laravel and React applications, reporting dashboards, authentication, database optimization, and VPS deployments.',
            'availability' => '', 'linkedin' => '', 'github' => '', 'twitter' => '', 'show_linkedin' => false, 'show_github' => false, 'show_twitter' => false,
            'blog_enabled' => false, 'chat_enabled' => true, 'resume_enabled' => true,
            'seo_title' => 'Bheem Chand — Full Stack Developer', 'seo_description' => 'Full stack developer in Dehradun. Explore Bheem Chand’s work with Laravel, React, REST APIs, and enterprise applications.',
            'projects_heading' => 'Built for the real world.', 'contact_heading' => 'Let’s build something meaningful.',
        ]]);
        $records = [
            ['experience', 'Full Stack Developer', 'beatle-analytics', 'Built Laravel and React applications, REST APIs, and reporting dashboards. Implemented JWT authentication and role-based access control. Improved API response time by 40% through database and query optimization. Managed VPS deployments and monitoring.', ['organization' => 'Beatle Analytics', 'period' => 'July 2023 — Present', 'location' => 'Dehradun']],
            ['experience', 'Software Developer Trainee', 'dream-tech', 'Built CRUD applications, REST APIs, backend services, database models, and frontend integrations. Worked on testing, code reviews, debugging, and performance.', ['organization' => 'Dream Tech', 'period' => 'December 2022 — May 2023', 'location' => 'Dehradun']],
            ['education', 'Master of Computer Applications', 'mca', 'CGPA: 7.6/10', ['organization' => 'Graphic Era Hill University', 'period' => '2021 — 2023', 'location' => 'Dehradun']],
            ['education', 'B.Sc. Information Technology', 'bsc-it', '67%', ['organization' => 'HNBGU', 'period' => '2018 — 2021', 'location' => 'Dehradun']],
            ['project', 'Running Room Management System', 'running-room-management', 'An enterprise application for Indian Railways covering room allocation, occupancy monitoring, meal booking, maintenance workflows, and compliance reporting. Secure REST APIs support mobile integration and administrative portals. Payment integration supports online room and meal bookings.', ['organization' => 'Indian Railways', 'tags' => 'Laravel, MySQL, JWT, Blade, Tailwind CSS', 'category' => 'Enterprise application', 'url' => '', 'github' => '', 'image_alt' => '', 'short_title' => 'Running Room Management', 'features' => "Room allocation and occupancy monitoring\nMeal booking and payment integration\nMaintenance workflows and compliance reports\nSecure mobile APIs"]],
            ['project', 'OBHS Feedback & Attendance Management', 'obhs-feedback-attendance', 'Web applications for Indian Railways supporting passenger feedback and attendance tracking. Secure APIs with OTP verification connect interactive dashboards, analytics, complaint resolution, staff monitoring, and reporting. Database optimization and audit-ready reports support operational transparency.', ['organization' => 'Indian Railways', 'tags' => 'Laravel, React.js, Tailwind CSS, MySQL', 'category' => 'Web application', 'url' => '', 'github' => '', 'image_alt' => '', 'short_title' => 'Feedback & Attendance', 'features' => "Passenger feedback and complaint resolution\nAttendance tracking and OTP verification\nDashboards and operational analytics\nStaff monitoring and audit-ready reports"]],
        ];
        foreach (['Backend' => 'PHP, Laravel, REST APIs, MVC, OOP', 'Frontend' => 'React.js, JavaScript ES6+, HTML5, CSS3, Bootstrap, Tailwind CSS', 'Database' => 'MySQL, SQL query optimization', 'Tools & platforms' => 'Git, GitHub, Postman, VS Code, Linux, VPS', 'Concepts' => 'JWT authentication, RBAC, API integration, database design'] as $category => $skills) {
            $records[] = ['skill', $category, Str::slug($category), $skills, []];
        }
        foreach ($records as $i => [$type, $title, $slug, $body, $data]) {
            Content::firstOrCreate(['slug' => $slug], compact('type', 'title', 'body', 'data') + ['published' => true, 'featured' => $type === 'project', 'sort_order' => $i]);
        }
    }
}
