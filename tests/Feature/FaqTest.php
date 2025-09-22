<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\FaqCategory;
use App\Models\FaqQuestion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FaqTest extends TestCase
{
    use RefreshDatabase;

    private $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
    }

    /**
     * Test admin can view FAQ categories.
     */
    public function test_admin_can_view_faq_categories()
    {
        FaqCategory::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.faq-categories.index'));

        $response->assertStatus(200);
        $response->assertViewHas('faqCategories');
    }

    /**
     * Test admin can create FAQ category.
     */
    public function test_admin_can_create_faq_category()
    {
        $categoryData = [
            'name' => 'General Questions',
            'description' => 'General frequently asked questions',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.faq-categories.store'), $categoryData);

        $response->assertRedirect();
        $this->assertDatabaseHas('faq_categories', [
            'name' => 'General Questions',
        ]);
    }

    /**
     * Test admin can edit FAQ category.
     */
    public function test_admin_can_edit_faq_category()
    {
        $category = FaqCategory::factory()->create();

        $updatedData = [
            'name' => 'Updated Category Name',
            'description' => 'Updated category description',
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('admin.faq-categories.update', $category->id), $updatedData);

        $response->assertRedirect();
        $this->assertDatabaseHas('faq_categories', [
            'name' => 'Updated Category Name',
        ]);
    }

    /**
     * Test admin can delete FAQ category.
     */
    public function test_admin_can_delete_faq_category()
    {
        $category = FaqCategory::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.faq-categories.destroy', $category->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('faq_categories', [
            'id' => $category->id,
        ]);
    }

    /**
     * Test admin can view FAQ questions.
     */
    public function test_admin_can_view_faq_questions()
    {
        FaqQuestion::factory()->count(5)->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.faq-questions.index'));

        $response->assertStatus(200);
        $response->assertViewHas('faqQuestions');
    }

    /**
     * Test admin can create FAQ question.
     */
    public function test_admin_can_create_faq_question()
    {
        $category = FaqCategory::factory()->create();

        $questionData = [
            'question' => 'What is this service about?',
            'answer' => 'This service provides research articles and academic content.',
            'faq_category_id' => $category->id,
            'status' => 'published',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.faq-questions.store'), $questionData);

        $response->assertRedirect();
        $this->assertDatabaseHas('faq_questions', [
            'question' => 'What is this service about?',
        ]);
    }

    /**
     * Test admin can edit FAQ question.
     */
    public function test_admin_can_edit_faq_question()
    {
        $question = FaqQuestion::factory()->create();

        $updatedData = [
            'question' => 'Updated question text?',
            'answer' => 'Updated answer content.',
            'status' => 'draft',
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('admin.faq-questions.update', $question->id), $updatedData);

        $response->assertRedirect();
        $this->assertDatabaseHas('faq_questions', [
            'question' => 'Updated question text?',
        ]);
    }

    /**
     * Test admin can delete FAQ question.
     */
    public function test_admin_can_delete_faq_question()
    {
        $question = FaqQuestion::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.faq-questions.destroy', $question->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('faq_questions', [
            'id' => $question->id,
        ]);
    }

    /**
     * Test member can view published FAQ questions.
     */
    public function test_member_can_view_published_faq_questions()
    {
        $category = FaqCategory::factory()->create();
        FaqQuestion::factory()->create([
            'faq_category_id' => $category->id,
            'status' => 'published',
        ]);

        $response = $this->get(route('member.faq'));

        $response->assertStatus(200);
        $response->assertViewHas('faqQuestions');
    }

    /**
     * Test FAQ question validation.
     */
    public function test_faq_question_validation()
    {
        $questionData = [
            'question' => '', // Empty question should fail validation
            'answer' => 'Answer content',
            'faq_category_id' => 1,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.faq-questions.store'), $questionData);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('question');
    }
}
