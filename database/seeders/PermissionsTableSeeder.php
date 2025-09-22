<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            [
                'id'    => 1,
                'title' => 'user_management_access',
            ],
            [
                'id'    => 2,
                'title' => 'permission_create',
            ],
            [
                'id'    => 3,
                'title' => 'permission_edit',
            ],
            [
                'id'    => 4,
                'title' => 'permission_show',
            ],
            [
                'id'    => 5,
                'title' => 'permission_delete',
            ],
            [
                'id'    => 6,
                'title' => 'permission_access',
            ],
            [
                'id'    => 7,
                'title' => 'role_create',
            ],
            [
                'id'    => 8,
                'title' => 'role_edit',
            ],
            [
                'id'    => 9,
                'title' => 'role_show',
            ],
            [
                'id'    => 10,
                'title' => 'role_delete',
            ],
            [
                'id'    => 11,
                'title' => 'role_access',
            ],
            [
                'id'    => 12,
                'title' => 'user_create',
            ],
            [
                'id'    => 13,
                'title' => 'user_edit',
            ],
            [
                'id'    => 14,
                'title' => 'user_show',
            ],
            [
                'id'    => 15,
                'title' => 'user_delete',
            ],
            [
                'id'    => 16,
                'title' => 'user_access',
            ],
            [
                'id'    => 17,
                'title' => 'setting_create',
            ],
            [
                'id'    => 18,
                'title' => 'setting_edit',
            ],
            [
                'id'    => 19,
                'title' => 'setting_show',
            ],
            [
                'id'    => 20,
                'title' => 'setting_delete',
            ],
            [
                'id'    => 21,
                'title' => 'setting_access',
            ],
            [
                'id'    => 22,
                'title' => 'faq_management_access',
            ],
            [
                'id'    => 23,
                'title' => 'faq_category_create',
            ],
            [
                'id'    => 24,
                'title' => 'faq_category_edit',
            ],
            [
                'id'    => 25,
                'title' => 'faq_category_show',
            ],
            [
                'id'    => 26,
                'title' => 'faq_category_delete',
            ],
            [
                'id'    => 27,
                'title' => 'faq_category_access',
            ],
            [
                'id'    => 28,
                'title' => 'faq_question_create',
            ],
            [
                'id'    => 29,
                'title' => 'faq_question_edit',
            ],
            [
                'id'    => 30,
                'title' => 'faq_question_show',
            ],
            [
                'id'    => 31,
                'title' => 'faq_question_delete',
            ],
            [
                'id'    => 32,
                'title' => 'faq_question_access',
            ],
            [
                'id'    => 33,
                'title' => 'content_management_access',
            ],
            [
                'id'    => 34,
                'title' => 'content_category_create',
            ],
            [
                'id'    => 35,
                'title' => 'content_category_edit',
            ],
            [
                'id'    => 36,
                'title' => 'content_category_show',
            ],
            [
                'id'    => 37,
                'title' => 'content_category_delete',
            ],
            [
                'id'    => 38,
                'title' => 'content_category_access',
            ],
            [
                'id'    => 39,
                'title' => 'content_tag_create',
            ],
            [
                'id'    => 40,
                'title' => 'content_tag_edit',
            ],
            [
                'id'    => 41,
                'title' => 'content_tag_show',
            ],
            [
                'id'    => 42,
                'title' => 'content_tag_delete',
            ],
            [
                'id'    => 43,
                'title' => 'content_tag_access',
            ],
            [
                'id'    => 44,
                'title' => 'content_page_create',
            ],
            [
                'id'    => 45,
                'title' => 'content_page_edit',
            ],
            [
                'id'    => 46,
                'title' => 'content_page_show',
            ],
            [
                'id'    => 47,
                'title' => 'content_page_delete',
            ],
            [
                'id'    => 48,
                'title' => 'content_page_access',
            ],
            [
                'id'    => 49,
                'title' => 'member_create',
            ],
            [
                'id'    => 50,
                'title' => 'member_edit',
            ],
            [
                'id'    => 51,
                'title' => 'member_show',
            ],
            [
                'id'    => 52,
                'title' => 'member_delete',
            ],
            [
                'id'    => 53,
                'title' => 'member_access',
            ],
            [
                'id'    => 54,
                'title' => 'country_create',
            ],
            [
                'id'    => 55,
                'title' => 'country_edit',
            ],
            [
                'id'    => 56,
                'title' => 'country_show',
            ],
            [
                'id'    => 57,
                'title' => 'country_delete',
            ],
            [
                'id'    => 58,
                'title' => 'country_access',
            ],
            [
                'id'    => 59,
                'title' => 'subscription_create',
            ],
            [
                'id'    => 60,
                'title' => 'subscription_edit',
            ],
            [
                'id'    => 61,
                'title' => 'subscription_show',
            ],
            [
                'id'    => 62,
                'title' => 'subscription_delete',
            ],
            [
                'id'    => 63,
                'title' => 'subscription_access',
            ],
            [
                'id'    => 64,
                'title' => 'subscription_management_access',
            ],
            [
                'id'    => 65,
                'title' => 'member_subscription_create',
            ],
            [
                'id'    => 66,
                'title' => 'member_subscription_edit',
            ],
            [
                'id'    => 67,
                'title' => 'member_subscription_show',
            ],
            [
                'id'    => 68,
                'title' => 'member_subscription_delete',
            ],
            [
                'id'    => 69,
                'title' => 'member_subscription_access',
            ],
            [
                'id'    => 70,
                'title' => 'article_management_access',
            ],
            [
                'id'    => 71,
                'title' => 'article_category_create',
            ],
            [
                'id'    => 72,
                'title' => 'article_category_edit',
            ],
            [
                'id'    => 73,
                'title' => 'article_category_show',
            ],
            [
                'id'    => 74,
                'title' => 'article_category_delete',
            ],
            [
                'id'    => 75,
                'title' => 'article_category_access',
            ],
            [
                'id'    => 76,
                'title' => 'article_create',
            ],
            [
                'id'    => 77,
                'title' => 'article_edit',
            ],
            [
                'id'    => 78,
                'title' => 'article_show',
            ],
            [
                'id'    => 79,
                'title' => 'article_delete',
            ],
            [
                'id'    => 80,
                'title' => 'article_access',
            ],
            [
                'id'    => 81,
                'title' => 'member_management_access',
            ],
            [
                'id'    => 82,
                'title' => 'member_type_create',
            ],
            [
                'id'    => 83,
                'title' => 'member_type_edit',
            ],
            [
                'id'    => 84,
                'title' => 'member_type_show',
            ],
            [
                'id'    => 85,
                'title' => 'member_type_delete',
            ],
            [
                'id'    => 86,
                'title' => 'member_type_access',
            ],
            [
                'id'    => 87,
                'title' => 'comment_create',
            ],
            [
                'id'    => 88,
                'title' => 'comment_edit',
            ],
            [
                'id'    => 89,
                'title' => 'comment_show',
            ],
            [
                'id'    => 90,
                'title' => 'comment_delete',
            ],
            [
                'id'    => 91,
                'title' => 'comment_access',
            ],
            [
                'id'    => 92,
                'title' => 'profile_password_edit',
            ],
        ];

        Permission::insert($permissions);
    }
}
